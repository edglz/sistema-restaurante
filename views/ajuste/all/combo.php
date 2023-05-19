<input type="hidden" id="url" value="<?php echo URL; ?>"/>
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<input type="hidden" id="cod_ti" value="1"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Ajustes</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Restaurante</a></li>
            <li class="breadcrumb-item active">Combos</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="card">         
            <div class="card-body">
                <div class="message-box contact-box">
                    <h4 class="card-title">Combos</h4>                    
                    <ul class="list-style-none">
                        <li class="divider"></li>
                        <li class="font-14">Permite crear productos que a su vez están compuestos por otros productos.
                            <br><br>Un combo puede contener varios productos, como por ejemplo:</li>
                    </ul>
                    <h6 class="card-title text-warning"><i class="mdi mdi-food display-4"></i> Combo 01</h6> 
                    <ul>
                        <li class="font-14">Hamburguesa</li>
                        <li class="font-14">Inka Cola 500 ml</li>                        
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light-inverse p-0">
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
                                            <h1><i class="mdi mdi-food text-warning"></i></h1>
                                            <h5 class="text-warning">Combos</h5>                                      
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
                                        <button class="btn btn-success btn-block btn-nuevo-combo" id="step1"><i class="fas fa-plus-circle"></i> Nuevo combo</button>
                                    </div>
                                    <div class="col-lg-12 m-t-20" id="filter_global">
                                        <div class="form-group m-b-0">
                                            <input class="form-control global_filter" id="global_filter" type="text" placeholder="Buscar combo">
                                            <span class="bar"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive b-t m-b-10">
                                    <table class="table table-condensed table-hover stylish-table" width="100%" id="table-productos">
                                        <thead class="table-head">
                                            <th>Combo</th>
                                            <th style="text-align: right">¿Activo?</th>
                                            <th></th>
                                        </thead>
                                        <tbody class="tb-st"></tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Column -->
                            <div class="col-lg-5 p-t-20 p-b-20 b-l" style="background: #fbfbfb;" id="step2">
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
                <h4 class="modal-title">Detalle del combo</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body">
                <div class="row floating-labels">
                    <div class="col-md-12">
                        <div class="form-group display-flex m-b-10">
                            <input class="form-control input-lg input-mayus" type="text" autocomplete="off" placeholder="Nombre" name="nombre_producto" id="nombre_producto" required="required"/>
                            <span class="bar"></span>
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
                        <div class="form-group m-b-40">
                            <textarea name="descripcion_producto" id="descripcion_producto" class="form-control"></textarea>
                            <span class="bar"></span>
                            <label for="descripcion_producto">Descripci&oacute;n del producto <i class="ti-info-alt text-warning font-10" data-original-title="Escribe una descripción breve del producto" data-toggle="tooltip" data-placement="top"></i></label>
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
                        <label for="delivery_producto">Delivery</label>
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
                <h4 class="modal-title">Presentaci&oacute;n del combo</h4>
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
                                            <img src="<?php echo URL; ?>public/img/productos/default.png" class="picture-src" id="wizardPicturePreview"/>
                                            <input type="hidden" name="imagen" id="imagen" value="default.png" />
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
                                <label for="receta_presentacion">Agregar Prod. <i class="ti-info-alt text-warning font-10" data-original-title="Esta opción te permite agregar productos para armar el combo" data-toggle="tooltip" data-placement="top"></i></label>
                            </div>                
                            <div class="col-sm-6 m-t-40 m-b-20" id="tp-2" style="display: none">
                                <input type="checkbox" name="stock_presentacion" id="stock_presentacion" class="chk-col-green" />
                                <label for="stock_presentacion">Control Stock</label>
                            </div>
                            <div class="col-sm-6 m-b-20" id="">
                                <input type="hidden" name="hidden_impuesto_presentacion" id="hidden_impuesto_presentacion" value=""/>
                                <input type="hidden" name="igv_impuesto" id="igv_impuesto" value="<?php echo Session::get('igv'); ?>"/>
                                <input type="checkbox" name="impuesto_presentacion" id="impuesto_presentacion" class="chk-col-green"/>
                                <label for="impuesto_presentacion">Impuesto <?php echo Session::get('impAcr'); ?> <i class="ti-info-alt text-warning font-10" data-original-title="Esta opción te permite trabajar con productos exonerados a impuestos. La configuración 'Uso de ventas inafecta a impuestos' debe de estar inactiva" data-toggle="tooltip" data-placement="top"></i></label>
                            </div>
                            <div class="col-sm-6 m-t-0">
                                <input type="hidden" name="hidden_delivery_presentacion" id="hidden_delivery_presentacion"/>
                                <input type="checkbox" name="delivery_presentacion" id="delivery_presentacion" class="chk-col-green"/>
                                <label for="delivery_presentacion">Delivery <i class="ti-info-alt text-warning font-10" data-original-title="¿Deseas mostrar esta presentación en tus deliverys?" data-toggle="tooltip" data-placement="top"></i></label>
                            </div>
                            <input type="hidden" name="hidden_insumo_principal_presentacion" id="hidden_insumo_principal_presentacion" value="0" />
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="row floating-labels">
                            <div class="col-md-12">
                                <div class="form-group m-b-40 letNumMayMin">
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
                                    <input type="text" class="form-control input-mayus cbu" name="cod_prod_presentacion" id="cod_prod_presentacion" autocomplete="off" required>
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
<div class="modal inmodal" id="modal-receta" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInTop">
        <form id="form-receta" method="post" class="form-receta">
            <div class="modal-header">
                <h4 class="modal-title">Detalle de los productos</h4>
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
                        <label>Producto:</label> <span id="insumo"></span> - <label>Unidad de Medida:</label> <span class="label label-warning" id="medida"></span>
                    </li>
                    <li class="list-group-item floating-labels">
                        <div class="row" style="margin-bottom: -14px">
                            <input type="hidden" name="id_pres_receta" id="id_pres_receta"/>
                            <input type="hidden" name="id_ins_receta" id="id_ins_receta"/>
                            <input type="hidden" name="id_tipo_ins_receta" id="id_tipo_ins_receta"/>
                            <div class="col-sm-2 ent">
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
                    <table class="table stylish-table table-hover m-l-0 m-r-0" width="100%">
                        <thead>
                            <tr>
                                <th>Tipo</th>
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
