/* ESTADO DE LOS PEDIDOS
a = aperturado/abierto/activo/
b = preparacion
c = en camino
d = despachado/entregado/cerrado
z = anulado
*/

// Mousetrap.bind('f', function(e) {
//     $v = $("#pedido_seleccionado").val();
//     console.log($v)
//     if($v != ""){
//         window.location.replace($("#url").val() + "venta/orden/"+$("#pedido_seleccionado").val()+'?f=facturar')
//     }else{
//         Swal.fire({
//             title: 'Error al acceder',
//             html: "Debes seleccionar una mesa con pedido abierto",
//             icon: "error"
//         })
//     }
// });
// Mousetrap.bind('d', function(e) {
//     $v = $("#pedido_seleccionado").val();
//     console.log($v)
//     if($v != ""){
//         window.location.replace($("#url").val() + "venta/orden/"+$("#pedido_seleccionado").val()+'?f=dividir')
//     }else{
//         Swal.fire({
//             title: 'Error al acceder',
//             html: "Debes seleccionar una mesa con pedido abierto",
//             icon: "error"
//         })
//     }
// });
var moneda = $("#moneda").val();
$(function() {
    var ele = document.getElementsByName("salones")
for(var x = 0 ; x < ele.length ; x ++){
    if(x == 0){
        ele[x].click();
        ele[x].classList.add('active')
    }
    else{
        ele[x].classList.remove('active')
    }
}
    moment.locale('es');
    tiempo_mesa();
    setInterval(tiempo_mesa, 1000);
    tiempo_pedido();
    setInterval(tiempo_pedido, 1000);
    tiempo_preparacion();
    setInterval(tiempo_preparacion, 1000);
    countNuevoPedidoDelivery();
    setInterval(countNuevoPedidoDelivery, 10000);
    alert_pedidos_programados();
    setInterval(alert_pedidos_programados, 50000);
    validarApertura();
    var parametro = getUrlParameter('cod');
    if(parametro !== undefined){
        if(getUrlParameter('tip') == 3){
            $('#codtipoped').val(3);
            listarPedidosDetalle(3,parametro,0);
            activaTab('tabp-3');
            $('#form-nuevo-pedido').attr('action','venta/pedido_create/pc3');
            if(getUrlParameter('est') == 'a'){
                $('#codpestdelivery').val(1);
                activaTab('delivery01');
                delivery_list_a();
                editar_pedido(parametro);
            } else if (getUrlParameter('est') == 'b'){
                activaTab('delivery02');
                delivery_list_b();
            } else if (getUrlParameter('est') == 'c'){
                activaTab('delivery03');
                delivery_list_c();
            } else if (getUrlParameter('est') == 'd'){
                activaTab('delivery04');
                delivery_list_d();
            }
            $('.display-estado-mesa').css('display','none');
            $('.cont01-1').css('display','none');
            $('.cont01-2').css('display','block');
        } else if(getUrlParameter('tip') == 2){
            activaTab('tabp-2');
            if(getUrlParameter('est') == 'a'){
                $('#codtipoped').val(2);
                activaTab('mostrador01');
                mostrador_list_a();
                //listarPedidosDetalle(2,parametro,0);
            }
            $('#form-nuevo-pedido').attr('action','venta/pedido_create/pc2');
            $('.display-estado-mesa').css('display','none');
            $('.cont01-1').css('display','none');
            $('.cont01-2').css('display','block');
        }        
    }
    $('#form-nuevo-pedido').formValidation({
    framework: 'bootstrap',
    excluded: ':disabled',
        fields: {}
    })
    .on('success.form.fv', function(e) {
        // Prevent form submission
        if($('#codtipoped').val() == 3 && $('#cliente_id').val() == ''){
            Swal.fire({   
                title:'Advertencia',   
                text: 'Ingrese un cliente al pedido',
                icon: "warning", 
                confirmButtonColor: "#34d16e",   
                confirmButtonText: "Aceptar",
                allowOutsideClick: false,
                showCancelButton: false,
                showConfirmButton: true
            }, function() {
                return false
            });
            return false;
        } else {
            e.preventDefault();
            var $form = $(e.target);
            var fv = $form.data('formValidation');
            fv.defaultSubmit();
        }
    });
    $('#form-cambiar-mesa').formValidation({
    framework: 'bootstrap',
    excluded: ':disabled',
        fields: {}
    })
    .on('success.form.fv', function(e) {
        // Prevent form submission
        e.preventDefault();
        var $form = $(e.target);
        var fv = $form.data('formValidation');
        fv.defaultSubmit();
    });

    $('#form-mover-pedidos').formValidation({
    framework: 'bootstrap',
    excluded: ':disabled',
        fields: {}
    })
    .on('success.form.fv', function(e) {
        // Prevent form submission
        e.preventDefault();
        var $form = $(e.target);
        var fv = $form.data('formValidation');
        fv.defaultSubmit();
    });

    $('#form-editar-pedido')
    .formValidation({
        framework: 'bootstrap',
        excluded: ':disabled',
        fields: {
        }
    })
    .on('success.form.fv', function(e) {

    e.preventDefault();
    var $form = $(e.target),
    fv = $form.data('formValidation');
    
    var id_repartidor = ($('#id_repartidor_edit').val() == null) ? '1' : $('#id_repartidor_edit').val();

    id_pedido = $('#id_pedido').val();
    hora_entrega = $('#hora_entrega_edit').val();
    id_repartidor = id_repartidor;
    amortizacion = $('#amortizacion').val();
    tipo_pago = $('#id_tipo_pago').val();
    paga_con = $('#paga_con').val();
    comision_delivery = $('#comision_delivery').val();

    $.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: $('#url').val()+'venta/pedido_crud',
        data: {
            id_pedido: id_pedido,
            id_repartidor: id_repartidor,
            hora_entrega: hora_entrega,
            amortizacion: amortizacion,
            tipo_pago: tipo_pago,
            paga_con: paga_con,
            comision_delivery: comision_delivery
        },
        success: function (cod) {
            $('#modal-editar-pedido').modal('hide');
            Swal.fire({   
                title:'Proceso Terminado',   
                text: 'Datos actualizados correctamente',
                icon: "success", 
                confirmButtonColor: "#34d16e",   
                confirmButtonText: "Aceptar",
                allowOutsideClick: false,
                showCancelButton: false,
                showConfirmButton: true
            }, function() {
                return false
            });
            delivery_list_a();
            delivery_list_b();
            listarPedidosDetalle(3,$('#id_pedido').val(),0);          
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown + ' ' + textStatus);
        }    
    });
        return false;
    });

    $('#form-editar-venta-pago')
    .formValidation({
        framework: 'bootstrap',
        excluded: ':disabled',
        fields: {
        }
    })
    .on('success.form.fv', function(e) {

    e.preventDefault();
    var $form = $(e.target),
    fv = $form.data('formValidation');
    
    tipo_pago = $('#id_venta_tipopago').val();
    id_venta = $('#id_venta').val();
    id_tipo_pago = $('#id_tipo_pago_v').val();

    $.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: $('#url').val()+'venta/venta_edit_pago',
        data: {
            id_venta: id_venta,
            id_tipo_pago: id_tipo_pago,
            tipo_pago: tipo_pago
        },
        success: function (cod) {
            $('#modal-editar-venta-pago').modal('hide');
            Swal.fire({   
                title:'Proceso Terminado',   
                text: 'Datos actualizados correctamente',
                icon: "success", 
                confirmButtonColor: "#34d16e",   
                confirmButtonText: "Aceptar",
                allowOutsideClick: false,
                showCancelButton: false,
                showConfirmButton: true
            }, function() {
                return false
            });
            delivery_list_c();
            listarPedidosDetalle(3,$('#id_pedido').val(),id_venta);        
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown + ' ' + textStatus);
        }    
    });
        return false;
    });

    $("#telefono_cliente").autocomplete({
        delay: 1,
        autoFocus: true,
        source: function (request, response) {
            $.ajax({
                url: $('#url').val()+'venta/buscar_cliente_telefono',
                type: "post",
                dataType: "json",
                data: {
                    cadena: request.term
                },
                success: function (data) {
                    response($.map(data, function (item) {
                        tipo_cli = (item.tipo_cliente == 1) ? $("#diAcr").val() : $("#tribAcr").val();
                        return {
                            id_cliente: item.id_cliente,
                            nombre: item.nombre,
                            telefono: item.telefono,
                            direccion: item.direccion,
                            referencia: item.referencia,
                            //label: tipo_cli+': '+item.dni+''+item.ruc,
                            value: item.telefono
                        }
                    }))
                }
            })
        },
        select: function (e, ui) {
            //$("#documento_cliente").val('');
            $("#cliente_id").val(ui.item.id_cliente);
            /////////////////////////////////////////////
            $('.display-nombre').css('display','block');
            //$('.display-telefono-cliente').css('display','block');
            $("#nomb_cliente").removeAttr('disabled');
            //$("#telefono_cliente").removeAttr('disabled');
            /////////////////////////////////////////
            if($('input:radio[name=tipo_entrega]:checked').val() == 1){
                $('.display-direccion-cliente').css('display','block');
                $('.display-referencia-cliente').css('display','block');
                $('.display-repartidor').css('display','block');
                $("#direccion_cliente").removeAttr('disabled');
                $("#referencia_cliente").removeAttr('disabled');
                $("#id_repartidor").removeAttr('disabled');
            } else {
                $('.display-direccion-cliente').css('display','none');
                $('.display-referencia-cliente').css('display','none');
                $('.display-repartidor').css('display','none');
                $("#direccion_cliente").attr('disabled','true');
                $("#referencia_cliente").attr('disabled','true');
                $("#id_repartidor").attr('disabled','true');
            }
            ////////////////////////////////////////////
            $("#telefono_cliente").val(ui.item.telefono);
            $("#nomb_cliente").val(ui.item.nombre);
            $("#direccion_cliente").val(ui.item.direccion);
            $("#referencia_cliente").val(ui.item.referencia);
            $('#form-nuevo-pedido').formValidation('revalidateField', 'telefono_cliente');
            $('#form-nuevo-pedido').formValidation('revalidateField', 'nomb_cliente');
            $('#form-nuevo-pedido').formValidation('revalidateField', 'direccion_cliente');
            $('#form-nuevo-pedido').formValidation('revalidateField', 'referencia_cliente');
            //$('.btn-opc-nuevo-cliente').html('<button class="btn btn-info" onclick="editar_cliente('+ui.item.id_cliente+');" type="button"><i class="fa fa-user"></i></button>');
        },
        change: function(e, ui) {
            $("#documento_cliente").val('');
        }
    })
    .autocomplete( "instance" )._renderItem = function( ul, item ) {
        return $( "<li class='ui-menu-item'></li>" )
            .data( "item.autocomplete", item )
            .append( "<div class='ui-menu-item-wrapper'>" + item.nombre+"<br>Telefono/Celular: "+item.telefono+"</div>" )
            .appendTo( ul );
    };
    
    /*
    var minlength = 1;
    $("#telefono_cliente").keyup(function () {
        var that = this,
        value = $(this).val();
        if (value.length < minlength ) {
            //$("#cliente_id").val('');
            //////////////////////////////////////////////
            //$('.display-nombre').css('display','none');
            //$('.display-telefono-cliente').css('display','none');
            //$('.display-direccion-cliente').css('display','none');
            //$('.display-referencia-cliente').css('display','none');
            //$('.display-repartidor').css('display','none');
            //$("#nomb_cliente").attr('disabled','true');
            //$("#telefono_cliente").attr('disabled','true');
            //$("#direccion_cliente").attr('disabled','true');
            //$("#referencia_cliente").attr('disabled','true');
            $('#id_repartidor').selectpicker('refresh');
            $('#id_repartidor').selectpicker('val', '');
            $('#form-nuevo-pedido').formValidation('revalidateField', 'id_repartidor');
            ////////////////////////////////////////////
            $("#telefono_cliente").val('');
            $("#nomb_cliente").val('');
            $("#direccion_cliente").val('');
            $("#referencia_cliente").val('');
            $('#form-nuevo-pedido').formValidation('revalidateField', 'telefono_cliente');
            $('#form-nuevo-pedido').formValidation('revalidateField', 'nomb_cliente');
            $('#form-nuevo-pedido').formValidation('revalidateField', 'direccion_cliente');
            $('#form-nuevo-pedido').formValidation('revalidateField', 'referencia_cliente');
            //$('.btn-opc-nuevo-cliente').html('<button class="btn btn-secondary" onclick="nuevo_cliente();" type="button"><i class="fa fa-user-plus"></i></button>');
        }
    });
    */
});

