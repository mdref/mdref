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
	function __construct(Path $path) {
		$this->path = $path;
	}
	
	/**
	 * @return string
	 */
	function __toString() {
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
}
