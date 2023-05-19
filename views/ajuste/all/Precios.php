<input type="hidden" id="url" value="<?php echo URL; ?>" />
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>" />
<input type="hidden" id="cod_ti" value="3" />
<style>
table td {
    text-align: center;
}
</style>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Ajustes</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Restaurante</a></li>
            <li class="breadcrumb-item active">Precios</li>
        </ol>
    </div>
</div>
<div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert_reload">
  <strong>Para que pueda surtir todos los cambios en la interfáz Tienes que recargar la página</strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="row p-1 offset-md-2">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center">Cambio de precios por categoria</h4>
                <p class="card-text text-center">Nota: Para que puedas cambiar el precio, debes tener registrado un precio para el día en todos los productos de dicha categoria
                    <br><strong>Si no es así, no se podrá cambiar.</strong>
                </p>
                <form id="frm_cambio_cat">
                    <div class="form-group">
                        <label for="frm_dia_cambio"></label>
                        <div class="form-group">
                            <label for="frm_select_categoria"></label>
                            <select class="form-control" id="frm_select_categoria">
                                <option value="">Favor de seleccionar la categoria</option>
                            </select>
                        </div>
                        <select class="form-control" id="frm_dia_cambio">
                            <option value="">Favor de seleccionar el día</option>
                            <option value="Lunes">Lunes</option>
                            <option value="Martes">Martes</option>
                            <option value="Miercoles">Miercoles</option>
                            <option value="Jueves">Jueves</option>
                            <option value="Viernes">Viernes</option>
                            <option value="Sabado">Sabado</option>
                            <option value="Domingo">Domingo</option>
                        </select>
                    </div>
                    <button type="button" name="" id="btn_change" class="btn btn-primary btn-block" data-id_cat="0">Cambiar
                        precio</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center">Cambio general</h4>
                <p class="card-text">Cambiar precio de todas las presentaciones</p>
                   <form id="frm_c_gral">
                    <div class="col-sm-4 my-1">
                        <label class="sr-only" for="">Precio</label>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Seleccione el día</label>
                        <select class="form-control" id="cambio_precio_general">
                            <option value="">Favor de seleccionar el día</option>
                            <option value="Lunes">Lunes</option>
                            <option value="Martes">Martes</option>
                            <option value="Miercoles">Miercoles</option>
                            <option value="Jueves">Jueves</option>
                            <option value="Viernes">Viernes</option>
                            <option value="Sabado">Sabado</option>
                            <option value="Domingo">Domingo</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary btn-block" id="btn_cambiar">Cambiar</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="message-box contact-box">
                    <h4 class="card-title">Categorías</h4>
                    <ul class="list-style-none display-categoria-list">
                        <li class="box-label"><a href="javascript:void(0)" class="link"
                                onclick="listarProductos('%')">Todos</a></li>
                    </ul>
                    <ul class="list-style-none display-categoria-list" style="font-size: 13px;">
                        <li class="divider"></li>
                        <div class="scroll_categoria" id="ul-cat"></div>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-t-0 p-b-0 floating-labels">
                        <div class="row" style="margin-left: -20px; margin-right: -20px">
                            <!-- Column -->
                            <div class="col-lg-12 p-0">
                                <div class="row p-20">
                                    <div class="col-lg-12 m-t-20" id="filter_global">
                                        <div class="form-group m-b-0">
                                            <input class="form-control global_filter" id="global_filter" type="text"
                                                placeholder="Buscar producto">
                                            <span class="bar"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive b-t m-b-10">
                                    <table class="table table-condensed table-hover stylish-table" width="100%"
                                        id="table-productos">
                                        <thead class="table-head">
                                            <th style="width: 30%; text-align: center">Producto</th>
                                            <th style="width: 10%; text-align: center">Precio actual</th>
                                            <th style="width: 10%; text-align: center">Agregar</th>
                                            <th style="width: 10%; text-align: center">Listar</th>
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
    <div class="col-lg-3">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <img class="card-img-top" src="holder.js/100x180/" alt="">
                    <div class="card-body">
                        <h4 class="card-title product_title">Favor de seleccionar un producto</h4>
                        <ul class="list-style-none display-categoria-list" style="font-size: 13px;">
                            <li class="divider"></li>
                            <div class="scroll_categoria" id="ul_precios"></div>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--MODAL PARA AÑADIR, CAMBIAR O HACER VARIAS COSAS -->
<div class="modal" id="modal_precios" tabindex="-1" role="dialog" aria-labelledby="modal_precios"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lbl_title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_prec" data-id_pres="0" data-nombre_prod="-">
                    <div class="col-sm-4 my-1">
                        <label class="sr-only" for="frm_precio">Precio</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <? echo session::get('moneda'); ?>
                                </div>
                            </div>
                            <input type="text" class="form-control" id="frm_precio" placeholder="Precio">
                            <b><small id="txt_error_precio" style="display: none;">-</small></b>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Día en que corresponde el precio</label>
                        <select class="form-control" id="frm_dia">
                            <option>Favor de ingresar el día</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btn_agregar">Agregar</button>
            </div>
        </div>
    </div>
</div>
<!-- FIN DE MODAL -->
<div class="modal" id="modal_edit" tabindex="-1" role="dialog" aria-labelledby="modal_edit" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lbl_edit_title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frm_edit" data-id_pres="0" data-dia="--" data-id_presentacion="0" data-nombre="0">
                    <div class="col-sm-12 my-1">
                        <label class="sr-only" for="frm_precio_edit">Precio</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <? echo session::get('moneda'); ?>
                                </div>
                            </div>
                            <input type="text" class="form-control" id="frm_precio_edit" placeholder="Precio">
                            <b><small id="txt_error_precio" style="display: none;">-</small></b>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btn_editar">Agregar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function() {
    $('#config').addClass("active");

    function filterGlobal() {
        $('#table-productos').DataTable().search(
            $('#global_filter').val()
        ).draw();
    }
    $('input.global_filter').on('keyup click', function() {
        filterGlobal();
    });
});
</script>