<?php

namespace mdref;

/**
 * The RefEntry class represents a reference entry, i.e. a .md file
 */
class RefEntry
{
	/**
	 * @var \mdref\Path
	 */
	protected $path;
	
	/**
	 * @var string
	 */
	protected $entry;
	
	/**
	 * @var resource
	 */
	protected $file;
	
	/**
	 * @param \mdref\Path $path
	 * @param type $entry
	 */
	function __construct(Path $path, $entry = null) {
		$this->path = $path;
		$this->entry = trim($entry ?: $path->getPathName(), DIRECTORY_SEPARATOR);
	}
	
	/**
	 * Clean up the file handle
	 */
	function __destruct() {
		if (is_resource($this->file)) {
			fclose($this->file);
		}
	}
	
	/**
	 * Format as URL
	 * @return string
	 */
	function formatUrl() {
		return htmlspecialchars($this->entry);
	}
	
	private function joinLink(array $parts) {
		$link = "";
		$upper = ctype_upper($parts[0][0]);;
		for ($i = 0; $i < count($parts); ++$i) {
			if (!strlen($parts[$i]) || $parts[$i] === ".") {
				continue;
			}
			if (strlen($link)) {
				if ($parts[$i][0] === ":") {
					$link = "";
				} elseif ($upper && !ctype_upper($parts[$i][0])) {
					$link .= "::";
				} else {
					$link .= "\\";
				}
			}
			$link .= trim($parts[$i], ": ");
			$upper = ctype_upper($parts[$i][0]);
		}
		return $link;
	}

	/**
	 * Format as link text
	 * @param bool $basename whether to use the basename only
	 * @return string
	 */
	function formatLink($basename = false) {
		$link = "";
		if (strlen($this->entry)) {
			$parts = explode(DIRECTORY_SEPARATOR, $this->entry);
			$link = $basename ? end($parts) : $this->joinLink($parts);
		}
		return htmlspecialchars($link);
	}
	
	/**
	 * Create a consolidated Path of this entry
	 * @return \mdref\Path
	 */
	function getPath() {
		$path = $this->path;
		$file = $path($this->entry);
		return $file;
	}
	
	private function openFile() {
		if (!is_resource($this->file)) {
			$file = $this->getPath();
			
			if (!$file->isFile()) {
				throw new \Exception("Not a file: '{$file}'");
			}
			if (!$this->file = fopen($file->getFullPath(".md"), "r")) {
				throw new \Exception("Could not open {$file}");
			}
		}
	}
	
	/**
	 * Read the title of the refentry
	 * @return string
	 */
	function readTitle() {
		$this->openFile();
		fseek($this->file, 1, SEEK_SET);
		return fgets($this->file);
	}
	
	/**
	 * Read the description of the refentry
	 * @return string
	 */
	function readDescription() {
		$this->openFile();
		fseek($this->file, 0, SEEK_SET);
		fgets($this->file);
		fgets($this->file);
		return fgets($this->file);
	}
	
	/**
	 * Format a "Edit me" URL. The project reference top directory needs a 
	 * »name«.mdref file besides its »name«.md entry point with the edit URL
	 * printf template as content. The sole printf argument is the relative 
	 * path of the entry.
	 * @return string
	 */
	function formatEditUrl() {
		$path = $this->path;
		$base = current(explode(DIRECTORY_SEPARATOR, $path->getPathName()));
		$file = $path($base);
		if ($file->isFile(".mdref")) {
			return sprintf(file_get_contents($file->getFullPath(".mdref")),
					$this->entry);
		}
	}
	
	/**
	 * Recurse into the reference tree
	 * @param \mdref\Finder $refs
	 * @param string $pattern
	 * @param callable $cb
	 */
	function recurse(Finder $refs, $pattern, callable $cb) {
		$path = $refs->find($refs->getBaseUrl()->mod($this->entry));
		foreach (new RefListing($path, $refs->glob($path, $pattern)) as $entry) {
			/* @var $entry RefEntry */
			$cb($entry, $pattern, function($entry, $pattern) use ($refs, $cb) {
				$entry->recurse($refs, $pattern, $cb);
			});
		}
	}
}
