var moneda = $("#moneda").val();
$(function() {
    listar(1);
    $('#clientes').addClass("active");
    $('#form').formValidation({
        framework: 'bootstrap',
        excluded: ':disabled',
        fields: {
            dni: {
                validators: {
                    stringLength: {
                        message: 'El '+$(".c-dni").text()+' debe tener '+$("#dni").attr("maxlength")+' digitos'
                    }
                }
            },
            ruc: {
                validators: {
                    stringLength: {
                        message: 'El '+$(".c-ruc").text()+' debe tener '+$("#ruc").attr("maxlength")+' digitos'
                    }
                }
            }
        }
    })
    .on('success.form.fv', function(e) {
        // Prevent form submission
        e.preventDefault();
        var $form = $(e.target),
        fv = $form.data('formValidation');

        var parametros = {
            "id_cliente" : $("input[name='id_cliente']").val(),
            "tipo_cliente" : $("input[name='tipo_cliente']").val(),
            "dni" : $("input[name='dni']").val(),
            "ruc" : $("input[name='ruc']").val(),
            "nombres" : $("input[name='nombres']").val(),
            "fecha_nac" : $("input[name='fecha_nac']").val(),
            "telefono" : $("input[name='telefono']").val(),
            "correo" : $("input[name='correo']").val(),
            "razon_social" : $("input[name='razon_social']").val(),
            "direccion" : $("input[name='direccion']").val(),
            "referencia" : $("input[name='referencia']").val()
        };

        if($("input[name='id_cliente']").val() != ''){
            var text = 'actualizará';
        } else{
            var text = 'registrará';
        }

        var html_confirm = '<div>Se '+text+' los datos del cliente:<br> '+$("input[name='razon_social']").val()+''+$("input[name='nombres']").val()+'</div><br>\
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
                    url: $('#url').val()+'cliente/cliente_crud',
                    type: 'POST',
                    data: parametros,
                    dataType: 'json'
                 })
                 .done(function(response){
                    $('#modal').modal('hide');
                    if(response==1){
                        var title = 'Proceso Terminado';
                        var text = 'Datos registrados correctamente';
                        var type = 'success';
                        listar($('#filtro_tipo_cliente').val());
                    }else if(response==2){
                        var title = 'Proceso Terminado';
                        var text = 'Datos actualizados correctamente';
                        var type = 'success';
                        listar($('#filtro_tipo_cliente').val());
                    }else if(response==0){
                        var title = 'Proceso No Culminado';
                        var text = 'Datos duplicados';
                        var type = 'error';                        
                    }
                    Swal.fire({
                        title: title,
                        text: text,
                        icon: type,
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "Aceptar"
                    });               
                    listar($('#filtro_tipo_cliente').val());
                    $('.display-one').css('display','block');
                    $('.display-two').css('display','none');
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

$('.list-personas').click( function() {
    $('#filtro_tipo_cliente').val('1');
    $('.display-one').css('display','block');
    $('.display-two').css('display','none');
    listar(1);
});

$('.list-empresas').click( function() {
    $('#filtro_tipo_cliente').val('2');
    $('.display-one').css('display','block');
    $('.display-two').css('display','none');
    listar(2);
});

var listar = function(tipo_cliente){

    //tipo_cliente = $('#filtro_tipo_cliente').selectpicker('val');
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
            "url": $('#url').val()+"cliente/cliente_list",
            "data": {
                tipo_cliente: tipo_cliente
            }
        },        
        "columns":[
            {
                "data":null,
                "render": function ( data, type, row ) {
                var tipo = (data.tipo_cliente == 1) ? $('#diAcr').val() : $('#tribAcr').val();
                return '<h6 style="white-space: normal;"><a href="javascript::void(0)" class="link" onclick="venta_list('+data.id_cliente+',\''+data.nombre+'\');">'+data.nombre+'</a></h6><small class="text-muted font-13"><span class="text-muted">'+tipo+':</span> '+data.dni+''+data.ruc+'</small>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.estado == 'a'){
                    return '<div class="text-center"><a href="javascript::void(0)" onclick="estado('+data.id_cliente+',\''+data.estado+'\',\''+data.nombre+'\''+');"><span class="label label-success">ACTIVO</span></a></div>';
                }else if(data.estado == 'i'){
                    return '<div class="text-center"><a href="javascript::void(0)" onclick="estado('+data.id_cliente+',\''+data.estado+'\',\''+data.nombre+'\''+');"><span class="label label-inverse">INACTIVO</span></a></div>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="text-right"><a href="javascript:void(0)" class="text-info edit" onclick="editar('+data.id_cliente+');"><i data-feather="edit" class="feather-sm fill-white"></i></a>'
                    +'&nbsp;<a href="javascript:void(0)" class="text-danger delete ms-2" onclick="anular('+data.id_cliente+',\''+data.nombre+'\''+');"><i data-feather="trash-2" class="feather-sm fill-white"></i></a></div>';
            }}
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            total = api
                .rows()
                .data()
                .count();

            $('.clientes-total').text(total);
        }
    });
    
    $('input.global_filter').on( 'keyup click', function () {
        filterGlobal();
    });

    $('#table').DataTable().on("draw", function(){
        feather.replace();
    });
};

var venta_list = function(id_cliente,nombre_cliente){
    $('.display-one').css('display','none');
    $('.display-two').css('display','block');
    $('.cliente-nombre').text(nombre_cliente);
    var count = 1;
    var table = $('#table-ventas')
    .DataTable({
        "destroy": true,
        "dom": "tp",
        "bSort": true,
        "order": [[0,"desc"]],
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"cliente/cliente_ventas",
            "data": {
                id_cliente: id_cliente
            }
        },
        "columns":[
            {"data":"fec_ven","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data).format('h:mm A')+'</span>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                return data.desc_td
                +'<br><span class="font-12">'+data.ser_doc+'-'+data.nro_doc+'</span>';
            }},
            {"data":"desc_monto","render": function ( data, type, row ) {
                return '<div class="text-right">'+formatNumber(data)+'</div>';
            }},
            {"data":"monto_total","render": function ( data, type, row ) {
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
 
            descuento = api
                .column( 2 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            total = api
                .column( 3 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            operaciones = api
                .rows()
                .data()
                .count();

            $('.ventas-total').text(moneda+' '+formatNumber(total));
            $('.ventas-descuentos').text(moneda+' '+formatNumber(descuento));
            $('.ventas-operaciones').text(operaciones);
        }
    });
    $('.dataTables_wrapper').addClass('p-0');
}

/* Estado del cliente Activo - Inactivo */
var estado = function(id_cliente,estado,nombres){

    if(estado == 'a'){
        var esta = 'INACTIVO';
        var est = 'i';
    }else{
        var esta = 'ACTIVO';
        var est = 'a';
    }

    var html_confirm = '<div>Se pondrá '+esta+' al cliente:<br> '+nombres+'</div><br>\
        <div><span class="text-success" style="font-size: 17px;">¿Está Usted de Acuerdo?</span></div>';

    Swal.fire({
        title: 'Necesitamos de tu Confirmación',
        html: html_confirm,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#34d16e',
        confirmButtonText: 'Si, Adelante!',
        cancelButtonText: "No!",
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                    url: $('#url').val()+'cliente/cliente_estado',
                    type: 'POST',
                    data: {
                        id_cliente: id_cliente,
                        estado: est
                    },
                    dataType: 'json'
                })
                .done(function(response){
                    Swal.fire({
                        title: 'Proceso Terminado',
                        text: 'Datos actualizados correctamente',
                        icon: 'success',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "Aceptar"
                    });
                    listar($('#filtro_tipo_cliente').val());
                    $('.display-one').css('display','block');
                    $('.display-two').css('display','none');
                })
                .fail(function(){
                    Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
                });
            });
        },
        allowOutsideClick: false              
    });
}

