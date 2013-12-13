<?php

namespace mdref;

class Finder
{
	/**
	 * Base URL
	 * @var \http\Controller\Url 
	 */
	protected $baseUrl;
	
	/**
	 * Reference paths
	 * @var array
	 */
	protected $refs = array();
	
	/**
	 * @param \http\Controller\Url $baseUrl
	 * @param mixed $paths array or string of paths with markdown references
	 */
	function __construct(\http\Controller\Url $baseUrl, $paths = ".") {
		if (!is_array($paths)) {
			$paths = explode(PATH_SEPARATOR, $paths);
		}
		$this->refs = $paths;
		$this->baseUrl = $baseUrl;
	}
	
	/**
	 * @param \http\Url $requestUrl
	 * @return Path
	 */
	function find(\http\Url $requestUrl) {
		$file = implode(DIRECTORY_SEPARATOR, 
				$this->baseUrl->params($requestUrl));
		
		foreach ($this->refs as $base) {
			$path = new Path($base, $file);
			if ($path->isFile()) {
				return $path;
			}
		}
	}

	function glob(Path $path, $pattern, $flags = GLOB_BRACE) {
		if (strlen($path->getBaseDir())) {
			return glob($path->getFullPath($pattern), $flags);
		}
		$glob = array();
		foreach ($this->refs as $ref) {
			$glob = array_merge($glob, array_map(function ($fn) use ($ref) {
				return substr($fn, strlen($ref));
			}, glob($ref . $pattern, $flags)));
		}
		return $glob;
	}
}
