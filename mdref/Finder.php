<?php

namespace mdref;

/**
 * Find markdown reference files in several REFPATH paths. 
 * 
 * The base URL is used to extract the relative identifier out of the request 
 * url in Finder::find().
 * 
 * Use the created Path of Finder::find() for Finder::glob() to find subrefs.
 */
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
	 * @return \http\Controller\Url
	 */
	function getBaseUrl() {
		return $this->baseUrl;
	}
	
	/**
	 * Find a markdown reference file in one REFPATH. If nothing could be found
	 * an empty Path will be returned.
	 * 
	 * @param \http\Url $requestUrl
	 * @return Path
	 */
	function find(\http\Url $requestUrl, $ext = ".md") {
		$file = implode(DIRECTORY_SEPARATOR, $this->baseUrl->params($requestUrl));
		
		foreach ($this->refs as $base) {
			$path = new Path($base, $file);
			if ($path->isFile($ext)) {
				return $path;
			}
		}
		
		return new Path;
	}

	/**
	 * Glob either in a Path's base dir, or, if the path does not have a base 
	 * dir set, in each REFPATH paths.
	 * 
	 * @param \mdref\Path $path
	 * @param string $pattern glob pattern
	 * @param int $flags glob flags
	 * @return array glob result
	 */
	function glob(Path $path, $pattern, $flags = GLOB_BRACE) {
		if (strlen($path->getBaseDir())) {
			return glob($path->getFullPath($pattern), $flags) ?: array();
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
