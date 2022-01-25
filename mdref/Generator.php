<?php

namespace mdref;

use mdref\Generator\{Cls, Func};

class Generator {
	protected string $destination;

	public function __construct(string $destination = ".") {
		$this->destination = $destination;
	}

	/**
	 * @param array<string, array<string, \ReflectionFunctionAbstract>> $functions
	 * @return void
	 */
	public function generateFunctions(array $functions) : void {
		foreach ($functions as $ns => $funcs) {
			$ns_path = $this->destination . "/" . strtr($ns, "\\", "/");
			foreach ($funcs as $fn => $rf) {
				$fn_file = "$ns_path/$fn.md";
				fprintf(STDERR, "Generating %s\n", $fn_file);
				is_dir($ns_path) || mkdir($ns_path, 0770, true);
				file_put_contents($fn_file, new Func($this, $rf));
			}
		}
	}

	/**
	 * @param array<string, array<string, \ReflectionClass>> $classes
	 * @return void
	 */
	public function generateClasses(array $classes) : void {
		foreach ($classes as $ns => $cls) {
			$ns_path = $this->destination . "/" . strtr($ns, "\\", "/");
			foreach ($cls as $cn => $rc) {
				$cn_path = "$ns_path/$cn";
				$cn_file = "$cn_path.md";
				fprintf(STDERR, "Generating %s\n", $cn_file);
				is_dir($ns_path) || mkdir($ns_path, 0770, true);
				file_put_contents($cn_file, new Cls($this, $rc));
				$this->generateMethods($rc);
			}
		}
	}

	private function generateMethods(\ReflectionClass $rc) : void {
		$funcs = [];
		foreach ($rc->getMethods(\ReflectionMethod::IS_PUBLIC) as $rm) {
			if ($rm->getDeclaringClass()->getName() === $rc->getName()) {
				foreach ($rc->getInterfaces() as $ri) {
					if ($ri->hasMethod($rm->getName())) {
						continue 2;
					}
				}
				$funcs[$rc->getName()][$rm->getName()] = $rm;
			}
		}
		$this->generateFunctions($funcs);
	}
}
