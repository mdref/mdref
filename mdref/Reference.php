<?php

namespace mdref;

use ArrayIterator;
use Iterator;
use IteratorAggregate;
use function is_numeric;
use function preg_replace;

/**
 * The complete available reference
 */
class Reference implements IteratorAggregate {
	/**
	 * List of mdref repositories
	 * @var Repo[]
	 */
	private $repos = array();

	/**
	 * @var Formatter
	 */
	private $fmt;

	/**
	 * @param array $refs list of mdref repository paths
	 */
	public function __construct(array $refs, Formatter $fmt = null) {
		foreach ($refs as $path) {
			$repo = new Repo($path);
			$this->repos[$repo->getName()] = $repo;
		}
		$this->fmt = $fmt ?: new Formatter;
	}

	/**
	 * Get the formatter.
	 * @return Formatter
	 */
	public function getFormatter() : Formatter {
		return $this->fmt;
	}

	/**
	 * Lookup the repo containing a ref entry
	 * @param string $entry requested reference entry, e.g. "pq/Connection/exec"
	 * @param string $canonical
	 * @return \mdref\Repo|NULL
	 */
	public function getRepoForEntry(string $entry, string &$canonical = null) : ?Repo {
		foreach ($this->repos as $repo) {
			/** @var $repo Repo */
			if ($repo->hasEntry($entry, $canonical)) {
				return $repo;
			}
		}
		return null;
	}

	/**
	 * Implements IteratorAggregate
	 * @return ArrayIterator repository list
	 */
	public function getIterator() : Iterator {
		return new ArrayIterator($this->repos);
	}

	/**
	 * @param string $anchor
	 * @return string
	 */
	public function formatAnchor(string $anchor) : string {
		if (is_numeric($anchor)) {
			return "L$anchor";
		}
		return $this->fmt->formatSlug($anchor);
	}

	/**
	 * @param string $string
	 * @return string
	 * @throws \Exception, Exception
	 */
	public function formatString(string $string) : string {
		return $this->fmt->formatString($string);
	}

	/**
	 * @param string $file
	 * @return string
	 * @throws \Exception, Exception
	 */
	public function formatFile(string $file) : string {
		return $this->fmt->formatFile($file);
	}
}
