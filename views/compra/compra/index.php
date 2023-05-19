<?php
date_default_timezone_set($_SESSION["zona_horaria"]);
setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
$fecha = date("d-m-Y");
$fechaa = date("m-Y");
?>
<input type="hidden" id="igv" value="<?php echo number_format(Session::get('igv'),2); ?>"/>
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<input type="hidden" id="url" value="<?php echo URL; ?>"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Compras</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>compra" class="link">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>compra" class="link">Todas las compras</a></li>
            <li class="breadcrumb-item active">Nueva compra</li>
        </ol>
    </div>
</div>
<form id="form-compra" method="post">
<div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body p-b-0">
                <h4 class="card-title">Datos generales</h4>
                <div class="row floating-labels m-t-40">
                    <div class="col-md-12">
                        <div class="form-group m-b-40">
                            <select id="id_tipo_compra" name="id_tipo_compra" class="selectpicker form-control" data-style="form-control btn-default" title="Seleccionar" required="required">
                                <option value="1">CONTADO</option>
                                <option value="2">CREDITO</option>
                            </select>
                            <span class="bar"></span>
                            <label for="id_tipo_compra">Tipo</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group m-b-40">
                            <select id="id_tipo_doc" name="id_tipo_doc" class="selectpicker form-control" data-style="form-control btn-default" title="Seleccionar" required="required">
                                <option value="1">BOLETA</option>
                                <option value="2">FACTURA</option>
                            </select>
                            <span class="bar"></span>
                            <label for="id_tipo_doc">Documento</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group m-b-40">
                            <input type="text" name="serie_doc" id="serie_doc" class="form-control" autocomplete="off" required="required">
                            <span class="bar"></span>
                            <label for="serie_doc">Serie</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group m-b-40 ent">
                            <input type="text" name="num_doc" id="num_doc" class="form-control" autocomplete="off" required="required">
                            <span class="bar"></span>
                            <label for="num_doc">N&uacute;mero</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group c-f m-b-40">
                            <input type="text" name="fecha_c" id="fecha_c" class="form-control f" autocomplete="off" required="required">
                            <span class="bar"></span>
                            <label for="fecha_c">Fecha</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group m-b-40">
                            <input type="text" name="hora_c" id="hora_c" class="form-control f" autocomplete="off" required="required">
                            <span class="bar"></span>
                            <label for="hora_c">Hora</label>
                        </div>
                    </div>
                </div>
            </div>
            <div><hr class="m-t-0 m-b-0"></div>
            <div class="card-body">
                <div class="d-flex flex-wrap">
                    <h4 class="card-title">Proveedor</h4>
                    <div class="ml-auto"><button type="button" class="btn btn-xs btn-danger" id="btnProvLimpiar"><i class="fas fa-eraser"></i></button> <button type="button" class="btn btn-xs btn-success" onclick="nuevoProveedor();"><i class="fas fa-plus"></i></button></div>
                </div>
                <div class="row floating-labels">
                    <div class="col-sm-12">
                        <div class="row floating-labels">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <input type="hidden" id="id_prov"/>
                                    <input type="text" id="buscar_proveedor" class="form-control" autocomplete="off"/>
                                    <span class="bar"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group m-b-20">
                            <input type="text" class="form-control bg-t" id="datos_proveedor" autocomplete="off" placeholder="Ingrese un proveedor" disabled>
                            <span class="bar"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title m-t-5">Detalle</h4>
                <h6 class="card-subtitle">B&uacute;squeda del producto o insumo</h6>
                <div class="row floating-labels">
                    <div class="col-sm-5">
                        <input id="id_tipo_ins_buscar" type="hidden" value="0" />
                        <input id="id_ins_buscar" type="hidden" value="0" />
                        <input autocomplete="off" id="buscar_insumo" class="form-control bg-t" type="text"/>
                        <span class="bar"></span>
                    </div>
                    <div class="col-sm-2 text-center" id="label-unidad-medida">U.M.</div>
                    <div class="col-sm-2 dec">
                        <input type="text" id="cantidad_buscar" class="form-control bg-t text-center" autocomplete="off" placeholder="Cantidad"/>
                        <span class="bar"></span>
                    </div>
                    <div class="col-sm-2 dec">
                        <input type="text" id="precio_buscar" class="form-control bg-t text-center" autocomplete="off" placeholder="P.U."/>
                        <span class="bar"></span>
                    </div>
                    <div class="col-sm-1">
                        <button class="btn btn-block btn-circle btn-orange" id="btn-agregar-insumo" type="button"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table stylish-table table-hover m-l-0 m-r-0 m-b-0" width="100%">
                        <thead class="table-head">
                            <tr>
                                <th>Cantidad</th>
                                <th>Producto/Insumo</th>
                                <th>U.M.</th>
                                <th class="text-center">P.U.</th>
                                <th class="text-center">Importe</th>
                                <th class="text-right">Acci&oacute;n</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st floating-labels" id="table-detalle"></tbody>
                    </table>
                </div>
            </div>
            <div><hr class="m-t-0 m-b-0"></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-5">
                        <div id="mensaje-credito" style="display: none;">
                            <div class="alert alert-warning m-b-0">Click <a class="alert-link link-credito-1">AQUI</a> para poder ingresar las cuotas de la compra.</div>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="row">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-3">
                                <div class="text-right">
                                    <span class="text-muted">SubTotal</span><br>
                                    <input type="hidden" id="subtotal_global" value="0"/>
                                    <h4 class="font-light"><?php echo Session::get('moneda'); ?> <span class="subtotal_global">0.00</span></h4>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-right floating-labels dec">
                                    <span class="text-muted text-desaum">Descuento</span><br>
                                    <input type="hidden" id="tipo_compra" value="1"/>
                                    <input type="text" id="total_descuento_aumento" class="form-control text-right" value="0.00" autocomplete="off"/>
                                    <span class="bar"></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-right">
                                    <span class="text-muted">Total</span><br>
                                    <h2 class="font-light"><?php echo Session::get('moneda'); ?> <span class="total_global">0.00</span></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <a class="btn btn-secondary" href="<?php echo URL; ?>compra"> Cancelar</a>
                <button class="btn btn-success">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal inmodal" id="modal-credito" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <h4 class="modal-title">Detalle de las cuotas</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="filtro_cuotas"/>
                <div class="row floating-labels m-t-20">
                    <div class="col-sm-6">
                        <div class="form-group m-b-40">
                            <input class="form-control bg-t total_credito_" type="text" id="total_credito" value="0.00" autocomplete="false" readonly="true">
                            <span class="bar"></span>
                            <label for="total_credito">Monto total</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group m-b-40 ent">
                            <input class="form-control" type="text" name="cuotas_credito" id="cuotas_credito" value="" autocomplete="false">
                            <span class="bar"></span>
                            <label for="cuotas_credito">N&uacute;mero de cuotas</label>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Total</th>
                                <th class="text-right">Fecha de pago</th>
                            </tr>
                        </thead>
                        <tbody id="table-cuotas" class="display-flex dec"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success btn-aceptar-cuota">Aceptar</button>
            </div>
        </div>
    </div>
