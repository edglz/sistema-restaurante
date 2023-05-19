<?php Session::init(); $ver = (Session::get('rol') == 1 OR Session::get('rol') == 2 OR Session::get('rol') == 3 OR Session::get('rol') == 7) ? '' :  header('location: ' . URL . 'err/danger'); ?>
<?php

class Informe extends Controller {

	function __construct() {
		parent::__construct();
	}
	
	function index() 
	{	
		Auth::handleLogin();
		$this->view->js = array('informe/js/inf.js');
		$this->view->render('informe/index', false);
	}
	/* INICIO MODULO VENTAS */

	function venta_all(){
		$this->view->TipoPedido = $this->model->TipoPedido();
		$this->view->Caja = $this->model->Caja();
		$this->view->Mozo = $this->model->Mozo();
		$this->view->Cliente = $this->model->Cliente();
		$this->view->TipoDocumento = $this->model->TipoDocumento();
		$this->view->js = array('informe/js/inf_ven_all.js');
		$this->view->render('informe/venta/venta', false);
	}
	function pruebas(){
		$this->model->venta_all_list($_POST);
	}
	function venta_all_list()
    {
        $this->model->venta_all_list($_POST);
    }

    function venta_all_det()
    {
        print_r(json_encode($this->model->venta_all_det($_POST)));
    }

    function venta_del(){
		$this->view->Repartidor = $this->model->Repartidor();
		$this->view->js = array('informe/js/inf_ven_delivery.js');
		$this->view->render('informe/venta/delivery', false);
	}

	function venta_delivery_list()
    {
        $this->model->venta_delivery_list($_POST);
    }

    function venta_culqi(){
		$this->view->js = array('informe/js/inf_ven_culqi.js');
		$this->view->render('informe/venta/culqi', false);
	}

	function venta_culqi_list()
    {
        $this->model->venta_culqi_list($_POST);
    }

    function venta_prod(){
    	$this->view->Categoria = $this->model->Categoria();
		$this->view->Producto = $this->model->Producto();
		$this->view->Presentacion = $this->model->Presentacion();
		$this->view->js = array('informe/js/inf_ven_prod.js');
		$this->view->render('informe/venta/producto', false);
	}

	function venta_prod_list()
    {
        $this->model->venta_prod_list($_POST);
    }

    function combPro()
    {
        print_r(json_encode($this->model->combPro($_POST)));
    }

    function combPre()
    {
        print_r(json_encode($this->model->combPre($_POST)));
    }

    function venta_mozo(){
		$this->view->Mozo = $this->model->Mozo();
		$this->view->js = array('informe/js/inf_ven_mozo.js');
		$this->view->render('informe/venta/mozo', false);
	}

	function venta_mozo_list(){
		$this->model->venta_mozo_list($_POST);
	}

	function venta_fpago(){
		$this->view->TipoPago = $this->model->TipoPago();
		$this->view->js = array('informe/js/inf_ven_fpago.js');
		$this->view->render('informe/venta/fpago', false);
	}

	function venta_fpago_list(){
		$this->model->venta_fpago_list($_POST);
	}

	function venta_desc(){
		$this->view->js = array('informe/js/inf_ven_desc.js');
		$this->view->render('informe/venta/descuento', false);
	}

	function venta_desc_list(){
		$this->model->venta_desc_list($_POST);
	}

	function venta_all_imp_($id_venta)
    {
        $dato = $this->model->venta_all_imp($id_venta);
        header('location: http://'.Session::get('pc_ip').'/imprimir/comprobante_venta.php?data='.urlencode(json_encode($dato)));
    }


    function venta_all_imp($id)
    {
        $this->view->empresa = $this->model->Empresa();
        $this->view->dato = $this->model->venta_all_imp_($id);
        $this->view->render('informe/venta/imprimir/imp_venta_all', true);
    }

	/* FIN MODULO VENTAS */

	/* INICIO MODULO COMPRAS */

	function compra_all(){
		$this->view->Proveedor = $this->model->Proveedor();
		$this->view->js = array('informe/js/inf_com_all.js');
		$this->view->render('informe/compra/compra', false);
	}

