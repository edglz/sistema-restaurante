var moneda = $("#moneda").val();
$(function() {
    moment.locale('es');
    $('#caja').addClass("active");
    $('#c-mon').addClass("active");
    mesas_list();
    //listar();
    //ventas_por_cobrar();

    $('#form-editar-pago')
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
            $('#modal-editar-pago').modal('hide');
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
            mesas_list();
            ventas_list();        
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown + ' ' + textStatus);
        }    
    });
        return false;
    });

    $('#form-editar-documento')
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

        if ($("#cliente_id").val() == ''){
            Swal.fire({   
                title:'Advertencia',   
                text: 'Ingrese un cliente para el comprobante de pago',
                icon: "warning", 
                confirmButtonColor: "#34d16e",   
                confirmButtonText: "Aceptar",
                allowOutsideClick: false,
                showCancelButton: false,
                showConfirmButton: true
            }, function() {
                return false
            });
        } else {
            id_venta = $('#id_venta').val();
            id_tipo_documento = $('input:radio[name="tipo_doc"]:checked').val();
            id_cliente = $('#cliente_id').val();

            $.ajax({
                dataType: 'JSON',
                type: 'POST',
                url: $('#url').val()+'venta/venta_edit_documento',
                data: {
                    id_venta: id_venta,
                    id_tipo_documento: id_tipo_documento,
                    id_cliente: id_cliente
                },
                success: function (cod) {
                    console.log(cod);
                    $('#modal-editar-documento').modal('hide');
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
                    mesas_list();
                    ventas_list();       
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(errorThrown + ' ' + textStatus);
                }    
            });
        }
        return false;
    });
    
    $("#buscar_cliente").autocomplete({
        delay: 1,
        autoFocus: true,
        source: function (request, response) {
            $.ajax({
                url: $('#url').val()+'venta/buscar_cliente',
                type: "post",
                dataType: "json",
                data: {
                    cadena: request.term,
                    tipo_cliente: $('#cliente_tipo').val()
                },
                success: function (data) {
                    response($.map(data, function (item) {
                        tipo_cli = (item.tipo_cliente == 1) ? $("#diAcr").val() : $("#tribAcr").val();
                        return {
                            id: item.id_cliente,
                            dni: item.dni,
                            ruc: item.ruc,
                            tipo: item.tipo_cliente,
                            nombres: item.nombre,
                            fecha_n: item.fecha_nac,
                            label: tipo_cli+': '+item.dni+''+item.ruc+' | '+item.nombre,
                            value: tipo_cli+': '+item.dni+''+item.ruc+' | '+item.nombre
                        }
                    }))
                }
            })
        },
        select: function (e, ui) {
            $("#cliente_id").val(ui.item.id);
            $(this).blur();
            $("#btn-submit-editar-documento").removeAttr('disabled');
            $("#btn-submit-editar-documento").removeClass('disabled');
            $('.opcion-cliente').html('<a class="input-group-prepend" href="javascript:void(0)"'
                +'onclick="editar_cliente('+ui.item.id+');" data-original-title="Editar cliente" data-toggle="tooltip"'
                +'data-placement="top">'
                    +'<span class="input-group-text bg-header">'
                        +'<small><i class="fas fa-user text-info"></i></small>'
                   +'</span>'
                +'</a>');
        }
    });
    $("#buscar_cliente").autocomplete("option", "appendTo", ".form-editar-documento");
});

/*
var ventas_por_cobrar = function(){
    var total = 0;
    $.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: $('#url').val()+'caja/monitor_ventas_porcobrar',
        success: function (data) {
            $('.mesas-operaciones').text(data.length);
            $.each(data, function(i, item) {
                total += parseFloat(item.VentasPorCobrar.total);
            });
            $('.mesas-total').text(moneda+' '+formatNumber(total));
        }
    });
};
*/

var listar = function(){

    function filterGlobal () {
        $('#table').DataTable().search( 
            $('#global_filter').val()
        ).draw();
    }

    var table = $('#table')
    .DataTable({
        "destroy": true,
        "dom": "tip",
        "bSort": true,
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"caja/monitor_list"
        },
        "columns":[
	        {"data":"fecha_aper","render": function ( data, type, row ) {
	            return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y')
	            +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data).format('h:mm A')+'</span>';
	        }},
	        {"data":null,"render": function ( data, type, row ) {
	            return '<h6><a href="javascript::void(0)" class="link" onclick="datalist('+data.id_apc+',\''+data.desc_per+'\');">'+data.desc_per+'</a></h6>';
	        }},
	        {"data": "desc_caja"},
	        {"data": "desc_turno"}
        ]
    });
    
    $('input.global_filter').on( 'keyup click', function () {
        filterGlobal();
    });
};

