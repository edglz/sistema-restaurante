<?php
setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
$fecha = date("Y-m-d");
$hora = date("H:i:s");
$codigo_anular_venta = date("dm");

?>

<input type="hidden" id="url" value="<?php echo URL; ?>"/>
<input type="hidden" id="rol_usr" value="<?php echo Session::get('rol'); ?>"/>
<input type="hidden" id="fecha" value="<?php echo $fecha; ?>"/>
<input type="hidden" id="hora" value="<?php echo $hora; ?>"/>
<input type="hidden" id="cod_ape" value="<?php echo Session::get('aperturaIn'); ?>"/>
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<input type="hidden" id="pc_ip" value="<?php echo Session::get('pc_ip'); ?>"/>
<input type="hidden" id="pc_name" value="<?php echo Session::get('pc_name'); ?>"/>
<input type="hidden" id="print_com" value="<?php echo Session::get('print_com'); ?>"/>
<input type="hidden" id="tribAcr" value="<?php echo Session::get('tribAcr'); ?>"/>
<input type="hidden" id="diAcr" value="<?php echo Session::get('diAcr'); ?>"/>
<input type="hidden" name="codtipoped" id="codtipoped" value="1"/>
<input type="hidden" name="codpagina" id="codpagina" value="1"/>
<input type="hidden" id="codpestdelivery" value=""/>
<input type="hidden" id="codsalonorigen" value=""/>
<input type="hidden" id="codmesaorigen" value=""/>
<input type="hidden" id="codigo_anular_venta" value="<?php echo $codigo_anular_venta; ?>"/>
<input type="hidden" id="pedido_seleccionado" value="">
<input type="hidden" id="usuid" value="<?= Session::get('usuid') ?>">

