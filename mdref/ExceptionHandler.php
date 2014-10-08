<?php

namespace mdref;

use http\Env as HTTP;

/**
 * Exception and error handler
 */
class ExceptionHandler
{
	/**
	 * Set up error/exception/shutdown handler
	 */
	public function __construct() {
		set_exception_handler($this);
		set_error_handler($this);
		register_shutdown_function($this);
	}
	
	/**
	 * The exception/error/shutdown handler callback
	 */
	public function __invoke($e = null, $msg = null) {
		if ($e instanceof \Exception) {
			try {
				echo static::htmlException($e);
			} catch (\Exception $ignore) {
				headers_sent() or HTTP::setResponseCode(500);
				die("FATAL ERROR");
			}
		} elseif (isset($e, $msg)) {
			throw new \Exception($msg, $e);
		} elseif (($error = error_get_last())) {
			switch ($error["type"]) {
			case E_PARSE:
			case E_ERROR:
			case E_USER_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
				while (ob_get_level()) {
					if (!@ob_end_clean()) {
						break;
					}
				}
				$message = sprintf("%s in %s at line %d", 
					$error["message"], $error["file"], $error["line"]);
				echo static::htmlError("Application Error", $message, 500, "");
				break;
			}
		}
	}
	
	/**
	 * Format an exception as HTML and send appropriate exception info as HTTP headers
	 * @param \Exception $e
	 * @param array $title_tag
	 * @param array $message_tag
	 * @param array $trace_tag
	 * @return string
	 */
	public static function htmlException(\Exception $e, array $title_tag = ["h1"], array $message_tag = ["p"], array $trace_tag = ["pre", "style='font-size:smaller;overflow-x:scroll'"]) {
		if ($e instanceof \http\Controller\Exception) {
			$code = $e->getCode() ?: 500;
			foreach ($e->getHeaders() as $key => $val) {
				HTTP::setResponseHeader($key, $val);
			}
		} else {
			$code = 500;
		}
		
		for ($html = ""; $e; $e = $e->getPrevious()) {
			$html .= static::htmlError(HTTP::getResponseStatusForCode($code),
				$e->getMessage(), $code, $e->getTraceAsString(), 
				$title_tag, $message_tag, $trace_tag);
		}
		return $html;
	}
	
	/**
	 * Format an error as HTML
	 * @param string $title
	 * @param string $message
	 * @param int $code
	 * @param string $trace
	 * @param array $title_tag
	 * @param array $message_tag
	 * @param array $trace_tag
	 * @return string
	 */
	public static function htmlError($title, $message, $code, $trace = null, array $title_tag = ["h1"], array $message_tag = ["p"], array $trace_tag = ["pre", "style='font-size:smaller;overflow-x:scroll'"]) {
		HTTP::setResponseCode($code);
		
		$html = sprintf("<%s>%s</%s>\n<%s>%s</%s>\n",
				implode(" ", $title_tag), $title, $title_tag[0],
				implode(" ", $message_tag), $message, $message_tag[0]);
		if ($trace_tag) {
			if (!isset($trace)) {
				ob_start();
				debug_print_backtrace();
				$trace = ob_get_clean();
			}
			if (!empty($trace)) {
				$html .= sprintf("<%s>%s</%s>\n",
						implode(" ", $trace_tag), $trace, $trace_tag[0]);
			}
		}
		
		return $html;
	}
}
