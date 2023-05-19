<?php
class Client extends Controller {
    function __construct() {
		parent::__construct();
	}
	
	function index() 
	{	  
		$old_cart = $this->model->Carta();
        $this->view->Carta = $old_cart->route;
        $this->view->js = array('client/js/app.js');
		$this->view->render('client/index');
	}
}