	function compra_all_list(){
		$this->model->compra_all_list($_POST);
	}

	function compra_all_det()
    {
        print_r(json_encode($this->model->compra_all_det($_POST)));
    }

    function compra_all_det_cuota()
    {
        print_r(json_encode($this->model->compra_all_det_cuota($_POST)));
    }
    
    function compra_all_det_subcuota()
    {
        print_r(json_encode($this->model->compra_all_det_subcuota($_POST)));
    }

	/* FIN MODULO COMPRAS */

	/* INICIO MODULO FINANZAS */

	function finanza_arq(){
		$this->view->Cajero = $this->model->Cajero();
		$this->view->js = array('informe/js/inf_fin_arqueo.js');
		$this->view->render('informe/finanza/arqueo', false);
	}

	function finanza_arq_list(){
		$this->model->finanza_arq_list($_POST);
	}

	function finanza_arq_resumen($id){
        $this->view->apc = $id;
        $this->view->js = array('informe/js/inf_fin_arqueo_resumen.js');
        $this->view->render('informe/finanza/arqueo/detalle', false);
    }

	function finanza_arq_resumen_default()
    {
        print_r(json_encode($this->model->finanza_arq_resumen_default($_POST)));
    }

    function finanza_arq_resumen_venta_list(){
        print_r(json_encode($this->model->finanza_arq_resumen_venta_list($_POST)));
    }

    function finanza_arq_resumen_venta_delivery_list(){
        print_r(json_encode($this->model->finanza_arq_resumen_venta_delivery_list($_POST)));
    }

    function finanza_arq_resumen_caja_list_i(){
        print_r(json_encode($this->model->finanza_arq_resumen_caja_list_i($_POST)));
    }

    function finanza_arq_resumen_caja_list_e(){
        print_r(json_encode($this->model->finanza_arq_resumen_caja_list_e($_POST)));
    }

    function finanza_arq_resumen_productos(){
        print_r(json_encode($this->model->finanza_arq_resumen_productos($_POST)));
    }

    function finanza_arq_resumen_anulaciones(){
        print_r(json_encode($this->model->finanza_arq_resumen_anulaciones($_POST)));
    }

    function finanza_ing(){
    	$this->view->Cajero = $this->model->Cajero();
		$this->view->js = array('informe/js/inf_fin_ingreso.js');
		$this->view->render('informe/finanza/ingreso', false);
	}

	function finanza_ing_list(){
		$this->model->finanza_ing_list($_POST);
	}

	function finanza_egr(){
		$this->view->Cajero = $this->model->Cajero();
		$this->view->js = array('informe/js/inf_fin_egreso.js');
		$this->view->render('informe/finanza/egreso', false);
	}

	function finanza_egr_list(){
		$this->model->finanza_egr_list($_POST);
	}

	function finanza_rem(){
		$this->view->Personal = $this->model->Personal();
		$this->view->js = array('informe/js/inf_fin_remun.js');
		$this->view->render('informe/finanza/remuneracion', false);
	}

	function finanza_rem_list(){
		$this->model->finanza_rem_list($_POST);
	}

	function finanza_arq_imp($id)
    {
        $this->view->empresa = $this->model->Empresa();
        $this->view->dato = $this->model->finanza_arq_imp($id);
        $this->view->render('informe/finanza/imprimir/imp_cierre', true);
    }

    function finanza_adel(){
    	$this->view->Personal = $this->model->Personal();
		$this->view->js = array('informe/js/inf_fin_adelanto.js');
		$this->view->render('informe/finanza/adelanto', false);
	}

	function finanza_adel_list_a(){
		$this->model->finanza_adel_list_a($_POST);
	}

	function finanza_adel_list_b(){
		$this->model->finanza_adel_list_b($_POST);
	}

	/* FIN MODULO FINANZAS */

	/* INICIO MODULO OPERACIONES */

	function oper_anul(){
		$this->view->Cajero = $this->model->Cajero();
		$this->view->js = array('informe/js/inf_ope_anul_pedido.js');
		$this->view->render('informe/operacion/anulacion_pedido', false);
	}