<div class="p-15"></div>
<div class="row u4-2">
    <div class="col-lg-8 u4 u4-2-1">
        <div class="card m-b-5">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs customtab" role="tablist">
                <li class="nav-item tab01"><a class="nav-link active" data-toggle="tab" href="#tabp-1" role="tab"><span class="hidden-sm-up">Mesas</span><span class="hidden-xs-down">Mesas</span></a></li>
               <?php 
                if(Session::get('rol') != -1){
                   ?>
                    <li class="nav-item tab02"><a class="nav-link" data-toggle="tab" href="#tabp-2" role="tab" onclick="mostrador()"><span class="hidden-sm-up">Mostrador</span><span class="hidden-xs-down">Mostrador</span></a></li>                
                <?php if(Session::get('rol') <> 10) { ?>
                <li class="nav-item tab03"><a class="nav-link" data-toggle="tab" href="#tabp-3" role="tab" onclick="delivery()"><span class="hidden-sm-up">Delivery</span><span class="hidden-xs-down">Delivery <span class="label label-rounded label-inverse pedidos-total-1">0</span></span></a></li>
                <?php } ?>
                   <?php 
                }
               ?>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="card-body display-estado-mesa p-t-10 p-b-0">
                    <div class="row">
                        <div class="col-md-5 col-8 align-self-center m-t-5">
                            <small class="label mesas-disponibles" style="background: #79df9d;" data-original-title="Mesa libre" data-toggle="tooltip" data-placement="right">&nbsp;</small>
                            <small class="label mesas-pago" style="background: #5a9bf3;" data-original-title="En proceso de pago" data-toggle="tooltip" data-placement="right">&nbsp;</small>
                            <small class="label mesas-ocupadas" style="background: #e7444c;" data-original-title="Mesa ocupada" data-toggle="tooltip" data-placement="right">&nbsp;</small>
                        </div>
                        <?php
                            if(Session::get('rol') != -1){
?>
 <?php
                            if(Session::get('rol') == 3):
                        ?>
                         <div class="col-lg-4 col-xs-6 col-sm-2 col-2">
                            <button class="btn btn-dark ml-4" onclick="actu('dividir')">Dividir</button><button class="btn btn-success ml-4" onclick="actu('facturar')">Cobrar</button>
                        </div>
                        <div class="col-md-3 col-4 align-self-center">
                            <div class="d-flex m-t-10 justify-content-end">
                                <div class="d-flex m-l-10">
                                    <form method="post" enctype="multipart/form-data" action="<?php echo URL; ?>venta/refrescar_mesas">
                                        <button class="btn btn-sm btn-primary" type="submit" data-original-title="Refrescar mesas" data-toggle="tooltip" data-placement="left"><i class="fas fa-sync-alt"></i></button>
                                    </form>
                                        &nbsp;&nbsp;
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-inverse dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item opc-cambiar-mesa" href="#modal-cambiar-mesa" data-toggle="modal">Cambiar mesa</a>
                                            <a class="dropdown-item opc-mover-pedidos" href="#modal-mover-pedidos" data-toggle="modal">Mover pedidos</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                            <div class="col-md-7 col-4 align-self-center">
                            <div class="d-flex m-t-10 justify-content-end">
                                <div class="d-flex m-l-10">
                                    <form method="post" enctype="multipart/form-data" action="<?php echo URL; ?>venta/refrescar_mesas">
                                        <button class="btn btn-sm btn-primary" type="submit" data-original-title="Refrescar mesas" data-toggle="tooltip" data-placement="left"><i class="fas fa-sync-alt"></i></button>
                                    </form>
                                        &nbsp;&nbsp;
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-inverse dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item opc-cambiar-mesa" href="#modal-cambiar-mesa" data-toggle="modal">Cambiar mesa</a>
                                            <a class="dropdown-item opc-mover-pedidos" href="#modal-mover-pedidos" data-toggle="modal">Mover pedidos</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
<?php
                            }
                        ?>
                     
                    </div>
                </div>
                <hr class="m-b-0 display-estado-mesa">
                <div class="tab-pane p-0 active" id="tabp-1" role="tabpanel">
                    <div class="vtabs customvtab">
                        <ul class="nav nav-tabs tabs-vertical" role="tablist">
                          <?php
                        $rol =  $_SESSION['rol'];
                       
                        if($rol != 5){
                            foreach($this->Salon as $key => $value): ?>
                                <li class="nav-item list-salones m-t-10"> <a class="nav-link" data-toggle="tab"  name="salones"  href="#tab-<?php echo $value['id_salon']; ?>" role="tab" id="tab<?php echo $cont++; ?>" onclick="mesa_list(<?php echo $value['id_salon']; ?>)"><span class="hidden-sm-up"><?php $cont_salon = $cont_salon + 1;echo 'SA'.$cont_salon; ?></span> <span class="hidden-xs-down font-14"><?php echo $value['descripcion'] ; ?></span> </a> </li>
                              <?php
                              endforeach;
                        }else{
                            $cont=1;
                            $cont_salon = 0; 
                            foreach($this->Salon as $key => $value):
                              foreach($this->Usuarios_rol  as $k => $d):
                                if($d->salon == $value['id_salon']):
                                ?>
                                 <li class="nav-item list-salones m-t-10"> <a class="nav-link" data-toggle="tab" name="salones" href="#tab-<?php echo $value['id_salon']; ?>" role="tab" id="tab<?php echo $cont++; ?>" onclick="mesa_list(<?php echo $value['id_salon']; ?>)"><span class="hidden-sm-up"><?php $cont_salon = $cont_salon + 1;echo 'SA'.$cont_salon; ?></span> <span class="hidden-xs-down font-14"><?php echo $value['descripcion'] ; ?></span> </a> </li>
                                <?php
                                endif;
                              endforeach;
                            endforeach;

                        }
                      
                      ?>

                        </ul>
                        <div class="tab-content w-100" style="padding-bottom: 10px;">

                            <?php $cont=1; $co=0; foreach($this->Salon as $key => $value): ?>
                            <div class="tab-pane tp<?php echo $cont++; ?>" id="tab-<?php echo $value['id_salon']; ?>" role="tabpanel">
                                <div class="row">
                                    <div class="col-sm-12 list-mesas scroll_mesas" data-toggle="buttons"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tabp-2" role="tabpanel">
                    <ul class="nav nav-tabs customtab" role="tablist">
                        <li class="nav-item mostrador01" data-toggle="tooltip" data-placement="bottom" data-original-title="Esperando confirmaci&oacute;n"> <a class="nav-link active" data-toggle="tab" href="#mostrador01" role="tab"><span class="hidden-sm-up"><i class="fas fa-bullhorn"></i></span><span class="hidden-xs-down"><i class="fas fa-bullhorn font-20"></i></span></a> </li>
                        <?php
                            if(Session::get('rol') != -1){
?>
 <li class="nav-item mostrador02" data-toggle="tooltip" data-placement="bottom" data-original-title="Pedidos en preparaci&oacute;n"> <a class="nav-link" data-toggle="tab" href="#mostrador02" role="tab"><span class="hidden-sm-up"><i class="fas fa-diagnoses"></i></span><span class="hidden-xs-down"><i class="fas fa-diagnoses font-20"></i></span></a> </li>
                        <li class="nav-item mostrador03" data-toggle="tooltip" data-placement="bottom" data-original-title="Pedidos entregados"> <a class="nav-link" data-toggle="tab" href="#mostrador03" role="tab"><span class="hidden-sm-up"><i class="fas fa-hands-helping"></i></span><span class="hidden-xs-down"><i class="fas fa-hands-helping font-20"></i></span></a> </li>
<?php
                            }
                        ?>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane m-b-10 active" id="mostrador01" role="tabpanel">
                            <div class="p-0">
                                <div class="p-l-10 p-t-10 b-b" style="display: flex; background: #ffcb91">
                                    <h6 class="text-white font-medium"><i class="ti-more"></i> ESPERANDO CONFIRMACION</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="row floating-labels m-t-30">
                                        <div class="col-5 col-sm-3 col-lg-3">
                                            <div class="row text-center m-t-0 p-l-10 p-r-10">
                                                <div class="col-lg-12 col-md-12 col-sm-12 m-b-20">
                                                    <h5 class="m-b-5 font-30 font-normal text-warning pedidos-mostrador-total"></h5>
                                                    <h6 class="font-bold">Pedidos</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-7 col-sm-9 col-lg-9">
                                            <div class="row">
                                                <div class="form-group col-12 m-b-20">
                                                    <input type="text" class="form-control search_filter_e" id="search_filter_e" autocomplete="off" placeholder="Buscar pedido">
                                                    <span class="bar"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="list-mostrador-confirmacion" class="table table-condensed table-hover stylish-table b-t" cellspacing="0" width="100%">
                                        <thead class="table-head">
                                            <tr>
                                                <th width="10%">Pedido</th>
                                                <th width="15%">Tiempo</th>
                                                <th width="60%">Cliente</th>
                                                <th width="15%" class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="tb-st"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane m-b-10" id="mostrador02" role="tabpanel">
                            <div class="p-0">
                                <div class="p-l-10 p-t-10 b-b" style="display: flex; background: #ffefa1">
                                    <h6 class="text-inverse font-medium"><i class="ti-more"></i> EN PREPARACION</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="row floating-labels m-t-30">
                                        <div class="col-5 col-sm-3 col-lg-3">
                                            <div class="row text-center m-t-0 p-l-10 p-r-10">
                                                <div class="col-lg-12 col-md-12 col-sm-12 m-b-20">
                                                    <h5 class="m-b-5 font-30 font-normal text-warning pedidos-mostrador-total"></h5>
                                                    <h6 class="font-bold">Pedidos</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-7 col-sm-9 col-lg-9">
                                            <div class="row">
                                                <div class="form-group col-12 m-b-20">
                                                    <input type="text" class="form-control search_filter_f" id="search_filter_f" autocomplete="off" placeholder="Buscar pedido">
                                                    <span class="bar"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="list-mostrador-preparacion" class="table table-condensed table-hover stylish-table b-t" cellspacing="0" width="100%">
                                        <thead class="table-head">
                                            <tr>
                                                <th width="10%">Pedido</th>
                                                <th width="15%">Tiempo</th>
                                                <th width="45%">Cliente</th>
                                                <th width="15%">Pago</th>
                                                <th width="15%" class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="tb-st"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane m-b-10" id="mostrador03" role="tabpanel">
                            <div class="p-0">
                                <div class="p-l-10 p-t-10 b-b" style="display: flex; background: #6ddccf">
                                    <h6 class="text-white font-medium"><i class="ti-more"></i> ENTREGADOS</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="row floating-labels m-t-30">
                                        <div class="col-5 col-sm-3 col-lg-3">
                                            <div class="row text-center m-t-0 p-l-10 p-r-10">
                                                <div class="col-lg-12 col-md-12 col-sm-12 m-b-20">
                                                    <h5 class="m-b-5 font-30 font-normal text-warning pedidos-mostrador-total"></h5>
                                                    <h6 class="font-bold">Pedidos</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-7 col-sm-9 col-lg-9">
                                            <div class="row">
                                                <div class="form-group col-12 m-b-20">
                                                    <input type="text" class="form-control search_filter_g" id="search_filter_g" autocomplete="off" placeholder="Buscar pedido">
                                                    <span class="bar"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="list-mostrador-entregados" class="table table-condensed table-hover stylish-table b-t" cellspacing="0" width="100%">
                                        <thead class="table-head">
                                            <tr>
                                                <th width="10%">Pedido</th>
                                                <th width="15%">Tiempo</th>
                                                <th width="45%">Cliente</th>
                                                <th width="15%">Pago</th>
                                                <th width="15%" class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="tb-st"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tabp-3" role="tabpanel">
                    <div class="card-body display-estado-pedidos p-t-10 p-b-0">
                        <div class="row">
                            <div class="col-md-5 col-8 align-self-center m-t-5">
                                <small class="label label-warning" data-original-title="Pedido para atenci&oacute;n" data-toggle="tooltip" data-placement="right">&nbsp;</small>
                                <small class="label label-primary" data-original-title="Pedido programado" data-toggle="tooltip" data-placement="right">&nbsp;</small>
                            </div>
                            <div class="col-md-7 col-4 align-self-center">
                                <div class="d-flex m-t-10 justify-content-end">
                                    <div class="d-flex m-l-10">
                                        <button class="btn btn-sm btn-primary" onclick="list_categorias_menu();"><i class="fas fa-clipboard-list"></i> Lista de productos</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="m-b-0 display-estado-pedidos">
                    <ul class="nav nav-tabs customtab" role="tablist">
                        <li class="nav-item delivery01" data-toggle="tooltip" data-placement="top" data-original-title="Esperando confirmaci&oacute;n"> <a class="nav-link active" data-toggle="tab" href="#delivery01" role="tab"><span class="hidden-sm-up"><i class="fas fa-bullhorn"></i></span><span class="hidden-xs-down"><i class="fas fa-bullhorn font-20"></i> <span class="label label-rounded label-inverse pedidos-total-1">0</span></span></a> </li>
                        <li class="nav-item delivery02" data-toggle="tooltip" data-placement="top" data-original-title="Pedidos en preparaci&oacute;n"> <a class="nav-link" data-toggle="tab" href="#delivery02" role="tab"><span class="hidden-sm-up"><i class="fas fa-diagnoses"></i></span><span class="hidden-xs-down"><i class="fas fa-diagnoses font-20"></i></span></a> </li>
                        <?php if(Session::get('rol') <> 5) { ?>
                        <li class="nav-item delivery03" data-toggle="tooltip" data-placement="top" data-original-title="Pedidos en camino"> <a class="nav-link" data-toggle="tab" href="#delivery03" role="tab"><span class="hidden-sm-up"><i class="fas fa-motorcycle"></i></span><span class="hidden-xs-down"><i class="fas fa-motorcycle font-20"></i></span></a> </li>
                        <li class="nav-item delivery04" data-toggle="tooltip" data-placement="top" data-original-title="Pedidos entregados"> <a class="nav-link" data-toggle="tab" href="#delivery04" role="tab"><span class="hidden-sm-up"><i class="fas fa-hands-helping"></i></span><span class="hidden-xs-down"><i class="fas fa-hands-helping font-20"></i></span></a> </li>
                        <?php } ?>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane m-b-10 active" id="delivery01" role="tabpanel">
                            <div class="p-0">
                                <div class="p-l-10 p-t-10 b-b" style="display: flex; background: #ffcb91;">
                                    <h6 class="text-white font-medium"><i class="ti-more"></i> ESPERANDO CONFIRMACION</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="row floating-labels m-t-30">
                                        <div class="col-5 col-sm-3 col-lg-3">
                                            <div class="row text-center m-t-0 p-l-10 p-r-10">
                                                <div class="col-lg-12 col-md-12 col-sm-12 m-b-20">
                                                    <h5 class="m-b-5 font-30 font-normal text-warning pedidos-total"></h5>
                                                    <h6 class="font-bold">Pedidos</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-7 col-sm-9 col-lg-9">
                                            <div class="row">
                                                <div class="form-group col-12 m-b-20">
                                                    <input type="text" class="form-control search_filter_a" id="search_filter_a" autocomplete="off" placeholder="Buscar pedido">
                                                    <span class="bar"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="list-delivery-confirmacion" class="table table-condensed table-hover stylish-table b-t" cellspacing="0" width="100%">
                                        <thead class="table-head">
                                            <tr>
                                                <th width="10%">Pedido</th>
                                                <th width="15%">Tiempo</th>
                                                <th width="30%">Cliente</th>
                                                <th width="15%">Entrega</th>
                                                <th width="15%">Pago</th>
                                                <th width="15%" class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="tb-st"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane m-b-10" id="delivery02" role="tabpanel">
                            <div class="p-0">
                                <div class="p-l-10 p-t-10 b-b" style="display: flex; background: #ffefa1;">
                                    <h6 class="text-inverse font-medium"><i class="ti-more"></i> EN PREPARACION</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="row floating-labels m-t-30">
                                        <div class="col-5 col-sm-3 col-lg-3">
                                            <div class="row text-center m-t-0 p-l-10 p-r-10">
                                                <div class="col-lg-12 col-md-12 col-sm-12 m-b-20">
                                                    <h5 class="m-b-5 font-30 font-normal text-warning pedidos-total"></h5>
                                                    <h6 class="font-bold">Pedidos</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-7 col-sm-9 col-lg-9">
                                            <div class="row">
                                                <div class="form-group col-12 m-b-20">
                                                    <input type="text" class="form-control search_filter_b" id="search_filter_b" autocomplete="off" placeholder="Buscar pedido">
                                                    <span class="bar"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="list-delivery-preparacion" class="table table-condensed table-hover stylish-table b-t" cellspacing="0" width="100%">
                                        <thead class="table-head">
                                            <tr>
                                                <th width="10%">Pedido</th>
                                                <th width="15%">Tiempo</th>
                                                <th width="30%">Cliente</th>
                                                <th width="15%">Entrega</th>
                                                <th width="15%">Pago</th>
                                                <th width="15%" class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="tb-st"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane m-b-10" id="delivery03" role="tabpanel">
                            <div class="p-0">
                                <div class="p-l-10 p-t-10 b-b" style="display: flex; background: #94ebcd;">
                                    <h6 class="text-inverse font-medium"><i class="ti-more"></i> EN CAMINO |&nbsp;Nota:&nbsp;<h6 class="text-black font-italic">Todos los pedidos de esta lista, faltan entregar el dinero.</h6></h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="row floating-labels m-t-30">
                                        <div class="col-5 col-sm-3 col-lg-3">
                                            <div class="row text-center m-t-0 p-l-10 p-r-10">
                                                <div class="col-lg-12 col-md-12 col-sm-12 m-b-20">
                                                    <h5 class="m-b-5 font-30 font-normal text-warning pedidos-total"></h5>
                                                    <h6 class="font-bold">Pedidos</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-7 col-sm-9 col-lg-9">
                                            <div class="row">
                                                <div class="form-group col-12 m-b-20">
                                                    <input type="text" class="form-control search_filter_c" id="search_filter_c" autocomplete="off" placeholder="Buscar pedido">
                                                    <span class="bar"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="list-delivery-enviados" class="table table-condensed table-hover stylish-table b-t" cellspacing="0" width="100%">
                                        <thead class="table-head">
                                            <tr>
                                                <th width="10%">Pedido</th>
                                                <th width="15%">Tiempo</th>
                                                <th width="30%">Cliente</th>
                                                <th width="15%">Entrega</th>
                                                <th width="15%">Pago</th>
                                                <th width="15%" class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="tb-st"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane m-b-10" id="delivery04" role="tabpanel">
                            <div class="p-0">
                                <div class="p-l-10 p-t-10 b-b" style="display: flex; background: #6ddccf;">
                                    <h6 class="text-white font-medium"><i class="ti-more"></i> ENTREGADOS</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="row floating-labels m-t-30">
                                        <div class="col-5 col-sm-3 col-lg-3">
                                            <div class="row text-center m-t-0 p-l-10 p-r-10">
                                                <div class="col-lg-12 col-md-12 col-sm-12 m-b-20">
                                                    <h5 class="m-b-5 font-30 font-normal text-warning pedidos-total"></h5>
                                                    <h6 class="font-bold">Pedidos</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-7 col-sm-9 col-lg-9">
                                            <div class="row">
                                                <div class="form-group col-12 m-b-20">
                                                    <input type="text" class="form-control search_filter_d" id="search_filter_d" autocomplete="off" placeholder="Buscar pedido">
                                                    <span class="bar"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="list-delivery-entregados" class="table table-condensed table-hover stylish-table b-t" cellspacing="0" width="100%">
                                        <thead class="table-head">
                                            <tr>
                                                <th width="10%">Pedido</th>
                                                <th width="15%">Tiempo</th>
                                                <th width="30%">Cliente</th>
                                                <th width="15%">Entrega</th>
                                                <th width="15%">Pago</th>
                                                <th width="15%" class="text-right">Total</th>
                                            </tr>
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
    <div class="col-lg-4 u4">
        <div class="card card_height m-b-0" style="background: #3f3f3f;">
            <form id="form-nuevo-pedido" method="post" enctype="multipart/form-data" action="<?php echo URL; ?>venta/pedido_create/pc1" class="form-nuevo-pedido">
            <input type="hidden" class="id-mesa" name="id_mesa" id="id_mesa">
            <input type="hidden" name="cliente_id" id="cliente_id" value="1">
            <div class="card-body card-body-right p-b-10" style="display: none">
                <div class="d-flex flex-wrap">
                    <div>
                        <h4 class="card-title m-b-0" style="font-weight: 600; color: #6a6a6a">
                            <span class="pedido-numero-icono"></span>
                            <span class="pedido-numero">Detalle</span>
                            <input type="hidden" id="nombre_mozo" value="" />
                        </h4>
                    </div>
                    <div class="ml-auto pedido-mozo" style="display: none;">
                        <div class="col-md-12">
                            <i class="fa fa-user text-warning font-12 pedido-mozo" data-original-title="" data-toggle="tooltip" data-placement="top"></i>
                        </div>
                    </div>
                    <div class="ml-auto display-pedido-programado" style="display: none;">
                        <div class="col-md-12">
                            <input type="checkbox" name="pedido_programado" id="pedido_programado" class="chk-col-red pedido_programado" value="0" />
                            <label for="pedido_programado" class="m-b-0">Programado</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="scroll_pedidos">
                    <div class="cont01 justify-center" style="height: 100%;">
                        <div class="text-center cont01-1">
                            <h2 class="text-white"><i class="ti ti-arrow-circle-left"></i></h2>
                            <h4 class="text-white">Seleccione <b class="nomPed">una mesa</b></h4>
                            <h6 class="text-white text-center">para ingresar o visualizar pedidos</h6>
                        </div>
                        <div class="text-center cont01-2" style="display: none;">
                            <h2 class="text-white"><button type="button" class="btn btn-circle btn-lg btn-orange waves-effect waves-dark btn-nuevo-pedido"><i class="ti-plus"></i></button></h2>
                            <h4 class="text-white m-b-0">Nuevo pedido</h4>
                        </div>
                    </div>
                    <div class="cont02 m-t-20" style="display: none;">
                        <div class="row p-t-0 p-l-20 p-r-20 p-b-10">
                            <div class="col-md-12 m-b-40 display-tipo-entrega" style="display: none;">
                                <label class="font-13 text-inverse">Tipo de entrega</label>
                                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                    <label class="btn waves-effect waves-light btn-tipo-entrega-1 btn-secondary active">
                                        <input type="radio" name="tipo_entrega" id="tipo_entrega_1" value="1" autocomplete="off" checked> A DOMICILIO
                                    </label>
                                    <label class="btn waves-effect waves-light btn-tipo-entrega-2 btn-secondary">
                                        <input type="radio" name="tipo_entrega" id="tipo_entrega_2" value="2" autocomplete="off"> POR RECOGER
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 display-hora-entrega floating-labels" style="display: none;">
                                <div class="form-group m-b-40">
                                    <select class="selectpicker form-control bg-t" name="hora_entrega" id="hora_entrega" data-style="form-control btn-default" title="Seleccionar" data-live-search="true" autocomplete="off" data-size="5" required>
                                        <optgroup>
                                            <option value="10:00:00">10:00 AM</option>
                                            <option value="10:30:00">10:30 AM</option>
                                            <option value="11:00:00">11:00 AM</option>
                                            <option value="11:30:00">11:30 AM</option>
                                            <option value="12:00:00">12:00 AM</option>
                                            <option value="12:30:00">12:30 AM</option>
                                            <option value="13:00:00">01:00 PM</option>
                                            <option value="13:30:00">01:30 PM</option>
                                            <option value="14:00:00">02:00 PM</option>
                                            <option value="14:30:00">02:30 PM</option>
                                            <option value="15:00:00">03:00 PM</option>
                                            <option value="15:30:00">03:30 PM</option>
                                            <option value="16:00:00">04:00 PM</option>
                                            <option value="16:30:00">04:30 PM</option>
                                            <option value="17:00:00">05:00 PM</option>
                                            <option value="17:30:00">05:30 PM</option>
                                            <option value="18:00:00">06:00 PM</option>
                                            <option value="18:30:00">06:30 PM</option>
                                            <option value="19:00:00">07:00 PM</option>
                                            <option value="19:30:00">07:30 PM</option>
                                            <option value="20:00:00">08:00 PM</option>
                                            <option value="20:30:00">08:30 PM</option>
                                            <option value="21:00:00">09:00 PM</option>
                                            <option value="21:30:00">09:30 PM</option>
                                            <option value="22:00:00">10:00 PM</option>
                                            <option value="22:30:00">10:30 PM</option>
                                        </optgroup>
                                    </select>
                                    <span class="bar"></span>
                                    <label for="hora_entrega">Hora de entrega</label>
                                </div>
                            </div>
                            <div class="col-md-12 display-telefono-cliente floating-labels" style="display: none;">
                                <div class="form-group m-b-20">
                                    <input type="text" class="form-control bg-t" name="telefono_cliente" id="telefono_cliente" autocomplete="off" required/>
                                    <span class="bar"></span>
                                    <label for="telefono_cliente">Tel&eacute;fono</label>
                                </div>
                            </div>
                            <div class="col-md-12 display-nombre floating-labels" style="display: none;">
                                <div class="form-group letNumMayMin m-t-20 m-b-40">
                                    <input type="text" class="form-control input-mayus bg-t" name="nomb_cliente" id="nomb_cliente" autocomplete="off" required/>
                                    <span class="bar"></span>
                                    <label for="nomb_cliente">Nombre cliente</label>
                                </div>
                            </div>                          
                            <div class="col-md-12 display-direccion-cliente floating-labels" style="display: none;">
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control input-mayus bg-t" name="direccion_cliente" id="direccion_cliente" autocomplete="off" required/>
                                    <span class="bar"></span>
                                    <label for="direccion_cliente">Direcci&oacute;n</label>
                                </div>
                            </div>
                            <div class="col-md-12 display-referencia-cliente floating-labels" style="display: none;">
                                <div class="form-group m-b-0">
                                    <input type="text" class="form-control input-mayus bg-t" name="referencia_cliente" id="referencia_cliente" autocomplete="off" required/>
                                    <span class="bar"></span>
                                    <label for="referencia_cliente">Referencia</label>
                                </div>
                            </div>
                            <div class="col-sm-12 display-repartidor floating-labels" style="display: none;">
                                <div class="form-group m-t-40 m-b-0">
                                    <select class="selectpicker form-control bg-t" name="id_repartidor" id="id_repartidor" data-style="form-control btn-default" title="Seleccionar" data-live-search-style="begins" data-live-search="true" required>
                                        <?php foreach($this->Repartidor as $key => $value): ?>
                                        <option value="<?php echo $value['id_usu']; ?>"><?php echo $value['nombres'].' '.$value['ape_paterno'].' '.$value['ape_materno']; ?></option>
                                        <?php endforeach; ?>
                                        <?php if(Session::get('opc_01') == 1) { ?>
                                        <optgroup>
                                            <option value="2222">RAPPI</option>
                                            <option value="3333">UBER</option>
                                            <option value="4444">GLOVO</option>
                                        </optgroup>
                                        <?php } ?>
                                    </select>
                                    <span class="bar"></span>
                                    <label for="id_repartidor">Repartidor</label>
                                </div>
                            </div>
                            <div class="col-sm-12 display-personas floating-labels" style="display: none;">
                                <div class="form-group m-t-20 m-b-40">
                                    <input id="tch3" name="nro_personas" type="text" value="1" class="form-control text-center bg-t numero-personas" style="border-bottom: 1px solid #d9d9d9;" readonly/>
                                    <label form="tch3">Nro de personas</label>
                                </div> 
                            </div>
                            <div class="col-sm-12 display-mozo floating-labels" style="display: none;">
                                <div class="form-group m-b-40">
                                    <select class="selectpicker form-control bg-t" name="id_mozo" id="id_mozo" data-style="form-control btn-default" title="Seleccionar" data-live-search-style="begins" data-live-search="true" required>
                                        <?php foreach($this->Mozo as $key => $value): ?>
                                        <option value="<?php echo $value['id_usu']; ?>"><?php echo $value['nombres'].' '.$value['ape_paterno'].' '.$value['ape_materno']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="bar"></span>
                                    <label for="id_mozo">Mozo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cont03 comment-widgets m-t-10" id="list-pedidos" style="display: none;height: 80%;"></div>
                    <div class="cont04 m-t-10" style="display: none;">
                        <div class="p-l-20 p-r-20">
                            <table class="table browser no-border">
                                <tbody>
                                    <tr>
                                        <td class="text-muted" width="120px;">Cliente</td>
                                        <td class="text-left font-medium pedido-cliente"></td>
                                    </tr>                                
                                    <tr class="display-delivery" style="display: none;">
                                        <td class="text-muted">Tel&eacute;fono</td>
                                        <td class="text-left pedido-telefono"></td>
                                    </tr>
                                    <tr class="display-delivery display-pedido-email" style="display: none;">
                                        <td class="text-muted">Email</td>
                                        <td class="text-left pedido-email"></td>
                                    </tr>
                                    <tr class="display-delivery display-pedido-direccion" style="display: none;">
                                        <td class="text-muted">Direcci&oacute;n</td>
                                        <td class="text-left pedido-direccion"></td>
                                    </tr>
                                    <tr class="display-delivery display-pedido-referencia" style="display: none;">
                                        <td class="text-muted">Referencia</td>
                                        <td class="text-left pedido-referencia"></td>
                                    </tr>
                                    <tr class="display-delivery" style="display: none;">
                                        <td class="text-muted" width="120px;">Entrega</td>
                                        <td class="text-left font-medium pedido-tipo-entrega"></td>
                                    </tr>
                                    <tr class="display-delivery display-hora-entrega" style="display: none;">
                                        <td class="text-muted" width="120px;">Hora</td>
                                        <td class="text-left font-medium pedido-hora-entrega"></td>
                                    </tr>
                                    <tr class="display-delivery display-pedido-repartidor" style="display: none;">
                                        <td class="text-muted">Repartidor</td>
                                        <td class="text-left pedido-repartidor"></td>
                                    </tr>
                                    <!--
                                    <tr class="display-delivery display-pedido-pago" style="display: none;">
                                        <td class="text-muted">Tipo pago</td>
                                        <td class="text-left pedido-pago"></td>
                                    </tr>
                                -->
                                    <tr class="display-pedido-monto">
                                        <td class="text-muted">Monto total</td>
                                        <td class="text-left pedido-total"></td>
                                    </tr>
                                    <!--
                                    <tr class="display-delivery display-pedido-total-amortizado" style="display: none;">
                                        <td class="text-muted">Monto amortizado</td>
                                        <td class="text-left pedido-total-amortizado"></td>
                                    </tr>
                                -->
                                </tbody>
                            </table>
                        </div>
                        <div class="p-l-10 p-t-10 b-b b-t bg-light-inverse" style="display: flex;">
                            <h6 class="text-inverse font-medium">Productos</h6>
                        </div>
                        <div class="comment-widgets m-t-0" id="list-pedidos-detalle"></div>
                    </div>
                </div>
            </div>
            <div class="card-footer card-footer-right m-t-10" style="display: none">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <div class="descriptive-icon-2 totalPagar animated fadeIn"></div>
                    </div>
                    <div class="col-7 text-right p-l-0">
                        <button type="button" class="btn btn-secondary btn-cancelar-pedido" data-toggle="tooltip" data-placement="top" data-original-title="Regresar"><i class="fas fa-undo-alt"></i></button>
                        <div class="btn-group display-opciones-pedido" style="display: none;">
                            <button type="button" class="btn btn-inverse dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="javascript:void(0);" class="dropdown-item opc-whatsapp-pedido"><i class="fab fa-whatsapp"></i> Enviar mensaje</a>
                                <div class="opc-editar-pedido"></div>                                
                                <div class="opc-facturar-pedido"></div>                                
                                <div class="opc-print-pedido"></div>                               
                                <a href="javascript:void(0);" class="dropdown-item opc-anular-pedido"><i class="fas fa-trash"></i> Anular pedido</a>
                            </div>
                        </div>
                        <span class="btn-submit-nuevo-pedido"></span>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<div id="card2"></div>
