<?php

class Multimozo extends Controller {

	function __construct() {
		parent::__construct();	
	}
	
	function index() 
	{	
		//echo Hash::create('sha256', 'jonathan', HASH_PASSWORD_KEY);
		Session::init();
		Session::set('loggedIn', false);
		$this->view->js = array('login/js/login.js');
		$this->view->render('login/multimozo',false);
	}
	
	function run()
	{
		$this->model->run($_POST);
	}
	
}