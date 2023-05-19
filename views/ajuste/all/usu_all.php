<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Ajustes</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Empresa</a></li>
            <li class="breadcrumb-item active">Usuarios</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">         
            <div class="card-body p-b-0">
                <div class="message-box contact-box">
                    <h2 class="add-ct-btn"><a href="<?php echo URL; ?>ajuste/usuario_nuevo"><button type="button" class="btn btn-circle btn-lg btn-orange waves-effect waves-dark"><i class="ti-plus"></i></button></a></h2>
                    <br>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="row floating-labels m-t-5">
                    <div class="col-sm-8 offset-sm-4 col-lg-4 offset-lg-8">
                        <div class="form-group m-b-10">
                            <input type="text" class="form-control global_filter" id="global_filter" autocomplete="off">
                            <span class="bar"></span>
                            <label for="global_filter">B&uacute;squeda</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive b-t m-b-10">
                    <table id="table" class="table table-hover stylish-table" cellspacing="0" width="100%">
                        <thead class="table-head">
                            <th>Nombres</th>
                            <th>Ape.Paterno</th>
                            <th>Ape.Materno</th>
                            <th>Cargo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-right">Acciones</th>
                        </thead>
                        <tbody class="tb-st"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>