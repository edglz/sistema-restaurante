<?php
Session::init();
class Menu extends Controller {

	function __construct() {
		parent::__construct();	
	}
	
	function index(){
		Session::set('menu', 1);
		$this->view->js = array('venta/js/jquery-ui.min.js','venta/js/js-render.js','venta/js/3.1_venta_orden.js','venta/js/venta_all.js','venta/js/venta_cliente.js');
        $this->view->render('menu/index' ,false);
    }
	
}