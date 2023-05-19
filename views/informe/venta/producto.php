<?php
date_default_timezone_set($_SESSION["zona_horaria"]);
setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
$fecha = date("d-m-Y H:i");
$fechaa = date("m-Y");
?>
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Informes</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>informe" class="link">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>informe" class="link">Informe de ventas</a></li>
            <li class="breadcrumb-item active">Ventas por producto</li>
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
                        <div class="col-lg-3">
                            <div class="form-group m-b-40">
                                <div class="input-group">
                                    <input type="text" class="form-control font-14 text-center" name="start" id="start" value="<?php echo '01-'. $fechaa ; ?>" autocomplete="off"/>
                                    <span class="input-group-text bg-gris">al</span>
                                    <input type="text" class="form-control font-14 text-center" name="end" id="end" value="<?php echo $fecha; ?>" autocomplete="off"/>
                                </div>
                                <label>Rango de fechas</label>
                            </div>
                        </div>
                        <div class="col-sm-5 col-lg-2">
                            <div class="form-group m-b-40">
                                <select class="selectpicker form-control" name="filtro_categoria" id="filtro_categoria" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5">
                                    <option value="%" active>Mostrar Todo</option>
                                    <optgroup>
                                        <?php foreach($this->Categoria as $key => $value): ?>
                                            <option value="<?php echo $value['id_catg']; ?>"><?php echo $value['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                                <span class="bar"></span>
                                <label for="filtro_categoria">Categor&iacute;a</label>
                            </div>
                        </div>
                        <div class="col-sm-7 col-lg-4">
                            <div class="form-group m-b-40">
                                <select class="selectpicker form-control" name="filtro_producto" id="filtro_producto" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5" disabled>
                                    <option value="%" active>Mostrar Todo</option>
                                    <optgroup>
                                        <?php foreach($this->Producto as $key => $value): ?>
                                            <option value="<?php echo $value['id_prod']; ?>"><?php echo $value['nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                                <span class="bar"></span>
                                <label for="filtro_producto">Producto</label>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group m-b-40">
                                <select class="selectpicker form-control" name="filtro_presentacion" id="filtro_presentacion" data-style="form-control btn-default" data-live-search="true" autocomplete="off" data-size="5" disabled>
                                    <option value="%" active>Mostrar Todo</option>
                                    <optgroup>
                                        <?php foreach($this->Presentacion as $key => $value): ?>
                                            <option value="<?php echo $value['id_pres']; ?>"><?php echo $value['presentacion']; ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                                <span class="bar"></span>
                                <label for="filtro_presentacion">Presentaci&oacute;n</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="text-center m-b-20">
                <div class="row">
                    <div class="col-6">
                        <h2 class="font-medium text-warning m-b-0 font-30 productos-operaciones"></h2>
                        <h6 class="font-bold m-b-10">Cantidad Vendida</h6>                            
                    </div>
                    <div class="col-6">
                        <h2 class="font-medium text-warning m-b-0 font-30 productos-total"></h2>
                        <h6 class="font-bold m-b-10">Total de Ventas</h6>
                    </div>
                </div>
            </div> 
            <div class="card-body p-0">
                <div class="table-responsive b-t m-b-10">
                    <table id="table" class="table table-hover table-condensed stylish-table" width="100%">
                        <thead class="table-head">
                            <tr>
                                <th width="10%">Categor&iacute;a</th>
                                <th width="15%">Producto</th>
                                <th width="15%">Presentaci&oacute;n</th>
                                <th width="10%">Cantidad por sal&oacute;n</th>
                                <th width="10%">Cantidad por mostrador</th>
                                <th width="10%">Cantidad por delivery</th>
                                <th width="10%">Cantidad por portero</th>
                                <th class="text-right" width="10%">Total cantidad vendida</th>
                                <th class="text-right" width="10%">P.V.</th>
                                <th class="text-right" width="10%">Total</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
