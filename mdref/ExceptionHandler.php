<?php

namespace mdref;

use http\Env;
use function debug_print_backtrace;
use function error_get_last;
use function headers_sent;
use function implode;
use function ob_end_clean;
use function ob_get_clean;
use function ob_get_level;
use function ob_start;
use function register_shutdown_function;
use function set_error_handler;
use function set_exception_handler;
use function sprintf;
use const E_COMPILE_ERROR;
use const E_CORE_ERROR;
use const E_ERROR;
use const E_PARSE;
use const E_USER_ERROR;

/**
 * Exception and error handler
 */
class ExceptionHandler
{
	/**
	 * @var \http\Env\Response
	 */
	private $response;

	/**
	 * Set up error/exception/shutdown handler
	 * @param Env\Response $r
	 */
	public function __construct(Env\Response $r) {
		$this->response = $r;
		set_exception_handler($this);
		set_error_handler($this);
		register_shutdown_function($this);
	}

	/**
	 * Clean output buffers
	 */
	private static function cleanBuffers() : void {
		while (ob_get_level()) {
			if (!@ob_end_clean()) {
				break;
			}
		}
	}

	/**
	 * The exception/error/shutdown handler callback
	 *
	 * @param \Throwable|string $e
	 * @param ?string $msg
	 * @throws \Exception
	 */
	public function __invoke($e = null, ?string $msg = null) : void {
		if ($e instanceof \Throwable) {
			try {
				self::cleanBuffers();
				echo static::htmlException($e);
			} catch (\Exception $ignore) {
				headers_sent() or Env::setResponseCode(500);
				die("FATAL ERROR:\n$e\n$ignore");
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
				self::cleanBuffers();
				$message = sprintf("%s in %s at line %d",
					$error["message"], $error["file"], $error["line"]);
				echo static::htmlError("Application Error", $message, 500, "");
				break;
			}
		}
	}

	/**
	 * Format an exception as HTML and send appropriate exception info as HTTP headers
	 * @param \Throwable $e
	 * @param array $title_tag
	 * @param array $message_tag
	 * @param array $trace_tag
	 * @return string
	 */
	public static function htmlException(\Throwable $e, array $title_tag = ["h1"], array $message_tag = ["p"],
			array $trace_tag = ["pre", "style='font-size:smaller;overflow-x:scroll'"]) : string {
		if ($e instanceof Exception) {
			$code = $e->getCode() ?: 500;
		} else {
			$code = 500;
		}
		
		for ($html = ""; $e; $e = $e->getPrevious()) {
			$html .= static::htmlError(Env::getResponseStatusForCode($code),
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
	public static function htmlError($title, $message, $code, $trace = null, array $title_tag = ["h1"],
			array $message_tag = ["p"], array $trace_tag = ["pre", "style='font-size:smaller;overflow-x:scroll'"]) : string {
		Env::setResponseCode($code);
		
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
