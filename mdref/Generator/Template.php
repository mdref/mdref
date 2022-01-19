<?php

namespace mdref\Generator;

use http\Exception\RuntimeException;

class Template {
	public function __construct(
		private string $file,
		private int $offset
	) {}

	public function source() {
		return file_get_contents($this->file, false, null, $this->offset);
	}

	private function cache(string $cache_file) : string {
		$cache_path = dirname($cache_file);
		if (!is_dir($cache_path) && !mkdir($cache_path, 0700, true)) {
			throw new RuntimeException(error_get_last()["message"]);
		}
		if (!file_put_contents($cache_file, '<?php namespace mdref\\Generator; ?>' . $this->source())) {
			throw new RuntimeException(error_get_last()["message"]);
		}
		return $cache_file;
	}

	public function __toString() : string {
		$cache_file = sys_get_temp_dir() . "/mdref/generate." . basename($this->file) . ".md.tmp";
		$cache_stat = @stat($cache_file);
		if ($cache_stat && $cache_stat["mtime"] >= filemtime($this->file)) {
			return $cache_file;
		}

		return $this->cache($cache_file);
	}
}