<div class="">
    <button class="waves-effect waves-light btn-inverse btn btn-circle btn-sm btn-up" style="bottom: 50px"><i class="ti-arrow-circle-up text-white"></i></button>
    <button class="waves-effect waves-light btn-inverse btn btn-circle btn-sm btn-down" style="bottom: 10px"><i class="ti-arrow-circle-down text-white"></i></button>
</div>

<div class="modal inmodal" id="modal-cambiar-mesa" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content animated bounceInTop">
        <form id="form-cambiar-mesa" method="post" enctype="multipart/form-data" action="<?php echo URL; ?>venta/CambiarMesa">
            <div class="modal-header">
                <h4 class="modal-title">Cambiar Mesa</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body p-0">     
                <div class="row floating-labels" style="margin-left: 0px; margin-right: 0px;">
                    <div class="col-sm-6 bg-light-inverse">
                        <h5 class="m-t-30 m-b-40"><i class="ti-angle-double-down"></i> Origen</h5>
                        <div class="form-group m-b-40">
                            <select class="selectpicker form-control" id="cod_salon_origen_opc01" data-style="form-control btn-default bg-transparent" title="Seleccionar" data-live-search-style="begins" data-live-search="true" required>
                                <?php foreach($this->Salon as $key => $value): ?>
                                <option value="<?php echo $value['id_salon']; ?>"><?php echo $value['descripcion']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="bar"></span>
                            <label for="cod_salon_origen_opc01">Sal&oacute;n</label>
                        </div>
                        <div class="form-group m-b-30">
                            <select class="selectpicker form-control" name="cod_mesa_origen_opc01" id="cod_mesa_origen_opc01" data-style="form-control btn-default bg-transparent" title="Seleccionar" data-live-search-style="begins" data-live-search="true" title="Seleccionar" required="required" data-size="5">
                                <option value="0"></option>
                            </select>
                            <span class="bar"></span>
                            <label for="cod_mesa_origen_opc01">Mesa</label>
                        </div>
                    </div>
                    <div class="col-sm-6 bg-header b-l">
                        <h5 class="m-t-30 m-b-40"><i class="ti-angle-double-up"></i> Destino</h5>
                        <div class="form-group m-b-40">
                            <select class="selectpicker form-control" id="cod_salon_destino_opc01" data-style="form-control btn-default bg-transparent" title="Seleccionar" data-live-search-style="begins" data-live-search="true" required>
                                <?php foreach($this->Salon as $key => $value): ?>
                                <option value="<?php echo $value['id_salon']; ?>"><?php echo $value['descripcion']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="bar"></span>
                            <label for="cod_salon_destino_opc01">Sal&oacute;n</label>
                        </div>
                        <div class="form-group m-b-30">
                            <select class="selectpicker form-control" name="cod_mesa_destino_opc01" id="cod_mesa_destino_opc01" data-style="form-control btn-default bg-transparent" title="Seleccionar" data-live-search-style="begins" data-live-search="true" title="Seleccionar" required="required" data-size="5">
                                <option value="0"></option>
                            </select>
                            <span class="bar"></span>
                            <label for="cod_mesa_destino_opc01">MSesa</label>
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

