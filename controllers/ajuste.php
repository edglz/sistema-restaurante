<?php Session::init(); $ver = (Session::get('rol') == 1) OR Session::get('rol') == 2 ? '' :  header('location: ' . URL . 'err/danger'); ?>
<?php 

class Ajuste extends Controller {

	function __construct() {
		parent::__construct();
	}
	
	function index() 
	{	
		$this->view->render('ajuste/index', false);
	}

	/* INICIO MODULO EMPRESA */

	function datosempresa(){
		$this->view->js = array('ajuste/js/aju_emp_datos.js','');
		$this->view->render('ajuste/all/empresa', false);
	}

	function datosempresa_data()
    {
        print_r(json_encode($this->model->datosempresa_data()));
    }

    function datosempresa_crud()
    {
        print_r(json_encode($this->model->datosempresa_crud($_POST)));
    }

    function tipodoc(){
		$this->view->js = array('ajuste/js/aju_emp_tdoc.js');
		$this->view->render('ajuste/all/tipodoc', false);
	}

	function tipodoc_list(){
        $this->model->tipodoc_list();
    }

    function tipodoc_crud(){
        print_r(json_encode($this->model->tipodoc_crud($_POST)));
    }

    function usuario(){
        $this->view->js = array('ajuste/js/aju_emp_usu.js');
        $this->view->render('ajuste/all/usu_all', false);
    }

    function usuario_list()
    {
        $this->model->usuario_list($_POST);
    }

    function usuario_nuevo(){
        $this->view->Rol = $this->model->Rol();
        $this->view->AreaProduccion = $this->model->AreaProduccion();
        $this->view->Mesas = $this->model->Mesas();
        $this->view->js = array('ajuste/js/wizard/jquery.bootstrap.wizard.js','ajuste/js/wizard/wizard.js','ajuste/js/aju_emp_usu_edit.js');
        $this->view->render('ajuste/all/usu_edit', false);
    }

    function usuario_edit($id){
        $this->view->Rol = $this->model->Rol();
        $this->view->AreaProduccion = $this->model->AreaProduccion();
        $this->view->Mesas = $this->model->Mesas();
        $this->view->js = array('ajuste/js/wizard/jquery.bootstrap.wizard.js','ajuste/js/wizard/wizard.js','ajuste/js/aju_emp_usu_edit.js');
        $this->view->usuario = $this->model->usuario_data($id);
        $this->view->render('ajuste/all/usu_edit', false);
    }

    function usuario_crud(){   
        if($_POST['id_usu'] != ''){
           $this->model->usuario_crud_update($_POST);
           print_r(json_encode(2));
        } else{
           $row=$this->model->usuario_crud_create($_POST);
           if ($row['cod'] == 1){
                print_r(json_encode(0));
            } else {
                print_r(json_encode(1));
            }
        }
    }

    function usuario_estado(){
        print_r(json_encode($this->model->usuario_estado($_POST)));
    }

    function usuario_delete(){
        print_r(json_encode($this->model->usuario_delete($_POST)));
    }

	/* FIN MODULO EMPRESA */


	/* INICIO MODULO RESTAURANTE */

	function caja(){
		$this->view->js = array('ajuste/js/aju_res_caja.js');
		$this->view->render('ajuste/all/caja', false);
	}

	function caja_list(){
        $this->model->caja_list();
    }

    function caja_crud(){
        if($_POST['id_caja'] != ''){
           print_r(json_encode($this->model->caja_crud_update($_POST)));
        } else{
           print_r(json_encode($this->model->caja_crud_create($_POST)));
        }
    }

    function areaprod(){
    	$this->view->Impresora = $this->model->Impresora();
		$this->view->js = array('ajuste/js/aju_res_areaprod.js');
		$this->view->render('ajuste/all/areaprod', false);
	}

    function areaprod_list()
    {
        $this->model->areaprod_list($_POST);
    }

    function areaprod_crud()
    {
        if($_POST['id_areap'] != ''){
           print_r(json_encode( $this->model->areaprod_crud_update($_POST)));
        } else{
           print_r(json_encode( $this->model->areaprod_crud_create($_POST)));
        }
    }

    function salonmesa(){
		$this->view->js = array('ajuste/js/aju_res_salmes.js?v='.rand_version());
        $this->view->Usuarios_rol = $this->model->Usuarios_rol();
        $this->view->Mozo = $this->model->Mozo();
		$this->view->render('ajuste/all/salonymesa', false);
	}

    function salon_list()
    {
        $this->model->salon_list($_POST);
    }

    function salon_crud()
    {
        if($_POST['id_salon'] != ''){
           print_r(json_encode( $this->model->salon_crud_update($_POST)));
        } else{
           print_r(json_encode( $this->model->salon_crud_create($_POST)));
        }
    }

