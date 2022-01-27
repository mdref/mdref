<?php

namespace mdref;

use function feof;
use function fgets;
use function fopen;
use function fseek;
use function strncmp;
use function substr;
use const SEEK_SET;

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
	 *
	 * @param string $path
	 * @throws Exception
	 */
	public function __construct(string $path) {
		if (!$this->fd = fopen($path, "rb")) {
			throw Exception::fromLastError();
		}
	}

	/**
	 * Read the title of the refentry
	 *
	 * @return string
	 * @throws Exception
	 */
	public function readTitle() : string {
		if ($this->rewind(1)) {
			return fgets($this->fd);
		}
		throw Exception::fromLastError();
	}

	/**
	 * Read the description (first line) of the refentry
	 *
	 * @return string
	 * @throws Exception
	 */
	public function readDescription() : string {
		if (!$this->rewind()) {
			throw Exception::fromLastError();
		}
		if (false !== fgets($this->fd)
		&& (false !== fgets($this->fd))) {
			return fgets($this->fd);
		}
		return "";
	}

	/**
	 * Read the full description (first section) of the refentry
	 *
	 * @return string
	 * @throws Exception
	 */
	public function readFullDescription() : string {
		$desc = $this->readDescription();
		while (false !== ($line = fgets($this->fd))) {
			if ($line[0] === "#") {
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
	public function readIntro() : string {
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

	/**
	 * Read section of $title
	 *
	 * @param $title
	 * @return string
	 */
	public function readSection(string $title) : string {
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

	/**
	 * @param int $offset
	 * @return bool
	 */
	private function rewind(int $offset = 0) : bool {
		return 0 === fseek($this->fd, $offset, SEEK_SET);
	}

	/**
	 * @param string $line
	 * @param string $title
	 * @return bool
	 */
	private function isHeading(string $line, ?string $title = null) : bool {
		if ("## " !== substr($line, 0, 3)) {
			return false;
		}
		if (isset($title)) {
			return !strncmp(substr($line, 3), $title, strlen($title));
		}
		return true;
	}
}
