<?php Session::init(); $ver = (Session::get('rol') == 1 OR Session::get('rol') == 2 OR Session::get('rol') == 3) ? '' :  header('location: ' . URL . 'err/danger'); ?>
<?php

class Carta extends Controller {

	function __construct() {
		parent::__construct();
	}
    function index(){
        $old_cart = $this->model->Carta();
        $this->view->Carta = $old_cart->route;
        $this->view->js = array('carta/js/upload_carta.js?'.random_version());
        $this->view->render('carta/index');
    }
	function imprime_carta(){
        $this->view->render('carta/imprimir/cartilla_virtual', true);
    }
    function sube_carta(){
        if($_FILES){
              $cade = "BHDASGDJASBNASDGSADYHUGASDUIH3783426324JSQDBSDHAJGDASHYDASGJSDHAGHSDAHU"; 
            if($_FILES['cartilla']['size'] <= 10 * MB){
                if($_FILES['cartilla']['type'] == "application/pdf"){
                    $old_cart = $this->model->Carta();
                    $old_cart = $old_cart->route;
                    unlink($old_cart);
                    $route =  'public/pdf/'.md5(substr(str_shuffle($cade), 0, 15)).'.pdf';
                    move_uploaded_file($_FILES['cartilla']['tmp_name'], $route);
                    $this->model->ActualizaCarta($route);
                }else{
                    echo 'Extension de archivo no permitida';
                }
            }else{
                echo 'El archivo supera el máximo permitido';
            }
        }
    }
    
}
function random_version (){
    $hash =  base64_encode(openssl_random_pseudo_bytes(30));
    $v = $hash.'='.uniqid();
    return $v;
}