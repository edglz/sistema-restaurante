<input type="hidden" id="url" value="<?php echo URL; ?>"/>
<input type="hidden" id="cod_ape" value="<?php echo Session::get('aperturaIn'); ?>"/>
<input type="hidden" id="rol_usr" value="<?php echo Session::get('rol'); ?>"/>
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Caja</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>caja/egreso" class="link">Inicio</a></li>
            <li class="breadcrumb-item active">Egresos</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">         
            <div class="card-body p-b-0">
                <div class="message-box contact-box">
                    <h2 class="add-ct-btn"><button type="button" class="btn btn-circle btn-lg btn-orange waves-effect waves-dark" data-toggle="modal" data-target="#modal"><i class="ti-plus"></i></button></h2>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="row floating-labels m-t-30">
                    <div class="col-4 col-sm-4 col-lg-4">
                        <div class="row text-center m-t-0 p-l-10 p-r-10">
                            <div class="col-lg-5 col-md-6 col-sm-12 m-b-20">
                                <h5 class="m-b-5 font-20 text-warning font-normal egresos-total"></h5><h6 class="font-bold">Total</h6>
                            </div>
                            <div class="col-lg-5 col-md-6 col-sm-12">
                                <h5 class="m-b-5 font-20 text-warning font-normal egresos-oper"></h5><h6 class="font-bold">N° Operaciones</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-8 col-sm-8 col-lg-8">
                        <div class="row">
                            <div class="form-group col-12 col-sm-3 m-b-40">
                                <select class="selectpicker form-control p-0" name="filtro_tipo_gasto" id="filtro_tipo_gasto" data-style="form-control btn-default" data-live-search="true" autocomplete="off">
                                    <option value="%" active>Mostrar Todo</option>
                                    <optgroup>
                                        <option value="1">COMPRAS</option>
                                        <option value="2">SERVICIOS</option>
                                        <option value="3">REMUNERACIONES</option>
                                    </optgroup>
                                </select>
                                <span class="bar"></span>
                                <label for="filtro_tipo_gasto">Tipo</label>
                            </div>
                            <div class="form-group col-12 col-sm-3 m-b-40">
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
                            <div class="form-group col-12 col-sm-6 m-b-20">
                                <input type="text" class="form-control global_filter" id="global_filter" autocomplete="off">
                                <span class="bar"></span>
                                <label for="global_filter">B&uacute;squeda</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive b-t m-b-10">
                    <table id="table" class="table table-hover table-condensed stylish-table" cellspacing="0" width="100%">
                        <thead class="table-head">
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Tipo</th>
                            <th>Entregado a</th>
                            <th>Motivo</th>
                            <th>Importe</th>
                            <th class="text-center">Estado</th>
                            <th class="text-right">Acciones</th>
                        </thead>
                        <tbody class="tb-st"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal inmodal" id="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated bounceInTop">
        <form id="form" method="post" enctype="multipart/form-data">
            <div class="modal-header">
                <h4 class="modal-title">Egreso Administrativo</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body">
                <div class="row floating-labels m-t-40">
                    <div class="col-sm-12">
                        <div class="form-group m-b-40 cbu">
                            <select class="selectpicker form-control p-0" name="id_tipo_gasto" id="id_tipo_gasto" data-style="form-control btn-default" title="Seleccionar" autocomplete="off" required="required">
                                <option value="1">Compras</option>
                                <option value="2">Servicios</option>
                                <option value="3">Remuneraciones</option>
                            </select>
                            <span class="bar"></span>
                            <label for="id_tipo_gasto">Tipo</label>
                        </div>
                    </div>
                    <div class="col-sm-12 opc-per" style="display: none;">
                        <div class="form-group m-b-40 cbu">
                            <select class="selectpicker form-control p-0" name="id_per" id="id_per" data-style="form-control btn-default" title="Seleccionar" data-size="5" data-live-search-style="begins" data-live-search="true" autocomplete="off" required>
                                <?php foreach($this->Personal as $key => $value): ?>
                                    <option value="<?php echo $value['id_usu']; ?>"><?php echo $value['nombres'].' '.$value['ape_paterno'].' '.$value['ape_materno']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="bar"></span>
                            <label for="id_per">Personal</label>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group m-b-40 dec">
                            <input type="text" class="form-control" name="importe" id="importe" autocomplete="off" required="required">
                            <span class="bar"></span>
                            <label for="importe">Importe - <?php echo Session::get('moneda'); ?></label>
                        </div>
                    </div>
                    <div class="col-sm-12 display-responsable">
                        <div class="form-group m-b-40">
                            <input name="responsable" id="responsable" class="form-control input-mayus" autocomplete="off" required="required"></input>
                            <span class="bar"></span>
                            <label for="responsable">Entregado a</label>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group m-b-40">
                            <textarea name="motivo" id="motivo" class="form-control" rows="2" required="required"></textarea>
                            <span class="bar"></span>
                            <label for="motivo">Motivo</label>
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