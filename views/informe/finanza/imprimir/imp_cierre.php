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
$pdf = new FPDF_CellFiti('P','mm',array(80,800));
$pdf->AddPage();
$pdf->SetMargins(0,0,0,0);
 
// DATOS ARQUEO DE CAJA
if($this->dato->estado == 'a'){$estado = 'ABIERTO';}else{$estado = 'CERRADO';}
$pdf->Ln(3);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(72,4,'ARQUEO DE CAJA',0,1,'C');
$pdf->Cell(72,4,'CORTE DE TURNO #COD0'.$this->dato->id_apc,0,1,'C'); 
$pdf->SetFont('Courier','',8); 
$pdf->Cell(72,4,'ESTADO: '.$estado,0,1,'C');       

$pdf->Ln(3);
$pdf->SetFont('Courier','B',9);
$pdf->Cell(15, 4, 'CAJERO:', 0);    
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(37, 4, utf8_decode($this->dato->desc_per),0,1,'R');
$pdf->Cell(15, 4, 'CAJA:', 0);    
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(37, 4, utf8_decode($this->dato->desc_caja),0,1,'R');
$pdf->Cell(15, 4, 'TURNO:', 0);    
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(37, 4, utf8_decode($this->dato->desc_turno),0,1,'R');
if($this->dato->estado == 'a'){$fecha_cierre = '';}else{$fecha_cierre = date('d-m-Y h:i A',strtotime($this->dato->fecha_cierre));}
$pdf->Cell(15, 4, 'FECHA APERTURA:', 0);    
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(37, 4, date('d-m-Y h:i A',strtotime($this->dato->fecha_aper)),0,1,'R');
$pdf->Cell(15, 4, 'FECHA CIERRE:', 0);    
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(37, 4, $fecha_cierre,0,1,'R');
$pdf->Ln(3);

//DINERO EN CAJA
$pdf->SetFont('Courier','B',10);
$pdf->Cell(72,4,'== DINERO EN CAJA ==',0,1,'C');
$pdf->Ln(4);
$pdf->SetFont('Courier','B',9);
$pdf->Cell(37, 4, 'APERTURA DE CAJA:', 0);    
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(15, 4, number_format(($this->dato->monto_aper),2),0,0,'R');
$pdf->Ln(4);
$pdf->Cell(37, 4, 'VENTAS EN EFECTIVO:', 0);    
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(15, 4, '+ '.number_format(($this->dato->Principal->pago_efe),2),0,0,'R');
$pdf->Ln(4);
$pdf->Cell(37, 4, 'ENTRADAS EN EFECTIVO:', 0);    
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(15, 4, '+ '.number_format(($this->dato->Ingresos->total),2),0,0,'R');
$pdf->Ln(4);
$pdf->Cell(37, 4, 'SALIDAS EN EFECTIVO:', 0);    
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(15, 4, '- '.number_format(($this->dato->Egresos->total),2),0,0,'R');
$pdf->Ln(5);
$pdf->Cell(72,0,'','T');
$pdf->Ln(1);
$pdf->Cell(37, 4, 'EFECTIVO EN CAJA:', 0);    
$pdf->Cell(20, 4, '', 0);
$efectivoencaja = $this->dato->monto_aper + $this->dato->Principal->pago_efe + $this->dato->Ingresos->total - $this->dato->Egresos->total;
$pdf->Cell(15, 4, '= '.number_format(($efectivoencaja),2),0,0,'R');
$pdf->Ln(4);
$pdf->Cell(37, 4, 'EFECTIVO EN CIERRE:', 0);    
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(15, 4, ''.number_format(($this->dato->monto_cierre),2),0,0,'R');
$pdf->Ln(5);
$pdf->Cell(72,0,'','T');
$pdf->Ln(1);
$pdf->Cell(37, 4, '', 0);    
$pdf->Cell(20, 4, '', 0);
$efectivodiferencia = $efectivoencaja - $this->dato->monto_cierre;
$nombre_efectivodiferencia = ($efectivodiferencia > 0) ? 'Faltante' : 'Restante';
$pdf->Cell(15, 4, $nombre_efectivodiferencia.' = '.number_format(($efectivodiferencia),2),0,0,'R');

//ENTRADAS
$pdf->Ln(8);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(72,4,'== ENTRADAS EFECTIVO ==',0,1,'C');
$pdf->Ln(4);
$pdf->SetFont('Courier','B',9);
$pdf->Cell(37, 4, 'ENTRADA DE DINERO', 0);    
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(15, 4, number_format(($this->dato->Ingresos->total),2),0,0,'R');
$pdf->Ln(4);
$pdf->Cell(72,0,'','T');
$pdf->Ln(1);
$pdf->Cell(37, 4, 'TOTAL ENTRADAS', 0);    
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(15, 4, '= '.number_format(($this->dato->Ingresos->total),2),0,0,'R');

