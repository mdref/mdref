<?php

namespace mdref;

/**
 * A single reference entry
 */
class Entry implements \IteratorAggregate {
	/**
	 * Compound name
	 * @var string
	 */
	private $name;
	
	/**
	 * Split name
	 * @var array
	 */
	private $list;
	
	/**
	 * The containing repository
	 * @var \mdref\Repo
	 */
	private $repo;
	
	/**
	 * The file path, if the refentry exists
	 * @var type 
	 */
	private $path;
	
	/**
	 * The file instance of this entry
	 * @var \mdref\File
	 */
	private $file;
	
	/**
	 * @param string $name the compound name of the ref entry, e.g. "pq/Connection/exec"
	 * @param \mdref\Repo $repo the containing repository
	 */
	public function __construct($name, Repo $repo) {
		$this->repo = $repo;
		$this->name = $name;
		$this->list = explode("/", $name);
		$this->path = $repo->hasEntry($name);
	}
	
	/**
	 * Get the compound name, e.g. "pq/Connection/exec"
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Get the containing repository
	 * @return \mdref\Repo
	 */
	public function getRepo() {
		return $this->repo;
	}
	
	/**
	 * Get the file path, if any
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}
	
	/**
	 * Get the file instance of this entry
	 * @return \mdref\File
	 */
	public function getFile() {
		if (!$this->file) {
			$this->file = new File($this->path);
		}
		return $this->file;
	}
	
	/**
	 * Read the title of the ref entry file
	 * @return string
	 */
	public function getTitle() {
		if ($this->isFile()) {
			return $this->getFile()->readTitle();
		}
		if ($this->isRoot()) {
			return $this->repo->getRootEntry()->getTitle();
		}
		return $this->name;
	}
	
	/**
	 * Read the description of the ref entry file
	 * @return string
	 */
	public function getDescription() {
		if ($this->isFile()) {
			return $this->getFile()->readDescription();
		}
		if ($this->isRoot()) {
			return $this->repo->getRootEntry()->getDescription();
		}
		return $this;
	}
	
	/**
	 * Read the intriductory section of the refentry file
	 * @return string
	 */
	public function getIntro() {
		if ($this->isFile()) {
			return $this->getFile()->readIntro();
		}
		if ($this->isRoot()) {
			return $this->repo->getRootEntry()->getIntro();
		}
		return "";
	}
	
	/**
	 * Check if the refentry exists
	 * @return bool
	 */
	public function isFile() {
		return strlen($this->path) > 0;
	}
	
	/**
	 * Check if this is the first entry of the reference tree
	 * @return bool
	 */
	public function isRoot() {
		return count($this->list) === 1;
	}
	
	/**
	 * Get the parent ref entry
	 * @return \mdref\Entry
	 */
	public function getParent() {
		if ("." !== ($dirn = dirname($this->name))) {
			return $this->repo->getEntry($dirn);
		}
	}
	
	/**
	 * Get the list of parents up-down
	 * @return array
	 */
	public function getParents() {
		$parents = array();
		for ($parent = $this->getParent(); $parent; $parent = $parent->getParent()) {
			array_unshift($parents, $parent);
		}
		return $parents;
	}
	
	/**
	 * Guess whether this ref entry is about a function or method
	 * @return bool
	 */
	public function isFunction() {
		$base = end($this->list);
		return $base{0} === "_" || ctype_lower($base{0});
	}
	
	/**
	 * Guess whether this ref entry is about a namespace, interface or class
	 * @return bool
	 */
	public function isNsClass() {
		$base = end($this->list);
		return ctype_upper($base{0});
	}
	
	/**
	 * Display name
	 * @return string
	 */
	public function __toString() {
		$parts = explode("/", trim($this->getName(), "/"));
		$myself = array_pop($parts);
		if (!$parts) {
			return $myself;
		}
		$parent = end($parts);
		
		switch ($myself{0}) {
		case ":":
			return "★" . substr($myself, 1);
			
		default:
			if (!ctype_lower($myself{0}) || ctype_lower($parent{0})) {
				return $myself;
			}
		case "_":
			return $parent . "::" . $myself;
		}
	}
	
	/**
	 * Get the base name of this ref entry
	 * @return string
	 */
	public function getBasename() {
		return dirname($this->path) . "/" . basename($this->path, ".md");
	}
	
	/**
	 * Guess whether there are any child nodes
	 * @param string $glob
	 * @return boolean
	 */
	function hasIterator($glob = null, $loose = false) {
		if (strlen($glob)) {
			return glob($this->getBasename() . "/$glob") ||
				($loose && glob($this->getBasename() . "/*/$glob"));
		} elseif ($this->isRoot()) {
			return true;
		} else {
			return is_dir($this->getBasename());
		}
	}
	
	/**
	 * Guess whether there are namespace/interface/class child nodes
	 * @return bool
	 */
	function hasNsClasses() {
		return $this->hasIterator("/[A-Z]*.md", true);
	}
	
	/**
	 * Guess whether there are function/method child nodes
	 * @return bool
	 */
	function hasFunctions() {
		return $this->hasIterator("/[a-z_]*.md");
	}
	
	/**
	 * Implements \IteratorAggregate
	 * @return \mdref\Tree child nodes
	 */
	function getIterator() {
		return new Tree($this->getBasename(), $this->repo);
	}
}
