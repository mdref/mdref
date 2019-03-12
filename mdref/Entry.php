<?php

namespace mdref;

use IteratorAggregate;
use function array_pop;
use function count;
use function ctype_lower;
use function ctype_upper;
use function dirname;
use function end;
use function explode;
use function implode;
use function strlen;
use function strtr;
use function substr;
use function trim;

/**
 * A single reference entry
 */
class Entry implements IteratorAggregate {
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
	 * @var ?string
	 */
	private $path;

	/**
	 * The file instance of this entry
	 * @var \mdref\File
	 */
	private $file;

	/**
	 * @param string $name the compound name of the ref entry, e.g. "pq/Connection/exec"
	 * @param Repo $repo the containing repository
	 */
	public function __construct(string $name, Repo $repo) {
		$this->repo = $repo;
		$this->name = $name;
		$this->list = explode("/", $name);
		$this->path = $repo->hasEntry($name);
	}

	/**
	 * Get the compound name, e.g. "pq/Connection/exec"
	 * @return string
	 */
	public function getName() : string {
		return $this->name;
	}

	/**
	 * Get the containing repository
	 * @return \mdref\Repo
	 */
	public function getRepo() : Repo {
		return $this->repo;
	}

	/**
	 * Get the file path, if any
	 * @return string
	 */
	public function getPath() : ?string {
		return $this->path;
	}

	/**
	 * Get the file instance of this entry
	 *
	 * @return \mdref\File
	 * @throws Exception
	 */
	public function getFile() : File {
		if (!$this->file) {
			$this->file = new File($this->path);
		}
		return $this->file;
	}

	/**
	 * Get edit URL
	 * @return string
	 */
	public function getEditUrl() : string {
		return $this->repo->getEditUrl($this->name);
	}

	/**
	 * Read the title of the ref entry file
	 *
	 * @return string
	 * @throws Exception
	 */
	public function getTitle() : string {
		if ($this->isFile()) {
			return trim($this->getFile()->readTitle());
		}
		if ($this->isRoot()) {
			return trim($this->repo->getRootEntry()->getTitle());
		}
		return $this->name;
	}

	/**
	 * Read the first line of the description of the ref entry file
	 *
	 * @return string
	 * @throws Exception
	 */
	public function getDescription() : string {
		if ($this->isFile()) {
			return trim($this->getFile()->readDescription());
		}
		if ($this->isRoot()) {
			return trim($this->repo->getRootEntry()->getDescription());
		}
		return $this;
	}

	/**
	 * Read the full description of the ref entry file
	 *
	 * @return string
	 * @throws Exception
	 */
	public function getFullDescription() : string {
		if ($this->isFile()) {
			return trim($this->getFile()->readFullDescription());
		}
		if ($this->isRoot()) {
			return trim($this->repo->getRootEntry()->getFullDescription());
		}
		return $this;
	}

	/**
	 * Read the intriductory section of the refentry file
	 *
	 * @return string
	 * @throws Exception
	 */
	public function getIntro() : string {
		if ($this->isFile()) {
			return trim($this->getFile()->readIntro());
		}
		if ($this->isRoot()) {
			return trim($this->repo->getRootEntry()->getIntro());
		}
		return "";
	}

	/**
	 * Check if the refentry exists
	 * @return bool
	 */
	public function isFile() : bool {
		return strlen($this->path) > 0;
	}

	/**
	 * Check if this is the first entry of the reference tree
	 * @return bool
	 */
	public function isRoot() : bool {
		return count($this->list) === 1;
	}

	/**
	 * Get the parent ref entry
	 * @return \mdref\Entry
	 */
	public function getParent() : ?Entry {
		switch ($dirn = dirname($this->name)) {
		case ".":
		case "/":
			return null;
		default:
			return $this->repo->getEntry($dirn);
		}
	}

	/**
	 * Get the list of parents up-down
	 * @return array
	 */
	public function getParents() : array {
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
	public function isFunction() : bool {
		$base = end($this->list);
		return $base{0} === "_" || ctype_lower($base{0});
	}

	/**
	 * Guess whether this ref entry is about a namespace, interface or class
	 * @return bool
	 */
	public function isNsClass() : bool {
		$base = end($this->list);
		return ctype_upper($base{0});
	}

	/**
	 * @return mixed
	 */
	public function getEntryName() {
		return end($this->list);
	}

	/**
	 * Get namespaced name
	 * @return string
	 */
	public function getNsName() : string {
		if ($this->isRoot()) {
			return $this->getName();
		} elseif ($this->isFunction()) {
			$parts = explode("/", trim($this->getName(), "/"));
			$self = array_pop($parts);
			return implode("\\", $parts) . "::" . $self;
		} else {
			return strtr($this->getName(), "/", "\\");
		}
	}

	/**
	 * Display name
	 * @return string
	 */
	public function __toString() : string {
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
	 * Get the dirname for child entries
	 * @return string
	 */
	public function getNextDirname() : string {
		return dirname($this->path) . "/" . basename($this->path, ".md");
	}

	/**
	 * Guess whether there are any child nodes
	 * @param string $glob
	 * @param bool $loose
	 * @return bool
	 */
	function hasIterator(?string $glob = null, bool $loose = false) : bool {
		if (strlen($glob)) {
			return glob($this->getNextDirname() . "/$glob") ||
				($loose && glob($this->getNextDirname() . "/*/$glob"));
		} elseif ($this->isRoot()) {
			return true;
		} elseif ($this->getNextDirname() !== "/") {
			return is_dir($this->getNextDirname());
		} else {
			return false;
		}
	}

	/**
	 * Guess whether there are namespace/interface/class child nodes
	 * @return bool
	 */
	function hasNsClasses() : bool {
		return $this->hasIterator("/[A-Z]*.md", true);
	}

	/**
	 * Guess whether there are function/method child nodes
	 * @return bool
	 */
	function hasFunctions() : bool {
		return $this->hasIterator("/[a-z_]*.md");
	}

	/**
	 * Implements IteratorAggregate
	 * @return Tree child nodes
	 */
	function getIterator() : Tree {
		return new Tree($this->getNextDirname(), $this->repo);
	}

	/**
	 * Get the structure of the refentry
	 * @return Structure
	 */
	function getStructure() : Structure {
		return new Structure($this);
	}
}
