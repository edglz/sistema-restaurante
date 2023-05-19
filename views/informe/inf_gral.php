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
function dx($x): void
{
    echo '<pre>';
    var_export($x);
    echo '</pre>';
}

define('EURO',chr(128));
$pdf = new FPDF_CellFiti('P','mm',array(80,2500));
$pdf->AddPage();
$pdf->SetMargins(0,0,0,0);
$pdf->SetFont('Courier','B',10);
 if($this->Empresa){
  $pdf->setX(0);
     $Empresa = $this->Empresa;
     $url_logo = URL."public/images/".$this->Empresa['logo'];
 	$pdf->Image($url_logo,L_CENTER-5,3,L_DIMENSION + 20,0,L_FORMATO);
    $pdf->Ln(12);
//     $pdf->MultiCell(0, 4, utf8_decode($Empresa['razon_social']), 0, 'C');
//     $pdf->MultiCell(0, 4, utf8_decode($Empresa['direccion_comercial']), 0, 'C');
//     $pdf->MultiCell(0, 4, utf8_decode($Empresa['provincia']).','.utf8_decode($Empresa['distrito']), 0, 'C');
//     $pdf->MultiCell(0, 4, utf8_decode('Télefono: '. $Empresa['celular']), 0, 'C');
 }

$pdf->SetFont('Arial', 'BI', 12);
$pdf->Ln(3);
$pdf->MultiCell(0, 5, 'ARQUEO DE CAJA GENERAL', 0, 'C');
$pdf->Ln(3);
$pdf->MultiCell(0, 5, 'CAJAS APERTURADAS', 0, 'C');
$pdf->SetFont('Courier','B',10);
$pdf->Ln(3);
foreach($this->Detalle->aperturas as $k => $d){
   
    $pdf->MultiCell(0, 4, utf8_decode($d->desc_caja) . ' / '.utf8_decode($d->desc_per), 0, 'C');
}
$pdf->SetFont('Arial', 'BI', 12);
$pdf->Ln(3);
$pdf->MultiCell(0, 5, 'APERTURAS Y CIERRES', 0, 'C');
$pdf->Ln(3);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(20, 5, 'CAJA', 1, 0, 'C');
$pdf->Cell(30, 5, 'APER.', 1, 0, 'C');
$pdf->Cell(30, 5, 'CIERRE', 1, 1, 'C');
$pdf->setX(0);
foreach($this->Detalle->aperturas as $k => $d){
    $pdf->Cell(20, 5, utf8_decode($d->desc_caja), 1, 0,'C');
    $pdf->Cell(30, 5, utf8_decode(date("d/m/y H:i",strtotime($d->fecha_aper))),1, 0,'C');
    if($d->fecha_cierre == NULL || $d->fecha_cierre == ""){
        $fecha_cierre = "ACTIVA";
    }else{
        $fecha_cierre = date("d/m/y H:i",strtotime($d->fecha_cierre));
    }
    $pdf->Cell(30, 5, utf8_decode($fecha_cierre), 1,1, 'C');
}
$pdf->SetFont('Arial', 'BI', 12);
$pdf->Ln(3);
$pdf->MultiCell(0, 5, 'DINERO EN CAJA', 0, 'C');
$pdf->Ln(3);
$pdf->SetFont('Courier','B',10);
$total_cajas = 0;
$dif = 0;
$monto_aper_total = 0;
$monto_cierre_total = 0;
$monto_cierre_sum = 0;
$pago_efe_sum = 0; 
$total_sum_ing = 0;
$total_sum_eg = 0;
foreach($this->Detalle->aperturas as $k => $d){
    $pdf->Cell(80, 0, '       ', 1, 1, 'L');
    $pdf->SetFont('Arial', 'BI', 10);
    $pdf->MultiCell(0, 5, utf8_decode($d->desc_caja), 0, 'C');
    $pdf->Cell(80, 0, '       ', 1, 1, 'L');
    $pdf->SetFont('Courier','B',10);
    $pdf->MultiCell(0, 5, 'MONTO APERTURA. ' .Session::get('moneda'). utf8_decode($d->monto_aper), 0, 'J');
    // $pdf->MultiCell(0, 5, 'MONTO CIERRE. ' .Session::get('moneda'). utf8_decode($d->monto_cierre),0, 'J');
    $pdf->MultiCell(0, 5, 'VENTAS EFECTIVO. ' .Session::get('moneda'). utf8_decode($d->Principal->pago_efe),0, 'J');
    $pdf->MultiCell(0, 5, 'ENTRADAS EFECTVO. ' .Session::get('moneda'). utf8_decode($d->Ingresos->total),0, 'J');
    $pdf->MultiCell(0, 5, 'SALIDAS EFECTIVO. ' .Session::get('moneda'). utf8_decode($d->Egresos->total),0, 'J');
    $efectivoencaja = $d->monto_aper + $d->Principal->pago_efe + $d->Ingresos->total - $d->Egresos->total;
    $pdf->Cell(80, 0, '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ', 0, 1, 'L');
    $pdf->MultiCell(0, 5, 'EFECTIVO EN ' .utf8_decode($d->desc_caja).': ' .Session::get('moneda'). utf8_decode(number_format($efectivoencaja, 2)),0, 'J');
    $pdf->MultiCell(0, 5, 'EFECTIVO EN CIERRE: ' .Session::get('moneda'). utf8_decode(number_format($d->monto_cierre, 2)),0, 'J');
    $efectivodiferencia = $efectivoencaja - $d->monto_cierre;
    $pdf->Cell(80, 0, '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ', 0, 1, 'L');
    $nombre_efectivodiferencia = ($efectivodiferencia > 0) ?  'FALTANTE' : 'RESTANTE';
    $pdf->MultiCell(0, 5, $nombre_efectivodiferencia.' = '.Session::get('moneda') . ' '. number_format(($efectivodiferencia),2),0,'R');
    $pdf->Cell(80, 0, '       ', 1, 1, 'L');
    //OPERACIONES GENERALES:
    $total_cajas += $efectivoencaja;
    $dif += $efectivodiferencia;
    $monto_aper_total += $d->monto_aper;
    $monto_cierre_total += $d->monto_cierre;
    $pago_efe_sum += $d->Principal->pago_efe;
    $total_sum_ing += $d->Ingresos->total;
    $total_sum_eg += $d->Egresos->total;
   
}

