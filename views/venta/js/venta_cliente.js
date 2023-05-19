$(function() {
    /* NOTA KERLYN: quitar el excluded del form*/
    $('#form-cliente').formValidation({
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
        var $form = $(e.target);
        var fv = $form.data('formValidation');

        /*
        if($('#codcomision').val() == 1){
            var tipo_cliente = $('input:radio[name=tipo_cli]:checked').val();
        }else{
            var tipo_cliente = $('#tipo_cliente').val();
        }
        */

        var id_cliente = $('#id_cliente').val();
        var tipo_cliente = $('#tipo_cliente').val();
        var ruc = $('#ruc').val();
        var dni = $('#dni').val();
        var nombres = $('#nombres').val();
        var fecha_nac = $('#fecha_nac').val();
        var telefono = $('#telefono').val();
        var correo = $('#correo').val();
        var razon_social = $('#razon_social').val();
        var direccion = $('#direccion').val();
        var referencia = $('#referencia').val();

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: {
                id_cliente: id_cliente,
                tipo_cliente: tipo_cliente,
                ruc:ruc,
                dni:dni,
                nombres:nombres,
                fecha_nac:fecha_nac,
                telefono:telefono,
                correo:correo,
                razon_social:razon_social,
                direccion:direccion,
                referencia:referencia
            },
            url: $('#url').val()+'venta/cliente_crud',
            success: function(data){
                if(data.cod == 1){
                    Swal.fire({   
                        title:'Proceso No Culminado',   
                        text: 'El cliente ya ha sido registrado anteriormente',
                        icon: 'warning',
                        confirmButtonColor: '#34d16e',   
                        confirmButtonText: 'Aceptar',
                        allowOutsideClick: false,
                        showCancelButton: false,
                        showConfirmButton: true
                    }, function() {
                        return false
                    });
                }else {
                    $('#modal-cliente').modal('hide');
                    $('#cliente_id').val(data.id_cliente);
                    $('#id_cliente').val(data.id_cliente);

                    if($('#codpagina').val() == 1){

                        /////////////////////////////////////////////
                        $('.display-nombre').css('display','block');
                        $('.display-telefono-cliente').css('display','block');
                        $("#nomb_cliente").removeAttr('disabled');
                        $("#telefono_cliente").removeAttr('disabled');
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

                        $('#telefono_cliente').val(telefono);
                        if(tipo_cliente == 1){
                            $("#nomb_cliente").val(nombres);
                        } else if (tipo_cliente == 2){
                            $("#nomb_cliente").val(razon_social);
                        }
                        $('#direccion_cliente').val(direccion);
                        $('#referencia_cliente').val(referencia);
                        $('#form-nuevo-pedido').formValidation('revalidateField', 'telefono_cliente');
                        $('#form-nuevo-pedido').formValidation('revalidateField', 'nomb_cliente');
                        $('#form-nuevo-pedido').formValidation('revalidateField', 'direccion_cliente');
                        $('#form-nuevo-pedido').formValidation('revalidateField', 'referencia_cliente');

                        $('.btn-opc-nuevo-cliente').html('<button class="btn btn-info" onclick="editar_cliente('+data.id_cliente+');" type="button"><i class="fa fa-user"></i></button>');

                    } else if($('#codpagina').val() == 2){

                        $('#modal-facturar').modal('show');
                        if(tipo_cliente == 1){
                            $("#buscar_cliente").val('DNI: '+dni+' | '+nombres);
                        } else if (tipo_cliente == 2){
                            $("#buscar_cliente").val('RUC: '+ruc+' | '+razon_social);
                        }
                        
                        $('.opcion-cliente').html('<a class="input-group-prepend" href="javascript:void(0)"'
                            +'onclick="editar_cliente('+data.id_cliente+');" data-original-title="Editar cliente" data-toggle="tooltip"'
                            +'data-placement="top">'
                                +'<span class="input-group-text bg-header">'
                                    +'<small><i class="fas fa-user text-info"></i></small>'
                               +'</span>'
                            +'</a>');

                        $("#btn-submit-facturar").removeAttr('disabled');
                        $("#btn-submit-facturar").removeClass('disabled');
                    } else {

                        if(tipo_cliente == 1){
                            $("#buscar_cliente").val('DNI: '+dni+' | '+nombres);
                        } else if (tipo_cliente == 2){
                            $("#buscar_cliente").val('RUC: '+ruc+' | '+razon_social);
                        }
                        
                        $('.opcion-cliente').html('<a class="input-group-prepend" href="javascript:void(0)"'
                            +'onclick="editar_cliente('+data.id_cliente+');" data-original-title="Editar cliente" data-toggle="tooltip"'
                            +'data-placement="top">'
                                +'<span class="input-group-text bg-header">'
                                    +'<small><i class="fas fa-user text-info"></i></small>'
                               +'</span>'
                            +'</a>');
                        
                    }

                }
                
            }
        });
        return false;
    });
});

