<?php

$ruta = 'https://api.beholia.com/UBL21/ws/baja.php';

$data = array(
    //Cabecera del documento
    "pass_firma" => '123456',
    "codigo" => "RA",
    "serie" => date('Ymd'),
    "secuencia" => (string) generar_numero_aleatorio(2),
    "fecha_referencia" => date('Y-m-d'),
    "fecha_baja" => date('Y-m-d'),
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
            "TIPO_COMPROBANTE" => "01",
            "SERIE" => "F001",
            "NUMERO" => "456896",
            "MOTIVO" => "Error en Factura"
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

function generar_numero_aleatorio($longitud) {
    $key = '';
    $pattern = '1234567890';
    $max = strlen($pattern) - 1;
    for ($i = 0; $i < $longitud; $i++)
        $key .= $pattern{mt_rand(0, $max)};
    return $key;
}

?>