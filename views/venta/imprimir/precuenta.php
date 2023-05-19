<?php
require_once ('public/lib/print/num_letras.php');
require_once ('public/lib/pdf/cellfit.php');

class FPDF_CellFiti extends FPDF_CellFit
{
	function AutoPrint($dialog=false)
	{
		//Open the print dialog or start printing immediately on the standard printer
		$param=($dialog ? 'true' : 'false');
		$script="print($param);";
		$this->IncludeJS($script);
	}

	function AutoPrintToPrinter($server, $printer, $dialog=false)
	{
		//Print on a shared printer (requires at least Acrobat 6)
		$script = "var pp = getPrintParams();";
		if($dialog)
			$script .= "pp.interactive = pp.constants.interactionLevel.full;";
		else
			$script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
		$script .= "pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";
		$script .= "print(pp);";
		$this->IncludeJS($script);
	}
}

define('EURO',chr(128));
$pdf = new FPDF_CellFiti('P','mm',array(80,200));
$pdf->AddPage();
$pdf->SetMargins(0,0,0,0);
 
// CABECERA
$pdf->SetFont('Helvetica','',6);
$pdf->Cell(72,4,'',0,1,'C');
$pdf->SetFont('Helvetica','',13);
$pdf->Cell(72,4,'PRE-CUENTA',0,1,'C');
$pdf->Ln(3);
$pdf->SetFont('Helvetica','',9);
$pdf->Cell(72,4,'FECHA: '.date('d-m-Y h:i A',strtotime($this->dato->fecha_pedido)),0,1,'L');
$pdf->Cell(72,4,'SALON: '.utf8_decode($this->dato->desc_salon),0,1,'L');
$pdf->Cell(72,4,'MESA: '.utf8_decode($this->dato->nro_mesa),0,1,'L');
 
// COLUMNAS
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(42, 10, 'PRODUCTO', 0);
$pdf->Cell(5, 10, 'CANT.',0,0,'R');
$pdf->Cell(10, 10, 'P.U.',0,0,'R');
$pdf->Cell(15, 10, 'IMP.',0,0,'R');
$pdf->Ln(8);
$pdf->Cell(72,0,'','T');
$pdf->Ln(1);
// PRODUCTOS
$total = 0;

if(isset($_GET['data_selected'])){
	try{
		//ACCEDIENDO AL VECTOR
		$arrayPam = $_GET['data_selected'];
		$arrayPam = json_decode($arrayPam, true);
		$limit = count($arrayPam);
		 /*for($i = 0; $i < $limit; $i++){
			 echo 'ID PRODUCTO : '. $arrayPam[$i]["id_p"] . ' -  SELECCIONADOS: ' . $arrayPam[$i]["i_selected"] . '<br>';
		 }*/
		 //
		
	}catch(Exception $e){
		echo "Error: " . $e->getMessage();
	}
}
$fx= 0;
foreach($this->dato->Detalle as $d){	
	if($arrayPam){	
			for($x = 0; $x < $limit; $x++){
				if($d->id_pres == $arrayPam[$x]["id_p"]){
						$pdf->SetFont('Helvetica', '', 9);
						$pdf->MultiCell(42,4,utf8_decode($d->Producto->pro_nom).' '.utf8_decode($d->Producto->pro_pre),0,'L'); 
						$pdf->Cell(47, -4, $arrayPam[$x]["i_selected"],0,0,'R');
						$pdf->Cell(10, -4, $d->precio,0,0,'R');
						$pdf->Cell(15, -4, number_format(($arrayPam[$x]["i_selected"] * $d->precio),2),0,0,'R');
						$pdf->Ln(1);
						$total = ($arrayPam[$x]["i_selected"] * $d->precio) + $total;
				}
			}
	}else{
		$pdf->SetFont('Helvetica', '', 9);
		$pdf->MultiCell(42,4,utf8_decode($d->Producto->pro_nom).' '.utf8_decode($d->Producto->pro_pre),0,'L'); 
		$pdf->Cell(47, -4, $d->cantidad,0,0,'R');
		$pdf->Cell(10, -4, $d->precio,0,0,'R');
		$pdf->Cell(15, -4, number_format(($d->cantidad * $d->precio),2),0,0,'R');
		$pdf->Ln(1);
		$total = ($d->cantidad * $d->precio) + $total;
	}
	$fx++;
}
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(72,0,'','T');
$pdf->Ln(1);
$pdf->Cell(37, 10, 'TOTAL', 0);    
$pdf->Cell(20, 10, '', 0);
$pdf->Cell(15, 10, number_format(($total),2),0,0,'R');
// PIE DE PAGINA
$pdf->Ln(10);
$pdf->Output('ticket.pdf','i');
?>