$(function () {
   listarCategorias(); 
   listarRecetas("%");
});
var listarCategorias = function(){
    $('#ul-cat').empty();
    $.ajax({
        type: "POST",
        url: $('#url').val()+"receta/lista_categorias",
        dataType: "json",
        success: function(item){
            if(item.data.length > 0){
                $.each(item.data, function(i, campo) {
                    $('#ul-cat')
                        .append(
                        $('<li/>')
                            .html('<a href="javascript:void(0)" class="link" onclick="listarRecetas('+campo.id_catg+')">'+campo.nombre+''
                            +'<span><i data-feather="edit" class="feather-sm fill-white" onclick="editarCategoria('+campo.id_catg+',\''+campo.nombre+'\',\''+campo.estado+'\',\''+campo.imagen+'\')"></i>'
                            +'&nbsp;<i data-feather="trash-2" class="feather-sm fill-white" onclick="eliminarCategoria('+campo.id_catg+')"></i>&nbsp;</span></a>')
                        )
                });
                feather.replace();
            }else{
                $('#ul-cat').html("<center><br><br><br><i class='mdi mdi-alert-circle display-3' style='color: #d3d3d3;'></i><br><br><span class='font-18' style='color: #d3d3d3;'>No hay datos disponibles</span><br></center>");
            }
        }
    });
}
/* Editar categoria */
var editarCategoria = function(id_catg,nombre,estado,imagen){
    $(".f").addClass("focused");
    $("#id_catg_categoria").val(id_catg);
    $("#descripcion_categoria").val(nombre);
    $('#wizardPicturePreview-2').attr('src',$("#url").val()+'public/images/productos/'+imagen+'');
    $('#imagen').val(imagen);
    $('#wizard-picture-2').val('');
    $('.display-categoria-list').hide();
    $('#display-categoria-nuevo').show();
    $('#form-categoria').formValidation('revalidateField', 'descripcion_categoria');
    if(estado == 'a'){
        $('#estado_categoria').prop('checked', true);
        $('#hidden_estado_categoria').val('a');
    } else {
        $('#estado_categoria').prop('checked', false);
        $('#hidden_estado_categoria').val('i');
    }
}

/* Eliminar categoria */
var eliminarCategoria = function(id_catg){
    $.ajax({
        type: "POST",
        url: $('#url').val()+"receta/elimina_Categoria",
        data: {
            id_catg: id_catg
        },
        dataType: "json",
        success: function(data){
            if(data.msj == 1){
                Swal.fire({   
                    title:'Proceso Terminado',   
                    text: 'Datos eliminados correctamente',
                    icon: "success", 
                    confirmButtonColor: "#34d16e",   
                    confirmButtonText: "Aceptar",
                    allowOutsideClick: false,
                    showCancelButton: false,
                    showConfirmButton: true
                }, function() {
                    return false
                });
                listarCategorias();
            }else if(data.msj == -1){
                Swal.fire({   
                    title:'Proceso No Culminado',   
                    text: 'Datos protegidos',
                    icon: "error", 
                    confirmButtonColor: "#34d16e",   
                    confirmButtonText: "Aceptar",
                    allowOutsideClick: false,
                    showCancelButton: false,
                    showConfirmButton: true
                }, function() {
                    return false
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown + ' ' + textStatus);
        }
    });
}

$(function() {

    $('#form-categoria')
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
        var form = $(this);

        var categoria = new FormData($('#form-categoria')[0]);


        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: $('#url').val()+'receta/receta_cat_crud',
            data: categoria,
            contentType: false,
            processData: false,
            success: function (cod) {
                if(cod.msj == -1){
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
                } else if(cod.msj == 1){
                    var cat = '%';
                    listarCategorias();
                    listarRecetas(cat);
                    $('#descripcion_categoria').val('');
                    $("#id_catg_categoria").val('');
                    $('.display-categoria-list').show();
                    $('#display-categoria-nuevo').hide();
                    Swal.fire({   
                        title:'Proceso Terminado',   
                        text: 'Datos registrados correctamente',
                        icon: "success", 
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "Aceptar",
                        allowOutsideClick: false,
                        showCancelButton: false,
                        showConfirmButton: true
                    }, function() {
                        return false
                    });
                } else if(cod.msj == 2) {
                    var cat = '%';
                    listarCategorias();
                    listarRecetas(cat);
                    $('#descripcion_categoria').val('');
                    $("#id_catg_categoria").val('');
                    $('.display-categoria-list').show();
                    $('#display-categoria-nuevo').hide();
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
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown + ' ' + textStatus);
            }   
        });

    return false;

    });
});
$('.btn-categoria-nuevo').click( function() {
    $(".f").removeClass("focused");
    $('.display-categoria-list').hide();
    $('#display-categoria-nuevo').show();
    $('#descripcion_categoria').val('');
    $("#descripcion_categoria").focus();
    $('#id_catg_categoria').val('');
    $('#estado_categoria').prop('checked', true);
    $('#wizardPicturePreview-2').attr('src',$("#url").val()+'public/images/productos/default.png');
    $('#imagen').val('default.png');
    $('#wizard-picture-2').val('');
});

