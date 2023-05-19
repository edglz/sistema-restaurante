<?php

include "printpdf.php";
$print = new Printpdf();
$tipo_cpe = $_GET['tipo'];
$id = $_GET['id'];

if ($tipo_cpe == 'factura') {
    $print->get_factura($id);
}

if ($tipo_cpe == 'boleta') {
    $print->get_boleta($id);
}

if ($tipo_cpe == 'notadebito') {
    $print->get_notadebito($id);
}

if ($tipo_cpe == 'notacredito') {
    $print->get_notacredito($id);
}
?>