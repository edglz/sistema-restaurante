<?php Session::init(); $ver = (Session::get('rol') == 4) ? '' :  header('location: ' . URL . 'err/danger'); ?>
<?php

class Produccion extends Controller {

	function __construct() {
		parent::__construct();	
	}
	
	function index() 
	{	
		$this->view->js = array('produccion/js/func_produccion.js');
		$this->view->render('produccion/index',false);
	}

	function mesas_list(){
        print_r(json_encode($this->model->mesas_list()));
    }

    function mostrador_list(){
        print_r(json_encode($this->model->mostrador_list()));
    }

    function delivery_list(){
        print_r(json_encode($this->model->delivery_list()));
    }

    function agrupacion_platos_list(){
        print_r(json_encode($this->model->agrupacion_platos_list()));
    }

    function agrupacion_platos_detalle(){
        print_r(json_encode($this->model->agrupacion_platos_detalle($_POST)));
    }

    function agrupacion_pedidos_list(){
        print_r(json_encode($this->model->agrupacion_pedidos_list()));
    }

    function agrupacion_pedidos_detalle(){
        print_r(json_encode($this->model->agrupacion_pedidos_detalle($_POST)));
    }

    function preparacion(){
        print_r(json_encode($this->model->preparacion($_POST)));
    }

    function atendido(){
        print_r(json_encode($this->model->atendido($_POST)));
    }
		
}