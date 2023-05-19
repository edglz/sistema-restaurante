<input type="hidden" id="url" value="<?php echo URL; ?>"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Ajustes</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Empresa</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste/usuario" class="link">Usuarios</a></li>
            <li class="breadcrumb-item active">Edici&oacute;n</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <form id="form" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_usu" id="id_usu" value="<?php echo $r = (isset($this->usuario['id_usu'])) ? $this->usuario['id_usu'] : ''; ?>"/>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12"><br><br>
                                <div class="ct-wizard-azzure" id="wizardProfile">
                                    <div class="picture-container">
                                        <div class="picture">
                                        <?php if ($this->usuario['id_usu'] != null ) { ?> 
                                            <img src="<?php echo URL; ?>public/images/users/<?php echo $this->usuario['imagen']; ?>" class="picture-src" id="wizardPicturePreview" title=""/>
                                            <input type="hidden" name="imagen" id="imagen" value="<?php echo $this->usuario['imagen']; ?>"/>
                                            <input type="file" name="imagen" id="wizard-picture">
                                        <?php } else { ?>
                                            <img src="<?php echo URL; ?>public/images/users/default-avatar.png" class="picture-src" id="wizardPicturePreview" title=""/>
                                            <input type="hidden" name="imagen" id="imagen" value="default-avatar.png" />
                                            <input type="file" name="imagen" id="wizard-picture">
                                        <?php } ?>
                                        </div>      
                                        <h6>Cambiar Imagen</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row floating-labels m-t-40">
                            <div class="col-md-6">
                                <div class="form-group ent m-b-40">
                                    <input type="text" class="form-control" name="dni" id="dni" value="<?php echo $r = (isset($this->usuario['dni'])) ? $this->usuario['dni'] : ''; ?>" autocomplete="off" minlength="<?php echo Session::get('diCar'); ?>" maxlength="<?php echo Session::get('diCar'); ?>" required="required"/>
                                    <span class="bar"></span>
                                    <label for="dni" class="c-dni"><?php echo Session::get('diAcr'); ?></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group letMayMin m-b-40">
                                    <input type="text" class="form-control input-mayus" name="nombres" id="nombres" value="<?php echo $r = (isset($this->usuario['nombres'])) ? $this->usuario['nombres'] : ''; ?>" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="nombres">Nombres</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group letMayMin m-b-40">
                                    <input type="text" class="form-control input-mayus" name="ape_paterno" id="ape_paterno" value="<?php echo $r = (isset($this->usuario['ape_paterno'])) ? $this->usuario['ape_paterno'] : ''; ?>" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="ape_paterno">Apellido Paterno</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group letMayMin m-b-40">
                                    <input type="text" class="form-control input-mayus" name="ape_materno" id="ape_materno" value="<?php echo $r = (isset($this->usuario['ape_materno'])) ? $this->usuario['ape_materno'] : ''; ?>" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="ape_materno">Apellido Materno</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control" name="email" id="email" value="<?php echo $r = (isset($this->usuario['email'])) ? $this->usuario['email'] : ''; ?>" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group m-b-40">
                                    <select class="selectpicker form-control" name="id_rol" id="id_rol" data-style="form-control btn-default" title="Seleccionar" data-live-search-style="begins" data-live-search="true" required="required">
                                        <?php foreach($this->Rol as $key => $value): ?>
                                            <option value="<?php echo $value['id_rol']; ?>"><?php echo $value['descripcion']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="bar"></span>
                                    <label for="id_rol">Cargo</label>
                                </div>
                                <input type="hidden" id="cod_rol" value="<?php echo $r = (isset($this->usuario['id_rol'])) ? $this->usuario['id_rol'] : ''; ?>">
                            </div>
                            <div class="col-sm-12" id="col-areap" style="display: none">
                                <div class="form-group m-b-40">
                                    <select class="selectpicker form-control" name="id_areap" id="id_areap" data-style="form-control btn-default" title="Seleccionar" data-live-search-style="begins" data-live-search="true" required>
                                        <?php foreach($this->AreaProduccion as $key => $value): ?>
                                            <option value="<?php echo $value['id_areap']; ?>"><?php echo $value['nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="bar"></span>
                                    <label for="id_areap">&Aacute;rea de Producci&oacute;n</label>
                                </div>
                                <input type="hidden" id="cod_area" value="<?php echo $r = (isset($this->usuario['id_areap'])) ? $this->usuario['id_areap'] : ''; ?>">
                            </div>
                            <div class="col-sm-12" id="col-piso" style="display: none">
                                <div class="form-group m-b-40">
                                    <select class="selectpicker form-control" name="id_mesa" id="id_mesa" data-style="form-control btn-default" title="Seleccionar" data-live-search-style="begins" data-live-search="true" required>
                                        <?php foreach($this->Mesas as $key => $value): ?>
                                            <option value="<?php echo $value['id_mesa']; ?>"><?php echo $value['desc_salon']; ?> - MESA <?=   $value['nro_mesa'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="bar"></span>
                                    <label for="id_areap">Piso</label>
                                </div>
                            </div>
                           


                            <div class="col-md-6">
                                <div class="form-group letNumMayMin m-b-40">
                                    <input type="text" class="form-control" name="usuario" id="usuario" value="<?php echo $r = (isset($this->usuario['usuario'])) ? $this->usuario['usuario'] : ''; ?>" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="usuario">Usuario</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group m-b-40">
                                    <input type="password" class="form-control" name="contrasena" id="contrasena" value="<?php echo $r = (isset($this->usuario['contrasena'])) ? base64_decode($this->usuario['contrasena']) : ''; ?>" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="contrasena">Contrase&ntilde;a</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="text-right">
                    <a href="<?php echo URL; ?>ajuste/usuario" class="btn btn-secondary">Cancelar</a>
                    <button class="btn btn-success" type="submit">Aceptar</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>