var mesa_list = function(cod){
    $.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: $('#url').val()+'venta/mesa_list',
        success: function (item) {
            $("#cover-spin").hide();
            var count = 0,
                count_disponibles = 0,
                count_ocupadas = 0,
                count_pagos = 0;
                $('.list-mesas').empty();

            $.each(item['mesa'], function(i, mesa) {
    
                x = (mesa.estado == 'a') ? count_disponibles++ : 'NINGUNO';
                y = (mesa.estado == 'p') ? count_pagos++ : 'NINGUNO';
                z = (mesa.estado == 'i') ? count_ocupadas++ : 'NINGUNO';

                if($('#rol_usr').val() == 5){

                    if(mesa.id_salon == cod && mesa.estado == 'a'){
                        $('.list-mesas')
                            .append($('<button class="btn btn-green dim btn-large-dim"'
                                +'onclick="registrarMesa('+mesa.id_mesa+',\''+mesa.nro_mesa+'\',\''+mesa.desc_salon+'\');">'+mesa.nro_mesa+'<input type="radio" autocomplete="off"></button>'));
                    } else if(mesa.id_salon == cod && mesa.estado == 'p'){
                        $('.list-mesas')
                            $('.list-mesas')
                            .append($('<input type="hidden" name="tiempo_mesa[]" value="'+mesa.fecha_pedido+'"/>'
                                +'<button class="btn btn-blue dim btn-large-dim"'
                                +'onclick="orden('+mesa.id_pedido+')">'
                                +'<span class="span-a"><i class="far fa-user"></i> '+mesa.nro_personas+'</span><span>'+mesa.nro_mesa+'</span>'
                                +'<span class="span-b"><i class="ti-timer"></i>&nbsp;<span class="hora-mesa'+count+++'">'+moment(mesa.fecha_pedido).fromNow(true)+'</span></span><input type="radio" autocomplete="off"></button>'));
                    } else if(mesa.id_salon == cod && mesa.estado == 'i'){
                        $('.list-mesas')
                            .append($('<input type="hidden" name="tiempo_mesa[]" value="'+mesa.fecha_pedido+'"/>'
                                +'<button class="btn btn-reed dim btn-large-dim"'
                                +'onclick="orden('+mesa.id_pedido+')">'
                                +'<span class="span-a"><i class="far fa-user"></i> '+mesa.nro_personas+'</span><span>'+mesa.nro_mesa+'</span>'
                                +'<span class="span-b"><i class="ti-timer"></i>&nbsp;<span class="hora-mesa'+count+++'">'+moment(mesa.fecha_pedido).fromNow(true)+'</span></span><input type="radio" autocomplete="off"></button>'));
                    }

                } else{

                    if($("#rol_usr").val() == -1){
                        console.log(mesa)
                        if(mesa.id_salon == cod && mesa.estado == 'a'){
                            $('.list-mesas')
                                .append($('<button class="btn btn-green dim btn-large-dim"'
                                    +'onclick="nuevoPedidoMesa('+mesa.id_mesa+',\''+mesa.nro_mesa+'\',\''+mesa.desc_salon+'\')">'+mesa.nro_mesa+'<input type="radio" autocomplete="off"></button>'));
                        }  else if(mesa.id_salon == cod && mesa.estado == 'i' && mesa.pedido.cliente_id == $("#usuid").val()){
                            $('.list-mesas')
                                .append($('<input type="hidden" name="tiempo_mesa[]" value="'+mesa.fecha_pedido+'"/>'
                                    +'<button class="btn btn-reed dim btn-large-dim"'
                                    +'onclick="listarPedidos('+mesa.id_salon+','+mesa.id_mesa+',1,'+mesa.id_pedido+',\''+mesa.nro_mesa+'\',\''+mesa.desc_salon+'\')">'
                                    +'<span class="span-a"><i class="far fa-user"></i> '+mesa.nro_personas+'</span><span>'+mesa.nro_mesa+'</span>'
                                    +'<span class="span-b"><i class="ti-timer"></i> <span class="hora-mesa'+count+++'">'+moment(mesa.fecha_pedido).fromNow(true)+'</span></span><input type="radio" autocomplete="off"></button>'));
                        }
                    }else{
                        if(mesa.id_salon == cod && mesa.estado == 'a'){
                            $('.list-mesas')
                                .append($('<button class="btn btn-green dim btn-large-dim"'
                                    +'onclick="nuevoPedidoMesa('+mesa.id_mesa+',\''+mesa.nro_mesa+'\',\''+mesa.desc_salon+'\')">'+mesa.nro_mesa+'<input type="radio" autocomplete="off"></button>'));
                        } else if(mesa.id_salon == cod && mesa.estado == 'p'){
                            $('.list-mesas')
                                $('.list-mesas')
                                .append($('<input type="hidden" name="tiempo_mesa[]" value="'+mesa.fecha_pedido+'"/>'
                                    +'<button class="btn btn-blue dim btn-large-dim"'
                                    +'onclick="listarPedidos('+mesa.id_salon+','+mesa.id_mesa+',1,'+mesa.id_pedido+',\''+mesa.nro_mesa+'\',\''+mesa.desc_salon+'\')">'
                                    +'<span class="span-a"><i class="far fa-user"></i> '+mesa.nro_personas+'</span><span>'+mesa.nro_mesa+'</span>'
                                    +'<span class="span-b"><i class="ti-timer"></i> <span class="hora-mesa'+count+++'">'+moment(mesa.fecha_pedido).fromNow(true)+'</span></span><input type="radio" autocomplete="off"></button>'));
                        } else if(mesa.id_salon == cod && mesa.estado == 'i'){
                            $('.list-mesas')
                                .append($('<input type="hidden" name="tiempo_mesa[]" value="'+mesa.fecha_pedido+'"/>'
                                    +'<button class="btn btn-reed dim btn-large-dim"'
                                    +'onclick="listarPedidos('+mesa.id_salon+','+mesa.id_mesa+',1,'+mesa.id_pedido+',\''+mesa.nro_mesa+'\',\''+mesa.desc_salon+'\')">'
                                    +'<span class="span-a"><i class="far fa-user"></i> '+mesa.nro_personas+'</span><span>'+mesa.nro_mesa+'</span>'
                                    +'<span class="span-b"><i class="ti-timer"></i> <span class="hora-mesa'+count+++'">'+moment(mesa.fecha_pedido).fromNow(true)+'</span></span><input type="radio" autocomplete="off"></button>'));
                        }
                    }
                   
                }

            });
            $('.mesas-disponibles').attr('data-original-title',count_disponibles+' mesas disponibles');
            $('.mesas-ocupadas').attr('data-original-title',count_ocupadas+' mesas ocupadas');
            $('.mesas-pago').attr('data-original-title',count_pagos+' mesas en proceso de pago');
        },
        beforeSend:()=>{
            $("#cover-spin").show();
        }
    });
}

var tiempo_mesa = function(){
    moment.locale('es');
    $('input[name^="tiempo_mesa"]').each(function(i) {
        var fechaConvertida = moment($(this).val()).fromNow(true);
        $(".hora-mesa"+i).text(fechaConvertida);
    });
}

var mostrador = function(){
    //mostrador_list_a();
    //mostrador_list_c();
    activaTab('mostrador01');
    mostrador_list_a();
    $('.pedido-mozo').hide();
}

var mostrador_list_a = function(){
    
    function filterGlobal () {
        $('#list-mostrador-confirmacion').DataTable().search( 
            $('#search_filter_e').val()
        ).draw();
    }
    
    var count = 0;
    var table = $('#list-mostrador-confirmacion')
    .DataTable({
        "destroy": true,
        "dom": "tip",
        "bSort": true,
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"venta/mostrador_list",
            "data": {
                estado: 'a'
            }
        },        
        "columns":[
            {"data":null,
                "render": function ( data, type, row) {
                return '<a href="javascript::void(0)"><span class="round round-warning" onclick="listarPedidosDetalle(2,'+data.id_pedido+',0);">'+data.nro_pedido+'</span></a>';
            }},
            {"data": null,
                "render": function(data, type, row){
                return '<input type="hidden" name="tiempo_pedido_mostrador[]" value="'+data.fecha_pedido+'"/><i class="ti-timer"></i> <span class="tiempo-pedido-mostrador'+count+++'">'+moment(data.fecha_pedido).fromNow(true)+'</span>'
            }},
            {"data": null,
                "render": function(data, type, row){
                return '<i class="ti-user"></i> '+data.nomb_cliente;
            }},
            {"data": null,
                "className": "text-right",
                "render": function(data, type, row){
                return moneda+' '+formatNumber(data.Total.total);
            }},
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            total = api
                .rows()
                .data()
                .count();

            $('.pedidos-mostrador-total').text(total);
        }
    });
    
    $('input.search_filter_e').on( 'keyup click', function () {
        filterGlobal();
    });
}

