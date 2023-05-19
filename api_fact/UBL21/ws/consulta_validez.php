<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "../controllers/validaciondedatos.php";
include "../controllers/procesarcomprobante.php";

error_reporting(E_ALL ^ E_NOTICE);
/*para aceptar la conexión desde cualquier origen*/
header("Access-Control-Allow-Origin: *");

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

$bodyRequest = file_get_contents("php://input");

$data = json_decode($bodyRequest, true);

$procesarcomprobante = new Procesarcomprobante();
$resp = $procesarcomprobante->procesar_consulta_validez($data);

echo json_encode($resp);

