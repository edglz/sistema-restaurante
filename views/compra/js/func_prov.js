$(function() {
    listar();
    $('#compras').addClass("active");
    $('#c-proveedores').addClass("active");
    $('#frm-proveedor').formValidation({
        framework: 'bootstrap',
        excluded: ':disabled',
        fields: {
            ruc: {
                validators: {
                    stringLength: {
                        message: 'El '+$(".c-ruc").text()+' debe tener '+$("#ruc").attr("maxlength")+' digitos'
                    }
                }
            }
        }
    }).on('success.form.fv', function(e) {
        // Prevent form submission
        e.preventDefault();
        var $form = $(e.target),
        fv = $form.data('formValidation');

        var parametros = {
            "id_prov" : $("input[name='id_prov']").val(),
            "ruc" : $("input[name='ruc']").val(),
            "razon_social" : $("input[name='razon_social']").val(),
            "direccion" : $("input[name='direccion']").val(),
            "telefono" : $("input[name='telefono']").val(),
            "contacto" : $("input[name='contacto']").val(),
            "email" : $("input[name='email']").val()            
        };

        if($("input[name='id_prov']").val() != ''){
            var text = 'editará';
        } else{
            var text = 'registrará';
        }

        var html_confirm = '<div>Se '+text+' los datos del Proveedor:<br> '+$("input[name='razon_social']").val()+'.</div><br>\
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
                    url: $('#url').val()+'compra/proveedor_crud',
                    type: 'POST',
                    data: parametros,
                    dataType: 'json'
                 })
                 .done(function(response){
                    $('#modal-proveedor').modal('hide');
                    if(response==1){
                        var title = 'Proceso Terminado';
                        var text = 'Datos registrados correctamente';
                        var type = 'success';
                        listar();
                    }else if(response==2){
                        var title = 'Proceso Terminado';
                        var text = 'Datos actualizados correctamente';
                        var type = 'success';
                        listar();
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
            "url": $('#url').val()+"compra/proveedor_list"
        },
        "columns":[
            {"data":null,"render": function ( data, type, row ) {
                var tipo = $('#tribAcr').val();
                return '<h6 style="white-space: normal;">'+data.razon_social+'</h6><small class="text-muted font-13"><span class="text-muted">'+tipo+':</span> '+data.ruc+'</small>';
            }},
            {"data": "direccion"},
            {"data":null,"render": function ( data, type, row ) {
                if(data.estado == 'a'){
                    return '<div class="text-center"><a href="javascript::void(0)" onclick="estado('+data.id_prov+',\''+data.estado+'\',\''+data.razon_social+'\''+');"><span class="label label-success">ACTIVO</span></a></div>';
                }else if(data.estado == 'i'){
                    return '<div class="text-center"><a href="javascript::void(0)" onclick="estado('+data.id_prov+',\''+data.estado+'\',\''+data.razon_social+'\''+');"><span class="label label-inverse">INACTIVO</span></a></div>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="text-right"><a href="javascript:void(0)" class="text-info edit" onclick="editar('+data.id_prov+');"><i data-feather="edit" class="feather-sm fill-white"></i></a></div>';
            }}
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            total = api
                .rows()
                .data()
                .count();

            $('.proveedores-total').text(total);
        }
    });
    
    $('input.global_filter').on( 'keyup click', function () {
        filterGlobal();
    });

    $('#table').DataTable().on("draw", function(){
        feather.replace();
    });
};

var editar = function(id_prov){
    $.ajax({
        url: $('#url').val()+'compra/proveedor_datos',
        type: 'POST',
        data: {id_prov: id_prov},
        dataType: 'json'
    })
    .done(function(item){
        $.each(item.data, function(i, campo) {
            $('#id_prov').val(campo.id_prov);
            $('#ruc').val(campo.ruc);
            $('#razon_social').val(campo.razon_social);
            $('#direccion').val(campo.direccion);
            $('#telefono').val(campo.telefono);
            $('#email').val(campo.email);
            $('#contacto').val(campo.contacto);            
        });
        $('#modal-proveedor').modal('show');
        $('.modal-title').text('Editar Proveedor');
    })
    .fail(function(){
        Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
    });
}

var estado = function(id_prov,estado,nombre){

    if(estado == 'a'){
        var esta = 'INACTIVO';
        var est = 'i';
    }else{
        var esta = 'ACTIVO';
        var est = 'a';
    }

    var html_confirm = '<div>Se pondrá '+esta+' al proveedor: <br> '+nombre+'</div><br>\
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
                    url: $('#url').val()+'compra/proveedor_estado',
                    type: 'POST',
                    data: {
                        id_prov: id_prov,
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
            $('#frm-cliente').formValidation('revalidateField', 'razon_social');
            $('#frm-cliente').formValidation('revalidateField', 'direccion');
        });
    } else if($("#ruc").val() == "") {
        $('#dni').val("");
        $('#ruc').val("");
        $('#nombres').val("");
        $('#ape_paterno').val("");
        $('#ape_materno').val("");
        $('#fecha_nac').val("");
        $('#telefono').val("");
        $('#email').val("");
        $('#razon_social').val("");
        $('#direccion').val("");
        $('#frm-cliente').formValidation('resetForm', true);
    }
});

$('.btn-nuevo').click( function() {
    $('#id_prov').val('');
    $('.modal-title').text('Nuevo Proveedor');
    $('#modal-proveedor').modal('show');
});

$('#modal-proveedor').on('hidden.bs.modal', function() {
    $(this).find('form')[0].reset();
    $('#frm-proveedor').formValidation('resetForm', true);
});