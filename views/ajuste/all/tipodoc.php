<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Ajustes</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Empresa</a></li>
            <li class="breadcrumb-item active">Tipo de Documentos</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body p-0 m-t-30 m-b-10">
                <div class="table-responsive b-t">
                    <table id="table" class="table table-hover stylish-table" cellspacing="0" width="100%">
                        <thead class="table-head">
                            <tr>
                                <th>Descripci&oacute;n</th>
                                <th>Serie</th>
                                <th>N&uacute;mero</th>
                                <th>Estado</th>
                                <th class="text-right">Acci&oacute;n</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="alert alert-info font-15">
            <h3 class="text-info"><i class="fa fa-exclamation-circle"></i> Informaci&oacute;n</h3>
            <dl>
                <dt>Tipo de documentos</dt>
                <dd>Permite editar las series y numeración asignados sobre cada 'Tipo de documento'.</dd>
            </dl>
            <ul class="list-icons">
                <li><i class="ti-angle-right"></i> La serie y número correlativo que ingrese, será considerado como el inicio.</li>
                <li><i class="ti-angle-right"></i> Considere estado "ACTIVO", si desea visualizar el tipo de documento, en el módulo de Punto de Venta.</li>
            </ul>
        </div>
    </div>
</div>

<div class="modal inmodal" id="modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated bounceInTop">
        <form id="form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_tipo_doc" id="id_tipo_doc">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body">
                <div class="row floating-labels m-t-40">
                    <div class="col-sm-12">
                        <div class="form-group m-b-40 letNumMayMin">
                            <input type="text" class="form-control input-mayus" name="serie" id="serie" autocomplete="off" minlength="4" maxlength="4">
                            <span class="bar"></span>
                            <label for="serie">Serie</label>
                        </div>
                    </div>
                    <div class="col-sm-12 ent">
                        <div class="form-group m-b-40">
                            <input type="text" class="form-control" name="numero" id="numero" minlength="8" maxlength="8" autocomplete="off">
                            <span class="bar"></span>
                            <label for="numero">N&uacute;mero</label>
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