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
$pdf = new FPDF_CellFiti('P','mm',array(80,700));
$pdf->AddPage();
$pdf->SetMargins(0,0,0,0);
 
// DATOS ARQUEO DE CAJA
if($this->dato->estado == 'a'){$estado = 'ABIERTO';}else{$estado = 'CERRADO';}
$pdf->Ln(3);
$pdf->SetFont('Helvetica','',10);
$pdf->Cell(72,4,'ARQUEO DE CAJA',0,1,'C');
$pdf->Cell(72,4,'COD0'.$this->dato->id_apc,0,1,'C'); 
$pdf->SetFont('Helvetica','',8); 
$pdf->Cell(72,4,'ESTADO: '.$estado,0,1,'C');       
$pdf->Ln(3);
$pdf->Cell(72,4,'CAJERO: '.utf8_decode($this->dato->desc_per),0,1,'');
$pdf->Cell(72,4,'CAJA: '.utf8_decode($this->dato->desc_caja),0,1,'');
$pdf->Cell(72,4,'TURNO: '.utf8_decode($this->dato->desc_turno),0,1,'');
$pdf->Cell(72,4,'FECHA APERTURA: '.date('d-m-Y h:i A',strtotime($this->dato->fecha_aper)),0,1,'');
if($this->dato->estado == 'a'){$fecha_cierre = '';}else{$fecha_cierre = date('d-m-Y h:i A',strtotime($this->dato->fecha_cierre));}
$pdf->Cell(72,4,'FECHA CIERRE: '.$fecha_cierre,0,1,'');
$pdf->Ln(1);
$pdf->Cell(72,0,'','T');
$pdf->Ln(0);
$pdf->Cell(37, 10, 'A. APERTURA DE CAJA:', 0);    
$pdf->Cell(20, 10, '', 0);
$pdf->Cell(15, 10, number_format(($this->dato->monto_aper),2),0,0,'R');
$pdf->Ln(5);
$pdf->Cell(37, 10, 'B. INGRESOS:', 0);    
$pdf->Cell(20, 10, '', 0);
$totalIng = $this->dato->Principal->total + $this->dato->Ingresos->total;
$pdf->Cell(15, 10, number_format($totalIng,2),0,0,'R');
$pdf->Ln(5);
$pdf->Cell(30, 10, '  Efectivo', 0);    
$pdf->Cell(20, 10, '', 0);
$pdf->Cell(15, 10, number_format(($this->dato->Principal->pago_efe),2),0,0,'R');
$pdf->Ln(5);
$pdf->Cell(30, 10, '  Tarjeta', 0);    
$pdf->Cell(20, 10, '', 0);
$pdf->Cell(15, 10, number_format(($this->dato->Principal->pago_tar),2),0,0,'R');
$pdf->Ln(5);
$pdf->Cell(30, 10, '  En caja', 0);    
$pdf->Cell(20, 10, '', 0);
$pdf->Cell(15, 10, number_format(($this->dato->Ingresos->total),2),0,0,'R');
$pdf->Ln(5);
$pdf->Cell(37, 10, 'C. EGRESOS:', 0);    
$pdf->Cell(20, 10, '', 0);
$totalEgr = $this->dato->EgresosA->total + $this->dato->EgresosB->total;
$pdf->Cell(15, 10, number_format($totalEgr,2),0,0,'R');
$pdf->Ln(5);
$pdf->Cell(30, 10, '  En caja', 0);    
$pdf->Cell(20, 10, '', 0);
$pdf->Cell(15, 10, number_format(($this->dato->EgresosA->total),2),0,0,'R');
$pdf->Ln(5);
$pdf->Cell(30, 10, '  Credito compras', 0);    
$pdf->Cell(20, 10, '', 0);
$pdf->Cell(15, 10, number_format(($this->dato->EgresosB->total),2),0,0,'R');
$pdf->Ln(10);
$pdf->Cell(72,0,'','T');
$pdf->Ln(0);
$pdf->Cell(37, 10, 'D. MONTO TOTAL:', 0);    
$pdf->Cell(20, 10, '', 0);
$montoEstimado = $this->dato->monto_aper + $totalIng - $totalEgr;
$pdf->Cell(15, 10, number_format($montoEstimado,2),0,0,'R');
$pdf->Ln(10);
$pdf->Cell(72,0,'','T');
$pdf->Ln(0);
$pdf->Cell(37, 10, 'E. MONTO DE CIERRE:', 0);  
$pdf->Ln(5);
$pdf->Cell(37, 10, '  EFECTIVO SISTEMA:', 0);    
$pdf->Cell(20, 10, '', 0);
$pdf->Cell(15, 10, number_format($montoEstimado - $this->dato->Principal->pago_tar,2),0,0,'R');
$pdf->Ln(5);
$pdf->Cell(37, 10, '  EFECTIVO CIERRE:', 0);    
$pdf->Cell(20, 10, '', 0);
$pdf->Cell(15, 10, number_format($this->dato->monto_cierre,2),0,0,'R');
$pdf->Ln(5);
$pdf->Cell(37, 10, '  DIFERENCIA:', 0);    
$pdf->Cell(20, 10, '', 0);
$montoDiferencia = $montoEstimado - $this->dato->monto_cierre;
$pdf->Cell(15, 10, number_format($montoDiferencia - $this->dato->Principal->pago_tar,2),0,0,'R');
$pdf->Ln(10);
$pdf->Cell(72,0,'','T');
$pdf->Ln(0);
$pdf->Cell(72, 10, 'F. OPERACIONES:', 0);
$pdf->Ln(5); 
$pdf->Cell(32, 10, 'CONCEPTO', 0);
$pdf->Cell(15, 10, 'OPER.',0,0,'R');
$pdf->Cell(25, 10, 'TOTAL',0,0,'R');
/*
$pdf->Ln(5); 
$pdf->Cell(32, 10, ' Deliverys APP', 0);
$pdf->Cell(15, 10, $this->dato->Deliverys->cant,0,0,'R');
$pdf->Cell(25, 10, number_format(($this->dato->Deliverys->total),2),0,0,'R');
*/
$pdf->Ln(5); 
$pdf->Cell(32, 10, ' Descuentos', 0);
$pdf->Cell(15, 10, $this->dato->Descuentos->cant,0,0,'R');
$pdf->Cell(25, 10, number_format(($this->dato->Principal->descu),2),0,0,'R');
$pdf->Ln(5); 
$pdf->Cell(32, 10, ' Comision delivery', 0);
$pdf->Cell(15, 10, $this->dato->ComisionDelivery->cant,0,0,'R');
$pdf->Cell(25, 10, number_format(($this->dato->Principal->comis_del),2),0,0,'R');
$pdf->Ln(5); 
$pdf->Cell(32, 10, ' Anulaciones ventas', 0);
$pdf->Cell(15, 10, $this->dato->Anulaciones->cant,0,0,'R');
$pdf->Cell(25, 10, number_format(($this->dato->Anulaciones->total),2),0,0,'R');
$pdf->Ln(8);
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

