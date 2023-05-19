<?php
date_default_timezone_set($_SESSION["zona_horaria"]);
setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
$fecha = date("d-m-Y h:i A");
$fechaa = date("m-Y 07:00");
?>
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Informes</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>informe" class="link">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>informe" class="link">Informe de ventas</a></li>
            <li class="breadcrumb-item active">Descuentos</li>
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
                        <div class="col-lg-4">
                            <div class="form-group m-b-20">
                                <div class="input-group">
                                    <input type="text" class="form-control font-14 text-center" name="start" id="start" value="<?php echo '01-'.$fechaa.' AM'; ?>" autocomplete="off"/>
                                    <span class="input-group-text bg-gris">al</span>
                                    <input type="text" class="form-control font-14 text-center" name="end" id="end" value="<?php echo $fecha; ?>" autocomplete="off"/>
                                </div>
                                <label>Rango de fechas</label>
                            </div>
                        </div>
                        <div class="col-lg-3 offset-lg-5">
                            <div class="form-group m-b-40">
                                <select class="selectpicker form-control" name="filtro_desc_tipo" id="filtro_desc_tipo" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5">
                                    <option value="%" active>Mostrar Todo</option>
                                    <optgroup>
                                        <option value="1">CORTESIA</option>
                                        <option value="2">DESCUENTO</option>
                                        <option value="3">CREDITO PERSONAL</option>
                                    </optgroup>
                                </select>
                                <span class="bar"></span>
                                <label for="filtro_desc_tipo">Tipo</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center m-b-20">
                <div class="row">
                    <div class="col-6">
                        <h2 class="font-medium text-warning m-b-0 font-30 descuentos-operaciones"></h2>
                        <h6 class="font-bold m-b-10">N° Operaciones</h6>                            
                    </div>
                    <div class="col-6">
                        <h2 class="font-medium text-warning m-b-0 font-30 descuentos-total"></h2>
                        <h6 class="font-bold m-b-10">Total de Descuentos</h6>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive b-t m-b-10">
                    <table id="table" class="table table-hover table-condensed stylish-table" width="100%">
                        <thead class="table-head">
                            <tr>
                                <th width="10%">Fecha</th>
                                <th width="15%">Documento</th>
                                <th width="10%">Tipo</th>
                                <th width="15%">Motivo</th>
                                <th width="20%">Responsable</th>
                                <th class="text-right" width="10%">TotConsumido</th>
                                <th class="text-right" width="10%">Descuento</th>
                                <th class="text-right" width="10%">TotVenta</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>