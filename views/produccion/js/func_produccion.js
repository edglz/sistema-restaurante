/* LEYENDA ESTADO DE LOS PLATOS
a = solicitado
b = preparacion
c = preparado
d = entregado
z = anulado
*/

$(function() {
	nropedidosMesa();
	setInterval(nropedidosMesa, 10000);
	nropedidosMostrador();
	setInterval(nropedidosMostrador, 10000);
	nropedidosDelivery();
	setInterval(nropedidosDelivery, 10000);
	$('#area-p').addClass("active");
	$('.container-fluid').addClass('p-10');
});	

/* Mostrar todos los pedidos realizados en las mesas */
var contMe = 0;
var nropedidosMesa = function(){
	$.ajax({     
        type: "post",
        dataType: "json",
        url: $('#url').val()+'produccion/mesas_list',
        success: function (data){
        	if(data.length == 0){ 
        		$('#cant_pedidos_mesa').text('');
        		pedidosMesa();
        		contMe = 0;
        	} 
	        $.each(data, function(i, item) {
				var nroPedMe = parseInt(item.Total.nro_p);
				$('#cant_pedidos_mesa').text(nroPedMe);
	    		if(parseInt(nroPedMe) !== contMe){
	    			contMe = 0;
	    			pedidosMesa();
	    			var sound = new buzz.sound("public/sound/ding_ding", {
						formats: [ "ogg", "mp3", "aac" ]
					});
					sound.play();
					contMe = nroPedMe + contMe;
	    		}
	    		//console.log('contMe = '+contMe+' <> NroPedMe = '+nroPedMe);
			});
		}
	})
}

var pedidosMesa = function(){
	moment.locale('es');
	$('#list_pedidos_mesa').html('<tr><td colspan="6"><div class="m-t-40 m-b-40 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div></td></tr>');
	$.ajax({     
        type: "post",
        dataType: "json",
        url: $('#url').val()+'produccion/mesas_list',
        success: function (data){
        $('#list_pedidos_mesa').empty();
        $.each(data, function(i, item) {
    		var horaPedido = moment(item.fecha_pedido).fromNow();
    		if (item.id_tipo == 2){
				probar = 'success';
				nombar = 'EN ESPERA';
				accion = 'atendido';
    		} else if(item.id_tipo == 1){
    			if(item.estado == 'a'){
					probar = 'success';
					nombar = 'EN ESPERA';
					accion = 'preparacion';
	    		} else if(item.estado == 'b'){
					probar = 'warning';
					nombar = 'EN PREPARACION';
					accion = 'atendido';
	    		}
    		}

    		var comentario = (item.comentario !== '') ? '<small class="text-uppercase font-14"><i class="fa fa-comment"></i> '+item.comentario+'</small>' : '';

    		$('#list_pedidos_mesa')
	            .append(
	                $('<tr class="tr-left-2"/>')
	                .append(
	                    $('<td/>')
	                    .html('<h5 class="m-b-0">N° '+item.nro_mesa+'</h5><h6 class="text-muted">'+item.desc_salon+'</h6>')
	                )
	                .append(
	                    $('<td/>')
	                    .html('<h4 class="m-b-0"><label class="font-bold m-b-0 text-danger">'+item.cantidad+' UNI</label> '+item.nombre_prod+' <br><span class="label label-info">'+item.pres_prod+'</span>'
	                    	+'&nbsp;<span class="label label-warning">'+item.Producto.pro_cat+'</span></h4>'
	                    	+comentario)
	                )
	                .append(
	                    $('<td/>')
	                    .html('<span class="font-16 font-bold">'+horaPedido+'</span>')
	                )
	                .append(
	                    $('<td/>')
	                    .html('<span class="label label-'+probar+' font-12 p-10">'+nombar+'</span>')
	                )
	                .append(
	                    $('<td/>')
	                    .html(item.nombres+' '+item.ape_paterno)
	                )
	                .append(
	                    $('<td class="text-right"/>')
	                    .html('<a onclick="'+accion+'('+item.id_pedido+','+item.id_pres+',\''+item.fecha_pedido+'\');">'
	                    	+'<button type="button" class="btn btn-circle btn-lg btn-success"><i class="ti-check"></i></button></a>')
	                )
	            );
    		})
        }
    });
}