var mostrador_list_b = function(){
    
    function filterGlobal () {
        $('#list-mostrador-preparacion').DataTable().search( 
            $('#search_filter_f').val()
        ).draw();
    }
    
    var count = 0;
    var table = $('#list-mostrador-preparacion')
    .DataTable({
        "destroy": true,
        "dom": "tip",
        "bSort": true,
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"venta/mostrador_list_c",
            "data": {
                estado: 'b'
            }
        },        
        "columns":[
            {"data":null,
                "render": function ( data, type, row) {
                return '<a href="javascript::void(0)"><span class="round round-warning" onclick="listarPedidosDetalle(2,'+data.id_pedido+','+data.id_venta+');">'+data.nro_pedido+'</span></a>';
            }},
            {"data": null,
                "render": function(data, type, row){
                return '<input type="hidden" name="tiempo_preparacion_mostrador[]" value="'+data.fecha_pedido+'"/><i class="ti-timer"></i> <span class="tiempo-preparacion-mostrador'+count+++'">'+moment(data.fecha_pedido).fromNow(true)+'</span>'
            }},
            {"data": null,
                "render": function(data, type, row){
                return '<i class="ti-user"></i> '+data.nomb_cliente;
            }},
            {"data": null,
                "render": function(data, type, row){
                //var repartidor = (data.tipo_entrega == 1) ? '<i class="fas fa-bicycle"></i> '+data.Tipopago.nombre : '-';
                if(data.id_tipo_pago == 1){
                    return '<span class="label label-success">'+data.Tipopago.nombre+'</span>';
                } else if(data.id_tipo_pago == 2){
                    return '<span class="label label-info">'+data.Tipopago.nombre+'</span>';
                } else if(data.id_tipo_pago == 3){
                    return '<span class="label label-warning">'+data.Tipopago.nombre+'</span>';
                } else if(data.id_tipo_pago == 4){
                    return '<span class="label label-danger text-primary font-bold">C</span> <span class="text-primary font-bold">'+data.Tipopago.nombre+'</span>';
                } else if(data.id_tipo_pago >= 5){
                    return '<span class="label label-light-primary">'+data.Tipopago.nombre+'</span>';
                }
            }},
            {"data": null,
                "className": "text-right",
                "render": function(data, type, row){
                return moneda+' '+formatNumber(data.total);
            }},
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            total = api
                .rows()
                .data()
                .count();

            $('.pedidos-mostrador-total').text(total);
        }
    });
    
    $('input.search_filter_f').on( 'keyup click', function () {
        filterGlobal();
    });
}

var mostrador_list_c = function(){

    function filterGlobal () {
        $('#list-mostrador-entregados').DataTable().search( 
            $('#search_filter_g').val()
        ).draw();
    }
    
    var table = $('#list-mostrador-entregados')
    .DataTable({
        "destroy": true,
        "dom": "tip",
        "bSort": true,
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"venta/mostrador_list_c",
            "data": {
                estado: 'd'
            }
        },        
        "columns":[
            {"data":null,
                "render": function ( data, type, row) {
                return '<a href="javascript::void(0)"><span class="round round-warning" onclick="listarPedidosDetalle(2,'+data.id_pedido+','+data.id_venta+');">'+data.nro_pedido+'</span></a>';
            }},
            {"data": null,
                "render": function(data, type, row){
                var fecha1 = moment(data.fecha_entrega);
                var fecha2 = moment(data.fecha_pedido);
                var duration = moment.duration(fecha2 - fecha1).humanize();
                return '<i class="ti-arrow-up text-warning"></i> '+moment(data.fecha_pedido).format('h:mm A')
                    +'<br><i class="ti-arrow-down text-success"></i> '+moment(data.fecha_entrega).format('h:mm A')
                    +'<br><i class="ti-timer"></i> '+duration;
            }},
            {"data": null,
                "render": function(data, type, row){
                return '<i class="ti-user"></i> '+data.nomb_cliente;
            }},
            {"data": null,
                "render": function(data, type, row){
                //var repartidor = (data.tipo_entrega == 1) ? '<i class="fas fa-bicycle"></i> '+data.Tipopago.nombre : '-';
                if(data.id_tipo_pago == 1){
                    return '<span class="label label-success">'+data.Tipopago.nombre+'</span>';
                } else if(data.id_tipo_pago == 2){
                    return '<span class="label label-info">'+data.Tipopago.nombre+'</span>';
                } else if(data.id_tipo_pago == 3){
                    return '<span class="label label-warning">'+data.Tipopago.nombre+'</span>';
                } else if(data.id_tipo_pago == 4){
                    return '<span class="label label-danger text-primary font-bold">C</span> <span class="text-primary font-bold">'+data.Tipopago.nombre+'</span>';
                } else if(data.id_tipo_pago >= 5){
                    return '<span class="label label-light-primary">'+data.Tipopago.nombre+'</span>';
                }
            }},
            {"data": null,
                "className": "text-right",
                "render": function(data, type, row){
                return moneda+' '+formatNumber(data.total);
            }},
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            total = api
                .rows()
                .data()
                .count();

            $('.pedidos-mostrador-total').text(total);
        }
    });
    
    $('input.search_filter_g').on( 'keyup click', function () {
        filterGlobal();
    });
}

var delivery = function(){
    activaTab('delivery01');
    delivery_list_a();
    $('.pedido-mozo').hide();
}
function portero(){
    console.log('Hola')
    activaTab('portero');
    $('.pedido-mozo').hide();
}
var count_pedido_delivery = 0;
var countNuevoPedidoDelivery = function(){
    $.ajax({     
        dataType: 'JSON',
        type: 'POST',
        url: $('#url').val()+'venta/delivery_list',
        data: {
            estado : 'a'
        },
        success: function (data){
            $.each(data, function(i, item) {
                if(parseInt(item.length) !== count_pedido_delivery){
                    count_pedido_delivery = 0;
                    if($('#codpestdelivery').val() == 1){
                        delivery_list_a();
                    }
                    var sound = new buzz.sound($('#url').val()+"public/sound/ding_ding", {
                        formats: [ "ogg", "mp3", "aac" ]
                    });
                    sound.play();
                    count_pedido_delivery = item.length + count_pedido_delivery;
                    $('.pedidos-total-1').text(count_pedido_delivery);
                }
            });
        }
    })
}

var delivery_list_a = function(){
    
    function filterGlobal () {
        $('#list-delivery-confirmacion').DataTable().search( 
            $('#search_filter_a').val()
        ).draw();
    }
    
    var count = 0;
    var table = $('#list-delivery-confirmacion')
    .DataTable({
        "destroy": true,
        "dom": "tip",
        "bSort": true,
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"venta/delivery_list",
            "data": {
                estado: 'a'
            }
        },        
        "columns":[
            {"data":null,
                "render": function ( data, type, row) {
                if(data.pedido_programado == 1){
                    return '<a href="javascript::void(0)"><span class="round round-primary" onclick="listarPedidosDetalle(3,'+data.id_pedido+',0);">'+data.nro_pedido+'</span></a>';
                } else {
                    return '<a href="javascript::void(0)"><span class="round round-warning" onclick="listarPedidosDetalle(3,'+data.id_pedido+',0);">'+data.nro_pedido+'</span></a>';
                }
            }},
            {"data": null,
                "render": function(data, type, row){
                return '<input type="hidden" name="tiempo_pedido_delivery[]" value="'+data.fecha_pedido+'"/><i class="ti-timer"></i> <span class="tiempo-pedido-delivery'+count+++'">'+moment(data.fecha_pedido).fromNow(true)+'</span>'
            }},
            {"data": null,
                "render": function(data, type, row){
                return '<i class="ti-mobile"></i> '+data.telefono_cliente+'<br><i class="ti-user"></i> '+data.nombre_cliente;
            }},
            {"data": null,
                "render": function(data, type, row){
                var tipo_entrega = (data.tipo_entrega == 1) ? '<span class="label label-primary">A DOMICILIO</span>' : '<span class="label label-inverse">POR RECOGER</span>';
                return tipo_entrega;
            }},
            {"data": null,
                "render": function(data, type, row){
                //var repartidor = (data.tipo_entrega == 1) ? '<i class="fas fa-bicycle"></i> '+data.Tipopago.nombre : '-';
                if(data.tipo_pago == 1){
                    return '<span class="label label-success">'+data.Tipopago.nombre+'</span>';
                } else if(data.tipo_pago == 2){
                    return '<span class="label label-info">'+data.Tipopago.nombre+'</span>';
                } else if(data.tipo_pago == 3){
                    return '<span class="label label-warning">'+data.Tipopago.nombre+'</span>';
                } else if(data.tipo_pago == 4){
                    return '<span class="label label-danger text-primary font-bold">C</span> <span class="text-primary font-bold">'+data.Tipopago.nombre+'</span>';
                } else if(data.tipo_pago >= 5){
                    return '<span class="label label-light-primary">'+data.Tipopago.nombre+'</span>';
                }
            }},
            {"data": null,
                "className": "text-right",
                "render": function(data, type, row){
                return moneda+' '+formatNumber(data.Total.total);
            }},
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            total = api
                .rows()
                .data()
                .count();

            $('.pedidos-total').text(total);
            $('.pedidos-total-1').text(total);
        }
    });
    
    $('input.search_filter_a').on( 'keyup click', function () {
        filterGlobal();
    });
}

var delivery_list_b = function(){

    function filterGlobal () {
        $('#list-delivery-preparacion').DataTable().search( 
            $('#search_filter_b').val()
        ).draw();
    }
    
    var count = 0;
    var table = $('#list-delivery-preparacion')
    .DataTable({
        "destroy": true,
        "dom": "tip",
        "bSort": true,
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"venta/delivery_list",
            "data": {
                estado: 'b'
            }
        },        
        "columns":[
            {"data":null,
                "render": function ( data, type, row) {
                return '<a href="javascript::void(0)"><span class="round round-warning" onclick="listarPedidosDetalle(3,'+data.id_pedido+',0);">'+data.nro_pedido+'</span></a>';
            }},
            {"data": null,
                "render": function(data, type, row){
                return '<input type="hidden" name="tiempo_preparacion_delivery[]" value="'+data.fecha_preparacion+'"/><i class="ti-timer"></i> <span class="tiempo-preparacion-delivery'+count+++'">'+moment(data.fecha_preparacion).fromNow(true)+'</span>'
            }},
            {"data": null,
                "render": function(data, type, row){
                return '<i class="ti-mobile"></i> '+data.telefono_cliente+'<br><i class="ti-user"></i> '+data.nombre_cliente;
            }},
            {"data": null,
                "render": function(data, type, row){
                var tipo_entrega = (data.tipo_entrega == 1) ? '<span class="label label-primary">A DOMICILIO</span>' : '<span class="label label-inverse">POR RECOGER</span>';
                return tipo_entrega;
            }},
            {"data": null,
                "render": function(data, type, row){
                //var repartidor = (data.tipo_entrega == 1) ? '<i class="fas fa-bicycle"></i> '+data.Tipopago.nombre : '-';
                if(data.tipo_pago == 1){
                    return '<span class="label label-success">'+data.Tipopago.nombre+'</span>';
                } else if(data.tipo_pago == 2){
                    return '<span class="label label-info">'+data.Tipopago.nombre+'</span>';
                } else if(data.tipo_pago == 3){
                    return '<span class="label label-warning">'+data.Tipopago.nombre+'</span>';
                } else if(data.tipo_pago == 4){
                    return '<span class="label label-danger text-primary font-bold">C</span> <span class="text-primary font-bold">'+data.Tipopago.nombre+'</span>';
                } else if(data.tipo_pago >= 5){
                    return '<span class="label label-light-primary">'+data.Tipopago.nombre+'</span>';
                }
            }},
            {"data": null,
                "className": "text-right",
                "render": function(data, type, row){
                return moneda+' '+formatNumber(data.Total.total);
            }},
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            total = api
                .rows()
                .data()
                .count();

            $('.pedidos-total').text(total);
        }
    });
    
    $('input.search_filter_b').on( 'keyup click', function () {
        filterGlobal();
    });
}

