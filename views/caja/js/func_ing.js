var moneda = $("#moneda").val();
$(function() {
    $('#caja').addClass("active");
    $('#c-ing').addClass("active");
    validarApertura();
    listar();
    $('#filtro_estado').change( function() {
        listar();
    });
    $('#form').formValidation({
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
            "importe" : $('#importe').val(),
            "responsable" : $('#responsable').val(),
            "motivo" : $('#motivo').val()
        };

        var html_confirm = '<div>Se creará un ingreso con los siguientes datos:</div>\
            <br><div style="width: 100% !important; float: none !important;">\
            <table class="table m-b-0">\
            <tr><td class="text-left">Importe: </td><td class="text-right">'+moneda+' '+formatNumber($('#importe').val())+'</td></tr>\
            <tr><td class="text-left">Recibido de: </td><td class="text-right">'+$('#responsable').val()+'</td></tr>\
            <tr><td class="text-left">Motivo: </td><td class="text-right">'+$('#motivo').val()+'</td></tr>\
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
                        url: $('#url').val()+'caja/ingreso_crud',
                        type: 'POST',
                        data: parametros,
                        dataType: 'json'
                    })
                    .done(function(response){
                        Swal.fire({
                            title: 'Proceso Terminado',
                            text: 'Datos registrados correctamente',
                            icon: 'success',
                            confirmButtonColor: "#34d16e",   
                            confirmButtonText: "Aceptar"
                        });
                        $('#modal').modal('hide');
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

/* Validar si se aperturo caja */
var validarApertura = function(){
    if($('#cod_ape').val() == 0 && $('#rol_usr').val() != 1){
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

var listar = function(){
    
    estado = $('#filtro_estado').selectpicker('val');

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
        "order": [[0,"desc"]],
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"caja/ingreso_list",
            "data": {
                estado: estado
            }
        },
        "columns":[
            {"data":"fecha_reg","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y');
            }},
            {"data":"fecha_reg","render": function ( data, type, row ) {
                return '<i class="ti-time"></i> '+moment(data).format('h:mm A');
            }},
            {"data": "responsable"},
            {"data": "motivo"},
            {"data":"importe","render": function ( data, type, row ) {
                return moneda+' '+data;
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.estado == 'a'){
                    return '<div class="text-center"><span class="label label-success">APROBADO</span></div>';
                }else if(data.estado == 'i'){
                    return '<div class="text-center"><span class="label label-danger">ANULADO</span></div>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="text-right"><a href="javascript:void(0)" class="text-danger delete ms-2" onclick="anular('+data.id_ing+',\''+data.importe+'\',\''+data.responsable+'\',\''+data.motivo+'\''+');"><i data-feather="trash-2" class="feather-sm fill-white"></i></a></div>'
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
                .column( 4 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            operaciones = api
                .rows()
                .data()
                .count();

            $('.ingresos-total').text(moneda+' '+formatNumber(total));
            $('.ingresos-oper').text(operaciones);
        }/*,
        "fnCreatedRow": function(nRow, aData, iDataIndex){
            $(nRow).addClass("tr-left");
        }*/
    });

    $('input.global_filter').on( 'keyup click', function () {
        filterGlobal();
    });

    $('#table').DataTable().on("draw", function(){
        feather.replace();
    });

};

/* Anular ingreso administrativo */
var anular = function(id_ing,importe,responsable,motivo){
    var html_confirm = '<div>Se anulará el ingreso con los siguientes datos:</div>\
        <br><div style="width: 100% !important; float: none !important;">\
        <table class="table m-b-0">\
        <tr><td class="text-left">Importe: </td><td class="text-right">'+moneda+' '+formatNumber(importe)+'</td></tr>\
        <tr><td class="text-left">Recibido de: </td><td class="text-right">'+responsable+'</td></tr>\
        <tr><td class="text-left">Motivo: </td><td class="text-right">'+motivo+'</td></tr>\
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
                url: $('#url').val()+'caja/ingreso_estado',
                type: 'POST',
                data: {id_ing: id_ing},
                dataType: 'json'
             })
             .done(function(response){
                Swal.fire({
                    title: 'Proceso Terminado',
                    text: 'Datos anulados correctamente',
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
}

/* Nuevo ingreso administrativo */
$('#modal').on('hidden.bs.modal', function() {
    $('#form').formValidation('resetForm', true);
    $("#importe").val('');
    $("#motivo").val('');
});