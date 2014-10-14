<?php

namespace mdref;

use http\Controller\Observer;

/**
 * Request handler
 */
class Action extends Observer {
	/**
	 * Reference paths
	 * @var string
	 */
	protected $refpath;
	
	/**
	 * The reference
	 * @var \mdref\Reference
	 */
	private $reference;
	
	/**
	 * Initialize the reference
	 */
	protected function init() {
		$this->reference = new Reference(explode(PATH_SEPARATOR, $this->refpath));
	}
	
	/**
	 * Create the view payload
	 * @param \http\Controller $ctl
	 * @return \stdClass
	 */
	private function createPayload(\http\Controller $ctl) {
		$pld = new \stdClass;
		
		try {
			$pld->quick = function($string) {
				$md = \MarkdownDocument::createFromString($string);
				$md->compile(\MarkdownDocument::AUTOLINK);
				return $md->getHtml();
			};
			
			$pld->file = function($file) {
				$fd = fopen($file, "r");
				$md = \MarkdownDocument::createFromStream($fd);
				$md->compile(\MarkdownDocument::AUTOLINK | \MarkdownDocument::TOC);
				$html = $md->getHtml();
				fclose($fd);
				return $html;
			};
			
			$pld->ref = implode("/",  $this->baseUrl->params(
				$this->baseUrl->mod($ctl->getRequest()->getRequestUrl())));
			
			$pld->refs = $this->reference;
			$pld->baseUrl = $this->baseUrl;
			
		} catch (\Exception $e) {
			$pld->exception = $e;
		}
		
		return $pld;
	}
	
	/**
	 * Redirect to canononical url
	 * @param \http\Controller $ctl
	 * @param string $cnn
	 */
	private function serveCanonical($ctl, $cnn) {
		$ctl->detachAll(Observer\View::class);
		$ctl->getResponse()->setHeader("Location", $this->baseUrl->mod($cnn));
		$ctl->getResponse()->setResponseCode(301);
	}
	
	/**
	 * Serve index.css
	 * @param \http\Controller $ctl
	 */
	private function serveStylesheet($ctl) {
		$ctl->detachAll(Observer\View::class);
		$ctl->getResponse()->setHeader("Content-Type", "text/css");
		$ctl->getResponse()->setBody(new \http\Message\Body(fopen(ROOT."/public/index.css", "r")));
	}
	
	/**
	 * Serve index.js
	 * @param \http\Controller $ctl
	 */
	private function serveJavascript($ctl) {
		$ctl->detachAll(Observer\View::class);
		$ctl->getResponse()->setHeader("Content-Type", "application/javascript");
		$ctl->getResponse()->setBody(new \http\Message\Body(fopen(ROOT."/public/index.js", "r")));
	}
	
	/**
	 * Serve a preset
	 * @param \http\Controller $ctl
	 * @param \stdClass $pld
	 * @throws \http\Controller\Exception
	 */
	private function servePreset($ctl, $pld) {
		switch ($pld->ref) {
		case "AUTHORS":
		case "LICENSE":
		case "VERSION":
			$pld->text = file_get_contents(ROOT."/$pld->ref");
			break;
		case "index.css":
			$this->serveStylesheet($ctl);
			break;
		case "index.js":
			$this->serveJavascript($ctl);
			break;
		default:
			throw new \http\Controller\Exception(404, "$pld->ref not found");
		}
	}
	
	/**
	 * Implements Observer
	 * @param \SplSubject $ctl \http\Controller
	 */
	public function update(\SplSubject $ctl) {
		/* @var http\Controller $ctl */
		$pld = $this->createPayload($ctl);
		$ctl[Observer\View::class] = function() use($pld) {
			return $pld;
		};
		
		if (!isset($pld->ref) || !strlen($pld->ref)) {
			/* front page */
			return;
		}
		
		$cnn = null;
		if (($repo = $this->reference->getRepoForEntry($pld->ref, $cnn))) {
			if (strlen($cnn)) {
				/* redirect */
				$this->serveCanonical($ctl, $cnn);
			} else {
				/* direct match */
				$pld->entry = $repo->getEntry($pld->ref);
			}
		} else {
			$this->servePreset($ctl, $pld);
		}
	}

}