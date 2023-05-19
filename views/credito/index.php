<?php
date_default_timezone_set($_SESSION["zona_horaria"]);
setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
$fecha = date("d-m-Y");
$fechaa = date("m-Y");
?>
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Cr&eacute;ditos</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>credito" class="link">Inicio</a></li>
            <li class="breadcrumb-item active">Compras</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body p-b-0">
                <div class="message-box contact-box">
                    <div class="row floating-labels m-t-30">
                        <div class="form-group col-lg-3 m-b-40">
                            <div class="input-group">
                                <input type="text" class="form-control font-14 text-center" name="start" id="start" value="<?php echo '01-'.$fechaa; ?>" autocomplete="off"/>
                                <span class="input-group-text bg-gris">al</span>
                                <input type="text" class="form-control font-14 text-center" name="end" id="end" value="<?php echo $fecha; ?>" autocomplete="off"/>
                            </div>
                            <label>Rango de fechas</label>
                        </div>
                        <div class="form-group col-lg-9 m-b-40">
                            <select class="selectpicker form-control" name="filtro_proveedor" id="filtro_proveedor" data-style="form-control btn-default" data-live-search="true" data-size="5" autocomplete="off">
                                <option value="%" active>Mostrar Todo</option>
                                <optgroup>
                                    <?php foreach($this->Proveedores as $key => $value): ?>
                                        <option value="<?php echo $value['id_prov']; ?>"><?php echo $value['ruc'].' - '.$value['razon_social']; ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                            <span class="bar"></span>
                            <label for="filtro_proveedor">Proveedor</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center m-b-20">
                <div class="row">
                    <div class="col-6 col-sm-3">
                        <h2 class="font-normal text-warning m-b-0 font-30 total-deuda"></h2>
                        <h6 class="font-bold m-b-10">Deuda</h6>                        
                    </div>
                    <div class="col-6 col-sm-3">
                        <h2 class="font-normal text-warning m-b-0 font-30 total-interes"></h2>
                        <h6 class="font-bold m-b-10">Inter&eacute;s</h6>
                    </div>
                    <div class="col-6 col-sm-3">
                        <h2 class="font-normal text-warning m-b-0 font-30 total-amortizado"></h2>
                        <h6 class="font-bold m-b-10">Amortizado</h6>
                    </div>
                    <div class="col-6 col-sm-3">                        
                        <h2 class="font-normal text-warning m-b-0 font-30 total-pendiente"></h2>
                        <h6 class="font-bold m-b-10">Pendiente</h6>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive b-t m-b-10">
                    <table id="table" class="table table-hover table-condensed stylish-table" width="100%">
                        <thead class="table-head">
                            <tr>
                                <th class="text-left">Fecha pago</th>
                                <th width="20%">Proveedor</th>
                                <th>Documento</th>
                                <th>Deuda</th>
                                <th>Inter&eacute;s</th>
                                <th>Amortizado</th>
                                <th>Pendiente</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal inmodal" id="modal-credito" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated bounceInTop">
        <form id="form-credito" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_credito" id="id_credito">
        <input type="hidden" name="total_credito" id="total_credito">
        <input type="hidden" name="monto_amortizado" id="monto_amortizado">
            <div class="modal-header">
                <h5 class="modal-title title-detalle"></h5>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<strong>Fecha: </strong>
						<span class="c-fecha-comp"></span>
					</div>
					<div class="col-sm-12">
						<strong>Proveedor: </strong>
						<span class="c-datos-prov"></span>
					</div>
					<div class="col-sm-12">
						<strong>Monto pendiente: </strong>
                        <span class="c-monto-pend"></span>
					</div>
				</div>
				<br>
				<div class="row floating-labels m-t-20">
                    <div class="col-sm-12">
                        <div class="form-group m-b-20 dec">
                            <input type="text" class="form-control" name="importe" id="importe" autocomplete="off" required>
                            <span class="bar"></span>
                            <label for="importe">Monto - <?php echo Session::get('moneda'); ?></label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <input type="checkbox" name="egreso" id="egreso" class="chk-col-red egreso" />
                        <label for="egreso">Registrar como egreso</label>
                    </div>
                    <div class="col-sm-12 floating-labels m-t-20 monto-egreso" style="display: none;">
                        <div class="form-group m-b-20 dec">
                            <input type="text" class="form-control" name="monto_egreso" id="monto_egreso" autocomplete="off" required>
                            <span class="bar"></span>
                            <label for="monto_egreso">Monto - <?php echo Session::get('moneda'); ?></label>
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

<div class="modal inmodal" id="modal-detalle" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInTop">
            <div class="modal-header">
                <h5 class="modal-title title-detalle">Detalle</h5>
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
                    <tbody class="tb-st" id="table-detalle"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>