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

if($this->dato->id_tdoc == 1 || $this->dato->id_tdoc == 2){

	$pdf = new FPDF_CellFiti('P', 'mm', 'A4');
	$pdf->AddPage();
	$pdf->SetMargins(-20,-20,-20);
	//$pdf->AddFont('LucidaConsole','','lucidaconsole.php');
	$pdf->SetFont('Arial','',10);
	//DETALLE DE LA EMPRESA
	$pdf->SetXY(18, 45);
	$pdf->Cell(72,3,utf8_decode($this->dato->Cliente->nombre),0,1,'L');
	$pdf->SetXY(18, 50);
	$pdf->Cell(72,3,utf8_decode($this->dato->Cliente->direccion),0,1,'L');
	$pdf->SetXY(18, 55);
	$pdf->Cell(72,3,utf8_decode($this->dato->Cliente->dni.''.$this->dato->Cliente->ruc),0,1,'L');
	$x=42;
	$pdf->SetXY(50, $x+13);//modificar solo esto
	$pdf->CellFitScale(20, 3,utf8_decode($this->dato->Cliente->telefono), 0, 1, 'L');
	$pdf->SetXY(71, $x+13);//modificar solo esto
	$pdf->CellFitScale(100, 3,utf8_decode(date('d',strtotime($this->dato->fec_ven))), 0, 1, 'L');
	$pdf->SetXY(78, $x+13);//modificar solo esto
	$pdf->CellFitScale(100, 3,utf8_decode(date('m',strtotime($this->dato->fec_ven))), 0, 1, 'L');
	$pdf->SetXY(86, $x+13);//modificar solo esto
	$pdf->CellFitScale(100, 3,utf8_decode(date('Y',strtotime($this->dato->fec_ven))), 0, 1, 'L');
	$y=23;
	$pdf->SetFont('Arial','',9);
	foreach($this->dato->Detalle as $d){
		$pdf->SetXY(8, $x+$y);//modificar solo esto
		$pdf->CellFitScale(20, 3,$d->cantidad, 0, 1, 'L');
		$pdf->SetXY(16, $x+$y);//modificar solo esto
		$pdf->CellFitScale(200, 3,substr((utf8_decode($d->Producto->pro_nom).' '.utf8_decode($d->Producto->pro_pre)),0,25), 0, 1, 'L');
		$pdf->SetXY(71, $x+$y);//modificar solo esto
		$pdf->CellFitScale(20, 3,$d->precio, 0, 1, 'L');
		$pdf->SetXY(84, $x+$y);//modificar solo esto
		$pdf->CellFitScale(20, 3,number_format(($d->cantidad * $d->precio),2), 0, 1, 'L');
		$y = $y + 5;
	}
	$pdf->SetFont('Arial','',10);
	$pdf->SetXY(0, 110);//modificar solo esto
	$pdf->CellFitScale(95, 3,'SUB TOTAL: '.number_format(($this->dato->total - $this->dato->descu),2), 0, 1, 'R');
	$pdf->SetXY(0, 115);//modificar solo esto
	$pdf->CellFitScale(95, 3,'COMISION TARJETA: '.number_format($this->dato->comis,2), 0, 1, 'R');
	$pdf->SetXY(75, 120);//modificar solo esto
	$pdf->CellFitScale(20, 3,number_format(($this->dato->total + $this->dato->comis - $this->dato->descu),2), 0, 1, 'R');
	$pdf->AutoPrint(true);
	$pdf->Output();

} else {

	define('EURO',chr(128));
	$pdf = new FPDF_CellFiti('P','mm',array(80,200));
	$pdf->AddPage();
	$pdf->SetMargins(0,0,0,0);
	 
	// CABECERA
	$pdf->SetFont('Helvetica','',6);
	$pdf->Cell(72,4,'',0,1,'C');
	$pdf->SetFont('Helvetica','',12);
	$pdf->Cell(72,4,utf8_decode($this->empresa['raz_soc']),0,1,'C');
	$pdf->SetFont('Helvetica','',8);
	$pdf->Cell(72,4,utf8_decode(Session::get('tribAcr')).': '.utf8_decode($this->empresa['ruc']),0,1,'C');
	$pdf->MultiCell(72,4,utf8_decode($this->empresa['direccion']),0,'C');
	$pdf->Cell(72,4,'TELF: '.utf8_decode($this->empresa['celular']),0,1,'C');
	 
	// DATOS FACTURA
	$elec = (($this->dato->id_tdoc == 1 || $this->dato->id_tdoc == 2) && Session::get('sunat') == 1) ? 'ELECTRONICA' : '';     
	$pdf->Ln(3);
	$pdf->SetFont('Helvetica', 'B', 9);
	$pdf->Cell(72,4,utf8_decode($this->dato->desc_td).' '.$elec,0,1,'C');
	$pdf->Cell(72,4,utf8_decode($this->dato->ser_doc).'-'.utf8_decode($this->dato->nro_doc),0,1,'C');
	$pdf->Ln(2);
	$pdf->SetFont('Helvetica', '', 8);
	$pdf->Cell(72,4,'FECHA DE EMISION: '.date('d-m-Y h:i A',strtotime($this->dato->fec_ven)),0,1,'');
	if(isset($this->dato->Pedido->desc_salon)){
		$pdf->Cell(72,4,utf8_decode('TIPO DE ATENCION').': '.utf8_decode($this->dato->Pedido->desc_salon).' - MESA: '.utf8_decode($this->dato->Pedido->nro_mesa),0,1,'');
	}else{
		$pdf->Cell(72,4,'TIPO DE ATENCION: Mostrador / Delivery',0,1,'');
	}
	$pdf->MultiCell(72,4,'CLIENTE: '.utf8_decode($this->dato->Cliente->nombre),0,1,'');
	$pdf->Cell(72,4,utf8_decode(Session::get('diAcr')).'/'.utf8_decode(Session::get('tribAcr')).': '.utf8_decode($this->dato->Cliente->dni.''.$this->dato->Cliente->ruc),0,1,'');
	$pdf->MultiCell(72,4,'DIRECCION: '.utf8_decode($this->dato->Cliente->direccion),0,1,'');
	 
	// COLUMNAS
	$pdf->SetFont('Helvetica', 'B', 8);
	$pdf->Cell(42, 10, 'PRODUCTO', 0);
	$pdf->Cell(5, 10, 'CANT.',0,0,'R');
	$pdf->Cell(10, 10, 'P.U.',0,0,'R');
	$pdf->Cell(15, 10, 'IMP.',0,0,'R');
	$pdf->Ln(8);
	$pdf->Cell(72,0,'','T');
	$pdf->Ln(1);
	 
	// PRODUCTOS
	foreach($this->dato->Detalle as $d){
	$pdf->SetFont('Helvetica', '', 8);
	$pdf->MultiCell(42,4,utf8_decode($d->Producto->pro_nom).' '.utf8_decode($d->Producto->pro_pre),0,'L'); 
	$pdf->Cell(47, -4, $d->cantidad,0,0,'R');
	$pdf->Cell(10, -4, $d->precio,0,0,'R');
	$pdf->Cell(15, -4, number_format(($d->cantidad * $d->precio),2),0,0,'R');
	$pdf->Ln(1);
	}
	 
	// SUMATORIO DE LOS PRODUCTOS Y EL IVA
	$sbt = (($this->dato->total + $this->dato->comis - $this->dato->descu) / (1 + $this->dato->igv));
	$igv = ($sbt * $this->dato->igv);
	$pdf->SetFont('Helvetica', '', 8);
	$pdf->Cell(72,0,'','T');
	$pdf->Ln(0);    
	$pdf->Cell(37, 10, 'SUB TOTAL', 0);    
	$pdf->Cell(20, 10, '', 0);
	$pdf->Cell(15, 10, number_format(($this->dato->total),2),0,0,'R');
	$pdf->Ln(4); 
	$pdf->Cell(37, 10, 'DESCUENTO', 0);    
	$pdf->Cell(20, 10, '', 0);
	$pdf->Cell(15, 10, number_format(($this->dato->descu),2),0,0,'R');
	$pdf->Ln(4);
	$pdf->Cell(37, 10, 'COMISION TARJETA', 0);    
	$pdf->Cell(20, 10, '', 0);
	$pdf->Cell(15, 10, number_format($this->dato->comis,2),0,0,'R');
	$pdf->Ln(5);    
	$pdf->Cell(37, 10, 'IMPORTE A PAGAR', 0);    
	$pdf->Cell(20, 10, '', 0);
	$pdf->Cell(15, 10, number_format(($this->dato->total - $this->dato->descu),2),0,0,'R');
	$pdf->Ln(4);    
	$pdf->Cell(72, 10, 'SON: '.numtoletras($this->dato->total - $this->dato->descu), 0);
	$pdf->Ln(10); 

	// PIE DE PAGINA
	$pdf->Ln(5);
	$pdf->Cell(72,0,'GRACIAS POR SU PREFERENCIA',0,1,'C');
	$pdf->Ln(10);
	$pdf->Output('ticket.pdf','i');

}

?>