var anular = function(id_cliente,nombres){

    var html_confirm = '<div>Se anulará del sistema, al cliente:<br> '+nombres+'</div><br>\
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
                url: $('#url').val()+'cliente/cliente_delete',
                type: 'POST',
                data: {id_cliente: id_cliente},
                dataType: 'json'
             })
             .done(function(response){
                if(response == 1){
                Swal.fire({
                    title: 'Proceso Terminado',
                    text: 'Datos eliminados correctamente',
                    icon: 'success',
                    confirmButtonColor: "#34d16e",   
                    confirmButtonText: "Aceptar"
                });
                }else{
                    Swal.fire({
                        title: 'Proceso No Culminado',
                        text: 'Datos protegidos',
                        icon: 'error',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "Aceptar"
                    });
                }
                listar($('#filtro_tipo_cliente').val());
                $('.display-one').css('display','block');
                $('.display-two').css('display','none');
             })
             .fail(function(){
                Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
             });
          });
        },
        allowOutsideClick: false              
    });
}

var editar = function(id_cliente){
    $.ajax({
        url: $('#url').val()+'cliente/cliente_datos',
        type: 'POST',
        data: {id_cliente: id_cliente},
        dataType: 'json'
    })
    .done(function(item){
        $.each(item.data, function(i, campo) {
            $('#id_cliente').val(campo.id_cliente);
            $('#tipo_cliente').val(campo.tipo_cliente);
            $('#dni').val(campo.dni);
            $('#ruc').val(campo.ruc);
            $('#nombres').val(campo.nombres);
            $('#ape_paterno').val(campo.ape_paterno);
            $('#ape_materno').val(campo.ape_materno);
            $('#fecha_nac').val(moment(campo.fecha_nac).format('DD-MM-Y'));
            $('#telefono').val(campo.telefono);
            $('#correo').val(campo.correo);
            $('#razon_social').val(campo.razon_social);
            $('#direccion').val(campo.direccion);
            $('#referencia').val(campo.referencia);
            $('.modal-title').text('Editar Cliente');
            if(campo.tipo_cliente == 1){
                $("#td_dni").attr('checked', true);
                $("#td_ruc").attr('checked', false);
                $(".dni").prop('disabled', false);
                $(".ruc").prop('disabled', true);
                $(".block01").css("display","block");
                $(".block02").css("display","none");
                $(".block03").css("display","block");
                $(".block04").css("display","block");
                $(".block05").css("display","block");
                $(".block06").css("display","block");
                $(".block07").css("display","none");
            } else if(campo.tipo_cliente == 2){
                $("#td_ruc").attr('checked', true);
                $("#td_dni").attr('checked', false);
                $(".dni").prop('disabled', true);
                $(".ruc").prop('disabled', false);
                $(".block01").css("display","none");
                $(".block02").css("display","block");
                $(".block03").css("display","none");
                $(".block04").css("display","none");
                $(".block05").css("display","none");
                $(".block06").css("display","none");
                $(".block07").css("display","block");
            }
        });
        $('#modal').modal('show');
    })
    .fail(function(){
        swal('Oops...', 'Problemas con la conexión a internet!', 'error');
    });
}

