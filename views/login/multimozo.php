<style>
    .text-warning {
        color: #21388B;
         !important;
    }

    .btn-warning {
        color: #fff !important;
        background-color: #21388B;
         !important;
        border-color: #21388B;
         !important;
        box-shadow: 0 1px 0 rgb(255 255 255 / 15%);
    }

    .btn-warning:hover,
    .btn-warning.disabled:hover {
        background: #21388B;
         !important;
        color: #ffffff !important;
        -webkit-box-shadow: 0 14px 26px -12px rgb(234 91 93 / 42%), 0 4px 23px 0 rgb(0 0 0 / 12%), 0 8px 10px -5px rgb(234 91 93 / 20%);
        box-shadow: 0 14px 26px -12px rgb(234 91 93 / 42%), 0 4px 23px 0 rgb(0 0 0 / 12%), 0 8px 10px -5px rgb(234 91 93 / 20%);
        border: 1px solid #21388B;
         !important;
    }

    @media (max-width: 767px) {
        img.logo {
            width: 50%;
        }

        .ocult {
            display: none;
        }

        .auth-wrapper .auth-box-2 {
            padding: 15px 25px 0px 25px;
        }
    }
</style>
<?php if (isset($_GET["pc"])) {
    Session::set('host_pc', 'PC0' . $_GET["pc"]);
} ?>
<link href="<?php echo URL; ?>public/css/style.min.css" id="theme" rel="stylesheet">
<div id="wrapper-2">
    <div class="row auth-wrapper gx-0">
        <div class="col-lg-4 col-xl-3 bg-info auth-box-2 on-sidebar" style="background-repeat:no-repeat; object-fit: cover ; background-image:url(<?php echo URL; ?>public/images/restobar.jpg);">
            <div class="h-100 d-flex align-items-start justify-content-flex-start">
                <div class="row justify-content-center text-center">
                    <div class="col-md-7 col-lg-12 col-xl-9">
                        <a href="javascript:void(0)" class="text-center db"><img src="<?php echo URL; ?>public/images/logo_prev1.png" width='100%' /></a>
                        <h2 class="text-white mt-4 fw-light">SISTEMA<span class="font-weight-medium text-warning"> RESTAURANTE</span></h2>
                        <h5 class="text-white mt-0 fw-light">Versi&oacute;n 3.1</h5>
                        <p class="op-5 text-white fs-4 mt-4 fw-light">Gesti&oacute;n realmente f&aacute;cil para hacer crecer tu negocio.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-xl-9 d-flex align-items-center justify-content-center">
            <div class="row justify-content-center w-100 mt-1 mt-lg-0">
                <div class="col-lg-6 col-xl-3 col-md-7">
                    <div class="px-2 my-4">
                        <a class="btn btn-primary btn-lg btn-block" href="<?php echo URL; ?>">Administrador</a>
                    </div>
                    <div class=" card">
                        <div class="card-body">
                            <form class="form-horizontal floating-labels" id="frm-login" role="form" method="post">
                                <h3 class="box-title m-b-20">Ingrese c&oacute;digo</h3>
                                <div class="form-group m-b-20">
                                    <input type="hidden" name="password" id="f-pass" class="form-control">
                                    <input type="password" name="usuario" id="f-user" class="form-control text-center font-30" autocomplete="off" required>
                                    <span class="bar"></span>
                                </div>
                                <div class="row button-group virtual-keyboard">
                                    <div class="col-4">
                                        <button type="button" class="btn waves-effect waves-light btn-block btn-lg btn-inverse" data="1">1</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn waves-effect waves-light btn-block btn-lg btn-inverse" data="2">2</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn waves-effect waves-light btn-block btn-lg btn-inverse " data="3">3</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn waves-effect waves-light btn-block btn-lg btn-inverse " data="4">4</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn waves-effect waves-light btn-block btn-lg btn-inverse " data="5">5</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn waves-effect waves-light btn-block btn-lg btn-inverse " data="6">6</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn waves-effect waves-light btn-block btn-lg btn-inverse " data="7">7</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn waves-effect waves-light btn-block btn-lg btn-inverse " data="8">8</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn waves-effect waves-light btn-block btn-lg btn-inverse " data="9">9</button>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn waves-effect waves-light btn-block btn-lg btn-inverse " data="0">0</button>
                                    </div>
                                    <div class="col-8">
                                        <button type="button" class="btn waves-effect waves-light btn-block btn-lg btn-inverse" data="DEL"><i class="fas fa-arrow-left"></i></button>
                                    </div>
                                </div>
                                <button class="btn btn-warning btn-block btn-lg text-uppercase waves-effect waves-light" type="submit">CONTINUAR</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>