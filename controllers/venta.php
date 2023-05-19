<?php Session::init(); $ver = (Session::get('rol') == 1  OR Session::get('rol') == 2 OR Session::get('rol') == 3 OR Session::get('rol') == 5 OR Session::get('rol') == 7 OR Session::get('rol') == -1) OR Session::get('menu') == 1 ? '' :  header('location: ' . URL . 'err/danger'); ?>
<?php
require_once 'public/lib/print/num_letras.php';

class Venta extends Controller {
	function __construct() {
		parent::__construct();
	}
	function index() 
	{	
		$this->view->Salon = $this->model->Salon();
        $this->view->Mozo = $this->model->Mozo();
        $this->view->Usuarios_rol = $this->model->Usuarios_rol($_SESSION['usuid']);
        $this->view->Repartidor = $this->model->Repartidor();
		$this->view->TipoPago = $this->model->TipoPago();
        $this->view->js = array('venta/js/jquery-ui.min.js','venta/js/venta_index.js','venta/js/venta_all.js','venta/js/venta_cliente.js');
		$this->view->render('venta/index',false);
	}
    function venta_portero(){
        $this->view->mesa = $this->model->getInfoMesa();
        $this->view->js = array('venta/js/venta_portero.js?v='.uniqid());
        $this->view->render('venta/orden_portero', false);
    }
    function pedidos_portero(){
        $this->model->pedidos_portero();
    }
	function mesa_list(){
        print_r(json_encode($this->model->mesa_list($_POST)));
    }
    function mostrador_list(){
        $this->model->mostrador_list($_POST);
    }
    function mostrador_list_c(){
        $this->model->mostrador_list_c($_POST);
    }
    function delivery_list(){
        $this->model->delivery_list($_POST);
    }
    function delivery_list_c(){
        $this->model->delivery_list_c($_POST);
    }
    function pedidoAccion(){
        print_r(json_encode($this->model->pedidoAccion($_POST)));
    }
    function listarPedidos(){
        print_r(json_encode($this->model->listarPedidos($_POST)));
    }
    function listarPedidosDetalle(){
        print_r(json_encode($this->model->listarPedidosDetalle($_POST)));
    }
    function listarUpdatePedidos(){
        print_r(json_encode($this->model->listarUpdatePedidos($_POST)));
    }
    function listarPedidosTicket(){
        print_r(json_encode($this->model->listarPedidosTicket($_POST)));
    }
	function ComboMesaOri()
    {
        $this->model->ComboMesaOri($_POST);
    }
    function ComboMesaDes()
    {
        $this->model->ComboMesaDes($_POST);
    }
    function subPedido(){
        print_r(json_encode($this->model->subPedido($_POST)));
    }
    function CambiarMesa(){        
        $row = $this->model->CambiarMesa($_POST);
        if ($row['cod'] == 1){
            header('location: ' . URL . 'venta');
        } else {
            header('location: ' . URL . 'venta');
        }
    }
    function MoverPedidos(){        
        $row = $this->model->MoverPedidos($_POST);
        if ($row['cod'] == 1){
            header('location: ' . URL . 'venta');
        } else {
            header('location: ' . URL . 'venta');
        }
    }
    function refrescar_mesas(){        
        $this->model->refrescar_mesas();
        header('location: ' . URL . 'venta');
    }
    function pedido_create($id){   
        $data = $this->model->$id($_POST);
        if($id != 'pc4'){
            if($data['fil'] == 1){
                Session::set('codped', $data['id_pedido']);
                if(Session::get('rol') == 5 && $id == 'pc1'){
                    print_r(json_encode($data));
                } else{
                    header('location: ' . URL . 'venta/orden/'.$data['id_pedido']);
                }                     
            } else {
                if(Session::get('rol') == 5){
                    print_r(json_encode(0));
                } else{
                    header('location: ' . URL . 'venta');
                }            
            }      
        }  else{
                Session::set('codped', $data['id_pedido']);
                header('location: ' . URL . 'venta/orden/'.$data['id_pedido']);
           
        }
    }
    function orden($id_pedido){
        $this->view->TipoDocumento = $this->model->TipoDocumento();
        $this->view->TipoPago = $this->model->TipoPago();
        $this->view->cod = '1'.date('dm').'97';
        $data = $this->model->ValidarEstadoPedido($id_pedido);
        if ($data['cod'] == 1){
            //SI FALLA PONES $this->view->js = array('venta/js/jquery-ui.min.js','venta/js/js-render.js','venta/js/3.1_venta_orden.js?'.random_version(),'venta/js/venta_all.js','venta/js/venta_cliente.js');

            $this->view->js = array('venta/js/jquery-ui.min.js','venta/js/js-render.js','venta/js/3.1_venta_orden.js?'.random_version(),'venta/js/venta_all.js','venta/js/venta_cliente.js');
            Session::set('codped', $id_pedido);
            Session::set('codtipoped', $data['tipo_pedido']);
            $this->view->render('venta/orden3.1',false);
        } else {
            header('location: ' . URL . 'venta');
        }
    }
    function RegistrarPedido()
    {
        $data = $this->model->ValidarEstadoPedido($_POST['cod_ped']);
        if ($data['cod'] == 1){
            $this->model->RegistrarPedido($_POST);
            print_r(json_encode(1));
        } else  {
            print_r(json_encode(2));
        }
    }

