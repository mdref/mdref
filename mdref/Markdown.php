<?php

namespace mdref;

class Markdown
{
	/**
	 * @var \mdref\Path
	 */
	protected $path;
	
	/**
	 * @param \mdref\Path $path
	 */
	function __construct(Path $path = null) {
		$this->path = $path;
	}
	
	/**
	 * @return string
	 */
	function __toString() {
		if (!$this->path) {
			return "";
		}
		try {
			$r = fopen($this->path->getFullPath(".md"), "r");
			$md = \MarkdownDocument::createFromStream($r);
			$md->compile(\MarkdownDocument::AUTOLINK | \MarkdownDocument::TOC);
			$html = $md->getHtml();
			fclose($r);
		} catch (\Exception $e) {
			$html = ExceptionHandler::html($e);
		}
		return $html;
	}

	function quick($string) {
		$md = \MarkdownDocument::createFromString($string);
		$md->compile(\MarkdownDocument::AUTOLINK);
		return $md->getHtml();
	}
}
