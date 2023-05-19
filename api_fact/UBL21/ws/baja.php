<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "../controllers/validaciondedatos.php";
include "../controllers/procesarcomprobante.php";

error_reporting(E_ALL ^ E_NOTICE);
header("Access-Control-Allow-Origin: *");

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

$bodyRequest = file_get_contents("php://input");


$data = json_decode($bodyRequest, true);

$array_emisor = get_array_emisor($data);
$array_detalle = get_array_detalle($data);
$array_cabecera = get_array_cabecera($data, $array_emisor);
$tipodeproceso = (isset($data['tipo_proceso'])) ? $data['tipo_proceso'] : "3";

$url_base = '../archivos_xml_sunat/';
$content_folder_xml = 'cpe_xml/';
$content_firmas = 'certificados/';

$nombre_archivo = $array_emisor['ruc'] . '-' . $data['codigo'] . '-' . $data['serie'] . '-' . $data['secuencia'];

if ($tipodeproceso == '1') {
    $ruta = $url_base . $content_folder_xml . 'produccion/' . $array_emisor['ruc'] . '/' . $nombre_archivo;
    $ruta_cdr = $url_base . $content_folder_xml . 'produccion/' . $array_emisor['ruc'] . '/';
    $ruta_firma = $url_base . $content_firmas . 'produccion/' . $array_emisor['ruc'] . '/' . $array_emisor['ruc'] . '.pfx';
    $ruta_ws = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService';
    $pass_firma = $data['pass_firma'];
}

if ($tipodeproceso == '3') {
    $ruta = $url_base . $content_folder_xml . 'beta/' . $array_emisor['ruc'] . '/' . $nombre_archivo;
    $ruta_cdr = $url_base . $content_folder_xml . 'beta/' . $array_emisor['ruc'] . '/';
    $ruta_firma = $url_base . $content_firmas . 'beta/' . $array_emisor['ruc'] . '/' . $array_emisor['ruc'] . '.pfx';
    $pass_firma = $data['pass_firma'];
    $ruta_ws = 'https://e-beta.sunat.gob.pe:443/ol-ti-itcpfegem-beta/billService';
}

$rutas = array();
$rutas['nombre_archivo'] = $nombre_archivo;
$rutas['ruta_xml'] = $ruta;
$rutas['ruta_cdr'] = $ruta_cdr;
$rutas['ruta_firma'] = $ruta_firma;
$rutas['pass_firma'] = $pass_firma;
$rutas['ruta_ws'] = $ruta_ws;

$procesarcomprobante = new Procesarcomprobante();
$resp = $procesarcomprobante->procesar_baja_sunat($array_cabecera, $array_detalle, $rutas);
$resp['file'] = $nombre_archivo;
echo json_encode($resp);

function get_array_cabecera($data, $emisor) {
    $cabecera = array(
        'CODIGO' => $data['codigo'],
        'SERIE' => $data['serie'],
        'SECUENCIA' => $data['secuencia'],
        'FECHA_REFERENCIA' => $data['fecha_referencia'],
        'FECHA_BAJA' => $data['fecha_baja'],
        'NRO_DOCUMENTO_EMPRESA' => $emisor['ruc'],
        'TIPO_DOCUMENTO_EMPRESA' => $emisor['tipo_doc'], 
        'NOMBRE_COMERCIAL_EMPRESA' => $emisor['nom_comercial'],
        'CODIGO_UBIGEO_EMPRESA' => $emisor['codigo_ubigeo'],
        'DIRECCION_EMPRESA' => $emisor['direccion'],
        'DEPARTAMENTO_EMPRESA' => $emisor['direccion_departamento'],
        'PROVINCIA_EMPRESA' => $emisor['direccion_provincia'],
        'DISTRITO_EMPRESA' => $emisor['direccion_distrito'],
        'CODIGO_PAIS_EMPRESA' => $emisor['direccion_codigopais'],
        'RAZON_SOCIAL_EMPRESA' => $emisor['razon_social'],
        'CONTACTO_EMPRESA' => "",
        'EMISOR_RUC' => $emisor['ruc'],
        'EMISOR_USUARIO_SOL' => $emisor['usuariosol'],
        'EMISOR_PASS_SOL' => $emisor['clavesol']
    );

    return $cabecera;
}

function get_array_detalle($data) {
    $detalle_documento = $data['detalle'];
    return $detalle_documento;
}

function get_array_emisor($data) {
    $data_emisor = $data['emisor'];

    $emisor['ruc'] = (isset($data_emisor['ruc'])) ? $data_emisor['ruc'] : '';
    $emisor['tipo_doc'] = (isset($data_emisor['tipo_doc'])) ? $data_emisor['tipo_doc'] : '6';
    $emisor['nom_comercial'] = (isset($data_emisor['nom_comercial'])) ? $data_emisor['nom_comercial'] : '';
    $emisor['razon_social'] = (isset($data_emisor['razon_social'])) ? $data_emisor['razon_social'] : '';
    $emisor['codigo_ubigeo'] = (isset($data_emisor['codigo_ubigeo'])) ? $data_emisor['codigo_ubigeo'] : '';
    $emisor['direccion'] = (isset($data_emisor['direccion'])) ? $data_emisor['direccion'] : '';
    $emisor['direccion_departamento'] = (isset($data_emisor['direccion_departamento'])) ? $data_emisor['direccion_departamento'] : '';
    $emisor['direccion_provincia'] = (isset($data_emisor['direccion_provincia'])) ? $data_emisor['direccion_provincia'] : '';
    $emisor['direccion_distrito'] = (isset($data_emisor['direccion_distrito'])) ? $data_emisor['direccion_distrito'] : '';
    $emisor['direccion_codigopais'] = (isset($data_emisor['direccion_codigopais'])) ? $data_emisor['direccion_codigopais'] : '';
    $emisor['usuariosol'] = (isset($data_emisor['usuariosol'])) ? $data_emisor['usuariosol'] : '';
    $emisor['clavesol'] = (isset($data_emisor['clavesol'])) ? $data_emisor['clavesol'] : '';

    return $emisor;
}