var delivery_list_c = function(){

    function filterGlobal () {
        $('#list-delivery-enviados').DataTable().search( 
            $('#search_filter_c').val()
        ).draw();
    }
    
    var table = $('#list-delivery-enviados')
    .DataTable({
        "destroy": true,
        "dom": "tip",
        "bSort": true,
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"venta/delivery_list_c",
            "data": {
                estado: 'c'
            }
        },        
        "columns":[
            {"data":null,
                "render": function ( data, type, row) {
                return '<a href="javascript::void(0)"><span class="round round-warning" onclick="listarPedidosDetalle(3,'+data.id_pedido+','+data.id_venta+');">'+data.nro_pedido+'</span></a>';
            }},
            {"data": null,
                "render": function(data, type, row){
                var fecha1 = moment(data.fecha_envio);
                var fecha2 = moment(data.fecha_pedido);
                var duration = moment.duration(fecha2 - fecha1).humanize();
                return '<i class="ti-arrow-up text-warning"></i> '+moment(data.fecha_pedido).format('h:mm A')
                    +'<br><i class="ti-arrow-down text-info"></i> '+moment(data.fecha_envio).format('h:mm A')
                    +'<br><i class="ti-timer"></i> '+duration;
            }},
            {"data": null,
                "render": function(data, type, row){
                return '<i class="ti-mobile"></i> '+data.telefono_cliente+'<br><i class="ti-user"></i> '+data.nombre_cliente;
            }},
            {"data": null,
                "render": function(data, type, row){
                var tipo_entrega = (data.tipo_entrega == 1) ? '<span class="label label-primary">A DOMICILIO</span>' : '<span class="label label-inverse">POR RECOGER</span>';
                return tipo_entrega;
            }},
            {"data": null,
                "render": function(data, type, row){
                //var repartidor = (data.tipo_entrega == 1) ? '<i class="fas fa-bicycle"></i> '+data.Tipopago.nombre : '-';
                if(data.tipo_pago_new == 1){
                    return '<span class="label label-success">'+data.Tipopago.nombre+'</span>';
                } else if(data.tipo_pago_new == 2){
                    return '<span class="label label-info">'+data.Tipopago.nombre+'</span>';
                } else if(data.tipo_pago_new == 3){
                    return '<span class="label label-warning">'+data.Tipopago.nombre+'</span>';
                } else if(data.tipo_pago_new == 4){
                    return '<span class="label label-danger text-primary font-bold">C</span> <span class="text-primary font-bold">'+data.Tipopago.nombre+'</span>';
                } else if(data.tipo_pago_new >= 5){
                    return '<span class="label label-light-primary">'+data.Tipopago.nombre+'</span>';
                }
            }},
            {"data": null,
                "className": "text-right",
                "render": function(data, type, row){
                return moneda+' '+formatNumber(data.total);
            }},
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            total = api
                .rows()
                .data()
                .count();

            $('.pedidos-total').text(total);
            //$('#idventa').val(idventa);
        }
    });
    
    $('input.search_filter_c').on( 'keyup click', function () {
        filterGlobal();
    });
}

var delivery_list_d = function(){

    function filterGlobal () {
        $('#list-delivery-entregados').DataTable().search( 
            $('#search_filter_d').val()
        ).draw();
    }
    
    var table = $('#list-delivery-entregados')
    .DataTable({
        "destroy": true,
        "dom": "tip",
        "bSort": true,
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"venta/delivery_list_c",
            "data": {
                estado: 'd'
            }
        },        
        "columns":[
            {"data":null,
                "render": function ( data, type, row) {
                return '<a href="javascript::void(0)"><span class="round round-warning" onclick="listarPedidosDetalle(3,'+data.id_pedido+',0);">'+data.nro_pedido+'</span></a>';
            }},
            {"data": null,
                "render": function(data, type, row){
                var fecha1 = moment(data.fecha_entrega);
                var fecha2 = moment(data.fecha_envio);
                var fecha3 = moment(data.fecha_preparacion);
                if(data.fecha_envio == '0000-00-00 00:00:00'){
                    var fecha_i = fecha3;
                } else {
                    var fecha_i = fecha2;
                }
                var duration = moment.duration(fecha_i - fecha1).humanize();
                return '<i class="ti-arrow-up text-info"></i> '+fecha_i.format('h:mm A')
                    +'<br><i class="ti-arrow-down text-success"></i> '+moment(data.fecha_entrega).format('h:mm A')
                    +'<br><i class="ti-timer"></i> '+duration;
            }},
            {"data": null,
                "render": function(data, type, row){
                return '<i class="ti-mobile"></i> '+data.telefono_cliente+'<br><i class="ti-user"></i> '+data.nombre_cliente;
            }},
            {"data": null,
                "render": function(data, type, row){
                var tipo_entrega = (data.tipo_entrega == 1) ? '<span class="label label-primary">A DOMICILIO</span>' : '<span class="label label-inverse">POR RECOGER</span>';
                return tipo_entrega;
            }},
            {"data": null,
                "render": function(data, type, row){
                //var repartidor = (data.tipo_entrega == 1) ? '<i class="fas fa-bicycle"></i> '+data.Tipopago.nombre : '-';
                if(data.tipo_pago_new == 1){
                    return '<span class="label label-success">'+data.Tipopago.nombre+'</span>';
                } else if(data.tipo_pago_new == 2){
                    return '<span class="label label-info">'+data.Tipopago.nombre+'</span>';
                } else if(data.tipo_pago_new == 3){
                    return '<span class="label label-warning">'+data.Tipopago.nombre+'</span>';
                } else if(data.tipo_pago_new == 4){
                    return '<span class="label label-danger text-primary font-bold">C</span> <span class="text-primary font-bold">'+data.Tipopago.nombre+'</span>';
                } else if(data.tipo_pago_new >= 5){
                    return '<span class="label label-light-primary">'+data.Tipopago.nombre+'</span>';
                }
            }},
            {"data": null,
                "className": "text-right",
                "render": function(data, type, row){
                return moneda+' '+formatNumber(data.total);
            }},
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            total = api
                .rows()
                .data()
                .count();

            $('.pedidos-total').text(total);
        }
    });
    
    $('input.search_filter_d').on( 'keyup click', function () {
        filterGlobal();
    });
}

var tiempo_pedido = function(){
    moment.locale('es');
    $('input[name^="tiempo_pedido_mostrador"]').each(function(i) {
        var fechaConvertida_mostrador_a = moment($(this).val()).fromNow(true);
        $(".tiempo-pedido-mostrador"+i).text(fechaConvertida_mostrador_a);
    });
    $('input[name^="tiempo_pedido_delivery"]').each(function(i) {
        var fechaConvertida_delivery_a = moment($(this).val()).fromNow(true);
        $(".tiempo-pedido-delivery"+i).text(fechaConvertida_delivery_a);
    });
}

var tiempo_preparacion = function(){
    moment.locale('es');
    $('input[name^="tiempo_preparacion_mostrador"]').each(function(i) {
        var fechaConvertida_mostrador_b = moment($(this).val()).fromNow(true);
        $(".tiempo-preparacion-mostrador"+i).text(fechaConvertida_mostrador_b);
    });
    $('input[name^="tiempo_preparacion_delivery"]').each(function(i) {
        var fechaConvertida_delivery_b = moment($(this).val()).fromNow(true);
        $(".tiempo-preparacion-delivery"+i).text(fechaConvertida_delivery_b);
    });
}

var listarPedidos = function(cod_salon,cod_mesa,id_tipo_pedido,id_pedido,mesa,salon){
    $("#pedido_seleccionado").val(id_pedido);
    $('#codsalonorigen').val(cod_salon);
    $('#codmesaorigen').val(cod_mesa);
    reset_default();
    $('.card_height').css('background','#fffde3');
    $('.card-body-right').css('display','block');
    $('.display-opciones-pedido').hide();
    if(id_tipo_pedido == 1){
        $('.pedido-numero-icono').html('');
        $('.pedido-numero').html(salon+' - Mesa: '+mesa);
    } 
    $('.cont03').css('display','block');
    $('#list-pedidos').empty();
    $.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: $('#url').val()+'venta/listarPedidos',
        data: {
            id_pedido: id_pedido,
            codpagina: $('#codpagina').val()
        },
        success: function (data) {
            var totPed = 0,
                total = 0;
            if (data.length != 0) {
                $.each(data, function(i, item) {
                    $('#nombre_mozo').val(item.nombre_mozo);
                    $('.pedido-mozo').show();
                    $('.pedido-mozo').attr('data-original-title','Mozo: '+item.nombre_mozo);
                    totPed = (item.cantidad * item.precio);
                    $('#list-pedidos')
                    .append(
                        $('<div class="d-flex flex-row comment-row comment-list" onclick="subPedido(1,'+item.id_pedido+','+item.id_pres+',\''+item.precio+'\');"/>')
                        .append('<div class="comment-text w-100 p-0 m-b-10n"><span style="display: inline-block;">'
                        +'<h6 class="m-b-5">'+item.Producto.pro_nom+' <span class="label label-warning">'+item.Producto.pro_pre+'</span></h6>'
                        +'<p class="m-b-0 font-13">'+item.cantidad+' Unidad(es) en '+moneda+' '+formatNumber(item.precio)+' | Unidad</p></span>'
                        +'<span class="price">'+moneda+' '+formatNumber(totPed)+'</span></div>'));
                    total = totPed + total;    
                });
                $('.totalPagar').html('<div class="text"><span>'+moneda+' '+formatNumber(total)+'</span>');
            } else {
                $('#list-pedidos').html('<div class="justify-center" style="height: 100%;"><div class="text-center"><h2><i class="fas fa-shopping-basket display-4" style="color: #d3d3d3;"></i></h2><h4 style="color: #d3d3d3;">Agregue productos</h4><h6 style="color: #d3d3d3;">No se encontraron productos</h6></div></div>');
                $('.totalPagar').html('<div class="text"><span>'+moneda+' '+formatNumber(total)+'</span>');
            }
        }
    });
    $('.card-footer-right').css('display','block');
    $('.btn-submit-nuevo-pedido').html('<a class="btn btn-orange" href="'+$('#url').val()+'venta/orden/'+id_pedido+'">Continuar <i class="fas fa-arrow-right"></i></a>');
}

