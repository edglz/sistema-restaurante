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
            <li class="breadcrumb-item active">Personal</li>
        </ol>
    </div>
</div>
<div class="row">
    <!-- Column -->
    <div class="col-lg-4">
        <div class="card">   
            <div class="card-body">
                <div class="row floating-labels m-t-30">
                    <div class="form-group col-lg-12 m-b-40">
                        <select class="selectpicker form-control" name="filtro_personal" id="filtro_personal" data-style="form-control btn-default" data-live-search="true" title="Seleccionar" autocomplete="off" data-size="5">
                            <optgroup>
                                <?php foreach($this->Personal as $key => $value): ?>
                                    <option value="<?php echo $value['id_usu']; ?>"><?php echo $value['nombres'].' '.$value['ape_paterno'].' '.$value['ape_materno']; ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                        <span class="bar"></span>
                        <label for="filtro_personal">Personal</label>
                    </div> 
                    <div class="col-sm-12">
                        <div class="form-group m-b-40">
                            <input type="text" class="form-control font-14 text-left" name="start" id="start" value="<?php echo '01-'.$fechaa.' AM'; ?>" autocomplete="off"/>
                            <span class="bar"></span>
                            <label for="start">Inicio</label>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group m-b-10">
                            <input type="text" class="form-control text-left" name="end" id="end" value="<?php echo $fecha; ?>" autocomplete="off">
                            <span class="bar"></span>
                            <label for="end">Final</label>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <hr class="m-b-0"> 
            </div>
            <div class="card-body p-t-20 bg-light">
                <div class="card card-inverse card-info m-t-0 m-b-0">
                    <div class="box text-center">
                        <h1 class="font-light text-white m-t-10"><?php echo Session::get('moneda'); ?><span class="monto-global">0.00</span></h1>
                        <h6 class="text-white">Monto global</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="col-lg-8">
    
            <div class="card card-body p-0">
                <ul class="nav nav-tabs justify-content-end customtab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab1" role="tab" aria-selected="true"><span class="hidden-sm-up">Egresos</span> <span class="hidden-xs-down">Egresos</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab2" role="tab" aria-selected="false"><span class="hidden-sm-up">Ventas al credito</span> <span class="hidden-xs-down">Ventas al credito</span></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab1" role="tabpanel">
                        <div class="row text-center m-t-0 p-l-10 p-r-10">
                            <div class="col-6 m-t-20">
                                <h3 class="m-b-0 font-normal ventas-operaciones-1">0</h3>
                                <h6 class="font-bold m-b-10">N° Operaciones</h6>
                            </div>
                            <div class="col-6 m-t-20">
                                <h3 class="m-b-0 font-normal"><?php echo Session::get('moneda'); ?><span class=" ventas-total-1">0.00</span></h3>
                                <h6 class="font-bold m-b-10">Total</h6>
                            </div>
                        </div>
                        <hr class="m-0">
                        <div class="p-0">
                            <div class="table-responsive m-b-10">
                                <table id="table-1" class="table table-condensed table-hover stylish-table" cellspacing="0" width="100%">
                                    <thead class="table-head">
                                        <th style="width: 15%;">Fecha</th>
                                        <th style="width: 25%;">Responsable</th>
                                        <th style="width: 50%;">Motivo</th>
                                        <th style="width: 10%; text-align: right;">Monto</th>
                                    </thead>
                                    <tbody class="tb-st"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab2" role="tabpanel">
                        <div class="row text-center m-t-0 p-l-10 p-r-10">
                            <div class="col-6 m-t-20">
                                <h3 class="m-b-0 font-normal ventas-operaciones-2">0</h3>
                                <h6 class="font-bold m-b-10">N° Operaciones</h6>
                            </div>
                            <div class="col-6 m-t-20">
                                <h3 class="m-b-0 font-normal"><?php echo Session::get('moneda'); ?><span class=" ventas-total-2">0.00</span></h3>
                                <h6 class="font-bold m-b-10">Total</h6>
                            </div>
                        </div>
                        <hr class="m-0">
                        <div class="p-0">
                            <div class="table-responsive m-b-10">
                                <table id="table-2" class="table table-condensed table-hover stylish-table" cellspacing="0" width="100%">
                                    <thead class="table-head">
                                        <th style="width: 15%;">Fecha</th>
                                        <th style="width: 25%;">Responsable</th>
                                        <th style="width: 50%;">Documento</th>
                                        <th style="width: 10%; text-align: right;">Monto</th>
                                    </thead>
                                    <tbody class="tb-st"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    
    </div>
</div>
<!-- Row -->