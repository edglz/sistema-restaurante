<?php
    Session::init();
    if(!Session::get("rol")){
        header('location: ' . URL . 'err/danger');
    }
    class Receta extends Controller{
        function __construct()
        {
            parent::__construct();
        }
        function index(){
            $this->view->js = array("recetas/js/recetasIndex.js?v=".time());
            $this->view->render("recetas/index", false);
        }
        function nueva(){
            $this->view->Catg = $this->model->CategoriasAll();
            $this->view->Producto = $this->model->ProductosAll();
            $this->view->Preparados = $this->model->Preparados();
            $this->view->js = array("recetas/js/recetaNueva.js?v=".time());
            $this->view->render("recetas/nueva", false);
        }

        function edita($id){
            $this->view->id_rec = $id;
            $this->view->Catg = $this->model->CategoriasAll();
            $this->view->Producto = $this->model->ProductosAll();
            $this->view->Preparados = $this->model->Preparados();
            $this->view->js = array("recetas/js/editareceta.js?v=".time());
            $this->view->render("recetas/edita", false);
        }
        function imprimir($id){
            $this->view->empresa = $this->model->Empresa();
            $this->view->receta = $this->model->receta($id);
            $this->view->render("recetas/imprimir/receta_gen", true);
        }
        function preparados(){
            $this->view->js = array("recetas/js/preparado_index.js?v=".time());
            $this->view->render("recetas/preparados/index", false);
        }
        ## CONSULTAS A BASE DE DATOS.
        function lista_categorias(){
            $this->model->Categorias();
        }
        function elimina_Categoria(){
            $this->model->eliminaCategoria($_POST);
        }
        function receta_cat_crud(){
            if($_POST['id_catg_categoria'] != ""){
                // ACTUALIZAMOS
                $this->model->editaCategoria($_POST);

            }else{
                // AGREGAMOS
                $this->model->registraCategoria($_POST);
            }
        }
        function crud_rec(){
            if(!$_POST['id_rec']){
                $this->model->registra_receta($_POST);
            }else{
                $this->model->eliminaReceta($_POST['id_rec']);
                $this->model->registra_receta($_POST);
            }
          
        }
        function receta_list(){
            try{
                $this->model->receta_list($_POST);
            }catch(Exception $e){
                echo $e->getMessage();
            }
        }
        function list_preparados(){
            $this->model->list_preparados();
        }
        function listaPresentaciones($id){
            $this->model->PresentacionesAll($id);
        }
        function eliminaReceta($id){
            $this->model->eliminaReceta($id);
        }
        function ver_receta($id){
            $c = $this->model->receta_edit_fetch($id);
            $c = array("data" => $c);
            echo json_encode($c);
        }
        ##
        public function get_catg_preparados(){
            $this->model->get_catg_preparados();
        }
        public function crud_preparados($method){
            switch ($method) {
                case 'insert':
                    $this->model->crud_catg_preparados($_POST);
                break;
                case 'update':
                    $this->model->crud_catg_preparados($_POST);
                break;
                case 'delete':
                    $this->model->delete_catg_prep($_POST['id_catg']);
                break;
            }

        }
        public function get_preparados(){
            $this->model->get_preparados($_POST['id_catg']);
        }
    }