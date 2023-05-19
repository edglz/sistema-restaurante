<?php Session::init(); $ver = (Session::get('rol') == 1 OR Session::get('rol') == 2 OR Session::get('rol') == 3) ? '' :  header('location: ' . URL . 'err/danger'); ?>
<?php

class Compra extends Controller {

	function __construct() {
		parent::__construct();
	}
	
	/* INICIO MODULO COMPRA */
	function index() 
	{	
		$this->view->Proveedor = $this->model->Proveedor();
		$this->view->js = array('compra/js/func_compra.js');
		$this->view->render('compra/compra', false);
	}

	function compra_list() 
	{
		$this->model->compra_list($_POST);
	}

	function compra_det()
    {
        print_r(json_encode($this->model->compra_det($_POST)));
    }

    function compra_crud()
    {
        print_r(json_encode($this->model->compra_crud_create($_POST)));
    }

    function compra_delete(){
        $row = $this->model->compra_delete($_POST);
        if ($row['cod'] == 1){
            print_r(json_encode(1));
        } else {
            print_r(json_encode(0));
        }
    }  

    function compra_proveedor_buscar()
    {
        print_r(json_encode($this->model->compra_proveedor_buscar($_REQUEST['cadena'])));
    }

    function compra_proveedor_nuevo()
    {
        print_r(json_encode($this->model->compra_proveedor_nuevo($_POST)));
    }

    function compra_insumo_buscar()
    {
        print_r(json_encode($this->model->compra_insumo_buscar($_REQUEST['cadena'])));
    }

	function nuevacompra() 
	{
		$this->view->js = array('compra/js/jquery-ui.min.js','compra/js/js-render.js','compra/js/func_compra_nueva.js','compra/js/func_compra_prov.js');
		$this->view->render('compra/compra/index');
	}

	/* FIN MODULO COMPRA */

	/* INICIO MODULO PROVEEDOR */
	function proveedor() 
	{	
		$this->view->js = array('compra/js/func_prov.js');
		$this->view->render('compra/proveedor', false);
	}

	function proveedor_list()
    {
        $this->model->proveedor_list($_POST);
    }

    function proveedor_datos(){
        $this->model->proveedor_datos($_POST);
    }

    function proveedor_crud(){
        if($_POST['id_prov'] != ''){
           $this->model->proveedor_crud_update($_POST);
           print_r(json_encode(2));
        } else {
           $row = $this->model->proveedor_crud_create($_POST);
           if ($row['cod'] == 1){
                print_r(json_encode(0));
            } else {
                print_r(json_encode(1));
            }
        }
    }

    function proveedor_estado(){
        print_r(json_encode($this->model->proveedor_estado($_POST)));
    }

	/* FIN MODULO PROVEEDOR */
}