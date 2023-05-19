<input type="hidden" id="url" value="<?php echo URL; ?>" />
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>" />
<input type="hidden" id="cod_ti" value="3" />
<input type="hidden" id="ingredientes" value="">
<style>
    table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
</style>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Recetas</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>tablero" class="link">Inicio</a></li>
            <li class="breadcrumb-item">Recetas</li>
            <li class="breadcrumb-item active">Nueva</li>
        </ol>
    </div>
</div>
<input type="hidden" id="id_receta" value="<?php echo $this->id_rec;?>">
<div class="row">
    <div class="col-lg-3">
        <div class="card text-left">
            <div class="card-body">
                <h4 class="card-title">Información de la receta</h4>
                <p class="card-text">Ingrese los datos principales de la receta</p>
                <div class="form-group">
                    <label for=""></label>
                    <input type="text" name="" id="rec_nomb" class="form-control" placeholder="Escriba el nombre de la receta">
                    <small id="helpId" class="form-text text-muted">Nombre de receta</small>
                </div>
                <div class="form-group">
                    <select class="form-control selectpicker" data-live-search="true" id="n_catg" aria-placeholder="Seleccione una categoria">
                        <option selected>Categoria de receta</option>
                        <?php
                        foreach ($this->Catg as $k => $d) :
                        ?>
                            <option value="<?php echo $d->id_catg ?>"><?php echo $d->nombre; ?></option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                    <small id="helpId" class="form-text text-muted">Seleccionar una categoria de receta</small>
                </div>
                <div class="form-group">
                    <select class="form-control selectpicker" data-live-search="true" id="n_producto" aria-placeholder="Seleccione una producto">
                        <option selected value="">Seleccione una producto</option>
                        <?php
                        foreach ($this->Producto as $k => $d) :
                        ?>
                            <option value="<?php echo $d->id_prod ?>"><?php echo $d->nombre; ?></option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                    <small id="helpId" class="form-text text-muted">Seleccionar un producto</small>
                </div>
                <div class="form-group">
                    <select class="form-control" data-live-search="true" id="n_pres" aria-placeholder="Seleccione una presentación" disabled>
                        <option selected>Seleccione una presentación</option>
                    </select>
                    <small id="helpId" class="form-text text-muted">Seleccionar una presentación</small>
                </div>
                <input id="add_prep" class="btn btn-primary btn-block" type="button" value="Ingredientes">
            </div>
        </div>
        <div class="card text-left">
            <div class="card-body">
                <h4 class="card-title">Información de modulo</h4>
                <p class="card-text">En este apartado necesitas modificar a que producto va a estar dirigido la receta a registrar.</p>
                <button class="btn btn-success btn-block" id="btn-guardar-receta">Guardar receta</button>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center">Preparación</h4>
                <textarea id="prep_body" placeholder="Favor de escribir tu receta para este producto..."></textarea>
            </div>
        </div>
    </div>
</div>


<div class="modal " id="modal_prep" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Ingredientes de receta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                <div id="ingr_rec" class="col-lg-12">
                
                </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="new_ingrediente">Nuevo ingrediente</button>
            </div>
        </div>
    </div>
</div>



<div class="modal  bd-example-modal-lg " id="modal_ing" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Nuevo ingrediente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="non_prep" id="non_prep" class="chk-col-green" />
                            <label for="non_prep">¿Este ingrediente es un preparado? <i class="ti-info-alt text-warning font-10" data-original-title="Selecciona si el ingrediente es un preparado" data-toggle="tooltip" data-placement="top"></i></label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7" id="prep_c" style="display: none;">
                    <div class="form-group">
                        <select class="form-control selectpicker"  id="ing_name" data-live-search="true">
                            <option>Seleccione el ingrediente preparado</option>
                            <?php
                            foreach ($this->Preparados as $k => $d) :
                            ?>
                                <option value="<?php echo $d->nombre ?>"><?php echo $d->nombre; ?></option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-7" id="no_prep">
                    <div class="form-group">
                        <input type="text" class="form-control" id="ing_name_text" aria-describedby="helpId" placeholder="Nombre del ingrediente">
                        <small id="helpId" class="form-text text-muted">Nombre del ingrediente</small>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="form-group">
                        <select class="form-control selectpicker" id="unit_medida" data-live-search="true">
                            <option>Seleccione una medida</option>
                            <option value="UNIDADES">UNIDADES</option>
                            <option value="GRAMOS">GRAMOS</option>
                            <option value="KILOGRAMOS">KILOGRAMOS</option>
                            <option value="MILILITROS">MILILITROS</option>
                            <option value="LITROS">LITROS</option>
                            <option value="OZ">ONZAS</option>
                            <option value="C.C">CENTIMETRO CUBICO</option>
                            <option value="LBS">LIBRA</option>
                            <option value="C/S">CUCHARADA SOPERA</option>
                            <option value="C/C">CUCHARADA DE POSTRE</option>
                        </select>
                        <small id="helpId" class="form-text text-muted">Unidad de medida</small>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                      <input type="text"
                        class="form-control numeric" id="cantidad" aria-describedby="helpId" placeholder="Ingrese la cantidad">
                      <small id="helpId" class="form-text text-muted">Cantidad</small>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="agregar_ing">Agregar</button>
            </div>
        </div>
    </div>
</div>