    function RegistrarVenta(){
       
        if($_POST['id_pedido'] != ''){
            $id_venta = $this->model->RegistrarVenta($_POST);
            print_r(json_encode($id_venta));
            if(Session::get('sunat') == 1){
               if($_POST['tipo_doc'] <> 3){
                    require_once 'api_fact/controller/api.php';
                    $invoice = new ApiSunat();
                    $data = $invoice->sendDocSunaht($id_venta,2);  
                }  
            } 
        }
    }

    function anular_pedido(){
        print_r(json_encode($this->model->anular_pedido($_POST)));
    }
    function b_p_s(){
        echo $this->model->deleteSeleccion($_POST);
    }
    function anular_venta(){
        print_r(json_encode($this->model->anular_venta($_POST)));
    }

    function defaultdata(){
        print_r(json_encode($this->model->defaultdata($_POST)));
    }

    function listarCategorias(){
        print_r(json_encode($this->model->listarCategorias($_POST)));
    }
    
    function listarProdsMasVend(){
        print_r(json_encode($this->model->listarProdsMasVend($_POST)));
    }

    function listarProductos(){
        print_r(json_encode($this->model->listarProductos($_POST)));
    }

    function buscar_producto(){
        print_r(json_encode($this->model->buscar_producto($_POST)));
    }

    function ListarDetallePed(){
        print_r(json_encode($this->model->ListarDetallePed($_POST)));
    }
    
    function cliente_crud(){
        if($_POST['id_cliente'] != ''){
            print_r(json_encode($this->model->cliente_crud_update($_POST)));
        } else {
            print_r(json_encode($this->model->cliente_crud_create($_POST)));
        }
    }

    function buscar_cliente()
    {
        print_r(json_encode($this->model->buscar_cliente($_POST)));
    }

    function buscar_cliente_telefono()
    {
        print_r(json_encode($this->model->buscar_cliente_telefono($_POST)));
    }

    function pedido_edit(){
        $this->model->pedido_edit($_POST);
    }

    function pedido_crud(){
        print_r(json_encode($this->model->pedido_crud_update($_POST)));
    }

    function pedido_delete()
    {
        $this->model->pedido_delete($_POST);
    }

    function venta_edit(){
        $this->model->venta_edit($_POST);
    }

    function venta_edit_pago(){
        print_r(json_encode($this->model->venta_edit_pago($_POST)));
    }