</div>
</form>
<div class="modal inmodal" id="modal-proveedor" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInTop">
            <div class="modal-header">
                <h4 class="modal-title">Nuevo Proveedor</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <form method="post" id="form-proveedor">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="row floating-labels m-t-20">
                            <div class="col-sm-6">
                                <div class="form-group m-b-40 ent">
                                    <input type="text" name="ruc" id="ruc" minlength="<?php echo $_SESSION["tribCar"]; ?>" maxlength="<?php echo $_SESSION["tribCar"]; ?>" class="form-control" autocomplete="off" required/>
                                    <span class="bar"></span>
                                    <label for="ruc" class="c-ruc"><?php echo $_SESSION["tribAcr"]; ?></label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group m-b-40 letNumMayMin">
                                    <input type="text" name="razon_social" id="razon_social" class="form-control input-mayus" autocomplete="off" required/>
                                    <span class="bar"></span>
                                    <label for="razon_social">Raz&oacute;n Social</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group m-b-40 letNumMayMin">
                                    <input type="text" name="direccion" id="direccion" class="form-control" autocomplete="off" required/>
                                    <span class="bar"></span>
                                    <label for="direccion">Direcci&oacute;n</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group m-b-40 ent">
                                    <input type="text" name="telefono" id="telefono" class="form-control" autocomplete="off"/>
                                    <span class="bar"></span>
                                    <label for="telefono">Tel&eacute;fono</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group m-b-20">
                                    <input type="text" name="email" id="email" class="form-control" autocomplete="off"/>
                                    <span class="bar"></span>
                                    <label for="email">Correo electr&oacute;nico</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group m-b-20 letNumMayMin">
                                    <input type="text" name="contacto" id="contacto" class="form-control" autocomplete="off"/>
                                    <span class="bar"></span>
                                    <label for="contacto">Contacto</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Aceptar</button>
            </div>
            </form>
        </div>
    </div>
</div>
<script id="table-detalle-template" type="text/x-jsrender" src="">
    {{for items}}
    <tr class="warning-element active">
        <td style="width: 10%;">
            <input name="cantidad_insumo" class="form-control bg-t" type="text" value="{{:cantidad_insumo}}" style="text-align:center;" autocomplete="off" onchange="compra.actualizar({{:id}}, this);"/>
            <small class="bar"></small>
        </td>
        <td style="width: 40%;">
            <input name="id_tipo_ins_insumo" type="hidden" value="{{:id_tipo_ins_insumo}}" />
            <input name="id_ins_insumo" type="hidden" value="{{:id_ins_insumo}}" />
            <h6 name="nombre_insumo">{{:nombre_insumo}}</h6>
        </td>
        <td style="width: 10%;">
            <span class="label label-warning text-uppercase" name="unidad_medida_insumo">{{:unidad_medida_insumo}}</span>
        </td>
        <td style="width: 15%;" class="dec">
            <input name="precio_insumo" class="form-control bg-t" type="text" style="text-align:center;" value="{{:precio_insumo}}" onchange="compra.actualizar({{:id}}, this);" autocomplete="off"/>
            <small class="bar"></small>
        </td>
        <td style="width: 15%;">
            <input type="text" name="importe" class="form-control bg-t" style="text-align:center;" value="{{:total}}" disabled="true"/>
        </td>
        <td class="text-right" style="width: 10%;">
            <a href="javascript:void(0)" class="text-danger delete ms-2" onclick="compra.retirar({{:id}});"><i data-feather="trash-2" class="feather-sm fill-white"></i></a>
        </td>
    </tr>
    {{/for}}
</script>