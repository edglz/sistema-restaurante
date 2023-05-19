<?php
date_default_timezone_set($_SESSION["zona_horaria"]);
setlocale(LC_ALL, "es_ES@euro", "es_ES", "esp");
$fecha = date("d-m-Y h:i A");
$fechaa = date("m-Y 07:00");
?>
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>" />
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Informes</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>informe" class="link">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>informe" class="link">Caja</a></li>
            <li class="breadcrumb-item active">Monitor Impresiones</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body p-b-0"></div>
            <div class="text-center m-b-20">
                <div class="row">
                    <div class="col-12">
                        <h2 class="font-medium text-warning m-b-0 font-30 operaciones"></h2>
                        <h6 class="font-bold m-b-10">N° Operaciones</h6>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive b-t m-b-10">
                    <table id="table" class="table table-hover table-condensed stylish-table" width="100%">
                        <thead class="table-head">
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Impresora</th>
                                <th>Tipo Impresion</th>
                                <th>Usuario Encargado</th>
                                <th># Pedido</th>
                                <th>Detalle Pedido</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Operaciones</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>