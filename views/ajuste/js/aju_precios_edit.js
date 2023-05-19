$(function () {
    var id_catg = '%';
    listarCategorias();
    listarProductos(id_catg);
    const URL = $("#url").val()
    $("#cover-spin").show();
    $("#alert_reload").hide()
})
const frm = document.getElementById("frm_prec");
var c = 0
var ajaxResponse = null
var ajaxObj = null
const frm_edit = document.getElementById("frm_edit");
const btn_update = document.getElementById("btn_change");
/* Mostrar datos en la lista categorias */
var listarCategorias = function () {
    $('#ul-cat').empty();
    $.ajax({
        type: "POST",
        url: $('#url').val() + "ajuste/producto_cat_list",
        dataType: "json",
        success: function (item) {
            if (item.data.length > 0) {
                $.each(item.data, function (i, campo) {
                    $("#s_categoria").append("<option value="+campo.id_catg+">"+campo.descripcion+"</option>")
                    $("#frm_select_categoria").append("<option value="+campo.id_catg+">"+campo.descripcion+"</option>")
                    $('#ul-cat')
                        .append(
                            $('<li/>')
                                .html('<a href="javascript:void(0)" class="link" onclick="listarProductos(' + campo.id_catg + ')">' + campo.descripcion + '')
                        )
                });
                feather.replace();
            } else {
                $('#ul-cat').html("<center><br><br><br><i class='mdi mdi-alert-circle display-3' style='color: #d3d3d3;'></i><br><br><span class='font-18' style='color: #d3d3d3;'>No hay datos disponibles</span><br></center>");
            }
        }
    });
}
/* Mostrar datos en la tabla productos */
var listarProductos = function (id_catg) {
    $('#categoria').val(id_catg);
    var contador = null
    $('#head-p').empty();
    $('#body-c').empty();
    let response = null
    var table = $('#table-productos')
        .DataTable({
            "destroy": true,
            "responsive": true,
            "dom": "tp",
            "bSort": false,
            "ajax": {
                "method": "POST",
                "url": $('#url').val() + "ajuste/select_Products_byCategory",
                "data": {
                    id_catg: id_catg
                }
            },
            "columns": [
                {
                    "data": null, "render": function (data, type, row) {
                        return '<div class="text-center"><span class="text-navy"><b><i>' + data.nombre + '</i></b></span>ﾠﾠ<span class="badge badge-success">' + data.notas + '</span></div>';
                    }
                },
                {
                    "data": null, "render": function (data, type, row) {

                        return '<div class="text-center"><span class="text-">' + 'S/ ' + data.precio + ' </span></div>';

                    }
                },
                {
                    "data": null, "render": function (data, type, row) {
                        return '<div class="text-center"><a href="javascript:void(0)" class="text-success" onclick="agregar_precio(' + data.id_pres + ',\'' + data.nombre + '\')"><i class="fas fa-plus-circle fa-2x"></i></a>'
                            + '</div>';
                    }
                },
                {
                    "data": null, "render": function (data, type, row) {
                        return '<div class="text-center"><a href="javascript:void(0)" class="text-secondary" onclick="listarPrecios(' + data.id_pres + ',\'' + data.nombre + '\');"><i class="fas fa-eye fa-2x"></i></a></div>';

                    }
                }
            ]
            
        });
    $('#table-productos').DataTable().on("draw", function () {
        feather.replace();
        $("#cover-spin").hide();
    });
}
//Listar precios y mostrarlos en pantalla
var listarPrecios = function (id_pres, nombreProducto) {
    $(".product_title").html(nombreProducto)
    $('#ul_precios').empty();
    $.ajax({
        type: "POST",
        url: $('#url').val() + "ajuste/seleccionar_precios",
        data: {
            id_pres: id_pres
        },
        dataType: "json",
        success: function (item) {
            if (item != null) {
                if (item.data.length > 0) {
                    var title = "Editar Precio";
                    $.each(item.data, function (i, campo) {
                        $('#ul_precios')
                            .append(
                                $('<li/>')
                                    .html('<div>Precio No.' + (i + 1) + 'ﾠﾠ'+ $("#moneda").val() + campo.precio + '<br><span class="badge badge-dark">'+campo.dia+'</span>'
                                        + '<button type="button" class="btn btn-white text-danger" data-toggle="tooltip" data-placement="bottom" title="Eliminar precio"  onclick="eliminar_precio('+campo.id_precio+')"><i class="fas fa-trash"></i></button>'
                                        + '<button type="button" class="btn btn-white text-info" data-toggle="tooltip" data-placement="bottom" title="Editar precio" onclick="editar_precio('+campo.id_precio+',\'' +nombreProducto+ '\','+campo.precio+ ',\'' + campo.dia + '\','+campo.id_pres+ '\);"><i class="fas fa-edit"></i></button>'
                                        +'<button type="button" class="btn btn-white text-success" data-toggle="tooltip" data-placement="bottom" title="Cambiar precio" onclick="cambiarPrecio('+campo.id_pres+'\,' + campo.precio + '\)"><i class="fas fa-sync-alt"></i></button></div>')
                            )
                    });
                    feather.replace();
                  
                } else {
                    $('#ul_precios').html("<center><br><br><br><i class='mdi mdi-alert-circle display-3' style='color: #d3d3d3;'></i><br><br><span class='font-18' style='color: #d3d3d3;'>No hay datos disponibles</span><br></center>");
                }
                $("#cover-spin").hide();
            }
          
        },
        beforeSend: ()=>{
            $("#cover-spin").show();
        }
    });
}
var agregar_precio = function (id_pres, nombre) {
    $('#modal_precios').modal('show')
    $('.modal-title').html("Agregar un precio para: <b>" + nombre + "</b>")
    $("#frm_precio").val("")
    $("#frm_comentario").val("")
    frm.dataset.id_pres = id_pres;
    frm.dataset.nombre_prod = nombre;
    $("#frm_dia").empty();
    $.ajax({
        type: "POST",
        url: $("#url").val() + "ajuste/fetch_dias_registrados",
        data: {
            id: id_pres
        },
        dataType: "json",
        success: function (response) {
            if(response != null){
                var i
               if(response.data.length >= 7){
                    $("#btn_agregar").attr("disabled", true)
                    $("#btn_agregar").html("No se pueden agregar más precios");
               }else{
                $("#btn_agregar").attr("disabled", false)
                $("#btn_agregar").html("Agregar");
                let dias = ["Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado", "Domingo"];
                let diasRegistrados = [];
                var limit = response.data.length;
                for(var x = 0; x < response.data.length ; x++){
                    diasRegistrados.push(response.data[x]["dia"]);
                }
                for(x = 0; x < 6; x++){
                    if(dias.includes(diasRegistrados[x])){
                         i = dias.indexOf(diasRegistrados[x]);
                         dias.splice(i, 1);
                    }
                }
                for(x = 0; x < dias.length; x++){
                    $("#frm_dia").append("<option value="+dias[x]+">"+dias[x]+"</option>")
                }
                
               }
            }
        }
    });
}
var eliminar_precio = function (id_precio) {
    Swal.fire({
        title: 'Estás seguro de eliminar este precio?',
        text: "No puedes revertir esta acción!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, borrarlo!',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.value == true) {
          $.ajax({
              type: "POST",
              url: $("#url").val() + "ajuste/eliminar_precio",
              data: {
                  id_precio : id_precio
              },
              success: function (response) {
                  console.log(response)
                  if(response == true){
                    Swal.fire("Precio eliminado correctamente", "Se ha eliminado un precio al producto: " + frm.dataset.nombre_prod, "success").then(()=>{
                        $('#modal_precios').modal('hide')
                        $('.modal-title').html("--")
                        $("#frm_precio").val("")
                        $("#frm_comentario").val("")
                        $("txt_limite").html("");
                        $("txt_error_precio").html("");
                        listarPrecios(frm.dataset.id_pres, frm.dataset.nombre_prod)
                    })
                }
                
              }
          });
        }
      })
}
var editar_precio = function (id_pres, nombre, precioSeleccionado, dia, id_presentacion) {
    $('#modal_edit').modal('show')
    $('#lbl_edit_title').html("Editar precio para: <b>" + nombre + "</b> del día <b>"+dia+"</b>")
    $("#frm_precio_edit").val("")
    $("#frm_precio_edit").val(precioSeleccionado)   
    frm_edit.dataset.id_pres = id_pres
    frm_edit.dataset.dia = dia 
    frm_edit.dataset.id_presentacion = id_presentacion
    frm_edit.dataset.nombre = nombre
}
$("#btn_agregar").on("click", (eventListener) => {
    let data = {
        'precio': $("#frm_precio").val(),
        'dia': $("#frm_dia").val()
    }
    if (parseFloat(data.precio)) { 
        $.ajax({
            type: "POST",
            url: $("#url").val() + "ajuste/verificar_existencia_de_precio",
            data: {
                id_pres: frm.dataset.id_pres,
                dia : data.dia
            }, 
            success: function (response) {
                console.log(response);
                if(response == 0){
                   if($("#frm_dia").val() !=""){
                        if($("#frm_precio").val() !=""){
                            $.ajax({
                                type: "POST",
                                url: $("#url").val() +"ajuste/agregar_precio",
                                data: {
                                    'id_pres': frm.dataset.id_pres,
                                    'precio': parseFloat($("#frm_precio").val()),
                                    'dia': $("#frm_dia").val()
                                },
                                success: function (response) {
                                    console.log(response);
                                    if(response == true){
                                        Swal.fire("Precio añadido", "Se ha agregado el precio", "success").then(()=>{
                                            $('#modal_precios').modal('hide')
                                            $('.modal-title').html("")
                                            $("#frm_precio").val("")
                                            $("#frm_comentario").val("")
                                            listarPrecios(frm.dataset.id_pres, frm.dataset.nombre_prod);
                                            $("#alert_reload").show();
                                        })
                                      
                                    }
                                    $("#cover-spin").hide();
                                },
                                beforeSend: ()=>{
                                    $("#cover-spin").show();
                                }
                            });
                        }else{
                            Swal.fire("Favor de agregar un precio", "", "error")
                        }
                   }else{
                    Swal.fire("Favor de agregar un día", "", "error")
                   }
                }else{
                    Swal.fire("Día ya existente", "Parece que ya se ha agregado un precio a este producto para este dia", "error")
                }
            }
        });
    } else {
        $("#txt_error_precio").css({
            color: "red",
            display: "block"
        })
        $("#txt_error_precio").html("Precio inválido")
    }
})
$("#frm_precio").on("input", (eventListener) => {
    let precio = $("#frm_precio").val()
    console.log(precio)
    if (parseFloat(precio)) {
        $("#txt_error_precio").css({
            color: "green",
            display: "none"
        })
        $("#frm_precio").val(parseFloat(precio))
    } else {
        $("#txt_error_precio").css({
            color: "red",
            display: "block"
        })
        $("#txt_error_precio").html("Precio inválido")
        $("#frm_precio").val("")
    }
})
$("#frm_comentario").on("input", (eventListener) => {
    var comentario = $("#frm_comentario").val()
    if (comentario.length <= 60) {
        if (comentario.length <= 20) {
            $("#txt_limite").css({
                color: "green"
            });
        } else if (comentario.length <= 40 && comentario.length > 20) {
            $("#txt_limite").css({
                color: "orange"
            });
        } else {
            $("#txt_limite").css({
                color: "red"
            });
        }
        $("#txt_limite").html("Limite: " + comentario.length + " - 60")
    } else {
        $("#txt_limite").html("Limite: 60 - 60")
        comentario = comentario.substr(0, 60);
        $("#frm_comentario").val(comentario)
    }

})
$("#btn_editar").on("click", function(){
    $.ajax({
        type: "POST",
        url: $("#url").val() + "ajuste/editar_precio",
        data: {
            precio : $("#frm_precio_edit").val(),
            id_pres : frm_edit.dataset.id_pres,
            dia : frm_edit.dataset.dia
        },
        success: function (response) {
            if(response == 1){
                Swal.fire("Precio editado", "Se ha editado el precio", "success").then(()=>{
                    $('#modal_edit').modal('hide')
                    $('#lbl_edit_title').html("")
                    $("#frm_precio_edit").val("")
                    frm_edit.dataset.id_pres = "0"
                    frm_edit.dataset.dia = "0"
                    listarPrecios(frm_edit.dataset.id_presentacion, frm_edit.dataset.nombre)
                })
                $("#cover-spin").hide();
            }
        },
        beforeSend: () => {
            $("#cover-spin").show();
        }
    });
})
function getCountPrecios(id_pres){
    return $.ajax({
        type: "POST",
        url: $('#url').val() + "ajuste/contar_precios",
        data: {
            id_pres : id_pres,  
        },

        cache: false,
        async: !1,
    });
}
//FUNCIONES DE CAMBIAR PRECIOS
//BETA 1.0
$("#frm_number").change(()=>{
    var p = $("#frm_number").val()
    var precio_prueba = 12.00;
    var result = (precio_prueba / 100) * p
    result = Number.parseFloat(precio_prueba + result).toFixed(2)
    $("#test_percent").html("Ejemplo de cambio: Precio original : " +$("#moneda").val() + "12.00<br>Precio con cambio por metodo de porcentaje:" +$("#moneda").val() + " " + result)
})

