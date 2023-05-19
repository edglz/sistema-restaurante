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
$pdf->Cell(72,4,'DELIVERY',0,1,'C');
$pdf->Ln(3);
$pdf->SetFont('Helvetica','',9);
$pdf->Cell(72,4,'ENTREGA: '.date('d-m-Y h:i A',strtotime($this->dato->fec_ven)),0,1,'L');

$pdf->MultiCell(72,4,'CLIENTE: '.utf8_decode($this->dato->Cliente->nombre),0,1,'');
if($this->dato->Cliente->tipo_cliente == 1){
$pdf->Cell(72,4,utf8_decode(Session::get('diAcr')).': '.utf8_decode($this->dato->Cliente->dni),0,1,'');
}else{
$pdf->Cell(72,4,utf8_decode(Session::get('tribAcr')).': '.utf8_decode($this->dato->Cliente->ruc),0,1,'');
}
$pdf->MultiCell(72,4,'TELEFONO: '.utf8_decode($this->dato->Cliente->telefono),0,1,'');
$pdf->MultiCell(72,4,'DIRECCION: '.utf8_decode($this->dato->Cliente->direccion),0,1,'');
$pdf->MultiCell(72,4,'REFERENCIA: '.utf8_decode($this->dato->Cliente->referencia),0,1,'');


// COLUMNAS
$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Cell(42, 10, 'PRODUCTO', 0);
$pdf->Cell(5, 10, 'CANT.',0,0,'R');
$pdf->Cell(10, 10, 'P.U.',0,0,'R');
$pdf->Cell(15, 10, 'IMP.',0,0,'R');
$pdf->Ln(8);
$pdf->Cell(72,0,'','T');
$pdf->Ln(1);

$total = 0;
foreach($this->dato->Detalle as $d){
$pdf->SetFont('Helvetica', '', 9);
$pdf->MultiCell(42,4,utf8_decode($d->Producto->pro_nom).' '.utf8_decode($d->Producto->pro_pre),0,'L'); 
$pdf->Cell(47, -4, $d->cantidad,0,0,'R');
$pdf->Cell(10, -4, $d->precio,0,0,'R');
$pdf->Cell(15, -4, number_format(($d->cantidad * $d->precio),2),0,0,'R');
$pdf->Ln(1);
$total = ($d->cantidad * $d->precio) + $total;
}


if($this->dato->id_tpag == 1){
	$pdf->Ln(0);
	$pdf->Cell(37, 10, 'EFECTIVO', 0);    
	$pdf->Cell(20, 10, '', 0);
	$pdf->Cell(15, 10, number_format(($this->dato->pago_efe),2),0,0,'R');
	} else if($this->dato->id_tpag == 2){
	$pdf->Ln(0);
	$pdf->Cell(37, 10, 'TARJETA', 0);    
	$pdf->Cell(20, 10, '', 0);
	$pdf->Cell(15, 10, number_format(($this->dato->pago_tar),2),0,0,'R');
	} else if($this->dato->id_tpag == 3){
	$pdf->Ln(0);
	$pdf->Cell(37, 10, 'EFECTIVO', 0);    
	$pdf->Cell(20, 10, '', 0);
	$pdf->Cell(15, 10, number_format(($this->dato->pago_efe),2),0,0,'R');
	$pdf->Ln(4);
	$pdf->Cell(37, 10, 'TARJETA', 0);    
	$pdf->Cell(20, 10, '', 0);
	$pdf->Cell(15, 10, number_format(($this->dato->pago_tar),2),0,0,'R');
	}
	if($this->dato->id_tpag == 1 OR $this->dato->id_tpag == 3){
	$pdf->Ln(8);
	$pdf->Cell(72,0,'','T');
	$pdf->Ln(0);
	$pdf->Cell(37, 10, 'PAGO CON', 0);    
	$pdf->Cell(20, 10, '', 0);
	$pdf->Cell(15, 10, number_format(($this->dato->pago_efe_none),2),0,0,'R');
	$pdf->Ln(4);
	$pdf->Cell(37, 10, 'VUELTO', 0);    
	$pdf->Cell(20, 10, '', 0);
	$vuelto = ($this->dato->pago_efe_none - $this->dato->pago_efe);
	$pdf->Cell(15, 10, strtoupper(number_format(($vuelto),2)),0,0,'R');
	} 
	
	if($this->dato->id_tpag > 3) {
	$pdf->Ln(0);
	$pdf->Cell(37, 10, 'PAGO CON', 0);    
	$pdf->Cell(20, 10, '', 0);
	$pdf->Cell(15, 10, $this->dato->desc_tp,0,0,'R');
	}
	$pdf->Ln(10);


	$pdf->SetFont('Helvetica', 'B', 10);
	$pdf->Cell(72,0,'','T');
	$pdf->Ln(1);
	$pdf->Cell(37, 10, 'DELIVERY', 0);    
	$pdf->Cell(20, 10, '', 0);
	// $pdf->Cell(15, 10, number_format(($total-),2),0,0,'R');
	$pdf->Ln(8);



	$pdf->Ln(10);

	$pdf->Output('ticket.pdf','i');
?>