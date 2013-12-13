<?php

namespace mdref;

class Path
{
	/**
	 * Computed path
	 * @var string
	 */
	protected $path = "";
	
	/**
	 * The base directory where path is located
	 * @var string
	 */
	protected $baseDir = "";
	
	function __construct($baseDir = "", $path = "") {
		$this->baseDir = $baseDir;
		$this->path = $path;
	}
	
	function __invoke($path) {
		$that = clone $this;
		$that->path = $path;
		return $that;
	}
	
	function __toString() {
		return $this->getFullPath();
	}

	function getBaseDir() {
		return $this->baseDir;
	}
	
	function getPathName() {
		return $this->path;
	}
	
	function getFullPath($ext = "") {
		return $this->baseDir . DIRECTORY_SEPARATOR . $this->path . $ext;
	}
	
	function getSubPath($path) {
		return trim(substr($path, strlen($this->baseDir)), DIRECTORY_SEPARATOR);
	}
	
	function isFile($ext = ".md") {
		return is_file($this->getFullPath($ext));
	}
	
	function toHtml() {
		if ($this->isFile()) {
			return htmlspecialchars(file_get_contents($this->getFullPath()));
		} elseif ($this->isFile("")) {
			return htmlspecialchars(file_get_contents($this->getFullPath("")));
		}
	}
}