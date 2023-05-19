<?php

class Login extends Controller {

	function __construct() {
		parent::__construct();	
	}
	
	function index() 
	{	
		//echo Hash::create('sha256', 'jonathan', HASH_PASSWORD_KEY);
		Session::init();
		Session::set('loggedIn', false);
		$this->view->js = array('login/js/login.js?v='. rand(12, 5000) / 100);
		$this->view->render('login/index' ,false);
	}
	// function multimozo()
	// {
	// 	Session::init();
	// 	Session::set('loggedIn', false);
	// 	$this->view->js = array('login/js/login.js');
	// 	$this->view->render('login/multimozo',false);
	// }
	function cliente(){
		Session::init();
		Session::set('loggedIn', false);
		$this->view->js = array('login/js/login.js?v='. rand(12, 5000) / 100);
		$this->view->render('login/cliente' ,false);
	}
	function registrar_cliente(){
		Session::init();
		Session::set('loggedIn', false);
		$this->view->js = array('login/js/registrar_cliente.js?v='. rand(12, 5000) / 100);
		$this->view->render('login/registrar_cliente' ,false);
	}
	function run()
	{
		$this->model->run($_POST);
	}
	function registra_cliente(){
		$this->model->registra_cliente($_POST);
	}
	
}