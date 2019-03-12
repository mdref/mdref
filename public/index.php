<?php

namespace mdref;

use http\Env\Request;
use http\Env\Response;
use function ini_get;
use function ini_set;
use function spl_autoload_register;
use function strncmp;
use function strtr;
use const GLOB_ONLYDIR;
use const PATH_SEPARATOR;
use const REFS;
use const ROOT;

define("ROOT", dirname(__DIR__));
define("REFS", getenv("REFPATH") ?: implode(PATH_SEPARATOR, glob(ROOT."/refs/*", GLOB_ONLYDIR)));

ini_set("open_basedir", ROOT.PATH_SEPARATOR.REFS);

if (!ini_get("date.timezone")) {
	date_default_timezone_set("UTC");
}

spl_autoload_register(function($c) {
	if (!strncmp($c, "mdref\\", 6)) {
		return require ROOT . "/" . strtr($c, "\\", "/") . ".php";
	}
});

$response = new Response;
$ehandler = new ExceptionHandler($response);
$reference = new Reference(explode(PATH_SEPARATOR, REFS));
$action = new Action($reference, new Request, $response, new BaseUrl);
$action->handle();
