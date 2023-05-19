var moneda = $("#moneda").val();
$(function() {
    $("#monto_egreso").prop('disabled', true);
    $('#creditos').addClass("active");
    $('#cr-compras').addClass("active");
    moment.locale('es');
	listar();

    $('#start').bootstrapMaterialDatePicker({
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });

    $('#end').bootstrapMaterialDatePicker({
        useCurrent: false,
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });

    $('#start,#end,#filtro_proveedor').change( function() {
        listar();
    });

    $('#form-credito').formValidation({
        framework: 'bootstrap',
        excluded: ':disabled',
        fields: {
        }
    }).on('success.form.fv', function(e) {
        // Prevent form submission
        e.preventDefault();
        var $form = $(e.target),
        fv = $form.data('formValidation');

        var parametros = {
            "id_credito" : $("input[name='id_credito']").val(),
            "total_credito" : $("input[name='total_credito']").val(),
            "monto_amortizado" : $("input[name='monto_amortizado']").val(),
            "importe" : $("input[name='importe']").val(),
            "egreso" : $("input[name='egreso']").val(),
            "monto_egreso" : $("input[name='monto_egreso']").val()
        };

        var html_confirm = '<div>Se registrará el siguiente pago de cuota con un monto de '+moneda+' '+$("input[name='importe']").val()+'.</div><br>\
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
                    url: $('#url').val()+'credito/credito_compra_cuota_pago',
                    type: 'POST',
                    data: parametros,
                    dataType: 'json'
                 })
                 .done(function(response){
                    $('#modal-credito').modal('hide');
                    Swal.fire({
                        title: 'Proceso Terminado',
                        text: 'Dato registrado correctamente',
                        icon: 'success',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "Aceptar"
                    });               
                    listar();
                 })
                 .fail(function(){
                    Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
                 });
              });
            },
            allowOutsideClick: false              
        });
    });
});

/* Mostrar datos en la tabla compras al credito */
var listar = function(){

    ifecha = $("#start").val();
    ffecha = $("#end").val();
    id_prov = $("#filtro_proveedor").selectpicker('val');

	var	table =	$('#table')
	.DataTable({
		"destroy": true,
        "responsive": true,
		"dom": "tip",
		"bSort": true,
        "order": [[0,"desc"]],
		"ajax":{
			"method": "POST",
			"url": $('#url').val()+"credito/credito_compra_list",
			"data": {
                ifecha: ifecha,
                ffecha: ffecha,
                id_prov: id_prov
            }
		},
		"columns":[
			{"data": null,"render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data.fecha).format('DD-MM-Y');
            }},
            {"data":"desc_prov"},
            {
                "data": null,
                "render": function ( data, type, row) {
                    return data.desc_td+' '+data.numero;
                }
            },
            {"data":"total","render": function ( data, type, row) {
                return '<span class="label label-danger font-12 p-5"> '+moneda+' '+formatNumber(data)+'</span>';
            }},
            {"data":"interes","render": function ( data, type, row) {
                return moneda+' '+formatNumber(data);
            }},
            {"data":"Amortizado.total","render": function ( data, type, row) {
                return moneda+' '+formatNumber(data);
            }},
            {"data":null,"render": function ( data, type, row) {
                var cal = (data.total - data.Amortizado.total).toFixed(2);
                return '<span class="label label-warning font-12 p-5"> '+moneda+' ' +formatNumber(cal)+'</span>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                var call = (data.total - data.Amortizado.total).toFixed(2);
                return '<div class="text-right"><a href="javascript:void(0)" class="text-info edit" onclick="detalle('+data.id_credito+',\''+data.desc_td+'\',\''+data.numero+'\')"><i data-feather="eye" class="feather-sm fill-white"></i></a>'
                    +'&nbsp;<a href="javascript:void(0)" class="text-success delete ms-2" onclick="pagoCuota('+data.id_credito+')"><i data-feather="plus-circle" class="feather-sm fill-white"></i></a></div>';
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
 
            total_deuda = api
                .column( 3 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            total_interes = api
                .column( 4 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            total_amortizado = api
                .column( 5 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            $('.total-deuda').text(moneda+' '+formatNumber(total_deuda));
            $('.total-interes').text(moneda+' '+formatNumber(total_interes));
            $('.total-amortizado').text(moneda+' '+formatNumber(total_amortizado));
            $('.total-pendiente').text(moneda+' '+formatNumber(total_deuda-total_amortizado));
        }
	});

    $('#table').DataTable().on("draw", function(){
        feather.replace();
    });

};

/* Pago o amortizacion de cuota */
var pagoCuota = function(id_credito){
    $('#modal-credito').modal('show');
    $.ajax({
        type: "POST",
        url: $('#url').val()+"credito/credito_compra_cuota_list",
        data: {
            id_credito: id_credito
        },
        dataType: "json",
        success: function(item){
            $.each(item.data, function(i, campo) {
                var call = (campo.total - campo.Amortizado.total).toFixed(2);
                $('#id_credito').val(campo.id_credito);
                $('#total_credito').val(campo.total);
                $('#monto_amortizado').val(campo.Amortizado.total);
                $('.title-detalle').text(campo.desc_td+' - '+campo.numero);
                $('.c-fecha-comp').text(moment(campo.fecha).format('DD-MM-Y'));  
                $('.c-datos-prov').text(campo.desc_prov);
                $('.c-monto-pend').text(moneda+' '+formatNumber(call));
            })  
        }
    });
};

/* Detalle de la cuota(s) al credito */
var detalle = function(id_credito,desc_td,numero){
    $('.title-detalle').text(desc_td+' - '+numero);
    $('#table-detalle').empty();
    $('#modal-detalle').modal('show');
    $.ajax({
        type: "POST",
        dataType: "json",
        url: $('#url').val()+"credito/credito_compra_det",
        data: {
            id_credito: id_credito
        },
        success: function(data){
            if(data.length > 0){   
                $.each(data, function(i, item) {
                    var egresos = (item.egreso == 1) ? '<i class="ti ti-check text-success"/>' : '<i class="ti ti-close text-danger"/>';
                    $('#table-detalle')
                    .append(
                      $('<tr/>')
                        .append($('<td/>').html(item.Usuario.nombre))
                        .append($('<td/>').html(moment(item.fecha).format('DD-MM-Y h:mm A')))
                        .append($('<td class="text-center"/>').html(egresos))
                        .append($('<td class="text-right"/>').html(moneda+' '+formatNumber(item.importe)))
                        );
                });
            } else {
                $('#table-detalle').html("<tr style='border-left: 1px solid #fff;'><td colspan='4'><div class='text-center'><h4 class='m-t-0' style='color: #d3d3d3;'><i class='mdi mdi-alert-circle display-3 m-t-40 m-b-10'></i><br><small>No se encontraron datos</small></h4></div></td></tr>");
            }
        }
    });
}

/* Opcion egreso de caja */
$('.egreso').on('click', function(event){
    if( $(this).is(':checked') ) {
        $('#egreso').val('1');
        $('.monto-egreso').css('display','block');
        $("#monto_egreso").prop('disabled', false);
    } else {
        $('#egreso').val('0');
        $('.monto-egreso').css('display','none');
        $("#monto_egreso").prop('disabled', true);
    }
});

$('#modal-credito').on('hidden.bs.modal', function() {
    $(this).find('form')[0].reset();
    $('#form-credito').formValidation('resetForm', true);
    $('#egreso').val('0');
    $('.egreso').prop('checked', false);
    $('.monto-egreso').css('display','none');
    //$('.icheckbox_flat-red').removeClass('checked');
    $("#monto_egreso").prop('disabled', true);
});