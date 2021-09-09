<?php

namespace mdref\Formatter;

use mdref\Exception;
use mdref\Formatter;

use League\CommonMark\Extension;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\Normalizer;

use function file_get_contents;
use function preg_replace;

class League extends Formatter {
	private $md;

	function __construct() {
		$this->md = new GithubFlavoredMarkdownConverter([
			"slug_normalizer" => [
				"instance" => new class implements Normalizer\TextNormalizerInterface {
					function normalize(string $text, $context = null) : string {
						return preg_replace("/[^[:alnum:]:._-]/", ".", $text);
					}
				}
			],
			"heading_permalink" => [
				"html_class" => "permalink",
				"id_prefix" => "",
				"fragment_prefix" => "",
				"title" => "",
				"symbol" => "#",
				"insert" => "after",
				"min_heading_level" => 2,
			]
		]);
		$this->md->getEnvironment()->addExtension(
			new Extension\DescriptionList\DescriptionListExtension
		);
		$this->md->getEnvironment()->addExtension(
			new Extension\HeadingPermalink\HeadingPermalinkExtension
		);
		$this->md->getEnvironment()->addExtension(
			new Extension\Attributes\AttributesExtension
		);
	}

	function formatString(string $string) : string {
		return $this->md->convertToHtml($string);
	}

	function formatFile(string $file) : string {
		$string = file_get_contents($file);
		if ($string === false) {
			throw Exception::fromLastError();
		}
		return $this->md->convertToHtml($string);
	}
}
