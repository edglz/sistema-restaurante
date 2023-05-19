<?php
date_default_timezone_set('America/Lima');
setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
$hora = date("g:i:s A");

require 'public/lib/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

$data = json_decode($_GET['data'],true);
$connector = new WindowsPrintConnector("smb://".gethostbyaddr($_SERVER['REMOTE_ADDR'])."/".$data['nombre_imp']);
$printer = new Printer($connector);

try {
  	$printer -> setJustification(Printer::JUSTIFY_CENTER);
	$printer -> setTextSize(1,1);
	if($data['cod_tped'] == 1){
		$printer -> text("MESA\n");
	}elseif($data['cod_tped'] == 2){
		$printer -> text("MOSTRADOR\n");
	}elseif($data['cod_tped'] == 3){
		$printer -> text("DELIVERY\n");
	}
	if($data['cod_tped'] == 1){
		$printer -> text("SALON: ".$data['desc_salon']."\n");
		$printer -> text("MESA: ".$data['nro_pedido']."\n");
	}elseif($data['cod_tped'] == 2 or $data['cod_tped'] == 3){
		$printer -> text("Nro de Pedido: ".$data['nro_pedido']."\n");
	}
	$printer -> selectPrintMode();
	$printer -> setJustification(Printer::JUSTIFY_LEFT);
	$printer -> text("HORA:	".$hora."\n");
	$printer -> text("CANT   PRODUCTO\n");
	foreach ($data['items'] as $value) {
		$printer -> text($value['cantidad']."      ".$value['producto']." | ".$value['presentacion']."\n");
		$printer -> text("    ".$value['comentario']."\n");
	}
	$printer -> cut();
	$printer -> close();

} catch(Exception $e) {
	echo "No se pudo imprimir en esta impresora " . $e -> getMessage() . "\n";
}
?>
echo "<script lenguaje="JavaScript">window.close();</script>";
