<?php

// RUTA para enviar documentos: Tu puedes definir tu propia ruta, en nustro caso la tenemos en la siguiente dirección
//$ruta = "http://localhost/FAE_2.1/ws/notacredito.php";
$ruta = 'http://api.corporacion3v.com/UBL21/ws/notacredito.php';

//se recomienda leer: http://cpe.sunat.gob.pe/sites/default/files/inline-images/Guia%2BXML%2BFactura%2Bversion%202-1%2B1%2B0%20%282%29.pdf


$data = array(
    "pass_firma" => '123456',
    //Cabecera del documento
    "total_gravadas" => "8.47",
    "porcentaje_igv" => "18.00",
    "total_igv" => "1.53",
    "total" => "10",
    "serie_comprobante" => "F001",
    "numero_comprobante" => (string) generar_numero_aleatorio(6),
    "fecha_comprobante" => date('Y-m-d'),
    "cod_tipo_documento" => "07",
    "cod_moneda" => "PEN",
    "tipo_comprobante_modifica" => "01",
    "nro_documento_modifica" => "F001-397508",
    "cod_tipo_motivo" => "01",
    "descripcion_motivo" => "ANULACION DE LA OPERACION",
    //Datos del cliente
    "cliente_numerodocumento" => "20100066603",
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
            "txtITEM" => 1,
            "txtUNIDAD_MEDIDA_DET" => "NIU",
            "txtCANTIDAD_DET" => "1",
            "txtPRECIO_DET" => "10",
            "txtSUB_TOTAL_DET" => "8.47",
            "txtPRECIO_TIPO_CODIGO" => "01",
            "txtIGV" => "1.53",
            "txtISC" => "0",
            "txtIMPORTE_DET" => "8.47",
            "txtCOD_TIPO_OPERACION" => "10",
            "txtCODIGO_DET" => "PP001",
            "txtDESCRIPCION_DET" => "Nombre Producto 01",
            "txtPRECIO_SIN_IGV_DET" => 8.47
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

