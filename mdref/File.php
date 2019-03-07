<?php

namespace mdref;

/**
 * A ref entry file
 */
class File {
	/**
	 * @var resource
	 */
	private $fd;

	/**
	 * Open the file
	 * @param string $path
	 */
	public function __construct($path) {
		$this->fd = fopen($path, "rb");
	}

	/**
	 * Read the title of the refentry
	 * @return string
	 */
	public function readTitle() {
		if ($this->rewind(1)) {
			return fgets($this->fd);
		}
	}

	/**
	 * Read the description (first line) of the refentry
	 * @return string
	 */
	public function readDescription() {
		if ($this->rewind()
		&& (false !== fgets($this->fd))
		&& (false !== fgets($this->fd))) {
			return fgets($this->fd);
		}
	}

	/**
	 * Read the full description (first section) of the refentry
	 * @return string
	 */
	public function readFullDescription() {
		$desc = $this->readDescription();
		while (false !== ($line = fgets($this->fd))) {
			if ($line{0} === "#") {
				break;
			} else {
				$desc .= $line;
			}
		}
		return $desc;
	}

	/**
	 * Read the first subsection of a global refentry
	 * @return string
	 */
	public function readIntro() {
		$intro = "";
		if ($this->rewind()) {
			$header = false;

			while (!feof($this->fd)) {
				if (false === ($line = fgets($this->fd))) {
					break;
				}
				/* search first header and read until next header*/
				if ($this->isHeading($line)) {
					if ($header) {
						break;
					} else {
						$header = true;
						continue;
					}
				}
				if ($header) {
					$intro .= $line;
				}
			}
		}
		return $intro;
	}

	public function readSection($title) {
		$section = "";
		if ($this->rewind()) {
			while (!feof($this->fd)) {
				if (false === ($line = fgets($this->fd))) {
					break;
				}
				/* search for heading with $title and read until next heading */
				if ($this->isHeading($line, $title)) {
					do {
						if (false === $line = fgets($this->fd)) {
							break;
						}
						if ($this->isHeading($line)) {
							break;
						}
						$section .= $line;
					} while (true);
				}
			}
		}
		return $section;
	}

	private function rewind($offset = 0) {
		return 0 === fseek($this->fd, $offset, SEEK_SET);
	}

	private function isHeading($line, $title = null) {
		if ("## " !== substr($line, 0, 3)) {
			return false;
		}
		if (isset($title)) {
			return !strncmp(substr($line, 3), $title, strlen($title));
		}
		return true;
	}
}