var listarPedidosDetalle = function(cod_atencion,id_pedido,id_venta){
    reset_default();
    $('.display-delivery').hide();
    $('.card_height').css('background','#fffde3');
    $('.card-body-right').css('display','block');
    $('.pedido-numero-icono').html('<i class="ti-arrow-circle-right"></i> ');
    $('.cont04').css('display','block');
    $('#list-pedidos-detalle').empty();
    $.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: $('#url').val()+'venta/listarPedidosDetalle',
        data: {
            cod_atencion: cod_atencion,
            id_pedido: id_pedido
        },
        success: function (data) {

            $('.pedido-numero').html(data.nro_pedido);

            var totPed = 0,
                total = 0;
            
            $('.pedido-cliente').text(data.nombre_cliente); 
            if(cod_atencion == 3){
                $('.display-delivery').show();                
                if(data.tipo_entrega == 1){
                    var tipo_entrega = '<span class="label label-primary">A DOMICILIO</span>';
                    $('.display-pedido-direccion').show();
                    $('.display-pedido-referencia').show();
                    $('.display-pedido-repartidor').show();
                    if(data.tipo_pago == 4){
                        $('.display-pedido-email').show();
                    } else {
                        $('.display-pedido-email').hide();
                    }
                }else{
                    var tipo_entrega = '<span class="label label-inverse">POR RECOGER</span>';
                    $('.display-pedido-email').hide();
                    $('.display-pedido-direccion').hide();
                    $('.display-pedido-referencia').hide();
                    $('.display-pedido-repartidor').hide();
                }
                $('.pedido-telefono').text(data.telefono_cliente);
                $('.pedido-email').text(data.email_cliente);
                $('.pedido-direccion').text(data.direccion_cliente);
                $('.pedido-referencia').text(data.referencia_cliente);
                $('.pedido-tipo-entrega').html(tipo_entrega);
                $('.pedido-repartidor').text(data.desc_repartidor);

                if(data.pedido_programado == 1){
                    var hora_entrega = moment(data.hora_entrega, 'HH:mm:ss');
                    $('.pedido-hora-entrega').text(moment(hora_entrega).format('hh:mm A'));
                    $('.pedido-total-amortizado').text(moneda+' '+formatNumber(data.amortizacion));
                } else {
                    $('.display-hora-entrega').hide();
                    $('.display-pedido-total-amortizado').hide();
                }

            } else {
                $('.display-delivery').hide();
            }
            if (data.Detalle.length != 0) {
                $.each(data.Detalle, function(i, item) {
                    totPed = (item.cant * item.precio);
                    $('#list-pedidos-detalle')
                    .append(
                        $('<div class="d-flex flex-row comment-row tr-left-3" onclick="subPedido(2,'+item.id_pedido+','+item.id_pres+',\''+item.precio+'\');"/>')
                        .append('<div class="comment-text w-100 p-0 m-b-10n"><span style="display: inline-block;">'
                        +'<h6 class="m-b-5">'+item.Producto.pro_nom+' <span class="label label-warning">'+item.Producto.pro_pre+'</span></h6>'
                        +'<p class="m-b-0 font-13">'+item.cant+' Unidad(es) en '+moneda+' '+formatNumber(item.precio)+' | Unidad</p></span>'
                        +'<span class="price">'+moneda+' '+formatNumber(totPed)+'</span></div>'));
                    total = totPed + total;    
                });
                $('.pedido-total').text(moneda+' '+formatNumber(total));
                if(data.estado_pedido == 'a'){
                    if($('#codtipoped').val() == 3){
                        $('.display-opciones-pedido').show();
                        $('.opc-print-pedido').hide();
                        $('.opc-whatsapp-pedido').show();
                        $('.opc-anular-pedido').show();
                        $('.opc-whatsapp-pedido').attr('onclick','enviar_whatsapp_pago(\''+data.nro_pedido+'\',\''+total+'\',\''+data.nombre_cliente+'\',\''+data.telefono_cliente+'\');');
                        $('.opc-facturar-pedido').html('<a class="dropdown-item" target="_self" href="'+$("#url").val()+'venta/orden/'+id_pedido+'"><i class="fas fa-plus"></i> Agregar productos</a>');
                        $('.opc-anular-pedido').attr('onclick','anular_pedido('+id_pedido+');');
                        //$('.btn-submit-nuevo-pedido').html('<a class="btn btn-success" href="javascript:void(0);" onclick="editar_pedido('+id_pedido+');">CONFIRMAR</a>');
                        $('.btn-submit-nuevo-pedido').html('<a class="btn btn-success" href="javascript:void(0);" onclick="confirmar_pedido('+id_pedido+',\''+data.estado_pedido+'\');">CONFIRMAR</a>');
                        
                        if(data.tipo_entrega == 1){ //A DOMICILIO
                            $('.opc-editar-pedido').show();
                            $('.opc-editar-pedido').html('<a class="dropdown-item" href="javascript:void(0);" onclick="editar_pedido('+id_pedido+');"><i class="fas fa-edit"></i> Editar pedido</a>');
                            $('.display-pedido-repartidor-edit').show();
                            if(data.pedido_programado == 1){ // PEDIDO PROGRAMADO;
                                $('.display-entrega-programada').show();
                            } else {
                                $('.display-entrega-programada').hide();
                            }
                        } else { //RECOGER
                            $('.opc-editar-pedido').show();
                            $('.opc-editar-pedido').html('<a class="dropdown-item" href="javascript:void(0);" onclick="editar_pedido('+id_pedido+');"><i class="fas fa-edit"></i> Editar pedido</a>');
                            $('.display-pedido-repartidor-edit').hide();
                            if(data.pedido_programado == 1){ // PEDIDO PROGRAMADO
                                $('.display-entrega-programada').show();
                            } else {
                                $('.display-entrega-programada').hide();
                                $('.opc-editar-pedido').hide();
                            }
                        }

                    } else {
                        $('.display-opciones-pedido').show();
                        $('.opc-print-pedido').hide();
                        $('.opc-whatsapp-pedido').hide();
                        $('.opc-facturar-pedido').hide();
                        $('.opc-editar-pedido').hide();
                        $('.opc-anular-pedido').show();
                        $('.opc-anular-pedido').attr('onclick','anular_pedido('+id_pedido+');');
                        if($('#rol_usr').val() != 5){
                            $('.btn-submit-nuevo-pedido').html('<a class="btn btn-success" target="_self" href="'+$("#url").val()+'venta/orden/'+id_pedido+'"><i class="mdi mdi-receipt"></i> COBRAR</a>');
                        } else {
                            $('.btn-submit-nuevo-pedido').html('');
                        }
                    }                    
                    $('.card-footer-right').css('display','block');
                    $('.display-pedido-monto').show();
                } else if(data.estado_pedido == 'b'){
                    $('.display-opciones-pedido').show();
                    $('.opc-print-pedido').hide();
                    $('.opc-whatsapp-pedido').hide();
                    $('.opc-facturar-pedido').hide();
                    if($('#codtipoped').val() == 3){

                        if(data.tipo_entrega == 1){ //A DOMICILIO
                            $('.opc-editar-pedido').show();
                            $('.opc-editar-pedido').html('<a class="dropdown-item" href="javascript:void(0);" onclick="editar_pedido('+id_pedido+');"><i class="fas fa-edit"></i> Editar pedido</a>');
                            $('.display-pedido-repartidor-edit').show();
                            if(data.pedido_programado == 1){ // PEDIDO PROGRAMADO;
                                $('.display-entrega-programada').show();
                            } else {
                                $('.display-entrega-programada').hide();
                            }
                        } else { //RECOGER
                            $('.opc-editar-pedido').show();
                            $('.opc-editar-pedido').html('<a class="dropdown-item" href="javascript:void(0);" onclick="editar_pedido('+id_pedido+');"><i class="fas fa-edit"></i> Editar pedido</a>');
                            $('.display-pedido-repartidor-edit').hide();
                            if(data.pedido_programado == 1){ // PEDIDO PROGRAMADO
                                $('.display-entrega-programada').show();
                            } else {
                                $('.display-entrega-programada').hide();
                                $('.opc-editar-pedido').hide();
                            }
                        }

                        $('.opc-anular-pedido').show();
                        $('.opc-anular-pedido').attr('onclick','anular_pedido('+id_pedido+');');
                    
                    } else {
                        $('.opc-editar-pedido').hide();
                        $('.opc-print-pedido').show();
                        $('.opc-print-pedido').html('<a class="dropdown-item" href="javascript:void(0);" onclick="impresion_ticket('+id_pedido+');"><i class="fas fa-print"></i> Ticket cliente</a>');
                        $('.opc-anular-pedido').show();
                        $('.opc-anular-pedido').attr('onclick','anular_venta('+id_pedido+',\''+data.nro_pedido+'\','+id_venta+');');
                    }

                    if(cod_atencion == 2){
                        $('.btn-submit-nuevo-pedido').html('<a class="btn btn-success" href="javascript:void(0);" onclick="pedidoAccion(3,\'b\','+data.id_pedido+');">ENTREGADO</a>');
                    }else{
                        if($('#rol_usr').val() != 5){
                            $('.btn-submit-nuevo-pedido').html('<a class="btn btn-success" target="_self" href="'+$("#url").val()+'venta/orden/'+id_pedido+'"><i class="mdi mdi-receipt"></i> COBRAR</a>');       
                        } else {
                            $('.btn-submit-nuevo-pedido').html('');
                        }
                    }
                    $('.card-footer-right').css('display','block');
                    $('.display-pedido-monto').hide();
                } else if(data.estado_pedido == 'c'){
                    $('.display-opciones-pedido').show();
                    $('.opc-whatsapp-pedido').hide();
                    $('.opc-editar-pedido').show();
                    $('.opc-editar-pedido').html('<a class="dropdown-item" href="javascript:void(0);" onclick="editar_venta_pago('+id_venta+');"><i class="fas fa-edit"></i> Editar venta</a>');
                        $('.display-entrega-programada').hide();
                        $('.display-pedido-repartidor-edit').hide();
                        $('.display-paga-con').hide();
                        $('.display-comision-delivery').hide();
                    $('.opc-anular-pedido').show();
                    $('.opc-anular-pedido').attr('onclick','anular_venta('+id_pedido+',\''+data.nro_pedido+'\','+id_venta+');');
                    $('.opc-print-pedido').show();
                    $('.opc-print-pedido').html('<a class="dropdown-item" href="'+$("#url").val()+'venta/impresion_reparto/'+id_venta+'" target="_blank"><i class="fas fa-print"></i> Ticket reparto</a>');
                    $('.opc-facturar-pedido').html('');
                    $('.btn-submit-nuevo-pedido').html('<a class="btn btn-success" href="javascript:void(0);" onclick="pedidoAccion(2,\'c\','+data.id_pedido+');">ENTREGADO</a>');
                    //$('.btn-submit-nuevo-pedido').html('<a class="btn btn-success" target="_self" href="'+$("#url").val()+'venta/orden/'+id_pedido+'"><i class="mdi mdi-receipt"></i> FACTURAR</a>');
                    $('.card-footer-right').css('display','block');
                    $('.display-pedido-pago').hide();
                    $('.display-pedido-monto').hide();
                    $('.display-pedido-total-amortizado').hide();
                } else if(data.estado_pedido == 'd'){
                    $('.display-opciones-pedido').hide();
                    $('.btn-submit-nuevo-pedido').html('');
                    $('.card-footer-right').css('display','none');
                    $('.display-pedido-pago').hide();
                    $('.display-pedido-monto').hide();
                    $('.display-pedido-total-amortizado').hide();
                }
            } else {
                $('.pedido-total').text(moneda+' 0.00');
                $('.card-footer-right').css('display','none');
                $('.display-opciones-pedido').hide();
                $('.totalPagar').html('');
                $('.btn-submit-nuevo-pedido').html('');
                $('#list-pedidos-detalle').html('<div class="justify-center m-t-40" style="height: 100%;"><div class="text-center"><h2><i class="fas fa-shopping-basket display-4" style="color: #d3d3d3;"></i></h2><h4 style="color: #d3d3d3;"><a href="'+$("#url").val()+'venta/orden/'+id_pedido+'" class="link">Agregue productos</a></h4><h6 style="color: #d3d3d3;">No se encontraron productos</h6></div></div>');    
            }
        }
    });
}

