<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	include "validaciondedatos.php";
	include "procesarcomprobante.php";

	$validacion = new Validaciondedatos();
	$data = $_POST;
	$items_detalle = json_decode($data['datadetalle'], true);
	//var_dump($items_detalle);
	//exit();

	$ruc_emisor = "20100066603";
    $emisor['ruc'] = $ruc_emisor;
    $emisor['tipo_doc'] = "6";
    $emisor['nom_comercial'] = "ALEX COMERCIAL";
    $emisor['razon_social'] = 'ALEX S.A.';
    $emisor['codigo_ubigeo'] = "070104";
    $emisor['direccion'] = "Jr. Puno";
    $emisor['direccion_departamento'] = 'LIMA';
    $emisor['direccion_provincia'] = 'LIMA';
    $emisor['direccion_distrito'] = 'CHACLACAYO';
    $emisor['direccion_codigopais'] = 'PE';
    $emisor['usuariosol'] = 'MODDATOS';
    $emisor['clavesol'] = 'moddatos';
    $tipodeproceso = '3'; //3=beta,2=homologacion,1=produccion
    $emisor['tipoproceso'] = $tipodeproceso; //3=beta,2=homologacion,1=produccion

	//$url_base = $_SERVER["DOCUMENT_ROOT"].'archivos_xml_sunat/';
	$url_base = '../archivos_xml_sunat/';

    $content_folder_xml = 'cpe_xml/';
    $content_firmas = 'certificados/';
    $archivo = $ruc_emisor . '-' . $data['tipo_comprobante'] . '-' . $data['serie_comprobante'].'-'.$data['numero_comprobante'];


    if ($tipodeproceso == '1') {
        $ruta = $url_base . $content_folder_xml . 'produccion/' . $ruc_emisor . "/" . $archivo;
        $ruta_cdr = $url_base . $content_folder_xml . 'produccion/' . $ruc_emisor . "/";
        $ruta_firma = $url_base . $content_firmas . 'produccion/' . $ruc_emisor . '.pfx';
        $ruta_ws = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService';
    }
    if ($tipodeproceso == '2') {
        $ruta = $url_base . $content_folder_xml . 'homologacion/' . $ruc_emisor . "/" . $archivo;
        $ruta_cdr = $url_base . $content_folder_xml . 'homologacion/' . $ruc_emisor . "/";
        $ruta_firma = $url_base . $content_firmas . 'homologacion/' . $ruc_emisor . '.pfx';
        $ruta_ws = 'https://www.sunat.gob.pe/ol-ti-itcpgem-sqa/billService';
    }
    if ($tipodeproceso == '3') {
        $ruta = $url_base . $content_folder_xml . 'beta/' . $ruc_emisor . "/" . $archivo;
        $ruta_cdr = $url_base . $content_folder_xml . 'beta/' . $ruc_emisor . "/";
        if (file_exists('beta/' . $ruc_emisor . '.pfx')) {
            $ruta_firma = $url_base . $content_firmas.'/' . $ruc_emisor . '.pfx';
        } else {
            $ruta_firma = $url_base . $content_firmas.'beta/firmabeta.pfx';
            $pass_firma = '123456';
        }
        $ruta_ws = 'https://e-beta.sunat.gob.pe:443/ol-ti-itcpfegem-beta/billService';
    }
    
    $rutas = array();
    $rutas['nombre_archivo'] = $archivo;
    $rutas['ruta_xml'] = $ruta;
    $rutas['ruta_cdr'] = $ruta_cdr;
    $rutas['ruta_firma'] = $ruta_firma;
    $rutas['pass_firma'] = $pass_firma;
    $rutas['ruta_ws'] = $ruta_ws;

	$data_comprobante = crear_cabecera($emisor, $data);

	$procesarcomprobante = new Procesarcomprobante();

	$tipo_comprobante = $data['tipo_comprobante'];

	if($tipo_comprobante == "01") {
		$resp = $procesarcomprobante->procesar_factura($data_comprobante, $items_detalle, $rutas);
		//$resp['ruta_xml'] = 'archivos_xml_sunat/cpe_xml/beta/20100066603/'.$archivo.'.XML';
		$resp['ruta_cdr'] = 'archivos_xml_sunat/cpe_xml/beta/20100066603/R-'.$archivo.'.XML';
		$resp['ruta_pdf'] = 'controllers/prueba.php?tipo=factura&id=0';
		$resp['url_xml'] = '';
		$resp['data_comprobante'] = $data_comprobante;
		echo json_encode($resp);
		exit();
	}

	if ($tipo_comprobante == "03") {
		$resp = $procesarcomprobante->procesar_boleta($data_comprobante, $items_detalle, $rutas);
		//$resp['ruta_xml'] = 'archivos_xml_sunat/cpe_xml/beta/20100066603/'.$archivo.'.XML';
		$resp['ruta_cdr'] = 'archivos_xml_sunat/cpe_xml/beta/20100066603/R-'.$archivo.'.XML';
		$resp['ruta_pdf'] = 'controllers/prueba.php?tipo=boleta&id=0';
		$resp['url_xml'] = '';
		echo json_encode($resp);
		exit();
	}

	if ($tipo_comprobante == "07") {
		$resp = $procesarcomprobante->procesar_nota_de_credito($data_comprobante, $items_detalle, $rutas);
		//$resp['ruta_xml'] = 'archivos_xml_sunat/cpe_xml/beta/20100066603/'.$archivo.'.XML';
		$resp['ruta_cdr'] = 'archivos_xml_sunat/cpe_xml/beta/20100066603/R-'.$archivo.'.XML';
		$resp['ruta_pdf'] = 'controllers/prueba.php?tipo=notacredito&id=0';
		$resp['url_xml'] = '';
		echo json_encode($resp);
		exit();
	}

	if ($tipo_comprobante == "08") {
		$resp = $procesarcomprobante->procesar_nota_de_debito($data_comprobante, $items_detalle, $rutas);
		//$resp['ruta_xml'] = 'archivos_xml_sunat/cpe_xml/beta/20100066603/'.$archivo.'.XML';
		$resp['ruta_cdr'] = 'archivos_xml_sunat/cpe_xml/beta/20100066603/R-'.$archivo.'.XML';
		$resp['ruta_pdf'] = 'controllers/prueba.php?tipo=notadebito&id=0';
		$resp['url_xml'] = '';
		echo json_encode($resp);
		exit();
	}


	function crear_cabecera($emisor, $data) {

		$notadebito_descripcion['01'] = 'INTERES POR MORA';
		$notadebito_descripcion['02'] = 'AUMENTO EN EL VALOR';
		$notadebito_descripcion['03'] = 'PENALIDADES';

		$notacredito_descripcion['01'] = 'ANULACION DE LA OPERACION';
		$notacredito_descripcion['02'] = 'ANULACION POR ERROR EN EL RUC';
		$notacredito_descripcion['03'] = 'CORRECION POR ERROR EN LA DESCRIPCION';
		$notacredito_descripcion['04'] = 'DESCUENTO GLOBAL';
		$notacredito_descripcion['05'] = 'DESCUENTO POR ITEM';
		$notacredito_descripcion['06'] = 'DEVOLUCION TOTAL';
		$notacredito_descripcion['07'] = 'DEVOLUCION POR ITEM';
		$notacredito_descripcion['08'] = 'BONIFICACION';
		$notacredito_descripcion['09'] = 'DISMINUCION EN EL VALOR';


		if(isset($data['tipo_comprobante'])) {
			if($data['tipo_comprobante'] == '07') { //Nota de Crédito
				$codigo_motivo_modifica = $data['notacredito_motivo_id'];
				$descripcion_motivo_modifica = $notacredito_descripcion[$data['notacredito_motivo_id']];
			}else if($data['tipo_comprobante'] == '08') { //Nota de Débito
				$codigo_motivo_modifica = $data['notadebito_motivo_id'];
				$descripcion_motivo_modifica = $notadebito_descripcion[$data['notadebito_motivo_id']];
			} else {
				$codigo_motivo_modifica = "";
				$descripcion_motivo_modifica = "";
			}
		}

		//********** CAMPOS NUEVOS PARA UBL 2.1 */
		//http://cpe.sunat.gob.pe/sites/default/files/inline-images/Guia%2BXML%2BFactura%2Bversion%202-1%2B1%2B0%20%282%29.pdf
		/*
		'TIPO_OPERACION' => '0101', //pag. 28
		'FECHA_VTO' => $data['fecha_comprobante'], //pag. 31 //fecha de vencimiento
		'POR_IGV' => '18.00', //Porcentaje del impuesto
		'CONTACTO_EMPRESA' => "", 
		'TOTAL_EXPORTACION' => $data['TOTAL_EXPORTACION']
		'COD_UBIGEO_CLIENTE' => (isset($data['cliente_codigoubigeo'])) ? $data['cliente_codigoubigeo'] : "",
		'DEPARTAMENTO_CLIENTE' => (isset($data['cliente_departamento'])) ? $data['cliente_departamento'] : "",
		'PROVINCIA_CLIENTE' => (isset($data['cliente_provincia'])) ? $data['cliente_provincia'] : "",
		'DISTRITO_CLIENTE' => (isset($data['cliente_distrito'])) ? $data['cliente_distrito'] : "", 
		*/

		/*$data['txt_subtotal_comprobante'] = '100';
		$data['txt_total_comprobante'] = '100';
		$data['txt_igv_porcentaje'] = '18.00';
		$data['txt_igv_comprobante'] = '0';
		$data['txt_total_exoneradas'] = '100';
		$data['txt_total_letras'] = 'CIEN';*/
		
		//$data['txt_total_exoneradas'] = 13000;
		$cabecera = array(
			'TIPO_OPERACION' => '0101', //pag. 28
	        'TOTAL_GRAVADAS' => (isset($data['txt_subtotal_comprobante'])) ? $data['txt_subtotal_comprobante'] : "0",
	        'TOTAL_INAFECTA' => "0",
	        'TOTAL_EXONERADAS' => (isset($data['txt_total_exoneradas'])) ? $data['txt_total_exoneradas'] : "0",
	        'TOTAL_GRATUITAS' => "0",
	        'TOTAL_PERCEPCIONES' => "0",
	        'TOTAL_RETENCIONES' => "0",
	        'TOTAL_DETRACCIONES' => "0",
			'TOTAL_BONIFICACIONES' => "0",
			'TOTAL_EXPORTACION' => "0",
	        'TOTAL_DESCUENTO' => "0",
			'SUB_TOTAL' => (isset($data['txt_subtotal_comprobante'])) ? $data['txt_subtotal_comprobante'] : "0",
			'POR_IGV' => (isset($data['txt_igv_porcentaje'])) ? $data['txt_igv_porcentaje'] : "18.00", //Porcentaje del impuesto
	        'TOTAL_IGV' => (isset($data['txt_igv_comprobante'])) ? $data['txt_igv_comprobante'] : "0",
	        'TOTAL_ISC' => "0",
	        'TOTAL_OTR_IMP' => "0",
	        'TOTAL' => (isset($data['txt_total_comprobante'])) ? $data['txt_total_comprobante'] : "0",
	        'TOTAL_LETRAS' => $data['txt_total_letras'],
	        //==============================================
	        'NRO_GUIA_REMISION' => "",
	        'COD_GUIA_REMISION' => "",
	        'NRO_OTR_COMPROBANTE' => "",
	        'COD_OTR_COMPROBANTE' => "",
	        //==============================================
	        'TIPO_COMPROBANTE_MODIFICA' => (isset($data['tipo_comprobante_modificado'])) ? $data['tipo_comprobante_modificado'] : "",
	        'NRO_DOCUMENTO_MODIFICA' => (isset($data['num_comprobante_modificado'])) ? $data['num_comprobante_modificado'] : "",
	        'COD_TIPO_MOTIVO' => $codigo_motivo_modifica,
	        'DESCRIPCION_MOTIVO' => $descripcion_motivo_modifica,
	        //===============================================
	        'NRO_COMPROBANTE' => $data['serie_comprobante'].'-'.$data['numero_comprobante'],
			'FECHA_DOCUMENTO' => $data['fecha_comprobante'],
			'FECHA_VTO' => $data['fecha_comprobante'], //pag. 31 //fecha de vencimiento
	        'COD_TIPO_DOCUMENTO' => $data['tipo_comprobante'],
	        'COD_MONEDA' => $data['codmoneda_comprobante'],
	        //==================================================
	        'NRO_DOCUMENTO_CLIENTE' => $data['cliente_numerodocumento'],
			'RAZON_SOCIAL_CLIENTE' => $data['cliente_nombre'],
			'TIPO_DOCUMENTO_CLIENTE' => $data['cliente_tipodocumento'], //RUC
			'DIRECCION_CLIENTE' => $data['cliente_direccion'],

			'COD_UBIGEO_CLIENTE' => (isset($data['cliente_codigoubigeo'])) ? $data['cliente_codigoubigeo'] : "",
			'DEPARTAMENTO_CLIENTE' => (isset($data['cliente_departamento'])) ? $data['cliente_departamento'] : "",
			'PROVINCIA_CLIENTE' => (isset($data['cliente_provincia'])) ? $data['cliente_provincia'] : "",
			'DISTRITO_CLIENTE' => (isset($data['cliente_distrito'])) ? $data['cliente_distrito'] : "",
			
	        'CIUDAD_CLIENTE' => $data['cliente_ciudad'],
	        'COD_PAIS_CLIENTE' => $data['cliente_pais'],
	        //===============================================
			'NRO_DOCUMENTO_EMPRESA' => $emisor['ruc'],
			'TIPO_DOCUMENTO_EMPRESA' => $emisor['tipo_doc'], //RUC
			'NOMBRE_COMERCIAL_EMPRESA' => $emisor['nom_comercial'],
			'CODIGO_UBIGEO_EMPRESA' => $emisor['codigo_ubigeo'],
	        'DIRECCION_EMPRESA' => $emisor['direccion'],
	        'DEPARTAMENTO_EMPRESA' => $emisor['direccion_departamento'],
	        'PROVINCIA_EMPRESA' => $emisor['direccion_provincia'],
	        'DISTRITO_EMPRESA' => $emisor['direccion_distrito'],
			'CODIGO_PAIS_EMPRESA' => $emisor['direccion_codigopais'],
			'RAZON_SOCIAL_EMPRESA' => $emisor['razon_social'],
			'CONTACTO_EMPRESA' => "",
	        //====================INFORMACION PARA ANTICIPO=====================//
	        'FLG_ANTICIPO' => "0",
	        //====================REGULAR ANTICIPO=====================//
	        'FLG_REGU_ANTICIPO' => "0",
	        'NRO_COMPROBANTE_REF_ANT' => "",
	        'MONEDA_REGU_ANTICIPO' => "",
	        'MONTO_REGU_ANTICIPO' => "0",
	        'TIPO_DOCUMENTO_EMP_REGU_ANT' => "",
	        'NRO_DOCUMENTO_EMP_REGU_ANT' => "",
	        //===================CLAVES SOL EMISOR====================//
	        'EMISOR_RUC' => $emisor['ruc'],
	        'EMISOR_USUARIO_SOL' => $emisor['usuariosol'],
			'EMISOR_PASS_SOL' => $emisor['clavesol']
	    );

	    return $cabecera;
	}
?>