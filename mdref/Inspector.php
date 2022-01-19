<?php

namespace mdref;

class Inspector {
	/**
	 * @var array
	 */
	private $constants = [];
	/**
	 * @var array
	 */
	private $classes = [];
	/**
	 * @var array
	 */
	private $functions = [];

	public function inspectExtension($ext) {
		if (!($ext instanceof \ReflectionExtension)) {
			$ext = new \ReflectionExtension($ext);
		}

		$this->addClasses($ext->getClasses());
		$this->addFunctions($ext->getFunctions());
		$this->addConstants($ext->getConstants());
	}

	public function inspectNamespace(string $namespace) {
		$grep = function(array $a) use($namespace) {
			return preg_grep("/^" . preg_quote($namespace) . "\\\\/", $a);
		};

		$this->addClasses($this->wrap(\ReflectionClass::class, $grep(get_declared_interfaces())));
		$this->addClasses($this->wrap(\ReflectionClass::class, array_filter(
			$grep(get_declared_classes()),
			fn($cn) => !is_subclass_of($cn, \UnitEnum::class)
		)));
		$this->addClasses($this->wrap(\ReflectionEnum::class, array_filter(
			$grep(get_declared_classes()),
			fn($cn) => is_subclass_of($cn, \UnitEnum::class)
		)));
		$this->addFunctions($this->wrap(\ReflectionFunction::class, $grep(get_defined_functions()["internal"])));
		$this->addFunctions($this->wrap(\ReflectionFunction::class, $grep(get_defined_functions()["user"])));
		$this->addConstants($grep(get_defined_constants()));
	}

	private function wrap(string $klass, array $list) : array {
		$res = [];
		foreach ($list as $entry) {
			$res[] = new $klass($entry);
		}
		return $res;
	}

	private function addClasses(array $classes) {
		foreach ($classes as $c) {
			/* @var $c \ReflectionClass */
			$ns_name = $c->inNamespace() ? $c->getNamespaceName() : "";
			$this->classes[$ns_name][$c->getShortName()] = $c;
		}
	}

	private function addFunctions(array $functions) {
		foreach ($functions as $f) {
			/* @var $f \ReflectionFunction */
			$ns_name = $f->inNamespace() ? $f->getNamespaceName() : "";
			$this->functions[$ns_name][$f->getShortName()] = $f;
		}
	}

	private function addConstants(array $constants) {
		foreach ($constants as $constant => $value) {
			$ns_name = ($ns_end = strrpos($constant, "\\")) ? substr($constant, 0, $ns_end++) : "";
			$cn = substr($constant, $ns_end);
			$this->constants[$ns_name][$cn] = $value;
		}
	}

	/**
	 * @return array
	 */
	public function getConstants() : array {
		return $this->constants;
	}

	/**
	 * @return array
	 */
	public function getClasses(): array {
		return $this->classes;
	}

	/**
	 * @return array
	 */
	public function getFunctions(): array {
		return $this->functions;
	}
}