/* Mostrar todos los pedidos realizados en el mostrador o para llevar */
var contMo = 0;
var nropedidosMostrador = function(){
	$.ajax({     
        type: "post",
        dataType: "json",
        url: $('#url').val()+'produccion/mostrador_list',
        success: function (data){
        	//console.log('A:'+data);
        	if(data.length == 0){ 
        		$('#cant_pedidos_most').text('');
        		pedidosMostrador();
        		contMo = 0;
        	}
        	$.each(data, function(i, item) {
        		//console.log('B:'+item);
				var nroPedMo = parseInt(item.Total.nro_p);
				$('#cant_pedidos_most').text(nroPedMo);
				//console.log('C:'+nroPedMo);
	    		if(parseInt(nroPedMo) !== contMo){
	    			contMo = 0;
	    			pedidosMostrador();
	    			var sound = new buzz.sound("public/sound/ding_ding", {
						formats: [ "ogg", "mp3", "aac" ]
					});
					sound.play();
					contMo = nroPedMo + contMo;
					//console.log('D:'+contMo);
	    		}
	    		//console.log('contMo = '+contMo+' <> NroPedMo = '+nroPedMo);
			})
		}
	})
}

var pedidosMostrador = function(){
	moment.locale('es');
	$('#list_pedidos_most').html('<tr><td colspan="6"><div class="m-t-40 m-b-40 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div></td></tr>');
	$.ajax({     
        type: "post",
        dataType: "json",
        url: $('#url').val()+'produccion/mostrador_list',
        success: function (data){
        $('#list_pedidos_most').empty();
        $.each(data, function(i, item) {
    		var horaPedido = moment(item.fecha_pedido).fromNow();
    		if (item.id_tipo == 2){
    			probar = 'success';
    			nombar = 'EN ESPERA';
    			accion = 'atendido';
    		} else if(item.id_tipo == 1){
    			if(item.estado == 'a'){
		    		probar = 'success';
		    		nombar = 'EN ESPERA';
		    		accion = 'preparacion';
	    		} else if(item.estado == 'b'){
    				probar = 'warning';
    				nombar = 'EN PREPARACION';
    				accion = 'atendido';
	    		}
    		}

    		var comentario = (item.comentario !== '') ? '<small class="text-uppercase font-14"><i class="fa fa-comment"></i> '+item.comentario+'</small>' : '';

    		$('#list_pedidos_most')
	            .append(
	                $('<tr class="tr-left-2"/>')
	                .append(
	                    $('<td/>')
	                    .html('<h5 class="m-b-0">'+item.nro_mesa+'</h5><h6 class="text-muted">N&uacute;mero</h6>')
	                )
	                .append(
	                    $('<td/>')
	                    .html('<h4 class="m-b-0"><label class="font-bold m-b-0 text-danger">'+item.cantidad+' UNI</label> '+item.nombre_prod+' <br><span class="label label-info">'+item.pres_prod+'</span>'
	                    	+'&nbsp;<span class="label label-warning">'+item.Producto.pro_cat+'</span></h4>'
	                    	+comentario)
	                )
	                .append(
	                    $('<td/>')
	                    .html('<span class="font-16 font-bold">'+horaPedido+'</span>')
	                )
	                .append(
	                    $('<td/>')
	                    .html('<span class="label label-'+probar+' font-12 p-10">'+nombar+'</span>')
	                )
	                .append(
	                    $('<td/>')
	                    .html(item.nombres+' '+item.ape_paterno)
	                )
	                .append(
	                    $('<td class="text-right"/>')
	                    .html('<a onclick="'+accion+'('+item.id_pedido+','+item.id_pres+',\''+item.fecha_pedido+'\');">'
	                    	+'<button type="button" class="btn btn-circle btn-lg btn-success"><i class="ti-check"></i></button></a>')
	                )
	            );		
    		})
        }
    });
}

