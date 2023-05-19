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
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>informe" class="link">Informe de compras</a></li>
            <li class="breadcrumb-item active">Todas las compras</li>
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
                        <div class="form-group col-lg-3 m-b-40">
                            <div class="input-group">
                                <input type="text" class="form-control font-14 text-center" name="start" id="start" value="<?php echo '01-'.$fechaa; ?>" autocomplete="off"/>
                                <span class="input-group-text bg-gris">al</span>
                                <input type="text" class="form-control font-14 text-center" name="end" id="end" value="<?php echo $fecha; ?>" autocomplete="off"/>
                            </div>
                            <label>Rango de fechas</label>
                        </div>
                        <div class="form-group col-lg-3 m-b-40">
                            <select class="selectpicker form-control" name="filtro_proveedor" id="filtro_proveedor" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5">
                                <option value="%" active>Mostrar Todo</option>
                                <optgroup>
                                    <?php foreach($this->Proveedor as $key => $value): ?>
                                        <option value="<?php echo $value['id_prov']; ?>"><?php echo $value['ruc'].' - '.$value['razon_social']; ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                            <span class="bar"></span>
                            <label for="filtro_proveedor">Proveedor</label>
                        </div>
                        <div class="form-group col-sm-4 col-lg-2 m-b-40">
                            <select class="selectpicker form-control" name="filtro_tipo" id="filtro_tipo" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5">
                                <option value="%" active>Mostrar Todo</option>
                                <optgroup>
                                    <option value="1">CONTADO</option>
                                    <option value="2">CREDITO</option>
                                </optgroup>
                            </select>
                            <span class="bar"></span>
                            <label for="filtro_tipo">Tipo Pago</label>
                        </div>              
                        <div class="form-group col-sm-4 col-lg-2 m-b-40">
                            <select class="selectpicker form-control" name="filtro_documento" id="filtro_documento" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5">
                                <option value="%" active>Mostrar Todo</option>
                                <optgroup>
                                    <option value="1">BOLETA</option>
                                    <option value="2">FACTURA</option>
                                </optgroup>
                            </select>
                            <span class="bar"></span>
                            <label for="filtro_documento">Tipo Comprobante</label>
                        </div>  
                        <div class="form-group col-sm-4 col-lg-2 m-b-40">
                            <select class="selectpicker form-control" name="filtro_estado" id="filtro_estado" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5">
                                <option value="%" active>Mostrar Todo</option>
                                <optgroup>
                                    <option value="a">APROBADO</option>
                                    <option value="i">ANULADO</option>
                                </optgroup>
                            </select>
                            <span class="bar"></span>
                            <label for="filtro_estado">Estado</label>
                        </div>               
                    </div>
                </div>
            </div>
            <div class="text-center m-b-20">
                <div class="row">
                    <div class="col-6">
                        <h2 class="font-medium text-warning m-b-0 font-30 compras-operaciones"></h2>
                        <h6 class="font-bold m-b-10">N° Operaciones</h6>                            
                    </div>
                    <div class="col-6">
                        <h2 class="font-medium text-warning m-b-0 font-30 compras-total"></h2>
                        <h6 class="font-bold m-b-10">Total de Compras</h6>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive b-t m-b-10">
                    <table id="table" class="table table-hover table-condensed stylish-table" width="100%">
                        <thead class="table-head">
                            <tr>
                                <th style="width:10%;">Fech.Reg.</th>
                                <th style="width:10%;">Fech.Doc.</th>
                                <th style="width:15%;">Documento</th>
                                <th style="width:25%;">Proveedor</th>
                                <th style="width:10%;" class="text-right">Total</th>
                                <th style="width:10%;" class="text-center">Tipo</th>
                                <th style="width:10%;" class="text-center">Estado</th>
                                <th style="width:10%;" class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal inmodal" id="modal-detalle" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <h5 class="modal-title">Detalle</h5>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-condensed m-b-0">
                        <thead class="table-head">
                            <tr>
                                <th>C&oacute;digo</th>
                                <th>Categor&iacute;a</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>P.U.</th>
                                <th class="text-right">Importe</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st" id="list-cetalle"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </form>
        </div>
    </div>
</div>
<div class="modal inmodal" id="modal-detalle-cuota" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <h5 class="modal-title title-d">Detalle</h5>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-hover table-condensed m-b-0">
                    <thead class="table-head">
                        <tr>
                            <th>Fecha</th>
                            <th>Interes</th>
                            <th class="text-right">Importe</th>
                            <th class="text-center">Estado</th>
                            <th class="text-right">Opciones</th>
                        </tr>
                    </thead>
                    <tbody class="tb-st" id="list-cuota"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal inmodal" id="modal-detalle-sub-cuota" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <h5 class="modal-title title-d">Detalle</h5>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-hover table-condensed m-b-0">
                    <thead class="table-head">
                        <tr>
                            <th>Cajero</th>
                            <th>Fecha/Hora</th>
                            <th>Egreso Caja</th>
                            <th class="text-right">Importe</th>
                        </tr>
                    </thead>
                    <tbody class="tb-st" id="list-sub-cuota"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Atr&aacute;s</button>
            </div>
        </div>
    </div>
</div>