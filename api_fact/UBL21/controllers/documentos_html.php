<?php
class Documentos_html {

	public function get_html_boleta($data) {
		$html = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html lang="es-ES" prefix="og: http://ogp.me/ns#" xmlns="https://www.w3.org/1999/xhtml">
		<head>   
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport" />
			<link href="https://fonts.googleapis.com/css?family=Josefin+Sans:400,700" rel="stylesheet" type="text/css">
			<link href="../assets/theme_doc_elect/font-awesome-4.6.1/css/font-awesome.css" rel="stylesheet">
			<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" rel="stylesheet">  
			<link href="../assets/theme_doc_elect/css/estilo.css" rel="stylesheet">
		</head>
		<body>    
			<table class="tablareceipt">
				<tbody>
					<tr>
						<td>
							<p>La Nueva SRL.</p>
							<img class="logofactura" src="../assets/theme_doc_elect/images/logo.png">
							<p style="text-transform: none !important;">Av. Siempre via #456 - Surco - Lima</p>
							<p style="text-transform: none !important;"><img src="../assets/theme_doc_elect/images/telephone.png" style="width: 12px;" /> Telf.: 677899900 / Cel.: 677788999</p>
							<p style="text-transform: none !important;"><img src="../assets/theme_doc_elect/images/email.svg" style="width: 12px;" /> Email: ventas@dominio.com</p>
						</td>
						<td class="datoruc">
							<p>R.U.C. 34567890345</p>                    
							<p>Boleta de Venta</p>
							<p>001 - 005346</p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<table class="tabladatos" >
				<tbody>
					<tr>
						<td class="tdatoslabel">
							<div style="padding-bottom: 10px; padding-right: 19px;">
								<span style="float: left;">Nombre Cliente:</span>
								<div style="margin-left: 105px; border-bottom: solid 1px #000;">Textiles Castro Import SAC</div>
							</div>
						</td>
						<td class="tdatoslabel tdatodlabeldos">
							<div>
								<span style="float: left;">Fecha de Emisión:</span>
								<div style="margin-left: 118px; border-bottom: solid 1px #000;">26/10/2018</div>
							</div>
						</td>
					</tr>
					<tr>
						<td class="tdatoslabel">
							<div style="padding-bottom: 10px; padding-right: 19px;">
								<span style="float: left;">DNI N°:</span>
								<div style="margin-left: 105px; border-bottom: solid 1px #000;"> 26607890345 </div>
							</div>
						</td>
						<td class="tdatoslabel tdatodlabeldos">
							<div>
								<span style="float: left;">Moneda: </span>
								<div style="margin-left: 118px; border-bottom: solid 1px #000;">Soles</div>
							</div>
						</td>
					</tr>
					<tr>
						<td class="tdatoslabel">
							<div style="padding-bottom: 10px; padding-right: 19px;">
								<span style="float: left;">Dirección:</span>
								<div style="margin-left: 105px; border-bottom: solid 1px #000;"> Jr. Italia N. 453 - La Victoria Lima - Perú</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			
			<div class="tablageneral">        
				<table cellpadding="0" cellspacing="0">
					<tbody>
						<tr class="titulotable">
							<td class="cantidadtabla">Cantidad</td>
							<td>Descripción</td>
							<td class="preciotabla">Precio Unitario</td>
							<td class="preciotabla ulttable">Valor de venta</td>
						</tr>
						<tr class="detalletable">
							<td class="nombredetalle">5</td>
							<td>Chompas de lana de alpaca</td>
							<td>$20.00</td>
							<td class="ulttable">$100.00</td>
						</tr>
						<tr class="detalletable">
							<td class="nombredetalle">10</td>
							<td>Llavero del mundial</td>
							<td>$2.00</td>
							<td class="ulttable">$20.00</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="tablageneral tablacostos tablaboleta">        
				<table cellpadding="0" cellspacing="0">
					<tbody>   
						<tr class="detalletable">                                                                
							<td colspan="2">SON: Ciento Veinte y 00/100 soles</td>
							<td class="preciotabla fintabla fintabla fintablados"><b>TOTAL</b></td>
							<td class="preciotabla ulttable fintabla">$120.00</td>
						</tr>                 
					</tbody>
				</table>        
			</div>
			<table cellpadding="0" cellspacing="0" class="tablacan">
				<tbody>
					<tr>    
						<td style="text-align: center;"><img style="width: 124px; margin-bottom: 11px;" src="../assets/theme_doc_elect/images/qr.png" /></td>                    
						<td>
							<div>Autorizado mediante Resolución de Intendencia N° 032-005-Representación impresa de la Boleta Electrónica. Consulte su documento electrónico en: <br /> <span style="font-size: 10px">https://bit.ly/2HiRWZI</span></div>
							<br><span class="codigofac">HASH: dGp1cUib9QWXjNEFDUqjDGszUKw=</span>
						</td>
					</tr>   
				</tbody>
			</table>
		</body>
		</html>
		';

		$resp['respuesta'] = 'ok';
		$resp['html'] = $html;
		return $resp;
	}