var pedidoAccion = function(cod_accion,estado_pedido,id_pedido){
    /* cod_accion
        1 = enviar delivery, 2 = entregado delivery, 3 = entregado mostrador
    */
    if(cod_accion == 1){
        var accion = 'PREPARADO';
    }else if(cod_accion == 2 || cod_accion == 3){
        var accion = 'ENTREGADO';
    }
    var html_confirm = '<div><br>\
        <div style="width: 100% !important; float: none !important;">\
            <table class="table m-b-0">\
                <tr><td class="text-left">Pedido: </td><td class="text-right">'+$('.pedido-numero').text()+'</td></tr>\
            </table>\
        </div><br>\
        Ingrese código de vaucher</div>\
        <form><input class="form-control text-center w-100" type="text" id="codigo_vaucher" autocomplete="off"/></form><br>\
        <div><span class="text-success" style="font-size: 17px;">¿Está Usted de Acuerdo?</span></div>';

    Swal.fire({
        title: 'Necesitamos de tu Confirmación',
        html: html_confirm,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#34d16e',
        confirmButtonText: 'Si, Adelante!',
        cancelButtonText: "No!",
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                    url: $('#url').val()+'venta/pedidoAccion',
                    type: 'POST',
                    data: {
                        cod_accion : cod_accion,
                        id_pedido : id_pedido,
                        codigo_operacion : $("#codigo_vaucher").val()
                    },
                    dataType: 'json'
                })
                .done(function(response){
                    Swal.fire({
                        title: 'Proceso Terminado',
                        text: 'El pedido ha sido '+accion,
                        icon: 'success',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "Aceptar"
                    });
                    reset_default();
                    $('.cont01').css('display','flex');
                    if(cod_accion == 1 || cod_accion == 2){
                        if(estado_pedido == 'b'){
                            delivery_list_b();
                        }else if(estado_pedido == 'c'){
                            delivery_list_c();
                        }
                    } else if(cod_accion == 3){
                        mostrador();
                    }
                })
                .fail(function(){
                    Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
                });
            });
        },
        allowOutsideClick: false              
    });
}

var registrarMesa = function(id_mesa,nro_mesa,salon){
    // var html_confirm = '<div>Se procederá aperturar la siguiente mesa:</div><br>\
    // <table class="table m-b-0">\
    // <tr><td class="text-left">Salon: </td><td class="text-right">'+salon+'</td></tr>\
    // <tr><td class="text-left">Mesa: </td><td class="text-right">'+nro_mesa+'</td></tr>\
    // </table>\
    // <input class="form-control  text-center" type="hidden" value="1" id="nro_personas"/><br>\
    // <div><span class="text-success" style="font-size: 17px;">¿Está Usted de Acuerdo?</span></div>';
    // var html_confirm = '<div>Se procederá aperturar la siguiente mesa:</div><br>\
    // <table class="table m-b-0">\
    // <tr><td class="text-left">Salon: </td><td class="text-right">'+salon+'</td></tr>\
    // <tr><td class="text-left">Mesa: </td><td class="text-right">'+nro_mesa+'</td></tr>\
    // </table>\
    // <label>Nro personas</label>\
    // <input class="form-control numero-personas text-center" type="hidden" value="1" id="nro_personas"/><br>\
    // <div><span class="text-success" style="font-size: 17px;">¿Está Usted de Acuerdo?</span></div>';
    // Swal.fire({
    //     title: 'Necesitamos de tu Confirmación',
    //     html: html_confirm,
    //     icon: 'warning',
    //     showCancelButton: true,
    //     confirmButtonColor: '#34d16e',
    //     confirmButtonText: 'Si, Adelante!',
    //     cancelButtonText: "No!",
    //     showLoaderOnConfirm: true,
    //     preConfirm: function() {
    //         return new Promise(function(resolve) {
                $.ajax({
                    url: $('#url').val()+'venta/pedido_create/pc1',
                    type: 'POST',
                    data: {
                        id_mesa: id_mesa,
                        nomb_cliente: 'Mesa '+nro_mesa,
                        nro_personas: 1,
                    },
                    dataType: 'json',
                    success: function (data) {
                        if(data['fil'] == 1){
                            window.open($("#url").val()+'venta/orden/'+data['id_pedido'],'_self');
                        }else{
                            window.open($("#url").val()+'venta','_self');
                        }
                    }
                })
                .fail(function(){
                    Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
                });
    //         });
    //     },
    //     allowOutsideClick: false              
    // });
    $('.numero-personas').TouchSpin({
        buttondown_class: "btn btn-secondary",
        buttonup_class: "btn btn-secondary",
        min: 1,
        mousewheel: false
    });
}

var orden = function(id_pedido){
    window.open($("#url").val()+'venta/orden/'+id_pedido,'_self');
}

var list_categorias_menu = function(){
    $('.categoriamenu').css('display','block');
    $('#categoriamenu').css('display','none');
    $('#modal-lista-menu').modal('show');
    $('.list_categorias_menu').empty();
    $.ajax({
        type: "POST",
        url: $('#url').val()+"venta/menu_categoria_list",
        dataType: "json",
        success: function(item){
            if(item.data.length > 0){
                $.each(item.data, function(i, item) {
                    var categoria_nombre = (item.descripcion).substr(0,1).toUpperCase()+(item.descripcion).substr(1).toLowerCase();
                    $('.list_categorias_menu')
                        .append(
                        $('<li class="nav-item m-t-10"/>')
                            .html('<a class="nav-link bg-header" data-toggle="tab" href="#categoriamenu" onclick="list_platos_menu('+item.id_catg+');" role="tab">'
                                +'<span class="hidden-sm-up">'+categoria_nombre+'</span>'
                                +'<span class="hidden-xs-down font-14">'+categoria_nombre+'</span> </a>')
                        )
                });
            }else{
                $('.list_categorias_menu').html("");
            }
        }
    });
}

var list_platos_menu = function(id_catg){

    $('.categoriamenu').css('display','none');
    $('#categoriamenu').css('display','block');
    
    function filterGlobal () {
        $('#list_platos_menu').DataTable().search( 
            $('#search_filter_menu').val()
        ).draw();
    }
    
    var count = 0;
    var table = $('#list_platos_menu')
    .DataTable({
        "destroy": true,
        "dom": "tp",
        "bSort": false,
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"venta/menu_plato_list",
            "data": {
                id_catg: id_catg
            }
        },        
        "columns":[
            {"data":null,
                "render": function ( data, type, row) {
                var pro_nom = (data.pro_nom).substr(0,1).toUpperCase()+(data.pro_nom).substr(1).toLowerCase();
                var pro_pre = (data.pro_pre).substr(0,1).toUpperCase()+(data.pro_pre).substr(1).toLowerCase();
                return pro_nom+' | '+pro_pre+' | '+$('#moneda').val()+' '+data.pro_cos;
            }},
            {"data": null,
                "render": function(data, type, row){
                var checked = (data.est_c == 'a') ? 'checked' : '';
                return '<div class="switch text-right" onclick="estado_plato_menu('+data.id_catg+','+data.id_pres+',\''+data.est_c+'\');"><label class="m-b-0"><input type="checkbox" '+checked+'><span class="lever switch-col-light-green"></span></label></div>';
            }}
        ]
    });
    
    $('input.search_filter_menu').on( 'keyup click', function () {
        filterGlobal();
    });
}

var estado_plato_menu = function(id_catg,id_pres,estado){
    $.ajax({
        type: "POST",
        url: $('#url').val()+"venta/menu_plato_estado",
        dataType: "json",
        data: {
            id_pres : id_pres,
            estado : estado
        },
        success: function(request){
            list_platos_menu(id_catg);
        }
    });
}

var nuevoPedidoMesa = function(id_mesa,mesa,salon){
    $('#codsalonorigen').val('0');
    $('#codmesaorigen').val('0');
    $('.id-mesa').val(id_mesa);
    //////////////////////////////////////
    reset_default();
    $('.display-mozo').css('display','block');
    $("#id_mozo").removeAttr('disabled');
    $('#id_mozo').selectpicker('refresh');
    $('#id_mozo').selectpicker('val', '');
    $("#nomb_cliente").removeAttr('disabled');
    $('.pedido-mozo').hide();
    $('.display-personas').css('display','block');
    $('.numero-personas').TouchSpin({
        buttondown_class: "btn btn-link text-success",
        buttonup_class: "btn btn-link text-success",
        min: 1,
        mousewheel: false
    });
    ///////////////////////////////////////////   
    // $('.card_height').css('background','#fffde3');
    $('.card_height').css('background','#ffffff');
    $('.card-body-right').css('display','block');
    $('.cont02').css('display','block');
    $('.pedido-numero-icono').html('');
    $('.pedido-numero').html(salon+' - Mesa: '+mesa);
    $('.card-footer-right').css('display','block');
    $('.display-opciones-pedido').hide();
    /////////////////////////////////////////////
    $('#form-nuevo-pedido').data('formValidation').resetForm($('#form-nuevo-pedido'));
    $("#nomb_cliente").val('Mesa: '+mesa);
    $('.btn-submit-nuevo-pedido').html('<button type="submit" class="btn btn-orange btn-submit-nuevo-pedido">Continuar <i class="fas fa-arrow-right"></i></button>');
}

var tab01 = function(){
    $('.topbar').css('width','100%');
    $('#form-nuevo-pedido').attr('action','venta/pedido_create/pc1');
    $('.display-estado-mesa').css('display','block');
    $('input[name^="codtipoped"]').val(1);
    /////////////////////
    reset_default();
    $('.cont01').css('display','flex');
    $('.cont01-1').css('display','block');
    $('.cont01-2').css('display','none');
    $('.nomPed').html('una mesa');
    $('.dim').removeClass('active');
    $('#codpestdelivery').val(0);
}

var tab02 =  function(){
    $('.topbar').css('width','100%');
    $('#form-nuevo-pedido').attr('action','venta/pedido_create/pc2');
    $('.display-estado-mesa').css('display','none');
    $('input[name^="codtipoped"]').val(2);
    /////////////////////////////
    reset_default();
    $('.cont01').css('display','flex');
    $('.cont01-1').css('display','none');
    $('.cont01-2').css('display','block');
    $('.nomPed').html('un pedido');
    $('#codpestdelivery').val(0);
}

var tab03 = function(){
    $('.topbar').css('width','100%');
    $('#form-nuevo-pedido').attr('action','venta/pedido_create/pc3');
    $('.display-estado-mesa').css('display','none');
    $('input[name^="codtipoped"]').val(3);
    ///////////////////////
    reset_default();
    $('.cont01').css('display','flex');
    $('.cont01-1').css('display','none');
    $('.cont01-2').css('display','block');
    $('.nomPed').html('un pedido');
    $('#codpestdelivery').val(1);
    //$('#cliente_id').val('');
}

$('.tab01,.list-salones').click( function() {
    tab01();
});

$('.tab02').click( function() {
    tab02();
});

$('.tab03').click( function() {
    tab03();
});

