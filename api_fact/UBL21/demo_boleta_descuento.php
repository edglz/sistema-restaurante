<?php

$ruta = "https://api.beholia.com/UBL21/ws/boleta.php";

$data = array( 
    "tipo_proceso" => 3,
    //Cabecera del documento
    "pass_firma" => '123456',
    "tipo_operacion" => "0101",
    "total_gravadas" => "29.66",
    "total_inafecta" => "0",
    "total_exoneradas" => "0",
    "total_gratuitas" => "0",
    "total_exportacion" => "0",
    "total_descuento" => "10",
    "sub_total" => "29.66",
    "porcentaje_igv" => "18.00",
    "total_igv" => "5.34",
    "total_isc" => "0",
    "total_otr_imp" => "0",
    "total" => "35",
    "total_letras" => "SON CIEN",
    "nro_guia_remision" => "",
    "cod_guia_remision" => "",
    "nro_otr_comprobante" => "",
    "serie_comprobante" => "B001", //Para boletas la serie debe comenzar por la letra B, seguido de tres dígitos
    "numero_comprobante" => (string) generar_numero_aleatorio(6),
    "fecha_comprobante" => date('Y-m-d'),
    "fecha_vto_comprobante" => date('Y-m-d'),
    "cod_tipo_documento" => "03",
    "cod_moneda" => "PEN",
    //Datos del cliente
    "cliente_numerodocumento" => "43358966",
    "cliente_nombre" => "Juan Perez Aguilar",
    "cliente_tipodocumento" => "1", //1: DNI
    "cliente_direccion" => "CAL.LOS PLATEROS NRO. 229",
    "cliente_pais" => "PE",
    "cliente_ciudad" => "Lima",
    "cliente_codigoubigeo" => "",
    "cliente_departamento" => "",
    "cliente_provincia" => "",
    "cliente_distrito" => "",
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
    //items del documento
    "detalle" => array(
        array(
            "txtITEM" => 1,
            "txtUNIDAD_MEDIDA_DET" => "NIU",
            "txtCANTIDAD_DET" => "1",
            "txtPRECIO_DET" => "10",
            "txtSUB_TOTAL_DET" => "6.59",
            "txtPRECIO_TIPO_CODIGO" => "01",
            "txtIGV" => "1.18",
            "txtISC" => "0",
            "txtIMPORTE_DET" => "6.59",
            "txtCOD_TIPO_OPERACION" => "10",
            "txtCODIGO_DET" => "P001",
            "txtDESCRIPCION_DET" => "Producto 01",
            "txtPRECIO_SIN_IGV_DET" => 6.59
        ),
        array(
            "txtITEM" => 2,
            "txtUNIDAD_MEDIDA_DET" => "NIU",
            "txtCANTIDAD_DET" => "1",
            "txtPRECIO_DET" => "15",
            "txtSUB_TOTAL_DET" => "9.88",
            "txtPRECIO_TIPO_CODIGO" => "01",
            "txtIGV" => "1.77",
            "txtISC" => "0",
            "txtIMPORTE_DET" => "9.88",
            "txtCOD_TIPO_OPERACION" => "10",
            "txtCODIGO_DET" => "P002",
            "txtDESCRIPCION_DET" => "Producto 02",
            "txtPRECIO_SIN_IGV_DET" => 9.88
        ),
        array(
            "txtITEM" => 3,
            "txtUNIDAD_MEDIDA_DET" => "NIU",
            "txtCANTIDAD_DET" => "1",
            "txtPRECIO_DET" => "20",
            "txtSUB_TOTAL_DET" => "13.18",
            "txtPRECIO_TIPO_CODIGO" => "01",
            "txtIGV" => "2.37",
            "txtISC" => "0",
            "txtIMPORTE_DET" => "13.18",
            "txtCOD_TIPO_OPERACION" => "10",
            "txtCODIGO_DET" => "DSDFG",
            "txtDESCRIPCION_DET" => "Producto 03",
            "txtPRECIO_SIN_IGV_DET" => 13.18
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