    function salon_crud_delete()
    {
        if($_POST['id_salon'] != ''){
           print_r(json_encode( $this->model->salon_crud_delete($_POST)));
        } 
    }

    function mesa_list()
    {
        $this->model->mesa_list($_POST);
    }

    function mesa_crud()
    {
        if($_POST['id_mesa'] != '' and $_POST['id_salon'] != ''){
           print_r(json_encode( $this->model->mesa_crud_update($_POST)));
        } else{
           print_r(json_encode( $this->model->mesa_crud_create($_POST)));
        }
    }

    function mesa_crud_delete()
    {
        if($_POST['id_mesa'] != ''){
           print_r(json_encode( $this->model->mesa_crud_delete($_POST)));
        } 
    }

    /* ======================= INICIO PRODUCTO */

    function producto(){
    	$this->view->AreaProduccion = $this->model->AreaProduccion();
		$this->view->js = array('ajuste/js/wizard/jquery.bootstrap.wizard.js','ajuste/js/wizard/wizard.js','ajuste/js/wizard/jquery-ui.min.js','ajuste/js/aju_res_prod.js','ajuste/js/aju_res_prod_ins.js', '');
		$this->view->render('ajuste/all/producto', false);
	}
    function precios(){
        $this->view->AreaProduccion = $this->model->AreaProduccion();
        $this->view->js = array(
            'ajuste/js/wizard/jquery.bootstrap.wizard.js',
            'ajuste/js/wizard/wizard.js',
            'ajuste/js/wizard/jquery-ui.min.js',
            'ajuste/js/aju_precios_edit.js?v='. rand(12, 57) / 10
        );
        $this->view->render('ajuste/all/Precios', false);
    }

	function producto_cat_list()
    {
        $this->model->producto_cat_list($_POST);
    }

	function producto_list()
    {
        $this->model->producto_list($_POST);
    }
  
    function producto_pres_list()
    {
        $this->model->producto_pres_list($_POST);
    }

    function producto_pres_ing()
    {
        print_r(json_encode($this->model->producto_pres_ing($_POST)));
    }

    function producto_combo_cat()
    {
        print_r(json_encode($this->model->producto_combo_cat()));
    }

    function producto_combo_unimed()
    {
        $this->model->producto_combo_unimed($_POST);
    }

    function producto_buscar_ins()
    {
        print_r(json_encode($this->model->producto_buscar_ins($_POST)));
    }

    function producto_ingrediente_create()
    {
        print_r(json_encode( $this->model->producto_ingrediente_create($_POST)));
    }

    function producto_ingrediente_update()
    {
        print_r(json_encode($this->model->producto_ingrediente_update($_POST)));
    }

    function producto_ingrediente_delete()
    {
        print_r(json_encode($this->model->producto_ingrediente_delete($_POST)));
    }

    function producto_crud()
    {
        if($_POST['id_prod'] != ''){
           print_r(json_encode($this->model->producto_crud_update($_POST)));
        } else{
           print_r(json_encode($this->model->producto_crud_create($_POST)));
        }
    }

    function producto_pres_crud()
    {    
        if($_POST['id_pres_presentacion'] != ''){
           print_r(json_encode($this->model->producto_pres_crud_update($_POST)));
        } else{
           print_r(json_encode($this->model->producto_pres_crud_create($_POST)));
        }
    }

    function producto_cat_crud()
    {
        if($_POST['id_catg_categoria'] != ''){
           print_r(json_encode($this->model->producto_cat_crud_update($_POST)));
        } else{
           print_r(json_encode($this->model->producto_cat_crud_create($_POST)));
        }
    }

    function producto_cat_delete()
    {
        print_r(json_encode($this->model->producto_cat_delete($_POST)));
    }

	/* ======================== FIN PRODUCTO */

    /* ======================== INICIO COMBOS */
    function combo(){
        $this->view->AreaProduccion = $this->model->AreaProduccion();
        $this->view->js = array('ajuste/js/wizard/jquery.bootstrap.wizard.js','ajuste/js/wizard/wizard.js','ajuste/js/wizard/jquery-ui.min.js','ajuste/js/aju_res_comb.js','ajuste/js/aju_res_prod_ins.js');
        $this->view->render('ajuste/all/combo', false);
    }

    function combo_list()
    {
        $this->model->combo_list($_POST);
    }

    /* ======================== FIN COMBOS */

	/* ======================== INICIO INSUMO */

	function insumo(){
    	$this->view->UnidadMedida = $this->model->UnidadMedida();
		$this->view->js = array('ajuste/js/aju_res_ins.js');
		$this->view->render('ajuste/all/insumo', false);
	}

