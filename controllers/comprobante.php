<?php

class Comprobante extends Controller {

	function __construct() {
		parent::__construct();
	}
    function index(){
        header('location: ' . URL . 'err/danger');
    }

    function ticket($id = false)
    {
        if($id == true){
        $this->view->empresa = $this->model->Empresa();
        $this->view->dato = $this->model->venta_all_imp_(base64_decode($id));
        $this->view->render('informe/venta/imprimir/imp_venta_all', true);
        }else{
            header('location: ' . URL . 'err/danger');
        }
    }
	
}