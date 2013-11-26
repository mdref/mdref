<?php
while (ob_get_level() && ob_end_clean());

$loader = require __DIR__ . "/../vendor/autoload.php";
/* @var $loader \Composer\Autoload\ClassLoader */
$loader->add("controllers", __DIR__ . "/../mdref");

use http\Controller;
use http\Controller\Url;

use http\Controller\Observer\Callback;
use http\Controller\Observer\Params;
use http\Controller\Observer\Action;
use http\Controller\Observer\View;
use http\Controller\Observer\Layout;


$url = new Url;

$ctl = new Controller;
$ctl->setDependency("baseUrl", $url);

$ctl->attach(new Params\Action);
$ctl->attach(new Action(["controllerPrefix" => "controllers\\"]));
$ctl->attach(new Callback(function(\http\Controller $ctl) use ($url) {
	$ctl->getPayload()->baseUrl = $url;
}));
$ctl->attach(new View(["directory" => __DIR__ . "/../mdref/views"]));
$ctl->attach(new Layout(["directory" => __DIR__ . "/../mdref/views"]));

$response = $ctl->notify()->getResponse();
$response->send();
