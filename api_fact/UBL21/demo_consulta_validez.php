<?php
// RUTA para enviar documentos: Tu puedes definir tu propia ruta, en nustro caso la tenemos en la siguiente dirección
$ruta = "http://localhost/FAE/UBL21/ws/consulta_validez.php";

$data = array(
        "EMISOR_RUC" => "20563083183",
        "EMISOR_USUARIO_SOL" => "3VFACT02",
        "EMISOR_PASS_SOL" => "3vfactcL",
        "TIPO_DOCUMENTO" => "01",
        "SERIE_DOCUMENTO" => "F002",
        "NUMERO_DOCUMENTO" => "29"
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


echo '<pre>';
print_r($response);