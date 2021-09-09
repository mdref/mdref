<?php

namespace mdref;

use function class_exists;

abstract class Formatter {
    abstract function formatString(string $string) : string;
    abstract function formatFile(string $file) : string;

	static function factory() : Formatter {
		if (class_exists("League\\CommonMark\\GithubFlavoredMarkdownConverter", true)) {
			return new Formatter\League;
		}
		if (extension_loaded("discount")) {
			return new Formatter\Discount;
		}
		throw new \Exception("No Markdown implementation found");
	}
}
