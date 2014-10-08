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
		if (0 === fseek($this->fd, 1, SEEK_SET)) {
			return fgets($this->fd);
		}
	}
	
	/**
	 * Read the description of the refentry
	 * @return string
	 */
	public function readDescription() {
		if (0 === fseek($this->fd, 0, SEEK_SET)
		&& (false !== fgets($this->fd))
		&& (false !== fgets($this->fd))) {
			return fgets($this->fd);
		}
	}
	
	/**
	 * Read the first subsection of a global refentry
	 * @return string
	 */
	public function readIntro() {
		$intro = "";
		if (0 === fseek($this->fd, 0, SEEK_SET)) {
			$header = false;
			
			while (!feof($this->fd)) {
				if (false === ($line = fgets($this->fd))) {
					break;
				}
				/* search first header and read until next header*/
				if ("## " === substr($line, 0, 3)) {
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
}