<div class="modal inmodal" id="modal-mover-pedidos" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content animated bounceInTop">
        <form id="form-mover-pedidos" method="post" enctype="multipart/form-data" action="<?php echo URL; ?>venta/MoverPedidos">
            <div class="modal-header">
                <h4 class="modal-title">Mover Pedidos</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body p-0">     
                <div class="row floating-labels" style="margin-left: 0px; margin-right: 0px;">
                    <div class="col-sm-6 bg-light-inverse">
                        <h5 class="m-t-30 m-b-40"><i class="ti-angle-double-down"></i> Origen</h5>
                        <div class="form-group m-b-40">
                            <select class="selectpicker form-control" id="cod_salon_origen_opc02" data-style="form-control btn-default bg-transparent" title="Seleccionar" data-live-search-style="begins" data-live-search="true" required>
                                <?php foreach($this->Salon as $key => $value): ?>
                                <option value="<?php echo $value['id_salon']; ?>"><?php echo $value['descripcion']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="bar"></span>
                            <label for="cod_salon_origen_opc02">Sal&oacute;n</label>
                        </div>
                        <div class="form-group m-b-30">
                            <select class="selectpicker form-control" name="cod_mesa_origen_opc02" id="cod_mesa_origen_opc02" data-style="form-control btn-default bg-transparent" title="Seleccionar" data-live-search-style="begins" data-live-search="true" title="Seleccionar" required="required" data-size="5">
                                <option value="0"></option>
                            </select>
                            <span class="bar"></span>
                            <label for="cod_mesa_origen_opc02">Mesa</label>
                        </div>
                    </div>
                    <div class="col-sm-6 bg-header b-l">
                        <h5 class="m-t-30 m-b-40"><i class="ti-angle-double-up"></i> Destino</h5>
                        <div class="form-group m-b-40">
                            <select class="selectpicker form-control" id="cod_salon_destino_opc02" data-style="form-control btn-default bg-transparent" title="Seleccionar" data-live-search-style="begins" data-live-search="true" required>
                                <?php foreach($this->Salon as $key => $value): ?>
                                <option value="<?php echo $value['id_salon']; ?>"><?php echo $value['descripcion']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="bar"></span>
                            <label for="cod_salon_destino_opc02">Sal&oacute;n</label>
                        </div>
                        <div class="form-group m-b-30">
                            <select class="selectpicker form-control" name="cod_mesa_destino_opc02" id="cod_mesa_destino_opc02" data-style="form-control btn-default bg-transparent" title="Seleccionar" data-live-search-style="begins" data-live-search="true" title="Seleccionar" required="required" data-size="5">
                                <option value="0"></option>
                            </select>
                            <span class="bar"></span>
                            <label for="cod_mesa_destino_opc02">Mesa</label>
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

