<input type="hidden" id="tribAcr" value="<?php echo Session::get('tribAcr'); ?>"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Compras</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>compra/proveedor" class="link">Inicio</a></li>
            <li class="breadcrumb-item active">Proveedores</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">         
            <div class="card-body p-b-0">
                <div class="message-box contact-box">
                    <h2 class="add-ct-btn"><a href="javascript:void(0)"><button type="button" class="btn btn-circle btn-lg btn-orange waves-effect waves-dark btn-nuevo"><i class="ti-plus"></i></button></a></h2>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="row floating-labels m-t-30">
                    <div class="col-4 col-lg-2">
                        <div class="row text-center m-t-0 p-l-10 p-r-10">
                            <div class="col-lg-12 col-md-12 col-sm-12 m-b-20">
                                <h5 class="m-b-5 font-30 font-normal text-warning proveedores-total"></h5>
                                <h6 class="font-bold">Proveedores</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-8 col-lg-7 offset-lg-3">
                        <div class="form-group m-b-20">
                            <input type="text" class="form-control global_filter" id="global_filter" autocomplete="off">
                            <span class="bar"></span>
                            <label for="global_filter">B&uacute;squeda</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive b-t m-b-10">
                    <table id="table" class="table table-hover table-condensed stylish-table" cellspacing="0" width="100%">
                        <thead class="table-head">
                            <th style="width: 35% !important;">Proveedor</th>
                            <th style="width: 45% !important;">Direcci&oacute;n</th>
                            <th style="width: 10% !important; text-align: center">Estado</th>
                            <th style="width: 10% !important; text-align: center">Acciones</th>
                        </thead>
                        <tbody class="tb-st"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal inmodal" id="modal-proveedor" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content animated bounceInRight">
        <form id="frm-proveedor" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_prov" id="id_prov">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body">
                <div class="row floating-labels m-t-0">
                    <div class="col-md-6 p-t-20">
                        <div class="form-group ent m-b-40">
                            <input type="text" class="form-control ruc" name="ruc" id="ruc" minlength="<?php echo Session::get('tribCar'); ?>" maxlength="<?php echo Session::get('tribCar'); ?>" value="" autocomplete="off" required="required"/>
                            <span class="bar"></span>
                            <label for="ruc" class="c-ruc"><?php echo Session::get('tribAcr'); ?></label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group letNumMayMin m-b-40">
                            <input type="text" class="form-control ruc input-mayus" name="razon_social" id="razon_social" value="" autocomplete="off" required="required"/>
                            <span class="bar"></span>
                            <label for="razon_social">Raz&oacute;n Social</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group letNumMayMin m-b-40">
                            <input type="text" class="form-control" name="direccion" id="direccion" value="" autocomplete="off" required="required"/>
                            <span class="bar"></span>
                            <label for="direccion">Direcci&oacute;n</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group ent m-b-40">
                            <input type="text" class="form-control dni" name="telefono" id="telefono" value="" autocomplete="off"/>
                            <span class="bar"></span>
                            <label for="telefono">Tel&eacute;fono</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group letMayMin m-b-40">
                            <input type="text" class="form-control input-mayus" name="contacto" id="contacto" value="" autocomplete="off"/>
                            <span class="bar"></span>
                            <label for="contacto">Contacto</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group m-b-40">
                            <input type="text" class="form-control dni" name="email" id="email" value="" autocomplete="off"/>
                            <span class="bar"></span>
                            <label for="email">Correo electr&oacute;nico</label>
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