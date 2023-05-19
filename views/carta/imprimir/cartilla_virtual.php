<?php
ini_set('display_errors', 1);
require_once ('public/lib/print/num_letras.php');
require_once ('public/lib/pdf/cellfit.php');
require_once ('public/lib/phpqrcode/qrlib.php');

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
$pdf = new FPDF_CellFiti('P','mm',array(80,130));
$pdf->AddPage();
$pdf->SetMargins(0,0,0,0);
$pdf->SetX(0);
$pdf->Image('public/images/carta/previa.png', 10, 5, 60, 30, 'png');
$pdf->Ln(30);
$pdf->SetFont('COURIER', 'BI', 15);
$pdf->MultiCell(80, 5, "CARTILLA VIRTUAL", 0, 'C');
$pdf->Ln(5);
$pdf->Image('public/images/carta/carta_final.png', 10, 50, 60, 60, 'png');




$pdf->Output('carta_virtual.pdf','I');
?>