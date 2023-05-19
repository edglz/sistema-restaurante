<?php
class Impresion extends Controller {
    function __construct() {
		parent::__construct();
	}
	
	function index() 
	{	  
		$this->view->render('impresion/index');
	}
}