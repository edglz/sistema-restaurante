<?php Session::init(); if (Session::get('rol') == 1 OR Session::get('rol') == 2) { ?>
<?php
date_default_timezone_set($_SESSION["zona_horaria"]);
setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
$fecha = date("d-m-Y");
$fechaa = date("m-Y");
?>
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Inventario</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>inventario/kardex" class="link">Inicio</a></li>
            <li class="breadcrumb-item active">Kardex valorizado</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body p-b-0">
                <div class="message-box contact-box">
                    <br>
                    <div class="row floating-labels m-t-5">
                        <div class="col-sm-3">
                            <div class="form-group m-b-40">
                                <div class="input-group">
                                    <input type="text" class="form-control font-14 text-center" name="start" id="start" value="<?php echo '01-'.$fechaa; ?>" autocomplete="off"/>
                                    <span class="input-group-text bg-gris">al</span>
                                    <input type="text" class="form-control font-14 text-center" name="end" id="end" value="<?php echo $fecha; ?>" autocomplete="off"/>
                                </div>
                                <label>Rango de fechas</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group s m-b-40">
                                <select class="selectpicker form-control" name="tipo_ip" id="tipo_ip" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5">
                                    <option value="1">INSUMO</option>
                                    <option value="2">PRODUCTO</option>
                                </select>
                                <span class="bar"></span>
                                <label for="tipo_ip">Tipo</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group s m-b-40">
                                <select class="selectpicker form-control" name="id_ip" id="id_ip" data-style="form-control btn-default" data-live-search="true" autocomplete="off" title="Seleccionar" data-size="5">
                                </select>
                                <span class="bar"></span>
                                <label for="id_ip">Producto/Insumo</label>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
            <div class="text-center m-b-10">
                <div class="row">
                    <div class="col-3">                        
                        <h2 class="font-medium m-b-0 font-30 stock-inicial">0.0000</h2>
                        <h6 class="font-bold m-b-10">Stock inicial</h6>
                    </div>
                    <div class="col-3">                        
                        <h2 class="text-success font-medium m-b-0 font-30 stock-entradas">0.0000</h2>
                        <h6 class="font-bold m-b-10">Cantidad de entradas</h6>
                    </div>
                    <div class="col-3">
                        <h2 class="text-danger font-medium m-b-0 font-30 stock-salidas">0.0000</h2>
                        <h6 class="font-bold m-b-10">Cantidad de salidas</h6>
                    </div>
                    <div class="col-3">
                        <h2 class="font-medium m-b-0 font-30 stock-final">0.0000</h2>
                        <h6 class="font-bold m-b-10">Stock final</h6>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive m-b-10">
                    <table id="table" class="table table-hover table-condensed stylish-table" width="100%">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 10%; vertical-align: bottom; border-bottom: 2px solid #c3c3c3 !important;">Fecha</th>
                                <th rowspan="2" style="width: 30%; vertical-align: bottom; border-bottom: 2px solid #c3c3c3 !important;">Concepto</th>
                                <th colspan="3" style="text-align: center;border-bottom: 1px solid #8cd16e !important;" class="border-bottom text-success" class="text-success">Entrada</th>
                                <th colspan="3" style="text-align: center;border-bottom: 1px solid #fc4b6c !important;" class="border-bottom text-danger" class="text-danger">Salida</th>
                                <th colspan="3" style="text-align: center;border-bottom: 1px solid #00c0f1 !important;" class="border-bottom text-info" class="text-info">Saldo</th>
                            </tr>
                            <tr>
                                <th class="text-success" style="text-align: center;border-bottom: 2px solid #8cd16e !important;">Cantidad</th>
                                <th class="text-success" style="text-align: center;border-bottom: 2px solid #8cd16e !important;">C.U.</th>
                                <th class="text-success" style="text-align: center;border-bottom: 2px solid #8cd16e !important;">Total</th>
                                <th class="text-danger" style="text-align: center;border-bottom: 2px solid #fc4b6c !important;">Cantidad</th>
                                <th class="text-danger" style="text-align: center;border-bottom: 2px solid #fc4b6c !important;">C.U.</th>
                                <th class="text-danger" style="text-align: center;border-bottom: 2px solid #fc4b6c !important;">Total</th>
                                <th class="text-info" style="text-align: center;border-bottom: 2px solid #00c0f1 !important;">Cantidad</th>
                                <th class="text-info" style="text-align: center;border-bottom: 2px solid #00c0f1 !important;">C.U.</th>
                                <th class="text-info" style="text-align: center;border-bottom: 2px solid #00c0f1 !important;">Total</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>