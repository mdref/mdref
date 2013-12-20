<?php

namespace mdref;

use http\Env as HTTP;

/**
 * mdref exception handler
 */
class ExceptionHandler
{
	function __construct() {
		set_exception_handler($this);
		set_error_handler($this);
	}
	
	function __invoke($e, $msg = null) {
		if ($e instanceof \Exception) {
			try {
				echo static::html($e);
			} catch (\Exception $ignore) {
				headers_sent() or HTTP::setResponseCode(500);
			}
		} else {
			throw new \Exception($msg, $e);
		}
		return true;
	}
	
	/**
	 * Format an exception as HTML and send appropriate exception info as HTTP headers
	 * @param \Exception $e
	 * @param array $title_tag
	 * @param array $message_tag
	 * @param array $trace_tag
	 * @return string
	 */
	static function html(\Exception $e, array $title_tag = ["h1"], array $message_tag = ["p"], array $trace_tag = ["pre", "style='font-size:smaller'"]) {
		if ($e instanceof \http\Controller\Exception) {
			$code = $e->getCode() ?: 500;
			foreach ($e->getHeaders() as $key => $val) {
				HTTP::setResponseHeader($key, $val);
			}
		} else {
			$code = 500;
		}
		HTTP::setResponseCode($code);
		$name = HTTP::getResponseStatusForCode($code);
		$html = sprintf("<%s>%s</%s>\n<%s>%s</%s>\n",
				implode(" ", $title_tag), $name, $title_tag[0],
				implode(" ", $message_tag), $e->getMessage(), $message_tag[0]);
		if ($trace_tag) {
			$html .= sprintf("<%s>%s</%s>\n",
					implode(" ", $trace_tag), $e->getTraceAsString(), $trace_tag[0]);
		}
		return $html;
	}
}
