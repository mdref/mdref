<?php

namespace mdref;

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
	
	function __construct(Path $path, $entry = null) {
		$this->path = $path;
		$this->entry = trim($entry ?: $path->getPathName(), DIRECTORY_SEPARATOR);
	}
	
	function __destruct() {
		if (is_resource($this->file)) {
			fclose($this->file);
		}
	}
	
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
				if ($upper && !ctype_upper($parts[$i][0])) {
					$link .= "::";
				} else {
					$link .= "\\";
				}
			}
			$link .= $parts[$i];
			$upper = ctype_upper($parts[$i][0]);
		}
		return $link;
	}

	function formatLink($basename = false) {
		$link = "";
		if (strlen($this->entry)) {
			$parts = explode(DIRECTORY_SEPARATOR, $this->entry);
			$link = $basename ? end($parts) : $this->joinLink($parts);
		}
		return htmlspecialchars($link);
	}
	
	function getPath() {
		$path = $this->path;
		$file = $path($this->entry);
		return $file;
	}
	
	private function openFile() {
		if (!is_resource($this->file)) {
			$file = $this->getPath();
			
			if (!$file->isFile()) {
				throw new \Exception("Not a file: '{$this->entry}'");
			}
			if (!$this->file = fopen($file->getFullPath(".md"), "r")) {
				throw new \Exception("Could not open {$this->entry}");
			}
		}
	}
	
	function readTitle() {
		$this->openFile();
		fseek($this->file, 1, SEEK_SET);
		return htmlspecialchars(fgets($this->file));
	}
	
	function readDescription() {
		$this->openFile();
		fseek($this->file, 0, SEEK_SET);
		fgets($this->file);
		fgets($this->file);
		return htmlspecialchars(fgets($this->file));
	}
	
	function formatEditUrl() {
		$path = $this->path;
		$base = current(explode(DIRECTORY_SEPARATOR, $path->getPathName()));
		$file = $path($base);
		if ($file->isFile(".mdref")) {
			return sprintf(file_get_contents($file->getFullPath(".mdref")),
					$this->entry);
		}
	}
}