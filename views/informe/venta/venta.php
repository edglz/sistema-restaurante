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
            <li class="breadcrumb-item active">Todas las ventas</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body p-b-0">
                <div class="message-box contact-box">
                    <h2 class="add-ct-btn">
                        <span style="text-align:right;" id="btn-excel"></span>
                    </h2>
                    <br>
                    <div class="row floating-labels m-t-5">
                        <div class="col-lg-4">
                            <div class="form-group m-b-40">
                                <div class="input-group">
                                    <input type="text" class="form-control font-14 text-center" name="start" id="start" value="<?php echo '01-'.$fechaa.' AM'; ?>" autocomplete="off"/>
                                    <span class="input-group-text bg-gris">al</span>
                                    <input type="text" class="form-control font-14 text-center" name="end" id="end" value="<?php echo $fecha; ?>" autocomplete="off"/>
                                </div>
                                <label>Rango de fechas</label>
                            </div>
                        </div>
                        <div class="col-sm-4 col-lg-2">
                            <div class="form-group m-b-40">
                                <select class="selectpicker form-control" name="tipo_ped" id="tipo_ped" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5">
                                    <option value="%" active>Mostrar Todo</option>
                                    <optgroup>
                                        <?php foreach($this->TipoPedido as $key => $value): ?>
                                            <option value="<?php echo $value['id_tipo_pedido']; ?>"><?php echo $value['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                                <span class="bar"></span>
                                <label for="tipo_ped">Canal Venta</label>
                            </div>
                        </div>
                        <div class="col-sm-8 col-lg-2">
                            <div class="form-group m-b-40">
                                <select class="selectpicker form-control" name="cliente" id="cliente" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5">
                                    <option value="%" active>Mostrar Todo</option>
                                    <optgroup>
                                        <?php foreach($this->Cliente as $key => $value): ?>
                                            <option value="<?php echo $value['id_cliente']; ?>"><?php echo $value['nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                                <span class="bar"></span>
                                <label for="cliente">Cliente</label>
                            </div>
                        </div>                        
                        <div class="col-sm-6 col-lg-2">
                            <div class="form-group m-b-40">
                                <select class="selectpicker form-control" name="tipo_doc" id="tipo_doc" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5">
                                    <option value="%" active>Mostrar Todo</option>
                                    <optgroup>
                                        <?php foreach($this->TipoDocumento as $key => $value): ?>
                                            <option value="<?php echo $value['id_tipo_doc']; ?>"><?php echo $value['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                                <span class="bar"></span>
                                <label for="tipo_doc">Tipo Comprobante</label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-2">
                            <div class="form-group m-b-40">
                                <select class="selectpicker form-control" name="estado" id="estado" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5">
                                    <option value="%" active>Mostrar Todo</option>
                                    <optgroup>
                                        <option value="a">APROBADO</option>
                                        <option value="i">ANULADO</option>
                                    </optgroup>
                                </select>
                                <span class="bar"></span>
                                <label for="estado">Estado</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center m-b-20">
                <div class="row">
                    <div class="col-6">
                        <h2 class="font-medium text-warning m-b-0 font-30 ventas-operaciones"></h2>
                        <h6 class="font-bold m-b-10">N° Operaciones</h6>                            
                    </div>
                    <div class="col-6">
                        <h2 class="font-medium text-warning m-b-0 font-30 ventas-total"></h2>
                        <h6 class="font-bold m-b-10">Total de Ventas</h6>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive b-t m-b-10">
                    <table id="table" class="table table-hover table-condensed stylish-table" width="100%">
                        <thead class="table-head">
                            <tr>
                                <th width="15%">Fecha</th>
                                <th width="10%">Caja</th>
                                <th width="20%">Cliente</th>
                                <th width="15%">Documento</th>                               
                                <th width="15%">Canal venta</th>                               
                                <th class="text-right" width="10%">Total</th>
                                <th width="10%">Estado</th>
                                <th class="text-right" width="5%">Opciones</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal inmodal" id="detalle" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <h5 class="modal-title title-detalle">Detalle</h5>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table m-b-0">
                        <thead class="table-head">
                            <tr>
                                <th width="10%">Cant.</th>
                                <th width="60%">Producto</th>
                                <th width="15%">P.U.</th>
                                <th width="15%" class="text-right">Total</th>
                            </tr>
                        </thead>
                    </table>
                    <div class="table-responsive scroll_detalle">
                        <table class="table table-hover table-condensed">
                            <tbody class="tb-st" id="lista_pedidos"></tbody>
                        </table>
                    </div>
                    <table class="table m-b-0">
                        <tfooter>
                            <tr>
                                <td width="85%" class="text-right">Total consumido:</td>
                                <td class="total-consumido text-right"></td>
                            </tr>
                            <tr>
                                <td width="85%" class="text-right text-info">Comision delivery:</td>
                                <td class="total-comision text-right"></td></tr>
                            <tr>
                                <td width="85%" class="text-right text-danger">Descuento:</td>
                                <td class="total-descuento text-right"></td></tr>
                            <tr>
                                <td width="80%" class="text-right text-success">Total facturado:</td>
                                <td class="total-facturado text-right"></td></tr>
                        </tfooter>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