	public function get_html_factura($data) {
		$html = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html lang="es-ES" prefix="og: http://ogp.me/ns#" xmlns="https://www.w3.org/1999/xhtml">
		<head>   
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport" />
			<link href="https://fonts.googleapis.com/css?family=Josefin+Sans:400,700" rel="stylesheet" type="text/css">
			<link href="../assets/theme_doc_elect/font-awesome-4.6.1/css/font-awesome.css" rel="stylesheet">
			<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" rel="stylesheet">  
			<link href="../assets/theme_doc_elect/css/estilo.css" rel="stylesheet">
		</head>
		<body>    
			<table class="tablareceipt">
				<tbody>
					<tr>
						<td>
							<p>Textiles el telar S.A.C.</p>
							<img class="logofactura" src="../assets/theme_doc_elect/images/logo.png">
							<p style="text-transform: none !important;">Av. Siempre via #456 - Surco - Lima</p>
							<p style="text-transform: none !important;"><img src="../assets/theme_doc_elect/images/telephone.png" style="width: 12px;" /> Telf.: 677899900 / Cel.: 677788999</p>
							<p style="text-transform: none !important;"><img src="../assets/theme_doc_elect/images/email.svg" style="width: 12px;" /> Email: ventas@dominio.com</p>
						</td>
						<td class="datoruc">
							<p>R.U.C. 34567890345</p>                    
							<p>Factura</p>
							<p>001 - 005346</p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<table class="tabladatos" >
				<tbody>
					<tr>
						<td class="tdatoslabel">
							<div style="padding-bottom: 10px; padding-right: 19px;">
								<span style="float: left;">Razón Social:</span>
								<div style="margin-left: 95px; border-bottom: solid 1px #000;">Textiles Castro Import SAC</div>
							</div>
						</td>
						<td class="tdatoslabel tdatodlabeldos">
							<div>
								<span style="float: left;">Fecha de Emisión:</span>
								<div style="margin-left: 118px; border-bottom: solid 1px #000;">26/10/2018</div>
							</div>
						</td>
					</tr>
					<tr>
						<td class="tdatoslabel">
							<div style="padding-bottom: 10px; padding-right: 19px;">
								<span style="float: left;">RUC N°:</span>
								<div style="margin-left: 95px; border-bottom: solid 1px #000;"> 26607890345 </div>
							</div>
						</td>
						<td class="tdatoslabel tdatodlabeldos">
							<div>
								<span style="float: left;">Guía de Remisión: </span>
								<div style="margin-left: 118px; border-bottom: solid 1px #000;">.</div>
							</div>
						</td>
					</tr>
					<tr>
						<td class="tdatoslabel">
							<div style="padding-bottom: 10px; padding-right: 19px;">
								<span style="float: left;">Dirección:</span>
								<div style="margin-left: 95px; border-bottom: solid 1px #000;"> Jr. Italia N. 453 - La Victoria Lima - Perú</div>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			
			<div class="tablageneral">        
				<table cellpadding="0" cellspacing="0">
					<tbody>
						<tr class="titulotable">
							<td class="cantidadtabla">Cantidad</td>
							<td>Descripción</td>
							<td class="preciotabla">Precio Unitario</td>
							<td class="preciotabla ulttable">Valor de venta</td>
						</tr>
						<tr class="detalletable">
							<td class="nombredetalle">5</td>
							<td>Chompas de lana de alpaca</td>
							<td>$20.00</td>
							<td class="ulttable">$100.00</td>
						</tr>
						<tr class="detalletable">
							<td class="nombredetalle">10</td>
							<td>Llavero del mundial</td>
							<td>$2.00</td>
							<td class="ulttable">$20.00</td>
						</tr>
						<tr class="detalletable">
							<td class="nombredetalle"></td>
							<td>Son sesenta y uno 00/100 soles</td>
							<td></td>
							<td class="ulttable"></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="tablageneral tablacostos">        
				<table cellpadding="0" cellspacing="0">
					<tbody>   
						<tr class="detalletable">                                                                
							<td class="imprenta" rowspan="3" style="width: 120px !important;">
								<img style="width: 120px; margin-bottom: 11px;" src="../assets/theme_doc_elect/images/qr.png" />
							</td>
							<td rowspan="3">
								<div style="clear: both; padding-bottom: 25px; width: 260px;">
									Autorizado mediante Resolución de Intendencia N° 032-005-Representación impresa de la Boleta Electrónica. Consulte su documento electrónico en: 
									<span style="font-size: 10px">https://bit.ly/2HiRWZI</span>
									<br>
									<span style="font-size: 11px">HASH: dGp1cUib9QWXjNEFDUqjDGszUKw=</span>
								</div>
							</td>
							<td class="preciotabla fintabla fintablados"><b>Subtotal</b></td>
							<td class="preciotabla ulttable fintabla">$20.00</td>
						</tr> 
						<tr class="detalletable">                                                                
							
							
							<td class="preciotabla fintabla fintablados"><b>I.G.V. (19%)</b></td>
							<td class="preciotabla ulttable fintabla">$20.00</td>
						</tr>   
						<tr class="detalletable">
							
							
							<td class="preciotabla fintabla fintabla fintablados"><b>TOTAL</b></td>
							<td class=" preciotabla ulttable fintabla">$20.00</td>
						</tr>
					</tbody>
				</table>
			</div>
		</body>
		</html>
		';

		$resp['respuesta'] = 'ok';
		$resp['html'] = $html;
		return $resp;
	}

