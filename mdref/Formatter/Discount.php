<?php

namespace mdref\Formatter;

use mdref\Exception;
use mdref\Formatter;

use MarkdownDocument;

class Discount extends Formatter {

	function formatString(string $string) : string {
		$md = \MarkdownDocument::createFromString($string);
		$md->compile(\MarkdownDocument::AUTOLINK);
		return $md->getHtml();
	}

	function formatFile(string $file) : string {
		$fd = fopen($file, "r");
		if (!$fd) {
			throw Exception::fromLastError();
		}

		$md = \MarkdownDocument::createFromStream($fd);
		$md->compile(\MarkdownDocument::AUTOLINK | \MarkdownDocument::TOC);
		$html = $md->getHtml();

		fclose($fd);

		return $html;
	}
}
