<?php

namespace mdref;

use http\Response;

class Exception extends \Exception
{
	function __construct($code, $message) {
		parent::__construct($message, $code);
	}

	function send(Response $res) {
		$res->setResponseCode($this->code);
		$res->setBody(new http\Message\Body);
		$res->getBody()->append($this->message);
	}
}
