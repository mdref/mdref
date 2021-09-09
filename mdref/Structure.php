<?php

namespace mdref;

/**
 * Structure of an entry
 */
class Structure {
	const OF_OTHER = "other";
	const OF_NAMESPACE = "ns";
	const OF_CLASS = "class";
	const OF_FUNC = "func";

	private $type;
	private $struct;
	private $entry;

	function __construct(Entry $entry) {
		$this->entry = $entry;

		if ($entry->isRoot() || $entry->isNsClass()) {
			if ($entry->isRoot()) {
				$this->type = self::OF_NAMESPACE;
				$this->getStructureOfRoot();
			} elseif (!strncmp($entry->getTitle(), "namespace", strlen("namespace"))) {
				$this->type = self::OF_NAMESPACE;
				$this->getStructureOfNs();
			} else {
				$this->type = self::OF_CLASS;
				$this->getStructureOfClass();
			}
		} elseif ($entry->isFunction()) {
			$this->type = self::OF_FUNC;
			$this->getStructureOfFunc();
		} else {
			$this->type = self::OF_OTHER;
		}
	}

	static function of(Entry $entry) : StructureOf {
		return (new static($entry))->getStruct();
	}

	function getStruct() : StructureOf {
		return $this->struct;
	}

	function format() {
		$this->struct->format();
	}

	private function getStructureOfFunc() : StructureOfFunc {
		return $this->struct = new StructureOfFunc([
			"ns" => $this->entry->getParent()->getNsName(),
			"name" => $this->entry->getEntryName(),
			"desc" => $this->entry->getFullDescription(),
			"returns" => $this->getReturns(),
			"params" => $this->getParams(),
			"throws" => $this->getThrows()
		]);
	}

	private function getStructureOfClass() : StructureOfClass {
		return $this->struct = new StructureOfClass([
			"ns" => $this->entry->getParent()->getNsName(),
			"name" => $this->prepareClassName(),
			"desc" => $this->entry->getFullDescription(),
			"consts" => $this->getConstants(),
			"props" => $this->getProperties(),
			"funcs" => $this->getFunctions(),
			"classes" => $this->getClasses(),
		]);
	}

	private function getStructureOfNs() : StructureOfNs {
		return $this->struct = new StructureOfNs([
			"name" => $this->entry->getNsName(),
			"desc" => $this->entry->getFullDescription(),
			"consts" => $this->getConstants(),
			"classes" => $this->getClasses(),
		]);
	}

	private function getStructureOfRoot() : StructureOfRoot {
		return $this->struct = new StructureOfRoot([
			"name" => $this->entry->getName(),
			"desc" => $this->entry->getFile()->readIntro(),
			"consts" => $this->getConstants(),
			"classes" => $this->getClasses()
		]);
	}

	private function getSection(string $section) : string {
		return $this->entry->getFile()->readSection($section);
	}

	private function prepareClassName() {
		return preg_replace_callback_array([
			'/(?P<type>class|interface|trait)\s+([\\\\\w]+\\\)?(?P<name>\w+)\s*/' => function($match) {
				return $match["type"] . " " . $match["name"] . " ";
			},
			'/(?P<op>extends|implements)\s+(?P<names>[\\w]+(?:(?:,\s*[\\\\\w]+)*))/' => function ($match) {
				return $match["op"] . " " . preg_replace('/\b(?<!\\\)(\w)/', '\\\\\\1', $match["names"]);
			}
		], $this->entry->getTitle());
	}