    function venta_edit_documento(){
        print_r(json_encode($this->model->venta_edit_documento($_POST)));
        if(Session::get('sunat') == 1){
           if($_POST['id_tipo_documento'] <> 3){
                require_once 'api_fact/controller/api.php';
                $invoice = new ApiSunat();
                $data = $invoice->sendDocSunaht($_POST['id_venta'],2);  
            }  
        }
    }

    function tags_list()
    {
        print_r(json_encode($this->model->tags_list($_POST)));
    }

    function tags_crud()
    {
        print_r(json_encode($this->model->tags_crud($_POST)));
    }

    /* INICIO COMPROBANTE SIN ENVIAR SUNAT */

    function contadorSunatSinEnviar(){
        print_r(json_encode($this->model->contadorSunatSinEnviar()));
    }

    /* FIN COMPROBANTE SIN ENVIAR SUNAT */
	
    /* INICIO PEDIDOS PREPARADOS */

    function contadorPedidosPreparados(){
        print_r(json_encode($this->model->contadorPedidosPreparados()));
    }

    function listarPedidosPreparados(){
        $this->model->listarPedidosPreparados();
    }

    function pedidoEntregado(){
        print_r(json_encode($this->model->pedidoEntregado($_POST)));
    }

    function pedido_estado_update(){
        print_r(json_encode($this->model->pedido_estado_update($_POST)));
    }

    function menu_categoria_list()
    {
        $this->model->menu_categoria_list($_POST);
    }

    function menu_plato_list(){
        $this->model->menu_plato_list($_POST);
    }

    function menu_plato_estado(){
        print_r(json_encode($this->model->menu_plato_estado($_POST)));
    }

    function impresion_precuenta($id_pedido, $piso, $data_seleccionado)
    {
        if(Session::get('print_pre') == 1){
            $fecha = $_GET['fecha_imp'] ? $_GET['fecha_imp'] : '-';
            if($fecha != ''){
                $dato = $this->model->impresion_precuenta($id_pedido);
                header('location: http://'.Session::get('pc_ip').'/imprimir/pre_cuenta.php?data='.urlencode(json_encode($dato)).'&piso='.$piso.'&data_selected='.$data_seleccionado . '&fecha_imp='.$fecha);
            }else{
                $dato = $this->model->impresion_precuenta($id_pedido);
                header('location: http://'.Session::get('pc_ip').'/imprimir/pre_cuenta.php?data='.urlencode(json_encode($dato)).'&piso='.$piso.'&data_selected='.$data_seleccionado);
            }
        } else {
            $this->view->dato = $this->model->impresion_precuenta($id_pedido);
            $this->view->render('venta/imprimir/precuenta', true);
        }
    }
    // pdf reparto 
    function impresion_reparto($id_venta)
    {
        if(Session::get('print_cpe') == 1){
            $dato = $this->model->impresion_reparto($id_venta);
            header('location: http://'.Session::get('pc_ip').'/imprimir/ticket_reparto.php?data='.json_encode($dato));            
        } else {
            $this->view->dato = $this->model->impresion_reparto($id_venta);
            $this->view->render('venta/imprimir/ticketreparto', true);
        }
    }
    
    function impresion_comanda()
    {
        $this->view->render('venta/imprimir/comanda', true);
    }


    function contador_comanda(){
        print_r(json_encode($this->model->contador_comanda($_POST)));
    }

    function Personal()
    {
        print_r(json_encode($this->model->Personal()));
    }

    function alert_pedidos_programados(){
        print_r(json_encode($this->model->alert_pedidos_programados()));
    }
    function verifica_porteria(){
        $this->model->verifica_porteria();
    }
    function registra_impresion(){
        $this->model->registra_impresion($_POST);
    }
    function info_product($id){
        $this->model->info_product($id);
    }
    /* FIN PEDIDOS PREPARADOS */
}
function random_version (){
    $hash =  base64_encode(openssl_random_pseudo_bytes(30));
    $v = $hash.'='.uniqid();
    return $v;
}