/* Mostrar todos los pedidos realizados en el mostrador o para llevar */
var contDe = 0;
var nropedidosDelivery = function(){
	$.ajax({     
        type: "post",
        dataType: "json",
        url: $('#url').val()+'produccion/delivery_list',
        success: function (data){
        	if(data.length == 0){ 
        		$('#cant_pedidos_del').text('');
        		pedidosDelivery();
        		contDe = 0;
        	}
	        $.each(data, function(i, item) {
				var nroPedDe = parseInt(item.Total.nro_p);
				$('#cant_pedidos_del').text(nroPedDe);
	    		if(parseInt(nroPedDe) !== contDe){
	    			contDe = 0;
	    			pedidosDelivery();
	    			var sound = new buzz.sound("public/sound/ding_ding", {
						formats: [ "ogg", "mp3", "aac" ]
					});
					sound.play();
					contDe = nroPedDe + contDe;
	    		}
	    		//console.log('contDe = '+contDe+' <> NroPedDe = '+nroPedDe);
			})
		}
	})
}

var pedidosDelivery = function(){
	moment.locale('es');
	$('#list_pedidos_del').html('<tr><td colspan="6"><div class="m-t-40 m-b-40 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div></td></tr>');
	$.ajax({     
        type: "post",
        dataType: "json",
        url: $('#url').val()+'produccion/delivery_list',
        success: function (data){
        $('#list_pedidos_del').empty();
        $.each(data, function(i, item) {
    		var horaPedido = moment(item.fecha_pedido).fromNow();
    		$('#cant_pedidos_del').text(item.Total.nro_p);
    		if (item.id_tipo == 2){
	    		probar = 'success';
	    		nombar = 'EN ESPERA';
	    		accion = 'atendido';
    		} else if(item.id_tipo == 1){
    			if(item.estado == 'a'){
	    			probar = 'success';
	    			nombar = 'EN ESPERA';
	    			accion = 'preparacion';
	    		} else if(item.estado == 'b'){
	    			probar = 'warning';
	    			nombar = 'EN PREPARACION';
	    			accion = 'atendido';
	    		}
    		}

    		var comentario = (item.comentario !== '') ? '<small class="text-uppercase font-14"><i class="fa fa-comment"></i> '+item.comentario+'</small>' : '';

    		$('#list_pedidos_del')
	            .append(
	                $('<tr class="tr-left-2"/>')
	                .append(
	                    $('<td/>')
	                    .html('<h5 class="m-b-0">'+item.nro_mesa+'</h5><h6 class="text-muted">N&uacute;mero</h6>')
	                )
	                .append(
	                    $('<td/>')
	                    .html('<h4 class="m-b-0"><label class="font-bold m-b-0 text-danger">'+item.cantidad+' UNI</label> '+item.nombre_prod+' <br><span class="label label-info">'+item.pres_prod+'</span>'
	                    	+'&nbsp;<span class="label label-warning">'+item.Producto.pro_cat+'</span></h4>'
	                    	+comentario)
	                )
	                .append(
	                    $('<td/>')
	                    .html('<span class="font-16 font-bold">'+horaPedido+'</span>')
	                )
	                .append(
	                    $('<td/>')
	                    .html('<span class="label label-'+probar+' font-12 p-10">'+nombar+'</span>')
	                )
	                .append(
	                    $('<td/>')
	                    .html(item.nombres+' '+item.ape_paterno)
	                )
	                .append(
	                    $('<td class="text-right"/>')
	                    .html('<a onclick="'+accion+'('+item.id_pedido+','+item.id_pres+',\''+item.fecha_pedido+'\');">'
	                    	+'<button type="button" class="btn btn-circle btn-lg btn-success"><i class="ti-check"></i></button></a>')
	                )
	            );			
    		})
        }
    });
}