$('.btn-nuevo').click( function() {
    $('#id_cliente').val('');
    $('#tipo_cliente').val(1);
    $('.modal-title').text('Nuevo Cliente');
    $('#modal').modal('show');
    $("#td_dni").attr('checked', true);
    $("#td_ruc").attr('checked', false);
    $(".dni").prop('disabled', false);
    $(".ruc").prop('disabled', true);
    $(".block01").css("display","block");
    $(".block02").css("display","none");
    $(".block03").css("display","block");
    $(".block04").css("display","block");
    $(".block05").css("display","block");
    $(".block06").css("display","block");
    $(".block07").css("display","none");
});

$('input:radio[id=td_dni]').on('click', function(event){
    $('#tipo_cliente').val(1);
    $("#td_dni").attr('checked', true);
    $("#td_ruc").attr('checked', false);
    $(".block01").css("display","block");
    $(".block02").css("display","none");
    $(".block03").css("display","block");
    $(".block04").css("display","block");
    $(".block05").css("display","block");
    $(".block06").css("display","block");
    $(".block07").css("display","none");
    $(".dni").prop('disabled', false);
    $(".ruc").prop('disabled', true);
    $('#form').formValidation('resetForm', true);
});

$('input:radio[id=td_ruc]').on('click', function(event){
    $('#tipo_cliente').val(2);
    $("#td_ruc").attr('checked', true);
    $("#td_dni").attr('checked', false);
    $(".block01").css("display","none");
    $(".block02").css("display","block");
    $(".block03").css("display","none");
    $(".block04").css("display","none");
    $(".block05").css("display","none");
    $(".block06").css("display","none");
    $(".block07").css("display","block");
    $(".dni").prop('disabled', true);
    $(".ruc").prop('disabled', false);
    $('#form').formValidation('resetForm', true);
});

