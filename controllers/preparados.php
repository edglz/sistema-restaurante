<?php

class Login extends Controller {

	function __construct() {
		parent::__construct();	
	}
	
	function index() 
	{	
		$this->view->render('preparados/index' ,false);
	}
	
	function run()
	{
		$this->model->run($_POST);
	}
	
}