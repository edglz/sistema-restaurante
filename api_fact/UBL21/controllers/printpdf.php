<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "../plugins/dompdf/vendor/autoload.php";
require "../plugins/phpqrcode/qrlib.php";

use Dompdf\Dompdf;

include "documentos_html.php";

class Printpdf {

    public function get_factura($emisor,$head,$detail) {
        /*         * *** FACTURA: DATOS OBLIGATORIOS PARA EL CÓDIGO QR **** */
        /* RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE EMISION |TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE | */
        $text_qr = '2660130746|03|f001|479397|18|118|26/10/2018|6|55887744|';
        $ruta_qr = "../archivos_xml_sunat/imgqr/" . $id . ".png";
        $file = "../archivos_xml_sunat/cpe_xml/beta/".$emisor['ruc']."/" . $emisor['ruc'] . "-".$head['COD_TIPO_DOCUMENTO']."-".$head['NRO_COMPROBANTE'].".pdf";

        QRcode::png($text_qr, $ruta_qr, 'Q', 15, 0);
        $html_documentos = new Documentos_html();
        $html = $html_documentos->get_html_factura('');

        define("DOMPDF_ENABLE_REMOTE", true);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html['html']);
        $dompdf->setPaper('A4');
        $dompdf->render();
        //$dompdf->stream("factura_n_" . $id . ".pdf");
        
        $pdf = $dompdf->output();
        file_put_contents($file, $pdf);
    }

    public function get_boleta($id) {
        $html_documentos = new Documentos_html();
        $html = $html_documentos->get_html_boleta('');

        define("DOMPDF_ENABLE_REMOTE", true);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html['html']);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream("boleta_n_" . $id . ".pdf");
    }

    public function get_notacredito($id) {
        $html_documentos = new Documentos_html();
        $html = $html_documentos->get_html_nota_credito('');

        define("DOMPDF_ENABLE_REMOTE", true);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html['html']);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream("notacredito_n_" . $id . ".pdf");
    }

    public function get_notadebito($id) {
        $html_documentos = new Documentos_html();
        $html = $html_documentos->get_html_nota_debito('');

        define("DOMPDF_ENABLE_REMOTE", true);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html['html']);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream("notadebito_n_" . $id . ".pdf");
    }

}

?>