$pdf->Ln(3);
$pdf->SetFont('Arial', 'BI', 12);
$pdf->MultiCell(0, 5, 'DINERO EN CAJA GENERAL', 0, 'C');
$pdf->SetFont('Courier','B',10);
$pdf->Ln(3);
$pdf->Cell(80, 0, '       ', 1, 1, 'L');
$pdf->MultiCell(0, 5, 'MONTO APER. GENERAL. ' .Session::get('moneda'). utf8_decode(number_format($monto_aper_total, 2)), 0, 'J');
// $pdf->MultiCell(0, 5, 'MONTO CIER. GENERAL. ' .Session::get('moneda'). utf8_decode(number_format($monto_cierre_total, 2)), 0, 'J');
$pdf->MultiCell(0, 5, 'VENTAS EFE. GENERAL. ' .Session::get('moneda'). utf8_decode(number_format($pago_efe_sum, 2)), 0, 'J');
$pdf->MultiCell(0, 5, 'ENTRADAS EFE. GENERAL. ' .Session::get('moneda'). utf8_decode(number_format($total_sum_ing, 2)), 0, 'J');
$pdf->MultiCell(0, 5, 'SALIDAS EFE. GENERAL. ' .Session::get('moneda'). utf8_decode(number_format($total_sum_eg, 2)), 0, 'J');
$pdf->Cell(80, 0, '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ', 0, 1, 'L');
$pdf->MultiCell(0, 5, 'EFECTIVO GENERAL. ' .Session::get('moneda'). utf8_decode(number_format($total_cajas, 2)), 0, 'J');
$pdf->MultiCell(0, 5, 'EFECTIVO CIER GENERAL. ' .Session::get('moneda'). utf8_decode(number_format($monto_cierre_total, 2)), 0, 'J');
$pdf->Cell(80, 0, '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ', 0, 1, 'L');
$pdf->Ln(1);

//OPERACIONES PARA CALCULAR SI ES RESTANTE O FALTANTE
$efe_gral = $monto_aper_total + $pago_efe_sum +  $total_sum_ing - $total_sum_eg;
$dif = $efe_gral - $monto_cierre_total;
$ndif = ($dif > 0) ? 'FALTANTE' : 'RESTANTE';
// $pdf->MultiCell(0, 5, 'TOTAL GENERAL. ' .Session::get('moneda'). utf8_decode(number_format($efe_gral, 2)), 0, 'R');
$pdf->MultiCell(0, 5, $ndif.' = '.Session::get('moneda') . ' '. number_format(($dif),2),0,'R');
$pdf->Cell(80, 0, '       ', 1, 1, 'L');
$pdf->Ln(3);
$pdf->MultiCell(0, 5, ' ', 1, 'C', 1);
$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 15);
$pdf->MultiCell(0, 0.5, ' ', 1, 'C', 1);
$pdf->MultiCell(0, 14, 'VENTAS INDIVIDUAL', 0, 'C');
$pdf->MultiCell(0, 0.5, ' ', 1, 'C', 1);
$pdf->Ln(3);
$pdf->SetFont('Courier','B',10);
//VARIABLES DE SUMA
$ent_dinero = 0;
$total_fin = 0;
$salidas_efe_compra = 0;
$salidas_efe_serv = 0;
$salidas_efe_rem = 0;
$salidas_efe_prov = 0;
$ventas_cred_total = 0;
$ventas_efe_total = 0;
$ventas_tar_total = 0;
$ventas_culqi_total = 0;
$ventas_portero_total = 0;
$ventas_yape_total = 0;
$ventas_lukita_total = 0;
$ventas_transfer_total = 0;
$ventas_estilos_total = 0;
$ventas_creditshop_total = 0;
$ventas_tasa_total = 0;
$ventas_plin_total = 0;
$ventas_tunki_total = 0;
$ventas_glovo_total = 0;
$ventas_rappi_total = 0;
$total_ven_app = 0;
$ventas_total = 0;
$desc_total = 0;
$com_delivery = 0;
$anul_ventas = 0;
$pollos_vendidos_total = 0;
$total_pollos_stock = 0;
//jghasg

