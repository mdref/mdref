<?php

namespace mdref;

/**
 * A list of markdown reference files
 */
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
	
	/**
	 * Implements \Countable
	 * @return int
	 */
	function count() {
		return count($this->entries);
	}
	
	/**
	 * Implements \Iterator
	 */
	function rewind() {
		reset($this->entries);
	}
	
	/**
	 * Implements \Iterator
	 * @return bool
	 */
	function valid() {
		return null !== key($this->entries);
	}
	
	/**
	 * Implements \Iterator
	 * @return string
	 */
	function key() {
		return $this->path->getSubPath(current($this->entries));
	}
	
	/**
	 * Implements \Iterator
	 */
	function next() {
		next($this->entries);
	}

	/**
	 * Implements \Iterator
	 * @return \mdref\RefEntry
	 */
	function current() {
		return new RefEntry($this->path, $this->key());//$this->format($this->key());
	}
	
	/**
	 * Get the parent reference entry
	 * @return null|\mdref\RefEntry
	 */
	function getParent() {
		switch ($parent = dirname($this->path->getPathName())) {
			case ".":
			case "":
				return null;
			default:
				return new RefEntry($this->path, $parent);
		}
	}
	
	/**
	 * Get the reference entry this reflist is based of
	 * @return \mdref\RefEntry
	 */
	function getSelf() {
		return new RefEntry($this->path);
	}
}
