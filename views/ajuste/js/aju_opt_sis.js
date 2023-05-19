$(function() {
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    $('.js-switch').each(function() {
        new Switchery($(this)[0], $(this).data());
    });
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
            url: $('#url').val()+'ajuste/datosistema_crud',
            type: 'POST',
            data: parametros,
            dataType: 'json',
            contentType: false,
            processData: false,
         })
         .done(function(response){
            var html_terminado = '<div>Datos actualizados correctamente</div>\
                <br><a href="'+$('#url').val()+'ajuste/sistema" class="btn btn-success">Aceptar</button>'
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
        url: $('#url').val()+"ajuste/datosistema_data",
        dataType: "json",
        success: function(item){
            $('#zona_hora').val(item.zona_hora);
            $('#trib_acr').val(item.trib_acr);
            $('#trib_car').val(item.trib_car);
            $('#di_acr').val(item.di_acr);
            $('#di_car').val(item.di_car);            
            $('#imp_acr').val(item.imp_acr);
            $('#imp_val').val(item.imp_val);
            $('#mon_acr').val(item.mon_acr);
            $('#mon_val').val(item.mon_val);           
            $('#pc_name').val(item.pc_name);           
            $('#pc_ip').val(item.pc_ip);   
            $('#print_com_hidden').val(item.print_com);   
            $('#print_pre_hidden').val(item.print_pre);
            $('#print_cpe_hidden').val(item.print_pre);
            if(item.print_com == '1'){$('#print_com').prop('checked', true)};
            if(item.print_pre == '1'){$('#print_pre').prop('checked', true)};
            if(item.print_cpe == '1'){$('#print_cpe').prop('checked', true)};
        }
    });
}

$('#print_com').on('change', function(event){
    if($(this).prop('checked')){
        $('#print_com_hidden').val('1');
    }else{
        $('#print_com_hidden').val('0');
    }
});

$('#print_pre').on('change', function(event){
    if($(this).prop('checked')){
        $('#print_pre_hidden').val('1');
    }else{
        $('#print_pre_hidden').val('0');
    }
});
$('#print_cpe').on('change', function(event){
    if($(this).prop('checked')){
        $('#print_cpe_hidden').val('1');
    }else{
        $('#print_cpe_hidden').val('0');
    }
});