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
            <li class="breadcrumb-item active">Ventas por delivery</li>
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
                        <div class="offset-4 col-lg-2">
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
                        <div class="col-lg-3">
                            <div class="form-group m-b-40">
                                <select class="selectpicker form-control" name="filtro_repartidor" id="filtro_repartidor" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5">
                                    <option value="%" active>Mostrar Todo</option>
                                    <optgroup>
                                        <?php foreach($this->Repartidor as $key => $value): ?>
                                            <option value="<?php echo $value['id_usu']; ?>"><?php echo $value['ape_paterno'].' '.$value['ape_materno'].' '.$value['nombres']; ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                    <?php if(Session::get('opc_01') == 1) { ?>
                                    <optgroup>
                                        <option value="2222">RAPPI</option>
                                        <option value="3333">UBER</option>
                                        <option value="4444">GLOVO</option>
                                    </optgroup>
                                    <?php } ?>
                                </select>
                                <span class="bar"></span>
                                <label for="filtro_repartidor">Repartidor</label>
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
                    <!--
                    <div class="col-4">
                        <h2 class="font-medium text-warning m-b-0 font-30 ventas-neta"></h2>
                        <h6 class="font-bold m-b-10">Total Neto 70%</h6>
                    </div>
                -->
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
                                <th width="10%">Fecha</th>
                                <th width="10%">Caja</th>
                                <th width="20%">Cliente</th>
                                <th width="20%">Repartidor</th>
                                <th width="15%">Documento</th>                               
                                <th class="text-right" width="10%">Entrega</th>
                                <th class="text-right" width="10%">Total</th>
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
                <h5 class="modal-title title-d">Detalle</h5>
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