<div class="modal inmodal" id="modal-cliente" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInRight">
        <form id="form-cliente" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_cliente" id="id_cliente">
        <input type="hidden" name="tipo_cliente" id="tipo_cliente">
            <div class="modal-header justify-center">
                <h4 class="modal-title-cliente"></h4>
                <div class="ml-auto m-t-10">
                    <input name="tipo_cli" type="radio" value="1" id="td_dni" class="with-gap radio-col-light-green"/>
                    <label for="td_dni"><?php echo Session::get('diAcr'); ?></label>
                    <input name="tipo_cli" type="radio" value="2" id="td_ruc" class="with-gap radio-col-light-green"/>
                    <label for="td_ruc"><?php echo Session::get('tribAcr'); ?></label>
                </div>
            </div>
            <div class="modal-body p-0 floating-labels">
                <div class="row" style="margin-left: 0px; margin-right: 0px;">
                    <!-- Column -->
                    <div class="col-lg-6 b-r">
                        <div class="row card-body p-0">
                            <div class="col-md-12 p-l-10 p-t-10 b-t b-b m-b-40 bg-light-info" style="display: flex;">
                                <h6 class="font-medium">Informaci&oacute;n personal</h6>
                            </div>                       
                            <div class="col-md-6 block01" style="display: block;">
                                <div class="form-group ent m-b-40">
                                    <input type="text" class="form-control dni" name="dni" id="dni" minlength="<?php echo Session::get('diCar'); ?>" maxlength="<?php echo Session::get('diCar'); ?>" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="dni" class="c-dni"><?php echo Session::get('diAcr'); ?></label>
                                </div>
                            </div>
                            <div class="col-md-6 block02" style="display: none;">
                                <div class="form-group ent m-b-40">
                                    <input type="text" class="form-control ruc" name="ruc" id="ruc" minlength="<?php echo Session::get('tribCar'); ?>" maxlength="<?php echo Session::get('tribCar'); ?>" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="ruc" class="c-ruc"><?php echo Session::get('tribAcr'); ?></label>
                                </div>
                            </div>
                            <div class="col-md-12 block07" style="display: none;">
                                <div class="form-group letNumMayMin m-b-40">
                                    <input type="text" class="form-control ruc input-mayus" name="razon_social" id="razon_social" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="razon_social">Raz&oacute;n Social</label>
                                </div>
                            </div>
                            <div class="col-md-12 block03" style="display: block;">
                                <div class="form-group letMayMin m-b-40">
                                    <input type="text" class="form-control dni input-mayus" name="nombres" id="nombres" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="nombres">Nombres</label>
                                </div>
                            </div>
                            <div class="col-md-6 block04" style="display: block;">
                                <div class="form-group letMayMin m-b-40">
                                    <input type="text" class="form-control dni input-mayus" name="ape_paterno" id="ape_paterno" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="ape_paterno">Apellido Paterno</label>
                                </div>
                            </div>
                            <div class="col-md-6 block04" style="display: block;">
                                <div class="form-group letMayMin m-b-40">
                                    <input type="text" class="form-control dni input-mayus" name="ape_materno" id="ape_materno" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="ape_materno">Apellido Materno</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <div class="col-lg-6">
                        <div class="row card-body bg-header p-0">
                            <div class="col-md-12 p-l-10 p-t-10 b-t b-b m-b-40 bg-light-inverse" style="display: flex;">
                                <h6 class="font-medium">Contacto</h6>
                            </div> 
                            <div class="col-md-6 block05" style="display: block;">
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control bg-transparent dni input-mayus" name="fecha_nac" id="fecha_nac" value="" autocomplete="off" data-mask="99-99-9999"/>
                                    <span class="bar"></span>
                                    <label for="fecha_nac">Fecha de Nacimiento</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ent m-b-40">
                                    <input type="text" class="form-control bg-transparent" name="telefono" id="telefono" value="" autocomplete="off"/>
                                    <span class="bar"></span>
                                    <label for="telefono">Tel&eacute;fono</label>
                                </div>
                            </div>
                            <div class="col-md-12 block06" style="display: block;">
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control bg-transparent dni" name="correo" id="correo" value="" autocomplete="off"/>
                                    <span class="bar"></span>
                                    <label for="correo">Correo electr&oacute;nico</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control bg-transparent" name="direccion" id="direccion" value="" autocomplete="off" required="required"/>
                                    <span class="bar"></span>
                                    <label for="direccion">Direcci&oacute;n</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group m-b-40">
                                    <input type="text" class="form-control bg-transparent input-mayus" name="referencia" id="referencia" value="" autocomplete="off"/>
                                    <span class="bar"></span>
                                    <label for="referencia">Referencia</label>
                                </div>
                            </div>
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

