<?php

require 'config.php';
require 'util/Auth.php';


// __autoload / Also spl_autoload_register (Take a look at it if you like)
function banshee_autoload($class) {
	require LIBS . $class .".php";
}
function returnJsonData($message, $handler) {
    // Crear el arreglo asociativo con los datos proporcionados
    $data = array(
        "message" => $message,
        "handler" => $handler
    );
    
    // Convertir el arreglo asociativo a JSON
    $json = json_encode($data);
    
	echo $json;
}


/*// Use an autoloader!
require 'libs/Bootstrap.php';
require 'libs/Controller.php';
require 'libs/Model.php';
require 'libs/View.php';

// Library
require 'libs/Database.php';
require 'libs/Session.php';
require 'libs/Hash.php';
*/
spl_autoload_register ("banshee_autoload");
$bootstrap = new Bootstrap();
$bootstrap->init();