// POLLOS VENDIDOS
if(Session::get('opc_02') == 2) {
$pollos_vendidos = 0;
foreach($this->dato->PollosVendidos as $d){
	$pollos_vendidos += $d->cantidad * $d->cant;
}
$pdf->Cell(72,0,'','T');
$pdf->Ln(2); 
$pdf->Cell(72,4,'POLLOS VENDIDOS: '.$pollos_vendidos,0,1,''); 
$pdf->Cell(72,4,'POLLOS STOCK: '.$this->dato->stock_pollo,0,1,'');
}
$pdf->Cell(72,0,'','T');
$pdf->Ln(2); 
$pdf->Cell(72,4,'DATOS DE IMPRESION',0,1,'');
$pdf->Cell(72,4,'USUARIO: '.Session::get('nombres').' '.Session::get('apellidos'),0,1,'');
date_default_timezone_set($_SESSION["zona_horaria"]);
setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
$pdf->Cell(72,4,'FECHA: '.date("d-m-Y h:i A"),0,1,'');
$pdf->Ln(8);
$pdf->Cell(72,4,'___________________________________',0,1,'C');
$pdf->Cell(72,4,utf8_decode($this->dato->desc_per),0,1,'C');
// PIE DE PAGINA
$pdf->Ln(10);
$pdf->Output('ticket.pdf','i');
?>