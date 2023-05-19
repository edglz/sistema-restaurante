<input type="hidden" id="url" value="<?php echo URL; ?>" />
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>" />
<input type="hidden" id="cod_ti" value="3" />
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Recetas</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>tablero" class="link">Inicio</a></li>
            <li class="breadcrumb-item active">Mis Preparados</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-4 pt-4">
        <div class="card">
            <div class="card-body">
                <div class="message-box contact-box">
                    <h4 class="card-title">Categorías</h4>
                    <h2 class="add-ct-btn" id="step1"><button type="button" class="btn btn-circle btn-lg btn-orange waves-effect waves-dark btn-categoria-nuevo" onclick="load_form()"><i class="ti-plus"></i></button></h2>
                    <form id="form-categoria" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id_catg_categoria" id="id_catg_categoria">
                        <div id="display-categoria-nuevo" style="display: none">
                            <ul class="list-style-none">
                                <li class="divider"></li>
                            </ul>
                            <div class="row floating-labels m-t-40">
                                <div class="col-sm-12">
                                    <div class="form-group f m-t-40 m-b-20 letNumMayMin">
                                        <input type="text" class="form-control input-mayus cbu ph-0" name="descripcion_categoria" id="descripcion_categoria" autocomplete="off" required>
                                        <span class="bar"></span>
                                        <label for="descripcion_categoria">Nombre</label>
                                    </div>
                                </div>

                                <div class="col-sm-12 m-t-20">
                                    <input type="hidden" name="hidden_estado_categoria" id="hidden_estado_categoria" />
                                    <input type="checkbox" name="estado_categoria" id="estado_categoria" class="chk-col-green" />
                                    <label for="estado_categoria">Activo</label>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-6 m-b-10">
                                    <a class="btn btn-secondary btn-block btn-categoria-cancelar">Cancelar</a>
                                </div>
                                <div class="col-6 m-b-10 text-right">
                                    <button type="submit" class="btn btn-block btn-success">Aceptar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <ul class="list-style-none display-categoria-list">
                        <li class="box-label"><a href="javascript:void(0)" class="link" onclick="listarRecetas('%')">Todos</a></li>
                    </ul>
                    <ul class="list-style-none display-categoria-list" style="font-size: 13px;">
                        <li class="divider"></li>
                        <div class="scroll_categoria" id="ul-cat"></div>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light-inverse p-0">
                        <div class="row">
                            <div class="col-12">
                                <div class="social-widget">
                                    <div class="soc-content">
                                        <div class="col-12 b-r">
                                            <h1><i class="mdi mdi-food-fork-drink text-warning"></i></h1>
                                            <h5 class="text-warning">Preparados</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-t-0 p-b-0 floating-labels">
                        <div class="row" style="margin-left: -20px; margin-right: -20px">
                            <!-- Column -->
                            <div class="col-lg-12 p-0">
                                <div class="row p-20">
                                    <div class="col-lg-12">
                                        <a href="<?php echo URL . '/nueva' ?>" class="btn btn-success btn-block btn-nuevo-producto"><i class="fas fa-plus-circle"></i> Nueva receta</a>
                                    </div>
                                    <div class="col-lg-12 m-t-20" id="filter_global">
                                        <div class="form-group m-b-0">
                                            <input class="form-control global_filter" id="global_filter" type="text" placeholder="Buscar receta">
                                            <span class="bar"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive b-t m-b-10">
                                    <table class="table table-condensed table-hover stylish-table" width="100%" id="table-recetas">
                                        <thead class="table-head">
                                            <th style="width: 75%;">Preparado</th>
                                            <th style="width: 20%; text-align: center">¿Activo?</th>
                                            <th style="width: 5%;text-align: center;">Opciones</th>
                                        </thead>
                                        <tbody class="tb-st"></tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal inmodal" id="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated bounceInRight">
        <form id="form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_preparado" id="id_areap">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body">
                <div class="row floating-labels m-t-40">
                    <div class="col-sm-12">
                        <div class="form-group letNumMayMin f m-b-40">
                            <input type="text" class="form-control" name="nombre" id="nombre" value="" autocomplete="off" required>
                            <span class="bar"></span>
                            <label for="nombre">Nombre</label>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group m-b-40">
                            <select class="selectpicker form-control" name="id_imp" id="id_imp" data-style="form-control btn-default" data-live-search-style="begins" data-live-search="true" title="Seleccionar" autocomplete="off" required="required">
                            <?php foreach($this->Impresora as $key => $value): ?>
                                <option value="<?php echo $value['id_imp']; ?>"><?php echo $value['nombre']; ?></option>
                            <?php endforeach; ?>
                            </select>
                            <span class="bar"></span>
                            <label for="id_imp">Impresora</label>
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
                <button type="submit" class="btn btn-success btn-guardar">Aceptar</button>
            </div>
        </form>
        </div>
    </div>
</div>