$('.btn-nuevo-pedido').click( function() {
    reset_default();
    $('.card_height').css('background','#fffde3');
    //$('.card_height').css('background','#FFFFF0');
    $('.card-body-right').css('display','block');
    $('.pedido-numero-icono').html('');
    $('.pedido-numero').html('Nuevo pedido:');
    $('.cont02').css('display','block');
    $('#form-nuevo-pedido').data('formValidation').resetForm($('#form-nuevo-pedido'));
    if($('#codtipoped').val() == 2){
        $('.display-nombre').css('display','block');
        $("#nomb_cliente").removeAttr('disabled');
        $('#form-nuevo-pedido').formValidation('revalidateField', 'nomb_cliente');
    } else if($('#codtipoped').val() == 3){
        //$('#cliente_id').val('');
        $('.display-pedido-programado').css('display','block');
        $('#tipo_entrega_1').prop('checked', true);
        $('#tipo_entrega_2').prop('checked', false);
        $('.display-tipo-entrega').css('display','block');
        $('.btn-tipo-entrega-1').addClass('active');
        $('.btn-tipo-entrega-2').removeClass('active');
        $('.display-telefono-cliente').css('display','block');
        $("#telefono_cliente").removeAttr('disabled');
        $('.display-nombre').css('display','block');
        $("#nomb_cliente").removeAttr('disabled');
        $('.display-direccion-cliente').css('display','block');
        $("#direccion_cliente").removeAttr('disabled');
        $('.display-referencia-cliente').css('display','block');
        $("#referencia_cliente").removeAttr('disabled');
        $('.display-repartidor').css('display','block');
        $("#id_repartidor").removeAttr('disabled');
        $('#id_repartidor').selectpicker('refresh');
        $('#id_repartidor').selectpicker('val', '');
        $('#form-nuevo-pedido').formValidation('revalidateField', 'id_repartidor');
        $('#form-nuevo-pedido').formValidation('revalidateField', 'telefono_cliente');
        $('#form-nuevo-pedido').formValidation('revalidateField', 'nomb_cliente');
        $('#form-nuevo-pedido').formValidation('revalidateField', 'direccion_cliente');
        $('#form-nuevo-pedido').formValidation('revalidateField', 'referencia_cliente');
        //$('.btn-opc-nuevo-cliente').html('<button class="btn btn-secondary" onclick="nuevo_cliente();" type="button"><i class="fa fa-user-plus"></i></button>');
    }
    $('.card-footer-right').css('display','block');
    $('.btn-submit-nuevo-pedido').html('<button type="submit" class="btn btn-orange btn-submit-nuevo-pedido">Continuar <i class="fas fa-arrow-right"></i></button>');
    $('.display-opciones-pedido').hide();
});

$('.btn-cancelar-pedido').click( function(){
    if($('#codtipoped').val() == 1){
        tab01();
    } else if($('#codtipoped').val() == 2){
        tab02();
    } else if($('#codtipoped').val() == 3){
        tab03();
    }
    /*
    $('#form-nuevo-pedido').data('formValidation').resetForm($('#form-nuevo-pedido'));
    */
});

$('.btn-tipo-entrega-1').on('click', function(event){
    $('.display-nombre').css('display','block');
    $("#nomb_cliente").prop('disabled', false);
    $('.display-direccion-cliente').css('display','block');
    $("#direccion_cliente").prop('disabled', false);
    $('.display-referencia-cliente').css('display','block');
    $("#referencia_cliente").prop('disabled', false);
    $('.display-repartidor').css('display','block');
    $("#id_repartidor").prop('disabled', false);
    $('#id_repartidor').selectpicker('refresh');
    $('#id_repartidor').selectpicker('val', '');
    $('#form-nuevo-pedido').formValidation('revalidateField', 'id_repartidor');
    $('#form-nuevo-pedido').formValidation('revalidateField', 'telefono_cliente');
    $('#form-nuevo-pedido').formValidation('revalidateField', 'nomb_cliente');
    $('#form-nuevo-pedido').formValidation('revalidateField', 'direccion_cliente');
    $('#form-nuevo-pedido').formValidation('revalidateField', 'referencia_cliente');
});

$('.btn-tipo-entrega-2').on('click', function(event){
    $('.display-nombre').css('display','block');
    $("#nomb_cliente").prop('disabled', false);
    $('.display-direccion-cliente').css('display','none');
    $("#direccion_cliente").prop('disabled', true);
    $('.display-referencia-cliente').css('display','none');
    $("#referencia_cliente").prop('disabled', true);
    $('.display-repartidor').css('display','none');
    $("#id_repartidor").prop('disabled', true);
    $('#form-nuevo-pedido').formValidation('revalidateField', 'telefono_cliente');
    $('#form-nuevo-pedido').formValidation('revalidateField', 'nomb_cliente');
});

/* INICIO - OPCION DE MESAS */
/* 1.- OPCION CAMBIAR MESA  */
/* Combo mesa origen */
var comboMesaOrigenOpc01 = function(cod_salon_origen_opc01){
    $('#cod_mesa_origen_opc01').find('option').remove();
    $.ajax({
        type: "POST",
        url: $('#url').val()+"venta/ComboMesaOri",
        data: {
            cod_salon_origen: cod_salon_origen_opc01
        },
        success: function (response) {
            $('#cod_mesa_origen_opc01').html(response);
            $('#cod_mesa_origen_opc01').selectpicker();
            $('#cod_mesa_origen_opc01').selectpicker('refresh');
            $('#cod_mesa_origen_opc01').val($('#codmesaorigen').val()).selectpicker('refresh');
        },
        error: function () {
            $('#cod_mesa_origen_opc01').html('There was an error!');
        }
    });
}

/* Combo mesa destino */
var comboMesaDestinoOpc01 = function(cod_salon_destino_opc01){
    $('#cod_mesa_destino_opc01').find('option').remove();
    $.ajax({
        type: "POST",
        url: $('#url').val()+"venta/ComboMesaDes",
        data: {
            cod_salon_destino: cod_salon_destino_opc01,
            estado: 'a'
        },
        success: function (response) {
            $('#cod_mesa_destino_opc01').html(response);
            $('#cod_mesa_destino_opc01').selectpicker();
            $('#cod_mesa_destino_opc01').selectpicker('refresh');
        },
        error: function () {
            $('#cod_mesa_destino_opc01').html('There was an error!');
        }
    });
}

/* Combo salon origen */
$('#cod_salon_origen_opc01').change( function() {
    var cod_salon_origen_opc01 = $('#cod_salon_origen_opc01').val();
    comboMesaOrigenOpc01(cod_salon_origen_opc01);
    $('#form-cambiar-mesa').formValidation('revalidateField', 'cod_mesa_origen_opc01');
});

/* Combo salon destino */
$('#cod_salon_destino_opc01').change( function() {
    var cod_salon_destino_opc01 = $('#cod_salon_destino_opc01').val();
    comboMesaDestinoOpc01(cod_salon_destino_opc01);
});

/* Boton cambiar mesa */
$('.opc-cambiar-mesa').click( function() {
    $('#cod_salon_origen_opc01').val($('#codsalonorigen').val()).selectpicker('refresh');
    var cod_salon_destino_opc01 = $('#cod_salon_destino_opc01').val();
    comboMesaOrigenOpc01($('#codsalonorigen').val());
    comboMesaDestinoOpc01(cod_salon_destino_opc01);
});

$('#modal-cambiar-mesa').on('hidden.bs.modal', function() {
    $(this).find('form')[0].reset();
    $('#form-cambiar-mesa').formValidation('resetForm', true);
    $('#cod_salon_origen_opc01').val('').selectpicker('refresh');
    $('#cod_salon_destino_opc01').val('').selectpicker('refresh');
});
/* OPCION CAMBIAR MESA  */

/* 2.- OPCION MOVER PEDIDOS  */
/* Combo mesa origen */
var comboMesaOrigenOpc02 = function(cod_salon_origen_opc02){
    $('#cod_mesa_origen_opc02').find('option').remove();
    $.ajax({
        type: "POST",
        url: $('#url').val()+"venta/ComboMesaOri",
        data: {
            cod_salon_origen: cod_salon_origen_opc02
        },
        success: function (response) {
            $('#cod_mesa_origen_opc02').html(response);
            $('#cod_mesa_origen_opc02').selectpicker();
            $('#cod_mesa_origen_opc02').selectpicker('refresh');
            $('#cod_mesa_origen_opc02').val($('#codmesaorigen').val()).selectpicker('refresh');
        },
        error: function () {
            $('#cod_mesa_origen_opc02').html('There was an error!');
        }
    });
}

/* Combo mesa destino */
var comboMesaDestinoOpc02 = function(cod_salon_destino_opc02){
    $('#cod_mesa_destino_opc02').find('option').remove();
    $.ajax({
        type: "POST",
        url: $('#url').val()+"venta/ComboMesaDes",
        data: {
            cod_salon_destino: cod_salon_destino_opc02,
            estado: 'i'
        },
        success: function (response) {
            $('#cod_mesa_destino_opc02').html(response);
            $('#cod_mesa_destino_opc02').selectpicker();
            $('#cod_mesa_destino_opc02').selectpicker('refresh');
        },
        error: function () {
            $('#cod_mesa_destino_opc02').html('There was an error!');
        }
    });
}

/* Combo salon origen */
$('#cod_salon_origen_opc02').change( function() {
    var cod_salon_origen_opc02 = $('#cod_salon_origen_opc02').val();
    comboMesaOrigenOpc02(cod_salon_origen_opc02);
    $('#form-mover-pedidos').formValidation('revalidateField', 'cod_mesa_origen_opc02');
});

/* Combo salon destino */
$('#cod_salon_destino_opc02').change( function() {
    var cod_salon_destino_opc02 = $('#cod_salon_destino_opc02').val();
    comboMesaDestinoOpc02(cod_salon_destino_opc02);
});

/* Boton cambiar mesa */
$('.opc-mover-pedidos').click( function() {
    $('#cod_salon_origen_opc02').val($('#codsalonorigen').val()).selectpicker('refresh');
    var cod_salon_destino_opc02 = $('#cod_salon_destino_opc02').val();
    comboMesaOrigenOpc02($('#codsalonorigen').val());
    comboMesaDestinoOpc02(cod_salon_destino_opc02);
});

$('#modal-mover-pedidos').on('hidden.bs.modal', function() {
    $(this).find('form')[0].reset();
    $('#form-mover-pedidos').formValidation('resetForm', true);
    $('#cod_salon_origen_opc02').val('').selectpicker('refresh');
    $('#cod_salon_destino_opc02').val('').selectpicker('refresh');
});
/* OPCION MOVER PEDIDOS  */

/* FIN OPCION DE MESAS */

/************  */

var reset_default = function(){
    $('.cont01').css('display','none');
    $('.cont02').css('display','none');
    $('.cont03').css('display','none');
    $('.cont04').css('display','none');
    $('.card-body-right').css('display','none');
    $('.card-footer-right').css('display','none');
    $('.card_height').css('background','#666');
    $('.totalPagar').html('');
    $('.pedido-numero-icono').html('');
    $('.pedido-numero').html('Detalle:');
    // NONE DISPLAY
    $('.display-pedido-programado').css('display','none');
    $('.pedido_programado').prop('checked', false);
    $('.pedido_programado').val('0');
    $('.display-tipo-entrega').css('display','none');
    $('.display-hora-entrega').css('display','none');
    $('.display-pedido-total-amortizado').css('display','none');
    $('.display-busqueda-cliente').css('display','none');
    $('.display-nombre').css('display','none');
    $('.display-telefono-cliente').css('display','none');
    $('.display-direccion-cliente').css('display','none');
    $('.display-referencia-cliente').css('display','none');
    $('.display-repartidor').css('display','none');
    $('.display-personas').css('display','none');
    $('.display-mozo').css('display','none');
    //DISABLED INPUT
    $("#hora_entrega").attr('disabled','true');
    $("#nomb_cliente").attr('disabled','true');
    $("#telefono_cliente").attr('disabled','true');
    $("#direccion_cliente").attr('disabled','true');
    $("#referencia_cliente").attr('disabled','true');
    $("#id_repartidor").attr('disabled','true');
    $("#id_mozo").attr('disabled','true');
}

