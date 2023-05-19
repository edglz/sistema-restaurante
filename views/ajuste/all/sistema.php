<input type="hidden" id="url" value="<?php echo URL; ?>"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Ajustes</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>ajuste" class="link">Sistema</a></li>
            <li class="breadcrumb-item active">Configuraci&oacute;n inicial</li>
        </ol>
    </div>
</div>

<form id="form" method="post" enctype="multipart/form-data">
<div class="row">
	<div class="col-md-9">
	    <div class="card p-20">
            <!-- Nav tabs -->
            <div class="vtabs customvtab">
                <ul class="nav nav-tabs tabs-vertical" role="tablist" >
                    <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#step1" role="tab" aria-selected="false"><span class="hidden-sm-up"><i class="ti-location-pin"></i></span> <span class="hidden-xs-down">Zona Horaria</span> </a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#step2" role="tab" aria-selected="false"><span class="hidden-sm-up"><i class="ti-id-badge"></i></span> <span class="hidden-xs-down">Indetificacion</span> </a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#step3" role="tab" aria-selected="false"><span class="hidden-sm-up"><i class="ti-money"></i></span> <span class="hidden-xs-down">Impuesto/Moneda</span> </a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#step4" role="tab" aria-selected="false"><span class="hidden-sm-up"><i class="ti-desktop"></i></span> <span class="hidden-xs-down">Ordenador</span> </a> </li>
                    <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#step5" role="tab" aria-selected="false"><span class="hidden-sm-up"><i class="ti-printer"></i></span> <span class="hidden-xs-down">Impresión</span> </a> </li>
                </ul>
                <!-- Tab panes -->
                
                <div class="tab-content">
                    <div class="tab-pane active" id="step1" role="tabpanel">
                        <div class="p-20">
                        	<h4 class="card-title"><i class="ti-location-pin font-18"></i> Zona Horaria</h4>
			                <h6 class="card-subtitle">Ingrese la zona horaria de su localidad.</h6>
                            <div class="row floating-labels">
                            	<div class="col-md-12">
	                                <div class="form-group m-b-40">
	                                    <input type="text" name="zona_hora" id="zona_hora" class="form-control" autocomplete="off" required>
	                                    <span class="bar"></span>
	                                </div>
	                            </div>
	                        </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="step2" role="tabpanel">
                        <div class="p-20">
                            <div class="row floating-labels">
	                            <div class="col-md-12">
	                                <h4 class="card-title"><i class="ti-bookmark-alt font-18"></i> Identificaci&oacute;n Tributaria</h4>
			                        <h6 class="card-subtitle">Utilizado con el fin de poder identificar inequívocamente a toda persona natural o jurídica susceptible de tributar, asignado a éstas por los Estados.</h6><br>
			                        <div class="row">
			                            <div class="col-md-6">
			                                <div class="form-group m-b-40 letNumMayMin">
			                                    <input type="text" name="trib_acr" id="trib_acr" class="form-control input-mayus" autocomplete="off" required>
			                                    <span class="bar"></span>
			                                    <label for="trib_acr">Nomenclatura <span class="label label-info font-13">EJEMPLO: RUC, RUT, RIF</span></label>
			                                </div>
			                            </div>
			                            <div class="col-md-6">
			                                <div class="form-group m-b-40 ent">
			                                    <input type="text" name="trib_car" id="trib_car" class="form-control" autocomplete="off" required>
			                                    <span class="bar"></span>
			                                    <label for="trib_car">N&uacute;mero de caracteres</label>
			                                </div>
			                            </div>
			                        </div>
	                            </div>
	                            <div class="col-md-12">
	                            	<h4 class="card-title"><i class="ti-id-badge font-18"></i> Documento de Identidad</h4>
			                        <h6 class="card-subtitle">Documento público que contiene datos de identificación personal.</h6><br>
			                        <div class="row">
			                            <div class="col-md-6">
			                                <div class="form-group m-b-40 letNumMayMin">
			                                    <input type="text" name="di_acr" id="di_acr" class="form-control input-mayus" autocomplete="off" required>
			                                    <span class="bar"></span>
			                                    <label for="di_acr">Nomenclatura <span class="label label-info font-13">EJEMPLO: DNI, CI, RG</span></label>
			                                </div>
			                            </div>
			                            <div class="col-md-6">
			                                <div class="form-group m-b-40 ent">
			                                    <input type="text" name="di_car" id="di_car" class="form-control" autocomplete="off" required>
			                                    <span class="bar"></span>
			                                    <label for="di_car">N&uacute;mero de caracteres</label>
			                                </div>
			                            </div>
			                        </div>
	                            </div>
	                        </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="step3" role="tabpanel">
						<div class="p-20">
                            <div class="row floating-labels">
	                            <div class="col-md-12">
	                            	<h4 class="card-title"><i class="ti-file font-18"></i> Impuesto</h4>
			                        <h6 class="card-subtitle">Tributo, exacci&oacute;n o la cantidad de dinero que se paga al Estado.</h6><br>
			                        <div class="row">
			                            <div class="col-md-6">
			                                <div class="form-group m-b-40 letNumMayMin">
			                                    <input type="text" name="imp_acr" id="imp_acr" class="form-control input-mayus" autocomplete="off" required>
			                                    <span class="bar"></span>
			                                    <label for="imp_acr">Nomenclatura <span class="label label-info font-13">EJEMPLO: IGV, IVA</span></label>
			                                </div>
			                            </div>
			                            <div class="col-md-6">
			                                <div class="form-group m-b-40 dec">
			                                    <input type="text" name="imp_val" id="imp_val" class="form-control" autocomplete="off" required>
			                                    <span class="bar"></span>
			                                    <label for="imp_val">Valor (%)</label>
			                                </div>
			                            </div>
			                        </div>
			                        <h4 class="card-title"><i class="ti-money font-18"></i> Moneda</h4>
			                        <h6 class="card-subtitle">Medida de cambio, de dinero para adquirir objetos, productos, entre otros.</h6><br>
			                        <div class="row">
			                            <div class="col-md-6">
			                                <div class="form-group m-b-40">
			                                    <input type="text" name="mon_acr" id="mon_acr" class="form-control" autocomplete="off" required>
			                                    <span class="bar"></span>
			                                    <label for="mon_acr">Nomenclatura <span class="label label-info font-13">EJEMPLO: SOLES, PESOS.</span></label>
			                                </div>
			                            </div>
			                            <div class="col-md-6">
			                                <div class="form-group m-b-40">
			                                    <input type="text" name="mon_val" id="mon_val" class="form-control" autocomplete="off" required>
			                                    <span class="bar"></span>
			                                    <label for="mon_val">S&iacute;mbolo</label>
			                                </div>
			                            </div>
			                        </div>
	                            </div>
	                        </div>
	                    </div>
                    </div>  
                    <div class="tab-pane" id="step4" role="tabpanel">
						<div class="p-20">
                            <div class="row floating-labels">
	                            <div class="col-md-12">
	                            	<h4 class="card-title"><i class="ti-desktop font-18"></i> PC Principal</h4>
			                        <h6 class="card-subtitle">Tributo, exacci&oacute;n o la cantidad de dinero que se paga al Estado.</h6><br>
			                        <div class="row">
			                            <div class="col-md-6">
			                                <div class="form-group m-b-40">
			                                    <input type="text" name="pc_name" id="pc_name" class="form-control" autocomplete="off" required>
			                                    <span class="bar"></span>
			                                    <label for="pc_name">Nombre</label>
			                                </div>
			                            </div>
			                            <div class="col-md-6">
			                                <div class="form-group m-b-40">
			                                    <input type="text" name="pc_ip" id="pc_ip" class="form-control" autocomplete="off" required>
			                                    <span class="bar"></span>
			                                    <label for="pc_ip">IP</label>
			                                </div>
			                            </div>
			                        </div>
			                    </div>
	                        </div>
	                    </div>
                    </div>  
                    <div class="tab-pane" id="step5" role="tabpanel">
						<div class="p-20">
                            <div class="row">
	                            <div class="col-md-12">
	                            	<div class="table-responsive">
	                                    <table class="table table-condensed table-hover" width="100%">
	                                        <thead>
	                                            <tr>
	                                                <th colspan="2">Nombre</th>
	                                                <th class="text-right">Opcion</th>
	                                            </tr>
	                                        </thead>
	                                        <tbody>
	                                            <tr>
	                                                <td style="width:50px;">
	                                                	<span class="round round-warning"><i class="ti-ticket"></i></span>
	                                                </td>
	                                                <td>
	                                                    <h5 class="m-t-5 m-b-0">Comandas</h5><h6 class="text-muted">Envía comandas a la cocina a través de una impresora.</h6>
	                                                </td>
	                                                <td class="text-right p-r-0" style="vertical-align: middle;">
	                                                	<div class="switch">
	                                                		<label><input type="checkbox" id="print_com"><span class="lever switch-col-light-green"></span></label>
				                                        </div>
	                                                	<input type="hidden" name="print_com" id="print_com_hidden">
	                                                </td>
	                                            </tr>
	                                            <tr>
	                                                <td style="width:50px;">
	                                                	<span class="round round-warning"><i class="ti-receipt"></i></span>
	                                                </td>
	                                                <td>
	                                                    <h5 class="m-t-5 m-b-0">Pre Cuenta</h5><h6 class="text-muted">Impresión de precuentas físicas.</h6>
	                                                </td>
	                                                <td class="text-right p-r-0" style="vertical-align: middle;">
	                                                	<div class="switch">
	                                                		<label><input type="checkbox" id="print_pre"><span class="lever switch-col-light-green"></span></label>
	                                                	</div>
	                                                	<input type="hidden" name="print_pre" id="print_pre_hidden">
	                                                </td>
	                                            </tr>
												<tr>
	                                                <td style="width:50px;">
	                                                	<span class="round round-warning"><i class="ti-receipt"></i></span>
	                                                </td>
	                                                <td>
	                                                    <h5 class="m-t-5 m-b-0">Comprobantes Electronicos</h5><h6 class="text-muted">Imprimir directamente Boleta, Factura y Notas de venta.</h6>
	                                                </td>
	                                                <td class="text-right p-r-0" style="vertical-align: middle;">
	                                                	<div class="switch">
	                                                		<label><input type="checkbox" id="print_cpe"><span class="lever switch-col-light-green"></span></label>
	                                                	</div>
	                                                	<input type="hidden" name="print_cpe" id="print_cpe_hidden">
	                                                </td>
	                                            </tr>
	                                        </tbody>
	                                    </table>
                                	</div>
	                            </div>
	                        </div>
	                    </div>
                    </div>                     
                    <div class="col-sm-12 text-right">
                    	<a href="<?php echo URL; ?>ajuste" class="btn btn-secondary">Cancelar</a>
                        <button class="btn btn-success text-right" type="submit">Aceptar</button>
                    </div>               
                </div>
	        </div>
	    </div>
	</div>
	<div class="col-md-3">
        <div class="alert alert-info font-15">
            <h3 class="text-info"><i class="fa fa-exclamation-circle"></i> Informaci&oacute;n</h3>
            <dl>
                <dt>Configuraci&oacute;n inicial</dt>
                <dd>Permite realizar la configuraci&oacute;n de los atributos principales del sistema.</dd>
            </dl>
        </div>
    </div>
</div>
</form>