<?php
$data_emp  = $this->empresa;
$data_receta = $this->receta;
require_once('public/lib/mpdf/mpdf.php');
$img = URL . 'public/images/'.$data_emp['logo'];
$mpdf = new mPDF();
$stylesheet = file_get_contents(URL.'public/pdfstyles/recetas.css');
$mpdf->WriteHTML($stylesheet, 1);
$mpdf->WriteHTML('
<table>
    <tr>
        <td align="center" width="800px"><img style="width: 300px;" src="'.$img.'"></td>
    </tr>
    <tr>
        <td align="center" width="800px">'.$data_emp['nombre_comercial']. ' ' . $data_emp['direccion_fiscal'].  '</td>
    </tr>
</table><br>');
$titulo = '<hr class="style-six"><h2 style="text-align:center;">'.$data_receta->nombre.'</h2<br>';
$producto = '<h3>PRODUCTO: '.$data_receta->Producto->nombre.'</h3><h3>CATEGORIA DE LA RECETA: '.$data_receta->catg->nombre.'</h3>';
$ingredientes = '<h3>Ingredientes:</h3><p>';
foreach($data_receta->ingredientes as $k => $d){
    $ingredientes .= $k + 1 . '.- ' . $d->oracion . '<br>';
}
$ingredientes .= '</p><br><hr><h1 style="text-align:center;">PREPARACIÓN</h1><br><br>';
$mpdf->WriteHTML($titulo);
$mpdf->watermark('LA PREVIA', 45, 96, 0.1);
$mpdf->WriteHTML($producto);
$mpdf->WriteHTML($ingredientes);
$mpdf->WriteHTML($data_receta->receta);
$receta_html = $data_receta->receta;
$mpdf->Output();