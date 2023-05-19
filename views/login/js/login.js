$(function() {
    // var parametro = getUrlParameter('multimozo');
    // if(parametro !== undefined){ 
    //     $('#wrapper-1').empty();
    //     $('#wrapper-2').show();
    //     $("#f-user").keyup(function () {
    //         var value = $(this).val();
    //         $("#f-pass").val(value);
    //     });
    // } else {
    //     $('#wrapper-1').show();
    //     $('#wrapper-2').empty();
    // }
    $('#frm-login').formValidation({
        framework: 'bootstrap',
        excluded: ':disabled',
        fields: {
        }
    }).on('success.form.fv', function(e) {
    	e.preventDefault();
        var $form = $(e.target),
        fv = $form.data('formValidation');
        var form = $(this);

        var parametros = new FormData($('#frm-login')[0]);

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: parametros,
            url: $('#url').val()+'login/run',
            contentType: false,
            processData: false,
            
        })
        .done(function(response){
            console.log(response)
        	if(response == 1){
        		window.open('/tablero','_self');
        	} else if (response == 2) {
        		window.open('produccion','_self');
        	} else if (response == 3) {
        		window.open('/venta','_self');
        	}else if(response == 7) {
                window.open('venta/venta_portero', '_self');
            }else {
	            $.toast({
	                heading: 'Acceso denegado!',
	                text: 'Datos erroneos.',
	                position: 'top-rigth',
	                loaderBg:'#696969',
	                icon: 'error',
	                hideAfter: 3000, 
	                stack: 20
	            });
	            $('#frm-login').formValidation('resetForm', true);
                $('#f-pass').val('');
        	}
        })
        .fail(function(){
            Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
        });
    });

    $(".virtual-keyboard button").on('click', function() {
        if ($(this).attr('data') == 'DEL') {
            board_text = $('#f-user').val();
            board_text = board_text.substring(0, board_text.length-1);
            $('#f-user').val(board_text);
            $('#f-pass').val(board_text);
        } else {
            $('#f-user').val($('#f-user').val() + $(this).attr('data'));
            $('#f-pass').val($('#f-pass').val() + $(this).attr('data'));
        }
        $('#frm-login').formValidation('revalidateField', 'usuario');
    });
})