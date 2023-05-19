$(function() {
    obtenerDatos();
    $('#config').addClass("active");
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

        var parametros = new FormData($('#form')[0]);

        $.ajax({
            url: $('#url').val()+'ajuste/datosempresa_crud',
            type: 'POST',
            data: parametros,
            dataType: 'json',
            contentType: false,
            processData: false,
         })
         .done(function(response){
            var html_terminado = '<div>Datos actualizados correctamente</div>\
                <br><a href="'+$('#url').val()+'ajuste/datosempresa" class="btn btn-success">Aceptar</button>'
            Swal.fire({
                title: 'Proceso Terminado',
                html: html_terminado,
                icon: 'success',
                showConfirmButton: false
            });
            obtenerDatos();
        })
        .fail(function(){
            swal('Oops...', 'Problemas con la conexión a internet!', 'error');
        });
    });
});

var obtenerDatos = function(){
    $.ajax({
        type: "POST",
        url: $('#url').val()+"ajuste/datosempresa_data",
        dataType: "json",
        success: function(item){
            if($('#usuid').val() == 1){
                $('.sunat').show();
                $(".sunat").prop('disabled', false);
            } else {
                $('.sunat').hide();
                $(".sunat").prop('disabled', true);
            }
            $('#tribAcr').val(item.trib_acr);
            $('#ruc').val(item.ruc);
            $('#razon_social').val(item.razon_social);
            $('#wizardPicturePreview-2').attr('src',$("#url").val()+'public/images/'+item.logo+'');
            $('#imagen').val(item.logo);
            $('#nombre_comercial').val(item.nombre_comercial);
            $('#direccion_comercial').val(item.direccion_comercial);
            $('#direccion_fiscal').val(item.direccion_fiscal);
            $('#celular').val(item.celular);        
            $('#ubigeo').val(item.ubigeo);        
            $('#departamento').val(item.departamento);        
            $('#provincia').val(item.provincia);        
            $('#distrito').val(item.distrito);       
            $('#usuariosol').val(item.usuariosol);       
            $('#clavesol').val(item.clavesol);       
            $('#clavecertificado').val(item.clavecertificado);
            $('#sunat_hidden').val(item.sunat);   
            $('#modo_hidden').val(item.modo);
            if(item.sunat == '1'){$('#sunat').prop('checked', true)};
            if(item.modo == '1'){$('#modo').prop('checked', true)};       
        }
    });
}

$('#sunat').on('change', function(event){
    if($(this).prop('checked')){
        $('#sunat_hidden').val('1');
    }else{
        $('#sunat_hidden').val('0');
    }
});

$('#modo').on('change', function(event){
    if($(this).prop('checked')){
        $('#modo_hidden').val('1');
    }else{
        $('#modo_hidden').val('3');
    }
});

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
            $("#nombre_comercial").val(data.nombreComercial);
            $("#direccion_fiscal").val(data.direccion);
            $("#celular").val(data.telefonos);
            //$("#ubigeo").val(data.ubigeo);
            $("#departamento").val(data.departamento);
            $("#provincia").val(data.provincia);
            $("#distrito").val(data.distrito);
            $('#form').formValidation('revalidateField', 'razon_social');
            $('#form').formValidation('revalidateField', 'nombreComercial');
            $('#form').formValidation('revalidateField', 'direccion');
            $('#form').formValidation('revalidateField', 'telefonos');
            $('#form').formValidation('revalidateField', 'departamento');
            $('#form').formValidation('revalidateField', 'provincia');
            $('#form').formValidation('revalidateField', 'distrito');
        });
    }
});
