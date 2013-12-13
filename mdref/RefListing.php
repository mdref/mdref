<?php

namespace mdref;

class RefListing implements \Countable, \Iterator
{
	/**
	 * @var \mdref\Path
	 */
	protected $path;
	
	/**
	 * @var array
	 */
	protected $entries;
	
	/**
	 * @param \mdref\Path $path
	 */
	function __construct(Path $path, array $files) {
		$this->path = $path;
		$this->entries = array_map(function($fn) {
			return substr(trim($fn, DIRECTORY_SEPARATOR), 0, -3);
		}, $files);
	}
	
	/**
	 * Copy constructor
	 * @param mixed $filter callable array filter or fnmatch pattern
	 * @return \mdref\RefListing
	 */
	function __invoke($filter) {
		die(__METHOD__);
		$that = clone $this;
		$that->entries =  array_filter($that->entries, is_callable($filter) 
				? $filter 
				: function($fn) use ($filter) {
					return fnmatch($filter, $fn);
				}
		);
		return $that;
	}
	
	function __toString() {
		return __METHOD__;
		return $this->format(substr($this->path, strlen($this->path->getBaseDir())));
	}
	
	function count() {
		return count($this->entries);
	}
	
	function rewind() {
		reset($this->entries);
	}
	
	function valid() {
		return null !== key($this->entries);
	}
	
	function key() {
		return $this->path->getSubPath(current($this->entries));
	}
	
	function next() {
		next($this->entries);
	}
	
	function current() {
		return new RefEntry($this->path, $this->key());//$this->format($this->key());
	}
	
	function getParent() {
		switch ($parent = dirname($this->path->getPathName())) {
			case ".":
			case "":
				return null;
			default:
				return new RefEntry($this->path, $parent);
		}
	}
	
	function getSelf() {
		return new RefEntry($this->path);
	}
	
	function format($entry) {
		return __METHOD__;
		$ns = "";
		if (strlen($entry = trim($entry, DIRECTORY_SEPARATOR))) {
			$upper = ctype_upper($entry[0]);
			$parts = explode(DIRECTORY_SEPARATOR, $entry);
			
			for ($i = 0; $i < count($parts); ++$i) {
				if (!strlen($parts[$i]) || $parts[$i] === ".") {
					continue;
				}
				if (strlen($ns)) {
					if ($upper && !ctype_upper($parts[$i][0])) {
						$ns .= "::";
					} else {
						$ns .= "\\";
					}
				}
				$ns .= $parts[$i];
				$upper = ctype_upper($parts[$i][0]);
			}
		}
		return $ns;
	}
}