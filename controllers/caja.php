<?php Session::init(); $ver = (Session::get('rol') == 1 OR Session::get('rol') == 2 OR Session::get('rol') == 3 OR Session::get('rol') == 7) ? '' :  header('location: ' . URL . 'err/danger'); ?>
<?php

class Caja extends Controller {

	function __construct() {
		parent::__construct();
		Auth::handleLogin();
	}
	
	/* MODULO APERTURA-CIERRE */
	function apercie() 
	{	
		$this->view->Cajero = $this->model->Cajero();
		$this->view->Caja = $this->model->Caja();
		$this->view->Turno = $this->model->Turno();
		$this->view->js = array('caja/js/func_caja.js');
		$this->view->render('caja/apercie', false);
	}

	function apercie_list()
    {
        print_r(json_encode($this->model->apercie_list()));
    }
    function monitor_imp(){
        $this->view->js = array('caja/js/func_imp.js');

        $this->view->render('caja/monitor_imp', false);

    }
    function lista_impresiones(){
        $this->model->lista_impresiones();
    }

    function apercie_crud(){

        if($_POST['id_apc'] != ''){
           $row = $this->model->cerrar_caja($_POST);
           if ($row['cod'] == 1){
                if($row['id_usu'] == Session::get('usuid')) {
                    Session::set('aperturaIn', false);
                }
                print_r(json_encode(1));
           } else {
                print_r(json_encode(0));
           }
        }else{
           $row = $this->model->aperturar_caja($_POST);
           if ($row['cod'] == 0){

                //SI ROL ES 1 ES PORQUE EL USUARIO LOGEADO ES ADMINISTRADOR
                if(Session::get('rol') == 1 OR Session::get('rol') == 2){


                    //REGISTRA APERTURA PARA ACTIVAR LAS VENTAS/CAJA
                    Session::set('aperturaIn', true);
                    //REGISTRA EL NUEVO ID DE APERTURA
                    Session::set('apcid', $row['id_apc']);

                    // INCICIO - NO BORRAR RECONTRA IMPORTANTE 
                    //SI APERTURA ES 1 ES PORQUE EL USUARIO LOGEADO ESTA APERTURADO
                    /*
                    if(Session::get('aperturaIn') == 1){

                        //SI ES DIFERENTE AL ADMINISTRADOR
                        if(Session::get('usuid') <> $_POST['id_usu']) {
                            Session::set('aperturaIn', true);
                        }

                    //SI APERTURA NO ES 1 ES PORQUE EL USUARIO LOGEADO NO ESTA APERTURADO
                    } else if(Session::get('aperturaIn') == 0){

                        //SI ES IGUAL AL ADMINISTRADOR
                        if(Session::get('usuid') == $_POST['id_usu']) {

                            //REGISTRA APERTURA PARA ACTIVAR LAS VENTAS/CAJA
                            Session::set('aperturaIn', true);
                            //REGISTRA EL NUEVO ID DE APERTURA
                            Session::set('apcid', $row['id_apc']);

                        } 

                    }
                    */
                    // FIN - NO BORRAR RECONTRA IMPORTANTE 

                //SI ROL NO ES 1 ES PORQUE EL USUARIO LOGEADO ES CAJERO
                } else if(Session::get('rol') == 3 || Session::get('rol')==7){

                    //REGISTRA APERTURA PARA ACTIVAR LAS VENTAS/CAJA
                    Session::set('aperturaIn', true);
                    //REGISTRA EL NUEVO ID DE APERTURA
                    Session::set('apcid', $row['id_apc']);

                }
                print_r(json_encode(1));
           } else {
                print_r(json_encode(0));
           }
        }
    }

    function apercie_montosist()
    {
        print_r(json_encode($this->model->apercie_montosist($_POST)));
    }

    function stock_pollo()
    {
        print_r(json_encode($this->model->stock_pollo($_POST)));
    }

    /* FIN MODULO APERTURA-CIERRE */

    /* INICIO MODULO INGRESO */
    function ingreso() 
	{	
		$this->view->js = array('caja/js/func_ing.js');
		$this->view->render('caja/ingreso', false);
	}

    function ingreso_list()
    {
        $this->model->ingreso_list($_POST);
    }

    function ingreso_crud(){
        print_r(json_encode($this->model->ingreso_crud_create($_POST)));
    }
    
    function ingreso_estado(){
        print_r(json_encode($this->model->ingreso_estado($_POST)));
    }
    /* FIN MODULO INGRESO */

    /* INICIO MODULO EGRESO */
    function egreso() 
	{	
		$this->view->Personal = $this->model->Personal();
		$this->view->js = array('caja/js/func_egr.js');
		$this->view->render('caja/egreso', false);
	}

    function egreso_list()
    {
        $this->model->egreso_list($_POST);
    }

    function egreso_crud(){
        print_r(json_encode($this->model->egreso_crud_create($_POST)));
    }
    
    function egreso_estado(){
        print_r(json_encode($this->model->egreso_estado($_POST)));
    }
    /* FIN MODULO EGRESO */

    /* INICIO MODULO MONITOR VENTAS */

    function monitor() 
    {   
        $this->view->TipoPago = $this->model->TipoPago();
        $this->view->js = array('venta/js/jquery-ui.min.js','caja/js/func_monitor.js','venta/js/venta_cliente.js');
        $this->view->render('caja/monitor', false);
    }

    function monitor_list()
    {
        $this->model->monitor_list($_POST);
    }

    function monitor_ventas_list()
    {
        $this->model->monitor_ventas_list();
    }

    function monitor_mesas_list()
    {
        $this->model->monitor_mesas_list();
    }
    function portero_apertura(){
        $this->view->Cajero = $this->model->Cajero();
		$this->view->Caja = $this->model->Caja();
		$this->view->Turno = $this->model->Turno();
        $this->view->js = array('caja/js/func_portero_ap.js?'.random_version());
         $this->view->render("caja/portero_ap", false);

    }
     function aperturaPortero(){
         $this->model->aperturaPortero();
     }
    
    function monitor_ventas_porcobrar(){
        print_r(json_encode($this->model->monitor_ventas_porcobrar($_POST)));
    }
    

    /* FIN MODULO MONITOR VENTAS */
}
function random_version (){
    $hash =  base64_encode(openssl_random_pseudo_bytes(30));
    $v = $hash.'='.uniqid();
    return $v;
}