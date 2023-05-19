<input type="hidden" id="url" value="<?php echo URL; ?>"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Inventario</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>inventario/ajuste" class="link">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>inventario/ajuste" class="link">Ajuste de stock</a></li>
            <li class="breadcrumb-item active">Nuevo</li>
        </ol>
    </div>
</div>
<form id="form" method="post">
<div class="row floating-labels">
    <div class="col-md-3">
        <div class="card">         
            <div class="card-body">
                <h4 class="card-title">Datos generales</h4>
                <div class="row m-t-40">
                    <div class="col-sm-12">
                        <div class="form-group m-b-40">
                            <select class="selectpicker form-control" name="id_tipo" id="id_tipo" data-style="form-control btn-default"  title="Seleccionar" required="required">
                                <option value="3">ENTRADA</option>
                                <option value="4">SALIDA</option>
                            </select>
                            <span class="bar"></span>
                            <label for="id_tipo">Tipo Operaci&oacute;n</label>
                        </div>
                    </div>
                    <div class="col-sm-12 floating-labels">
                        <div class="form-group m-b-40">
                            <select class="selectpicker form-control" name="id_responsable" id="id_responsable" data-style="form-control btn-default" data-live-search-style="begins" data-live-search="true" title="Seleccionar" autocomplete="off" required="required">
                                <?php foreach($this->Responsable as $key => $value): ?>
                                    <option value="<?php echo $value['id_usu']; ?>"><?php echo $value['nombres'].' '.$value['ape_paterno'].' '.$value['ape_materno']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="bar"></span>
                            <label for="id_responsable">Responsable</label>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group m-b-20">
                            <textarea name="motivo" id="motivo" class="form-control" rows="2" required="required"></textarea>
                            <span class="bar"></span>
                            <label for="motivo">Descripci&oacute;n</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card">         
            <div class="card-header">
                <h4 class="card-title m-t-5">Detalle</h4>
                <h6 class="card-subtitle">B&uacute;squeda del producto o insumo</h6>
                <div class="row m-t-0">
                    <div class="col-sm-12">
                        <div class="form-group m-b-0">
                            <input type="text" name="buscar_insumo" id="buscar_insumo" class="form-control bg-t" autocomplete="off" />
                            <span class="bar"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nvo-ins" style="display: none">
                <div class="card-body">
                    <ul class="list-group sortable-list">
                        <li class="list-group-item" style="background:#f1f1f1; font-size: 14px;">
                            <span>Insumo/Producto:</span> <span id="label-insumo"></span> - <span>Unidad de Medida:</span> <span class="label label-warning" id="label-medida"></span>
                        </li>
                        <li class="list-group-item">
                            <div class="row" style="margin-bottom: -14px">
                                <input type="hidden" name="id_tipo_ins_buscar" id="id_tipo_ins_buscar"/>
                                <input type="hidden" name="id_ins_buscar" id="id_ins_buscar"/>
                                <!--
                                <input type="hidden" name="insCant" id="insCant"/>
                                -->
                                <div class="col-sm-2 dec">
                                    <div class="form-group m-b-0">
                                        <input type="text" name="cantidad_buscar" id="cantidad_buscar" class="form-control" style="text-align: center;" autocomplete="off" />
                                        <span class="bar"></span>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <select name="medida_buscar" id="medida_buscar" class="selectpicker form-control" data-live-search="true" autocomplete="off" data-size="5">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <small>Equivale a:<br><strong><span id="cantidad_equivalente_buscar">0</span> - <span id="label-unidad-medida"></span></strong></small>
                                </div>
                                <div class="col-sm-1 text-right" style="padding: 0px;">
                                    <span><?php echo Session::get('moneda'); ?></span>
                                </div>
                                <div class="col-sm-2 dec">
                                    <div class="form-group m-b-0">
                                        <input class="form-control" type="text" name="precio_buscar" id="precio_buscar" style="text-align: center;" autocomplete="off" placeholder="P.U.">
                                        <span class="bar"></span>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="text-right">
                                        <button type="button" class="btn btn-sm btn-circle btn-orange btn-agregar-insumo"><i class="fas fa-plus"></i></button>
                                        <button type="button" class="btn btn-sm btn-danger btn-eliminar-insumo"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            </div> 
                        </li>
                    </ul>
                </div>
                <div><hr class="m-t-0 m-b-0"></div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">   
                    <table class="table stylish-table table-hover m-l-0 m-r-0 m-b-0" width="100%">
                        <thead class="table-head">
                            <tr>
                                <th>Tipo</th>
                                <th>Nombre</th>                                 
                                <th>Cantidad</th>
                                <th>Unidad Medida</th>
                                <th>P.U.</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="tb-st" id="table-detalle" width="100%"></tbody>
                    </table>   
                </div>
            </div>
            <div class="card-footer text-right">
                <a class="btn btn-secondary" href="<?php echo URL; ?>inventario/ajuste"> Cancelar</a>
                <button class="btn btn-success">Aceptar</button>
            </div>
        </div>
    </div>
</div>
</form>
<script id="table-detalle-template" type="text/x-jsrender" src="">
    {{for items}} 
        <tr class="active">
            <td>{{:tipo}}</td>
            <td>
                <input name="id_ins_insumo" type="hidden" value="{{:id_ins_insumo}}" />
                <input name="id_tipo_ins_insumo" type="hidden" value="{{:id_tipo_ins_insumo}}" />
                {{:nombre_insumo}}
            </td>
            <td>
                {{:cantidad_insumo}}
            </td>
            <td>
                <span class="label label-warning text-uppercase" name="unidad_medida_insumo">{{:unidad_medida_insumo}}</span>
            </td>
            <td>
                <?php echo Session::get('moneda'); ?> {{:precio_insumo}}
            </td>
            <td>
                <div class="text-right">
                    <a href="javascript:void(0)" class="text-danger delete ms-2" onclick="facturador.retirar({{:id}});"><i data-feather="trash-2" class="feather-sm fill-white"></i></a>
                </div>
            </td>
        </tr>
    {{/for}}
</script>