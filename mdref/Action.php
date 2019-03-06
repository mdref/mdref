<?php

namespace mdref;

use http\Env\Request;
use http\Env\Response;
use http\Message\Body;

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
		$pld->anchor = [$this->reference, "formatAnchor"];
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
	 * Server a PHP stub
	 */
	private function serveStub() {
		$name = $this->request->getQuery("ref", "s");
		$repo = $this->reference->getRepoForEntry($name);
		if (!$repo->hasStub($stub)) {
			throw new Exception(404, "Stub not found");
		}
		$this->response->setHeader("Content-Type", "application/x-php");
		$this->response->setContentDisposition(["attachment" => ["filename" => "$name.stub.php"]]);
		$this->response->setBody(new Body(fopen($stub, "r")));
		$this->response->send();
	}

	/**
	 * Serve a preset
	 * @param \stdClass $pld
	 * @return true to continue serving the payload
	 * @throws Exception
	 */
	private function servePreset($pld) {
		switch ($pld->ref) {
		case "AUTHORS":
		case "LICENSE":
		case "VERSION":
			$pld->text = file_get_contents(ROOT."/$pld->ref");
			return true;
		case "index.css":
			$this->serveStylesheet();
			break;
		case "index.js":
			$this->serveJavascript();
			break;
		case "stub":
			$this->serveStub();
			break;
		default:
			throw new Exception(404, "$pld->ref not found");
		}
		return false;
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
				} elseif (!$this->servePreset($pld)) {
					return;
				}
			}

		} catch (\Exception $e) {
			$pld->exception = $e;
		}

		$this->serve($pld);
	}
}
