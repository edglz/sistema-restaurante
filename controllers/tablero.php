<?php Session::init(); $ver = (Session::get('rol') == 1 OR Session::get('rol') == 2) ? '' :  header('location: ' . URL . 'err/danger'); ?>
<?php

class Tablero extends Controller {

	function __construct() {
		parent::__construct();
		Auth::handleLogin();
		$this->view->js = array('tablero/js/func_tablero.js');
	}
	
	function index() 
	{	
		$this->view->Caja = $this->model->Caja();
		$this->view->render('tablero/index');
	}
	
	function logout()
	{
		Session::destroy();
		header('location: ' . URL .  'login');
		exit;
	}
	
	function tablero_datos()
    {
        $this->model->tablero_datos($_POST);
    }
	function ventas_portero(){
		$this->model->ventas_portero();
	}
	function count_ventas(){
		$this->model->count_ventas();
	}

}