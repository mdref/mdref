<?php

namespace mdref;


use InvalidArgumentException;
use IteratorAggregate;
use function basename;
use function current;
use function file_get_contents;
use function glob;
use function is_file;
use function realpath;
use function rtrim;
use function sprintf;
use function trim;

/**
 * A reference repo
 */
class Repo implements IteratorAggregate {
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
	public function __construct(string $path) {
		if (!($mdref = current(glob("$path/*.mdref")))) {
			throw new InvalidArgumentException(
				sprintf("Not a reference, could not find '*.mdref': '%s'", $path));
		}

		$this->path = realpath($path);
		$this->name = basename($mdref, ".mdref");
		$this->edit = trim(file_get_contents($mdref));
	}

	/**
	 * Get the repository's name
	 * @return string
	 */
	public function getName() : string {
		return $this->name;
	}

	/**
	 * Get the path of the repository or a file in it
	 * @param string $file
	 * @return string
	 */
	public function getPath(string $file = "") : string {
		return $this->path . "/$file";
	}

	/**
	 * Get the edit url for a ref entry
	 * @param string $entry
	 * @return string
	 */
	public function getEditUrl(string $entry) : string {
		return sprintf($this->edit, $entry);
	}

	/**
	 * Get the file path of an entry in this repo
	 *
	 * @param string $entry
	 * @param string|null $canonical
	 * @return string file path
	 */
	public function hasEntry(string $entry, ?string &$canonical = null) : ?string {
		$trim = rtrim($entry, "/");
		$file = $this->getPath("$trim.md");
		if (is_file($file)) {
			if ($trim !== $entry) {
				$canonical = $trim;
			}
			return $file;
		}
		$file = $this->getPath($this->getName()."/$entry.md");
		if (is_file($file)) {
			$canonical = $this->getName() . "/" . $entry;
			return $file;
		}
		return null;
	}

	/**
	 * Get the canonical entry name of a file in this repo
	 * @param string $file
	 * @return string entry
	 */
	public function hasFile(string $file) : ?string {
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
		return null;
	}

	/**
	 * Check whether the repo has a stub file to serve
	 * @param string|null $path receives the path if there's a stub
	 * @return bool
	 */
	public function hasStub(string &$path = null) : bool {
		$path = $this->getPath($this->getName() . ".stub.php");
		return is_file($path) && is_readable($path);
	}

	/**
	 * Get an Entry instance
	 * @param string $entry
	 * @return \mdref\Entry
	 * @throws \OutOfBoundsException
	 */
	public function getEntry(string $entry) : Entry {
		return new Entry($entry, $this);
	}

	/**
	 * Get the root Entry instance
	 * @return \mdref\Entry
	 */
	public function getRootEntry() : Entry {
		return new Entry($this->name, $this);
	}

	/**
	 * Implements \IteratorAggregate
	 * @return \mdref\Tree
	 */
	public function getIterator() : Tree {
		return new Tree($this->path, $this);
	}
}
