<?php
date_default_timezone_set($_SESSION["zona_horaria"]);
setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
$fecha = date("d-m-Y h:i A");
?>
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<input type="hidden" id="codRol" value="<?php echo Session::get('rol'); ?>"/>
<input type="hidden" id="stock_pollo" value=""/>
<input type="hidden" id="fechaC" value="<?php echo $fecha; ?>"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Caja</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>caja/apercie" class="link">Inicio</a></li>
            <li class="breadcrumb-item active">Apertura y cierre</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-7 col-md-12">
        <div class="card bg-header">
            <div class="row" style="margin-left: 0px; margin-right: 0px;">
                <!-- Column -->
                <div class="col-lg-6 b-r">
                    <div class="card-body">
                        <div class="row text-center display-apertura m-t-40 m-b-40" style="display: none;">
                            <div class="col-sm-8 offset-sm-2">
                                <h4 style="color: #d3d3d3;"><i class="mdi mdi-lock-outline display-3 m-t-40 m-b-10"></i>
                                </h4>
                                <span class="label label-danger label-close m-b-20">CERRADO</span><br>
                                <h6>Ingrese los datos,<br>para abrir una caja</h6>
                            </div>
                        </div>
                        <div class="row text-center m-t-30 m-b-40 display-cierre" style="display: none;">
                            <div class="col-sm-8 offset-sm-2">
                                <h4 style="color: #d3d3d3;"><i class="mdi mdi-lock-open-outline display-3 m-t-40 m-b-10"></i>
                                </h4>
                                <span class="label label-success label-open m-b-20">ABIERTO</span><br>
                                <h6 class="fecha-apertura"></h6>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Column -->
                <div class="col-lg-6 p-0">
                    <div class="p-0">
                        <form id="form-apertura" method="post" class="display-apertura" style="display: none;" enctype="multipart/form-data">
                            <div class="card-body bg-white" style="border-radius: .25rem;">
                                <div class="row m-t-30">
                                    <div class="col-sm-12 floating-labels">
                                        <div class="form-group m-b-40">
                                            <select class="selectpicker form-control" name="id_caja" id="id_caja" data-style="form-control btn-default" data-live-search-style="begins" data-live-search="true" title="Seleccionar" autocomplete="off" required="required">
                                                <?php foreach($this->Caja as $key => $value): ?>
                                                    <option value="<?php echo $value['id_caja']; ?>"><?php echo $value['descripcion']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <span class="bar"></span>
                                            <label for="id_caja">Caja</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 floating-labels">
                                        <div class="form-group m-b-20">
                                            <select class="selectpicker form-control" name="id_turno" id="id_turno" data-style="form-control btn-default" data-live-search-style="begins" data-live-search="true" title="Seleccionar" autocomplete="off" required="required">
                                                <?php foreach($this->Turno as $key => $value): ?>
                                                    <option value="<?php echo $value['id_turno']; ?>"><?php echo $value['descripcion']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <span class="bar"></span>
                                            <label for="id_turno">Turno</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group m-b-20 dec">
                                            <label class="font-13 text-inverse">INGRESE MONTO DE APERTURA</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white text-muted" style="display: grid;">
                                                        <small class="text-left">EFE</small>
                                                        <div class="text-left font-medium"><?php echo Session::get('moneda'); ?></div>
                                                    </span>
                                                </div>
                                                <input type="text" name="onlyNum" class="form-control form-control-lg" style="height: 58px;border-left: 0px; border-right: 0px;" name="monto_aper" id="monto_aper" value="" autocomplete="off" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-white text-muted"><?php echo Session::get('monAcr'); ?></span>
                                                </div>
                                            </div>                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <button type="submit" class="btn btn-success btn-block btn-aceptar-apertura">ABRIR CAJA</button>
                            </div>
                        </form>
                        <form id="form-cierre" method="post" class="display-cierre" style="display: none;" enctype="multipart/form-data">
                            <input type="hidden" name="id_apc" id="id_apc">
                            <input type="hidden" name="monto_sistema" id="monto_sistema">
                            <div class="card-body bg-white" style="border-radius: .25rem;">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group m-t-20 m-b-20 dec">
                                            <label class="font-13 text-inverse">INGRESE MONTO DE CIERRE</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white text-muted" style="display: grid;">
                                                        <small class="text-left">EFE</small>
                                                        <div class="text-left font-medium"><?php echo Session::get('moneda'); ?></div>
                                                    </span>
                                                </div>
                                                <input type="text" name="onlyNum" class="form-control form-control-lg" style="height: 58px;border-left: 0px; border-right: 0px;" name="monto_cierre" id="monto_cierre" value="" autocomplete="off" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-white text-muted"><?php echo Session::get('monAcr'); ?></span>
                                                </div>
                                            </div>                           
                                        </div>
                                        <span class="text-mute font-13 font-italic">(*) Considerar solo dinero en efectivo.</span>
                                    </div>
                                </div>
                                <div class="p-b-40"></div>
                                <div class="p-b-20"></div>
                            </div>
                            <div class="card-footer bg-white">
                                <button type="submit" class="btn btn-danger btn-block btn-aceptar-cierre">CERRAR CAJA</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>