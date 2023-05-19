<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Ajustes</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Restaurante</a></li>
            <li class="breadcrumb-item active">Insumos</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        <div class="card">         
            <div class="card-body">
                <div class="message-box contact-box">
                    <h4 class="card-title">Categorías</h4>
                    <h2 class="add-ct-btn" id="step1"><button type="button" class="btn btn-circle btn-lg btn-orange waves-effect waves-dark btn-nuevo-categoria"><i class="ti-plus"></i></button></h2>
                    <ul class="list-style-none">
                        <li class="box-label"><a href="javascript:void(0)" class="link" onclick="listarInsumos('%')">Todos</a></li>
                    </ul>
                    <form id="frm-categoria" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id_catg_categoria" id="id_catg_categoria">
                    <div id="nueva-catg" style="display: none">
                        <ul class="list-style-none">
                            <li class="divider"></li>
                        </ul>
                        <div class="row floating-labels m-t-40">
                            <div class="col-sm-12">
                                <div class="form-group f m-b-20 letNumMayMin">
                                    <input type="text" class="form-control input-mayus cbu ph-0" name="descripcion_categoria" id="descripcion_categoria" autocomplete="off" onkeyup="mayus(this);" required>
                                    <span class="bar"></span>
                                    <label for="descripcion_categoria">Nombre</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 m-b-10">
                                <a class="btn btn-secondary btn-block btn-ccatg">Cancelar</a>
                            </div>
                            <div class="col-6 m-b-10 text-right">
                                <button type="submit" class="btn btn-block btn-success">Aceptar</button>
                            </div>
                        </div>
                    </div>
                    </form>
                    <ul class="list-style-none" style="font-size: 13px;">
                        <li class="divider"></li>
                        <div class="scroll_categoria" id="ul-cat"></div>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-0">
                        <div class="row">
                            <div class="col-12">
                                <div class="social-widget">
                                    <div class="soc-content">
                                        <div class="col-4 b-r">
                                            <a href="producto">
                                                <h1><i class="mdi mdi-food-fork-drink text-muted"></i></h1>
                                                <h5 class="text-muted">Platos y bedidas</h5>
                                            </a>
                                        </div>
                                        <div class="col-4 b-r">
                                            <a href="combo">
                                                <h1><i class="mdi mdi-food text-muted"></i></h1>
                                                <h5 class="text-muted">Combos</h5>
                                            </a>
                                        </div>
                                        <div class="col-4">
                                            <h1><i class="mdi mdi-food-variant text-warning"></i></h1>
                                            <h5 class="text-warning">Insumos</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                 
                    </div>
                    <div class="card-body floating-labels p-0">
                        <div class="row p-20">
                            <div class="col-sm-12">
                                <button class="btn btn-success btn-block btn-nuevo-insumo" id="step2"><i class="fas fa-plus-circle"></i> Nuevo insumo</button>
                            </div>
                            <div class="col-sm-12 m-t-20" id="filter_global">
                                <div class="form-group m-b-0">
                                    <input class="form-control global_filter" id="global_filter" type="text" placeholder="Buscar insumo">
                                    <span class="bar"></span>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive b-t m-b-10">
                            <table class="table table-condensed table-hover stylish-table" width="100%" id="table-insumos">
                                <thead class="table-head">
                                    <th>C&oacute;digo</th>
                                    <th>Nombre</th>
                                    <th>Categor&iacute;a</th>
                                    <th>Unidad</th>
                                    <th class="text-right">¿Activo?</th>
                                    <th></th>
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

<div class="modal inmodal" id="modal-insumo" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog" style="max-width:400px;">
        <div class="modal-content animated bounceInRight">
        <form id="form-insumo" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_ins" id="id_ins">
            <div class="modal-header">
                <h4 class="modal-title">Detalle del insumo</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body">
                <div class="row floating-labels">
                    <div class="col-md-12">
                        <div class="form-group m-b-40 letNumMayMin">
                            <input class="form-control input-lg input-mayus" type="text" autocomplete="off" name="nomb_ins" id="nomb_ins" placeholder="Nombre" required="required"/>
                            <span class="bar"></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group f m-b-40 letNumMayMin">
                            <input type="text" class="form-control input-mayus cbu" name="cod_ins" id="cod_ins" onkeyup="mayus(this);" autocomplete="off">
                            <span class="bar"></span>
                            <label for="cod_ins">C&oacute;digo</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group m-b-40">
                            <select class="selectpicker form-control cbu" name="id_med" id="id_med" data-style="form-control btn-default" autocomplete="off" required="required" title="Seleccionar" data-size="5">
                                <?php foreach($this->UnidadMedida as $key => $value): ?>
                                    <option value="<?php echo $value['id_med']; ?>"><?php echo $value['descripcion']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="bar"></span>
                            <label for="id_med">Unidad de medida</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group f m-b-40 cbu">
                            <input type="hidden" id="categoria"/>
                            <select class="selectpicker form-control" name="id_catg" id="id_catg" data-style="form-control btn-default" data-live-search-style="begins" data-live-search="true" title="Seleccionar" data-size="5" required="required">  
                            </select>
                            <span class="bar"></span>
                            <label for="id_catg">Categor&iacute;a</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group f m-b-40 dec">
                            <input type="text" class="form-control cbu" name="cos_uni" id="cos_uni" autocomplete="off" required="required">
                            <span class="bar"></span>
                            <label for="cos_uni">Costo Unitario</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group f m-b-20 dec">
                            <input type="text" class="form-control cbu" name="stock_min" id="stock_min" autocomplete="off">
                            <span class="bar"></span>
                            <label for="stock_min">Stock m&iacute;nimo <i class="ti-info-alt text-warning font-10" data-original-title="Considere este valor según unidad de medida" data-toggle="tooltip" data-placement="top"></i></label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group m-b-20">
                            <select class="selectpicker form-control" name="estado" id="estado" data-style="form-control btn-default" autocomplete="off">
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success btn-guardar">Aceptar</button>
            </div>
        </form>
        </div>
    </div>
</div>

<script type="text/javascript">
$(function() {
    $('#config').addClass("active");
    function filterGlobal () {
    $('#table-insumos').DataTable().search( 
        $('#global_filter').val()
    ).draw();
    }
    $('input.global_filter').on( 'keyup click', function () {
        filterGlobal();
    });
});
</script>
