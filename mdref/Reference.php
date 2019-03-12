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
		return preg_replace("/[^[:alnum:]\.:_]/", ".", $anchor);
	}

	/**
	 * @param string $string
	 * @return string
	 * @throws \Exception
	 */
	public function formatString(string $string) : string {
		if (extension_loaded("discount")) {
			$md = \MarkdownDocument::createFromString($string);
			$md->compile(\MarkdownDocument::AUTOLINK);
			return $md->getHtml();
		}
		if (extension_loaded("cmark")) {
			$node = \CommonMark\Parse($string);
			return \CommonMark\Render\HTML($node);
		}
		throw new \Exception("No Markdown implementation found");
	}

	/**
	 * @param string $file
	 * @return string
	 * @throws \Exception
	 */
	public function formatFile(string $file) : string {
		if (extension_loaded("discount")) {
			$fd = fopen($file, "r");
			$md = \MarkdownDocument::createFromStream($fd);
			$md->compile(\MarkdownDocument::AUTOLINK | \MarkdownDocument::TOC);
			$html = $md->getHtml();
			fclose($fd);
			return $html;
		}
		if (extension_loaded("cmark")) {
			$node = \CommonMark\Parse(file_get_contents($file));
			return \CommonMark\Render\HTML($node);
		}
		throw new \Exception("No Markdown implementation found");
	}
}