<div class="modal inmodal" id="modal-lista-menu" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated bounceInTop">
            <div class="modal-header">
                <h4 class="modal-title">Lista de productos</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
            </div>
            <div class="modal-body p-0">
                <!-- Nav tabs -->
                <div class="vtabs customvtab">
                    <ul class="nav nav-tabs tabs-vertical bg-header list_categorias_menu" role="tablist" style="min-width: 200px;"></ul>
                    <!-- Tab panes -->
                    <div class="tab-content w-100 p-0">
                        <div class="justify-center m-t-40 categoriamenu">
                            <div class="text-center">
                                <h2><i class="ti ti-arrow-circle-left"></i></h2>
                                <h4 class="">Seleccione una categor&iacute;a</b></h4>
                                <h6 class="text-center">para visualizar sus productos</h6>
                            </div>
                        </div>
                        <div class="tab-pane active" id="categoriamenu" role="tabpanel" style="display: none">
                            <div class="card-body p-0">
                                <div class="row floating-labels m-t-30">
                                    <div class="col-lg-7 offset-lg-5">
                                        <div class="row">
                                            <div class="form-group col-12 m-b-20">
                                                <input type="text" class="form-control search_filter_menu" id="search_filter_menu" autocomplete="off" placeholder="Buscar producto">
                                                <span class="bar"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-0">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive p-b-10">
                                            <table class="table table-condensed table-hover b-t" id="list_platos_menu" width="100%">
                                                <thead class="table-head">
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th class="text-right">Estado</th>
                                                    </tr>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal inmodal" id="modal-editar-pedido" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated bounceInTop">
        <form id="form-editar-pedido" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_pedido" id="id_pedido"/>
            <div class="modal-header b-0">
                <h4 class="modal-title">Confirmar pedido</h4>
            </div>
            <div class="modal-body">
                <div class="row floating-labels m-t-40">
                    <div class="col-md-12 display-pedido-repartidor-edit" style="display: block;">
                        <div class="form-group m-b-40 cbu">
                            <select class="selectpicker form-control p-0" name="id_repartidor_edit" id="id_repartidor_edit" data-style="form-control btn-default" title="Seleccionar" data-size="5" data-live-search-style="begins" data-live-search="true" autocomplete="off" >
                                <?php foreach($this->Repartidor as $key => $value): ?>
                                <option value="<?php echo $value['id_usu']; ?>"><?php echo $value['nombres'].' '.$value['ape_paterno'].' '.$value['ape_materno']; ?></option>
                                <?php endforeach; ?>
                                <optgroup>
                                    <option value="2222">RAPPI</option>
                                    <option value="3333">UBER</option>
                                    <option value="4444">GLOVO</option>
                                </optgroup>
                            </select>
                            <span class="bar"></span>
                            <label for="id_repartidor_edit">Repartidor</label>
                        </div>
                    </div>
                    <div class="col-md-12 display-entrega-programada floating-labels" style="display: block;">
                        <div class="form-group m-b-40">
                            <select class="selectpicker form-control" name="hora_entrega_edit" id="hora_entrega_edit" data-style="form-control btn-default" title="Seleccionar" data-live-search="true" autocomplete="off" data-size="5">
                                <optgroup>
                                    <option value="10:00:00">10:00 AM</option>
                                    <option value="10:30:00">10:30 AM</option>
                                    <option value="11:00:00">11:00 AM</option>
                                    <option value="11:30:00">11:30 AM</option>
                                    <option value="12:00:00">12:00 AM</option>
                                    <option value="12:30:00">12:30 AM</option>
                                    <option value="13:00:00">01:00 PM</option>
                                    <option value="13:30:00">01:30 PM</option>
                                    <option value="14:00:00">02:00 PM</option>
                                    <option value="14:30:00">02:30 PM</option>
                                    <option value="15:00:00">03:00 PM</option>
                                    <option value="15:30:00">03:30 PM</option>
                                    <option value="16:00:00">04:00 PM</option>
                                    <option value="16:30:00">04:30 PM</option>
                                    <option value="17:00:00">05:00 PM</option>
                                    <option value="17:30:00">05:30 PM</option>
                                    <option value="18:00:00">06:00 PM</option>
                                    <option value="18:30:00">06:30 PM</option>
                                    <option value="19:00:00">07:00 PM</option>
                                    <option value="19:30:00">07:30 PM</option>
                                    <option value="20:00:00">08:00 PM</option>
                                    <option value="20:30:00">08:30 PM</option>
                                    <option value="21:00:00">09:00 PM</option>
                                    <option value="21:30:00">09:30 PM</option>
                                    <option value="22:00:00">10:00 PM</option>
                                    <option value="22:30:00">10:30 PM</option>
                                </optgroup>
                            </select>
                            <span class="bar"></span>
                            <label for="hora_entrega_edit">Hora de entrega</label>
                        </div>
                    </div>
                    <div class="col-sm-12 display-tipo-pago floating-labels" style="display: block">
                        <div class="form-group m-b-40">
                            <select class="selectpicker form-control" name="id_tipo_pago" id="id_tipo_pago" data-style="form-control btn-default" data-live-search-style="begins" data-live-search="true">
                                <?php foreach($this->TipoPago as $key => $value): ?>
                                    <option label="<?php echo $value['id_pago']; ?>" value="<?php echo $value['id_tipo_pago']; ?>"><?php echo $value['descripcion']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="bar"></span>
                            <label for="id_tipo_pago">Tipo pago</label>
                        </div>
                    </div>
                    <div class="col-md-12 display-paga-con floating-labels" style="display: block">
                        <div class="form-group m-b-40">
                            <input type="text" class="form-control input-mayus" name="paga_con" id="paga_con" autocomplete="off"/>
                            <span class="bar"></span>
                            <label for="paga_con">Paga con</label>
                        </div>
                    </div>
                    <div class="col-md-12 display-comision-delivery floating-labels" style="display: block">
                        <div class="form-group m-b-40">
                            <input type="text" class="form-control input-mayus" name="comision_delivery" id="comision_delivery" autocomplete="off"/>
                            <span class="bar"></span>
                            <label for="comision_delivery">Comisi&oacute;n delivery</label>
                        </div>
                    </div>
                    <div class="col-md-12 display-entrega-programada floating-labels" style="display: block;">
                        <div class="form-group m-b-40 dec">
                            <input type="text" class="form-control" name="amortizacion" id="amortizacion" autocomplete="off"/>
                            <span class="bar"></span>
                            <label for="amortizacion">Amortizar - <?php echo Session::get('moneda'); ?></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer b-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Aceptar</button>
            </div>
        </form>
        </div>
    </div>
