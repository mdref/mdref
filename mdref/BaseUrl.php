<?php

namespace mdref;

use http\Url;
use function dirname;
use function filter_input;
use function strlen;
use function substr;
use function urldecode;
use const FILTER_VALIDATE_BOOLEAN;
use const INPUT_SERVER;

class BaseUrl extends Url
{
	/**
	 * Create base URL
	 *
	 * @param mixed $url
	 */
	function __construct($url = null) {
		$self = [
			"scheme" => filter_input(INPUT_SERVER, "HTTPS", FILTER_VALIDATE_BOOLEAN)
				? "https"
				: "http",
			"path"   => dirname(filter_input(INPUT_SERVER, "SCRIPT_NAME")) . "/",
		];
		parent::__construct($self, $url,
			self::JOIN_PATH |
			self::SANITIZE_PATH |
			self::STRIP_QUERY |
			self::STRIP_AUTH |
			self::FROM_ENV
		);
	}

	/**
	 * Extract path info
	 *
	 * @param mixed $url full request url
	 * @return string
	 */
	function pathinfo($url) : string {
		$url = new Url($this, $url, Url::FROM_ENV | Url::STRIP_QUERY);
		$info = substr($url, strlen($this));
		return urldecode($info);
	}


}
