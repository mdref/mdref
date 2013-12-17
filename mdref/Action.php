<?php

namespace mdref;

use http\Controller\Observer;

/**
 * The sole action controller of mdref
 */
class Action extends Observer
{
	private function serveReference(\http\Controller $ctl) {
		$payload = $ctl->getPayload();
		$finder = new Finder($this->baseUrl, REFS);
		$path = $finder->find(new \http\Url($ctl->getRequest()->getRequestUrl()));
		$payload->listing = new RefListing($path, 
				$finder->glob($path, "/[_a-zA-Z]*.md"));
		$payload->title = $payload->listing->getSelf()->formatLink();
		$payload->refs = $finder;
		if ($path->isFile()) {
			$payload->html = new Markdown($path);
			$payload->sublisting = new RefListing($path, 
					$finder->glob($path, "/[_a-z]*.md"));
			return true;
		}
	}
	
	private function serveInternal(\http\Controller $ctl) {
		$payload = $ctl->getPayload();
		$finder = new Finder($this->baseUrl, ROOT);
		$url = new \http\Url($ctl->getRequest()->getRequestUrl());
		$path = $finder->find($url, "");
		if ($path->isFile("")) {
			$payload->html = $path->toHtml();
		} else if (strcmp($url, $this->baseUrl)) {
			throw new \http\Controller\Exception(404, "Could not find '$path'");
		}
	}

	/**
	 * Implements \SplObserver
	 * @param \SplSubject $ctl
	 */
	function update(\SplSubject $ctl) {
		/* @var \http\Controller $ctl */
		try {
			$ctl->getPayload()->baseUrl = $this->baseUrl;

			if (!$this->serveReference($ctl)) {
				$this->serveInternal($ctl);
			}
		} catch (\Exception $e) {
			$ctl->getPayload()->exception = $e;
		}
	}
}