var editar_cliente = function(id_cliente){
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
            $('#fecha_nac').val(moment(campo.fecha_nac).format('DD-MM-Y'));
            $('#telefono').val(campo.telefono);
            $('#correo').val(campo.correo);
            $('#razon_social').val(campo.razon_social);
            $('#direccion').val(campo.direccion);
            $('#referencia').val(campo.referencia);
            $('.modal-title-cliente').text('Editar Cliente');
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
        $('#modal-cliente').modal('show');
    })
    .fail(function(){
        swal('Oops...', 'Problemas con la conexión a internet!', 'error');
    });
}

var nuevo = function(){
    if($('#codcomision').val() == 1){
        $("#td_dni").attr('checked', false);
        $("#td_ruc").attr('checked', false);
        $(".block01").css("display","none");
        $(".block02").css("display","none");
        $(".block03").css("display","none");
        $(".block04").css("display","none");
        $(".block05").css("display","none");
        $(".block06").css("display","none");
        $(".block07").css("display","none");
        $(".block08").css("display","none");
    } 
    
    if($('#cliente_tipo').val() != ''){
        if($('#cliente_tipo').val() == 1){
            $('#tipo_cliente').val(1);
            $(".block01").css("display","block");
            $(".block02").css("display","none");
            $(".block03").css("display","block");
            $(".block04").css("display","block");
            $(".block05").css("display","block");
            $(".block06").css("display","block");
            $(".block07").css("display","none");
            $(".dni").prop('disabled', false);
            $(".ruc").prop('disabled', true);
            $('#form-cliente').formValidation('resetForm', true);
            $('#modal-cliente').modal('show');
        } else if($('#cliente_tipo').val() == 2){
            $('#tipo_cliente').val(2);
            $(".block01").css("display","none");
            $(".block02").css("display","block");
            $(".block03").css("display","none");
            $(".block04").css("display","none");
            $(".block05").css("display","none");
            $(".block06").css("display","none");
            $(".block07").css("display","block");
            $(".dni").prop('disabled', true);
            $(".ruc").prop('disabled', false);
            $('#form-cliente').formValidation('resetForm', true);
            $('#modal-cliente').modal('show');
        } else {
            //$('#modal-cliente').modal('show');
        }
    }
}

/* Nuevo Cliente */
var nuevoCliente = function(){
    nuevo();
}

var nuevo_cliente = function() {
    $('#id_cliente').val('');
    $('#tipo_cliente').val(1);
    $('#telefono').val($('#telefono_cliente').val());
    $('.modal-title-cliente').text('Nuevo Cliente');
    $('#modal-cliente').modal('show');
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
};

/* INICIO SRTA KERLYN */
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
    $(".block08").css("display","block");
    $(".dni").prop('disabled', false);
    $(".ruc").prop('disabled', true);
    $('#form-cliente').formValidation('resetForm', true);
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
    $(".block08").css("display","block");
    $(".dni").prop('disabled', true);
    $(".ruc").prop('disabled', false);
    $('#form-cliente').formValidation('resetForm', true);
});

/* FIN SRTA KERLYN */

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
            $('#form-cliente').formValidation('revalidateField', 'nombres');
            $('#form-cliente').formValidation('revalidateField', 'ape_paterno');
            $('#form-cliente').formValidation('revalidateField', 'ape_materno');
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
        $('#form-cliente').formValidation('resetForm', true);
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
            $('#form-cliente').formValidation('revalidateField', 'razon_social');
            $('#form-cliente').formValidation('revalidateField', 'direccion');
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
        $('#form-cliente').formValidation('resetForm', true);
    }
});

$('#modal-cliente').on('hidden.bs.modal', function() {
    $(this).find('#form-cliente')[0].reset();
    $('#form-cliente').formValidation('resetForm', true);
    //$('#modal-facturar').modal('show');
});

/* Boton limpiar datos del cliente (modal) */
$("#btnClienteLimpiar").click(function() {
    $("#cliente_id").val('');
    $('#id_cliente').val('');
    $("#buscar_cliente").val('');
    $("#buscar_cliente").focus();
    $('.opcion-cliente').html('<a class="input-group-prepend" href="javascript:void(0)"'
        +'onclick="nuevoCliente();" data-original-title="Registrar nuevo cliente" data-toggle="tooltip"'
        +'data-placement="top">'
            +'<span class="input-group-text bg-header">'
                +'<small><i class="fas fa-user-plus"></i></small>'
           +'</span>'
        +'</a>');
});