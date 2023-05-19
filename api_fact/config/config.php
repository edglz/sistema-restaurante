<?php 

define('ROOT_WS_SUNAT', URL.'api_fact/UBL21/ws/');
define('FAE_ENTORNO', Session::get('modo'));// 3: demo ...... 1: produccion
define('ROOT_UBL21', URL.'api_fact/UBL21/archivos_xml_sunat/cpe_xml/'.((FAE_ENTORNO == 1)?'produccion':'beta').'/'); // en prod cambiar /beta por /produccion/

?>