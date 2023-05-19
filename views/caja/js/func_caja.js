var moneda = $("#moneda").val();
$(function() {
    moment.locale('es');
    listar();
    stock_pollo();
    $('#form-apertura').formValidation({
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
            "id_apc" : '',
            "id_caja" : $('#id_caja').val(),
            "id_turno" : $('#id_turno').val(),
            "monto_aper" : $('#monto_aper').val()
        };

        var html_confirm = '<div>Se creará una apertura de caja con los siguientes datos:</div>\
            <br><div style="width: 100% !important; float: none !important;">\
            <table class="table m-b-0">\
            <tr><td class="text-left">Caja: </td><td class="text-right">'+$('select[name="id_caja"] option:selected').text()+'</td></tr>\
            <tr><td class="text-left">Turno: </td><td class="text-right">'+$('select[name="id_turno"] option:selected').text()+'</td></tr>\
            <tr><td class="text-left">Monto: </td><td class="text-right">'+moneda+' '+formatNumber($('#monto_aper').val())+'</td></tr>\
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
                 $.ajax({
                    url: $('#url').val()+'caja/apercie_crud',
                    type: 'POST',
                    data: parametros,
                    dataType: 'json'
                 })
                 .done(function(response){
                    if(response == 1){
                    Swal.fire({
                        title: 'Proceso Terminado',
                        text: 'Datos registrados correctamente',
                        icon: 'success',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "Aceptar"
                    });
                    $("#modal-apertura").modal('hide');
                    }else{
                        Swal.fire({
                            title: 'Proceso No Culminado',
                            text: 'Datos duplicados',
                            icon: 'error',
                            confirmButtonColor: "#34d16e",   
                            confirmButtonText: "Aceptar"
                        });
                    }
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

    $('#form-cierre').formValidation({
        framework: 'bootstrap',
        excluded: ':disabled',
        fields: {
        }
    }).on('success.form.fv', function(e) {
        // Prevent form submission
        e.preventDefault();
        var $form = $(e.target);
        var fv = $form.data('formValidation');
        cierre();
    });

    $('#caja').addClass("active");
    $('#c-apc').addClass("active");
});

var listar = function(){
    $.ajax({
        url:   $('#url').val()+'caja/apercie_list',
        type:  'POST',
        dataType: 'json',
        success: function(data) {
            if(data == false){
                $('.display-apertura').css('display','block');
                $('.display-cierre').css('display','none');
            } else {
                $('.display-apertura').css('display','none');
                $('.display-cierre').css('display','block');
                $('#id_apc').val(data.id_apc);
                $('.fecha-apertura').text(moment(data.fecha_aper).format('[Abierto el día ]dddd, D [de] MMMM [a las] h:mm:ss a'));
                //$('.fecha-apertura').text(moment(data.fecha_aper).format('DD-MM-Y')+' | '+moment(data.fecha_aper).format('h:mm A'));
                monto_sistema(data.id_apc);
            }
        }
    });
}

var monto_sistema = function(id_apc){
    $.ajax({
        //async: false,
        data: { id_apc : id_apc },
        type:  'POST',
        dataType: 'json',
        url:   $('#url').val()+'caja/apercie_montosist',
        success: function(data) {
            if (data.total != '') {
                var montoSist = (parseFloat(data.Apertura.monto_aper) + parseFloat(data.total) - parseFloat(data.pago_tar) + parseFloat(data.Ingresos.total) - parseFloat(data.EgresosA.total) - parseFloat(data.EgresosB.total)).toFixed(2);
                $("#monto_sistema").val(montoSist);
            }
        }
    });
}

var cierre = function(){

    var parametros = {
        "id_apc" : $('#id_apc').val(),
        "monto_cierre" : $('#monto_cierre').val(),
        "monto_sistema" : $('#monto_sistema').val(),
        "stock_pollo" : $('#stock_pollo').val()
    };

    var html_confirm = '<div>Se cerrará el turno de caja con los siguientes datos:</div>\
        <br><div style="width: 100% !important; float: none !important;">\
        <table class="table">\
        <tr><td class="text-left">Importe: </td><td class="text-right">'+moneda+' '+formatNumber($('#monto_cierre').val())+'</td></tr>\
        </table>\
        </div>\
        <div><span class="text-success" style="font-size: 17px;">¿Está Usted de Acuerdo?</span></div>';
        
    var html_print = '<div>El turno de caja ha sido cerrada.<br>El encargado del DINAMO realizará el arqueo de caja, De click en "ACEPTAR" para culminar con el cierre de caja.</div>';
   
    //var html_print = '<div>El turno de caja ha sido cerrada.<br>Puede imprimir el arqueo de caja, para obtener el detalle de sus procesos.</div>\
    //    <br><div class="text-center"><a href="'+$("#url").val()+'informe/finanza_arq_imp/'+$('#id_apc').val()+'" target="_blank"><i class="fas fa-print font-20 text-primary"></i><br>Arqueo de caja</a></div><br>\
      //  ';
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
                url: $('#url').val()+'caja/apercie_crud',
                type: 'POST',
                data: parametros,
                dataType: 'json'
             })
             .done(function(response){
                Swal.fire({
                    title: 'Proceso Terminado',
                    html: html_print,
                    icon: 'success',
                    confirmButtonColor: "#34d16e",   
                    confirmButtonText: "Aceptar"
                });
                $("#modal-cierre").modal('hide');
                listar();
             })
             .fail(function(){
                Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
             });
          });
        },
        allowOutsideClick: false              
    });
}

$('.btn-aceptar-apertura').on('submit', function() {
    $('.s').addClass('focused');
});

var stock_pollo = function(){
    $.ajax({
        type:  'POST',
        dataType: 'json',
        url:   $('#url').val()+'caja/stock_pollo',
        success: function(data) {
            if (data.total != '') {
                $("#stock_pollo").val(data.total);
            }
        }
    }); 
}

/* Accion desde la fecha */
/*
$('#fecha_cierre').on('change', function(e) { 
    $.ajax({
        data: { id_apc : $("#id_apc").val() },
        type:  'POST',
        dataType: 'json',
        url:   $('#url').val()+'caja/apercie_montosist',
        success: function(data) {
            if (data.total != '') {
                // Se agrego parseFloat(data.pago_tar) para obtener monto de cierre en efectivo
                var montoSist = (parseFloat(data.Apertura.monto_aper) - parseFloat(data.pago_tar) + parseFloat(data.total) + parseFloat(data.Ingresos.total) - parseFloat(data.EgresosA.total) - parseFloat(data.EgresosB.total)).toFixed(2);
                $("#monto_sistema").val(montoSist);
            }
        }
    }); 
});
*/