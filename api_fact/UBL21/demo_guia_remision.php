<?php

// RUTA para enviar documentos: Tu puedes definir tu propia ruta, en nustro caso la tenemos en la siguiente dirección
$ruta = "http://localhost/FAE/UBL21/ws/guia_remision.php";
//$ruta = 'http://api.corporacion3v.com/UBL21/ws/guia_remision.php';

//se recomienda leer: http://cpe.sunat.gob.pe/sites/default/files/inline-images/Guia%2BXML%2BFactura%2Bversion%202-1%2B1%2B0%20%282%29.pdf

$data = array(
    "tipo_proceso" => 3,
    "pass_firma" => '123456',
    //Cabecera del documento
    "serie_comprobante" => "T001",
    "numero_comprobante" => (string) generar_numero_aleatorio(8),
    "fecha_comprobante" => date('Y-m-d'),
    "cod_tipo_documento" => "09",
    "nota" => "esta es una nota",
    //01 VENTA, 14 VENTA SUJETA A CONFIRMACION DEL COMPRADOR, 02 COMPRA
    //04 TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA, 18 TRASLADO EMISOR ITINERANTE CP
    //08 IMPORTACION, 09 EXPORTACION, 19 TRASLADO A ZONA PRIMARIA, 13 OTROS
    "codmotivo_traslado" => "01",
    "motivo_traslado" => "Venta",
    "peso" => "100.000",
    "numero_paquetes" => "5",
    "codtipo_transportista" => "01", //01 Transporte público, 02 Transporte privado
    "tipo_documento_transporte" => "6", //6: indica RUC: Catálogo 06
    "nro_documento_transporte" => "20100066633",
    "razon_social_transporte" => "Transportes Ejemplo SRL",
    "ubigeo_destino" => "070155",
    "dir_destino" => "Jr. Direccion Destino 345",
    "ubigeo_partida" => "070104",
    "dir_partida" => "Jr. Direccion Partida 112",
    //Datos del cliente
    "cliente_numerodocumento" => "20100065403",
    "cliente_nombre" => "GRAFICA INDUSTRIAL SRL",
    "cliente_tipodocumento" => "6", //1: DNI
    //data de la empresa emisora o contribuyente que entrega el documento electrónico.
    "emisor" => array(
        "ruc" => "20100077707",
        "tipo_doc" => "6",
        "nom_comercial" => "Tu Empresa SRL",
        "razon_social" => "Tu Empresa SRL",
        "codigo_ubigeo" => "070104",
        "direccion" => "Jr. Puno 4654",
        "direccion_departamento" => "LIMA",
        "direccion_provincia" => "LIMA",
        "direccion_distrito" => "LIMA",
        "direccion_codigopais" => "PE",
        "usuariosol" => "MODDATOS",
        "clavesol" => "moddatos"
    ),
    //items
    "detalle" => array(
        array(
            "ITEM" => "1",
            "PESO" => "100.000",
            "NUMERO_ORDEN" => "1",
            "DESCRIPCION" => "Producto 01",
            "CODIGO_PRODUCTO" => "PIUU8"
        )
    )
);

//Invocamos el servicio
$token = ''; //en caso quieras utilizar algún token generado desde tu sistema
//codificamos la data
$data_json = json_encode($data);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ruta);
curl_setopt(
        $ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Token token="' . $token . '"',
    'Content-Type: application/json',
        )
);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$respuesta = curl_exec($ch);
curl_close($ch);

$response = json_decode($respuesta, true);

echo "=========== DATA RETORNO =============== ";
echo "<br /><br />respuesta	: " . $response['respuesta'];
echo "<br /><br />hash_cpe	: " . $response['hash_cpe'];
echo "<br /><br />hash_cdr	: " . $response['hash_cdr'];
echo "<br /><br />msj_sunat	: " . $response['msj_sunat'];
echo "<br /><br />ruta_pdf	: " . $response['ruta_pdf'];

function generar_numero_aleatorio($longitud) {
    $key = '';
    $pattern = '1234567890';
    $max = strlen($pattern) - 1;
    for ($i = 0; $i < $longitud; $i++)
        $key .= $pattern{mt_rand(0, $max)};
    return $key;
}

?>