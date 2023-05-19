$(function() {
	$('#form-proveedor').formValidation({
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

        e.preventDefault();
        var $form = $(e.target),
        fv = $form.data('formValidation');

        var ruc = $('#ruc').val(),
			razon_social = $('#razon_social').val(),
			direccion = $('#direccion').val(),
			telefono = $('#telefono').val(),
			email = $('#email').val(),
			contacto = $('#contacto').val();

		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: {
				ruc : ruc,
				razon_social : razon_social,
				direccion : direccion,
				telefono : telefono,
				email : email,
				contacto : contacto
			},
			url: $('#url').val()+'compra/compra_proveedor_nuevo',
			success: function(data){
				if(data.cod == 1){
					Swal.fire({   
                        title:'Proceso No Culminado',   
                        text: 'Datos duplicados',
                        icon: "error", 
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "Aceptar",
                        allowOutsideClick: false,
                        showCancelButton: false,
                        showConfirmButton: true
                    }, function() {
                        return false
                    });
				}else {
					$('#id_prov').val(data.id_prov);
                    $('#datos_proveedor').val(razon_social);
                    $('#modal-proveedor').modal('hide');
				}
			}
		});
        return false;
    });
});

/* Nuevo Proveedor */
var nuevoProveedor = function(){
    $('#modal-proveedor').modal('show');
}

/* Consultar ruc del nuevo cliente */
$("#ruc").keyup(function(event) {
    var that = this,
    value = $(this).val();
    if (value.length == $("#ruc").attr("maxlength")) {
        $.getJSON("https://dniruc.apisperu.com/api/v1/ruc/"+$("#ruc").val()+"?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InRvbW15ZGVsZ2Fkb3JvZHJpZ3VlekBnbWFpbC5jb20ifQ.ZUOOpJcgrZ2zDZuNO3OoBC8ViItUZLn3zixVsWMeq8c", {
            format: "json"
        })
        .done(function(data) {
            $("#ruc").val(data.ruc);
            $("#razon_social").val(data.razonSocial);
            $("#direccion").val(data.direccion);
            $('#form-proveedor').formValidation('revalidateField', 'razon_social');
            $('#form-proveedor').formValidation('revalidateField', 'direccion');
        });
    } else if($("#ruc").val() == "") {
        $('#ruc').val("");
        $('#razon_social').val("");
        $('#direccion').val("");
        $('#telefono').val("");
        $('#email').val("");
        $('#contacto').val("");
        $('#form-proveedor').formValidation('resetForm', true);
    }
});

$('#modal-proveedor').on('hidden.bs.modal', function() {
	//$('#ruc_numero').val('');
    $(this).find('form')[0].reset();
    $('#form-proveedor').formValidation('resetForm', true);
});