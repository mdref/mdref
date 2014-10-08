<?php

namespace mdref;

/**
 * The complete available reference
 */
class Reference implements \IteratorAggregate {
	/**
	 * List of mdref repositories
	 * @var array
	 */
	private $repos = array();
	
	/**
	 * @param array $refs list of mdref repository paths
	 */
	public function __construct(array $refs) {
		foreach ($refs as $path) {
			$repo = new Repo($path);
			$this->repos[$repo->getName()] = $repo;
		}
	}
	
	/**
	 * Lookup the repo containing a ref entry
	 * @param string $entry requested reference entry, e.g. "pq/Connection/exec"
	 * @param type $canonical
	 * @return \mdref\Repo|NULL
	 */
	public function getRepoForEntry($entry, &$canonical = null) {
		foreach ($this->repos as $repo) {
			if ($repo->hasEntry($entry, $canonical)) {
				return $repo;
			}
		}
	}
	
	/**
	 * Implements \IteratorAggregate
	 * @return \ArrayIterator repository list
	 */
	public function getIterator() {
		return new \ArrayIterator($this->repos);
	}
	
}
