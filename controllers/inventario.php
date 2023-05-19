<?php

class Inventario extends Controller {

	function __construct() {
		parent::__construct();
	}
	
	/* MODULO AJUSTE DE STOCK */
	function ajuste() 
	{	
		//Auth::handleLogin();
		$this->view->js = array('inventario/js/func_entsal.js');
		$this->view->render('inventario/ajuste', false);
	}

	function nuevoajuste() 
	{
		$this->view->Responsable = $this->model->Responsable();
		$this->view->js = array('inventario/js/jquery-ui.min.js','inventario/js/js-render.js','inventario/js/func_entsal_e.js');
		$this->view->render('inventario/ajuste/index');
	}

	function ajuste_list() 
	{
		$this->model->ajuste_list($_POST);
	}

	function ajuste_crud()
    {
        print_r(json_encode($this->model->ajuste_crud_create($_POST)));
    }

	function ajuste_det() 
	{
		print_r(json_encode($this->model->ajuste_det($_POST)));
	}

	function ajuste_delete() 
	{
		$row = $this->model->ajuste_delete($_POST);
		if ($row['cod'] == 1){
            print_r(json_encode(1));
        } else {
            print_r(json_encode(0));
        }
	}

	function ajuste_insumo_buscar()
    {
        print_r(json_encode($this->model->ajuste_insumo_buscar($_POST)));
    }

    function combomedida()
    {
        $this->model->combomedida($_POST);
    }

	/* MODULO STOCK */
	function stock() 
	{
		$this->view->js = array('inventario/js/func_stock.js');
		$this->view->render('inventario/stock', false);
	}

	function stock_list() 
	{
		$this->model->stock_list($_POST);
	}

	/* MODULO KARDEX */
	function kardex() 
	{
		$this->view->js = array('inventario/js/func_kardexv.js');
		$this->view->render('inventario/kardex', false);
	}

	function kardex_list()
    {
        $this->model->kardex_list($_POST);
    }

    function ComboInsumoProducto()
    {
        $this->model->ComboInsumoProducto($_POST);
    }
}