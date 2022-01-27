<?php

namespace mdref;

use DOMDocument;
use DOMElement;
use DOMNode;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Normalizer;
use League\CommonMark\Extension;
use mdref\Formatter\Wrapper;

class Formatter {
	public function __construct(
		protected ?MarkdownConverter $md = null,
		protected ?Wrapper $wrapper = null,
	) {
		if (!$this->md) {
			$this->md = new GithubFlavoredMarkdownConverter([
				"slug_normalizer" => [
					"instance" => new class($this) implements Normalizer\TextNormalizerInterface {
						protected $formatter;
						function __construct(Formatter $fmt) {
							$this->formatter = $fmt;
						}
						function normalize(string $text, $context = null) : string {
							return $this->formatter->formatSlug($text);
						}
					}
				],
			]);
			$this->md->getEnvironment()->addExtension(
				new Extension\DescriptionList\DescriptionListExtension
			);
			$this->md->getEnvironment()->addExtension(
				new Extension\Attributes\AttributesExtension
			);
		}
		if (!$this->wrapper) {
			$this->wrapper = new Wrapper($this);
		}
	}

	public function formatString(string $string) : string {
		return $this->md->convertToHtml($string);
	}

	public function formatFile(string $file) : string {
		$string = file_get_contents($file);
		if ($string === false) {
			throw Exception::fromLastError();
		}
		return $this->md->convertToHtml($string);
	}

	/**
	 * Format a simplified url slug
	 * @param string $string input text, like a heading
	 * @return string the simplified slug
	 */
	public function formatSlug(string $string) : string {
		return preg_replace("/[^\$[:alnum:]:._-]+/", ".", $string);
	}

	/**
	 * @param string $page HTML content
	 * @param object $pld Action payload
	 * @return string marked up HTML content
	 */
	public function markup(string $page, $pld) : string {
		$dom = new DOMDocument("1.0", "utf-8");
		$dom->formatOutput = true;
		$dom->loadHTML("<!doctype html>\n <meta charset=utf-8>\n" . $page, LIBXML_HTML_NOIMPLIED);
		foreach ($dom->childNodes as $node) {
			$this->walk($node, $pld);
		}
		$html = "";
		foreach ($dom->childNodes as $child) {
			$html .= $dom->saveHTML($child);
		}
		return $html;
	}

	public function createPermaLink(DOMElement $node, string $slug, $pld) {
		if (strlen($slug)) {
			$node->setAttribute("id", $slug);
		}
		$perm = $node->ownerDocument->createElement("a");
		$perm->setAttribute("class", "permalink");
		$perm->setAttribute("href", "$pld->ref#$slug");
		$perm->textContent = "#";
		return $perm;
	}

	protected function walk(DOMNode $node, $pld) {
		switch ($node->nodeType) {
			case XML_ELEMENT_NODE:
				$this->walkElement($node, $pld);
				break;
			case XML_TEXT_NODE:
				$this->wrapper->wrap($node, $pld);
				break;
			default:
				break;
		}
	}

	protected function highlightCode(DOMElement $node) {
		foreach (["default", "comment", "html", "keyword", "string"] as $type) {
			ini_set("highlight.$type", "inherit\" class=\"$type");
		}
		$code = highlight_string($node->textContent, true);
		$temp = new DOMDocument("1.0", "utf-8");
		$temp->loadHTML($code, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		return $node->ownerDocument->importNode($temp->firstChild, true);
	}

	protected function walkElement(DOMElement $node, $pld) {
		switch ($node->tagName) {
			case "h1":
				$perm = $this->createPermaLink($node, "", $pld);
				$node->insertBefore($perm, $node->firstChild);
				$pld->currentSection = null;
				break;
			case "h2":
				$pld->currentSection = $this->formatSlug($node->textContent);
			case "h3":
			case "h4":
			case "h5":
			case "h6":
				$slug = $this->formatSlug($node->textContent);
				$perm = $this->createPermaLink($node, $slug, $pld);
				$node->appendChild($perm);
				break;
			case "span":
				if (!empty($pld->currentSection) && $node->hasAttribute("class")) {
					switch ($pld->currentSection) {
						case "Properties:":
						case "Constants:":
							switch ($node->getAttribute("class")) {
								case "constant":
								case "var":
									$slug = $this->formatSlug($node->textContent);
									$perm = $this->createPermaLink($node, $slug, $pld);
									$node->insertBefore($perm);
									break;
							}
							break;
					}
				}
			case "a":
			case "br":
			case "hr":
			case "em":
				return; // !
			case "code":
				if ($node->parentNode && $node->parentNode->nodeName === "pre") {
					$code = $this->highlightCode($node);
					$this->walk($code, $pld);
					$node->parentNode->replaceChild($code, $node);
				}
				return; // !
		}

		// suck it out, because we're modifying the DOM
		foreach (iterator_to_array($node->childNodes) as $child) {
			$this->walk($child, $pld);
		}
	}
}
