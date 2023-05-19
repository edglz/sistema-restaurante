<?php
// opc_01: GLOVO,RAPPI,ETC
// opc_02: ver stock de pollo
// opc_03: imprimir ticket en mesa
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
  $_SESSION["zona_horaria"] = Session::get('zona_hor');
?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="theme-color" content="#444">
    <meta name="msapplication-navbutton-color" content="#444">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="shortcut icon" type="image/x-icon" href="<?php echo URL; ?>public/images/favicons/favicon.ico">
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo URL; ?>public/images/favicons/apple-touch-icon-180x180.png">
    <link rel="icon" type="image/png" href="<?php echo URL; ?>public/images/favicons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="<?php echo URL; ?>public/images/favicons/android-chrome-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="<?php echo URL; ?>public/images/favicons/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="<?php echo URL; ?>public/images/favicons/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="<?php echo URL; ?>public/images/favicons/manifest.json">
    <title> LA PREVIA | RESTAURANTE</title>
    <!-- Bootstrap Core CSS -->
    <link href="<?php echo URL; ?>public/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo URL; ?>public/plugins/toast-master/css/jquery.toast.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo URL; ?>public/css/style.css" rel="stylesheet">
    <link href="<?php echo URL; ?>public/css/style_all.css" rel="stylesheet">
    <link href="<?php echo URL; ?>public/css/colors/insaga.css" id="theme" rel="stylesheet">
    
    <!-- You can change the theme colors from here -->
    <link href="<?php echo URL; ?>public/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet">
    <link href="<?php echo URL; ?>public/plugins/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet" type="text/css">
    <link href="<?php echo URL; ?>public/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet"/>
    <link href="<?php echo URL; ?>public/plugins/formvalidation/formValidation.min.css" rel="stylesheet">
    <link href="<?php echo URL; ?>public/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css" rel="stylesheet"/>
    <link href="<?php echo URL; ?>public/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.css" rel="stylesheet"/>
    <link href="<?php echo URL; ?>public/plugins/bootstrap-tour/css/bootstrap-tour-standalone.min.css" rel="stylesheet"/>
    <link href="<?php echo URL; ?>public/plugins/wizard/wizard.css" rel="stylesheet">
    <link href="<?php echo URL; ?>public/plugins/jquery-ui-1.12.1/jquery-ui.css" rel="stylesheet">
    <script type="text/javascript" src="<?php echo URL; ?>public/plugins/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo URL; ?>public/js/feather.min.js"></script>
    <script type="text/javascript" src="<?php echo URL; ?>public/js/custom.js"></script>
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">


    <input type="hidden" id="url" value="<?php echo URL; ?>"/>
    <input type="hidden" id="fecha_gral" value="<?php echo Session::get('codx_g');?>">
</head>

<style>body.swal2-shown>[aria-hidden=true]{transition:.1s filter;filter:blur(10px) grayscale(90%)}</style>
<style>
    #cover-spin {
    position:fixed;
    width:100%;
    left:0;right:0;top:0;bottom:0;
    background-color: rgba(17, 17, 17, 0.411);
    z-index:9999;
    display:none;
}

@-webkit-keyframes spin {
	from {-webkit-transform:rotate(0deg);}
	to {-webkit-transform:rotate(360deg);}
}

@keyframes spin {
	from {transform:rotate(0deg);}
	to {transform:rotate(360deg);}
}

