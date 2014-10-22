<?php

define("ROOT", dirname(__DIR__));
define("REFS", getenv("REFPATH") ?: implode(PATH_SEPARATOR, glob(ROOT."/refs/*")));

#ini_set("open_basedir", ROOT.":".REFS);

$loader = require ROOT . "/vendor/autoload.php";
/* @var $loader \Composer\Autoload\ClassLoader */
$loader->add("mdref", ROOT);

use http\Controller;
use http\Controller\Url;
use http\Controller\Observer\Layout;

use mdref\ExceptionHandler;
use mdref\Action;

new ExceptionHandler;

$ctl = new Controller;
$ctl->setDependency("baseUrl", new Url)
	->attach(new Action(["refpath" => REFS]))
	->attach(new Layout)
	->notify()
	->getResponse()
	->send();
