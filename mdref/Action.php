<?php

namespace mdref;

use http\Env\Request;
use http\Env\Response;
use http\Message\Body;
use stdClass;
use function file_get_contents;
use function htmlspecialchars;
use function ob_start;
use const ROOT;

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
	 * @var \http\Env\Request
	 */
	private $request;

	/**
	 * @var \http\Env\Response
	 */
	private $response;

	/**
	 * @var resource
	 */
	private $output;

	/**
	 * @var \http\Url
	 */
	private $baseUrl;

	/**
	 * Initialize the reference
	 */
	public function __construct(Reference $ref, Request $req, Response $res, BaseUrl $baseUrl, $output = null) {
		$this->reference = $ref;
		$this->request = $req;
		$this->response = $res;
		$this->baseUrl = $baseUrl;
		$this->output = $output;
		ob_start($res);
	}

	/**
	 * Shorthand for \htmlspecialchars()
	 * @param $txt string
	 * @return string
	 */
	function esc(string $txt) : string {
		return htmlspecialchars($txt);
	}

	/**
	 * Create the view payload
	 * @return \stdClass
	 */
	private function createPayload() : object {
		$pld = new stdClass;

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
	 * Redirect to canonical url
	 * @param string $cnn
	 */
	private function serveCanonical(string $cnn) : void {
		$this->response->setHeader("Location", $this->baseUrl->mod(["path" => $cnn]));
		$this->response->setResponseCode(301);
		if (is_resource($this->output)) {
			$this->response->send($this->output);
		} else {
			$this->response->send();
		}
	}

	/**
	 * Serve index.css
	 */
	private function serveStylesheet() : void {
		$this->response->setHeader("Content-Type", "text/css");
		$this->response->setBody(new Body(\fopen(ROOT."/public/index.css", "r")));
		if (is_resource($this->output)) {
			$this->response->send($this->output);
		} else {
			$this->response->send();
		}
	}

	/**
	 * Serve index.js
	 */
	private function serveJavascript() : void {
		$this->response->setHeader("Content-Type", "application/javascript");
		$this->response->setBody(new Body(\fopen(ROOT."/public/index.js", "r")));
		if (is_resource($this->output)) {
			$this->response->send($this->output);
		} else {
			$this->response->send();
		}
	}

	/**
	 * Server a PHP stub
	 * @throws Exception
	 *
	 */
	private function serveStub() : void {
		$name = $this->request->getQuery("ref", "s");
		$repo = $this->reference->getRepoForEntry($name);
		if (!$repo->hasStub($stub)) {
			throw new Exception(404, "Stub not found");
		}
		$this->response->setHeader("Content-Type", "application/x-php");
		$this->response->setContentDisposition(["attachment" => ["filename" => "$name.stub.php"]]);
		$this->response->setBody(new Body(\fopen($stub, "r")));
		if (is_resource($this->output)) {
			$this->response->send($this->output);
		} else {
			$this->response->send();
		}
	}

	/**
	 * Serve a preset
	 * @param object $pld
	 * @return true to continue serving the payload
	 * @throws Exception
	 */
	private function servePreset(object $pld) : bool {
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

	/**
	 * Serve a payload
	 */
	private function serve() : void {
		extract((array) func_get_arg(0));
		include ROOT."/views/layout.phtml";
		$this->response->addHeader("Link", "<" . $this->baseUrl->path . "index.css>; rel=preload; as=style");
		$this->response->addHeader("Link", "<" . $this->baseUrl->path . "index.js>; rel=preload; as=script");
		if (is_resource($this->output)) {
			$this->response->send($this->output);
		} else {
			$this->response->send();
		}
	}

	/**
	 * Request handler
	 */
	public function handle() : void {
		try {
			$pld = $this->createPayload();

			if (strlen($pld->ref)) {
				$cnn = null;
				if (($repo = $this->reference->getRepoForEntry($pld->ref, $cnn))) {
					if (strlen($cnn)) {
						/* redirect */
						$this->serveCanonical($cnn);
						return;
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