var agrupacion_platos_list = function(){
	moment.locale('es');
	$('#agrupacion_platos_list').html('<div class="col-lg-12 m-t-40 m-b-40 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');
	$.ajax({     
        //async: false,
        type: "post",
        dataType: "json",
        url: $('#url').val()+'produccion/agrupacion_platos_list',
        success: function (data){
        $('#agrupacion_platos_list').empty();
        $.each(data, function(i, item) {        	
    		$('#agrupacion_platos_list')
    			.append(
	                $('<div class="col-lg-12"/>')
	                .html('<div class="card">'
	                	+'<div class="card-body p-0">'
	                	+'<div class="d-flex flex-wrap p-20 p-b-10 justify-center" style="background: #8196ac;"><div><h2 class="font-bold text-white">'+item.nombre_prod+' <span class="label label-danger p-10">'+item.pres_prod+'</span></h2></div><div class="ml-auto align-self-center"><label class="font-bold font-30 m-b-0" style="color: #fff947 !important;"><span class="total'+i+'"></span> UNI</label></div></div>'
	                	+'<table class="table stylish-table b-t" id="table'+i+'">'
	                		+'<thead class="table-head">'
		                        +'<tr><th width="10%">Atención</th><th width="40%">Cantidad/Producto</th><th width="15%">Tiempo</th><th width="15%">Estado</th><th width="10%">Mozo/Cajero</th><th width="10%" class="text-right">Acci&oacute;n</th></tr>'
		                    +'</thead>'
		                    +'<tbody class="tb-st listdetalle'+i+'">')
	            );
	        	$.ajax({  
					async: false,   
			        type: "post",
			        dataType: "json",
			        url: $('#url').val()+'produccion/agrupacion_platos_detalle',
			        data: { 
			        	nombre_prod : item.nombre_prod,
			        	pres_prod : item.pres_prod
			        },
			        success: function (dato){
			        var total_cantidad = 0;
			        $.each(dato, function(index, value) {
						var horaPedido = moment(value.fecha_pedido).fromNow();
						var tipo_atencion = (value.tipo_atencion == 1) ? '<h5 class="m-b-0">MESA: '+value.nro_mesa+'</h5><h6 class="text-muted">'+value.desc_salon+'</h6>' : '<h5 class="m-b-0">Nro: '+value.nro_mesa+'</h5><h6 class="text-muted">'+value.desc_salon+'</h6>';
			    		total_cantidad += parseFloat(value.cantidad);
			    		if (value.id_tipo == 2){
				    		probar = 'success';
				    		nombar = 'EN ESPERA';
				    		accion = 'atendido';
			    		} else if(value.id_tipo == 1){
			    			if(value.estado == 'a'){
				    			probar = 'success';
				    			nombar = 'EN ESPERA';
				    			accion = 'preparacion';
				    		} else if(value.estado == 'b'){
				    			probar = 'warning';
				    			nombar = 'EN PREPARACION';
				    			accion = 'atendido';
				    		}
			    		}

			    		var comentario = (value.comentario !== '') ? '<small class="text-uppercase font-14"><i class="fa fa-comment"></i> '+value.comentario+'</small>' : '';

			        	$('.listdetalle'+i)
				            .append(
				                $('<tr class="tr-left"/>')
				                .append(
				                    $('<td/>')
				                    .html(tipo_atencion)
				                )
				                .append(
				                    $('<td/>')
				                    .html('<h4 class="m-b-0"><label class="font-bold m-b-0 text-danger">'+value.cantidad+' UNI</label> '+value.nombre_prod+' <br><span class="label label-info">'+value.pres_prod+'</span>'
				                    	+'&nbsp;<span class="label label-warning">'+value.Producto.pro_cat+'</span></h4>'
				                    	+comentario)
				                )
				                .append(
				                    $('<td/>')
				                    .html('<span class="font-16 font-bold">'+horaPedido+'</span>')
				                )
				                .append(
				                    $('<td/>')
				                    .html('<span class="label label-'+probar+' font-12 p-10">'+nombar+'</span>')
				                )
				                .append(
				                    $('<td/>')
				                    .html(value.nombres+' '+value.ape_paterno)
				                )
				                .append(
				                    $('<td class="text-right"/>')
				                    .html('<a onclick="'+accion+'('+value.id_pedido+','+value.id_pres+',\''+value.fecha_pedido+'\');">'
				                    	+'<button type="button" class="btn btn-circle btn-lg btn-success"><i class="ti-check"></i></button></a>')
				                )
				            );			
			    		});
			        	$('.total'+i).text(total_cantidad);			        	
			        }
			    });
    		});	
        }
    });
}