#cover-spin::after {
    content:'';
    display:block;
    position:absolute;
    left:48%;top:40%;
    width:40px;height:40px;
    border-style:solid;
    border-color:rgb(255, 255, 255);
    border-top-color:transparent;
    border-width: 4px;
    border-radius:50%;
    -webkit-animation: spin .5s both infinite;
    animation: spin .8 linear infinite;
}
</style>
<?php Session::init(); ?>
<?php if (Session::get('loggedIn') == true):?>
<div id="cover-spin"></div>
<body class="fix-header fix-sidebar card-no-border" id="card1">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <!-- <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
    </div> -->
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        
        <header class="topbar" style="width: 100% !important;">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <div class="navbar-header">
                    <?php if(Session::get('rol') == 5) { ?>
                    <a class="navbar-brand" href="javascript:void(0)">
                    <?php } else { ?>
                    <a class="navbar-brand" href="<?php echo URL; ?>tablero">
                    <?php } ?>
                        <!-- Logo icon -->
                        <b>
                            <img src="<?php echo URL; ?>public/images/logo_prev1.png" width="50%" style="margin-top: -5px;"/>
                        </b>
                        <!--End Logo icon -->
                        <!-- Logo text -->
                        <!-- <span><span class="font-18 font-medium text-white">BRAIN</span> 
                        <span class="font-18 font-medium text-white">POS</span></span>  -->
                    </a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav mr-auto mt-md-0">
                        <!-- This is  -->
                        <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                        <li class="nav-item"> <a class="nav-link sidebartoggler hidden-sm-down text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                        <?php if(Session::get('rol') == 5) { ?>
                        <li class="nav-item"> <a class="nav-link text-muted waves-effect waves-dark" href="<?php echo URL; ?>venta"><i class="fas fa-desktop"></i></a> </li>
                        <?php } ?>
                        <li class="nav-item search-box search-products" style="display: none"> <a class="nav-link text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="ti-search"></i></a>
                            <form class="app-search">
                                <input type="text" class="form-control" name="buscar_producto" id="buscar_producto" placeholder="Buscar productos..." autocomplete="off"> <a class="srh-btn" onclick=" document.getElementById('buscar_producto').value = '' "><i class="ti-close"></i></a>
                            </form>
                        </li>
                        <!-- ============================================================== -->
                    </ul>
                    <!-- ============================================================== -->
                    <!-- User profile and search -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav my-lg-0">
                        <!-- ============================================================== -->
                        <!-- Comment -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                        	<?php if(Session::get('opc_02') == 1) { ?>
                            <button type="button" class="btn waves-effect waves-light btn-primary btn-stock-pollo" style="display: none;" onclick="stock_pollo();">Stock de pollo</button>
                            <?php } ?>
                            <?php if((Session::get('rol') == 1 OR Session::get('rol') == 2 OR Session::get('rol') == 3) AND Session::get('sunat') == 1) { ?>
                            <a href="<?php echo URL; ?>facturacion"><button class="btn btn-secondary waves-effect waves-light border-0" type="button"><span class="btn-label"><img src="<?php echo URL; ?>public/images/logo-sunat.png" width="20px" height="20px" /> Sunat</span> <span class="cont-sunat"></span></button></a>
                            <?php } ?>
                            <?php if(Session::get('rol') == 4) { ?>
                            <button type="button" class="btn waves-effect waves-light btn-primary" onclick="listarPedidos();">Por orden de llegada</button>
                            <button type="button" class="btn waves-effect waves-light btn-warning" onclick="agruparPlatos();">Ordenar por tipo de plato o bebida</button>
                            <button type="button" class="btn waves-effect waves-light btn-success" onclick="agruparPedidos();">Ordenar por pedidos</button>
                            <?php } ?>
                            <?php if(Session::get('rol') <> 4) { ?>
                            <a class="nav-link dropdown-toggle text-muted text-muted waves-effect waves-dark listar-pedidos-preparados" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="ti-bell"></i>
                                <div class="t-notify"> <span class="heartbit"></span> <span class="point"></span> </div>
                            </a>                            
                            <div class="dropdown-menu dropdown-menu-right mailbox scale-up">
                                <ul>
                                    <li>
                                        <div class="drop-title">Pedidos preparados</div>
                                    </li>
                                    <li>
                                        <div class="message-center lista-pedidos-preparados"></div>
                                    </li>
                                </ul>
                            </div>
                            <a href="<?php echo URL; ?>tablero/logout" class="nav-link text-muted waves-effect waves-dark" data-toggle="tooltip" title="Salir"><i class="fas fa-sign-out-alt"></i></a>
                            <?php } ?>
                        </li>
                        <!-- ============================================================== -->
                        <!-- End Comment -->
                        <!-- ============================================================== -->
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- User profile -->
                <div class="user-profile" style="background: url(<?php echo URL; ?>public/images/background/user-info.jpg) no-repeat;">
                    <!-- User profile image -->
                    <div class="profile-img"> <img src="<?php echo URL; ?>public/images/users/<?php echo Session::get('imagen'); ?>" alt="user" /> </div>
                    <!-- User profile text-->
                    <div class="profile-text"> <a href="#" class="dropdown-toggle u-dropdown" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true"><?php echo Session::get('nombres'); ?></a>
                        <div class="dropdown-menu animated flipInY"> 
                            <a href="<?php echo URL; ?>tablero/logout" class="dropdown-item"><i class="fa fa-power-off"></i> Salir</a> 
                        </div>
                    </div>
                </div>
                <!-- End User profile text-->
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                    <?php if (Session::get('rol') == 4):?> 
                        <li id="area-p"><a class="waves-effect waves-dark" href="<?php echo URL; ?>produccion" aria-expanded="false"><i class="mdi mdi-tablet"></i><span class="hide-menu"> Producci&oacute;n</span></a>
                        </li>
                    <?php endif; ?>
                    <?php if (Session::get('rol') <> 4 && Session::get('rol')<>7):?>                     
                        <li id="restau"><a class="waves-effect waves-dark" href="<?php echo URL; ?>venta" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu"> Punto de Venta </span></a>
                        </li>  
                    <?php endif; ?>


                    <?php if (Session::get('rol') == 7):?>                     
                        <li id="restau"><a class="waves-effect waves-dark" href="<?php echo URL; ?>venta/venta_portero" aria-expanded="false"><i class="mdi mdi-receipt"></i><span class="hide-menu"> Punto de Venta </span></a>
                        </li>  
                        <li id="caja"><a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><i class="mdi mdi-desktop-mac"></i><span class="hide-menu"> Caja </span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="<?php echo URL; ?>caja/apercie" id="c-apc"> Apertura y cierre</a></li>
                                <li><a href="<?php echo URL; ?>caja/ingreso" id="c-ing"> Ingresos</a></li>
                                <li><a href="<?php echo URL; ?>caja/egreso" id="c-egr"> Egresos</a></li>
                                <li><a href="<?php echo URL; ?>caja/monitor_imp" id="c-i"> Monitor de impresiones</a></li>
                                <?php if (Session::get('rol') == 1 OR Session::get('rol') == 2 OR Session::get('rol') == 3):?> 
                                <li><a href="<?php echo URL; ?>caja/monitor" id="c-mon"> Monitor de ventas</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if (Session::get('rol') == 1 OR Session::get('rol') == 2 OR Session::get('rol') == 3):?>                       
                        <li id="caja"><a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><i class="mdi mdi-desktop-mac"></i><span class="hide-menu"> Caja </span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="<?php echo URL; ?>caja/apercie" id="c-apc"> Apertura y cierre</a></li>
                                <li><a href="<?php echo URL; ?>caja/ingreso" id="c-ing"> Ingresos</a></li>
                                <li><a href="<?php echo URL; ?>caja/egreso" id="c-egr"> Egresos</a></li>
                                <li><a href="<?php echo URL; ?>caja/monitor_imp" id="c-i"> Monitor de impresiones</a></li>
                                <?php if (Session::get('rol') == 1 OR Session::get('rol') == 2):?> 
                                <li><a href="<?php echo URL; ?>caja/monitor" id="c-mon"> Monitor de ventas</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <li id="clientes"><a class="waves-effect waves-dark" href="<?php echo URL; ?>cliente" aria-expanded="false"><i class="mdi mdi-account-circle"></i><span class="hide-menu"> Clientes </span></a>
                        </li>
                    	<?php 
                    		if(Session::get('rol') != 3 ):
                    	?>
                    	    <li id="compras"><a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><i class="fas fa-dolly" style="margin-left: -1px; font-size: 18px !important;"></i><span class="hide-menu"> Compras </span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="<?php echo URL; ?>compra" id="c-compras"> Todas las compras</a></li>
                                <li><a href="<?php echo URL; ?>compra/proveedor" id="c-proveedores"> Proveedores</a></li>
                            </ul>
                        </li>
                        <li id="creditos"><a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><i class="mdi mdi-credit-card"></i><span class="hide-menu"> Creditos </span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="<?php echo URL; ?>credito" id="cr-compras"> Compras</a></li>
                            </ul>
                        </li> 
                    	
                    <?php endif; endif; ?>
                    <?php if (Session::get('rol') == 1 OR Session::get('rol') == 2 OR Session::get('rol') == 3):?>  
                        <li id="inventario"><a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><i class="mdi mdi-archive"></i><span class="hide-menu"> Inventario </span></a>
                            <ul aria-expanded="false" class="collapse">                                
                                <li><a href="<?php echo URL; ?>inventario/stock" id="i-stock"> Stock</a></li>
                                <?php if (Session::get('rol') == 1 OR Session::get('rol') == 2):?> 
                                <li><a href="<?php echo URL; ?>inventario/kardex" id="i-karval"> Kardex valorizado</a></li>                                
                                <li><a href="<?php echo URL; ?>inventario/ajuste" id="i-entsal"> Ajuste de stock</a></li>
                                <?php endif; ?>                            
                            </ul>
                        </li>
                        <li id="preparaciones"><a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><i class="mdi mdi-flask-empty"></i><span class="hide-menu"> Preparaciones </span></a>
                            <ul aria-expanded="false" class="collapse">                                
                            <li><a href="<?php echo URL; ?>receta" id="i-entsal"> Mis recetas</a></li>
                                <li><a href="<?php echo URL; ?>receta/preparados" id="i-karval"> Preparados </a></li>                                
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if (Session::get('rol') == 1 OR Session::get('rol') == 2 OR Session::get('rol') == 3):?>                       
                        <li id="carta"><a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><i class="mdi mdi-food"></i><span class="hide-menu"> Carta </span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="<?php echo URL; ?>carta" id="c-apc"> Subir Cartilla</a></li>
                                <!-- <li><a href="<?php echo URL; ?>caja/panel" id="c-ing"> Panel de control</a></li>
                                <li><a href="<?php echo URL; ?>caja/descargas" id="c-egr"> Descargas</a></li> -->
                            </ul>
                        </li>
                       
                    <?php endif; ?>
                    <?php if (Session::get('rol') == 1 OR Session::get('rol') == 2):?> 
                        <li id="informes"><a class="waves-effect waves-dark" href="<?php echo URL; ?>informe" aria-expanded="false"><i class="mdi mdi-view-list"></i><span class="hide-menu"> Informes </span></a>
                        </li>
                        
                        <li id="config"><a class="waves-effect waves-dark" href="<?php echo URL; ?>ajuste" aria-expanded="false"><i class="mdi mdi-settings"></i><span class="hide-menu"> Ajustes </span></a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if (Session::get('rol') == 1 OR Session::get('rol') == 2):?>                     
                        <li id="tablero"><a class="waves-effect waves-dark" href="<?php echo URL; ?>tablero" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span class="hide-menu"> Tablero </span></a>
                        </li>
                    <?php endif; ?>
                    
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
            <!-- Bottom points-->
            <div class="sidebar-footer">
                <!-- item--><a href="<?php echo URL; ?>tablero/logout" class="link" data-toggle="tooltip" title="Salir"><i class="mdi mdi-power"></i></a> </div>
            <!-- End Bottom points-->
        </aside>
        
        <div class="page-wrapper">
            <div class="container-fluid">
            
<?php endif; ?>

    