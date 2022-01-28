<?php

namespace mdref\Generator;

use mdref\Generator;
use mdref\Generator\{Arg, Param};
use phpDocumentor\Reflection\{DocBlock, DocBlockFactory, DocBlock\Tags};

use Reflector;

class Scrap {
	public function __construct(
		protected Generator $gen,
		protected Reflector $ref,
		protected ?DocBlock $doc = null,
		bool $overrideDocFromRef = false,
	) {
		if ($overrideDocFromRef || !$this->doc) {
			$this->createDocBlock();
		}
		if (!$this->doc) {
			printf(... match (get_class($ref)) {
				\ReflectionClass::class => ["Missing docs for class %s\n", $ref->name],
				\ReflectionMethod::class => ["Missing docs for method %s::%s()\n", $ref->class, $ref->name],
				\ReflectionProperty::class => ["Missing docs for property %s %s::\$%s\n", $ref->getType(), $ref->class, $ref->name],
				\ReflectionClassConstant::class => ["Missing docs for constant %s::%s\n", $ref->class, $ref->name],
				\ReflectionFunction::class => ["Missing docs for function %s()\n", $ref->name],
				\ReflectionParameter::class => ($ref->getDeclaringClass()
					? ["Missing docs for method arg %s::%s(%s $%s)\n", $ref->getDeclaringClass()->name]
					: ["Missing docs for function arg %s(%s $%s)\n"])
					+ [3=>$ref->getDeclaringFunction()->name, $ref->getType(), $ref->name],
				default => ["Missing docs for ??? %s\n", $ref->name],
			});
		}
	}

	protected function createDocBlock() {
		if (method_exists($this->ref, "getDocComment")) {
			$docs = $this->ref->getDocComment();
		} elseif (($this->ref instanceof \ReflectionParameter) && $this->ref->getDeclaringClass()?->hasProperty($this->ref->name)) {
			// ctor promoted properties
			$docs = $this->ref->getDeclaringClass()->getProperty($this->ref->name)->getDocComment();
		}
		if (isset($docs) && $docs !== false && strlen($docs)) {
			$this->doc = DocBlockFactory::createInstance()->create((string) $docs);
		}
	}

	protected function toString(string $file, int $offset, array $imports = []) : string {
		$tpl = (string) new Template($file, $offset);
		$patch = function(string $scrap_class, Reflector $ref) : void {
			echo new $scrap_class($this->gen, $ref, $this->doc, true);
		};
		return (static function(Generator $gen, Reflector $ref, ?DocBlock $doc) use($patch) {
			$imports = func_get_arg(3); extract($imports); unset($imports);
			ob_start(null, 0x4000);
			include func_get_arg(4);
			return ob_get_clean();
		})($this->gen, $this->ref, $this->doc, $imports, $tpl);
	}

	protected function getParamTag(string $var_name) : ?Tags\Param {
		if ($this->doc) foreach ($this->doc->getTagsByName("param") as $param) {
			if ($param->getVariableName() === $var_name) {
				return $param;
			}
		}
		return null;
	}

	protected function getVarTag(string $var_name) : ?Tags\Var_ {
		if ($this->doc) foreach ($this->doc->getTagsByName("var") as $prop) {
			if ($prop->getVariableName() === $var_name) {
				return $prop;
			}
		}
		return null;
	}
}