//SALIDAS
$pdf->Ln(8);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(72,4,'== SALIDAS EFECTIVO ==',0,1,'C');
$pdf->Ln(4);
$pdf->SetFont('Courier','B',9);
$pdf->Cell(37, 4, 'COMPRAS', 0);   
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(15, 4, number_format(($this->dato->EgresosA->total),2),0,0,'R');
$pdf->Ln(4);
$pdf->Cell(37, 4, 'SERVICIOS', 0);   
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(15, 4, number_format(($this->dato->EgresosB->total),2),0,0,'R');
$pdf->Ln(4);
$pdf->Cell(37, 4, 'REMUNERACIONES', 0);   
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(15, 4, number_format(($this->dato->EgresosC->total),2),0,0,'R');
$pdf->Ln(4);
$pdf->Cell(37, 4, 'PAGOS A PROVEEDORES', 0);   
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(15, 4, number_format(($this->dato->EgresosD->total),2),0,0,'R');
$pdf->Ln(4);
$pdf->Cell(72,0,'','T');
$pdf->Ln(1);
$pdf->Cell(37, 4, 'TOTAL SALIDAS', 0);    
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(15, 4, '= '.number_format(($this->dato->Egresos->total),2),0,0,'R');

//VENTAS
$pdf->Ln(8);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(72,4,'== VENTAS ==',0,1,'C');
$pdf->Ln(4);
$pdf->SetFont('Courier','B',9);

$pdf->Cell(32, 4, '', 0);
$pdf->Cell(15, 4, 'OPER.',0,0,'R');
$pdf->Cell(25, 4, 'TOTAL',0,0,'R');
$pdf->Ln(4);

if($this->dato->Credito->total > 0){
$pdf->Cell(32, 4, 'AL CREDITO', 0);   
$pdf->Cell(20, 4, '', 0);
$pdf->Cell(15, 4, number_format(($this->dato->Credito->total),2),0,0,'R');
$pdf->Ln(4);
}

$pdf->Cell(32, 4, 'EN EFECTIVO', 0);   
$pdf->Cell(15, 4, $this->dato->Efectivo->cant,0,0,'R');
$pdf->Cell(25, 4, number_format(($this->dato->Efectivo->total),2),0,0,'R');
$pdf->Ln(4);

$pdf->Cell(32, 4, 'EN TARJETA', 0);   
$pdf->Cell(15, 4, $this->dato->Tarjeta->cant,0,0,'R');
$pdf->Cell(25, 4, number_format(($this->dato->Tarjeta->total),2),0,0,'R');
$pdf->Ln(4);

if($this->dato->Culqi->total > 0){
$pdf->Cell(32, 4, 'CON CULQI', 0);   
$pdf->Cell(15, 4, $this->dato->Culqi->cant,0,0,'R');
$pdf->Cell(25, 4, number_format(($this->dato->Culqi->total),2),0,0,'R');
$pdf->Ln(4);
}

if($this->dato->Yape->total > 0){
$pdf->Cell(32, 4, 'CON YAPE', 0);   
$pdf->Cell(15, 4, $this->dato->Yape->cant,0,0,'R');
$pdf->Cell(25, 4, number_format(($this->dato->Yape->total),2),0,0,'R');
$pdf->Ln(4);
}

if($this->dato->Lukita->total > 0){
$pdf->Cell(32, 4, 'CON LUKITA', 0);   
$pdf->Cell(15, 4, $this->dato->Lukita->cant,0,0,'R');
$pdf->Cell(25, 4, number_format(($this->dato->Lukita->total),2),0,0,'R');
$pdf->Ln(4);
}

if($this->dato->Transferencias->total > 0){
$pdf->Cell(32, 4, 'CON TRANSFERENCIAS', 0);   
$pdf->Cell(15, 4, $this->dato->Transferencias->cant,0,0,'R');
$pdf->Cell(25, 4, number_format(($this->dato->Transferencias->total),2),0,0,'R');
$pdf->Ln(4);
}

if($this->dato->Estilos->total > 0){
$pdf->Cell(32, 4, 'CON ESTILOS', 0);   
$pdf->Cell(15, 4, $this->dato->Estilos->cant,0,0,'R');
$pdf->Cell(25, 4, number_format(($this->dato->Estilos->total),2),0,0,'R');
$pdf->Ln(4);
}

if($this->dato->Credishop->total > 0){
$pdf->Cell(32, 4, 'CON CREDISHOP', 0);   
$pdf->Cell(15, 4, $this->dato->Credishop->cant,0,0,'R');
$pdf->Cell(25, 4, number_format(($this->dato->Credishop->total),2),0,0,'R');
$pdf->Ln(4);
}

if($this->dato->Tasa->total > 0){
$pdf->Cell(32, 4, 'CON TASA', 0);   
$pdf->Cell(15, 4, $this->dato->Tasa->cant,0,0,'R');
$pdf->Cell(25, 4, number_format(($this->dato->Tasa->total),2),0,0,'R');
$pdf->Ln(4);
}

if($this->dato->Plin->total > 0){
$pdf->Cell(32, 4, 'CON PLIN', 0);   
$pdf->Cell(15, 4, $this->dato->Plin->cant,0,0,'R');
$pdf->Cell(25, 4, number_format(($this->dato->Plin->total),2),0,0,'R');
$pdf->Ln(4);
}

