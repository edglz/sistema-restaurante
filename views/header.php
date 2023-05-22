<!doctypehtml>
    <html>

    <head>
        <meta charset="utf-8">
        <meta content="IE=edge" http-equiv="X-UA-Compatible">
        <meta content="width=device-width,initial-scale=1" name="viewport">
        <meta content="" name="description">
        <meta content="" name="author">
        <meta content="#444" name="theme-color">
        <meta content="#444" name="msapplication-navbutton-color">
        <meta content="yes" name="apple-mobile-web-app-capable">
        <link rel="stylesheet" href="https://npmcdn.com/flatpickr@4.6.13/dist/themes/material_green.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
   document.addEventListener('DOMContentLoaded', () => {
    flatpickr(".date");
   })

</script>
        <meta content="black-translucent" name="apple-mobile-web-app-status-bar-style">
        <link href="<?php echo URL; ?>public/images/favicons/favicon.ico" rel="shortcut icon" type="image/x-icon">
        <link href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-57x57.png" rel="apple-touch-icon" sizes="57x57">
        <link href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-60x60.png" rel="apple-touch-icon" sizes="60x60">
        <link href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-72x72.png" rel="apple-touch-icon" sizes="72x72">
        <link href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-76x76.png" rel="apple-touch-icon" sizes="76x76">
        <link href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-114x114.png" rel="apple-touch-icon" sizes="114x114">
        <link href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-120x120.png" rel="apple-touch-icon" sizes="120x120">
        <link href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-144x144.png" rel="apple-touch-icon" sizes="144x144">
        <link href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-152x152.png" rel="apple-touch-icon" sizes="152x152">
        <link href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-180x180.png" rel="apple-touch-icon" sizes="180x180">
        <link href="<?php echo URL; ?>public/images/favicons/favicon-32x32.png" rel="icon" sizes="32x32" type="image/png">
        <link href="<?php echo URL; ?>public/images/favicons/android-chrome-192x192.png" rel="icon" sizes="192x192" type="image/png">
        <link href="<?php echo URL; ?>public/images/favicons/favicon-96x96.png" rel="icon" sizes="96x96" type="image/png">
        <link href="<?php echo URL; ?>public/images/favicons/favicon-16x16.png" rel="icon" sizes="16x16" type="image/png">
        <link href="<?php echo URL; ?>public/images/favicons/manifest.json" rel="manifest">
        <title>RESTAURANT AMAZONICO</title>
        <link href="<?php echo URL; ?>public/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo URL; ?>public/plugins/toast-master/css/jquery.toast.css" rel="stylesheet">
        <link href="<?php echo URL; ?>public/css/style.css" rel="stylesheet">
        <link href="<?php echo URL; ?>public/css/style_all.css" rel="stylesheet">
        <link href="<?php echo URL; ?>public/css/colors/insaga.css" rel="stylesheet" id="theme">
        <link href="<?php echo URL; ?>public/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet">
        <link href="<?php echo URL; ?>public/plugins/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet" type="text/css">
        <link href="<?php echo URL; ?>public/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet">
        <link href="<?php echo URL; ?>public/plugins/formvalidation/formValidation.min.css" rel="stylesheet">
        <link href="<?php echo URL; ?>public/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css" rel="stylesheet">
        <link href="<?php echo URL; ?>public/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.css" rel="stylesheet">
        <link href="<?php echo URL; ?>public/plugins/bootstrap-tour/css/bootstrap-tour-standalone.min.css" rel="stylesheet">
        <link href="<?php echo URL; ?>public/plugins/wizard/wizard.css" rel="stylesheet">
        <link href="<?php echo URL; ?>public/plugins/jquery-ui-1.12.1/jquery-ui.css" rel="stylesheet">
        <script src="<?php echo URL; ?>public/plugins/jquery/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo URL; ?>public/js/feather.min.js" type="text/javascript"></script>
        <script src="<?php echo URL; ?>public/js/custom.js" type="text/javascript"></script>
        <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet"><input id="url" type="hidden" value="<?php echo URL; ?>"> <input id="fecha_gral" type="hidden" value="<?php echo Session::get('codx_g'); ?>">
    </head>
    <style>
        body.swal2-shown>[aria-hidden=true] {
            transition: .1s filter;
            filter: blur(10px) grayscale(90%)
        }
         /* Estilo personalizado para el footer */
    .footer-alert {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      padding: 10px;
    }
       
    </style>
    <style>
        #cover-spin {
            position: fixed;
            width: 100%;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            background-color: rgba(17, 17, 17, .411);
            z-index: 9999;
            display: none
        }

        @-webkit-keyframes spin {
            from {
                -webkit-transform: rotate(0)
            }

            to {
                -webkit-transform: rotate(360deg)
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0)
            }

            to {
                transform: rotate(360deg)
            }
        }

        #cover-spin::after {
            content: '';
            display: block;
            position: absolute;
            left: 48%;
            top: 40%;
            width: 40px;
            height: 40px;
            border-style: solid;
            border-color: #fff;
            border-top-color: transparent;
            border-width: 4px;
            border-radius: 50%;
            -webkit-animation: spin .5s both infinite;
            animation: spin .8 linear infinite
        }
    </style>
     
    <?php Session::init(); ?><?php if (Session::get('loggedIn') == true) : ?><div id="cover-spin"></div>

    <body class="card-no-border fix-header fix-sidebar" id="card1">

        <div id="main-wrapper">
            
            <header class="topbar" style="width:100%!important">
                <nav class="navbar navbar-expand-md navbar-light top-navbar">
                    <div class="navbar-header"><?php if (Session::get('rol') == 5) { ?><a href="javascript:void(0)" class="navbar-brand"><?php } else { ?><a href="<?php echo URL; ?>tablero" class="navbar-brand"><?php } ?><b><img src="<?php echo URL; ?>public/images/logo_prev1.png" width="50%" style="margin-top:-5px"></b></a></div>
                    <div class="navbar-collapse">
                        <ul class="navbar-nav mr-auto mt-md-0">
                            <li class="nav-item"><a href="javascript:void(0)" class="waves-effect waves-dark text-muted nav-link hidden-md-up nav-toggler"><i class="ti-menu"></i></a></li>
                            <li class="nav-item"><a href="javascript:void(0)" class="waves-effect waves-dark text-muted nav-link hidden-sm-down sidebartoggler"><i class="ti-menu"></i></a></li><?php if (Session::get('rol') == 5) { ?><li class="nav-item"><a href="<?php echo URL; ?>venta" class="waves-effect waves-dark text-muted nav-link"><i class="fas fa-desktop"></i></a></li><?php } ?><li class="nav-item search-box search-products" style="display:none"><a href="javascript:void(0)" class="waves-effect waves-dark text-muted nav-link"><i class="ti-search"></i></a>
                                <form class="app-search"><input id="buscar_producto" class="form-control" name="buscar_producto" placeholder="Buscar productos..." autocomplete="off"> <a class="srh-btn" onclick='document.getElementById("buscar_producto").value=""'><i class="ti-close"></i></a></form>
                            </li>
                        </ul>
                        <ul class="navbar-nav my-lg-0">
                            <li class="nav-item dropdown"><?php if (Session::get('opc_02') == 1) { ?><button class="waves-effect btn waves-light btn-primary btn-stock-pollo" type="button" onclick="stock_pollo()" style="display:none">Stock de pollo</button><?php } ?><?php if ((Session::get('rol') == 1 or Session::get('rol') == 2 or Session::get('rol') == 3) and Session::get('sunat') == 1) { ?><a href="<?php echo URL; ?>facturacion"><button class="waves-effect btn waves-light border-0 btn-secondary" type="button"><span class="btn-label"><img src="<?php echo URL; ?>public/images/logo-sunat.png" width="20px" height="20px"> Sunat</span> <span class="cont-sunat"></span></button></a><?php } ?><?php if (Session::get('rol') == 4) { ?><button class="waves-effect btn waves-light btn-primary" type="button" onclick="listarPedidos()">Por orden de llegada</button> <button class="waves-effect btn waves-light btn-warning" type="button" onclick="agruparPlatos()">Ordenar por tipo de plato o bebida</button> <button class="waves-effect btn waves-light btn-success" type="button" onclick="agruparPedidos()">Ordenar por pedidos</button><?php } ?><?php if (Session::get('rol') <> 4) { ?><a class="waves-effect waves-dark text-muted text-muted nav-link dropdown-toggle listar-pedidos-preparados" aria-expanded="false" aria-haspopup="true" data-toggle="dropdown"><i class="ti-bell"></i>
                                        <div class="t-notify"><span class="heartbit"></span> <span class="point"></span></div>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right mailbox scale-up">
                                        <ul>
                                            <li>
                                                <div class="drop-title">Pedidos preparados</div>
                                            </li>
                                            <li>
                                                <div class="lista-pedidos-preparados message-center"></div>
                                            </li>
                                        </ul>
                                    </div><a href="<?php echo URL; ?>tablero/logout" class="waves-effect waves-dark text-muted nav-link" data-toggle="tooltip" title="Salir"><i class="fas fa-sign-out-alt"></i></a><?php } ?>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <aside class="left-sidebar">
                <div class="scroll-sidebar">
                    <div class="user-profile" style="background:url(<?php echo URL; ?>public/images/background/user-info.jpg) no-repeat">
                        <div class="profile-img"><img src="<?php echo URL; ?>public/images/users/<?php echo Session::get('imagen'); ?>" alt="user"></div>
                        <div class="profile-text"><a href="#" class="dropdown-toggle u-dropdown" aria-expanded="true" aria-haspopup="true" data-toggle="dropdown" role="button"><?php echo Session::get('nombres'); ?></a>
                            <div class="dropdown-menu animated flipInY"><a href="<?php echo URL; ?>tablero/logout" class="dropdown-item"><i class="fa fa-power-off"></i> Salir</a></div>
                        </div>
                    </div>
                    <nav class="sidebar-nav">
                        <ul id="sidebarnav"><?php if (Session::get('rol') == 4) : ?><li id="area-p"><a href="<?php echo URL; ?>produccion" class="waves-effect waves-dark" aria-expanded="false"><i class="mdi mdi-tablet"></i><span class="hide-menu"> Producción</span></a></li><?php endif; ?><?php if (Session::get('rol') <> 4 && Session::get('rol') <> 7) : ?><li id="restau"><a href="<?php echo URL; ?>venta" class="waves-effect waves-dark" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu"> Punto de Venta</span></a></li><?php endif; ?><?php if (Session::get('rol') == 7) : ?><li id="restau"><a href="<?php echo URL; ?>venta/venta_portero" class="waves-effect waves-dark" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu"> Punto de Venta</span></a></li>
                                <?php if (Session::get('rol') == 1 or Session::get('rol') == 2 or Session::get('rol') == 3) : ?><li><a href="<?php echo URL; ?>caja/monitor" id="c-mon">Monitor de ventas</a></li><?php endif; ?>
                                    </ul>
                                </li><?php endif; ?><?php if (Session::get('rol') == 1 or Session::get('rol') == 2 or Session::get('rol') == 3) : ?><li id="caja"><a href="#" class="waves-effect waves-dark has-arrow" aria-expanded="false"><i class="mdi mdi-desktop-mac"></i><span class="hide-menu"> Caja</span></a>
                                    <ul class="collapse" aria-expanded="false">
                                        <li><a href="<?php echo URL; ?>caja/apercie" id="c-apc">Apertura y cierre</a></li>
                                        <li><a href="<?php echo URL; ?>caja/ingreso" id="c-ing">Ingresos</a></li>
                                        <li><a href="<?php echo URL; ?>caja/egreso" id="c-egr">Egresos</a></li>
                                        <li><a href="<?php echo URL; ?>caja/monitor_imp" id="c-i">Monitor de impresiones</a></li><?php if (Session::get('rol') == 1 or Session::get('rol') == 2) : ?><li><a href="<?php echo URL; ?>caja/monitor" id="c-mon">Monitor de ventas</a></li><?php endif; ?>
                                    </ul>
                                </li>
                                <li id="clientes"><a href="<?php echo URL; ?>cliente" class="waves-effect waves-dark" aria-expanded="false"><i class="mdi mdi-account-circle"></i><span class="hide-menu"> Clientes</span></a></li><?php if (Session::get('rol') != 3) : ?><li id="compras"><a href="#" class="waves-effect waves-dark has-arrow" aria-expanded="false"><i class="fas fa-dolly" style="margin-left:-1px;font-size:18px!important"></i><span class="hide-menu"> Compras</span></a>
                                        <ul class="collapse" aria-expanded="false">
                                            <li><a href="<?php echo URL; ?>compra" id="c-compras">Todas las compras</a></li>
                                            <li><a href="<?php echo URL; ?>compra/proveedor" id="c-proveedores">Proveedores</a></li>
                                        </ul>
                                    </li>
                                    <li id="creditos"><a href="#" class="waves-effect waves-dark has-arrow" aria-expanded="false"><i class="mdi mdi-credit-card"></i><span class="hide-menu"> Creditos</span></a>
                                        <ul class="collapse" aria-expanded="false">
                                            <li><a href="<?php echo URL; ?>credito" id="cr-compras">Compras</a></li>
                                        </ul>
                                    </li><?php endif;
                                                                                                                                                                                                                                endif; ?><?php if (Session::get('rol') == 1 or Session::get('rol') == 2 or Session::get('rol') == 3) : ?><li id="inventario"><a href="#" class="waves-effect waves-dark has-arrow" aria-expanded="false"><i class="mdi mdi-archive"></i><span class="hide-menu"> Inventario</span></a>
                                        <ul class="collapse" aria-expanded="false">
                                            <li><a href="<?php echo URL; ?>inventario/stock" id="i-stock">Stock</a></li><?php if (Session::get('rol') == 1 or Session::get('rol') == 2) : ?><li><a href="<?php echo URL; ?>inventario/kardex" id="i-karval">Kardex valorizado</a></li>
                                                <li><a href="<?php echo URL; ?>inventario/ajuste" id="i-entsal">Ajuste de stock</a></li><?php endif; ?>
                                        </ul>
                                    </li>
                                    <li id="preparaciones"><a href="#" class="waves-effect waves-dark has-arrow" aria-expanded="false"><i class="mdi mdi-flask-empty"></i><span class="hide-menu"> Preparaciones</span></a>
                                        <ul class="collapse" aria-expanded="false">
                                            <li><a href="<?php echo URL; ?>receta" id="i-entsal">Mis recetas</a></li>
                                            <li><a href="<?php echo URL; ?>receta/preparados" id="i-preparados">Preparados</a></li>
                                        </ul>
                                    </li><?php endif; ?><?php if (Session::get('rol') == 1 or Session::get('rol') == 2 or Session::get('rol') == 3) : ?><li id="carta"><a href="#" class="waves-effect waves-dark has-arrow" aria-expanded="false"><i class="mdi mdi-food"></i><span class="hide-menu"> Carta</span></a>
                                        <ul class="collapse" aria-expanded="false">
                                            <li><a href="<?php echo URL; ?>carta" id="c-apc">Subir Cartilla</a></li>
                                        </ul>
                                    </li><?php endif; ?><?php if (Session::get('rol') == 1 or Session::get('rol') == 2) : ?><li id="informes"><a href="<?php echo URL; ?>informe" class="waves-effect waves-dark" aria-expanded="false"><i class="mdi mdi-view-list"></i><span class="hide-menu"> Informes</span></a></li>
                                    <li id="config"><a href="<?php echo URL; ?>ajuste" class="waves-effect waves-dark" aria-expanded="false"><i class="mdi mdi-settings"></i><span class="hide-menu"> Ajustes</span></a></li><?php endif; ?><?php if (Session::get('rol') == 1 or Session::get('rol') == 2) : ?><li id="tablero"><a href="<?php echo URL; ?>tablero" class="waves-effect waves-dark" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span class="hide-menu"> Tablero</span></a></li><?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <div class="sidebar-footer"><a href="<?php echo URL; ?>tablero/logout" class="link" data-toggle="tooltip" title="Salir"><i class="mdi mdi-power"></i></a></div>
            </aside>
            <div class="page-wrapper">
                <div class="container-fluid">
               
                
                <?php endif; ?>

               
                