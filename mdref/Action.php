<?php

namespace mdref;

use http\Controller\Observer;

class Action extends Observer
{
	function update(\SplSubject $ctl) {
		/* @var \http\Controller $ctl */
		try {
			$payload = $ctl->getPayload();
			$request = $ctl->getRequest();

			$finder = new Finder($this->baseUrl, REFS);
			$url = new \http\Url($request->getRequestUrl());
			if (!$path = $finder->find($url)) {
				$path = new Path;
			}

			$payload->baseUrl = $this->baseUrl;
			$payload->requestUrl = $url;

			$payload->listing = new RefListing($path, 
					$finder->glob($path, "/[_a-zA-Z]*.md"));

			if ($path->isFile()) {
				$payload->markdown = new Markdown($path);
				$payload->sublisting = new RefListing($path, 
						$finder->glob($path, "/[_a-z]*.md"));
			} else if ($path($url)->isFile("")) {
				$payload->markdown = $path->toHtml();
			} else if (strcmp($url, $this->baseUrl)) {
				throw new \http\Controller\Exception(404, "Could not find '$url'");
			}
		} catch (\Exception $e) {
			$payload->baseUrl = $this->baseUrl;
			$ctl->getPayload()->exception = $e;
		}
	}
}