var agrupacion_pedidos_list = function(){
	moment.locale('es');
	$('#agrupacion_pedidos_list').html('<div class="col-lg-12 m-t-40 m-b-40 text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');
	$.ajax({     
        //async: false,
        type: "post",
        dataType: "json",
        url: $('#url').val()+'produccion/agrupacion_pedidos_list',
        success: function (data){
        $('#agrupacion_pedidos_list').empty();
        $.each(data, function(i, item) {
        	var tipoatencion = (item.tipo_atencion == 1)  ? 'MESA: ' : 'Nro: ';   	
    		$('#agrupacion_pedidos_list')
    			.append(
	                $('<div class="col-lg-12"/>')
	                .html('<div class="card">'
	                	+'<div class="card-body p-0">'
	                	+'<div class="d-flex flex-wrap p-20 p-b-10 justify-center" style="background: #8196ac;"><div><h3 class="font-bold text-white">'+tipoatencion+''+item.nro_mesa+'</h3><h4 class="text-white">'+item.desc_salon+'</h4></div><div class="ml-auto align-self-center"><label class="font-bold font-30 m-b-0" style="color: #fff947 !important;"><span class="total'+i+'"></span> UNI</label></div></div>'
	                	+'<table class="table stylish-table b-t" id="table'+i+'">'
	                		+'<thead class="table-head">'
		                        +'<tr><th width="10%">Atención</th><th width="40%">Cantidad/Producto</th><th width="15%">Tiempo</th><th width="15%">Estado</th><th width="10%">Mozo/Cajero</th><th width="10%" class="text-right">Acci&oacute;n</th></tr>'
		                    +'</thead>'
		                    +'<tbody class="tb-st listdetalle'+i+'">')
	            );
	        	$.ajax({  
					async: false,   
			        type: "post",
			        dataType: "json",
			        url: $('#url').val()+'produccion/agrupacion_pedidos_detalle',
			        data: { 
			        	id_pedido : item.id_pedido
			        },
			        success: function (dato){
			        var total_cantidad = 0;
			        $.each(dato, function(index, value) {
						var horaPedido = moment(value.fecha_pedido).fromNow();
						var tipo_atencion = (value.tipo_atencion == 1) ? '<h5 class="m-b-0">MESA: '+value.nro_mesa+'</h5><h6 class="text-muted">'+value.desc_salon+'</h6>' : '<h5 class="m-b-0">Nro: '+value.nro_mesa+'</h5><h6 class="text-muted">'+value.desc_salon+'</h6>';
			    		total_cantidad += parseFloat(value.cantidad);
			    		if (value.id_tipo == 2){
				    		probar = 'success';
				    		nombar = 'EN ESPERA';
				    		accion = 'atendido';
			    		} else if(value.id_tipo == 1){
			    			if(value.estado == 'a'){
				    			probar = 'success';
				    			nombar = 'EN ESPERA';
				    			accion = 'preparacion';
				    		} else if(value.estado == 'b'){
				    			probar = 'warning';
				    			nombar = 'EN PREPARACION';
				    			accion = 'atendido';
				    		}
			    		}

			    		var comentario = (value.comentario !== '') ? '<small class="text-uppercase font-14"><i class="fa fa-comment"></i> '+value.comentario+'</small>' : '';

			        	$('.listdetalle'+i)
				            .append(
				                $('<tr class="tr-left"/>')
				                .append(
				                    $('<td/>')
				                    .html(tipo_atencion)
				                )
				                .append(
				                    $('<td/>')
				                    .html('<h4 class="m-b-0"><label class="font-bold m-b-0 text-danger">'+value.cantidad+' UNI</label> '+value.nombre_prod+' <br><span class="label label-info">'+value.pres_prod+'</span>'
				                    	+'&nbsp;<span class="label label-warning">'+value.Producto.pro_cat+'</span></h4>'
				                    	+comentario)
				                )
				                .append(
				                    $('<td/>')
				                    .html('<span class="font-16 font-bold">'+horaPedido+'</span>')
				                )
				                .append(
				                    $('<td/>')
				                    .html('<span class="label label-'+probar+' font-12 p-10">'+nombar+'</span>')
				                )
				                .append(
				                    $('<td/>')
				                    .html(value.nombres+' '+value.ape_paterno)
				                )
				                .append(
				                    $('<td class="text-right"/>')
				                    .html('<a onclick="'+accion+'('+value.id_pedido+','+value.id_pres+',\''+value.fecha_pedido+'\');">'
				                    	+'<button type="button" class="btn btn-circle btn-lg btn-success"><i class="ti-check"></i></button></a>')
				                )
				            );			
			    		});
			        	$('.total'+i).text(total_cantidad);			        	
			        }
			    });
    		});	
        }
    });
}