</div>

<div class="modal inmodal" id="modal-editar-venta-pago" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content animated bounceInTop">
        <form id="form-editar-venta-pago" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_venta" id="id_venta"/>
            <input type="hidden" name="id_venta_tipopago" id="id_venta_tipopago"/>
            <div class="modal-header b-0">
                <h4 class="modal-title">Editar tipo de pago</h4>
            </div>
            <div class="modal-body">
                <div class="row floating-labels m-t-40">
                    <div class="col-sm-12 display-tipo-pago floating-labels" style="display: block">
                        <div class="form-group m-b-40">
                            <select class="selectpicker form-control" name="id_tipo_pago_v" id="id_tipo_pago_v" data-style="form-control btn-default" data-live-search-style="begins" data-live-search="true">
                                <?php foreach($this->TipoPago as $key => $value): ?>
                                    <option label="<?php echo $value['id_pago']; ?>" value="<?php echo $value['id_tipo_pago']; ?>"><?php echo $value['descripcion']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="bar"></span>
                            <label for="id_tipo_pago_v">Tipo pago</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer b-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Aceptar</button>
            </div>
        </form>
        </div>
    </div>
</div>

<?php require_once('views/venta/compartido.php'); ?> 

<script type="text/javascript">
$(function() {
    $('#restau').addClass("active");
    $('#tab1').addClass("active");
    $('.tp1').addClass("active");
});
</script>
<?php if(Session::get('rol') <> 0) { ?>
<style type="text/css">
.btn-up{
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 25px;
}
.btn-down{
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 25px;
}
</style>
<?php } ?>
<script>
   
</script>