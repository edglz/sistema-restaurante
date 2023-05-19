<?php
date_default_timezone_set($_SESSION["zona_horaria"]);
setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
$fecha = date("d-m-Y");
$fechaa = date("m-Y");
?>
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Informes</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>informe" class="link">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>informe" class="link">Informe de ventas</a></li>
            <li class="breadcrumb-item active">Ventas por culqi</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body p-b-0">
                <div class="message-box contact-box">
                    <h2 class="add-ct-btn">
                    </h2>
                    <br>
                    <div class="row floating-labels m-t-5">
                        <div class="col-lg-3">
                            <div class="form-group m-b-40">
                                <div class="input-group">
                                    <input type="text" class="form-control font-14 text-center" name="start" id="start" value="<?php echo '01-'.$fechaa; ?>" autocomplete="off"/>
                                    <span class="input-group-text bg-gris">al</span>
                                    <input type="text" class="form-control font-14 text-center" name="end" id="end" value="<?php echo $fecha; ?>" autocomplete="off"/>
                                </div>
                                <label>Rango de fechas</label>
                            </div>
                        </div>
                        <div class="offset-7 col-lg-2">
                            <div class="form-group m-b-40">
                                <select class="selectpicker form-control" name="filtro_tipo_entrega" id="filtro_tipo_entrega" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5">
                                    <option value="%" active>Mostrar Todo</option>
                                    <optgroup>
                                        <option value="1">A DOMICILIO</option>
                                        <option value="2">POR RECOGER</option>
                                    </optgroup>
                                </select>
                                <span class="bar"></span>
                                <label for="filtro_tipo_entrega">Tipo Entrega</label>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
            <div class="text-center m-b-20">
                <div class="row">
                    <div class="col-lg-2 offset-1">
                        <h2 class="font-medium text-warning m-b-0 font-30 ventas-operaciones"></h2>
                        <h6 class="font-bold m-b-10">N° Operaciones</h6>                            
                    </div>
                    <div class="col-lg-2">
                        <h2 class="font-medium text-warning m-b-0 font-30 ventas-total"></h2>
                        <h6 class="font-bold m-b-10">Monto de Ventas</h6>
                    </div>
                    <div class="col-lg-2">
                        <h2 class="font-medium text-warning m-b-0 font-30 ventas-comision"></h2>
                        <h6 class="font-bold m-b-10">Total de Comisiones</h6>
                    </div>
                    <div class="col-lg-2">
                        <h2 class="font-medium text-warning m-b-0 font-30 ventas-igv"></h2>
                        <h6 class="font-bold m-b-10">Total IGV</h6>
                    </div>
                    <div class="col-lg-2">
                        <h2 class="font-medium text-warning m-b-0 font-30 ventas-recibir"></h2>
                        <h6 class="font-bold m-b-10">Total a Recibir</h6>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive b-t m-b-10">
                    <table id="table" class="table table-hover table-condensed stylish-table" width="100%">
                        <thead class="table-head">
                            <tr>
                                <th width="15%">Fecha operaci&oacute;n</th>
                                <th width="25%">Cliente</th>
                                <th width="20%">Comprobante</th>
                                <th class="text-right" width="10%">Monto</th>
                                <th class="text-right" width="10%">Comisi&oacute;n</th>
                                <th class="text-right" width="10%">IGV</th>
                                <th class="text-right" width="10%">Recibir</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>