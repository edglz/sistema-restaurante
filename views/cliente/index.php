<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<input type="hidden" id="tribAcr" value="<?php echo Session::get('tribAcr'); ?>"/>
<input type="hidden" id="diAcr" value="<?php echo Session::get('diAcr'); ?>"/>
<input type="hidden" id="filtro_tipo_cliente" value="1"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Clientes</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>cliente" class="link">Inicio</a></li>
            <li class="breadcrumb-item active">Clientes</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-body p-b-0">
                <div class="message-box contact-box">
                    <h2 class="add-ct-btn"><a href="javascript:void(0);"><button type="button" class="btn btn-circle btn-lg btn-orange waves-effect waves-dark btn-nuevo"><i class="ti-plus"></i></button></a></h2>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="row floating-labels m-t-30">
                    <div class="col-5 col-sm-3 col-lg-3">
                        <div class="row text-center m-t-0 p-l-10 p-r-10">
                            <div class="col-lg-12 col-md-12 col-sm-12 m-b-20">
                                <h5 class="m-b-5 font-30 font-normal text-warning clientes-total"></h5>
                                <h6 class="font-bold">Clientes</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-7 col-sm-9 col-lg-9">
                        <div class="row">
                            <div class="form-group col-12 m-b-20">
                                <input type="text" class="form-control global_filter" id="global_filter" autocomplete="off">
                                <span class="bar"></span>
                                <label for="global_filter">B&uacute;squeda</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs customtab" role="tablist">
                <li class="nav-item"> <a class="nav-link list-personas active" data-toggle="tab" href="#contenido" role="tab"><span class="hidden-sm-up">Personas</span> <span class="hidden-xs-down">Personas</span></a> </li>
                <li class="nav-item"> <a class="nav-link list-empresas" data-toggle="tab" href="#contenido" role="tab"><span class="hidden-sm-up">Empresas</span> <span class="hidden-xs-down">Empresas</span></a> </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="contenido" role="tabpanel">
                    <div class="p-0">
                        <div class="table-responsive m-b-10">
                            <table id="table" class="table table-condensed table-hover stylish-table" cellspacing="0" width="100%">
                                <thead class="table-head">
                                    <th style="width: 80%;">Cliente</th>
                                    <th style="width: 10%; text-align: center;">Estado</th>
                                    <th style="width: 10%; text-align: center;">Acciones</th>
                                </thead>
                                <tbody class="tb-st"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card">
            <div class="card-body p-20 display-one" style="display: block;">
                <div class="row text-center justify-center" style="height: 200px;">
                    <div class="col-sm-8">
                        <h2><i class="ti ti-arrow-circle-left"></i></h2>
                        <h4 class="">Seleccione un cliente</h4>
                        <h6 class="text-center text-muted">Aquí puedes ver las ventas realizadas por el cliente seleccionado</h6>
                    </div>
                </div>
            </div>
            <div class="card-body p-0 display-two" style="display: none;">
                <h5 class="p-20 b-b bg-light-inverse justify-center" style="border-top-left-radius: .25rem; border-top-right-radius: .25rem; justify-content: left;"><i class="mdi mdi-account-circle menu-icon"></i>&nbsp;<span class="cliente-nombre"></span></h5>
                <div class="row text-center m-t-0 p-l-10 p-r-10">
                    <div class="col-4 m-t-20">
                        <h3 class="m-b-0 font-normal ventas-operaciones"></h3>
                        <h6 class="font-bold m-b-10">N° Operaciones</h6>
                    </div>
                    <div class="col-4 m-t-20">
                        <h3 class="m-b-0 font-normal ventas-descuentos"></h3>
                        <h6 class="font-bold m-b-10">Descuentos</h6>
                    </div>
                    <div class="col-4 m-t-20">
                        <h3 class="m-b-0 font-normal ventas-total"></h3>
                        <h6 class="font-bold m-b-10">Total</h6>
                    </div>                    
                    <div class="col-md-12 m-b-10"></div>
                </div>
                <hr class="m-0">
                <div class="table-responsive m-b-10">
                    <table id="table-ventas" class="table table-hover table-condensed stylish-table m-b-0" width="100%">
                        <thead class="table-head">
                            <tr>
                                <th>Fecha</th>
                                <th>Documento</th>
                                <th class="text-right">Desc.</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st" id="list-venta"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal inmodal" id="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
        <form id="form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_cliente" id="id_cliente">
        <input type="hidden" name="tipo_cliente" id="tipo_cliente">
            <div class="modal-header justify-center">
                <h4 class="modal-title"></h4>
                <div class="ml-auto m-t-10">
                    <input name="tipo_cli" type="radio" value="1" id="td_dni" class="with-gap radio-col-light-green"/>
                    <label for="td_dni"><?php echo Session::get('diAcr'); ?></label>
                    <input name="tipo_cli" type="radio" value="2" id="td_ruc" class="with-gap radio-col-light-green"/>
                    <label for="td_ruc"><?php echo Session::get('tribAcr'); ?></label>
                </div>
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
                            <div class="col-md-12">
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Aceptar</button>
            </div>
        </form>
        </div>
    </div>
</div>