	function insumo_cat_list()
    {
        $this->model->insumo_cat_list($_POST);
    }

    function insumo_list()
    {
        $this->model->insumo_list($_POST);
    }

    function insumo_combo_cat()
    {
        print_r(json_encode($this->model->insumo_combo_cat()));
    }

   function insumo_cat_crud()
    {
        if($_POST['id_catg'] != ''){
           print_r(json_encode( $this->model->insumo_cat_crud_update($_POST)));
        } else{
           print_r(json_encode( $this->model->insumo_cat_crud_create($_POST)));
        }
    }

    function insumo_crud()
    {
        if($_POST['id_ins'] != ''){
           print_r(json_encode( $this->model->insumo_crud_update($_POST)));
        } else{
           print_r(json_encode( $this->model->insumo_crud_create($_POST)));
        }
    }

    function insumo_cat_delete()
    {
        print_r(json_encode($this->model->insumo_cat_delete($_POST)));
    }

    function printer(){
        $this->view->js = array('ajuste/js/aju_res_print.js');
        $this->view->render('ajuste/all/print', false);
    }

    function print_list()
    {
        $this->model->print_list($_POST);
    }

    function print_crud()
    {
        if($_POST['id_imp'] != ''){
           print_r(json_encode( $this->model->print_crud_update($_POST)));
        } else{
           print_r(json_encode( $this->model->print_crud_create($_POST)));
        }
    }
    /* ======================== FIN INSUMO */

    /* FIN MODULO RESTAURANTE */

    /* INICIO MODULO SISTEMA */

    /* ======================== INICIO OPTIMIZACION */
    function optimizar(){
        $this->view->js = array('ajuste/js/aju_opt_pedidos.js');
        $this->view->render('ajuste/all/optimizar', false);
    }

    function optimizar_pedidos()
    {
        print_r(json_encode($this->model->optimizar_pedidos($_POST)));
    }

    function optimizar_ventas()
    {
        print_r(json_encode($this->model->optimizar_ventas($_POST)));
    }

    function optimizar_productos()
    {
        print_r(json_encode($this->model->optimizar_productos($_POST)));
    }

    function optimizar_insumos()
    {
        print_r(json_encode($this->model->optimizar_insumos($_POST)));
    }

    function optimizar_clientes()
    {
        print_r(json_encode($this->model->optimizar_clientes($_POST)));
    }

    function optimizar_proveedores()
    {
        print_r(json_encode($this->model->optimizar_proveedores($_POST)));
    }

    function optimizar_mesas()
    {
        print_r(json_encode($this->model->optimizar_mesas($_POST)));
    }

    /* ======================== FIN OPTIMIZACION */


    /* ======================== INCIO OTROS AJUSTES */

    function sistema(){
        $this->view->js = array('ajuste/js/aju_opt_sis.js');
        $this->view->render('ajuste/all/sistema', false);
    }

    function datosistema_data()
    {
        print_r(json_encode($this->model->datosistema_data()));
    }

    function datosistema_crud()
    {
        print_r(json_encode($this->model->datosistema_crud($_POST)));
    }
    //NUEVAS FUNCIONES BY RDJ
    function select_Products_byCategory(){
        $this->model->select_Products_byCategory($_POST);
    }
    function seleccionar_precios(){
       
       $this->model->seleccionar_precios_por_presentacion($_POST);
    }
    function agregar_precio(){
      $this->model->agregar_precio($_POST);
    }
    function eliminar_precio(){
        $this->model->eliminar_precio($_POST);
    }
    function verificar_existencia_de_precio(){
        $this->model->verificar_existencia_de_precio($_POST);
    }
    function fetch_dias_registrados(){
        $this->model->extraer_dias($_POST);
    }
    function editar_precio(){
        $this->model->editar_precio($_POST);
    }
    function contar_precios(){
        $this->model->contar_precios($_POST);
    }
    function cambiar_precio_por_id(){
        $this->model->cambiar_precio_por_id($_POST);
    }
    function cambiar_precio_por_categoria(){
        $this->model->cambiar_precio_por_categoria($_POST);
    }
    function cambiar_precio_general(){
        $this->model->cambiar_precio_general($_POST);
    }
    function add_rol_salon(){
        $this->model->add_rol_salon($_POST);
    }
    function listar_personal(){
        $this->model->listar_personal($_POST);

    }
    function Mozo1(){
        $this->model->Mozo1();

    }
    function listar_usuarios_en_salon(){
        $this->model->listar_usuarios_en_salon($_POST);
    }
    function borrar_acceso(){
        $this->model->borrar_acceso($_POST);
    }
    /* ======================== FIN OTROS AJUSTES */

    /* FIN MODULO SISTEMA */

}
function rand_version(){
	return rand(12, 5000) / 100;
}