	private function splitList(string $pattern, string $text) : array {
		$text = trim($text);
		if (strlen($text) && !preg_match("/^None/", $text)) {
			if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
				return $matches;
			}
		}
		return [];
	}

	private function getConstantValue(string $name) {
		$ns = $this->entry->getNsName();
		if (defined("\\$ns::$name")) {
			return constant("\\$ns::$name");
		}
		if (defined("\\$ns\\$name")) {
			return constant("\\$ns\\$name");
		}
		return null;
	}

	private function getConstants() : array {
		static $pattern = '/
			\*\s+
			(?:[<]span[ ]class="constant"[>])?
			(?<name>\w+)
			(?:[<]\/span[>])?
			(?:\s*=\s*(?P<value>.+))?
			(?P<desc>(?:\s*\n\s*[^\*\n#].*)*)
		/x';

		$structs = [];
		$consts = $this->splitList($pattern, $this->getSection("Constants"));
		foreach ($consts as $const) {
			if (!isset($const["value"]) || !strlen($const["value"])) {
				$const["value"] = $this->getConstantValue($const["name"]);
			}
			$structs[$const["name"]] = new StructureOfConst($const);
		}
		return $structs;
	}

	private function getProperties() : array {
		static $pattern = '/
			\*\s+
			(?P<modifiers>\w+\s+)*
			(?:\((?P<usages>(?:(?:\w+)\s*)*)\))*\s*
			(?P<type>[\\\\\w]+)\s+
			(?<name>\$\w+)
			(?:\s*=\s*(?P<defval>.+))?
			(?P<desc>(?:\s*\n\s*[^\*].*)*)
		/x';

		$structs = [];
		$props = $this->splitList($pattern, $this->getSection("Properties"));
		foreach ($props as $prop) {
			$structs[$prop["name"]] = new StructureOfVar($prop);
		}
		return $structs;
	}

	private function getFunctions() : array {
		$structs = [];
		foreach ($this->entry as $sub) {
			if ($sub->isFunction()) {
				$structs[$sub->getEntryName()] = static::of($sub);
			}
		}
		return $structs;
	}

	private function getClasses() : array {
		$structs = [];
		foreach ($this->entry as $sub) {
			if ($sub->isNsClass()) {
				$structs[$sub->getEntryName()] = static::of($sub);
			}
		}
		return $structs;
	}

	private function getParams() : array {
		static $pattern = '/
			\*\s+
			(?P<modifiers>\w+\s+)*
			(?P<type>[\\\\\w_]+)\s+
			(?P<ref>&)?(?P<name>\$[\w_]+)
			(?:\s*=\s*(?P<defval>.+))?
			(?P<desc>(?:\s*[^*]*\n(?!\n)\s*[^\*].*)*)
		/x';

		$structs = [];
		$params = $this->splitList($pattern, $this->getSection("Params"));
		foreach ($params as $param) {
			$structs[$param["name"]] = new StructureOfVar($param);
		}
		return $structs;
	}

	private function getReturns() : array {
		static $pattern = '/
			\*\s+
			(?<type>[\\\\\w_]+)
			\s*,?\s*
			(?P<desc>(?:.|\n(?!\s*\*))*)
		/x';

		$returns = $this->splitList($pattern, $this->getSection("Returns"));
		$retvals = [];
		foreach ($returns as list(, $type, $desc)) {
			$retvals[] = [$type, $desc];
		}
		return $retvals;
		return [implode("|", array_unique(array_column($returns, "type"))), $retdesc];
	}

	private function getThrows() : array {
		static $pattern = '/
			\*\s+
			(?P<exception>[\\\\\w]+)\s*
		/x';

		$throws = $this->splitList($pattern, $this->getSection("Throws"));
		return array_column($throws, "exception");
	}
}

abstract class StructureOf {
	function __construct(array $props = []) {
		foreach ($props as $key => $val) {
			if (is_int($key)) {
				continue;
			}
			if (!property_exists(static::class, $key)) {
				throw new \UnexpectedValueException(
					sprintf("Property %s::\$%s does not exist", static::class, $key)
				);
			}
			if ($key === "desc" || $key === "modifiers" || $key === "defval") {
				$val = trim($val);
			}
			$this->$key = $val;
		}
	}

	// abstract function format();

	function formatDesc($level, array $tags = []) {
		$indent = str_repeat("\t", $level);
		$desc = trim($this->desc);
		if (false !== stristr($desc, "deprecated in")) {
			$tags[] = "deprecated";
		}
		if ($tags) {
			$desc .= "\n\n@" . implode("\n@", $tags);
		}
		$desc = preg_replace('/[\t ]*\n/',"\n$indent * ", $desc);
		printf("%s/**\n%s * %s\n%s */\n", $indent, $indent, $desc, $indent);
	}

	function saneTypes(array $types) {
		$sane = [];
		foreach ($types as $type) {
			if (strlen($s = $this->saneType($type, false))) {
				$sane[] = $s;
			}
		}
		return $sane;
	}

	function saneType($type, $strict = true) {
		switch (strtolower($type)) {
			case "object":
			case "resource":
			case "stream":
			case "mixed":
			case "true":
			case "false":
			case "null":
				if ($strict) {
					break;
				}
				/* fallthrough */
			case "bool":
			case "int":
			case "float":
			case "string":
			case "array":
			case "callable":
				return $type;
				break;
			default:
				return ($type[0] === "\\" ? "":"\\") . $type;
				break;
		}
	}
}

class StructureOfRoot extends StructureOf {
	public $name;
	public $desc;
	public $consts;
	public $classes;

	function format() {
		$this->formatDesc(0);

		foreach ($this->consts as $const) {
			$const->format(0);
			printf(";\n");
		}

		printf("namespace %s;\nuse %s;\n", $this->name, $this->name);
		StructureOfNs::$last = $this->name;

		foreach ($this->getClasses() as $class) {
			$class->format();
		}
	}

	function getClasses() {
		yield from $this->classes;
		foreach ($this->classes as $class) {
			yield from $class->getClasses();
		}
	}
}
class StructureOfNs extends StructureOfRoot {
	public $funcs;

	public static $last;

	function format() {
		print $this->formatDesc(0);

		if (strlen($this->name) && $this->name !== StructureOfNs::$last) {
			StructureOfNs::$last = $this->name;
			printf("namespace %s;\n", $this->name);
		}
		foreach ($this->consts as $const) {
			$const->format(0);
			printf(";\n");
		}
	}
}

class StructureOfClass extends StructureOfNs
{
	public $ns;
	public $props;

	function format() {
		if ($this->ns !== StructureOfNs::$last) {
			printf("namespace %s;\n", $this->ns);
			StructureOfNs::$last = $this->ns;
		}

		print $this->formatDesc(0);
		printf("%s {\n", $this->name);

		foreach ($this->consts as $const) {
			$const->format(1);
			printf(";\n");
		}

		foreach ($this->props as $prop) {
			$prop->formatAsProp(1);
			printf(";\n");
		}

		foreach ($this->funcs as $func) {
			$func->format(1);
			if (strncmp($this->name, "interface", strlen("interface"))) {
				printf(" {}\n");
			} else {
				printf(";\n");
			}
		}

		printf("}\n");
	}
}

class StructureOfFunc extends StructureOf {
	public $ns;
	public $class;
	public $name;
	public $desc;
	public $params;
	public $returns;
	public $throws;

	function omitParamTypes() {
		switch ($this->name) {
			// ArrayAccess
			case "offsetGet":
			case "offsetSet":
			case "offsetExists":
			case "offsetUnset":
			// Serializable
			case "serialize":
			case "unserialize":
				return true;
		}
		return false;
	}

	function format(int $level) {
		$tags = [];
		foreach ($this->params as $param) {
			$type = $this->saneType($param->type, false);
			$tags[] = "param {$type} {$param->name} {$param->desc}";
		}
		foreach ($this->throws as $throws) {
			$tags[] = "throws " . $this->saneType($throws);
		}
		if ($this->name !== "__construct" && $this->returns) {

			if (count($this->returns) > 1) {
				$type = implode("|", $this->saneTypes(array_column($this->returns, 0)));
				$desc = "";
				foreach ($this->returns as list($typ, $ret)) {
					if (strlen($desc)) {
						$desc .= "\n\t\t or ";
					}
					$desc .= $this->saneType($typ, false) . " " . $ret;
				}
			} else {
				$type = $this->saneType($this->returns[0][0], false);
				$desc = $this->returns[0][1];
			}
			$tags[] = "return $type $desc";
		}
		$this->formatDesc(1, $tags);
		printf("\tfunction %s(", $this->name);
		$comma = "";
		$omit = $this->omitParamTypes();
		foreach ($this->params as $param) {
			print $comma;
			$param->formatAsParam($level, !$omit);
			$comma = ", ";
		}
		printf(")");
	}
}

class StructureOfConst extends StructureOf {
	public $name;
	public $desc;
	public $value;

	function format(int $level) {
		$indent = str_repeat("\t", $level);
		$this->formatDesc($level);
		printf("%sconst %s = ", $indent, $this->name);
		var_export($this->value);
	}
}

class StructureOfVar extends StructureOf {
	public $name;
	public $type;
	public $desc;
	public $modifiers;
	public $usages;
	public $defval;
	public $ref;

	function formatDefval() {
		if (strlen($this->defval)) {
			if (false && defined($this->defval)) {
				printf(" = ");
				var_export(constant($this->defval));
			} else if (strlen($this->defval)) {
				if (false !== strchr($this->defval, "\\") && $this->defval[0] != "\\") {
					$this->defval = "\\" . $this->defval;
				}
				printf(" = %s", $this->defval);
			}
		} elseif ($this->modifiers) {
			if (stristr($this->modifiers, "optional") !== false) {
				printf(" = NULL");
			}
		}
	}
	function formatAsProp($level) {
		$indent = str_repeat("\t", $level);
		$this->formatDesc($level,
			preg_split('/\s+/', $this->modifiers ." " . $this->usages, -1, PREG_SPLIT_NO_EMPTY)
			+ [-1 => "var " . $this->saneType($this->type, false)]
		);
		printf("%s%s %s", $indent, $this->modifiers, $this->name);
		$this->formatDefval();
	}

	function formatAsParam($level, $with_type = true) {
		if ($with_type && strlen($type = $this->saneType($this->type))) {
			printf("%s ", $type);
		}
		printf("%s%s", $this->ref, $this->name);
		$this->formatDefval();
	}
}
