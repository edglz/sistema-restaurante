<?php
    Session::init();
    if(!Session::get("rol")){
        header('location: ' . URL . 'err/danger');
    }
    class Recetas extends Controller{
        function __construct()
        {
            parent::__construct();
        }
        function index(){
            $this->view->js = array("recetas/js/recetasIndex.js");
            $this->view->render("recetas/index", false);
        }
    }