var datalist = function(id_apc,nombre_cajero){
    $('.display-one').css('display','none');
    $('.display-two').css('display','block');
    ventas_list(id_apc);
    mesas_list(id_apc);
    $('#id_apc').val(id_apc);
    $('.cajero-nombre').text(nombre_cajero);
}

var ventas_list = function(id_apc){
    var count = 1;
    var table = $('#table-ventas')
    .DataTable({
        "destroy": true,
        "dom": "tp",
        "bSort": true,
        "order": [[0,"desc"]],
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"caja/monitor_ventas_list"
        },
        "columns":[
        	{"data":"fec_ven","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data).format('h:mm A')+'</span>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                var tachado = (data.estado !== 'a') ? 'line-through' : '';
                return '<div style="text-decoration: '+tachado+';">'+data.desc_td
                +'<br><span class="font-12">'+data.ser_doc+'-'+data.nro_doc+'</span></div>';
            }},
            {"data":"Cliente.nombre","render": function ( data, type, row ) {
                return '<div class="mayus">'+data+'</div>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.id_tped == 1){
                    return 'SALON'
                    +'<br><span class="font-12">'+data.Pedido.desc_salon+' - Mesa: '+data.Pedido.nro_mesa+'</span>';
                } else if(data.id_tped == 2){
                   return 'MOSTRADOR';
                } else {
                    return 'DELIVERY';
                }
            }},
            {"data": null,
                "render": function(data, type, row){
                //var repartidor = (data.tipo_entrega == 1) ? '<i class="fas fa-bicycle"></i> '+data.Tipopago.nombre : '-';
                if(data.id_tpag == 1){
                    return '<span class="label label-success">'+data.Tipopago.nombre+'</span>';
                } else if(data.id_tpag == 2){
                    return '<span class="label label-info">'+data.Tipopago.nombre+'</span>';
                } else if(data.id_tpag == 3){
                    return '<span class="label label-warning">'+data.Tipopago.nombre+'</span>';
                } else if(data.id_tpag == 4){
                    return '<span class="label label-danger text-primary font-bold">C</span> <span class="text-primary font-bold">'+data.Tipopago.nombre+'</span>';
                } else if(data.id_tpag >= 5){
                    return '<span class="label label-light-primary">'+data.Tipopago.nombre+'</span>';
                }
            }},
	    	{"data":"monto_total","render": function ( data, type, row ) {
	            return '<div class="text-right">'+formatNumber(data)+'</div>';
	        }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.estado !== 'a'){
                    return '<div class="text-right"><span class="label label-danger">ANULADO</span></div>';
                }else{
                    if(data.id_tdoc == 3){
                        var opcion1 = 'block';
                    } else {
                        var opcion1 = 'none'; 
                    }
                    return '<div class="text-right"><div class="btn-group">'
                        +'<a href="javascript::void(0)" class="text-dark" id="new" data-toggle="dropdown" aria-expanded="false"><i data-feather="more-vertical" class="feather-sm"></i></a>'
                            +'<div class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(0px, -197px, 0px); top: 0px; left: 0px; will-change: transform;">'
                                +'<a class="dropdown-item" style="display: '+opcion1+'" href="javascript:void(0);" onclick="editar_documento('+data.id_ven+');"><i data-feather="file-text" class="feather-sm fill-white"></i> Editar tipo documento</a>'
                                +'<a class="dropdown-item" href="javascript:void(0);" onclick="editar_pago('+data.id_ven+');"><i data-feather="dollar-sign" class="feather-sm fill-white"></i> Editar pago</a>'
                                +'<a class="dropdown-item" href="'+$("#url").val()+'informe/venta_all_imp/'+data.id_ven+'" target="_blank"><i data-feather="printer" class="feather-sm fill-white"></i> Imprimir</a>'
                                +'<div class="dropdown-divider" style="display: '+opcion1+'"></div>'
                                +'<a class="dropdown-item text-danger" style="display: '+opcion1+'" href="javascript:void(0);" onclick="anular_venta('+data.id_ped+',\''+data.ser_doc+'-'+data.nro_doc+'\','+data.id_ven+');"><i data-feather="alert-circle" class="feather-sm fill-white"></i> Anular venta</a>'
                            +'</div>'
                        +'</div></div>';
                }
            }}
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            total = api
                .column( 5 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            operaciones = api
                .rows()
                .data()
                .count();

            $('.ventas-total').text(moneda+' '+formatNumber(total));
            $('.ventas-operaciones').text(operaciones);
        }
    });
    $('.dataTables_wrapper').addClass('p-0');
    $('#table-ventas').DataTable().on("draw", function(){
        feather.replace();
    });
}

