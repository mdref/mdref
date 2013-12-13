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
	 * @param array $files
	 */
	function __construct(Path $path, array $files) {
		$this->path = $path;
		$this->entries = array_map(function($fn) {
			return substr(trim($fn, DIRECTORY_SEPARATOR), 0, -3);
		}, $files);
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
}
