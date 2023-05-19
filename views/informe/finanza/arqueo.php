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
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>informe" class="link">Informe de finanzas</a></li>
            <li class="breadcrumb-item active">Aperturas y cierres</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <form method="post" enctype="multipart/form-data" target="_blank" action="#">
            <div class="card-body p-b-0">
                <div class="message-box contact-box">
                    <br>
                    <div class="row floating-labels m-t-5">
                        <div class="col-lg-4">
                            <div class="form-group m-b-40">
                                <div class="input-group">
                                    <input type="text" class="form-control font-14 text-center" name="start" id="start" value="<?php echo '01-'.$fechaa.' AM'; ?>" autocomplete="off"/>
                                    <span class="input-group-text bg-gris">al</span>
                                    <input type="text" class="form-control font-14 text-right" name="end" id="end" value="<?php echo $fecha; ?>" autocomplete="off"/>
                                </div>
                                <label>Rango de fechas</label>
                            </div>
                        </div>
                        <div class="col-sm-3 col-lg-3">
                            <div class="form-group m-b-40">
                                <input type="text" class="form-control global_filter" id="global_filter" autocomplete="off">
                                <span class="bar"></span>
                                <label for="global_filter">B&uacute;squeda</label>
                            </div>
                        </div>
                        <div class="col-sm-9 col-lg-5">
                            <div class="form-group m-b-40">
                                <select class="selectpicker form-control" name="filtro_cajero" id="filtro_cajero" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5">
                                    <option value="%" active>Mostrar Todo</option>
                                    <optgroup>
                                        <?php foreach($this->Cajero as $key => $value): ?>
                                            <option value="<?php echo $value['id_usu']; ?>"><?php echo $value['nombres'].' '.$value['ape_paterno'].' '.$value['ape_materno']; ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                                <span class="bar"></span>
                                <label for="filtro_cajero">Cajero</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>
            <div class="card-body p-0">
                <div class="table-responsive b-t m-b-10">
                    <table id="table" class="table table-hover table-condensed stylish-table" width="100%">
                        <thead class="table-head">
                            <tr>
                                <th style="width: 10%">C&oacute;digo</th>
                                <th style="width: 10%">Fech.Aper.</th>
                                <th style="width: 10%">Fech.Cier.</th>
                                <th style="width: 20%">Cajero</th>
                                <th style="width: 10%">Caja</th>
                                <th style="width: 20%">Turno</th>
                                <th style="width: 10%">Estado</th>
                                <th style="width: 10%" class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>