if($this->dato->Tunki->total > 0){
$pdf->Cell(32, 4, 'CON TUNKI', 0);   
$pdf->Cell(15, 4, $this->dato->Tunki->cant,0,0,'R');
$pdf->Cell(25, 4, number_format(($this->dato->Tunki->total),2),0,0,'R');
$pdf->Ln(4);
}

if(Session::get('opc_01') == 1) {
$pdf->Cell(32, 4, 'CON GLOVO', 0);   
$pdf->Cell(15, 4, $this->dato->Glovo->cant,0,0,'R');
$pdf->Cell(25, 4, number_format(($this->dato->Glovo->total),2),0,0,'R');
$pdf->Ln(4);
$pdf->Cell(32, 4, 'CON RAPPI', 0);    
$pdf->Cell(15, 4, $this->dato->Rappi->cant,0,0,'R');
$pdf->Cell(25, 4, number_format(($this->dato->Rappi->total),2),0,0,'R');
$pdf->Ln(4);
}

$pdf->Cell(72,0,'','T');
$pdf->Ln(1);
$pdf->Cell(37, 4, 'TOTAL VENTAS', 0);    
$pdf->Cell(20, 4, '', 0);
$totalapp = $this->dato->Glovo->total + $this->dato->Rappi->total;
$pdf->Cell(15, 4, '= '.number_format(($this->dato->Principal->total + $totalapp),2),0,0,'R');

//OTRAS OPERACIONES
$pdf->Ln(8);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(72,4,'== OTRAS OPERACIONES ==',0,1,'C');
$pdf->Ln(4);
$pdf->SetFont('Courier','',9);
$pdf->Cell(32, 4, '', 0);
$pdf->Cell(15, 4, 'OPER.',0,0,'R');
$pdf->Cell(25, 4, 'TOTAL',0,0,'R');
$pdf->Ln(4); 
$pdf->Cell(32, 4, 'DESCUENTOS', 0);
$pdf->Cell(15, 4, $this->dato->Descuentos->cant,0,0,'R');
$pdf->Cell(25, 4, number_format(($this->dato->Principal->descu),2),0,0,'R');
$pdf->Ln(4); 
$pdf->Cell(32, 4, 'COMISION DELIVERY', 0);
$pdf->Cell(15, 4, $this->dato->ComisionDelivery->cant,0,0,'R');
$pdf->Cell(25, 4, number_format(($this->dato->Principal->comis_del),2),0,0,'R');
$pdf->Ln(4); 
$pdf->Cell(32, 4, 'ANULACIONES VENTAS', 0);
$pdf->Cell(15, 4, $this->dato->Anulaciones->cant,0,0,'R');
$pdf->Cell(25, 4, number_format(($this->dato->Anulaciones->total),2),0,0,'R');

if(Session::get('opc_02') == 1) {
$pollos_vendidos = 0;
foreach($this->dato->PollosVendidos as $d){
	$pollos_vendidos += $d->cantidad * $d->cant;
}
$pdf->Ln(4); 
$pdf->Cell(32, 4, 'POLLOS VENDIDOS', 0);
$pdf->Cell(15, 4, $pollos_vendidos,0,0,'R');
$pdf->Cell(25, 4, '',0,0,'R');
$pdf->Ln(4); 
$pdf->Cell(32, 4, 'POLLOS STOCK', 0);
$pdf->Cell(15, 4, $this->dato->stock_pollo,0,0,'R');
$pdf->Cell(25, 4, '',0,0,'R');
}
//PRODUCTOS VENDIDOS
$pdf->Ln(8);
$pdf->SetFont('Courier','B',12);
$pdf->Cell(72,4,'== PRODUCTOS VENDIDOS ==',0,1,'C');
$pdf->Ln(1);
// COLUMNAS
$pdf->SetFont('Courier', 'B', 9);
$pdf->Cell(40, 4, 'PRODUCTO',0);
$pdf->Cell(10, 4, 'CANT.',0,0,'R');
$pdf->Cell(10, 4, 'P.U.',0,0,'R');
$pdf->Cell(10, 4, 'IMP.',0,0,'R');
$pdf->Ln(4);
$pdf->Cell(72,0,'','T');
$pdf->Ln(1);
// PRODUCTOS
foreach($this->dato->Detalle as $d){
$pdf->SetFont('Arial', 'B', 8);
$pdf->MultiCell(40,4,utf8_decode($d->Producto->pro_nom).''.utf8_decode($d->Producto->pro_pre),1,'L'); 
$pdf->Cell(45, -4, $d->cantidad,0,0,'R');
$pdf->Cell(14, -4, $d->precio,1,0,'L');
$pdf->Cell(15, -4, number_format(($d->cantidad * $d->precio),2),1,0,'L');
$pdf->Ln(1);
} 
$pdf->Cell(72,0,'','T');
$pdf->Ln(6); 
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