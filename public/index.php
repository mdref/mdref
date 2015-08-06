<?php

use mdref\Action;
use mdref\BaseUrl;
use mdref\Reference;
use mdref\ExceptionHandler;

use http\Env\Request;
use http\Env\Response;

define("ROOT", dirname(__DIR__));
define("REFS", getenv("REFPATH") ?: implode(PATH_SEPARATOR, glob(ROOT."/refs/*")));

ini_set("open_basedir", ROOT.PATH_SEPARATOR.REFS);

spl_autoload_register(function($c) {
	if (!strncmp($c, "mdref\\", 6)) {
		return require ROOT . "/" . strtr($c, "\\", "/") . ".php";
	}
});

new ExceptionHandler;

$reference = new Reference(explode(PATH_SEPARATOR, REFS));
$action = new Action($reference, new Request, new Response, new BaseUrl);
$action->handle();
