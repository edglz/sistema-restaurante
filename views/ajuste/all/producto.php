<input type="hidden" id="url" value="<?php echo URL; ?>"/>
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<input type="hidden" id="cod_ti" value="3"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Ajustes</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Restaurante</a></li>
            <li class="breadcrumb-item active">Productos</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        <div class="card">         
            <div class="card-body">
                <div class="message-box contact-box">
                    <h4 class="card-title">Categorías</h4>
                    <h2 class="add-ct-btn" id="step1"><button type="button" class="btn btn-circle btn-lg btn-orange waves-effect waves-dark btn-categoria-nuevo"><i class="ti-plus"></i></button></h2>
                    <form id="form-categoria" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id_catg_categoria" id="id_catg_categoria">
                        <div id="display-categoria-nuevo" style="display: none">
                            <ul class="list-style-none">
                                <li class="divider"></li>
                            </ul>
                            <div class="row floating-labels m-t-40">
                                <div class="col-sm-12">
                                    <div class="ct-wizard-azzure" id="wizardProfile">
                                        <div class="picture-container">
                                            <div class="picture" style="width: 150px; height:150px">
                                                <img src="<?php echo URL; ?>public/images/productos/default.png" class="picture-src" id="wizardPicturePreview-2"/>
                                                <input type="hidden" name="imagen" id="imagen" value="default.png" />
                                                <input type="file" name="imagen" id="wizard-picture-2">
                                            </div>      
                                            <h6>Cambiar Imagen</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group f m-t-40 m-b-20 letNumMayMin">
                                        <input type="text" class="form-control input-mayus cbu ph-0" name="descripcion_categoria" id="descripcion_categoria" autocomplete="off" required>
                                        <span class="bar"></span>
                                        <label for="descripcion_categoria">Nombre</label>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group m-t-20 ent">
                                        <input type="text" class="form-control input-mayus cbu ph-0" name="orden_categoria" id="orden_categoria" autocomplete="off">
                                        <span class="bar"></span>
                                        <label for="orden_categoria">Orden</label>
                                    </div>
                                </div>
                                <div class="col-sm-12 m-t-20">
                                    <input type="hidden" name="hidden_estado_categoria" id="hidden_estado_categoria"/>
                                    <input type="checkbox" name="estado_categoria" id="estado_categoria" class="chk-col-green"/>
                                    <label for="estado_categoria">Activo</label>
                                </div>
                                <div class="col-sm-12 m-t-0">
                                    <input type="hidden" name="hidden_delivery_categoria" id="hidden_delivery_categoria"/>
                                    <input type="checkbox" name="delivery_categoria" id="delivery_categoria" class="chk-col-green"/>
                                    <label for="delivery_categoria">Delivery <i class="ti-info-alt text-warning font-10" data-original-title="¿Deseas mostrar esta presentación en tus deliverys?" data-toggle="tooltip" data-placement="top"></i></label>
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
                        <li class="box-label"><a href="javascript:void(0)" class="link" onclick="listarProductos('%')">Todos</a></li>
                    </ul>
                    <ul class="list-style-none display-categoria-list" style="font-size: 13px;">
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
                    <div class="card-header bg-light-inverse p-0">
                        <div class="row">
                            <div class="col-12">
                                <div class="social-widget">
                                    <div class="soc-content">
                                        <div class="col-4 b-r">
                                            <h1><i class="mdi mdi-food-fork-drink text-warning"></i></h1>
                                            <h5 class="text-warning">Platos y bedidas</h5>                            
                                        </div>
                                        <div class="col-4 b-r">
                                            <a href="combo">
                                                <h1><i class="mdi mdi-food text-muted"></i></h1>
                                                <h5 class="text-muted">Combos</h5>
                                            </a>                                    
                                        </div>
                                        <div class="col-4">
                                            <a href="insumo">
                                                <h1><i class="mdi mdi-food-variant text-muted"></i></h1>
                                                <h5 class="text-muted">Insumos</h5>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                   
                    </div>
                    <div class="card-body p-t-0 p-b-0 floating-labels">
                        <div class="row" style="margin-left: -20px; margin-right: -20px">
                            <!-- Column -->
                            <div class="col-lg-7 p-0">
                                <div class="row p-20">
                                    <div class="col-lg-12">
                                        <button class="btn btn-success btn-block btn-nuevo-producto" id="step2"><i class="fas fa-plus-circle"></i> Nuevo producto</button>
                                    </div>
                                    <div class="col-lg-12 m-t-20" id="filter_global">
                                        <div class="form-group m-b-0">
                                            <input class="form-control global_filter" id="global_filter" type="text" placeholder="Buscar producto">
                                            <span class="bar"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive b-t m-b-10">
                                    <table class="table table-condensed table-hover stylish-table" width="100%" id="table-productos">
                                        <thead class="table-head">
                                            <th style="width: 75%;">Producto</th>
                                            <th style="width: 10%; text-align: right">¿Transformable?</th>
                                            <th style="width: 10%; text-align: right">¿Activo?</th>
                                            <th style="width: 5%;"></th>
                                        </thead>
                                        <tbody class="tb-st"></tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Column -->
                            <div class="col-lg-5 p-t-20 p-b-20 b-l" style="background: #fbfbfb;" id="step3">
                                <div id="head-p"></div>
                                <div id="body-c"></div>
                                <div id="body-p"></div>
                            </div>
                            <!-- Column -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal inmodal" id="modal-producto" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInTop">
        <form id="form-producto" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_prod_producto" id="id_prod_producto">
            <div class="modal-header">
                <h4 class="modal-title">Detalle del producto</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body">
                <div class="row floating-labels">
                    <div class="col-md-12">
                        <div class="form-group display-flex">
                            <input class="form-control input-lg input-mayus" type="text" autocomplete="off" placeholder="Nombre" name="nombre_producto" id="nombre_producto" required="required"/>
                            <span class="bar"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            <label class="btn waves-effect waves-light btn-secondary" id="transf" data-original-title="Producto que debes PREPARAR ANTES DE VENDER, Ejm: Ceviches, parrillas o tragos" data-toggle="tooltip" data-placement="top">
                                <input type="radio" name="id_tipo" value="1" autocomplete="off"> Transformado
                            </label>
                            <label class="btn waves-effect waves-light btn-secondary" id="ntransf" data-original-title="Producto que has COMPRADO PARA VENDER, Ejm: Gaseosas, chifles" data-toggle="tooltip" data-placement="top">
                                <input type="radio" name="id_tipo" value="2" autocomplete="off"> No Transformado
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row floating-labels m-t-40">
                    <div class="col-sm-12">
                        <div class="form-group m-b-40">
                            <select class="selectpicker form-control" name="id_areap_producto" id="id_areap_producto" data-style="form-control btn-default" data-live-search-style="begins" data-live-search="true" title="Seleccionar" data-size="5" required="required">
                                <?php foreach($this->AreaProduccion as $key => $value): ?>
                                    <option value="<?php echo $value['id_areap']; ?>"><?php echo $value['nombre']; ?></option>
                                <?php endforeach; ?> 
                            </select>
                            <span class="bar"></span>
                            <label for="id_areap_producto">&Aacute;rea de producci&oacute;n <i class="ti-info-alt text-warning font-10" data-original-title="Area donde se elabora el producto" data-toggle="tooltip" data-placement="top"></i></label>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group m-b-40">
                            <input name="notas_producto" id="notas_producto" class="form-control" data-role="tagsinput" placeholder="add">
                            <span class="bar"></span>
                            <label for="notas_producto">Notas <i class="ti-info-alt text-warning font-10" data-original-title="Escribe una o varias notas, Ejm: SIN SAL, POCA SAL" data-toggle="tooltip" data-placement="top"></i></label>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group m-b-40 cbu">
                            <input type="hidden" id="categoria"/>
                            <select class="selectpicker form-control" name="id_catg_producto" id="id_catg_producto" data-style="form-control btn-default" data-live-search-style="begins" data-live-search="true" title="Seleccionar" data-size="5" required="required">  
                            </select>
                            <span class="bar"></span>
                            <label for="id_catg_producto">Categor&iacute;a <i class="ti-info-alt text-warning font-10" data-original-title="Categoría del producto" data-toggle="tooltip" data-placement="top"></i></label>
                        </div>
                    </div>
                    <div class="col-sm-12 m-t-10">
                        <input type="hidden" name="hidden_estado_producto" id="hidden_estado_producto"/>
                        <input type="checkbox" name="estado_producto" id="estado_producto" class="chk-col-green"/>
                        <label for="estado_producto">Activo</label>
                    </div>
                    <div class="col-sm-12 m-t-0">
                        <input type="hidden" name="hidden_delivery_producto" id="hidden_delivery_producto"/>
                        <input type="checkbox" name="delivery_producto" id="delivery_producto" class="chk-col-green"/>
                        <label for="delivery_producto">Delivery <i class="ti-info-alt text-warning font-10" data-original-title="¿Deseas mostrar esta presentación en tus deliverys?" data-toggle="tooltip" data-placement="top"></i></label>
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