var agruparPlatos = function(){
	$('.display-agrupacion-platos').css('display','block');
	$('.display-agrupacion-pedidos').css('display','none');
	$('.display-lista').css('display','none');
	$('#tipo_atencion').val('4');
	//$('#condicion').val('1');
	agrupacion_platos_list();
}

var agruparPedidos = function(){
	$('.display-agrupacion-platos').css('display','none');
	$('.display-agrupacion-pedidos').css('display','block');
	$('.display-lista').css('display','none');
	$('#tipo_atencion').val('5');
	agrupacion_pedidos_list();
}

var listarPedidos = function(){
	$('#tipo_atencion').val($('#tipo_atencion_opcional').val());
	$('.display-agrupacion-platos').css('display','none');
	$('.display-agrupacion-pedidos').css('display','none');
	$('.display-lista').css('display','block');
	if($('#tipo_atencion_opcional').val() == 1){
		nropedidosMesa();
		pedidosMesa();
	} else if($('#tipo_atencion_opcional').val() == 2){
		nropedidosMostrador();
		pedidosMostrador();
		//console.log('BOTON POR ORDEN DE LLEGADA, OPCION MOSTRADOR');
	} else if($('#tipo_atencion_opcional').val() == 3){
		nropedidosDelivery();
		pedidosDelivery();
	}
}

$('#tab1').on('click', function() {
	$('#tipo_atencion').val('1');
	$('#tipo_atencion_opcional').val('1');		
	nropedidosMesa();
	pedidosMesa();
});

$('#tab2').on('click', function() {
	$('#tipo_atencion').val('2');
	$('#tipo_atencion_opcional').val('2');
	nropedidosMostrador();
	pedidosMostrador();
});

$('#tab3').on('click', function() {
	$('#tipo_atencion').val('3');
	$('#tipo_atencion_opcional').val('3');
	nropedidosDelivery();
	pedidosDelivery();
});

