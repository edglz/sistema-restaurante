<?php
/**
 * Created by PhpStorm.
 * User: elporfirio
 * Date: 2019-02-26
 * Time: 23:04
 */
 
require('fpdf/fpdf.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';
// require 'config.php';
//require 'vendor/autoload.php';
//require 'Constantes.php';

class Email
{
    public function __construct() {
        //$this->db = new Database(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_CHARSET);
    }

    public function sendEmail($correo_cliente,$documento_cliente,$datos_factura,$negocio) {

        try {
            
            $pdf = new FPDF();
            $pdf->AddPage('PORTRAIT', 'letter');
            $data = json_decode($datos_factura,true);
            
            //DATOS DE EMPRESA
            $pdf->Ln(5);
            $pdf->SetFont('Arial','B',11);
            $pdf->Cell( 100, 5, utf8_decode($data['Empresa']['nombre_comercial']) , 0, 1, '');
            $pdf->SetFont('Arial','',10);
            $pdf->MultiCell( 100, 5, utf8_decode($data['Empresa']['direccion_comercial']) , 0, 'L');
            $pdf->Cell( 100, 5, 'TELF: '.utf8_decode($data['Empresa']['celular']) , 0, 1, '');
            
            //DATOS DE CLIENTE
            $textypos = 5;
            $pdf->SetFont('Arial','B',10);    
            $pdf->setY(50);$pdf->setX(10);
            $pdf->Cell(5,$textypos,"Cliente: ");
            $pdf->SetFont('Arial','',10);    
            $pdf->setY(50);$pdf->setX(30);
            $pdf->Cell(5,$textypos,utf8_decode($data['Cliente']['nombre']));
            
            if ($data['Cliente']['tipo_cliente'] == 1){
                $tipodoc = 'DNI';
                $nrodoc = $data['Cliente']['dni'];
            }else if ($data['Cliente']['tipo_cliente'] == 2){
                $tipodoc = 'RUC';
                $nrodoc = $data['Cliente']['ruc'];
            }
            $pdf->SetFont('Arial','B',10);    
            $pdf->setY(55);$pdf->setX(10);
            $pdf->Cell(5,$textypos, $tipodoc);
            $pdf->SetFont('Arial','',10);    
            $pdf->setY(55);$pdf->setX(30);
            $pdf->Cell(5,$textypos, $nrodoc);
            
            $pdf->SetFont('Arial','B',10);
            $pdf->setY(60);$pdf->setX(10);
            $pdf->Cell(5,$textypos,"Direccion: ");
            $pdf->SetFont('Arial','',10);    
            $pdf->setY(60);$pdf->setX(30);
            $pdf->MultiCell( 100, 5, utf8_decode($data['Cliente']['direccion']), 0, 'L');
            
            // NRO DE FACTURA
            $elec = (($data['id_tdoc'] == 1 || $data['id_tdoc'] == 2) && $data['Empresa']['sunat'] == 1) ? 'ELECTRONICA' : '';
            $pdf->SetLineWidth(0.1); $pdf->SetFillColor(500); 
            $pdf->Rect(120, 10, 85, 30);
            $pdf->SetXY( 120, 15 ); 
            $pdf->SetFont( "Arial", "B", 12 ); 
            $pdf->Cell( 85, 4, 'R.U.C. Nro. '.utf8_decode($data['Empresa']['ruc']), 0, 0, 'C');
            $pdf->SetXY( 120, 15 ); 
            $pdf->Cell( 85, 20, $data['desc_td'].' '.$elec, 0, 0, 'C');
            $pdf->SetXY( 120, 15 ); 
            $pdf->Cell( 85, 36, $data['ser_doc'].'-'.$data['nro_doc'] , 0, 0, 'C');
            
            $pdf->SetFont('Arial','B',10);    
            $pdf->setY(55);$pdf->setX(135);
            $pdf->Cell(5,$textypos,"F. Emision: ");
            $pdf->SetFont('Arial','',10);    
            $pdf->setY(55);$pdf->setX(168);
            $pdf->Cell(5,$textypos, date('d-m-Y h:i A',strtotime($data['fec_ven'])));
            
            $pdf->SetFont('Arial','B',10); 
            $pdf->setY(60);$pdf->setX(135);
            $pdf->Cell(5,$textypos,"Moneda: ");
            $pdf->SetFont('Arial','',10);    
            $pdf->setY(60);$pdf->setX(168);
            $pdf->Cell(5,$textypos,"SOLES");
            
            /// Apartir de aqui empezamos con la tabla de productos
            $pdf->setY(70);$pdf->setX(135);
            $pdf->Ln();
            /////////////////////////////
            //// Array de Cabecera
            
            $header = array("Descripcion","Cant.","P. Unitario","Importe");
            //// Arreglo de Productos
            
                // Column widths
                $w = array(126, 20, 25, 25);
                // Header, el numero 7 define el alto de la cabecera
                $pdf->SetFont('Arial','B',10); 
                for($i=0;$i<count($header);$i++)
                    
                $pdf->Cell($w[$i],7,$header[$i],1,0,'C');
                $pdf->Ln();
                // Data
                $pdf->SetFont('Arial','',10);
                
                $total = 0;
                $total_ope_gravadas = 0;
                $total_igv_gravadas = 0;
                $total_ope_exoneradas = 0;
                $total_igv_exoneradas = 0;
                foreach($data['Detalle'] as $row)
                {
                    if($row['codigo_afectacion'] == '10'){
                        $total_ope_gravadas = $total_ope_gravadas + $row['valor_venta'];
                        $total_igv_gravadas = $total_igv_gravadas + $row['total_igv'];
                        $total_ope_exoneradas = $total_ope_exoneradas + 0;
                        $total_igv_exoneradas = $total_igv_exoneradas + 0;
                    } else{
                        $total_ope_gravadas = $total_ope_gravadas + 0;
                        $total_igv_gravadas = $total_igv_gravadas + 0;
                        $total_ope_exoneradas = $total_ope_exoneradas + $row['valor_venta'];
                        $total_igv_exoneradas = $total_igv_exoneradas + $row['total_igv'];
                    }

                    $pdf->Cell($w[0],6,utf8_decode($row['nombre_producto']),1);
                    $pdf->Cell($w[1],6,number_format($row['cantidad']),'1',0,'C');
                    $pdf->Cell($w[2],6,"S/  ".number_format($row['precio_unitario'],2,".",","),'1',0,'R');
                    $pdf->Cell($w[3],6,"S/  ".number_format($row['cantidad']*$row['precio_unitario'],2,".",","),'1',0,'R');
            
                    $pdf->Ln();
                    //// aqui multiplica la cantidad por elprecio unitario
                    $total = ($row['cantidad'] * $row['precio_unitario']) + $total;
                }
            /////////////////////////////
            //// Apartir de aqui esta la tabla con los subtotales y totales
            //$yposdinamic = 70 + (count($products)*10);
            
            $pdf->setX(215);
            $pdf->Ln();
            /////////////////////////////
            
            /*
            $operacion_gravada = (($data['total'] + $data['comis_tar'] + $data['comis_del'] - $data['desc_monto']) / (1 + $data['igv']));
            $igv = ($operacion_gravada * $data['igv']);
            */
            //$header = array("", "");
            $data2 = array(
                array("Subtotal",number_format(($data['total']),2)),
                array("Costo delivery", number_format(($data['comis_del']),2)),
                array("Descuento", number_format(($data['desc_monto']),2)),
                array("Operacion Gravada", number_format(($total_ope_gravadas),2)),
                array("Operacion Exonerada", number_format(($total_ope_exoneradas),2)),
                array("IGV", number_format(($total_igv_gravadas + $total_igv_exoneradas),2)),
                array("Importe Total", number_format(($data['total'] + $data['comis_del'] - $data['desc_monto']),2)),
            );
                // Column widths
                $w2 = array(40, 36);
                // Header
            
                $pdf->Ln();
                // Data
                foreach($data2 as $row)
                {
                    $pdf->setX(130);
                    $pdf->Cell($w2[0],6,$row[0],1);
                    $pdf->Cell($w2[1],6,"S/ ".number_format($row[1], 2, ".",","),'1',0,'R');
                    $pdf->Ln();
                }
            /////////////////////////////
            
            //$yposdinamic += (count($data2)*10);
            $pdf->SetFont('Arial','B',10);    
            $pdf->setX(10);
            $pdf->Ln();
            $pdf->Cell(5,$textypos,'SON: '.numtoletras($data['total'] + $data['comis_del'] - $data['desc_monto']));
            $pdf->SetFont('Arial','',8);
            $pdf->Ln(5);
            $pdf->setX(10);
            $pdf->Cell(5,$textypos,utf8_decode('Designado Emisor Electrónico según Resolución de Superintendecia Nro.155-2017/SUNAT.'),'C');
            $pdf->Ln(5);
            $pdf->setX(10);
            $pdf->Cell(5,$textypos,utf8_decode('Representación impresa de '.$data['desc_td'].' '.$elec));
            $pdfdoc = $pdf->Output('', 'S');

            $mail = new PHPMailer(true);
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            
            $mail->CharSet = 'UTF-8';
            
            $mail->Host = 'mail.braintech.com.pe';
            $mail->SMTPAuth = true;

            $mail->Username = 'no-responder@braintech.com.pe';
            $mail->Password = 'K^h;E]bTnkk=';

            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
            
            $xml = $documento_cliente;
            $fxml = file_get_contents($xml);
            $mail->addStringAttachment($fxml, utf8_decode($data['Empresa']['ruc']).'-'.$data['ser_doc'].'-'.$data['nro_doc'].'.XML');
            
            //$mail->addStringAttachment($pdfdoc, 'my-doc.pdf');
            $mail->AddStringAttachment($pdfdoc, utf8_decode($data['Empresa']['ruc']).'-'.$data['ser_doc'].'-'.$data['nro_doc'].'.pdf', 'base64', 'application/pdf');
                
            ## MENSAJE A ENVIAR
            $mail->setFrom('no-responder@braintech.com.pe', $negocio);
            $mail->addAddress($correo_cliente);

            $mail->isHTML(true);
            $mail->Subject = 'Estimado cliente adjuntamos los documentos electrónicos';
            $mail->Body = 'Gracias por su compra.';

            $mail->send();
            echo json_encode(1);

        } catch (Exception $exception) {
            echo 'Error:', $exception->getMessage();
            echo json_encode(2);
        }
    }
}