$('.btn-categoria-cancelar').click( function() {
    $(".f").removeClass("focused");
    $('.display-categoria-list').show();
    $('#display-categoria-nuevo').hide();
    $('#descripcion_categoria').val('');
    $("#descripcion_categoria").focus();
    $('#id_catg_categoria').val('');
});
var listarRecetas = function(id_catg){
    function filterGlobal () {
            $('#table-recetas').DataTable().search( 
                $('#global_filter').val()
            ).draw();
    }
    $('#categoria').val(id_catg);
    $('#head-p').empty();
    $('#body-c').empty();
    $('#body-p').html('<div class="row text-center"><div class="col-sm-10 offset-sm-1"><h4><i class="ti ti-arrow-circle-left"></i><br>Seleccione una receta</h4><h6>Debes agregar o seleccionar una receta para poder imprimir su preparación</h6></div></div>');
    var tx = $('#table-recetas')
    .DataTable({
        "destroy": true,
        "responsive": true,
        "dom": "tp",
        "bSort": false,
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"receta/receta_list",
            "data": {
                id_catg : id_catg
            }
        },
        "columns":[
            {"data":null,"render": function ( data, type, row) {
                return '<a href="javascript:void(0)" class="link">'+data.nombre+'</a>';
            }},
            {"data":null,"render": function ( data, type, row) {
                return '<a href="javascript:void(0)" class="link">'+data.producto+'</a>';
            }},
            
            {"data":null,"render": function ( data, type, row) {
                  return '<i class="ti-calendar"></i> '+moment(data.fecha_creacion).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data.fecha_creacion).format('h:mm A')+'</span>';
            }},
            {"data":null,"render": function ( data, type, row) {
                if(data.estado == 'a'){
                    return '<div class="text-center"><span class="text-navy"><i class="ti-check"></i> Si </span></div>';
                } else if (data.estado == 'i'){
                    return '<div class="text-center"><span class="text-danger"><i class="ti-close"></i> No </span></div>'
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                return `<div class="text-center"><a href="${$("#url").val()}receta/imprimir/${data.id_receta}" target="_blank" class="btn btn-danger ml-2 mt-2"><i class="fa fa-file-pdf"></i></a>
                <button class="btn btn-danger ml-2 mt-2" onclick="elimina_receta(${data.id_receta})"><i class="fa fa-trash"></i></button>
                <a href="${$("#url").val()}receta/edita/${data.id_receta}" class="btn btn-success ml-2 mt-2"><i class="fa fa-edit"></i></a></div>`;
            }}
        ]
    });
    $('input.global_filter').on( 'keyup click', function () {
                filterGlobal();
            });
        
            $('#table-recetas').DataTable().on("draw", function(){
                feather.replace();
    });
}

var listaPreparados = function(){
    var tx = $('#table-preparados')
    .DataTable({
        "destroy": true,
        "responsive": true,
        "dom": "ftp",
        "bSort": false,
        "lengthMenu": [[7, 14, 23, -1], [7, 14, 23, "All"]],
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"receta/list_preparados",
        },
        "columns":[
            {"data":null,"render": function ( data, type, row) {
                return '<a href="javascript:void(0)" class="link">'+data.nombre+'</a>';
            }},
            {"data":null,"render": function ( data, type, row) {
                return '<a href="javascript:void(0)" class="link">'+data.nombre_catg+'</a>';
            }},
            {"data":null,"render": function ( data, type, row) {
                return '<button class="btn btn-info"><i class="ti ti-eye"></i></button>';
            }},
            {"data":null,"render": function ( data, type, row) {
                return '<button class="btn btn-success"><i class="ti ti-plus"></i></button>';
            }}
        ]
    });
}
var elimina_receta = (id) => {
    Swal.fire({
        title : 'Alerta',
        html : '<b>¿Estás seguro de eliminar esta receta?<br>Ya no se podrán revertir cambios.</b>',
        icon: 'warning',
        showCancelButton: true,
        showConfirmButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Eliminar',
        confirmButtonColor: 'red',
    }).then((resp)=>{
        if(resp.value == true){
        
            $.ajax({
                type: "POST",
                url: $("#url").val() + "receta/eliminaReceta/"+id,
                dataType: "json",
                success: function (data) {
                    console.log(data)
                    if(data.msj == 1){
                        Swal.fire({   
                            title:'Proceso Terminado',   
                            text: 'Datos eliminados correctamente',
                            icon: "success", 
                            confirmButtonColor: "#34d16e",   
                            confirmButtonText: "Aceptar",
                            allowOutsideClick: false,
                            showCancelButton: false,
                            showConfirmButton: true
                        }).then(()=>{
                            listarRecetas('%')
                        })
                    }else if(data.msj == -1){
                        Swal.fire({   
                            title:'Proceso No Culminado',   
                            text: 'Error al eliminar',
                            icon: "error", 
                            confirmButtonColor: "#34d16e",   
                            confirmButtonText: "Aceptar",
                            allowOutsideClick: false,
                            showCancelButton: false,
                            showConfirmButton: true
                        }).then(()=>{
                            listarRecetas('%')
                        })
                    }
                }
            });
        }
    })
}