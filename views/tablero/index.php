<?php
    date_default_timezone_set($_SESSION["zona_horaria"]);
    setlocale(LC_ALL,"es_ES@euro","es_ES","esp");
    $fecha = date("d-m-Y h:i A");
    $fechaa = date("d-m-Y 07:00");
?>
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>" />
<br>
<div class="row">
    <div class="col-sm-12 col-lg-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Resumen general</h4>
                <div class="row floating-labels m-t-40">
                    <div class="col-sm-12">
                        <div class="form-group m-b-40">
                            <select class="selectpicker form-control" name="id_caja" id="id_caja"
                                data-style="form-control btn-default" data-size="5" data-live-search-style="begins"
                                data-live-search="true" autocomplete="off" required>
                                <?php foreach($this->Caja as $key => $value): ?>
                                <option value="<?php echo $value['id_apc']; ?>"><?php echo $value['desc_caja']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="bar"></span>
                            <label for="id_caja">Caja</label>
                        </div>
                    </div>
                    <!--
                    <div class="col-sm-12">
                        <div class="form-group m-b-40">
                            <input type="text" class="form-control text-left" name="start" id="start" value="<?php echo $fechaa.' AM'; ?>" autocomplete="off" required>
                            <span class="bar"></span>
                            <label for="start">Inicio</label>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group m-b-40">
                            <input type="text" class="form-control text-left" name="end" id="end" value="<?php echo $fecha; ?>" autocomplete="off" required>
                            <span class="bar"></span>
                            <label for="end">Final</label>
                        </div>
                    </div>
                    -->
                </div>
                <div class="d-flex flex-row">
                    <div class="align-self-center">
                        <span class="display-6 text-primary efectivo_real"></span>
                        <h5 class="text-muted">Efectivo real</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 p-0">
            <div class="card card-body p-0">
                <h4 class="card-title p-t-20 p-l-20 p-r-20 m-b-10">Por canal de venta</h4>
                <ul class="nav nav-tabs justify-content-end customtab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab1" role="tab" aria-selected="true"><span
                                class="hidden-sm-up">Aprobadas</span> <span class="hidden-xs-down">Aprobadas</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab2" role="tab" aria-selected="false"><span
                                class="hidden-sm-up">Anuladas</span> <span class="hidden-xs-down">Anuladas</span></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab1" role="tabpanel">
                        <div class="message-box p-0">
                            <div class="message-widget m-t-0">
                                <!-- Message -->
                                <a href="#">
                                    <div class="user-img"><span class="round bg-success"><i
                                                class="fas fa-box"></i></span></div>
                                    <div class="mail-contnet">
                                        <h5 class="monto-venta-salon"></h5> <span class="mail-desc">Nro de ventas: <span
                                                class="font-14 cantidad-venta-salon"></span></span> <span
                                            class="time">SALONES</span>
                                    </div>
                                </a>
                                <!-- Message -->
                                <a href="#">
                                    <div class="user-img"><span class="round bg-primary"><i
                                                class="fas fa-shopping-basket"></i></span></div>
                                    <div class="mail-contnet">
                                        <h5 class="monto-venta-mostrador"></h5> <span class="mail-desc">Nro de ventas:
                                            <span class="font-14 cantidad-venta-mostrador"></span></span> <span
                                            class="time">MOSTRADOR</span>
                                    </div>
                                </a>
                                <!-- Message -->
                                <a href="#">
                                    <div class="user-img"><span class="round bg-warning"><i
                                                class="fas fa-motorcycle"></i></span></div>
                                    <div class="mail-contnet">
                                        <h5 class="monto-venta-delivery"></h5> <span class="mail-desc">Nro de ventas:
                                            <span class="font-14 cantidad-venta-delivery"></span></span> <span
                                            class="time">DELIVERY</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab2" role="tabpanel">
                        <div class="message-box p-0">
                            <div class="message-widget m-t-0">
                                <!-- Message -->
                                <a href="#">
                                    <div class="user-img"><span class="round bg-success"><i
                                                class="fas fa-box"></i></span></div>
                                    <div class="mail-contnet">
                                        <h5 class="monto-venta-salon-i"></h5> <span class="mail-desc">Nro de ventas:
                                            <span class="font-14 cantidad-venta-salon-i"></span></span> <span
                                            class="time">SALONES</span>
                                    </div>
                                </a>
                                <!-- Message -->
                                <a href="#">
                                    <div class="user-img"><span class="round bg-primary"><i
                                                class="fas fa-shopping-basket"></i></span></div>
                                    <div class="mail-contnet">
                                        <h5 class="monto-venta-mostrador-i"></h5> <span class="mail-desc">Nro de ventas:
                                            <span class="font-14 cantidad-venta-mostrador-i"></span></span> <span
                                            class="time">MOSTRADOR</span>
                                    </div>
                                </a>
                                <!-- Message -->
                                <a href="#">
                                    <div class="user-img"><span class="round bg-warning"><i
                                                class="fas fa-motorcycle"></i></span></div>
                                    <div class="mail-contnet">
                                        <h5 class="monto-venta-delivery-i"></h5> <span class="mail-desc">Nro de ventas:
                                            <span class="font-14 cantidad-venta-delivery-i"></span></span> <span
                                            class="time">DELIVERY</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-9">
        <div class="row">
            <div class="col-sm-6 col-lg-4">
                <div class="card card-outline-success">
                    <div class="card-header p-2"></div>
                    <div class="card-body">
                        <h4 class="card-title">Ventas en efectivo</h4>
                        <div class="text-right"> <span class="text-muted">Total</span>
                            <h1 class="font-light"><sup><i class="ti-arrow-up text-success"></i></sup> <span
                                    class="pago_efe"></span></h1>
                        </div>
                        <span class="text-success pago_efe_porcentaje"></span>
                        <div class="progress">
                            <div class="progress-bar bg-success pago_efe_progressbar" role="progressbar"
                                style="width: 0.00%; height: 6px;" aria-valuenow="25" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card card-outline-info">
                    <div class="card-header p-2"></div>
                    <div class="card-body">
                        <h4 class="card-title">Ventas en tarjeta</h4>
                        <div class="text-right"> <span class="text-muted">Total</span>
                            <h1 class="font-light"><sup><i class="ti-arrow-up text-info"></i></sup> <span
                                    class="pago_tar"></span></h1>
                        </div>
                        <span class="text-info pago_tar_porcentaje"></span>
                        <div class="progress">
                            <div class="progress-bar bg-info pago_tar_progressbar" role="progressbar"
                                style="width: 0.00%; height: 6px;" aria-valuenow="25" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card card-outline-primary">
                    <div class="card-header p-2"></div>
                    <div class="card-body">
                        <h4 class="card-title">Total de ventas</h4>
                        <div class="text-right"> <span class="text-muted">Efectivo + Tarjetas</span>
                            <h1 class="font-light"><sup><i class="ti-arrow-up text-primary"></i></sup> <span
                                    class="total_ventas"></span></h1>
                        </div>
                        <span class="text-primary">100%</span>
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 100.00%; height: 6px;"
                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="font-10">Incluye descuentos, comisi&oacute;n delivery</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card bg-light-info">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="align-self-center">
                                <span class="display-6 text-info ingresos"></span>
                                <h6 class="text-muted">Varios conceptos</h6>
                                <h5>Ingresos caja</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card bg-light-danger">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="align-self-center">
                                <span class="display-6 text-danger egresos"></span>
                                <h6 class="text-muted">Varios conceptos</h6>
                                <h5>Egresos caja</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card bg-light-warning">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="align-self-center">
                                <span class="display-6 text-warning descuentos"></span>
                                <h6 class="text-muted">Varios conceptos</h6>
                                <h5>Descuentos</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card bg-light-primary">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="align-self-center">
                                <span class="display-6 text-warning comision-delivery"></span>
                                <h6 class="text-muted">Varios conceptos</h6>
                                <h5>Comisi&oacute;n delivery</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           
            <!-- <div class="col-sm-6 col-lg-4">
                <div class="card bg-light-success">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="align-self-center">
                                <span class="display-6 text-info ventas_portero"></span>
                                <h6 class="text-muted">Ventas portero</h6>
                                <h5>Ventas hechas el día de hoy</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="card bg-light-primary">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="align-self-center">
                                <span class="display-6 text-warning count_ventas_portero"></span>
                                <h6 class="text-muted">Ventas portero</h6>
                                <h5>Ventas en efectivo</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <?php if(Session::get('opc_02') == 1) { ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <div class="row">
                        <div class="col-12">
                            <div class="social-widget">
                                <div class="soc-header box-google" style="font-size: 20px;">Pollos vendidos</div>
                                <div class="soc-content">
                                    <div class="col-6 b-r">
                                        <h3 class="font-medium pollos-vendidos"></h3>
                                        <h5 class="text-muted">Vendidos</h5>
                                    </div>
                                    <div class="col-6">
                                        <h3 class="font-medium pollos-stock"></h3>
                                        <h5 class="text-muted">Stock</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body p-0">
                <div class="d-flex no-block p-20">
                    <h4 class="card-title">10 Productos mas vendidos</h4>
                </div>
                <div class="table-responsive b-t m-b-0">
                    <table class="table stylish-table">
                        <thead class="table-head">
                            <tr>
                                <th colspan="2">Producto</th>
                                <th>Ventas</th>
                                <th>Importe</th>
                                <th class="text-right">% Ventas</th>
                            </tr>
                        </thead>
                        <tbody id="lista_productos"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body p-0">
                <div class="d-flex no-block p-20">
                    <h4 class="card-title">10 Platos mas vendidos</h4>
                </div>
                <div class="table-responsive b-t m-b-0">
                    <table class="table stylish-table">
                        <thead class="table-head">
                            <tr>
                                <th colspan="2">Producto</th>
                                <th>Ventas</th>
                                <th>Importe</th>
                                <th class="text-right">% Ventas</th>
                            </tr>
                        </thead>
                        <tbody id="lista_platos"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>