$('#modal').on('hidden.bs.modal', function() {
    $(this).find('form')[0].reset();
    $('#form').formValidation('resetForm', true);
});

/* Consultar dni del nuevo cliente */
$("#dni").keyup(function(event) {
    var that = this,
    value = $(this).val();
    if (value.length == $("#dni").attr("maxlength")) {
        $.getJSON("https://dniruc.apisperu.com/api/v1/dni/"+$("#dni").val()+"?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InRvbW15ZGVsZ2Fkb3JvZHJpZ3VlekBnbWFpbC5jb20ifQ.ZUOOpJcgrZ2zDZuNO3OoBC8ViItUZLn3zixVsWMeq8c", {
            format: "json"
        })
        .done(function(data) {
            $("#dni").val(data.dni);
            $("#nombres").val(data.nombres+' '+data.apellidoPaterno+' '+data.apellidoMaterno);
            $('#form').formValidation('revalidateField', 'nombres');
            $('#form').formValidation('revalidateField', 'ape_paterno');
            $('#form').formValidation('revalidateField', 'ape_materno');
        });
    } else if($("#dni").val() == "") {
        $('#dni').val("");
        $('#ruc').val("");
        $('#nombres').val("");
        $('#fecha_nac').val("");
        $('#telefono').val("");
        $('#correo').val("");
        $('#razon_social').val("");
        $('#direccion').val("");
        $('#referencia').val("");
        $('#form').formValidation('resetForm', true);
    }
});

/* Consultar ruc del nuevo cliente */
$("#ruc").keyup(function(event) {
    var that = this,
    value = $(this).val();
    if (value.length == $("#ruc").attr("maxlength")) {
        $.getJSON("https://dniruc.apisperu.com/api/v1/ruc/"+$("#ruc").val()+"?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InRvbW15ZGVsZ2Fkb3JvZHJpZ3VlekBnbWFpbC5jb20ifQ.ZUOOpJcgrZ2zDZuNO3OoBC8ViItUZLn3zixVsWMeq8c", {
            format: "json"
        })
        .done(function(data) {
            //$("#dni").val(data.ruc);
            $("#ruc").val(data.ruc);
            $("#razon_social").val(data.razonSocial);
            $("#direccion").val(data.direccion);
            $('#form').formValidation('revalidateField', 'razon_social');
            $('#form').formValidation('revalidateField', 'direccion');
        });
    } else if($("#ruc").val() == "") {
        $('#dni').val("");
        $('#ruc').val("");
        $('#nombres').val("");
        $('#fecha_nac').val("");
        $('#telefono').val("");
        $('#correo').val("");
        $('#razon_social').val("");
        $('#direccion').val("");
        $('#referencia').val("");
        $('#form').formValidation('resetForm', true);
    }
});