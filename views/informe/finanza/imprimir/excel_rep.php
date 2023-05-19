<?php
header('Content-Type: application/xls');
header('Content-Disposition: attachment; filename="reporte_ap_cod'.$this->Resumen->id_apc.'_'.date('Y-m-d H:i:s').'.xls" ');
header('Pagma: no-cache');
header('Expires: 0');
?>
<style type="text/css">
.tg {
    border-collapse: collapse;
    border-spacing: 0;
}

.tg td {
    border-color: black;
    border-style: solid;
    border-width: 1px;
    font-family: Arial, sans-serif;
    font-size: 14px;
    overflow: hidden;
    padding: 10px 5px;
    word-break: normal;
}

.tg th {
    border-color: black;
    border-style: solid;
    border-width: 1px;
    font-family: Arial, sans-serif;
    font-size: 14px;
    font-weight: normal;
    overflow: hidden;
    padding: 10px 5px;
    word-break: normal;
}

.tg .tg-0pky {
    border-color: inherit;
    text-align: center;
    vertical-align: top
}

.tg .tg-0lax {
    text-align: center;
    vertical-align: top
}

table th,
th,
td {
    font-size: 15px !important;
    font-weight: bold !important;
    font-family: Arial, Helvetica, sans-serif !important;
    text-align: center !important;
}

</style>


<?php 
if($this->Resumen->estado == 'a'){$fecha_cierre = '';}else{$fecha_cierre = date('d-m-Y h:i A',strtotime($this->Resumen->fecha_cierre));}
$detalle_emp = "MUNDO HOLISTICO S.R.L.<br>
Ruc:20605905367<br>
JR. PUNO NRO. 714 HUANCAYO CERCADO <br>
JUNIN - HUANCAYO - HUANCAYO";
$id_apc = 'CORTE DE TURNO #COD0'.$this->dato->id_apc;
$c = 0;
$s = 0.00;
$total = 0.00;
?>

<!--INICIO TABLA-->
<?php
foreach($this->Areas as $area){
    $total_Area = 0;
?>
<table class="tg" width="1000px"cellspacing="2" cellpadding="2">
    <thead>
       
        <?php 
            if($c == 0){
                $c++;
                ?> 
                 <tr>
                    <th colspan="6">DINAMO</th>
                 </tr>
                <tr>
                    <th colspan="6"><?php echo $detalle_emp; ?></th>
                </tr>
                <tr>
                    <th colspan="6">DE: <?php echo date('d-m-Y h:i A',strtotime($this->Resumen->fecha_aper)); ?></th>
                </tr>
                <tr>
                    <th colspan="6">A: <?php echo $fecha_cierre; ?></th>
                </tr>
                <tr>
                    <th colspan="6">COD APERTURA: <?php echo $this->dato->id_apc; ?></th>
                </tr>
                <tr>
                    <th colspan="6"><?php echo "TURNO: " .$this->Resumen->desc_turno; ?></th>
                </tr>
                <tr>
                    <th colspan="6"><?php echo "CAJERO: " . $this->Resumen->desc_per; ?></th>
                </tr>
            <?php }?>

            <tr>
                <th colspan="6">AREA DE PRODUCCION: <?php echo $area->nombre;?></th>
            </tr>
            <tr>
                <th colspan="6">PRODUCTOS VENDIDOS:</th>
            </tr>
            <tr>
                <td>ID</td>
                <td>PRODUCTO</td>
                <td>CANTIDAD</td>
                <td>PRECIO</td>
                <td>IMPORTE</td>
            </tr>
    </thead>
    <?php 
        foreach ($this->Resumen->Detalle as $DetalleProducto){
            if($DetalleProducto->Producto->id_areap == $area->id_areap){
                ?>
                <tbody>
                    <tr>
                        <td><?php echo $DetalleProducto->id_prod;?></td>
                        <td><?php echo $DetalleProducto->Producto->pro_nom . ' ' . $DetalleProducto->Producto->pro_pre; ?></td>
                        <td><?php echo $DetalleProducto->cantidad; ?></td>
                        <td><?php echo $_SESSION['moneda'] . ' '. number_format($DetalleProducto->precio, 2);?></td>
                        <td><?php echo $_SESSION['moneda'] . ' '. number_format(($DetalleProducto->precio * $DetalleProducto->cantidad), 2);?></td>
                    </tr>
                <?php
                $total_Area += ($DetalleProducto->precio * $DetalleProducto->cantidad);
            }
        }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="">TOTAL AREA DE PRODUCCION <?php echo $area->nombre;  echo ': ' .  $_SESSION['moneda'] . ' '. number_format($total_Area, 2); ?> </td>
    </tr>
    </tfoot>
</table>
<br><br><br><br>
<?php
$total += $total_Area;
}
?>
<table class="tg" width="1000px" cellspacing="2" cellpadding="2">
    <tr>
        <th>TOTAL VENTAS GENERAL: <?php echo $_SESSION['moneda'] . ' ' . number_format($total, 2); ?></th>
    </tr>
</table>