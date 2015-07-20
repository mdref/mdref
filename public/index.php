<?php

use mdref\Action;
use mdref\BaseUrl;
use mdref\Reference;
use mdref\ExceptionHandler;

use http\Env\Request;
use http\Env\Response;

define("ROOT", dirname(__DIR__));

#ini_set("open_basedir", ROOT.":".REFS);

$loader = require ROOT . "/vendor/autoload.php";
/* @var $loader \Composer\Autoload\ClassLoader */
$loader->add("mdref", ROOT);

new ExceptionHandler;

$reference = new Reference(($refs = getenv("REFPATH")) ? explode(PATH_SEPARATOR, $refs) : glob(ROOT."/refs/*"));
$action = new Action($reference, new Request, new Response, new BaseUrl);

ob_start($response);
$action->handle();
ob_end_flush();
$response->send();
