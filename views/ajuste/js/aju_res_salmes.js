$(function() {
    $('#config').addClass("active");
    listarSalones();
    
});
const contenedor_salon = document.getElementById('modal_add_salon')

/* Mostrar datos en la tabla salones */
var listarSalones = function(){

    function filterGlobal () {
        $('#table01').DataTable().search( 
            $('#global_filter_01').val()
        ).draw();
    }
   
    var table = $('#table01')
    .DataTable({
        "destroy": true,
        "responsive": true,
        "dom": "tip",
        "bSort": true,
        "order": true,
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"ajuste/salon_list"
        },
        "columns":[
            {"data":"descripcion"},
            {"data":"Mesas.total"},
            {"data":null,"render": function ( data, type, row) {
                if(data.estado == 'a'){
                  return '<span class="label label-success">ACTIVO</span>';
                } else if (data.estado == 'i'){
                  return '<span class="label label-danger">INACTIVO</span>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="text-right"><a href="javascript:void(0)" class="text-success edit" onclick="listarMesas('+data.id_salon+',\''+data.descripcion+'\');"><i data-feather="eye" class="feather-sm fill-white"></i></a>'
                    +'&nbsp;<a href="javascript:void(0)" class="text-info edit ms-2" onclick="editarSalon('+data.id_salon+',\''+data.descripcion+'\',\''+data.abreviatura+'\',\''+data.estado+'\');"><i data-feather="edit" class="feather-sm fill-white"></i></a>'
                    +'&nbsp;<a href="javascript:void(0)" class="text-danger delete ms-2" onclick="eliminarSalon('+data.id_salon+',\''+data.descripcion+'\');"><i data-feather="trash-2" class="feather-sm fill-white"></i></a>'
                    +'&nbsp;<a href="javascript:void(0)" class="text-danger delete ms-2" onclick="listar_usuarios('+data.id_salon+',\''+data.descripcion+'\');"><i class="fa fa-users"></i> </i></a>'
                    +'&nbsp;<a href="javascript:void(0)" id="btn_add_priv-'+data.id_salon+'" class="text-success" data-net="'+data.estado+'" onclick="persona_cargo('+data.id_salon+',\''+data.descripcion+'\');"><i class="fa fa-user-plus"></i> </a></div>';
            }}
        ]
    });
   
    $('input.global_filter_01').on( 'keyup click', function () {
        filterGlobal();
    });

    $('#table01').DataTable().on("draw", function(){
        feather.replace();
    });
}
var eliminar_u_salon = (id_usuario, id_salon)=>{
    Swal.fire({
        title: 'Estás seguro?',
        text: "Se le quitará el acceso al mozo a este salón!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, estoy seguro!'
      }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: $("#url").val() + "ajuste/borrar_acceso",
                data: {
                    id_usu : id_usuario,
                    id_salon : id_salon
                },
                success: function (response) {
                    console.log(response)
                    if(response == 1){
                        Swal.fire("Correcto","Se ha eliminado el permiso", "success").then(()=>{
                            window.location.reload();
                        })
                    }else{
                        Swal.fire("Error",response, "success").then(()=>{
                           
                        })
                    }
                }
            });
        }
      })
}
var listar_usuarios = (id_salon, nombre)=>{
    $.ajax({
        type: "POST",
        url: $("#url").val() + "ajuste/listar_usuarios_en_salon",
        data: {id_salon : id_salon},
        dataType: "json",
        success: function (response) {
            console.log(response)
            $("#dtp_ped").empty()
            for(var x = 0; x <  response.data.length; x++) {
                $("#dtp_ped").append(
                    $('<div class="d-flex pt-2 flex-row comment-row comment-list"/>')
                        .append('<div class="comment-text w-100 p-0 m-b-10n"><span style="display: inline-block;">'
                        +'<h6 class="m-b-5">'+response.data[x].nombre+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-trash fa-2x" style="color: red;" onclick="eliminar_u_salon('+response.data[x].id_usu+','+id_salon+')" aria-hidden="true"></i></span></h6>'
                        +'</div>')
                )
                
            }
            $("#myModal").modal("show");
        //     $("#dtp_ped").append(
        //         $('<div class="d-flex flex-row comment-row comment-list"/>')
        //    .append('<div class="comment-text w-100 p-0 m-b-10n"><span style="display: inline-block;">'
        //    +'<h6 class="m-b-5">'+item.Producto.pro_nom+' <span class="label label-warning">'+item.Producto.pro_pre+'</span></h6>'
        //    +'<p class="m-b-0 font-13">'+item.cantidad+' Unidad(es) en '+moneda+' '+formatNumber(item.precio)+' | Unidad</p></span>'
        //    +'<span class="price">'+moneda+' '+formatNumber(total)+'</span></div>'));                  
        //    
              
        }
    });

  
}
var listar_personal = (id_salon)=>{
    $('.container_select').empty()
    $.ajax({
        type: "POST",
        url: $("#url").val() + "ajuste/listar_personal",
        data: {
            id_salon : id_salon
        },
        dataType: "json",
        success: function (users) {
            console.log(users)
            var registrados = []
            for(var x = 0; x < users.data.length; x++) {
                    for(var y = 0; y < users.data[x].length; y++) {
                        if(users.data[x][y].ST == 1){
                           registrados.push(users.data[x][y].id_usu)
                        }
                    }
            }
            
            for(var x = 0; x < users.mozos.length; x++) {
                var exist = false
                for(y = 0; y < registrados.length; y++){
                    if(users.mozos[x].id_usu == registrados[y]){
                        exist = true
                    }
                }
                if(exist == false){
                    $('.container_select').append( '<option value="'+users.mozos[x].id_usu+'">'+'Mozo: '+users.mozos[x].nombre+'</option>');
                }
            }     
        }
    });
}
var listar_mozos = () =>{
    console.log("Listar mozos")
    $.ajax({
        type: "POST",
        url: $("#url").val() + "ajuste/Mozo1",
        success: function (response) {
            console.log(response)
        }
    });
}
var persona_cargo = function(id_salon, nombre){
    var el = document.getElementById('btn_add_priv-'+id_salon);
    if(el.dataset.net != 'i'){
        $("#title_modal_sal").html("Agregar mozo a el salon llamado <b>"+nombre+"</b>")
        $("#modal_add_salon").modal("show");
        listar_personal(id_salon)
        contenedor_salon.dataset.id_salon = id_salon; 
        contenedor_salon.dataset.nombre_salon = nombre;
    }else{
        Swal.fire("Debes activar este salon para poder añadir usuarios")
    }

}
$("#success_add_salon").on("click", function(){
    var id_salon = contenedor_salon.dataset.id_salon
    var nombre_salon = contenedor_salon.dataset.nombre_salon
    var id_usu = $("#id_mozo").val()
    
    if(id_usu != "" && nombre_salon !="" && id_salon !=""){
        $.ajax({
            type: "POST",
            url: $("#url").val() + "ajuste/add_rol_salon",
            data: {
                id_salon: id_salon,
                id_usu: id_usu
            },
            success: function (response) {
               if(response == true){
                   Swal.fire("Usuario añadido!", "Se ha añadido el usuario al salón " + nombre_salon, "success").then(()=>{
                       window.location.reload();
                   })
               }else{
                   Swal.fire("Error al ingresar rol", response, "error").then(()=>{
                    window.location.reload();
                   })
               }
            }
        });
    }else{
        swal.fire("Notificacíón","Favor de seleccionar un usuario", "error")
    }

})
/* Mostrar datos en la tabla mesas */
var listarMesas = function(id_salon,descripcion){
    var mesaNueva = '';
    /* Ocultar panel mensaje 'seleccione un salon' */
    $('#lizq-s').css("display","none");
    /* Mostrar tabla mesas por salon */
    $('#lizq-i').css("display","block");
    $('#btn-nuevo').html('<button type="button" class="btn btn-circle btn-lg btn-orange waves-effect waves-dark" onclick="editarMesa('+mesaNueva+');"><i class="ti-plus"></i></button>');
    $('#id_salon_1').val(id_salon);
    $('#title-mesa').text(descripcion);

    function filterGlobal () {
        $('#table02').DataTable().search( 
            $('#global_filter_02').val()
        ).draw();
    }

    var table = $('#table02')
    .DataTable({
        "destroy": true,
        "responsive": true,
        "dom": "tip",
        "bSort": true,
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"ajuste/mesa_list",
            "data": { id_salon : id_salon }
        },
        "columns":[
            {"data":"nro_mesa"},
            {"data":"Salon.descripcion"},
            {"data":null,"render": function ( data, type, row) {
                if(data.estado == 'a'){
                  return '<a onclick="estadoMesa('+data.id_mesa+');"><span class="label label-success">ACTIVO</span></a>';
                } else if (data.estado == 'i' || data.estado == 'p'){
                  return '<span class="label label-warning">OCUPADO</span>'
                }
                else if (data.estado == 'm'){
                  return '<a onclick="estadoMesa('+data.id_mesa+');"><span class="label label-danger">INACTIVO</span></a>'
                } 
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="text-right"><a href="javascript:void(0)" class="text-info edit" onclick="editarMesa('+data.id_mesa+',\''+data.nro_mesa+'\',\''+data.estado+'\');"><i data-feather="edit" class="feather-sm fill-white"></i></a>'
                    +'&nbsp;<a href="javascript:void(0)" class="text-danger delete ms-2" onclick="eliminarMesa('+data.id_mesa+',\''+data.nro_mesa+'\');"><i data-feather="trash-2" class="feather-sm fill-white"></i></a></div>';
            }}
        ]
    });

    $('input.global_filter_02').on('keyup click', function () {
        filterGlobal();
    });

    $('#table02').DataTable().on("draw", function(){
        feather.replace();
    });
}

