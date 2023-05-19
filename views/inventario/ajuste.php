<?php Session::init(); if (Session::get('rol') == 1 OR Session::get('rol') == 2) { ?>
<?php
date_default_timezone_set($_SESSION["zona_horaria"]);
setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
$fecha = date("d-m-Y");
$fechaa = date("m-Y");
?>
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Inventario</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>inventario/ajuste" class="link">Inicio</a></li>
            <li class="breadcrumb-item active">Ajuste de stock</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body p-b-0">
                <div class="message-box contact-box">
                    <h2 class="add-ct-btn"><a href="<?php echo URL; ?>inventario/nuevoajuste"><button type="button" class="btn btn-circle btn-lg btn-orange waves-effect waves-dark"><i class="ti-plus"></i></button></a></h2><br>
                </div>
            </div>
            <div class="card-body p-b-0 p-r-0 p-t-0">
                <form method="post" enctype="multipart/form-data" target="_blank" action="#">
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
                    <div class="col-lg-5 offset-lg-4">
                        <div class="form-group m-b-10">
                            <input type="text" class="form-control global_filter" id="global_filter" autocomplete="off">
                            <span class="bar"></span>
                            <label for="global_filter">B&uacute;squeda</label>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive b-t m-b-10">
                    <table id="table" class="table table-hover table-condensed stylish-table" width="100%">
                        <thead class="table-head">
                            <th style="width:15%">Fecha</th>
                            <th style="width:20%">Tipo Operaci&oacute;n</th>
                            <th style="width:20%">Responsable</th>
                            <th style="width:25%">Concepto</th>
                            <th style="width:10%" class="text-center">Estado</th>
                            <th style="width:10%" class="text-right">Acciones</th>
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
                <div class="table-responsive b-t">
                    <table class="table table-hover table-condensed m-b-0" width="100%">
                        <thead class="table-head">
                            <tr>
                                <th>C&oacute;digo</th>
                                <th>Categor&iacute;a</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Unidad de Medida</th>
                                <th class="text-right">Precio Costo</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st" id="table-detalle"></tbody>
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
<?php } ?>