	function oper_anul_list()
    {
        $this->model->oper_anul_list($_POST);
    }
	//INICIO DE MODULO PARA PORTERO
	function portero_all() {
		$this->view->js = array('informe/js/informe_portero_gral.js?v='. rand(12, 5000) / 100);
		$this->view->render('informe/portero/inf_gral', false);
	}

	/* FIN MODULO OPERACIONES */
	function finanza_arq_excel($id)
    {
        $this->view->empresa = $this->model->Empresa();
        $this->view->dato = $this->model->finanza_arq_imp($id);
        $this->view->render('informe/finanza/imprimir/excel_cierre', true);
    }

	//MODULO DE PORTEROS
	function portero_list_ventas(){
		$this->model->portero_list_ventas($_POST);
	}
	function get_portero_pedido(){
		$this->model->get_portero_pedido($_POST);
	}
	function ingreso_portero(){
		$this->view->js = array('informe/portero/js/ing_finanzas.js?v='.rand_version());
		$this->view->render('informe/portero/finanzas/ingresos_portero', false);
	}
	function egreso_portero(){
		$this->view->js = array(('informe/portero/js/eg_finanzas.js?v='.rand_version()));
		$this->view->render('informe/portero/finanzas/egresos_portero', false);
	}
	function aperturas_portero(){
		$this->view->js = array(('informe/portero/js/reporte_apertura.js?v='.rand_version()));
		$this->view->render('informe/portero/aperturas', false);
	}
	function inf_aperturas_portero(){
		$this->model->inf_aperturas_portero($_POST);
	}
	function imprimir_venta_portero($token){
		$this->view->empresa = $this->model->Empresa();
		$this->view->detalle = $this->model->inf_venta_portero($token);
		$this->view->render('informe/portero/imprimir/imp_venta_portero', true);
	}
	function excel_imp_rep($id){
		$this->view->empresa = $this->model->Empresa();
		$this->view->Areas = $this->model->Areas();
		$this->view->Resumen = $this->model->finanza_arq_imp($id);
        $this->view->render('informe/finanza/imprimir/excel_rep', true);
	}
	function imp_excel_portero($id){
		$this->view->Empresa = $this->model->Empresa();
		$this->view->Detalle = $this->model->rep_ventas_portero($id);
		$this->view->Areas = $this->model->Areas();
		$this->view->DetalleApertura = $this->model->rep_apertura_portero($id);
		$this->view->render('informe/portero/excel/venta_portero_excel', true);
	}
	function imp_portero_rep($id){
		$this->view->empresa = $this->model->Empresa();
		$this->view->Detalle = $this->model->rep_ventas_portero($id);
		$this->view->Areas = $this->model->Areas();
		$this->view->DetalleApertura = $this->model->rep_apertura_portero($id);
		$this->view->render('informe/portero/excel/pdf_portero_ventas', true);
	}
	function rep_ingresos(){
		$this->model->rep_ingresos($_POST);
	}
	function rep_egresos(){
		$this->model->rep_egresos($_POST);
	}
	function venta_prod_portero (){
		$this->view->Categoria = $this->model->Categoria();
		$this->view->Producto = $this->model->Producto();
		$this->view->Presentacion = $this->model->Presentacion();
		$this->view->js = array('informe/js/inf_ven_prod_portero.js');
		$this->view->render('informe/portero/all_prod', false);
	}
	function venta_portero_list(){
		$this->model->venta_portero_list($_POST);
	}
	function informe_general(){
		$this->model->informe_general($_POST);
	}
	function informe_pdf($cod){
		$this->view->Empresa = $this->model->Empresa();
		$this->view->Areas = $this->model->Areas();
		$this->view->Detalle = $this->model->informe_pdf($cod);
		$this->view->Cod_imp = $cod;
		$this->view->Catg = $this->model->Categoria();
		$this->view->render('informe/inf_gral', true);

	}
	
}
function rand_version(){
	return rand(12, 5000) / 100;
}