$('.mostrador01').click( function() {
    mostrador_list_a();
    reset_default();
    $('.cont01').css('display','flex');
});

$('.mostrador02').click( function() {
    mostrador_list_b();
    reset_default();
    $('.cont01').css('display','flex');
});

$('.mostrador03').click( function() {
    mostrador_list_c();
    reset_default();
    $('.cont01').css('display','flex');
});

$('.delivery01').click( function() {
    delivery_list_a();
    reset_default();
    $('.cont01').css('display','flex');
    $('#codpestdelivery').val(1);
});

$('.delivery02').click( function() {
    delivery_list_b();
    reset_default();
    $('.cont01').css('display','flex');
    $('#codpestdelivery').val(0);
});

$('.delivery03').click( function() {
    delivery_list_c();
    reset_default();
    $('.cont01').css('display','flex');
    $('#codpestdelivery').val(0);
});

$('.delivery04').click( function() {
    delivery_list_d();
    reset_default();
    $('.cont01').css('display','flex');
    $('#codpestdelivery').val(0);
});

/****   *///

/* Link de pago whatsapp */
var enviar_whatsapp_pago = function(nro_pedido,total_pedido,cliente,telefono){
    var html_confirm = '<div>Enviar mensaje:</div>\
    <div class="font-18 font-bold">Pedido N° '+nro_pedido+'</div><br>\
    <div><form><textarea id="textarea" class="form-control" rows="8">Hola '+cliente+'!\
    \nYa aceptamos tu solicitud de tu pedido por un monto de '+$('#moneda').val()+' '+formatNumber(total_pedido)+'.\
    \nGracias por confirar en nosotros</textarea></div>\
    <div><span class="text-success" style="font-size: 17px;">¿Está Usted de Acuerdo?</span></div>';
    Swal.fire({
        title: '',
        html: html_confirm,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#34d16e',
        confirmButtonText: 'Si, Adelante!',
        cancelButtonText: "No!",
        showLoaderOnConfirm: true,
        preConfirm: function() {
            window.open('https://api.whatsapp.com/send?phone=51'+telefono+'&text='+$('#textarea').val() ,'_blank');
        }            
    });
}

var anular_pedido = function(id_pedido){
    var html_confirm = '<div>Se procederá a anular este pedido</div><br>\
    <div><span class="text-success" style="font-size: 17px;">¿Está Usted de Acuerdo?</span></div>';
    Swal.fire({
        title: 'Necesitamos de tu Confirmación',
        html: html_confirm,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#34d16e',
        confirmButtonText: 'Si, Adelante!',
        cancelButtonText: "No!",
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                    url: $('#url').val()+'venta/anular_pedido',
                    type: 'POST',
                    data: {
                        id_pedido : id_pedido,
                        tipo_pedido : $("#codtipoped").val()
                    },
                    dataType: 'json'
                })
                .done(function(response){
                    window.open($("#url").val()+'venta','_self');
                })
                .fail(function(){
                    Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
                });
            });
        },
        allowOutsideClick: false              
    });
}

var anular_venta = function(id_pedido,nro_pedido,id_venta){
    var html_confirm = '<div>Se procederá a anular el siguiente pedido:</div>\
    <div class="font-18 font-bold">Pedido N° '+nro_pedido+'</div><br>\
    Ingrese código de seguridad</div><br>\
    <form><input class="form-control text-center w-50" type="password" id="codigo_anular_venta_" autocomplete="off"/></form><br>\
    <div><span class="text-success" style="font-size: 17px;">¿Está Usted de Acuerdo?</span></div>';
    Swal.fire({
        title: 'Necesitamos de tu Confirmación',
        html: html_confirm,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#34d16e',
        confirmButtonText: 'Si, Adelante!',
        cancelButtonText: "No!",
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return new Promise(function(resolve) {
                if($('#codigo_anular_venta').val() == $('#codigo_anular_venta_').val()){
                    $.ajax({
                        url: $('#url').val()+'venta/anular_venta',
                        type: 'POST',
                        data: {
                            id_venta : id_venta,
                            id_pedido : id_pedido
                        },
                        dataType: 'json'
                    })
                    .done(function(response){
                        window.open($("#url").val()+'venta','_self');
                    })
                    .fail(function(){
                        Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
                    });
                } else {
                    Swal.fire({
                        title: 'Proceso No Culminado',
                        text: 'El código ingresado es incorrecto',
                        icon: 'error',
                        confirmButtonColor: '#34d16e',
                        confirmButtonText: "Aceptar"
                    });
                }
            });
        }            
    });
}

var editar_pedido = function(id_pedido){
    $('#modal-editar-pedido').modal('show');
    $.ajax({
      type: "post",
      dataType: "json",
      data: {
          id_pedido: id_pedido
      },
      url: $('#url').val()+'venta/pedido_edit',
        success: function (item){
            $.each(item.data, function(i, campo) {
                $('#id_pedido').val(id_pedido);
                $('#id_repartidor_edit').selectpicker('val', campo.id_repartidor);
                $('#hora_entrega_edit').selectpicker('val', campo.hora_entrega);
                $('#amortizacion').val(campo.amortizacion);
                $('#id_tipo_pago').selectpicker('val', campo.tipo_pago);
                $('#paga_con').val(campo.paga_con);
                $('#comision_delivery').val(campo.comision_delivery);
                if(campo.tipo_pago == 1){
                    $('.display-paga-con').show();
                } else {
                    $('.display-paga-con').hide();
                }
            });
        }
    });
}

$("#id_tipo_pago").change(function() {
    if(this.value == 1){
        $('.display-paga-con').show();
    } else {
        $('.display-paga-con').hide();
    }
});

var editar_venta_pago = function(id_venta){
    $('#modal-editar-venta-pago').modal('show');
    $.ajax({
      type: "post",
      dataType: "json",
      data: {
          id_venta: id_venta
      },
      url: $('#url').val()+'venta/venta_edit',
        success: function (item){
            $.each(item.data, function(i, campo) {
                $('#id_pedido').val(campo.id_pedido);
                $('#id_venta').val(campo.id_venta);
                $('#id_venta_tipopago').val(campo.id_tipo_pago);
                $('#id_tipo_pago_v').selectpicker('val', campo.id_tipo_pago);
            });
        }
    });
}

 confirmar_pedido = function(id_pedido,estado_pedido){
    var html_confirm ='<div>Enviar los productos para su preparación, a su área de producción correspondiente</div><br>\
    <div style="width: 100% !important; float: none !important;">\
        <table class="table m-b-0">\
            <tr><td class="text-left">Pedido: </td><td class="text-right">'+$('.pedido-numero').text()+'</td></tr>\
            <tr><td class="text-left">Cliente: </td><td class="text-right">'+$('.pedido-cliente').text()+'</td></tr>\
        </table>\
    </div><br>\
    <div><span class="text-success" style="font-size: 17px;">¿Está Usted de Acuerdo?</span></div>';
    Swal.fire({
        title: 'Necesitamos de tu Confirmación',
        html: html_confirm,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#34d16e',
        confirmButtonText: 'Si, Adelante!',
        cancelButtonText: "No!",
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return new Promise(function(resolve) {
                impresion_comanda(id_pedido,estado_pedido);
                //window.open($("#url").val()+'venta','_self');
                delivery_list_a();
                reset_default();
                $('.cont01').css('display','flex');
                swal.close();
            });
        }       
    });
}

var alert_pedidos_programados = function(){
    $.ajax({
      type: "post",
      dataType: "json",
      url: $('#url').val()+'venta/alert_pedidos_programados',
        success: function (data){
            var fecha1 = moment($('#hora').val(), 'HH:mm:ss');
            var fecha2 = moment(data.hora_entrega, 'HH:mm:ss');
            var duration = moment.duration(fecha2 - fecha1).humanize();
            var minutos = fecha2.diff(fecha1, 'minutes');
            if(minutos < 30){
                html = '';
                var html_confirm = '<div class="font-16">Hay un pedido para las <strong>'+moment(fecha2).format('hh:mm A')+'</strong> esperando ser enviado a <strong>PREPARACIÓN</strong></div>\
                <br><div style="width: 100% !important; float: none !important;">\
                    <table class="table m-b-0">\
                    <tr><td class="text-left">Pedido: </td><td class="text-right">'+data.nro_pedido+'</td></tr>\
                    <tr><td class="text-left">Cliente: </td><td class="text-right">'+data.nombre_cliente+'</td></tr>\
                    </table>\
                </div><br>\
                <div><span class="text-success" style="font-size: 17px;">¿Continuar ahora?</span></div>';
                Swal.fire({
                    title: '',
                    html: html_confirm,
                    icon: 'info',
                    position: 'top-end',
                    showCancelButton: true,
                    confirmButtonColor: '#34d16e',
                    confirmButtonText: 'Si, Adelante!',
                    cancelButtonText: "No!",
                    showLoaderOnConfirm: true,
                    preConfirm: function() {
                        return new Promise(function(resolve) {
                            $('#codtipoped').val(3);
                            listarPedidosDetalle(3,data.id_pedido,0);
                            activaTab('tabp-3');
                            activaTab('delivery01');
                            delivery_list_a();
                            $('.display-estado-mesa').css('display','none');
                            $('.cont01-1').css('display','none');
                            $('.cont01-2').css('display','block');
                            Swal.close();
                        });
                    }             
                });
                var sound = new buzz.sound($('#url').val()+"public/sound/alert02", {
                    formats: [ "ogg", "mp3", "aac" ]
                });
                sound.play();
            }
            //alert(duration);
        }
    });
}

var validarApertura = function(){
    if($('#cod_ape').val() == 0 && $('#rol_usr').val() != 1 && $('#rol_usr').val() != 2 && $('#rol_usr').val() != 7 && $("#rol_usr").val() != -1){
        var html_confirm = '<div>Para poder realizar esta operación es necesario Aperturar Caja</div>\
            <br>\
            <div><span class="text-success" style="font-size: 18px;">¿Está Usted de Acuerdo?</span></div><br>\
            <a href="'+$("#url").val()+'caja/apercie" class="btn btn-success">Si, Adelante!</a>';

        Swal.fire({
            title: 'Advertencia',
            html: html_confirm,
            icon: 'warning',
            allowOutsideClick: false,
            allowEscapeKey : false,
            showCancelButton: false,
            showConfirmButton: false,
            closeOnConfirm: false,
            closeOnCancel: false
        });
    }
}

$('.pedido_programado').on('click', function(event){
    if( $(this).is(':checked') ) {
        $('#pedido_programado').val('1');
        $('.display-hora-entrega').css('display','block');
        $("#hora_entrega").removeAttr('disabled');
        $('#hora_entrega').selectpicker('refresh');
        $('#hora_entrega').selectpicker('val', '');
        $('#form-nuevo-pedido').formValidation('revalidateField', 'hora_entrega');
    } else {
        $('#pedido_programado').val('0');
        $("#hora_entrega").prop('disabled', true);
        $('.display-hora-entrega').css('display','none');
    }
});

/*
var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};
*/

function activaTab(tab){
    $('.nav-tabs a[href="#' + tab + '"]').tab('show');
};


