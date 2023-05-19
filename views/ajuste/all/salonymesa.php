

 
   
 
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Ajustes</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Restaurante</a></li>
            <li class="breadcrumb-item active">Salones y mesas</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body p-b-0">
                <div class="message-box contact-box">
                    <h2 class="add-ct-btn"><button type="button" class="btn btn-circle btn-lg btn-orange waves-effect waves-dark" onclick="editarSalon();"><i class="ti-plus"></i></button></h2><br>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="row floating-labels m-t-5">
                    <div class="col-sm-5 offset-sm-7">
                        <div class="form-group m-b-10">
                            <input type="text" class="form-control global_filter_01" id="global_filter_01" autocomplete="off">
                            <span class="bar"></span>
                            <label for="global_filter_01">B&uacute;squeda</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive b-t m-b-10">
                    <table id="table01" class="table table-hover stylish-table" cellspacing="0" width="100%">
                        <thead class="table-head">
                            <th>Nombre</th>
                            <th>Mesas</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </thead>
                        <tbody class="tb-st"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card" id="lizq-s" style="display: block;">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-sm-8 offset-sm-2">
                        <h3><i class="ti ti-arrow-circle-left"></i><br>Agregue o seleccione un salón</h3>
                        <p>Debes agregar o seleccionar un salón para poder agregar, modificar o visualizar sus mesas</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card" id="lizq-i" style="display: none;">
            <div class="card-body p-b-0">
                <div class="message-box contact-box">
                    <h4>Mesa(s) de <span id="title-mesa"></span></h4>
                    <h2 class="add-ct-btn" id="btn-nuevo"></h2>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="row floating-labels m-t-10">
                    <div class="col-sm-5 offset-sm-7">
                        <div class="form-group m-t-20 m-b-10">
                            <input type="text" class="form-control global_filter_02" id="global_filter_02" autocomplete="off">
                            <span class="bar"></span>
                            <label for="global_filter_02">B&uacute;squeda</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive b-t m-b-10">
                    <table id="table02" class="table table-hover stylish-table" cellspacing="0" width="100%">
                        <thead class="table-head">
                            <th>Nombre</th>
                            <th>Sal&oacute;n</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </thead>
                        <tbody class="tb-st"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="modal01" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated bounceInRight">
            <form id="form01" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_salon" id="id_salon">
                <div class="modal-header">
                    <h4 class="modal-title">Sal&oacute;n</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                </div>
                <div class="modal-body">
                    <div class="row floating-labels m-t-40">
                        <div class="col-sm-12">
                            <div class="form-group f m-b-40 letNumMayMin">
                                <input type="text" class="form-control" name="descripcion" id="descripcion" autocomplete="off" required>
                                <span class="bar"></span>
                                <label for="descripcion">Nombre</label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group m-b-40">
                                <select class="selectpicker form-control" name="estado" id="estado" data-style="form-control btn-default" data-live-search-style="begins" data-live-search="true">
                                    <option value="a" active>ACTIVO</option>
                                    <option value="i">INACTIVO</option>
                                </select>
                                <span class="bar"></span>
                                <label for="estado">Estado</label>
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
<div class="modal inmodal" id="modal02" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated bounceInRight">
            <form id="form02" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_mesa" id="id_mesa">
                <input type="hidden" name="id_salon_1" id="id_salon_1">
                <div class="modal-header">
                    <h4 class="modal-title">Mesa</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
                </div>
                <div class="modal-body">
                    <div class="row floating-labels m-t-40">
                        <div class="col-sm-12">
                            <div class="form-group f m-b-40 letNumMayMin">
                                <input type="text" class="form-control" name="nro_mesa" id="nro_mesa" autocomplete="off" required>
                                <span class="bar"></span>
                                <label for="nro_mesa">Nombre</label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group m-b-40">
                                <select class="selectpicker form-control" name="estado_1" id="estado_1" data-style="form-control btn-default" data-live-search-style="begins" data-live-search="true">
                                    <option value="a" active>ACTIVO</option>
                                    <option value="m">INACTIVO</option>
                                </select>
                                <span class="bar"></span>
                                <label for="estado_1">Estado</label>
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

<div class="modal" id="modal_add_salon" tabindex="-1" role="dialog" aria-labelledby="modal_add_salon" aria-hidden="true" data-id_salon = "0" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title_modal_sal">

                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group m-b-40 salon_content">
                        <select class="form-control bg-t container_select" name="id_mozo" id="id_mozo">
                            <option value="">Favor de seleccionar un mozo</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="success_add_salon">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<div id="myModal" class="modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Usuarios registrados</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>    
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12" id="dtp_ped">

                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>