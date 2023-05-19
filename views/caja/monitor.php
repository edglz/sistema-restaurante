<?php Session::init(); if (Session::get('rol') == 1 OR Session::get('rol') == 2 OR Session::get('rol') == 3) { ?>
<?php
date_default_timezone_set($_SESSION["zona_horaria"]);
setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
$fecha = date("d-m-Y h:i A");
$codigo_anular_venta = '04'.date('dm');
?>
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<input type="hidden" id="codRol" value="<?php echo Session::get('rol'); ?>"/>
<input type="hidden" id="tribAcr" value="<?php echo Session::get('tribAcr'); ?>"/>
<input type="hidden" id="diAcr" value="<?php echo Session::get('diAcr'); ?>"/>
<input type="hidden" id="id_apc" value=""/>
<input type="hidden" id="codigo_anular_venta" value="<?php echo $codigo_anular_venta; ?>"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Caja</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>caja/monitor" class="link">Inicio</a></li>
            <li class="breadcrumb-item active">Monitor de ventas</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-0 display-two" style="display: block;">
                <ul class="nav nav-tabs customtab" role="tablist">
                    <li class="nav-item"> <a class="nav-link list-mesas active" data-toggle="tab" href="#tab1" role="tab"><span class="hidden-sm-up">Mesas por cobrar</span> <span class="hidden-xs-down">Mesas por cobrar</span></a> </li>
                    <li class="nav-item"> <a class="nav-link list-ventas" data-toggle="tab" href="#tab2" role="tab"><span class="hidden-sm-up">Lista de ventas</span> <span class="hidden-xs-down">Lista de ventas</span></a> </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane p-0 active" id="tab1" role="tabpanel">
                        <div class="row text-center m-t-0 p-l-10 p-r-10">
                            <div class="col-6 m-t-20">
                                <h3 class="m-b-0 font-normal mesas-operaciones"></h3>
                                <h6 class="font-bold m-b-10">N° Mesas por cobrar</h6>
                            </div>
                            <div class="col-6 m-t-20">
                                <h3 class="m-b-0 font-normal mesas-total"></h3>
                                <h6 class="font-bold m-b-10">Monto pendiente por cobrar</h6>
                            </div>
                            <div class="col-md-12 m-b-10"></div>
                        </div>
                        <div class="table-responsive m-b-10 b-t">
                            <table id="table-mesas" class="table table-condensed table-hover stylish-table" style="margin-top: 0px !important;" width="100%">
                                <thead class="table-head">
                                    <tr>
                                        <th>#</th>
                                        <th>Pedido</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="tb-st"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane p-0" id="tab2" role="tabpanel">
                        <div class="row text-center m-t-0 p-l-10 p-r-10">
                            <div class="col-6 m-t-20">
                                <h3 class="m-b-0 font-normal ventas-operaciones"></h3>
                                <h6 class="font-bold m-b-10">N° Operaciones</h6>
                            </div>
                            <div class="col-6 m-t-20">
                                <h3 class="m-b-0 font-normal ventas-total"></h3>
                                <h6 class="font-bold m-b-10">Total</h6>
                            </div>
                            <div class="col-md-12 m-b-10"></div>
                        </div>
                        <div class="table-responsive m-b-10 b-t">
                            <table id="table-ventas" class="table table-condensed table-hover stylish-table" style="margin-top: 0px !important;" width="100%">
                                <thead class="table-head">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Documento</th>
                                        <th>Cliente</th>
                                        <th>Canal venta</th>
                                        <th>Tipo pago</th>
                                        <th class="text-right">Total</th>
                                        <th class="text-right">Opciones</th>
                                    </tr>
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

<div class="modal inmodal" id="modal-editar-pago" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated bounceInTop">
        <form id="form-editar-pago" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_venta" id="id_venta" class="id_venta"/>
            <input type="hidden" name="id_venta_tipopago" id="id_venta_tipopago"/>
            <div class="modal-header b-0">
                <h4 class="modal-title">Editar tipo de pago</h4>
            </div>
            <div class="modal-body">
                <div class="row floating-labels m-t-40">
                    <div class="col-sm-12 display-tipo-pago floating-labels" style="display: block">
                        <div class="form-group m-b-40">
                            <select class="selectpicker form-control" name="id_tipo_pago_v" id="id_tipo_pago_v" data-style="form-control btn-default" data-live-search-style="begins" data-live-search="true">
                                <?php foreach($this->TipoPago as $key => $value): ?>
                                    <option label="<?php echo $value['id_pago']; ?>" value="<?php echo $value['id_tipo_pago']; ?>"><?php echo $value['descripcion']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="bar"></span>
                            <label for="id_tipo_pago_v">Tipo pago</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer b-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Aceptar</button>
            </div>
        </form>
        </div>
    </div>
</div>

<div class="modal inmodal" id="modal-editar-documento" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content animated bounceInTop">
        <form id="form-editar-documento" class="form-editar-documento" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_venta" id="id_venta" class="id_venta" />
            <div class="modal-header b-0">
                <h4 class="modal-title">Editar tipo de documento</h4>
            </div>
            <div class="modal-body">
                <div class="col-lg-12 m-b-20">
                    <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                        <label class="btn waves-effect waves-light btn-secondary btn-tipo-doc-1 active">
                            <input type="radio" name="tipo_doc" value="1" autocomplete="off">BOLETA
                        </label>
                        <label class="btn waves-effect waves-light btn-secondary btn-tipo-doc-2">
                            <input type="radio" name="tipo_doc" value="2" autocomplete="off"> FACTURA
                        </label>
                    </div>
                </div>
                <div class="col-lg-12">
                    <input type="hidden" name="cliente_id" id="cliente_id" value="1"/>
                    <input type="hidden" name="cliente_tipo" id="cliente_tipo" value="1"/>
                    <label class="font-13 text-inverse">Buscar cliente</label>
                    <div class="input-group">
                        <div class="opcion-cliente input-group-prepend"></div>                       
                        <input type="text" class="form-control" name="buscar_cliente" id="buscar_cliente" value="PUBLICO EN GENERAL" autocomplete="off">
                        <a class="input-group-append" href="javascript:void(0)" id="btnClienteLimpiar"data-original-title="Limpiar datos" data-toggle="tooltip" data-placement="top">
                            <span class="input-group-text bg-header">
                                <small><i class="fas fa-times link-danger"></i></small>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer b-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success" id="btn-submit-editar-documento">Aceptar</button>
            </div>
        </form>
        </div>
    </div>
</div>

<div class="modal inmodal" id="modal-cliente" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInTop">
            <form id="form-cliente" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_cliente" id="id_cliente">
            <input type="hidden" name="tipo_cliente" id="tipo_cliente">
            <div class="modal-header justify-center">
                <h4 class="modal-title-cliente">Nuevo Cliente</h4>
                <?php if(0 == 1){ ?>
                <div class="ml-auto m-t-10">
                    <input name="tipo_cli" type="radio" value="1" id="td_dni" class="with-gap radio-col-light-green"/>
                    <label for="td_dni"><?php echo Session::get('diAcr'); ?></label>
                    <input name="tipo_cli" type="radio" value="2" id="td_ruc" class="with-gap radio-col-light-green"/>
                    <label for="td_ruc"><?php echo Session::get('tribAcr'); ?></label>
                </div>
                <?php } else { ?>
                <div class="ml-auto m-t-10"></div>
                <?php } ?>
            </div>
            <div class="modal-body p-0 floating-labels">
                <div class="row" style="margin-left: 0px; margin-right: 0px;">
                    <!-- Column -->
                    <div class="col-lg-6 b-r">
                        <div class="row card-body p-0">
                            <div class="col-md-12 p-l-10 p-t-10 b-t b-b m-b-40 bg-light-info" style="display: flex;">
                                <h6 class="font-medium">Informaci&oacute;n personal</h6>
                            </div>                       
                            <div class="col-md-6 block01" style="display: block;">
                                <div class="form-group ent m-b-40">
                                    <input type="text" class="form-control dni" name="dni" id="dni" minlength="<?php echo Session::get('diCar'); ?>" maxlength="<?php echo Session::get('diCar'); ?>" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="dni" class="c-dni"><?php echo Session::get('diAcr'); ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 block02" style="display: none;">
                                <div class="form-group ent m-b-40">
                                    <input type="text" class="form-control ruc" name="ruc" id="ruc" minlength="<?php echo Session::get('tribCar'); ?>" maxlength="<?php echo Session::get('tribCar'); ?>" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="ruc" class="c-ruc"><?php echo Session::get('tribAcr'); ?></label>
                                </div>
                            </div>                            
                            <div class="col-md-12 block07" style="display: none;">
                                <div class="form-group letNumMayMin m-b-40">
                                    <input type="text" class="form-control ruc input-mayus" name="razon_social" id="razon_social" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="razon_social">Raz&oacute;n Social</label>
                                </div>
                            </div>
                            <div class="col-md-12 block03" style="display: block;">
                                <div class="form-group letMayMin m-b-40">
                                    <input type="text" class="form-control dni input-mayus" name="nombres" id="nombres" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="nombres">Nombre completo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-lg-6">
                        <div class="row card-body bg-header p-0">
                            <div class="col-md-12 p-l-10 p-t-10 b-t b-b m-b-40 bg-light-inverse" style="display: flex;">
                                <h6 class="font-medium">Contacto</h6>
                            </div> 
                            <div class="col-md-6 block05" style="display: block;">
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control bg-transparent dni input-mayus" name="fecha_nac" id="fecha_nac" value="" autocomplete="off" data-mask="99-99-9999"/>
                                    <span class="bar"></span>
                                    <label for="fecha_nac">Fecha de Nacimiento</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ent m-b-40">
                                    <input type="text" class="form-control bg-transparent" name="telefono" id="telefono" value="" autocomplete="off"/>
                                    <span class="bar"></span>
                                    <label for="telefono">Tel&eacute;fono</label>
                                </div>
                            </div>
                            <div class="col-md-12 block06" style="display: block;">
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control bg-transparent dni" name="correo" id="correo" value="" autocomplete="off"/>
                                    <span class="bar"></span>
                                    <label for="correo">Correo electr&oacute;nico</label>
                                </div>
                            </div>
                            <?php if(0 == 1){ ?>
                                <div class="col-md-12 block08" style="display: none;">
                            <?php }else{ ?>
                                <div class="col-md-12">
                            <?php } ?>
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control bg-transparent" name="direccion" id="direccion" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="direccion">Direcci&oacute;n</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control bg-transparent input-mayus" name="referencia" id="referencia" value="" autocomplete="off"/>
                                    <span class="bar"></span>
                                    <label for="referencia">Referencia</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Volver</button>
                <button type="submit" class="btn btn-success">Aceptar</button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>