/* Editar datos del salon */
var editarSalon = function(id_salon,descripcion,abreviatura,estado){
    $(".f").addClass("focused");
    $('#id_salon').val(id_salon);
    $('#descripcion').val(descripcion);
    $('#abreviatura').val(abreviatura);
    $('#estado').selectpicker('val', estado);    
    $("#modal01").modal('show');
}

var eliminarSalon = function(id_salon,descripcion){
    var html_confirm = '<div>Se eliminará el siguiente salón:<br>'+descripcion+'</div>\
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
                    url: $('#url').val()+'ajuste/salon_crud_delete',
                    type: 'POST',
                    data: {id_salon: id_salon},
                    dataType: 'json'
                })
                .done(function(cod){
                    if(cod == 1){
                        listarSalones();
                        $('#table02 tbody').remove();
                        $('#lizq-s').css("display","block");
                        $('#lizq-i').css("display","none");
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
                    } else if(cod == 0){
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
                })
                .fail(function(){
                    Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
                });
            });
        },
        allowOutsideClick: false              
    });
}

/* Editar datos de la mesa*/
var editarMesa = function(id_mesa,nro_mesa,estado){
    $(".f").addClass("focused");
    $('#id_mesa').val(id_mesa);
    $('#nro_mesa').val(nro_mesa);    
    $('#estado_1').selectpicker('val', estado); 
    $("#modal02").modal('show');
}