var preparacion = function(cod_ped,cod_prod,fecha_p){
	$.ajax({
		dataType: 'JSON',
		type: 'POST',
		url: $('#url').val()+'produccion/preparacion',
		data: {
	      	cod_ped: cod_ped,
	      	cod_prod: cod_prod,
	      	fecha_p: fecha_p
      	},
      	success: function (datos) {
      		if($('#tipo_atencion').val() == 1){
      			$('#cant_pedidos_mesa').text('');
	      		nropedidosMesa();
	      		pedidosMesa();
      		} else if($('#tipo_atencion').val() == 2){
      			nropedidosMostrador();
				pedidosMostrador();
      		} else if($('#tipo_atencion').val() == 3){
      			nropedidosDelivery();
	      		pedidosDelivery();
      		} else if($('#tipo_atencion').val() == 4) {
      			agrupacion_platos_list();
      		} else if($('#tipo_atencion').val() == 5) {
      			agrupacion_pedidos_list();
      		}	
      	},
      	error: function(jqXHR, textStatus, errorThrown){
        	console.log(errorThrown + ' ' + textStatus);
      	}   
  	});
}

var atendido = function(cod_ped,cod_prod,fecha_p){
	$.ajax({
		dataType: 'JSON',
		type: 'POST',
		url: $('#url').val()+'produccion/atendido',
		data: {
      		cod_ped: cod_ped,
      		cod_prod: cod_prod,
      		fecha_p: fecha_p
      	},	
      	success: function (datos) {
      		if($('#tipo_atencion').val() == 1){
      			nropedidosMesa();
	      		pedidosMesa();
	      		$('#cant_pedidos_mesa').text('');
				contMe = contMe - 1;
      		} else if($('#tipo_atencion').val() == 2){
      			nropedidosMostrador();
	      		pedidosMostrador();
	      		$('#cant_pedidos_most').text('');
				contMo = contMo - 1;
      		} else if($('#tipo_atencion').val() == 3){
      			nropedidosDelivery();
				pedidosDelivery();
				$('#cant_pedidos_del').text('');
				contDe = contDe - 1;
      		} else if($('#tipo_atencion').val() == 4) {
      			agrupacion_platos_list();
      			if($('#cant_pedidos_mesa').text() == 1){
      				$('#cant_pedidos_mesa').text('');
	    			contMe = 0;
	    		} else if($('#cant_pedidos_most').text() == 2) {
	    			$('#cant_pedidos_most').text('');
					contMo = 0;
	    		} else if($('#cant_pedidos_del').text() == 3) {
	    			$('#cant_pedidos_del').text('');
					contDe = 0;
	    		}
      		} else if($('#tipo_atencion').val() == 5) {
      			agrupacion_pedidos_list();
      			if($('#cant_pedidos_mesa').text() == 1){
      				$('#cant_pedidos_mesa').text('');
	    			contMe = 0;
	    		} else if($('#cant_pedidos_most').text() == 2) {
	    			$('#cant_pedidos_most').text('');
					contMo = 0;
	    		} else if($('#cant_pedidos_del').text() == 3) {
	    			$('#cant_pedidos_del').text('');
					contDe = 0;
	    		}
      		}	
      		/*
      		else {
      			agrupacion_platos_list();
      			if($('#cant_pedidos_mesa').text() == 1){
      				$('#cant_pedidos_mesa').text('');
	    			contMe = 0;
	    		} else if($('#cant_pedidos_most').text() == 2) {
	    			$('#cant_pedidos_most').text('');
					contMo = 0;
	    		} else if($('#cant_pedidos_del').text() == 3) {
	    			$('#cant_pedidos_del').text('');
					contDe = 0;
	    		}
      		}
      		*/
      	},
      	error: function(jqXHR, textStatus, errorThrown){
        	console.log(errorThrown + ' ' + textStatus);
      	}   
  	});
}