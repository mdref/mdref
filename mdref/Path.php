<?php

namespace mdref;

/**
 * A path made out of a base dir and an thereof relative path name.
 */
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
	
	/**
	 * @param string $baseDir
	 * @param string $path
	 */
	function __construct($baseDir = "", $path = "") {
		$this->baseDir = $baseDir;
		$this->path = $path;
	}
	
	/**
	 * Create a copy of this path with a different path name
	 * 
	 * @param string $path
	 * @return \mdref\Path
	 */
	function __invoke($path) {
		$that = clone $this;
		$that->path = $path;
		return $that;
	}
	
	/**
	 * Retrurns the full path as string
	 * @return string
	 */
	function __toString() {
		return $this->getFullPath();
	}

	/**
	 * The base directory
	 * @return string
	 */
	function getBaseDir() {
		return $this->baseDir;
	}
	
	/**
	 * The path name relative to the base dir
	 * @return string
	 */
	function getPathName() {
		return $this->path;
	}
	
	/**
	 * The full path
	 * @param string $ext extension
	 * @return string
	 */
	function getFullPath($ext = "") {
		return $this->baseDir . DIRECTORY_SEPARATOR . $this->path . $ext;
	}
	
	/**
	 * Retrieve a another subpath within the base dir
	 * @param type $path
	 * @return string
	 */
	function getSubPath($path) {
		return trim(substr($path, strlen($this->baseDir)), DIRECTORY_SEPARATOR);
	}
	
	function isFile($ext = ".md") {
		return is_file($this->getFullPath($ext));
	}
	
	function toHtml() {
		$head = sprintf("<h1>%s</h1>\n", htmlspecialchars(basename($this->getPathName())));
		if ($this->isFile()) {
			$html = htmlspecialchars(file_get_contents($this->getFullPath()));
		} elseif ($this->isFile("")) {
			$html = htmlspecialchars(file_get_contents($this->getFullPath("")));
		} else {
			throw new \http\Controller\Exception(404, "Not Found: {$this->getPathName()}");
		}
		return $head . "<pre>" . $html ."</pre>"; 
	}
}