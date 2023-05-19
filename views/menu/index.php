<div class="containter-fluid" style="margin-top: 50px; margin-left: 50px;margin-right: 50px; margin-bottom: 50px;">
    <input type="hidden" id="url" value="<?php echo URL; ?>" />
    <input type="hidden" id="apcid" value="<?php echo Session::get('apcid'); ?>" />
    <input type="hidden" id="pc_ip" value="<?php echo Session::get('pc_ip'); ?>" />
    <input type="hidden" id="print_com" value="<?php echo Session::get('print_com'); ?>" />
    <input type="hidden" id="print_pre" value="<?php echo Session::get('print_pre'); ?>">
    <input type="hidden" id="print_cpe" value="<?php echo Session::get('print_cpe'); ?>">
    <input type="hidden" id="pc_name" value="<?php echo Session::get('pc_name'); ?>" />
    <input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>" />
    <input type="hidden" id="codped" value="<?php echo Session::get('codped'); ?>" />
    <input type="hidden" id="codtipoped" value="<?php echo Session::get('codtipoped'); ?>" />
    <input type="hidden" id="codtipopedentrega" value="0" />
    <input type="hidden" id="codrepartidor" value="1" />
    <input type="hidden" id="rol_usr" value="<?php echo Session::get('rol'); ?>" />
    <input type="hidden" id="tribAcr" value="<?php echo Session::get('tribAcr'); ?>" />
    <input type="hidden" id="diAcr" value="<?php echo Session::get('diAcr'); ?>" />
    <input type="hidden" id="codcomision" value="0" />
    <input type="hidden" name="codpagina" id="codpagina" value="2" />
    <input type="hidden" name="cod_general" id="cod_general" value="<?php echo $this->cod; ?>">
    <input type="hidden" id="codimpcomandamesa" value="<?php echo Session::get('opc_03'); ?>" />
    <input type="hidden" id="mesaje_waz" value="Su comprobante de pago electrónico ha sido generado correctamente, puede revisarlo en el siguiente enlace:" />
    <div class="row u4-1">
        <div class="col-lg-12 u4">
            <div class="card m-b-0">
                <div class="card-body p-0">
                    <div class="tab-pane p-0 active" id="tabp-1" role="tabpanel">
                        <div class="vtabs customvtab">
                            <!-- poner slim a las categorias -->
                            <ul class="scroll_productos nav nav-tabs tabs-vertical hidden-xs-down" id="list-catgrs" role="tablist" style="display:block">
                            </ul>
                            <div class="tab-content w-100" style="padding:5px">
                                <div class="scroll_productos" id="list-productos"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="card2"></div>
            </div>
        </div>
        <!-- Right sidebar -->
        <!-- ============================================================== -->
        <!-- .right-sidebar -->
        <div class="right-sidebar" id="sidebar_mobile">
            <div class="rpanel-title"> Categor&iacute;as <span><i class="ti-close right-side-toggle"></i></span> </div>
            <div class="r-panel-body p-0">
                <div class="scroll_categorias list-group font-13" id="list-catgrs-movil"></div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- End Right sidebar -->
        <!-- ============================================================== -->
    </div>
    <div class="">
        <button class="right-side-toggle waves-effect waves-light btn-primary btn btn-circle btn-lg btn-categ pull-right m-l-10" style="bottom: 122px"><i class="fas fa-chevron-circle-left text-white"></i></button>
        <button class="waves-effect waves-light btn-inverse btn btn-circle btn-lg btn-up" style="bottom: 65px"><i class="ti-arrow-circle-up text-white"></i></button>
        <button class="waves-effect waves-light btn-inverse btn btn-circle btn-lg btn-down" style="bottom: 10px"><i class="ti-arrow-circle-down text-white"></i></button>
    </div>

    <style>
        .btn-up {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 25px;
        }

        .btn-down {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 25px;
        }
    </style>

</div>