<?php

namespace mdref;

use Iterator;
use RecursiveIterator;
use function array_filter;
use function array_search;
use function basename;
use function current;
use function dirname;
use function glob;
use function is_dir;
use function key;
use function next;
use function pathinfo;
use function preg_match;
use function reset;
use function strcmp;
use function usort;

class Tree implements RecursiveIterator {
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
	public function __construct(string $path, Repo $repo) {
		if (realpath($path)."/" === $repo->getPath()) {
			$list = [$path ."/". $repo->getName() .".md"];
		} elseif (!($list = glob("$path/*.md"))) {
			$list = glob("$path/*/*.md");
		}
		if ($list) {
			$this->list = array_filter($list, $this->generateFilter($list));
			usort($this->list, $this->generateSorter());
		}
		$this->repo = $repo;
	}

	/**
	 * @param array $list
	 * @return callable
	 */
	private function generateFilter(array $list) : \Closure {
		return function($v) use($list) {
			if ($v[0] === ".") {
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
	 * @return callable
	 */
	private function generateSorter() : \Closure {
		return function($a, $b) {
			$ab = basename($a, ".md");
			$bb = basename($b, ".md");

			if ($ab[0] === ":" && $bb[0] === ":") {
				return strcmp($ab, $bb);
			} elseif ($ab[0] === ":") {
				return -1;
			} elseif ($bb[0] === ":") {
				return 1;
			}
/*
			$ad = is_dir(dirname($a)."/$ab");
			$bd = is_dir(dirname($b)."/$bb");

			if ($ad && $bd) {
				return strcmp($ab, $bb);
			} elseif ($ad) {
				return -1;
			} elseif ($bd) {
				return 1;
			}
*/
			$au = preg_match("/^\p{Lu}/", $ab);
			$bu = preg_match("/^\p{Lu}/", $bb);

			if ($au && $bu) {
				return strcmp($ab, $bb);
			} elseif ($au) {
				return -1;
			} elseif ($bu) {
				return 1;
			}

			return strcmp($ab, $bb);
		};
	}

	/**
	 * Implements \Iterator
	 * @return \mdref\Entry
	 */
	public function current() : Entry {
		return $this->repo->getEntry($this->repo->hasFile(current($this->iter)));
	}

	/**
	 * Implements \Iterator
	 */
	public function next() : void {
		next($this->iter);
	}

	/**
	 * Implements \Iterator
	 * @return int
	 */
	public function key() : int {
		return key($this->iter);
	}

	/**
	 * Implements \Iterator
	 */
	public function rewind() : void {
		$this->iter = $this->list;
		reset($this->iter);
	}

	/**
	 * Implements \Iterator
	 * @return bool
	 */
	public function valid() : bool {
		return null !== key($this->iter);
	}

	/**
	 * Implements \RecursiveIterator
	 * @return bool
	 */
	public function hasChildren() : bool {
		return $this->current()->hasIterator();
	}

	/**
	 * Implements \RecursiveIterator
	 * @return \mdref\Tree
	 */
	public function getChildren() : Tree {
		return $this->current()->getIterator();
	}
}
