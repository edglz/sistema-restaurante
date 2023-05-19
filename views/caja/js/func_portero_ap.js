var moneda = $("#moneda").val();
$(function () {
    "use-strict";
    Crud.listar();
    $("#form-apertura").on("submit", (e)=>{
        e.preventDefault();
        Crud.Apert();
    })
    $("#form-cierre").on("sumbit", (e)=>{
        e.preventDefault();
        Crud.Cerrar();
    })
});

class Crud {
    
static listar (){
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
static Apert(){
    var parametros = {
        "id_apc" : '',
        "id_caja" : $('#id_caja').val(),
        "id_turno" : $('#id_turno').val(),
        "monto_aper" : $('#monto_aper').val()
    };
    var html_confirm = '<div>Se creará una apertura de puerta con los siguientes datos:</div>\
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
                  }).then(()=>{
                    $("#modal-apertura").modal('hide');
                    window.location.reload();
                  })
                 
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
}
static Cerrar(){

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
}