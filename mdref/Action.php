<?php

namespace mdref;

use http\Controller\Observer;

/**
 * The sole action controller of mdref
 */
class Action extends Observer
{
	private function serveReference(\http\Url $url, \http\Controller\Payload $payload) {
		$finder = new Finder($this->baseUrl, REFS);
		$path = $finder->find($url);
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
	
	private function serveInternal(\http\Url $url, \http\Controller\Payload $payload) {
		$finder = new Finder($this->baseUrl, ROOT);
		$path = $finder->find($url, "");
		if ($path->isFile("")) {
			$payload->html = $path->toHtml();
			return true;
		}
	}
	
	private function getType($file) {
		static $inf = null;
		static $typ = array(".css" => "text/css", ".js" => "applicatin/javascript");
		
		$ext = strrchr($file, ".");
		if (isset($typ[$ext])) {
			return $typ[$ext];
		}
		
		if (!$inf) {
			$inf = new \FINFO(FILEINFO_MIME_TYPE);
		}
		return $inf->file($file);
	}
	
	private function servePublic(\http\Url $url, \http\Env\Response $res) {
		$finder = new Finder($this->baseUrl, ROOT."/public");
		$path = $finder->find($url, "");
		if ($path->isFile("")) {
			$res->setHeader("Content-Type", $this->getType($path->getFullPath("")));
			$res->setBody(new \http\Message\Body(fopen($path->getFullPath(""),"r")));
			return true;
		}
	}

	/**
	 * Implements \SplObserver
	 * @param \SplSubject $ctl
	 */
	function update(\SplSubject $ctl) {
		/* @var \http\Controller $ctl */
		try {
			$pld = $ctl->getPayload();
			$pld->baseUrl = $this->baseUrl;
			$url = $this->baseUrl->mod($ctl->getRequest()->getRequestUrl());
			$pld->permUrl = implode("/", $this->baseUrl->params($url));
			
			if ($this->serveReference($url, $pld) || $this->serveInternal($url, $pld)) {
				return;
			} elseif ($this->servePublic($url, $ctl->getResponse())) {
				$ctl->detachAll("\\http\\Controller\\Observer\\View");
				return;
			}
			
			/* fallthrough */
			if (strcmp($url->path, $this->baseUrl->path)) {
				throw new \http\Controller\Exception(404, "Could not find '$url'");
			}
		} catch (\Exception $e) {
			$ctl->getPayload()->exception = $e;
		}
	}
}
