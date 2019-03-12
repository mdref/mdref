<?php

namespace mdref;

use http\Env\Response;
use http\Message\Body;

class Exception extends \Exception
{
	/**
	 * Exception constructor with reversed arguments
	 *
	 * @param int $code HTTP code
	 * @param string $message reason message
	 */
	function __construct(int $code, ?string $message = null) {
		parent::__construct($message, $code);
	}

	/**
	 * Construct an Exception from error_get_last()'s message and code 500
	 * @return Exception
	 */
	static function fromLastError() : Exception {
		return new static(500, error_get_last()["message"]);
	}

	/**
	 * Send the error response
	 * @param Response $res
	 */
	function send(Response $res) : void {
		$res->setResponseCode($this->code);
		$res->setBody(new Body);
		$res->getBody()->append($this->message);
	}
}