$("#btn_change").on("click", ()=>{
    var dia = $("#frm_dia_cambio").val()
    var cat = $("#frm_select_categoria").val()
    btn_update.dataset.id_cat = cat
    if(!(dia.length <= 0)){
         if(!(cat.length <= 0)){
             cambiar_precio_por_categoria(btn_update.dataset.id_cat, dia)
         }else{
             Swal.fire("Error","Favor de ingresar una categoria", "error")
         }
    }else{
     Swal.fire("Error","Favor de ingresar un día", "error")
    }
    
 })

var cambiarPrecio = function(id_pres, precio, nombre){
    Swal.fire({
        title: 'Deseas cambiar el precio a '+$("#moneda").val() + ' ' + Number.parseFloat(precio).toFixed(2)+' ?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Si, cambiarlo',
        denyButtonText: `No cambiarlo`,
        icon: 'question'
        
      }).then((result) => {
        if (result.value == true) {

            $.ajax({
                type: "POST",
                url: $("#url").val()+"ajuste/cambiar_precio_por_id",
                data: {
                    id_pres: id_pres,
                    precio : Number.parseFloat(precio).toFixed(2)
                },
                success: function (response) {
                    if(response == true){
                        Swal.fire("Precio cambiado", "Se ha cambiado el precio de la presentación", "success").then(()=>{
                            listarPrecios(id_pres, "")

                        })
                    }else{
                        Swal.fire("Error en la consulta", "Lea la ventana de comandos", "error")
                        console.log(response)
                    }
                    $("#cover-spin").hide();
                },
                beforeSend: () => {
                    $("#cover-spin").show()
                }
            });

        } else if (result.value == false) {
          Swal.fire('Operación cancelada por el usuario', '', 'info')
        }
      })
}
//CAMBIOS DE PRECIO GLOBALMENTE

