<?php

namespace mdref;


/**
 * A reference repo
 */
class Repo implements \IteratorAggregate {
	/**
	 * The name of the repository
	 * @var string
	 */
	private $name;
	
	/**
	 * The path to the repository
	 * @var string
	 */
	private $path;
	
	/**
	 * The edit url template
	 * @var string
	 */
	private $edit;
	
	/**
	 * Path to the repository containing the name.mdref file
	 * @param string $path
	 * @throws \InvalidArgumentException
	 */
	public function __construct($path) {
		if (!($mdref = current(glob("$path/*.mdref")))) {
			throw new \InvalidArgumentException(
				sprintf("Not a reference, could not find '*.mdref': '%s'",
					$path));
		}
		
		$this->path = realpath($path);
		$this->name = basename($mdref, ".mdref");
		$this->edit = trim(file_get_contents($mdref));
	}
	
	/**
	 * Get the repository's name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Get the path of the repository or a file in it
	 * @param string $file
	 * @return string
	 */
	public function getPath($file = "") {
		return $this->path . "/$file";
	}
	
	/**
	 * Get the edit url for a ref entry
	 * @param string $entry
	 * @return string
	 */
	public function getEditUrl($entry) {
		return sprintf($this->edit, $entry);
	}
	
	/**
	 * Get the file path of an entry in this repo
	 * @param string $entry
	 * @return string file path
	 */
	public function hasEntry($entry, &$canonical = null) {
		$file = $this->getPath("$entry.md");
		if (is_file($file)) { 
			return $file;
		}
		$file = $this->getPath($this->getName()."/$entry.md");
		if (is_file($file)) {
			$canonical = $this->getName() . "/" . $entry;
			return $file;
		}
	}
	
	/**
	 * Get the canonical entry name of a file in this repo
	 * @param string $file
	 * @return string entry
	 */
	public function hasFile($file) {
		if (($file = realpath($file))) {
			$path = $this->getPath();
			$plen = strlen($path);
			if (!strncmp($file, $path, $plen)) {
				$dirname = dirname(substr($file, $plen));
				$basename = basename($file, ".md");
				
				if ($dirname === ".") {
					return $basename;
				}
				
				return  $dirname . "/". $basename;
			}
		}
	}
	
	/**
	 * Get an Entry instance
	 * @param string $entry
	 * @return \mdref\Entry
	 * @throws \OutOfBoundsException
	 */
	public function getEntry($entry) {
		return new Entry($entry, $this);
	}
	
	/**
	 * Get the root Entry instance
	 * @return \mdref\Entry
	 */
	public function getRootEntry() {
		return new Entry($this->name, $this);
	}
	
	/**
	 * Implements \IteratorAggregate
	 * @return \mdref\Tree
	 */
	public function getIterator() {
		return new Tree($this->path, $this);
	}
}
