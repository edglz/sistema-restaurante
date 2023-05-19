$(function() {
    $('#config').addClass("active");
    listarTiposDoc();
    $('#form')
      .formValidation({
          framework: 'bootstrap',
          excluded: ':disabled',
          fields: {
          }
      })
      .on('success.form.fv', function(e) {

            e.preventDefault();
            var $form = $(e.target),
            fv = $form.data('formValidation');
            
            id_tipo_doc = $('#id_tipo_doc').val();
            serie = $('#serie').val();
            numero = $('#numero').val();
            estado = $('#estado').val();

            $.ajax({
                dataType: 'JSON',
                type: 'POST',
                url: 'tipodoc_crud',
                data: {
                    id_tipo_doc: id_tipo_doc,
                    serie: serie,
                    numero: numero,
                    estado: estado
                },
                success: function (datos) {
                    $('#modal').modal('hide');
                    Swal.fire({   
                        title:'Proceso Terminado',   
                        text: 'Datos actualizados correctamente',
                        icon: "success", 
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "Aceptar",
                        allowOutsideClick: false,
                        showCancelButton: false,
                        showConfirmButton: true
                    }, function() {
                        return false
                    });
                    listarTiposDoc();
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(errorThrown + ' ' + textStatus);
                }   
            });

          return false;
    });
});

/* Mostrar datos en la tabla tipo de documentos */
var listarTiposDoc = function(){
    var table = $('#table')
    .DataTable({
        "destroy": true,
        "responsive": true,
        "dom": "tip",
        "bSort": true,
        "ajax":{
            "method": "POST",
            "dataType": "JSON",
            "url": $('#url').val()+"ajuste/tipodoc_list"
        },
        "columns":[
            {"data":"descripcion"},
            {"data":"serie"},
            {"data":"numero"},
            {"data":null,"render": function ( data, type, row) {
              if(data.estado == 'a'){
                return '<span class="label label-success">ACTIVO</span>';
              } else if (data.estado == 'i'){
                return '<span class="label label-danger">INACTIVO</span>'
              }
            }},
            {"data":null,"render": function ( data, type, row ) {
                feather.replace();
                return '<div class="text-right"><a href="javascript:void(0)" class="text-info edit" onclick="editar('+data.id_tipo_doc+',\''+data.descripcion+'\',\''+data.serie+'\',\''+data.numero+'\',\''+data.estado+'\');"><i data-feather="edit" class="feather-sm fill-white"></i></a></div>';
            }}
        ]
    });
}

/* Editar datos del tipo de documento */
function editar(id_tipo_doc,descripcion,serie,numero,estado){
    $(".f").addClass("focused");
    $('#id_tipo_doc').val(id_tipo_doc);
    $('#serie').val(serie);
    $('#numero').val(numero);
    $('#estado').selectpicker('val', estado);
	$(".modal-title").html("<center>" + descripcion + "</center>");        
	$("#modal").modal('show');
}

$('#modal').on('hidden.bs.modal', function() {
    $(this).find('form')[0].reset();
    $('#form').formValidation('resetForm', true);
    $('#estado').selectpicker('val', 'a');
});
