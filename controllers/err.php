<?php

class Err extends Controller {

	function __construct() {
		parent::__construct();
	}
	
	function index() {
		$this->view->render('err/inc/header', true);
		$this->view->render('err/index', true);
	}

	function danger() {
		$this->view->render('err/inc/header', true);
		$this->view->render('err/danger', true);
	}

}