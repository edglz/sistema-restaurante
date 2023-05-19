<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<input type="hidden" id="opc_02" value="<?php echo Session::get('opc_02'); ?>"/>
<input type="hidden" id="cod_ape" value="<?php echo $this->apc; ?>"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Caja</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>informe" class="link">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>informe/finanza_arq" class="link">Informe de finanzas</a></li>
            <li class="breadcrumb-item active">Resumen de caja</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body p-0 bg-estado">
                <div class="text-center m-t-30">
                    <h3 class="card-title text-white m-b-0 text-codigo"></h3>
                    <h5 class="card-title text-white m-b-30 text-estado"></h5>
                </div>
            </div>
            <div class="card-body"> 
                <small class="text-muted">Usuario</small>
                <h6 class="c-usuario"></h6>
                <small class="text-muted p-t-10 db">Caja</small>
                <h6 class="c-caja"></h6>
                <small class="text-muted p-t-10 db">Turno</small>
                <h6 class="c-turno"></h6>
                <small class="text-muted p-t-10 db">Fecha de apertura</small>
                <h6 class="c-fecha-apertura"></h6>
                <small class="text-muted p-t-10 db">Fecha de cierre</small>
                <h6 class="c-fecha-cierre"></h6>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card-group">
            <div class="card" style="max-width: 400px;">
                <table class="table table-hover stylish-table m-b-0">
                    <tbody>
                        <tr class="p-10i">
                            <td style="width:50px;border-top:0px;"><span class="round"><i class="ti ti-harddrive text-white"></i></span>
                            </td>
                            <td style="border-top:0px;">
                                <h6>Apertura caja</h6><small class="text-muted"></small>
                            </td>
                            <td class="text-right font-regular c-monto-apertura" style="border-top:0px;"></td>
                        </tr>
                        <tr class="p-10i" onclick="ingresos()">
                            <td style="width:50px;"><span class="round bg-success"><i class="ti ti-arrow-up text-white"></i></span>
                            </td>
                            <td>
                                <h6>Ingresos</h6><small class="text-muted">Ventas, caja chica</small>
                            </td>
                            <td class="text-right font-regular c-total-ingreso"></td>
                        </tr>                        
                        <tr class="p-10i" onclick="egresos()">
                            <td style="width:50px;"><span class="round bg-danger"><i class="ti ti-arrow-down text-white"></i></span>
                            </td>
                            <td>
                                <h6>Egresos</h6><small class="text-muted">Caja chica</small>
                            </td>
                            <td class="text-right font-regular c-total-egreso"></td>
                        </tr>
                    </tbody>
                </table>
                <hr class="m-t-0">
                <div class="card-body weather-small p-0">
                    <div class="row">
                        <div class="col-6">
                            <div class="row">
                                <div class="col-12 b-r p-10">
                                    <div class="m-l-20">
                                        <div class="m-l-10">
                                            <h2 class="font-bold text-success m-b-0 c-monto-estimado"></h2>
                                            <small>Total en caja</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row">
                                <div class="col-12 p-10 align-self-center">
                                    <div class="d-flex m-l-20">
                                        <div class="display-7 text-mute"><i class="ti ti-wallet"></i></div>
                                        <div class="m-l-10">
                                            <h4 class="font-regular text-mute m-b-0 c-monto-efectivo"></h4>
                                            <small>Efectivo</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 p-10 align-self-center">
                                    <div class="d-flex m-l-20">
                                        <div class="display-7 text-mute"><i class="ti ti-credit-card"></i></div>
                                        <div class="m-l-10">
                                            <h4 class="font-regular text-mute m-b-0 c-monto-tarjeta"></h4>
                                            <small>Tarjeta</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
                <hr class="m-b-0 m-t-0">
                <div class="row">
                    <div class="col-12">
                        <div class="social-widget">
                            <div class="soc-content">
                                <div class="col-6 b-r">
                                    <h3 class="font-medium m-t-10 text-info c-monto-cierre"></h3>
                                    <h5 class="text-muted font-14 m-b-0">Monto cierre</h5>
                                    <h5 class="text-muted font-12 m-b-0">(Efectivo)</h5>
                                </div>
                                <div class="col-6">
                                    <h3 class="font-medium m-t-10 text-warning c-monto-diferencia"></h3>
                                    <h5 class="text-muted font-14 m-b-0">Diferencia</h5>
                                    <h5 class="text-muted font-12 m-b-0 name-c-monto-diferencia"></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>               
            </div>
            <div class="card">
                <ul class="nav nav-tabs customtab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab1" role="tab" aria-selected="true"><span class="hidden-sm-up text-tab">Ingresos</span> <span class="hidden-xs-down text-tab">Ingresos</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab2" role="tab" aria-selected="false" onclick="productos();"><span class="hidden-sm-up">Productos</span> <span class="hidden-xs-down">Productos</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab3" role="tab" aria-selected="false" onclick="anulaciones();"><span class="hidden-sm-up">Anulaciones</span> <span class="hidden-xs-down">Anulaciones</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab7" role="tab" aria-selected="false" onclick="canales();"><span class="hidden-sm-up">Canales</span> <span class="hidden-xs-down">Canales</span></a>
                    </li>
                    <!--
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab7" role="tab" aria-selected="false" onclick="venta_delivery_list('a');"><span class="hidden-sm-up">Delivery APP</span> <span class="hidden-xs-down">Delivery APP</span></a>
                    </li>
                -->
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab1" role="tabpanel">
                        <ul class="nav nav-tabs justify-content-end customtab" role="tablist">
                            <li class="nav-item nav-ing">
                                <a class="nav-link nav-ing-1 active" data-toggle="tab" href="#tab4" role="tab" aria-selected="true" onclick="venta_list(1,'a');"><span class="hidden-sm-up">Ventas</span> <span class="hidden-xs-down">Ventas</span></a>
                            </li>
                            <li class="nav-item nav-ing">
                                <a class="nav-link nav-ing-2" data-toggle="tab" href="#tab5" role="tab" aria-selected="false" onclick="caja_list_i('a');"><span class="hidden-sm-up">Caja chica</span> <span class="hidden-xs-down">Caja chica</span></a>
                            </li>
                            <li class="nav-item nav-egr">
                                <a class="nav-link nav-egr-1" data-toggle="tab" href="#tab6" role="tab" aria-selected="false" onclick="caja_list_e('a');"><span class="hidden-sm-up">Caja chica</span> <span class="hidden-xs-down">Caja chica</span></a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane panel-ing-1 active" id="tab4" role="tabpanel">
                                <div class="row text-center m-t-0 p-l-10 p-r-10">
                                    <div class="col-lg-3 col-md-3 text-center">
                                        <div class="btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-block btn btn-secondary btn-xs btn-est-1 m-t-10 m-b-0 active" onclick="venta_list(1,'a')">
                                                <input type="radio" name="options" id="option1" autocomplete="off" checked="">APROBADAS
                                            </label>
                                            <label class="btn btn-block btn btn-secondary btn-xs btn-est-2 m-t-5 m-b-0" onclick="venta_list(1,'i')">
                                                <input type="radio" name="options" id="option2" autocomplete="off" checked="">ANULADAS
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 m-t-20">
                                        <h5 class="m-b-0 font-regular ventas-monto"></h5><small>Total</small>
                                    </div>
                                    <div class="col-lg-3 col-md-3 m-t-20">
                                        <h5 class="m-b-0 font-regular ventas-desc"></h5><a href="javascript:void()" class="link"><small onclick="venta_list(2,'a')">Descuentos</small></a>
                                    </div>
                                    <div class="col-lg-3 col-md-3 m-t-20">
                                        <h5 class="m-b-0 font-regular ventas-oper"></h5><small onclick="">Operaciones</small>
                                    </div>
                                    <div class="col-md-12 m-b-10"></div>
                                </div>
                                <hr class="m-0">
                                <table class="table m-b-0" width="100%">
                                    <thead class="table-head">
                                        <tr>
                                            <th>Documento</th>
                                            <th>Número</th>
                                            <th class="text-right">Desc.</th>
                                            <th class="text-right">Total</th>
                                        </tr>
                                    </thead>
                                </table>
                                <div class="scroll-1">
                                    <table class="table table-hover stylish-table m-b-0" width="100%">
                                        <tbody class="tb-st" id="list-venta"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane panel-ing-2" id="tab5" role="tabpanel">
                                <div class="row text-center m-t-0 p-l-10 p-r-10">
                                    <div class="col-lg-3 col-md-3 text-center">
                                        <div class="btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-block btn btn-secondary btn-xs btn-est-1 m-t-10 m-b-0 active" onclick="caja_list_i('a')">
                                                <input type="radio" name="options" id="option1" autocomplete="off" checked="">APROBADAS
                                            </label>
                                            <label class="btn btn-block btn btn-secondary btn-xs btn-est-2 m-t-5 m-b-0" onclick="caja_list_i('i')">
                                                <input type="radio" name="options" id="option2" autocomplete="off" checked="">ANULADAS
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-5 m-t-20">
                                        <h5 class="m-b-0 font-regular caja-monto"></h5><small>Total</small></div>
                                    <div class="col-lg-4 col-md-4 m-t-20">
                                        <h5 class="m-b-0 font-regular caja-oper"></h5><small onclick="">Operaciones</small></div>
                                    <div class="col-md-12 m-b-10"></div>
                                </div>
                                <hr class="m-0">
                                <table class="table stylish-table m-b-0" width="100%">
                                    <thead class="table-head">
                                        <tr>
                                            <th width="40%">Recibido de</th>
                                            <th width="40%">Motivo</th>
                                            <th class="text-right" width="20%">Total</th>
                                        </tr>
                                    </thead>
                                </table>
                                <div class="scroll-1">
                                    <table class="table stylish-table m-b-0">
                                        <tbody class="tb-st" id="list-caja-i"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane panel-egr-1" id="tab6" role="tabpanel">
                                <div class="row text-center m-t-0 p-l-10 p-r-10">
                                    <div class="col-lg-3 col-md-3 text-center">
                                        <div class="btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-block btn btn-secondary btn-xs btn-est-1 m-t-10 m-b-0 active" onclick="caja_list_e('a')">
                                                <input type="radio" name="options" id="option1" autocomplete="off" checked="">APROBADAS
                                            </label>
                                            <label class="btn btn-block btn btn-secondary btn-xs btn-est-2 m-t-5 m-b-0" onclick="caja_list_e('i')">
                                                <input type="radio" name="options" id="option2" autocomplete="off" checked="">ANULADAS
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-5 m-t-20">
                                        <h5 class="m-b-0 font-regular caja-monto"></h5><small>Total</small></div>
                                    <div class="col-lg-4 col-md-4 m-t-20">
                                        <h5 class="m-b-0 font-regular caja-oper"></h5><small onclick="">Operaciones</small></div>
                                    <div class="col-md-12 m-b-10"></div>
                                </div>
                                <hr class="m-0">
                                <table class="table stylish-table m-b-0" width="100%">
                                    <thead class="table-head">
                                        <tr>
                                            <th width="15%">Tipo</th>
                                            <th width="35%">Entregado a</th>
                                            <th width="30%">Motivo</th>
                                            <th class="text-right" width="20%">Total</th>
                                        </tr>
                                    </thead>
                                </table>
                                <div class="scroll-1">
                                    <table class="table stylish-table m-b-0">
                                        <tbody class="tb-st" id="list-caja-e"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <div class="tab-pane" id="tab2" role="tabpanel">
                        <?php if(Session::get('opc_02') == 1) { ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="social-widget">
                                    <div class="soc-content">
                                        <div class="col-6 b-r">
                                            <h3 class="font-medium m-t-10 text-info c-pollos-vendidos"></h3>
                                            <h5 class="text-muted font-14 m-b-0">Vendidos</h5>
                                            <h5 class="text-muted font-12 m-b-0">(Pollos)</h5>
                                        </div>
                                        <div class="col-6">
                                            <h3 class="font-medium m-t-10 text-warning c-pollos-stock"></h3>
                                            <h5 class="text-muted font-14 m-b-0">Stock</h5>
                                            <h5 class="text-muted font-12 m-b-0">(Pollos)</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <table class="table stylish-table m-b-0 b-t" width="100%">
                            <thead class="table-head">
                                <tr>
                                    <th style="width: 55%;">Producto</th>
                                    <th class="text-right" style="width: 10%;">Cantidad</th>
                                    <th class="text-right" style="width: 30%;">Total</th>
                                </tr>
                            </thead>
                        </table>
                        <div class="scroll-2">
                            <table class="table stylish-table m-b-0">
                                <tbody class="tb-st" id="list-productos"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab3" role="tabpanel">
                        <div class="row text-center m-t-0">
                            <div class="col-lg-6 col-md-6 m-t-20">
                                <h5 class="m-b-0 font-regular anul-monto"></h5><small>Total</small></div>
                            <div class="col-lg-6 col-md-6 m-t-20">
                                <h5 class="m-b-0 font-regular anul-oper"></h5><small>Operaciones</small></div>
                            <div class="col-md-12 m-b-10"></div>
                        </div>
                        <hr class="m-0">
                        <table class="table stylish-table m-b-0" width="100%">
                            <thead class="table-head">
                                <tr>
                                    <th style="width: 55%;">Producto</th>
                                    <th class="text-right" style="width: 10%;">Cantidad</th>
                                    <th class="text-right" style="width: 30%;">Total</th>
                                </tr>
                            </thead>
                        </table>
                        <div class="scroll-3">
                            <table class="table stylish-table m-b-0">
                                <tbody class="tb-st" id="list-anulaciones"></tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="tab7" role="tabpanel">
                        <ul class="nav nav-tabs justify-content-end customtab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#tab8" role="tab" aria-selected="true"><span class="hidden-sm-up">Aprobadas</span> <span class="hidden-xs-down">Aprobadas</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab9" role="tab" aria-selected="false"><span class="hidden-sm-up">Anuladas</span> <span class="hidden-xs-down">Anuladas</span></a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab8" role="tabpanel">
                                <table class="table table-hover m-b-0">
                                    <tbody>
                                        <tr>
                                            <td style="width:50px;border-top:0px;"><span class="round bg-success"><i class="fas fa-box text-white"></i></span>
                                            </td>
                                            <td style="border-top:0px;">
                                                <h6 class="m-t-5 m-b-0">Salones</h6>Nro de ventas: <span class="cantidad-venta-salon"></span>
                                            </td>
                                            <td class="text-right font-regular monto-venta-salon" style="border-top:0px;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;"><span class="round bg-primary"><i class="fas fa-shopping-basket text-white"></i></span>
                                            </td>
                                            <td>
                                                <h6 class="m-t-5 m-b-0">Mostrador</h6>Nro de ventas: <span class="cantidad-venta-mostrador"></span>
                                            </td>
                                            <td class="text-right font-regular monto-venta-mostrador"></td>
                                        </tr>                        
                                        <tr>
                                            <td style="width:50px;"><span class="round bg-warning"><i class="fas fa-motorcycle text-white"></i></span>
                                            </td>
                                            <td>
                                                <h6 class="m-t-5 m-b-0">Delivery</h6>Nro de ventas: <span class="cantidad-venta-delivery"></span>
                                            </td>
                                            <td class="text-right font-regular monto-venta-delivery"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane" id="tab9" role="tabpanel">
                                <table class="table table-hover m-b-0">
                                    <tbody>
                                        <tr>
                                            <td style="width:50px;border-top:0px;"><span class="round bg-success"><i class="fas fa-box text-white"></i></span>
                                            </td>
                                            <td style="border-top:0px;">
                                                <h6 class="m-t-5 m-b-0">Salones</h6>Nro de ventas: <span class="cantidad-venta-salon-i"></span>
                                            </td>
                                            <td class="text-right font-regular monto-venta-salon-i" style="border-top:0px;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width:50px;"><span class="round bg-primary"><i class="fas fa-shopping-basket text-white"></i></span>
                                            </td>
                                            <td>
                                                <h6 class="m-t-5 m-b-0">Mostrador</h6>Nro de ventas: <span class="cantidad-venta-mostrador-i"></span>
                                            </td>
                                            <td class="text-right font-regular monto-venta-mostrador-i"></td>
                                        </tr>                        
                                        <tr>
                                            <td style="width:50px;"><span class="round bg-warning"><i class="fas fa-motorcycle text-white"></i></span>
                                            </td>
                                            <td>
                                                <h6 class="m-t-5 m-b-0">Delivery</h6>Nro de ventas: <span class="cantidad-venta-delivery-i"></span>
                                            </td>
                                            <td class="text-right font-regular monto-venta-delivery-i"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <!--
                        <table class="table table-hover m-b-0">
                            <tbody>
                                <tr>
                                    <td style="width:50px;border-top:0px;"><span class="round bg-success"><i class="fas fa-box text-white"></i></span>
                                    </td>
                                    <td style="border-top:0px;">
                                        <h6 class="m-t-5 m-b-0">Salones</h6>Nro de ventas: <span class="cantidad-venta-salon"></span>
                                    </td>
                                    <td class="text-right font-regular monto-venta-salon" style="border-top:0px;"></td>
                                </tr>
                                <tr>
                                    <td style="width:50px;"><span class="round bg-primary"><i class="fas fa-shopping-basket text-white"></i></span>
                                    </td>
                                    <td>
                                        <h6 class="m-t-5 m-b-0">Mostrador</h6>Nro de ventas: <span class="cantidad-venta-mostrador"></span>
                                    </td>
                                    <td class="text-right font-regular monto-venta-mostrador"></td>
                                </tr>                        
                                <tr>
                                    <td style="width:50px;"><span class="round bg-warning"><i class="fas fa-motorcycle text-white"></i></span>
                                    </td>
                                    <td>
                                        <h6 class="m-t-5 m-b-0">Delivery</h6>Nro de ventas: <span class="cantidad-venta-delivery"></span>
                                    </td>
                                    <td class="text-right font-regular monto-venta-delivery"></td>
                                </tr>
                            </tbody>
                        </table>
                        -->
                        <!--
                        <div class="row text-center m-t-0 p-l-10 p-r-10">
                            <div class="col-lg-6 col-md-6 m-t-20">
                                <h5 class="m-b-0 font-regular ventas-delivery-monto"></h5><small>Total</small>
                            </div>
                            <div class="col-lg-6 col-md-6 m-t-20">
                                <h5 class="m-b-0 font-regular ventas-delivery-oper"></h5><small>Operaciones</small>
                            </div>
                            <div class="col-md-12 m-b-10"></div>
                        </div>
                        <hr class="m-0">
                        <table class="table m-b-0" width="100%">
                            <thead class="table-head">
                                <tr>
                                    <th>#</th>
                                    <th>Documento</th>
                                    <th>Número</th>
                                    <th class="text-right">Desc.</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                        </table>
                        <div class="scroll-3">
                            <table class="table table-hover stylish-table m-b-0" width="100%">
                                <tbody class="tb-st" id="list-venta-delivery"></tbody>
                            </table>
                        </div>
                        -->
                    </div>
                    
                </div>
            </div>
        </div>
        <!--
        <div class="card m-b-0">
            <div class="panel-main-box">
                <div class="panel-left-aside">
                    <div class="open-panel"><i class="ti-angle-right"></i></div>
                    <div class="panel-left-inner">
                        <div class="form-material">
                            <input class="form-control p-20" type="text" placeholder="Search Contact">
                        </div>
                        <ul class="feeds panelonline style-none">
                            <li>
                                <div class="bg-info">A</div> Apertura Caja <span class="text-muted">S/ 150.00</span></li>
                            <li>
                                <div class="bg-success"><i class="ti-server text-white"></i></div> Ingresos<span class="text-muted">S/ 50.00</span></li>
                            <li>
                                <div class="bg-warning"><i class="ti-shopping-cart text-white"></i></div> Egresos<span class="text-muted">S/ 50.00</span></li>
                            <li>
                                <div class="bg-danger"><i class="ti-user text-white"></i></div> New user registered.<span class="text-muted">30 May</span></li>
                            <li>
                                <div class="bg-inverse"><i class="far fa-bell text-white"></i></div> New Version just arrived. <span class="text-muted">27 May</span></li>
                            <li>
                                <div class="bg-info"><i class="far fa-bell text-white"></i></div> You have 4 pending tasks. <span class="text-muted">Just Now</span></li>
                            <li>
                                <div class="bg-danger"><i class="ti-user text-white"></i></div> New user registered.<span class="text-muted">30 May</span></li>
                            <li>
                                <div class="bg-inverse"><i class="far fa-bell text-white"></i></div> New Version just arrived. <span class="text-muted">27 May</span></li>
                            <li>
                                <div class="bg-info"><i class="far fa-bell text-white"></i></div> You have 4 pending tasks. <span class="text-muted">Just Now</span></li>
                            <li class="p-20"></li>
                        </ul>
                      
                    </div>
                </div>
                <div class="panel-right-aside">
                    
                    <div class="panel-rbox">
                        <ul class="panel-list p-20">
                            <li>
                                <div class="panel-img"><img src="../assets/images/users/1.jpg" alt="user" /></div>
                                <div class="panel-content">
                                    <h5>James Anderson</h5>
                                    <div class="box bg-light-info">Lorem Ipsum is simply dummy text of the printing & type setting industry.</div>
                                </div>
                                <div class="panel-time">10:56 am</div>
                            </li>
                            <li>
                                <div class="panel-img"><img src="../assets/images/users/2.jpg" alt="user" /></div>
                                <div class="panel-content">
                                    <h5>Bianca Doe</h5>
                                    <div class="box bg-light-success">It’s Great opportunity to work.</div>
                                </div>
                                <div class="panel-time">10:57 am</div>
                            </li>
                            <li class="reverse">
                                <div class="panel-time">10:57 am</div>
                                <div class="panel-content">
                                    <h5>Steave Doe</h5>
                                    <div class="box bg-light-inverse">It’s Great opportunity to work.</div>
                                </div>
                                <div class="panel-img"><img src="../assets/images/users/5.jpg" alt="user" /></div>
                            </li>
                            <li class="reverse">
                                <div class="panel-time">10:57 am</div>
                                <div class="panel-content">
                                    <h5>Steave Doe</h5>
                                    <div class="box bg-light-inverse">It’s Great opportunity to work.</div>
                                </div>
                                <div class="panel-img"><img src="../assets/images/users/5.jpg" alt="user" /></div>
                            </li>
                            <li>
                                <div class="panel-img"><img src="../assets/images/users/3.jpg" alt="user" /></div>
                                <div class="panel-content">
                                    <h5>Angelina Rhodes</h5>
                                    <div class="box bg-light-primary">Well we have good budget for the project</div>
                                </div>
                                <div class="panel-time">11:00 am</div>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
        -->
    </div>
</div>