	public function get_html_nota_credito($data) {
		$html = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html lang="es-ES" prefix="og: http://ogp.me/ns#" xmlns="https://www.w3.org/1999/xhtml">
		<head>   
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport" />
			<link href="https://fonts.googleapis.com/css?family=Josefin+Sans:400,700" rel="stylesheet" type="text/css">
			<link href="../assets/theme_doc_elect/font-awesome-4.6.1/css/font-awesome.css" rel="stylesheet">
			<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" rel="stylesheet">  
			<link href="../assets/theme_doc_elect/css/estilo.css" rel="stylesheet">
		</head>
		<body>    
			<table class="tablareceipt">
				<tbody>
					<tr>
						<td>
							<p>Textiles el telar S.A.C.</p>
							<img class="logofactura" src="../assets/theme_doc_elect/images/logo.png">
							<p style="text-transform: none !important;">Av. Siempre via #456 - Surco - Lima</p>
							<p style="text-transform: none !important;"><img src="../assets/theme_doc_elect/images/telephone.png" style="width: 12px;" /> Telf.: 677899900 / Cel.: 677788999</p>
							<p style="text-transform: none !important;"><img src="../assets/theme_doc_elect/images/email.svg" style="width: 12px;" /> Email: ventas@dominio.com</p>
						</td>
						<td class="datoruc">
							<p>R.U.C. 34567890345</p>                    
							<p>Nota de Crédito</p>
							<p>001 - 005346</p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<table class="tabladatos">
				<tbody>
					<tr>
						<td class="tdatoslabel" style="width: 50%; padding-right: 19px;">
							<div style="padding-bottom: 10px">
								<span style="float: left;">Nombre Cliente:</span>
								<div style="margin-left: 105px; border-bottom: dashed 1px #000 !important;">Textiles Castro Import SAC</div>
							</div>
						</td>
						<td class="tdatoslabel tdatodlabeldos">
							<div>
								<span style="float: left;"><b>DOCUMENTO QUE MODIFICA:</b></span>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="tabladatos">
				<tbody>            
					<tr>
						<td class="tdatoslabel" style="width: 50%; padding-right: 19px;">
							<div style="padding-bottom: 10px">
								<span style="float: left;">Dirección:</span>
								<div style="margin-left: 68px; border-bottom: dashed 1px #000 !important;"> Jr. Italia N. 453 - La Victoria Lima - Perú </div>
							</div>
						</td>
						<td class="tdatoslabel tdatodlabeldos">
							<div>
								<span style="float: left;">Denominación: </span>
								<div style="margin-left: 118px; border-bottom: dashed 1px #000 !important;">Factura</div>
							</div>
						</td>
					</tr>
					<tr>
						<td class="tdatoslabel" style="width: 50%; padding-right: 19px;">
							<div style="padding-bottom: 10px">
								<span style="float: left;">Fecha de Emisión:</span>
								<div style="margin-left: 115px; border-bottom: dashed 1px #000 !important;"> 26/06/2018 </div>
							</div>
						</td>
						<td class="tdatoslabel tdatodlabeldos">
							<div>
								<span style="float: left;">N°: </span>
								<div style="margin-left: 25px; border-bottom: dashed 1px #000 !important;">34567890345</div>
							</div>
						</td>
					</tr>
					<tr>
						<td class="tdatoslabel tdatonombre tdatocre tdatocons"><p>Por los consiguiente:</p></td>
						<td class="tdatoslabel tdatofecha tdatocre"><p><span>Emisión del Comprobante de pago que modifica:</span></p> 10/05/2018</td>
					</tr>
				</tbody>
			</table>
			
			<div class="tablageneral">        
				<table cellpadding="0" cellspacing="0">
					<tbody>
						<tr class="titulotable">
							<td class="cantidadtabla">Cantidad</td>
							<td class="cantidadtabla">Código</td>
							<td>Descripción</td>
							<td class="preciotabla">Precio Unitario</td>
							<td class="preciotabla ulttable">Valor de venta o servicio prestado</td>
						</tr>
						<tr class="detalletable" style="height: 120px;">
							<td class="nombredetalle">1</td>
							<td class="nombredetalle">HUS87</td>
							<td style="font-size: 18px;">Por Anulación Total de la Comisión por Servicios de Alquiler de Auto. Pasajero. Huaman Juan</td>
							<td></td>
							<td class="ulttable">$100.00</td>
						</tr>
						<tr class="detalletable">
							<td class="nombredetalle"></td>
							<td class="nombredetalle"></td>
							<td style="font-weight: bold;">Son ciento dieciocho y 00/100 soles</td>
							<td></td>
							<td class="ulttable"></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="tablageneral tablacostos">        
				<table cellpadding="0" cellspacing="0">
					<tbody>   
						<tr class="detalletable">                                                                
							<td rowspan="3" style="padding: 0px !important;">
								<b>MOTIVOS DE LA EMISIÓN DE LA NOTA DE CRÉDITO</b>
								<table style="width:100%">
									<tr>
										<th style="font-weight: normal; font-size: 12px;">
											<input type="checkbox" id="cbox1" value="first_checkbox"> Anulación de la Operación
										</th>
										<th style="font-weight: normal; font-size: 12px;">
											<input type="checkbox" id="cbox1" value="first_checkbox">Anulación por Error en RUC
										</th>
									</tr>
									<tr>
										<th style="font-weight: normal; font-size: 12px;">
											<input type="checkbox" id="cbox1" value="first_checkbox">Corrección Error en Descripción
										</th>
										<th style="font-weight: normal; font-size: 12px;">
											<input type="checkbox" id="cbox1" value="first_checkbox">Devolución Global
										</th>
									</tr>
									<tr>
										<th style="font-weight: normal; font-size: 12px;">
											<input type="checkbox" id="cbox1" value="first_checkbox">Descuento por Item
										</th>
										<th style="font-weight: normal; font-size: 12px;">
											<input type="checkbox" id="cbox1" value="first_checkbox" checked>Devolución Total
										</th>
									</tr>
									<tr>
										<th style="font-weight: normal; font-size: 12px;">
											<input type="checkbox" id="cbox1" value="first_checkbox">Devolución por Item
										</th>
										<th style="font-weight: normal; font-size: 12px;">
											<input type="checkbox" id="cbox1" value="first_checkbox">Bonificación
										</th>
									</tr>
									<tr>
										<th style="font-weight: normal; font-size: 12px;">
											<input type="checkbox" id="cbox1" value="first_checkbox">Disminución en el Valor
										</th>
										<th></th>
									</tr>
								</table>
							</td>
							<td class="preciotabla fintabla fintablados"><b>I.G.V.</b></td>
							<td class="preciotabla ulttable fintabla">$18.00</td>
						</tr> 
						<tr class="detalletable">
							<td class="preciotabla fintabla fintablados"><b>OTROS</b></td>
							<td class="preciotabla ulttable fintabla"> -- </td>
						</tr>   
						<tr class="detalletable">
							<td class="preciotabla fintabla fintabla fintablados"><b>TOTAL</b></td>
							<td class=" preciotabla ulttable fintabla">$118.00</td>
						</tr>
					</tbody>
				</table>
			</div>
			<table>
				<tbody>
					<tr>
						<td style="width: 34%;">
							<p>
								<b>Recepción de la nota de credito</b><br />                                
							</p>
							<p><span>Apellidos y nombres de quien recepciona la nota de crédito:</span> Cristopher Andréz Villanueva Paz</p>
							<p><span>DNI:</span> 78869955</p>
							<p><span>Fecha de recepción:</span> 26/05/2018</p>
						</td>
						<td style="width: 33%; text-align: center;">
							<img style="width: 124px; margin-bottom: 11px;" src="../assets/theme_doc_elect/images/qr.png" />
						</td>
						<td style="width: 33%;">
							Autorizado mediante Resolución de Intendencia N° 032-005-Representación impresa de la Nota de Crédito. Consulte su documento electrónico en: 
							<span style="font-size: 10px">https://bit.ly/2HiRWZI</span>
						</td>
					</tr>
					<tr>
						<td style="width: 34%;">
						</td>
						<td style="text-align: center;" colspan="2">
							<span class="codigofac">HASH: dGp1cUib9QWXjNEFDUqjDGszUKw=</span>
						</td>
					</tr>
				</tbody>    
			</table>
			
		</body>
		</html>
		';

		$resp['respuesta'] = 'ok';
		$resp['html'] = $html;
		return $resp;
	}

