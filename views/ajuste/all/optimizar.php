<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Ajustes</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Sistema</a></li>
            <li class="breadcrumb-item active">Optimizaci&oacute;n de procesos</li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeIn ng-scope">
    <div class="row">
        <div class="col-sm-2 col-lg-2">
            <div class="card text-center">
                <div class="card-body">
                    <h1><i class="ti ti-receipt text-success"></i></h1>
                    <h4 class="card-title">Pedidos</h4>
                    <p class="card-text">Eliminar recursos temporales</p>
                    <a href="javascript:void(0)" class="btn btn-primary btn-block opt-ped">Optimizar</a>
                </div>
            </div>
        </div>
        <?php if(Session::get('usuid') == 1) { ?>
        <div class="col-sm-2 col-lg-2">
            <div class="card text-center">
                <div class="card-body">
                    <h1><i class="ti ti-ticket text-success"></i></h1>
                    <h4 class="card-title">Ventas</h4>
                    <a href="javascript:void(0)" class="btn btn-primary btn-block opt-ven">Restaurar</a>
                </div>
            </div>
        </div>
        <div class="col-sm-2 col-lg-2">
            <div class="card text-center">
                <div class="card-body">
                    <h1><i class="mdi mdi-food-fork-drink text-success"></i></h1>
                    <h4 class="card-title">Productos</h4>
                    <a href="javascript:void(0)" class="btn btn-primary btn-block opt-prod">Restaurar</a>
                </div>
            </div>
        </div>
        <div class="col-sm-2 col-lg-2">
            <div class="card text-center">
                <div class="card-body">
                    <h1><i class="mdi mdi-food-variant text-success"></i></h1>
                    <h4 class="card-title">Insumos</h4>
                    <a href="javascript:void(0)" class="btn btn-primary btn-block opt-ins">Restaurar</a>
                </div>
            </div>
        </div>
        <div class="col-sm-2 col-lg-2">
            <div class="card text-center">
                <div class="card-body">
                    <h1><i class="mdi mdi-account-multiple text-success"></i></h1>
                    <h4 class="card-title">Clientes</h4>
                    <a href="javascript:void(0)" class="btn btn-primary btn-block opt-clientes">Restaurar</a>
                </div>
            </div>
        </div>
        <div class="col-sm-2 col-lg-2">
            <div class="card text-center">
                <div class="card-body">
                    <h1><i class="mdi mdi-account-location text-success"></i></h1>
                    <h4 class="card-title">Proveedores</h4>
                    <a href="javascript:void(0)" class="btn btn-primary btn-block opt-proveedores">Restaurar</a>
                </div>
            </div>
        </div>
        <div class="col-sm-2 col-lg-2">
            <div class="card text-center">
                <div class="card-body">
                    <h1><i class="mdi mdi-folder-multiple-outline text-success"></i></h1>
                    <h4 class="card-title">Salones y mesas</h4>
                    <a href="javascript:void(0)" class="btn btn-primary btn-block opt-mesas">Restaurar</a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>