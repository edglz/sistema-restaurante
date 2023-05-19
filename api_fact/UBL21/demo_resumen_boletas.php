<?php

$ruta = 'https://api.beholia.com/UBL21/ws/resumen_boletas.php';

$data = array(
    "pass_firma" => '123456',
    "tipo_proceso" => 3,
    //Cabecera del documento
    "codigo" => "RC",
    "serie" => '20190722',
    "secuencia" => (string) generar_numero_aleatorio(2),
    "fecha_referencia" => '2019-07-22',
    "fecha_documento" => '2019-07-22',
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
            "TIPO_COMPROBANTE" => "03",
            "NRO_COMPROBANTE" => "B001-43",
            "NRO_DOCUMENTO" => "00000000",
            "TIPO_DOCUMENTO" => "1",
            "NRO_COMPROBANTE_REF" => "0",
            "TIPO_COMPROBANTE_REF" => "0",
            "STATUS" => "1",//3-resumen dirio de boletas
            "COD_MONEDA" => "PEN",
            "TOTAL" => "100.94",
            "GRAVADA" => "85.54",
            "IGV" => "15.4",
            "EXONERADO" => "0",
            "INAFECTO" => "0",
            "EXPORTACION" => "0",
            "GRATUITAS" => "0",
            "MONTO_CARGO_X_ASIG" => "0",
            "CARGO_X_ASIGNACION" => "0",
            "ISC" => "0",
            "OTROS" => "0"
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