	public function get_html_nota_debito($data) {
		$html = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html lang="es-ES" prefix="og: http://ogp.me/ns#" xmlns="https://www.w3.org/1999/xhtml">
		<head>   
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport" />
			<link href="https://fonts.googleapis.com/css?family=Josefin+Sans:400,700" rel="stylesheet" type="text/css">
			<link href="../assets/theme_doc_elect/font-awesome-4.6.1/css/font-awesome.css" rel="stylesheet">
			<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" rel="stylesheet">  
			<link href="../assets/theme_doc_elect/css/estilo.css" rel="stylesheet">
		</head>
		<body>    
			<table class="tablareceipt">
				<tbody>
					<tr>
						<td>
							<p>Textiles el telar S.A.C.</p>
							<img class="logofactura" src="../assets/theme_doc_elect/images/logo.png">
							<p style="text-transform: none !important;">Av. Siempre via #456 - Surco - Lima</p>
							<p style="text-transform: none !important;"><img src="../assets/theme_doc_elect/images/telephone.png" style="width: 12px;" /> Telf.: 677899900 / Cel.: 677788999</p>
							<p style="text-transform: none !important;"><img src="../assets/theme_doc_elect/images/email.svg" style="width: 12px;" /> Email: ventas@dominio.com</p>
						</td>
						<td class="datoruc">
							<p>R.U.C. 34567890345</p>                    
							<p>Nota de Débito</p>
							<p>001 - 005346</p>
						</td>
					</tr>
				</tbody>
			</table>
			
			<table class="tabladatos">
					<tbody>
						<tr>
							<td class="tdatoslabel" style="width: 50%; padding-right: 19px;">
								<div style="padding-bottom: 10px">
									<span style="float: left;">Nombre Cliente:</span>
									<div style="margin-left: 105px; border-bottom: dashed 1px #000 !important;">Textiles Castro Import SAC</div>
								</div>
							</td>
							<td class="tdatoslabel tdatodlabeldos">
								<div>
									<span style="float: left;"><b>DOCUMENTO QUE MODIFICA:</b></span>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<table class="tabladatos">
					<tbody>            
						<tr>
							<td class="tdatoslabel" style="width: 50%; padding-right: 19px;">
								<div style="padding-bottom: 10px">
									<span style="float: left;">Dirección:</span>
									<div style="margin-left: 68px; border-bottom: dashed 1px #000 !important;"> Jr. Italia N. 453 - La Victoria Lima - Perú </div>
								</div>
							</td>
							<td class="tdatoslabel tdatodlabeldos">
								<div>
									<span style="float: left;">Denominación: </span>
									<div style="margin-left: 118px; border-bottom: dashed 1px #000 !important;">Factura</div>
								</div>
							</td>
						</tr>
						<tr>
							<td class="tdatoslabel" style="width: 50%; padding-right: 19px;">
								<div style="padding-bottom: 10px">
									<span style="float: left;">Fecha de Emisión:</span>
									<div style="margin-left: 115px; border-bottom: dashed 1px #000 !important;"> 26/06/2018 </div>
								</div>
							</td>
							<td class="tdatoslabel tdatodlabeldos">
								<div>
									<span style="float: left;">N°: </span>
									<div style="margin-left: 25px; border-bottom: dashed 1px #000 !important;">34567890345</div>
								</div>
							</td>
						</tr>
						<tr>
							<td class="tdatoslabel tdatonombre tdatocre tdatocons"><p>Por los consiguiente:</p></td>
							<td class="tdatoslabel tdatofecha tdatocre"><p><span>Emisión del Comprobante de pago que modifica:</span></p> 10/05/2018</td>
						</tr>
					</tbody>
				</table>
			
			<div class="tablageneral">        
				<table cellpadding="0" cellspacing="0">
					<tbody>
						<tr class="titulotable">
							<td class="cantidadtabla">Cantidad</td>
							<td class="cantidadtabla">Código</td>
							<td>Descripción</td>
							<td class="preciotabla">Precio Unitario</td>
							<td class="preciotabla ulttable">Valor de venta o servicio prestado</td>
						</tr>
						<tr class="detalletable" style="height: 120px;">
							<td class="nombredetalle">1</td>
							<td class="nombredetalle">HUS87</td>
							<td style="font-size: 18px;">Intereses Moratorios por atraso en el pago de la factura 5346</td>
							<td></td>
							<td class="ulttable">$100.00</td>
						</tr>
						<tr class="detalletable">
							<td class="nombredetalle"></td>
							<td class="nombredetalle"></td>
							<td style="font-weight: bold;">Son ciento dieciocho y 00/100 soles</td>
							<td></td>
							<td class="ulttable"></td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="tablageneral tablacostos">        
				<table cellpadding="0" cellspacing="0">
					<tbody>   
						<tr class="detalletable">                                                                
							<td rowspan="3" style="padding: 0px !important; vertical-align: sub !important;">
								<b>MOTIVOS DE LA EMISIÓN DE LA NOTA DE DÉBITO</b>
								<table style="width:100%">
									<tr>
										<th style="font-weight: normal; font-size: 12px;">
											<input type="checkbox" id="cbox1" value="first_checkbox" checked> Intereses por Mora
										</th>
										<th style="font-weight: normal; font-size: 12px;">
											<input type="checkbox" id="cbox1" value="first_checkbox"> Aumento en el Valor
										</th>
									</tr>
									<tr>
										<th style="font-weight: normal; font-size: 12px;">
											<input type="checkbox" id="cbox1" value="first_checkbox"> Penalidades
										</th>
										<th>
										</th>
									</tr>
								</table>
							</td>
							<td class="preciotabla fintabla fintablados"><b>I.G.V.</b></td>
							<td class="preciotabla ulttable fintabla">$18.00</td>
						</tr> 
						<tr class="detalletable"> 
							<td class="preciotabla fintabla fintablados"><b>OTROS</b></td>
							<td class="preciotabla ulttable fintabla"> -- </td>
						</tr>   
						<tr class="detalletable">
							<td class="preciotabla fintabla fintabla fintablados"><b>TOTAL</b></td>
							<td class=" preciotabla ulttable fintabla">$118.00</td>
						</tr>
					</tbody>
				</table>
			</div>
			<table>
				<tbody>
					<tr>
						<td style="width: 34%;">
							<p>
								<b>Recepción de la nota de credito</b><br />                                
							</p>
							<p><span>Apellidos y nombres de quien recepciona la nota de crédito:</span> Cristopher Andréz Villanueva Paz</p>
							<p><span>DNI:</span> 78869955</p>
							<p><span>Fecha de recepción:</span> 26/05/2018</p>
						</td>
						<td style="width: 33%; text-align: center;">
							<img style="width: 124px; margin-bottom: 11px;" src="../assets/theme_doc_elect/images/qr.png" />
						</td>
						<td style="width: 33%;">
							Autorizado mediante Resolución de Intendencia N° 032-005-Representación impresa de la Nota de Crédito. Consulte su documento electrónico en: 
							<span style="font-size: 10px">https://bit.ly/2HiRWZI</span>
						</td>
					</tr>
					<tr>
						<td style="width: 34%;">
						</td>
						<td style="text-align: center;" colspan="2">
							<span class="codigofac">HASH: dGp1cUib9QWXjNEFDUqjDGszUKw=</span>
						</td>
					</tr>
				</tbody>    
			</table>
			
		</body>
		</html>
		';

		$resp['respuesta'] = 'ok';
		$resp['html'] = $html;
		return $resp;
	}
}
?>