<div class="modal inmodal" id="modal-presentacion" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInTop">
        <form id="form-presentacion" method="post" enctype="multipart/form-data">
        <input type="hidden" name="nombre_producto_presentacion" id="nombre_producto_presentacion">
        <input type="hidden" name="id_prod_presentacion" id="id_prod_presentacion">
        <input type="hidden" name="id_pres_presentacion" id="id_pres_presentacion">
            <div class="modal-header">
                <h4 class="modal-title">Presentaci&oacute;n del producto</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-5 b-r">
                        <div class="row m-t-20 m-b-20">
                            <div class="col-sm-12">
                                <div class="ct-wizard-azzure" id="wizardProfile">
                                    <div class="picture-container">
                                        <div class="picture" style="width: 150px; height:150px">
                                            <img src="<?php echo URL; ?>public/images/productos/default.png" class="picture-src" id="wizardPicturePreview"/>
                                            <input type="hidden" name="imagen" id="imagen2" value="default.png" />
                                            <input type="file" name="imagen" id="wizard-picture">
                                        </div>      
                                        <h6>Cambiar Imagen</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row floating-labels">
                            <div class="col-sm-6 m-t-40 m-b-20">
                                <input type="hidden" name="hidden_estado_presentacion" id="hidden_estado_presentacion"/>
                                <input type="checkbox" name="estado_presentacion" id="estado_presentacion" class="chk-col-green"/>
                                <label for="estado_presentacion">Activo</label>
                            </div>
                            <div class="col-sm-6 m-t-40 m-b-20" id="tp-1" style="display: none">
                                <input type="hidden" name="hidden_receta_presentacion" id="hidden_receta_presentacion" value=""/>
                                <input type="checkbox" name="receta_presentacion" id="receta_presentacion" class="chk-col-green" />
                                <label for="receta_presentacion">Tiene receta <i class="ti-info-alt text-warning font-10" data-original-title="¿Esta presentación requiere insumos o productos para su preparación?" data-toggle="tooltip" data-placement="top"></i></label>
                            </div>                
                            <div class="col-sm-6 m-t-40 m-b-20" id="tp-2" style="display: none">
                                <input type="checkbox" name="stock_presentacion" id="stock_presentacion" class="chk-col-green" />
                                <label for="stock_presentacion">Control Stock <i class="ti-info-alt text-warning font-10" data-original-title="¿Esta presentación requiere tener un control de entradas y salidas?" data-toggle="tooltip" data-placement="top"></i></label>
                            </div>
                            <div class="col-sm-6 m-b-20" id="">
                                <input type="hidden" name="hidden_impuesto_presentacion" id="hidden_impuesto_presentacion" value=""/>
                                <input type="hidden" name="igv_impuesto" id="igv_impuesto" value="<?php echo Session::get('igv'); ?>"/>
                                <input type="checkbox" name="impuesto_presentacion" id="impuesto_presentacion" class="chk-col-green"/>
                                <label for="impuesto_presentacion">Impuesto <?php echo Session::get('impAcr'); ?> <i class="ti-info-alt text-warning font-10" data-original-title="Esta opción te permite trabajar con productos exonerados a impuestos. La configuración 'Uso de ventas inafecta a impuestos' debe de estar inactiva" data-toggle="tooltip" data-placement="top"></i></label>
                            </div>
                            <div class="col-sm-6 m-b-20">
                                <input type="hidden" name="hidden_delivery_presentacion" id="hidden_delivery_presentacion"/>
                                <input type="checkbox" name="delivery_presentacion" id="delivery_presentacion" class="chk-col-green"/>
                                <label for="delivery_presentacion">Delivery <i class="ti-info-alt text-warning font-10" data-original-title="¿Deseas mostrar esta presentación en tus deliverys?" data-toggle="tooltip" data-placement="top"></i></label>
                            </div>
                            <input type="hidden" name="hidden_insumo_principal_presentacion" id="hidden_insumo_principal_presentacion"/>
                            <?php if(Session::get('opc_02') == 1) { ?>
                            <div class="col-sm-6 m-t-0">
                                <input type="checkbox" name="insumo_principal_presentacion" id="insumo_principal_presentacion" class="chk-col-green"/>
                                <label for="insumo_principal_presentacion">Insumo Princ. <i class="ti-info-alt text-warning font-10" data-original-title="¿Esta presentación contiene un insumo principal para su control de entradas y salidas?" data-toggle="tooltip" data-placement="top"></i></label>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="row floating-labels">
                            <div class="col-md-12">
                                <div class="form-group m-b-40">
                                    <input class="form-control input-lg input-mayus" type="text" autocomplete="off" name="presentacion_presentacion" id="presentacion_presentacion" placeholder="Nombre" required="required"/>
                                    <span class="bar"></span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group m-b-40">
                                    <textarea name="descripcion_presentacion" id="descripcion_presentacion" class="form-control"></textarea>
                                    <span class="bar"></span>
                                    <label for="descripcion_presentacion">Descripci&oacute;n de la presentaci&oacute;n <i class="ti-info-alt text-warning font-10" data-original-title="Escribe una descripción breve de la presentación" data-toggle="tooltip" data-placement="top"></i></label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group f m-b-40 letNumMayMin">
                                    <input type="text" class="form-control input-mayus cbu" name="cod_prod_presentacion" id="cod_prod_presentacion" autocomplete="off">
                                    <span class="bar"></span>
                                    <label for="cod_prod_presentacion">C&oacute;digo</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group f m-b-40 dec">
                                    <input type="text" class="form-control cbu" name="precio_presentacion" id="precio_presentacion" autocomplete="off" required>
                                    <span class="bar"></span>
                                    <label for="precio_presentacion">Precio venta - <?php echo Session::get('moneda'); ?></label>
                                </div>
                            </div>
                            <div class="col-sm-6" id="tp-3" style="display: none">
                                <div class="form-group f m-b-40 dec">
                                    <input type="text" class="form-control cbu" name="precio_delivery" id="precio_delivery" autocomplete="off">
                                    <span class="bar"></span>
                                    <label for="precio_delivery">Precio delivery - <?php echo Session::get('moneda'); ?> <i class="ti-info-alt text-warning font-10" data-original-title="Cantidad monetaria que se vende esta presentación solo para deliverys" data-toggle="tooltip" data-placement="top"></i></label>
                                </div>
                            </div>
                            <div class="col-sm-6" id="tp-4" style="display: none">
                                <div class="form-group m-b-40 ent">
                                    <input type="text" name="stock_min_presentacion" id="stock_min_presentacion" class="form-control cbu"autocomplete="off"/>
                                    <span class="bar"></span>
                                    <label for="stock_min_presentacion">Stock m&iacute;nimo <i class="ti-info-alt text-warning font-10" data-original-title="Considere este valor para el control de stock" data-toggle="tooltip" data-placement="top"></i></label>
                                </div>
                            </div>
                        </div>
                        <div id="mensaje-ins" style="margin-bottom: 10px"></div>
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
<div class="modal long-modal" id="modal-receta" tabindex="-1" role="dialog" aria-labelledby="longmodal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInTop">
        <form id="form-receta" method="post" class="form-receta">
            <div class="modal-header">
                <h4 class="modal-title">Detalle de la receta</h4>
            </div>
            <div class="modal-body" style="background:#f1f1f1;">
                <div class="row floating-labels m-t-0">
                    <div class="col-sm-12">
                        <div class="form-group m-b-0">
                            <input type="text" name="buscar_ingrediente" id="buscar_ingrediente" class="form-control bg-t" autocomplete="off" placeholder="B&uacute;squeda"/>
                            <span class="bar"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div><hr class="m-t-0 m-b-0"></div>
            <div class="modal-body list-ingredientes" style="display: none">
                <ul class="list-group">
                    <li class="list-group-item" style="background:#f1f1f1; font-size: 14px;">
                        <label>Insumo:</label> <span id="insumo"></span> - <label>Unidad de Medida:</label> <span class="label label-warning" id="medida"></span>
                    </li>
                    <li class="list-group-item floating-labels">
                        <div class="row" style="margin-bottom: -14px">
                            <input type="hidden" name="id_pres_receta" id="id_pres_receta"/>
                            <input type="hidden" name="id_ins_receta" id="id_ins_receta"/>
                            <input type="hidden" name="id_tipo_ins_receta" id="id_tipo_ins_receta"/>
                            <div class="col-sm-2 dec">
                                <div class="form-group m-b-0">
                                    <input type="text" name="cant_receta" id="cant_receta" class="form-control" style="text-align: center;" autocomplete="off" />
                                    <span class="bar"></span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <select name="id_med_receta" id="id_med_receta" class="selectpicker form-control" data-live-search="true" autocomplete="off" data-size="5">
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <small>Equivale a:<br><strong><span id="valor_ing">0</span> - <span id="desc_medida"></span></strong></small>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-right">
                                    <button type="submit" class="btn btn-sm btn-circle btn-orange"><i class="ti-plus"></i></button>
                                    <button type="button" class="btn btn-sm btn-danger btn-eliminar"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div> 
                    </li>
                </ul>
            </div>
            <div class="list-ingredientes" style="display: none"><hr class="m-t-0 m-b-0"></div>
        </form>
            <div class="modal-body p-0">
                <div class="table-responsive scroll_receta">   
                    <table class="table stylish-table m-l-0 m-r-0" width="100%">
                        <thead class="table-head">
                            <tr>
                                <th>Tipo</th>
                                <th>Categor&iacute;a</th>
                                <th>Nombre</th>                                    
                                <th>Cantidad</th>
                                <th>Unidad Medida</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st" id="table-receta"></tbody>
                    </table>   
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-cerrar-receta">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(function() {
    $('#config').addClass("active");
    function filterGlobal () {
    $('#table-productos').DataTable().search( 
        $('#global_filter').val()
    ).draw();
    }
    $('input.global_filter').on( 'keyup click', function () {
        filterGlobal();
    } );
});
</script>
