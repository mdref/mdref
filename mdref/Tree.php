<?php

namespace mdref;

class Tree implements \RecursiveIterator {
	/**
	 * The repository
	 * @var \mdref\Repo
	 */
	private $repo;
	
	/**
	 * List of first level entries
	 * @var array
	 */
	private $list = array();
	
	/**
	 * The list iterator
	 * @var array
	 */
	private $iter;
	
	/**
	 * @param string $path
	 * @param \mdref\Repo $repo
	 */
	public function __construct($path, Repo $repo) {
		if (!($list = glob("$path/*.md"))) {
			$list = glob("$path/*/*.md");
		}
		if ($list) {
			$this->list = array_filter($list, $this->generateFilter($list));
			sort($this->list, SORT_STRING);
		}
		$this->repo = $repo;
	}
	
	/**
	 * @param array $list
	 * @return callable
	 */
	private function generateFilter(array $list) {
		return function($v) use($list) {
			if ($v{0} === ".") {
				return false;
			}
			if (false !== array_search("$v.md", $list, true)) {
				return false;
			}
			
			$pi = pathinfo($v);
			if (isset($pi["extension"]) && "md" !== $pi["extension"]) {
				return false;
			}
			
			return true;
		};
	}

	/**
	 * Implements \Iterator
	 * @return \mdref\Entry
	 */
	public function current() {
		return $this->repo->getEntry($this->repo->hasFile(current($this->iter)));
	}
	
	/**
	 * Implements \Iterator
	 */
	public function next() {
		next($this->iter);
	}
	
	/**
	 * Implements \Iterator
	 * @return int
	 */
	public function key() {
		return key($this->iter);
	}
	
	/**
	 * Implements \Iterator
	 */
	public function rewind() {
		$this->iter = $this->list;
		reset($this->iter);
	}
	
	/**
	 * Implements \Iterator
	 * @return bool
	 */
	public function valid() {
		return null !== key($this->iter);
	}
	
	/**
	 * Implements \RecursiveIterator
	 * @return bool
	 */
	public function hasChildren() {
		return $this->current()->hasIterator();
	}
	
	/**
	 * Implements \RecursiveIterator
	 * @return \mdref\Tree
	 */
	public function getChildren() {
		return $this->current()->getIterator();
	}
}