var mesas_list = function(id_apc){
    var count = 1;
    var table = $('#table-mesas')
    .DataTable({
        "destroy": true,
        "dom": "tp",
        "bSort": true,
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"caja/monitor_mesas_list"
        },
        "columns":[
            {"data":null,"render": function ( data, type, row ) {
                return count++;
            }},
            {"data":null,"render": function ( data, type, row ) {
                return data.desc_salon+' - Mesa: '+data.nro_mesa;
            }},
            {"data":"Total.total","render": function ( data, type, row ) {
                return '<div class="text-right">'+formatNumber(data)+'</div>';
            }}
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            total = api
                .column( 2 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            operaciones = api
                .rows()
                .data()
                .count();

            $('.mesas-total').text(moneda+' '+formatNumber(total));
            $('.mesas-operaciones').text(operaciones);
        }
    });
    $('.dataTables_wrapper').addClass('p-0');
}

$('.list-mesas').click( function() {
    mesas_list($('#id_apc').val());
});

$('.list-ventas').click( function() {
    ventas_list($('#id_apc').val());
});

var editar_pago = function(id_venta){
    $('#modal-editar-pago').modal('show');
    $.ajax({
      type: "post",
      dataType: "json",
      data: {
          id_venta: id_venta
      },
      url: $('#url').val()+'venta/venta_edit',
        success: function (item){
            $.each(item.data, function(i, campo) {
                //$('#id_pedido').val(campo.id_pedido);
                $('.id_venta').val(campo.id_venta);
                $('#id_venta_tipopago').val(campo.id_tipo_pago);
                $('#id_tipo_pago_v').selectpicker('val', campo.id_tipo_pago);
            });
        }
    });
}

var editar_documento = function(id_venta){
    $('.id_venta').val(id_venta);
    $('#cliente_id').val(1);
    $('#cliente_tipo').val(1);
    $('#modal-editar-documento').modal('show');
    $('.btn-tipo-doc-1').addClass('active');
    $('.btn-tipo-doc-2').removeClass('active');
    $("input[name=tipo_doc][value='1']").attr("checked",true);
    $('.opcion-cliente').html('<a class="input-group-prepend" href="javascript:void(0)"'
        +'onclick="nuevoCliente();" data-original-title="Registrar nuevo cliente" data-toggle="tooltip"'
        +'data-placement="top">'
            +'<span class="input-group-text bg-header">'
                +'<small><i class="fas fa-user-plus"></i></small>'
           +'</span>'
        +'</a>');
}

var anular_venta = function(id_pedido,nro_pedido,id_venta){
    var html_confirm = '<div>Se procederá a anular la siguiente venta:</div>\
    <div class="font-18 font-bold">Nota de Venta N°'+nro_pedido+'</div><br>\
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
                        ventas_list();
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

$('#modal-editar-documento').on('hidden.bs.modal', function() {
    $(this).find('#form-editar-documento')[0].reset();
    $('#form-editar-documento').formValidation('resetForm', true);
});

$('input[name="tipo_doc"]').on('change', function(){
    value = $('input:radio[name="tipo_doc"]:checked').val();
    $('#cliente_tipo').val(value);
    $('#tipo_cliente').val(value);
    $('#id_cliente').val('');
    if(value == 3){
        //$('#cliente_id').val(1);
        //$('#buscar_cliente').val('PUBLICO EN GENERAL');
    }else{
        $('#cliente_id').val('');
        $('#buscar_cliente').val('');
    }
    $('.opcion-cliente').html('<a class="input-group-prepend" href="javascript:void(0)"'
        +'onclick="nuevoCliente();" data-original-title="Registrar nuevo cliente" data-toggle="tooltip"'
        +'data-placement="top">'
            +'<span class="input-group-text bg-header">'
                +'<small><i class="fas fa-user-plus"></i></small>'
           +'</span>'
        +'</a>');
});