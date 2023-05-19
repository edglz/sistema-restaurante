<input type="hidden" id="url" value="<?php echo URL; ?>" />
<input type="hidden" id="rol_usr" value="<?php echo Session::get('rol'); ?>" />
<input type="hidden" id="fecha" value="<?php echo $fecha; ?>" />
<input type="hidden" id="hora" value="<?php echo $hora; ?>" />
<input type="hidden" id="cod_ape" value="<?php echo Session::get('aperturaIn'); ?>" />
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>" />
<input type="hidden" id="pc_ip" value="<?php echo Session::get('pc_ip'); ?>" />
<input type="hidden" id="pc_name" value="<?php echo Session::get('pc_name'); ?>" />
<input type="hidden" id="print_com" value="<?php echo Session::get('print_com'); ?>" />
<input type="hidden" id="tribAcr" value="<?php echo Session::get('tribAcr'); ?>" />
<input type="hidden" id="diAcr" value="<?php echo Session::get('diAcr'); ?>" />
<input type="hidden" name="codtipoped" id="codtipoped" value="1" />
<input type="hidden" name="codpagina" id="codpagina" value="1" />
<input type="hidden" id="codpestdelivery" value="" />
<input type="hidden" id="codsalonorigen" value="" />
<input type="hidden" id="codmesaorigen" value="" />
<input type="hidden" id="codigo_anular_venta" value="<?php echo $codigo_anular_venta; ?>" />
<div class="row">
    <div class="col-lg-12">
        <div class="card text-left">
            <div class="card-body">
                <div class="alert alert-success" role="alert">
                    <strong>SALON: <?= $this->mesa->desc_salon ?><br>
                        NRO MESA: <?= $this->mesa->id_mesa ?>
                    </strong>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8 pt-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Pedidos recientes</h4>
                <p class="card-text">Información de los pedidos recientes</p>
                <!-- LISTA DE PEDIDOS ACTIVOS -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive b-t m-b-10">
                            <table id="table" class="table table-hover table-condensed stylish-table" width="100%">
                                <thead class="table-head">
                                    <tr>
                                        <th width="10%">Fecha</th>
                                        <th width="15%">Personas</th>
                                        <th class="text-right" width="10%"></th>
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
    <div class="col-lg-4 u4">
        <div class="card card_height m-b-0 form_pedido" style="background: #3f3f3f;">
            <form id="form-nuevo-pedido" method="post" enctype="multipart/form-data" action="<?php echo URL; ?>venta/pedido_create/pc4" class="form-nuevo-pedido">
                <input type="hidden" class="id-mesa" name="id_mesa" id="id_mesa" value="<?= Session::get('id_mesa') ?>">
                <input type="hidden" name="cliente_id" id="cliente_id" value="1">
                <div class="card-body p-0 new">
                    <div class="scroll_pedidos">
                        <div class="cont01 justify-center" style="height: 870px;">
                            <div class="text-center cont01-2" style="display: block;">
                                <h2 class="text-white"><button type="button" class="btn btn-circle btn-lg btn-orange waves-effect waves-dark btn-nuevo-pedido"><i class="ti-plus"></i></button></h2>
                                <h4 class="text-white m-b-0">Nuevo pedido</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0" id="container_nuevo" style="display: none;">
                    <div class="cont01 justify-center" style="height: 870px;">
                        <div class="p-2">
                            <h2 class="text-center p-2">Pedido nuevo</h2>
                            <hr>
                            <div class="col-sm-12 display-personas floating-labels">
                                <div class="form-group m-t-20 m-b-40">
                                    <input id="tch3" name="personas" type="text" value="1" class="form-control text-center bg-t numero-personas" style="border-bottom: 1px solid #d9d9d9;" />
                                    <label form="tch3">Nro de personas</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block btn-crear-pedido">Crear pedido</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>