var cambiar_precio_por_categoria = function (id_cat, dia){
    var id_cat = $("#frm_select_categoria").val()
    var dia_cambio = $("#frm_dia_cambio").val()
    if(id_cat != "" && dia_cambio !=""){
        Swal.fire({
            title: '¿Deseas cambiar cambiar el precio de la categoria?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Si, cambiarlo',
            denyButtonText: `No cambiarlo`,
            icon: 'question'
            
          }).then((result) => {
            if(result.value){
                $.ajax({
                    type: "POST",
                    url: $("#url").val()+"ajuste/cambiar_precio_por_categoria",
                    data: {
                        id_cat : id_cat,
                        dia: dia
                    },
                    success: function (response) {
                        console.log(response)
                        if(response == true){
                            Swal.fire("Operación correcta","Se ha cambiado los precios correctamente", "success")
                            listarProductos(id_cat);
                        }else{
                            Swal.fire("Alerta", response, "error");
                        }
                        $("#cover-spin").hide();
                    },
                    beforeSend: () => {
                        $("#cover-spin").show();
                    }
    
                });
            }
          })
    }else{
        Swal.fire("Error", "Favor de llenar todos los campos","error")
    }
}

var cambiar_precio_general = (dia) => {
    $.ajax({
        type: "POST",
        url: $("#url").val() + "ajuste/cambiar_precio_general",
        data: {
            dia : dia,
        },
        success: function (response) {
          if(response == 1){
              Swal.fire("Correcto", "Se han cambiado los precios del día " + dia + "<br>Nota: Si algunos precios no se cambiaron, favor de verificar que todas las presentaciones tengan precios registrados para el día " + dia + "<br>Para así, cambiarlos", "success").then(()=>{
                  window.location.reload();
              })
          } else{
              Swal.fire("Error", response, "error")
          }
        }
    });
}
$("#btn_cambiar").on("click", () =>{
   if($("#cambio_precio_general").val() != ""){
    Swal.fire({
        title: 'Alerta',
        html: 'Deseas cambiar el precio del día <b>'+$("#cambio_precio_general").val() +'</b>',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Si, cambiarlo',
        denyButtonText: `No cambiarlo`,
        icon: 'question'
      }).then((result)=>{
        if(result.value){
            cambiar_precio_general($("#cambio_precio_general").val());
        }
      })
   }else{
       Swal.fire("Error", "Favor de seleccionar un día", "error")
   }
})


