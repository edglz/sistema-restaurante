<input type="hidden" id="condicion" value="0"/>
<input type="hidden" id="tipo_atencion" value="1"/>
<input type="hidden" id="tipo_atencion_opcional" value="1"/>
<div class="display-agrupacion-platos" style="display: none">
    <div class="row" id="agrupacion_platos_list"></div>
</div>
<div class="display-agrupacion-pedidos" style="display: none">
    <div class="row" id="agrupacion_pedidos_list"></div>
</div>
<div class="row display-lista" style="display: block;">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body p-t-0 p-b-0"></div>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs customtab" role="tablist">
                <li class="nav-item" id="tab1"> <a class="nav-link active" data-toggle="tab" href="#tab-1" role="tab"><span class="hidden-sm-up">Mesa</span> <span class="hidden-xs-down font-medium">MESA <span class="label label-success" id="cant_pedidos_mesa"></span></span></a> </li>
                <li class="nav-item" id="tab2"> <a class="nav-link" data-toggle="tab" href="#tab-2" role="tab"><span class="hidden-sm-up">Mostrador</span> <span class="hidden-xs-down font-medium">MOSTRADOR <span class="label label-success" id="cant_pedidos_most"></span></span></a> </li>
                <li class="nav-item" id="tab3"> <a class="nav-link" data-toggle="tab" href="#tab-3" role="tab"><span class="hidden-sm-up">Delivery</span> <span class="hidden-xs-down font-medium">DELIVERY <span class="label label-success" id="cant_pedidos_del"></span></span></a> </li>
                <li class="nav-item" id="tab4"> <a class="nav-link" data-toggle="tab" href="#tab-3" role="tab"><span class="hidden-sm-up">Portero</span> <span class="hidden-xs-down font-medium">PORTERO <span class="label label-success" id="cant_pedidos_del"></span></span></a> </li>

            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active" id="tab-1" role="tabpanel">
                    <div class="table-responsive p-0">
                        <table class="table stylish-table" width="100%">
                            <thead class="table-head">
                                <tr>
                                    <th width="10%">Mesa</th>
                                    <th width="40%">Cantidad/Producto</th>
                                    <th width="15%">Tiempo</th>
                                    <th width="15%">Estado</th>
                                    <th width="10%">Mozo</th>
                                    <th width="10%" class="text-right">Acci&oacute;n</th>
                                </tr>
                            </thead>
                            <tbody id="list_pedidos_mesa" class="tb-st"></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="tab-2" role="tabpanel">
                    <div class="table-responsive p-0">
                        <table class="table table-hover table-condensed stylish-table" width="100%">
                            <thead class="table-head">
                                <tr>
                                    <th width="10%">Pedido</th>
                                    <th width="40%">Cantidad/Producto</th>
                                    <th width="15%">Tiempo</th>
                                    <th width="15%">Estado</th>
                                    <th width="10%">Cajero</th>
                                    <th width="10%" class="text-right">Acci&oacute;n</th>
                                </tr>
                            </thead>
                            <tbody id="list_pedidos_most" class="tb-st"></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="tab-3" role="tabpanel">
                    <div class="table-responsive p-0">
                        <table class="table table-hover table-condensed stylish-table" width="100%">
                            <thead class="table-head">
                                <tr>
                                    <th width="10%">Pedido</th>
                                    <th width="40%">Cantidad/Producto</th>
                                    <th width="15%">Tiempo</th>
                                    <th width="15%">Estado</th>
                                    <th width="10%">Cajero</th>
                                    <th width="10%" class="text-right">Acci&oacute;n</th>
                                </tr>
                            </thead>
                            <tbody id="list_pedidos_del" class="tb-st"></tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="tab-4" role="tabpanel">
                    <div class="table-responsive p-0">
                        <table class="table table-hover table-condensed stylish-table" width="100%">
                            <thead class="table-head">
                                <tr>
                                    <th width="10%">Pedido</th>
                                    <th width="40%">Cantidad/Producto</th>
                                    <th width="15%">Tiempo</th>
                                    <th width="15%">Estado</th>
                                    <th width="10%">Cajero</th>
                                    <th width="10%" class="text-right">Acci&oacute;n</th>
                                </tr>
                            </thead>
                            <tbody id="list_pedidos_del" class="tb-st"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>