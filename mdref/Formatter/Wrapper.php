<?php
namespace mdref\Formatter;

use DomNode;
use DOMText;
use ReflectionExtension;
use mdref\Formatter;

class Wrapper {
	protected $docref = "https://php.net/manual/en/%s";
	protected $types = [
		"language.types.declarations#language.types.declarations.%s" => ["void", "mixed"],
		"language.types.%s" => ["null", "boolean", "integer", "float", "string", "resource", "array", "callable", "iterable"],
		"language.types.null" =>  ["NULL"],
		"language.types.boolean" => ["true", "TRUE", "false", "FALSE", "bool",  "BOOL"],
		"language.types.integer" => ["int", "long"],
		"language.types.float" => ["double", "real"],
		"language.types.object" => ["stdClass"],
		"language.types.callable" => ["callback"],
		"language.types.enumerations" => ["enum"],
		"language.references" => ["reference"],
	];
	protected $exts = ["standard", "core", "spl", "json", "date"];

	function __construct(
		protected Formatter $fmt
	) {}

	public function wrap(DOMText $node, $pld) : void {
		$nodes = [];

		$split = "[&?\(\)\|\"'\s\][\.,-]+";
		$items = preg_split("/($split)/", $node->textContent, 0, PREG_SPLIT_DELIM_CAPTURE);
		foreach ($items as $item) {
			if (preg_match("/^($split|[[:punct:]+])*$/", $item)) {
				$nodes[] = $node->ownerDocument->createTextNode($item);
				continue;
			}

			$new = $this->wrapType($node, $item, $pld)
				?: $this->wrapKeyWord($node, $item, $pld)
					?: $this->wrapSpecial($node, $item, $pld);
			if (is_array($new)) {
				foreach ($new as $n)
					$nodes[] = $n;
			} elseif ($new) {
				$nodes[] = $new;
			} else {
				$nodes[] = $node->ownerDocument->createTextNode($item);
			}
		}
		if ($nodes) {
			$parent = $node->parentNode;
			$new_node = array_pop($nodes);
			$parent->replaceChild($new_node, $node);
			foreach ($nodes as $prev_node) {
				$parent->insertBefore($prev_node, $new_node);
			}
		}
	}

	protected function getType(string $item) : ?string {
		static $types;
		if (!$types) {
			foreach ($this->types as $doc => $list) foreach ($list as $type) {
				$types[$type] = sprintf($this->docref, sprintf($doc, $type));
			}
			foreach ($this->exts as $ext) foreach ((new ReflectionExtension($ext))->getClassNames() as $class) {
				$types[$class] = sprintf($this->docref, "class." . strtolower($class));
			}
		}

		$item = trim($item, "\\");
		if (!isset($types[$item])) {
			return null;
		}
		return $types[$item];
	}
	protected function wrapType(DOMText $node, string $item, $pld) : ?DOMNode {
		if (!($type = $this->getType($item))) {
			return null;
		}
		$a = $node->ownerDocument->createElement("a");
		$a->setAttribute("href", $type);
		$a->textContent = $item;
		$code = $node->ownerDocument->createElement("code");
		$code->insertBefore($a);
		return $code;
	}

	protected function wrapKeyword(DOMText $node, string $item, $pld) : DomNode|array|null {
		switch ($item) {
			case "is":
				if ($node->parentNode->nodeName !== "h1") {
					break;
				}
			case "extends":
			case "implements":
				if ($node->parentNode->nodeName === "h1") {
					$nodes = [
						$node->ownerDocument->createElement("br"),
						$node->ownerDocument->createEntityReference("nbsp"),
						$new = $node->ownerDocument->createElement("em")
					];
					$new->textContent = $item;
					return $nodes;
				}
			case "class":
			case "enum":
			case "interface":
			case "namespace":
			case "public":
			case "protected":
			case "private":
			case "static":
			case "final":
			case "abstract":
			case "self":
			case "parent":
				$new = $node->ownerDocument->createElement("em");
				$new->textContent = $item;
				return $new;
		}
		return null;
	}

	protected function isFirstDeclaration(DOMNode $node, string $item, bool $is_slug = false) : bool {
		return $node->parentNode->nodeName === "li"
			&& !$node->ownerDocument->getElementById($is_slug ? $item : $this->fmt->formatSlug($item));
	}

	protected function isVar(string $item) : bool {
		return str_starts_with($item, "\$");
	}

	protected function wrapVar(DOMNode $node, string $item, $pld) : DOMNode {
		$ele = $node->ownerDocument->createElement("span");
		$ele->setAttribute("class", "var");
		$ele->textContent = $item;

		if (!empty($pld->currentSection)) {
			$slug = $this->fmt->formatSlug($item);
			if ($this->isFirstDeclaration($node, $slug, true)) {
				$perm = $this->fmt->createPermaLink($ele, $slug, $pld);
				$ele->insertBefore($perm);
			}
		}
		return $ele;
	}

	protected function isNamespaced(DOMNode $node, string $item, $pld) : bool {
		return str_contains($item, "\\") || str_contains($item, "::");
	}

	protected function wrapNamespaced(DOMNode $node, string $item, $pld) : ?DOMNode {
		$href = preg_replace("/\\\\|::/", "/", trim($item, "\\:"));
		$canonical = null;
		$repo = $pld->refs->getRepoForEntry($href, $canonical);

		if ($repo) {
			if (!empty($canonical)) {
				$href = $canonical;
			}
			$link = $node->ownerDocument->createElement("a");
			$link->setAttribute("href", $href);
			$link->textContent = $item;
			return $link;
		}

		$hash = basename($href);
		$href = dirname($href);
		$repo = $pld->refs->getRepoForEntry($href, $canonical);
		if ($repo) {
			if (!empty($canonical)) {
				$href = $canonical;
			}
			$link = $node->ownerDocument->createElement("a");
			$link->setAttribute("href", "$href#$hash");
			$link->textContent = $item;
			return $link;
		}

		return null;
	}

	protected function wrapConstant(DOMNode $node, string $item, $pld) : ?DOMNode {
		$strict = "_";
		if (!empty($pld->currentSection)) {
			switch ($pld->currentSection) {
				case "Properties:":
				case "Constants:":
					$strict = "";
					break;
			}
		}
		if (preg_match("/^[A-Z]({$strict}[A-Z0-9_v])+\$/", $item)) {
			// assume some constant
			$span = $node->ownerDocument->createElement("span");
			$span->setAttribute("class", "constant");
			$span->textContent = $item;
			if (!$strict && $pld->currentSection === "Constants:" && $node->parentNode->nodeName === "li" && $node->parentNode->firstChild === $node) {
				$perm = $this->fmt->createPermaLink($span, $this->fmt->formatSlug($item), $pld);
				$span->insertBefore($perm);
			}
			return $span;
		}

		return null;
	}

	protected function wrapSpecial(DOMNode $node, string $item, $pld) : ?DOMNode {
		if ($this->isVar($item)) {
			if (($ele = $this->wrapVar($node, $item, $pld))) {
				return $ele;
			}
		}
		if ($this->isNamespaced($node, $item, $pld)) {
			if (($ele = $this->wrapNamespaced($node, $item, $pld))) {
				return $ele;
			}
		}
		return $this->wrapConstant($node, $item, $pld);
	}
}