/* Eliminar mesa */
var eliminarMesa = function(id_mesa,nro_mesa){
    var html_confirm = '<div>Se eliminará la sigueinte mesa:<br>'+nro_mesa+'</div>\
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
                    url: $('#url').val()+'ajuste/mesa_crud_delete',
                    type: 'POST',
                    data: {
                        id_salon: $('#id_salon_1').val(),
                        id_mesa: id_mesa
                    },
                    dataType: 'json'
                })
                .done(function(cod){
                    if(cod == 1){
                        listarSalones();
                        listarMesas($('#id_salon_1').val());
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
                    } else if(cod == 0){
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
                })
                .fail(function(){
                    Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
                });
            });
        },
        allowOutsideClick: false              
    });
}
var listar_mozos = ()=>{

}

$(function() {
    $('#form01')
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

          var salones = {
            id_salon: 0,
            descripcion: 0,
            abreviatura: 0,
            estado: 0
          }

          salones.id_salon = $('#id_salon').val();
          salones.descripcion = $('#descripcion').val();
          salones.abreviatura = $('#abreviatura').val();
          salones.estado = $('#estado').val();

          $.ajax({
              dataType: 'JSON',
              type: 'POST',
              url: $('#url').val()+'ajuste/salon_crud',
              data: salones,
              success: function (cod) {
                    if(cod == 0){
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
                    } else if(cod == 1){
                        listarSalones();
                        $('#modal01').modal('hide');
                        $('#title-mesa').text(salones.descripcion);
                        $('#table02 tbody').remove();
                        /* Mostrar panel mensaje 'seleccione un salon' */
                        $('#lizq-s').css("display","block");
                        /* Ocultar tabla mesas */
                        $('#lizq-i').css("display","none");
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
                    } else if(cod == 2) {
                        listarSalones();
                        $('#modal01').modal('hide');
                        $('#title-mesa').text(salones.descripcion);
                        $('#table02 tbody').remove();
                        /* Mostrar panel mensaje 'seleccione un salon' */
                        $('#lizq-s').css("display","block");
                        /* Ocultar tabla mesas */
                        $('#lizq-i').css("display","none");
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

    $('#form02')
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

          var mesas = {
            id_mesa: 0,
            id_salon: 0,
            nro_mesa: 0,
            estado: 0
          }

          mesas.id_mesa = $('#id_mesa').val();
          mesas.id_salon = $('#id_salon_1').val();
          mesas.nro_mesa = $('#nro_mesa').val();
          mesas.estado = $('#estado_1').val();

          $.ajax({
              dataType: 'JSON',
              type: 'POST',
              url: $('#url').val()+'ajuste/mesa_crud',
              data: mesas,
              success: function (cod) {
                  if(cod == 0){
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
                  } else if(cod == 1){
                    $('#modal02').modal('hide');
                    listarSalones();
                    listarMesas(mesas.id_salon);
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
                  } else if(cod == 2) {
                    $('#modal02').modal('hide');
                    listarSalones();
                    listarMesas(mesas.id_salon);
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

$('#modal01').on('hidden.bs.modal', function() {
    $(this).find('form')[0].reset();
    $('#form01').formValidation('resetForm', true);
    $("#estado").selectpicker('val', 'a');
});

$('#modal02').on('hidden.bs.modal', function() {
    $(this).find('form')[0].reset();
    $('#form02').formValidation('resetForm', true);
    $("#estado_1").selectpicker('val', 'a');
});