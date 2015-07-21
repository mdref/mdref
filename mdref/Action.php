<?php

namespace mdref;

use http\Env\Request;
use http\Env\Response;

/**
 * Request handler
 */
class Action {
	/**
	 * The reference
	 * @var \mdref\Reference
	 */
	private $reference;

	/**
	 * @var \http\Request
	 */
	private $request;

	/**
	 * @var \http\Response
	 */
	private $response;

	/**
	 * @var \http\Url
	 */
	private $baseUrl;
	
	/**
	 * Initialize the reference
	 */
	public function __construct(Reference $ref, Request $req, Response $res, BaseUrl $baseUrl) {
		$this->reference = $ref;
		$this->request = $req;
		$this->response = $res;
		$this->baseUrl = $baseUrl;
		ob_start($res);
	}

	function esc($txt) {
		return htmlspecialchars($txt);
	}
	
	/**
	 * Create the view payload
	 * @param \http\Controller $ctl
	 * @return \stdClass
	 */
	private function createPayload() {
		$pld = new \stdClass;
		
		$pld->esc = "htmlspecialchars";
		$pld->quick = [$this->reference, "formatString"];
		$pld->file = [$this->reference, "formatFile"];

		$pld->ref = $this->baseUrl->pathinfo(
			$this->baseUrl->mod($this->request->getRequestUrl()));

		$pld->refs = $this->reference;
		$pld->baseUrl = $this->baseUrl;
			
		return $pld;
	}
	
	/**
	 * Redirect to canononical url
	 * @param string $cnn
	 */
	private function serveCanonical($cnn) {
		$this->response->setHeader("Location", $this->baseUrl->mod(["path" => $cnn]));
		$this->response->setResponseCode(301);
		$this->response->send();
	}
	
	/**
	 * Serve index.css
	 */
	private function serveStylesheet() {
		$this->response->setHeader("Content-Type", "text/css");
		$this->response->setBody(new \http\Message\Body(fopen(ROOT."/public/index.css", "r")));
		$this->response->send();
	}
	
	/**
	 * Serve index.js
	 */
	private function serveJavascript() {
		$this->response->setHeader("Content-Type", "application/javascript");
		$this->response->setBody(new \http\Message\Body(fopen(ROOT."/public/index.js", "r")));
		$this->response->send();
	}
	
	/**
	 * Serve a preset
	 * @param \stdClass $pld
	 * @throws Exception
	 */
	private function servePreset($pld) {
		switch ($pld->ref) {
		case "AUTHORS":
		case "LICENSE":
		case "VERSION":
			$pld->text = file_get_contents(ROOT."/$pld->ref");
			break;
		case "index.css":
			$this->serveStylesheet();
			break;
		case "index.js":
			$this->serveJavascript();
			break;
		default:
			throw new Exception(404, "$pld->ref not found");
		}
	}

	private function serve() {
		extract((array) func_get_arg(0));
		include ROOT."/views/layout.phtml";
		$this->response->send();
	}
	
	public function handle() {
		try {

			$pld = $this->createPayload();

			if (strlen($pld->ref)) {
				$cnn = null;
				if (($repo = $this->reference->getRepoForEntry($pld->ref, $cnn))) {
					if (strlen($cnn)) {
						/* redirect */
						return $this->serveCanonical($cnn);
					} else {
						/* direct match */
						$pld->entry = $repo->getEntry($pld->ref);
					}
				} else {
					return $this->servePreset($pld);
				}
			}
		
		} catch (\Exception $e) {
			$pld->exception = $e;
		}

		$this->serve($pld);
	}
}