$cant_ventas_efe = 0;
$cant_ventas_tar = 0;
$cant_ventas_culqi = 0;
$cant_ventas_portero = 0;
$cant_ventas_yape = 0;
$cant_ventas_lukita = 0;
$cant_ventas_transfer = 0;
$cant_ventas_estilos = 0;
$cant_ventas_creditshop = 0;
$cant_ventas_tasa = 0;
$cant_ventas_plin = 0;
$cant_ventas_tunki = 0;
$cant_ventas_glovo = 0;
$cant_ventas_rappi = 0;
$cant_desc = 0;
$cant_com_delivery = 0;
$cant_anul_ventas = 0;
$pt = 0;

    foreach($this->Detalle->aperturas as $k => $d):
      
        $pt += $d->Principal->total;     
        $pdf->SetFont('Arial', 'BI', 12);
       
        $pdf->MultiCell(0, 5, utf8_decode($d->desc_caja), 1, 'C');
        
        $pdf->SetFont('Courier','B',10);
        $pdf->Ln(3);
        $pdf->Cell(80, 0, ' ', 1, 1, 'C');
        $pdf->Cell(80,4,'== ENTRADAS EFECTIVO '.$d->desc_caja.' ==',0,1,'C');
        $pdf->Cell(80, 0, ' ', 1, 1, 'C');
        $pdf->Ln(4);
        $pdf->SetFont('Courier','B',9);
        $pdf->Cell(37, 4, 'ENTRADA DE DINERO', 0);    
        $pdf->Cell(37, 4, number_format(($d->Ingresos->total),2),0,1,'R');
        $pdf->Cell(80, 0, '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ', 0, 1, 'L');

        
      
        $pdf->Cell(37, 4, 'TOTAL ENTRADAS', 0);    
        $pdf->Cell(37, 4, '= '.number_format(($d->Ingresos->total),2),0,1,'R');
        $pdf->Ln(3);

        //SUMAMOS TOTAL DE ENTRADAS DINERO
        $ent_dinero += $d->Ingresos->total;
        //SUMAMOS TOTAL DE SALIDAS
        $salidas_efe_compra += $d->EgresosA->total;
        $salidas_efe_serv += $d->EgresosB->total;
        $salidas_efe_rem += $d->EgresosC->total;
        $salidas_efe_prov += $d->EgresosD->total;
        $total_fin += $d->Egresos->total;
        $pdf->SetFont('Courier','B',10);
        $pdf->Cell(80, 0, ' ', 1, 1, 'C');
        $pdf->Cell(80,4,'== SALIDAS EFECTIVO '.$d->desc_caja.' ==',0,1,'C');
        $pdf->Cell(80, 0, ' ', 1, 1, 'C');
        $pdf->Ln(4);
        $pdf->SetFont('Courier','B',9);
        $pdf->Cell(37, 4, 'COMPRAS', 0);   
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, number_format(($d->EgresosA->total),2),0,0,'R');
        $pdf->Ln(4);
        $pdf->Cell(37, 4, 'SERVICIOS', 0);   
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, number_format(($d->EgresosB->total),2),0,0,'R');
        $pdf->Ln(4);
        $pdf->Cell(37, 4, 'REMUNERACIONES', 0);   
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, number_format(($d->EgresosC->total),2),0,0,'R');
        $pdf->Ln(4);
        $pdf->Cell(37, 4, 'PAGOS A PROVEEDORES', 0);   
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, number_format(($d->EgresosD->total),2),0,0,'R');
        $pdf->Ln(4);
        $pdf->Cell(80, 0, '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ', 0, 1, 'L');
        $pdf->Ln(1);
        $pdf->Cell(37, 4, 'TOTAL SALIDAS', 0);    
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, '= '.number_format(($d->Egresos->total),2),0,1,'R');
        $pdf->Ln(3);
        //SUMAMOS LAS DEMAS VARIABLES, VENTAS, CULQUI, PORTERO, ETC
        $ventas_cred_total += $d->Credito->total;
        $ventas_efe_total += $d->Efectivo->total;
        $ventas_tar_total += $d->Tarjeta->total;
        $ventas_culqi_total += $d->Culqi->total;
        $ventas_portero_total += $d->Portero->total;
        $ventas_yape_total += $d->Yape->total;
        $ventas_lukita_total += $d->Lukita->total;
        $ventas_transfer_total +=$d->Transferencias->total;
        $ventas_estilos_total += $d->Estilos->total;
        $ventas_creditshop_total += $d->Credishop->total;
        $ventas_tasa_total += $d->Tasa->total;
        $ventas_plin_total += $d->Plin->total;
        $ventas_tunki_total += $d->Tunki->total;
       
        $desc_total += $d->Principal->descu;
        $com_delivery += $d->Principal->comis_del;
        $anul_ventas += $d->Anulaciones->total;
        $cant_ventas_efe +=  $d->Efectivo->cant;
        $cant_ventas_tar +=  $d->Tarjeta->cant;
        $cant_ventas_culqi +=  $d->Culqi->cant;
        $cant_ventas_portero += $d->Portero->cant;
        $cant_ventas_yape += $d->Yape->cant;
        $cant_ventas_lukita += $d->Lukita->cant;
        $cant_ventas_transfer += $d->Transferencias->cant;
        $cant_ventas_estilos +=  $d->Estilos->cant;
        $cant_ventas_creditshop += $d->Credishop->cant;
        $cant_ventas_tasa +=  $d->Tasa->cant;
        $cant_ventas_plin += $d->Plin->cant;
        $cant_ventas_tunki +=  $d->Tunki->cant;
        $cant_ventas_glovo +=  $d->Glovo->cant;
        $cant_ventas_rappi += $d->Rappi->cant;
        $cant_desc += $d->Principal->descu;
        $cant_com_delivery += $d->Principal->comis_del;
        $cant_anul_ventas += $d->Anulaciones->cant;
        if(Session::get('opc_02') == 1) {
            
            foreach($d->PollosVendidos as $d){
                $pollos_vendidos_total += $d->cantidad * $d->cant;
                 }
         }
        //$total_pollos_stock += $d->stock_pollo;
        //VENTAS
     
        $pdf->SetFont('Courier','B',10);
        $pdf->Cell(80, 0, ' ', 1, 1, 'C');
        $pdf->Cell(80,4,'== VENTAS '.$d->desc_caja.' ==',0,1,'C');
        $pdf->Cell(80, 0, ' ', 1, 1, 'C');
        $pdf->Ln(4);
        $pdf->SetFont('Courier','B',9);

        $pdf->Cell(32, 4, '', 0);
        $pdf->Cell(15, 4, 'OPER.',0,0,'R');
        $pdf->Cell(25, 4, 'TOTAL',0,0,'R');
        $pdf->Ln(4);

        if($d->Credito->total > 0){
        
        $pdf->Cell(32, 4, 'AL CREDITO', 0);   
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, number_format(($d->Credito->total),2),0,0,'R');
        $pdf->Ln(4);
        }

        $pdf->Cell(32, 4, 'EN EFECTIVO', 0);   
        $pdf->Cell(15, 4, $d->Efectivo->cant,0,0,'R');
        $pdf->Cell(25, 4, number_format(($d->Efectivo->total),2),0,0,'R');
        $pdf->Ln(4);
        $pdf->SetFont('Courier','BI',9);
        $pdf->Cell(32, 4, 'EN TARJETA', 0);   
        $pdf->Cell(15, 4, $d->Tarjeta->cant,0,0,'R');
        $pdf->Cell(25, 4, number_format(($d->Tarjeta->total),2),0,0,'R');
        $pdf->Ln(4);
        $pdf->SetFont('Courier','B',9);

        if($d->Culqi->total > 0){
        $pdf->Cell(32, 4, 'CON CULQI', 0);   
        $pdf->Cell(15, 4, $d->Culqi->cant,0,0,'R');
        $pdf->Cell(25, 4, number_format(($d->Culqi->total),2),0,0,'R');
        $pdf->Ln(4);
        }
       
        if($d->Yape->total > 0){
        $pdf->Cell(32, 4, 'CON YAPE', 0);   
        $pdf->Cell(15, 4, $d->Yape->cant,0,0,'R');
        $pdf->Cell(25, 4, number_format(($d->Yape->total),2),0,0,'R');
        $pdf->Ln(4);
        }

        if($d->Lukita->total > 0){
        $pdf->Cell(32, 4, 'CON LUKITA', 0);   
        $pdf->Cell(15, 4, $d->Lukita->cant,0,0,'R');
        $pdf->Cell(25, 4, number_format(($d->Lukita->total),2),0,0,'R');
        $pdf->Ln(4);
        }

        if($d->Transferencias->total > 0){
        $pdf->Cell(32, 4, 'CON TRANSFERENCIAS', 0);   
        $pdf->Cell(15, 4, $d->Transferencias->cant,0,0,'R');
        $pdf->Cell(25, 4, number_format(($d->Transferencias->total),2),0,0,'R');
        $pdf->Ln(4);
        }

        if($d->Estilos->total > 0){
        $pdf->Cell(32, 4, 'CON ESTILOS', 0);   
        $pdf->Cell(15, 4, $d->Estilos->cant,0,0,'R');
        $pdf->Cell(25, 4, number_format(($d->Estilos->total),2),0,0,'R');
        $pdf->Ln(4);
        }

        if($d->Credishop->total > 0){
        $pdf->Cell(32, 4, 'CON CREDISHOP', 0);   
        $pdf->Cell(15, 4, $d->Credishop->cant,0,0,'R');
        $pdf->Cell(25, 4, number_format(($d->Credishop->total),2),0,0,'R');
        $pdf->Ln(4);
        }

        if($d->Tasa->total > 0){
        $pdf->Cell(32, 4, 'CON TASA', 0);   
        $pdf->Cell(15, 4, $d->Tasa->cant,0,0,'R');
        $pdf->Cell(25, 4, number_format(($d->Tasa->total),2),0,0,'R');
        $pdf->Ln(4);
        }

        if($d->Plin->total > 0){
        $pdf->Cell(32, 4, 'CON PLIN', 0);   
        $pdf->Cell(15, 4, $d->Plin->cant,0,0,'R');
        $pdf->Cell(25, 4, number_format(($d->Plin->total),2),0,0,'R');
        $pdf->Ln(4);
        }

        if($d->Tunki->total > 0){
        $pdf->Cell(32, 4, 'CON TUNKI', 0);   
        $pdf->Cell(15, 4, $d->Tunki->cant,0,0,'R');
        $pdf->Cell(25, 4, number_format(($d->Tunki->total),2),0,0,'R');
        $pdf->Ln(4);
        }

        if(Session::get('opc_01') == 1) {
            $total_venta += $d->Principal->total + $total_ven_app;
            $ventas_glovo_total += $d->Glovo->total;
            $ventas_rappi_total += $d->Rappi->total;
            $total_ven_app += $d->Glovo->total + $d->Rappi->total;
        $pdf->Cell(32, 4, 'CON GLOVO', 0);   
        $pdf->Cell(15, 4, $d->Glovo->cant,0,0,'R');
        $pdf->Cell(25, 4, number_format(($d->Glovo->total),2),0,0,'R');
        $pdf->Ln(4);
        $pdf->Cell(32, 4, 'CON RAPPI', 0);    
        $pdf->Cell(15, 4, $d->Rappi->cant,0,0,'R');
        $pdf->Cell(25, 4, number_format(($d->Rappi->total),2),0,0,'R');
        $pdf->Ln(4);
        }
        $pdf->Cell(80, 0, '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ', 0, 1, 'L');
        $pdf->Ln(1);
        $pdf->Cell(37, 4, 'TOTAL VENTAS', 0);    
        $pdf->Cell(20, 4, '', 0);
        $totalapp = $d->Glovo->total + $d->Rappi->total;
        $pdf->Cell(15, 4, '= '.number_format(($d->Principal->total + $totalapp),2),0,0,'R');

        //OTRAS OPERACIONES
        $pdf->Ln(8);
        $pdf->SetFont('Courier','B',10);
        $pdf->Cell(80, 0, ' ', 1, 1, 'C');
  

        $pdf->Cell(80,4,'== OTRAS OPERACIONES '.$d->desc_caja.' ==',0,1,'C');

        $pdf->Cell(80, 0, ' ', 1, 1, 'C');
        $pdf->Ln(4);
        $pdf->SetFont('Courier','',9);
        $pdf->Cell(32, 4, '', 0);
        $pdf->Cell(15, 4, 'OPER.',0,0,'R');
        $pdf->Cell(25, 4, 'TOTAL',0,0,'R');
        $pdf->Ln(4); 
        $pdf->Cell(32, 4, 'DESCUENTOS', 0);
        $pdf->Cell(15, 4, $d->Descuentos->cant,0,0,'R');
        $pdf->Cell(25, 4, number_format(($d->Principal->descu),2),0,0,'R');
        $pdf->Ln(4); 
        $pdf->Cell(32, 4, 'COMISION DELIVERY', 0);
        $pdf->Cell(15, 4, $d->ComisionDelivery->cant,0,0,'R');
        $pdf->Cell(25, 4, number_format(($d->Principal->comis_del),2),0,0,'R');
        $pdf->Ln(4); 
        $pdf->Cell(32, 4, 'ANULACIONES VENTAS', 0);
        $pdf->Cell(15, 4, $d->Anulaciones->cant,0,0,'R');
        $pdf->Cell(25, 4, number_format(($d->Anulaciones->total),2),0,1,'R');

        if(Session::get('opc_02') == 1) {
        $pollos_vendidos = 0;
        foreach($d->PollosVendidos as $d){
            $pollos_vendidos += $d->cantidad * $d->cant;
        }
        $pdf->Ln(4); 
        $pdf->Cell(32, 4, 'POLLOS VENDIDOS', 0);
        $pdf->Cell(15, 4, $pollos_vendidos,0,0,'R');
        $pdf->Cell(25, 4, '',0,0,'R');
        $pdf->Ln(4); 
        $pdf->Cell(32, 4, 'POLLOS STOCK', 0);
        $pdf->Cell(15, 4, $d->stock_pollo,0,0,'R');
        $pdf->Cell(25, 4, '',0,1,'R');
        }
        $pdf->Ln(3);

    endforeach;
   
        $pdf->Ln(1);
       
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->MultiCell(0, 0.5, ' ', 1, 'C', 1);
        $pdf->MultiCell(0, 14, 'VENTAS GENERALES', 0, 'C');
        $pdf->MultiCell(0, 0.5, ' ', 1, 'C', 1);

        $pdf->Ln(2);
        $pdf->SetFont('Courier','B',10);
      
        $pdf->Cell(80,4,'== ENTRADAS EFECTIVO GENERAL ==',1,1,'C');
        

        $pdf->Ln(4);
        $pdf->SetFont('Courier','B',9);
        $pdf->Cell(37, 4, 'ENTRADA DE DINERO', 0);    
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, number_format(( $ent_dinero),2),0,0,'R');
        $pdf->Ln(4);
        $pdf->Cell(80, 0, '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ', 0, 1, 'L');
        $pdf->Ln(1);
        $pdf->Cell(37, 4, 'TOTAL ENTRADAS', 0);    
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, '= '.number_format(( $ent_dinero),2),0,0,'R');
        $pdf->Ln(8);
        $pdf->SetFont('Courier','B',10);
        $pdf->Cell(80,4,'== SALIDAS EFECTIVO GENERAL==',1,1,'C');
        $pdf->Ln(4);
        $pdf->SetFont('Courier','B',9);
        $pdf->Cell(37, 4, 'COMPRAS', 0);   
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, number_format(($salidas_efe_compra),2),0,0,'R');
        $pdf->Ln(4);
        $pdf->Cell(37, 4, 'SERVICIOS', 0);   
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, number_format(( $salidas_efe_serv),2),0,0,'R');
        $pdf->Ln(4);
        $pdf->Cell(37, 4, 'REMUNERACIONES', 0);   
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, number_format(( $salidas_efe_rem),2),0,0,'R');
        $pdf->Ln(4);
        $pdf->Cell(37, 4, 'PAGOS A PROVEEDORES', 0);   
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, number_format(($salidas_efe_prov),2),0,0,'R');
        $pdf->Ln(4);
        $pdf->Cell(80, 0, '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ', 0, 1, 'L');
        $pdf->Ln(1);
        $pdf->Cell(37, 4, 'TOTAL SALIDAS', 0);    
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, '= '.number_format(( $total_fin),2),0,1,'R');
        $pdf->Ln(1);

        //VENTAS
        $pdf->Ln(2);
        $pdf->SetFont('Courier','B',10);
   
        $pdf->Cell(80,4,'== VENTAS GENERAL ==',1,1,'C');
      

        $pdf->Ln(4);
        $pdf->SetFont('Courier','B',9);

        $pdf->Cell(32, 4, '', 0);
        $pdf->Cell(15, 4, 'OPER.',0,0,'R');
        $pdf->Cell(25, 4, 'TOTAL',0,0,'R');
        $pdf->Ln(4);

        if($ventas_cred_total > 0){
        
        $pdf->Cell(32, 4, 'AL CREDITO', 0);   
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, number_format(($ventas_cred_total),2),0,0,'R');
        $pdf->Ln(4);
        }

        $pdf->Cell(32, 4, 'EN EFECTIVO', 0);   
        $pdf->Cell(15, 4, $cant_ventas_efe,0,0,'R');
        $pdf->Cell(25, 4, number_format((  $ventas_efe_total),2),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(32, 4, 'EN TARJETA', 0);   
        $pdf->Cell(15, 4,  $cant_ventas_tar,0,0,'R');
        $pdf->Cell(25, 4, number_format(($ventas_tar_total),2),0,0,'R');
        $pdf->Ln(4);

        if( $ventas_culqi_total > 0){
        $pdf->Cell(32, 4, 'CON CULQI', 0);   
        $pdf->Cell(15, 4, $cant_ventas_culqi ,0,0,'R');
        $pdf->Cell(25, 4, number_format(( $ventas_culqi_total),2),0,0,'R');
        $pdf->Ln(4);
        }
        
        if($ventas_yape_total > 0){
        $pdf->Cell(32, 4, 'CON YAPE', 0);   
        $pdf->Cell(15, 4,  $cant_ventas_yape ,0,0,'R');
        $pdf->Cell(25, 4, number_format(($ventas_yape_total),2),0,0,'R');
        $pdf->Ln(4);
        }

        if($ventas_lukita_total> 0){
        $pdf->Cell(32, 4, 'CON LUKITA', 0);   
        $pdf->Cell(15, 4,  $cant_ventas_lukita,0,0,'R');
        $pdf->Cell(25, 4, number_format(( $ventas_lukita_total),2),0,0,'R');
        $pdf->Ln(4);
        }

        if($ventas_transfer_total  > 0){
        $pdf->Cell(32, 4, 'CON TRANSFERENCIAS', 0);   
        $pdf->Cell(15, 4, $cant_ventas_transfer ,0,0,'R');
        $pdf->Cell(25, 4, number_format(($ventas_transfer_total ),2),0,0,'R');
        $pdf->Ln(4);
        }

        if($ventas_estilos_total > 0){
        $pdf->Cell(32, 4, 'CON ESTILOS', 0);   
        $pdf->Cell(15, 4, $cant_ventas_estilos,0,0,'R');
        $pdf->Cell(25, 4, number_format(($ventas_estilos_total),2),0,0,'R');
        $pdf->Ln(4);
        }

        if($ventas_creditshop_total > 0){
        $pdf->Cell(32, 4, 'CON CREDISHOP', 0);   
        $pdf->Cell(15, 4, $cant_ventas_creditshop,0,0,'R');
        $pdf->Cell(25, 4, number_format(($ventas_creditshop_total),2),0,0,'R');
        $pdf->Ln(4);
        }

        if($ventas_tasa_total > 0){
        $pdf->Cell(32, 4, 'CON TASA', 0);   
        $pdf->Cell(15, 4,  $cant_ventas_tasa,0,0,'R');
        $pdf->Cell(25, 4, number_format(($ventas_tasa_total),2),0,0,'R');
        $pdf->Ln(4);
        }

        if($ventas_plin_total > 0){
        $pdf->Cell(32, 4, 'CON PLIN', 0);   
        $pdf->Cell(15, 4, $cant_ventas_plin,0,0,'R');
        $pdf->Cell(25, 4, number_format(($ventas_plin_total),2),0,0,'R');
        $pdf->Ln(4);
        }

        if($ventas_tunki_total > 0){
        $pdf->Cell(32, 4, 'CON TUNKI', 0);   
        $pdf->Cell(15, 4, $cant_ventas_tunki,0,0,'R');
        $pdf->Cell(25, 4, number_format(($ventas_tunki_total),2),0,0,'R');
        $pdf->Ln(4);
        }
        if(Session::get('opc_01') == 1) {
        $pdf->Cell(32, 4, 'CON GLOVO', 0);   
        $pdf->Cell(15, 4, $cant_ventas_glovo,0,0,'R');
        $pdf->Cell(25, 4, number_format(($ventas_glovo_total),2),0,0,'R');
        $pdf->Ln(4);
        $pdf->Cell(32, 4, 'CON RAPPI', 0);    
        $pdf->Cell(15, 4, $cant_ventas_rappi,0,0,'R');
        $pdf->Cell(25, 4, number_format(($ventas_rappi_total),2),0,0,'R');
        $pdf->Ln(4);
        }

        $pdf->Cell(80, 0, '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ', 0, 1, 'L');
        $pdf->Ln(1);
        $pdf->Cell(37, 4, 'TOTAL VENTAS', 0);    
        $pdf->Cell(20, 4, '', 0);
        $totalapp1 =$ventas_glovo_total + $ventas_rappi_total;
        $pdf->Cell(15, 4, '= '.number_format(($pt + $totalapp1),2),0,0,'R');

        //OTRAS OPERACIONES
        $pdf->Ln(8);
        $pdf->SetFont('Courier','B',10);
        
        $pdf->Cell(80,4,'== OTRAS OPERACIONES GENERAL ==',1,1,'C');
     

        $pdf->Ln(4);
        $pdf->SetFont('Courier','',9);
        $pdf->Cell(32, 4, '', 0);
        $pdf->Cell(15, 4, 'OPER.',0,0,'R');
        $pdf->Cell(25, 4, 'TOTAL',0,0,'R');
        $pdf->Ln(4); 
        $pdf->Cell(32, 4, 'DESCUENTOS', 0);
        $pdf->Cell(15, 4,  $cant_desc,0,0,'R');
        $pdf->Cell(25, 4, number_format(($desc_total),2),0,0,'R');
        $pdf->Ln(4); 
        $pdf->Cell(32, 4, 'COMISION DELIVERY', 0);
        $pdf->Cell(15, 4, $cant_com_delivery,0,0,'R');
        $pdf->Cell(25, 4, number_format(($com_delivery),2),0,0,'R');
        $pdf->Ln(4); 
        $pdf->Cell(32, 4, 'ANULACIONES VENTAS', 0);
        $pdf->Cell(15, 4, $cant_anul_ventas,0,0,'R');
        $pdf->Cell(25, 4, number_format(($anul_ventas),2),0,1,'R');
        $pdf->Ln(3);

        if(Session::get('opc_02') == 1) {
        
        $pdf->Ln(4); 
        $pdf->Cell(32, 4, 'POLLOS VENDIDOS', 0);
        $pdf->Cell(15, 4, $pollos_vendidos_total,0,0,'R');
        $pdf->Cell(25, 4, '',0,0,'R');
        $pdf->Ln(4); 
        $pdf->Cell(32, 4, 'POLLOS STOCK', 0);
        $pdf->Cell(15, 4, $total_pollos_stock,0,0,'R');
        $pdf->Cell(25, 4, '',0,1,'R');
        }
$pdf->SetFont('Arial', 'BI', 10);
$pdf->Ln(5);
$pdf->MultiCell(0, 5, ' ', 1, 'C', 1);
$pdf->SetFont('Arial', 'BI', 12);

$pdf->MultiCell(0, 8, 'VENTA DE PRODUCTOS POR CATEGORIA', 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Courier','B',10);
$total_gx = 0;

foreach($this->Catg as $catk => $cla):
    $id_cat = $this->Catg[$catk]['id_catg'];
    $pdf->Cell(80, 0, '       ', 1, 1, 'L');
    $pdf->MultiCell(0, 5, utf8_decode($this->Catg[$catk]['descripcion']), 0, 'C');
    $pdf->Cell(80, 0, '       ', 1, 1, 'L');
        // COLUMNAS
        $pdf->SetFont('Courier', 'B', 9);
        $pdf->Cell(40, 4, 'PRODUCTO',0);
        $pdf->Cell(10, 4, 'CANT.',0,0,'R');
        $pdf->Cell(10, 4, 'P.U.',0,0,'R');
        $pdf->Cell(10, 4, 'IMP.',0,0,'R');
        $pdf->Ln(4);
    $pdf->Cell(80,0,'','T');
    $pdf->Ln(1);
    foreach($this->Detalle->aperturas as $kl => $dx):
        //VERIFICAMOS CUANTOS ELEMENTOS TIENE EL DETALLE
        $final_prd = [];
        $list_prod = [];
        foreach($this->Detalle->aperturas as $kl => $dx):
            foreach ($dx->Detalle as $k => $d) :
                //UNIMOS EL ARRAY A UNO SOLO
                array_push($list_prod, $d);
            endforeach;
        endforeach;
        $ids_products = [];
        //CREAMOS EL ARRAY PARA QUE NO SE REPITA LAS ID
        foreach ($list_prod as $k => $dl) :
            $id_product = $dl->id_prod;
            if (! in_array($id_product, $ids_products)) :
                $ids_products[] = $id_product;
            endif;
        endforeach;
            $result = [];
            foreach ($ids_products as $unique_id) :
                $temp  = [];
                $quantity = 0;
                    foreach ($list_prod as $k => $d) :
                        $id = $d->id_prod;
                        if ($id === $unique_id) :
                            $temp[] = $d; 
                        endif;
                    endforeach;    
                $product = $temp;
                //dx($product);
                $cantidad = 0;
                $count_prod = count($product);
                    for($x = 0; $x < $count_prod; $x++){
                        $cantidad = $cantidad + intval($product[$x]->cantidad);
                    }
                    for($x = 0; $x < $count_prod; $x++){
                        if($x == 0){
                            $final_prd = array(
                                "id_prod" => $product[0]->id_prod,
                                "cantidad" => $cantidad,
                                "precio" => $product[0]->precio,
                                "Producto" => array(
                                    'pro_nom' => $product[0]->Producto->pro_nom,
                                    'pro_pre' => $product[0]->Producto->pro_pre,
                                    'id_areap'=> $product[0]->Producto->id_areap,
                                    'id_catg' => $product[0]->Producto->id_catg,
                                    'pro_cat' => $product[0]->Producto->pro_cat,
                                )
                            );
                        }
                    }
            
            
                $result[] = $final_prd;
            endforeach;     
    endforeach;
    $total_cat = 0;
    $sum_total = 0;
    foreach($result as $k => $d){
        if($id_cat == $d["Producto"]["id_catg"]){
            $pdf->SetFont('Arial', 'B', 8);
            $total_cat += intval($d["cantidad"]);
            $sum_total += floatval($d["cantidad"] * $d["precio"]);
            $pdf->MultiCell(40,4,utf8_decode($d["Producto"]["pro_pre"]) . ' '.utf8_decode($d["Producto"]["pro_nom"]),1,'L'); 
            $pdf->Cell(45, -4, $d["cantidad"],0,0,'R');
            $pdf->Cell(14, -4, Session::get('moneda'). ' ' . $d["precio"],1,0,'L');
            $pdf->Cell(15, -4, Session::get('moneda'). ' '.number_format(($d["cantidad"] * $d["precio"]),2),1,0,'L');
            $total_gx = $total_gx + floatval($d["cantidad"] * $d["precio"]);
            $pdf->Ln(1);
        }
    }   
    $pdf->SetFont('Courier', 'BI', 9);
    $pdf->Multicell(80, 5, utf8_decode("TOTAL PRODUCTOS VENDIDOS EN LA CATEGORIA " . $this->Catg[$catk]['descripcion'] . ' = ' . $total_cat . " UNIDADES\nTOTAL EN " . Session::get('moneda'). ' VENDIDOS = '.  Session::get('moneda'). ' '. number_format($sum_total, 2)), 1, 'C');
endforeach;
$pdf->Cell(80,0,'','T');
$pdf->Cell(0, 4, 'TOTAL EN VENTAS: ' .  Session::get('moneda'). ' '. number_format($total_gx, 2), 0, 1, 'R');
$pdf->Ln(6); 
$pdf->Cell(72,4,'DATOS DE IMPRESION',0,1,'');
$pdf->Cell(72,4,'USUARIO: '.Session::get('nombres').' '.Session::get('apellidos'),0,1,'');
date_default_timezone_set($_SESSION["zona_horaria"]);
setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
$pdf->Cell(72,4,'FECHA: '.date("d-m-Y h:i A"),0,1,'');
$pdf->Ln(8);
$pdf->Cell(0,4,'___________________________________',0,1,'C');
$pdf->Cell(72,4,utf8_decode('FIRMA DE ENCARGADO'),0,1,'C');

$pdf->Ln(10);
$pdf->Output('reporte_general'.$this->Cod_imp.'.pdf','i');
