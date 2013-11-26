<?php

namespace controllers;

use http\Controller\Action;

abstract class AppController extends Action
{
	protected function init() {
		parent::init();
		
		$this->payload->title = "mdref";
		$this->payload->listing = 123;
	}
}