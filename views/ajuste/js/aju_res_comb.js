$(function() {
    listarCombos();
    $('.scroll_receta').slimscroll({
        height: 350
    });
    /*
    var tour = new Tour({
    steps: [
        {
          element: "#step1",
          placement: "top",
          title: "Paso #01",
          content: "Agregue un combo y asignele sus atributos correspondientes.<br><b>Ejemplo: COMBO FAMILIAR</b>"
        },
        {
          element: "#step2",
          placement: "top",
          title: "Paso #02",
          content: "En esta sección podrá agregar presentaciones a su combo.<br>"
          +"<b>Ejemplo: 5 PERSONAS</b>"
        }
    ]});

    $('.startTour').click(function(){
        tour.restart();
    })
    */
});

/* Mostrar datos en la tabla productos */
var listarCombos = function(){
    $('#head-p').empty();
    $('#body-c').empty();
    $('#body-p').html('<div class="row text-center"><div class="col-sm-10 offset-sm-1"><h4><i class="ti ti-arrow-circle-left"></i><br>Agregue o seleccione un combo</h4><h6>Debes agregar o seleccionar un combo para poder agregar o modificar sus presentaciones</h6></div></div>');
    var table = $('#table-productos')
    .DataTable({
        "destroy": true,
        "responsive": true,
        "dom": "tp",
        "bSort": false,
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"ajuste/combo_list",
            "data": {
                id_prod : '%'
            }
        },
        "columns":[
            {"data":null,"render": function ( data, type, row) {
                return '<a href="javascript:void(0)" class="link" onclick="listarPresentaciones('+data.id_prod+',\''+data.nombre+'\')">'+data.nombre+'</a>';
            }},
            {"data":null,"render": function ( data, type, row) {
                if(data.estado == 'a'){
                    return '<div class="text-right"><span class="text-navy"><i class="ti-check"></i> Si </span></div>';
                } else if (data.estado == 'i'){
                    return '<div class="text-right"><span class="text-danger"><i class="ti-close"></i> No </span></div>'
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="text-right"><a href="javascript:void(0)" class="text-info edit" onclick="editarProducto('+data.id_prod+')"><i data-feather="edit" class="feather-sm fill-white"></i></a></div>';
            }}
        ]
    });

    $('#table-productos').DataTable().on("draw", function(){
        feather.replace();
    });
}

/* Listar presentaciones de cada producto seleccionado */
var listarPresentaciones = function(id_prod,nombre){
    var moneda = $("#moneda").val();
    var id_pres = '%';
    $.ajax({
        type: "POST",
        url: $('#url').val()+"ajuste/producto_pres_list",
        data: {
            id_prod: id_prod,
            id_pres: id_pres
        },
        dataType: "json",
        success: function(item){
        $('#head-p').html('<button class="btn btn-success btn-block btn-nuvpres" onclick="nuevaPresentacion('+id_prod+',\''+nombre+'\')"><i class="fa fa-plus-circle"></i> Agregar presentaci&oacute;n </button>');
        $('#body-c').html('<br><strong class="text-warning">Presentaciones de <span id="nomb_pres">'+nombre+'</span></strong><br><br>');
            if (item.data.length != 0) {
                $('#body-p').empty();
                $.each(item.data, function(i, campo) {
                    if(campo.estado == 'a'){
                        var boxpres = '';
                    }else{
                        var boxpres = 'boxpres';
                    }
                    $('#body-p')
                    .append(
                        $('<a href="javascript:void(0)" class="link" onclick="editarPresentacion('+campo.id_pres+',\''+nombre+'\')"/>')
                        .html('<div class="card col-sm-12 '+boxpres+' font-14" style="margin-bottom: 10px;"><div class="card-body d-flex" style="padding: 10px 0px 10px 0px;"><div class="read">'+campo.presentacion+'</div><div class="ml-auto">'+moneda+' '+campo.precio+'</div></div></div>')
                    )
                });
            } else {
                $('#body-p').html('<div class="row text-center"><div class="col-sm-10 offset-sm-1"><h4><i class="ti ti-arrow-circle-up"></i><br>Agregue una presentación</h4><h6>Debes agregar una presentación para poder guardar y usar el combo</h6></div></div>');
            }
        }
    });
}

/* Editar datos de un producto */
var editarProducto = function(id_prod){
    $('.bootstrap-tagsinput').css('display','block');
    $('#cod_catg').find('option').remove();
    $('#modal-producto').modal('show');
    $("#id_prod_producto").val(id_prod);
    $.ajax({
        type: "POST",
        url: $('#url').val()+"ajuste/combo_list",
        data: {
            id_prod: id_prod
        },
        dataType: "json",
        success: function(item){
            $.each(item.data, function(i, campo) {
                $('#notas_producto').tagsinput('removeAll');
                $('#nombre_producto').val(campo.nombre);
                if(campo.id_tipo == 1){
                    $('#transf').prop('checked', true);
                    $('#ntransf').prop('checked', false);
                } else if (campo.id_tipo == 2){
                    $('#ntransf').prop('checked', true);
                    $('#transf').prop('checked', false);
                }
                if(campo.delivery == 1){
                    $('#delivery_producto').prop('checked', true);
                    $('#hidden_delivery_producto').val(1);
                } else {
                    $('#delivery_producto').prop('checked', false);
                    $('#hidden_delivery_producto').val(0);
                }
                if(campo.estado == 'a'){
                    $('#estado_producto').prop('checked', true);
                    $('#hidden_estado_producto').val('a');
                } else {
                    $('#estado_producto').prop('checked', false);
                    $('#hidden_estado_producto').val('i');
                }
                $('#id_areap_producto').selectpicker('val', campo.id_areap);
                $('#cod_catg').selectpicker('val', campo.id_catg);
                $('#cod_catg').selectpicker();
                $('#cod_catg').selectpicker('refresh');
                $('#estado_producto').selectpicker('val', campo.estado);
                $('#notas_producto').tagsinput('add',campo.notas);
                $('#descripcion_producto').val(campo.descripcion);
            });
        }
    });
}

/* Nueva presentacion de un producto */
var nuevaPresentacion = function(id_prod,nombre){
    $(".f").removeClass("focused");
    $('#form-presentacion').formValidation('resetForm', true);
    $('#wizardPicturePreview').attr('src',$("#url").val()+'public/images/productos/default.png');
    $('#imagen').val('default.png');
    $('#wizard-picture').val('');
    $('#id_prod_presentacion').val(id_prod);
    $('#nombre_producto_presentacion').val(nombre);
    $('#descripcion_presentacion').val('');
    $('#cod_prod_presentacion').val('');
    $('#stock_min_presentacion').val('');
    $('#precio_delivery').val('');
    $.ajax({
        type: "POST",
        url: $('#url').val()+"ajuste/combo_list",
        data: {
            id_prod: id_prod
        },
        dataType: "json",
        success: function(item){
            $.each(item.data, function(i, campo) {
            //id_tipo = 1 (Producto Transformado)
                if(campo.id_tipo == 1){
                    // Ocultar check receta (tp-1), stock/stock_minimo (tp-2)
                    $('#tp-1').css('display','none');
                    $('#tp-2').css('display','none');
                    $('#tp-4').css('display','none');
                    // Quita el check a receta
                    $('#receta_presentacion').prop('checked', false);
                    $('#mensaje-ins').css('display','block');
                    $('#mensaje-ins').html('<div class="alert alert-warning">'
                        +'Guarde los datos de la presentaci&oacute;n, para que pueda ingresar productos'
                        +'</div>');
                }
                //id_tipo = 2 (Producto NO Transformado)
                else{
                    $('#stock_presentacion').prop('checked', false);
                    $('#mensaje-ins').css('display','none');
                    // Ocultar check receta (tp-1)
                    $('#tp-1').css('display','none');
                    // Mostrar check stock / stock-minimo (tp-2)
                    $('#tp-2').css('display','block');
                    $('#tp-4').css('display','none');
                }
                $('#hidden_receta_presentacion').val(0);
                $('#hidden_impuesto_presentacion').val(1);
                $('#impuesto_presentacion').prop('checked', true);
                $('#hidden_estado_presentacion').val('a');
                $('#estado_presentacion').prop('checked', true);
                $('#hidden_delivery_presentacion').val(0);
                $('#delivery_presentacion').prop('checked', false);
                $('#tp-3').css('display','none');
            });
        }
    });
    $('#id_pres_presentacion').val('');
    $('#modal-presentacion').modal('show');
}

/* Editar datos de una presentacion de un producto */
var editarPresentacion = function(id_pres,nombre){
    $(".f").addClass("focused");
    var id_prod = '%';
    $('#form-presentacion').formValidation('resetForm', true);
    $("#nombre_producto_presentacion").val(nombre);
    $('#id_pres_receta').val(id_pres);
    $('#modal-presentacion').modal('show');
    $.ajax({
        type: "POST",
        url: $('#url').val()+"ajuste/producto_pres_list",
        data: {
            id_prod: id_prod,
            id_pres: id_pres
        },
        dataType: "json",
        success: function(item){
            $.each(item.data, function(i, campo) {
                $('#id_pres_presentacion').val(campo.id_pres);
                $('#id_prod_presentacion').val(campo.id_prod);
                $('#cod_prod_presentacion').val(campo.cod_prod);
                $('#presentacion_presentacion').val(campo.presentacion);
                $('#descripcion_presentacion').val(campo.descripcion);
                $('#precio_presentacion').val(campo.precio);
                $('#precio_delivery').val(campo.precio_delivery);
                $('#wizardPicturePreview').attr('src',$("#url").val()+'public/images/productos/'+campo.imagen+'');
                $('#imagen').val(campo.imagen);
                $('#wizard-picture').val('');

                if(campo.impuesto == 1){
                    $('#impuesto_presentacion').prop('checked', true);
                    $('#hidden_impuesto_presentacion').val(1);
                } else {
                    $('#impuesto_presentacion').prop('checked', false);
                    $('#hidden_impuesto_presentacion').val(0);
                }

                if(campo.delivery == 1){
                    $('#delivery_presentacion').prop('checked', true);
                    $('#hidden_delivery_presentacion').val(1);
                    $('#tp-3').css('display','block');
                } else {
                    $('#delivery_presentacion').prop('checked', false);
                    $('#hidden_delivery_presentacion').val(0);
                    $('#tp-3').css('display','none');
                }

                if(campo.estado == 'a'){
                    $('#estado_presentacion').prop('checked', true);
                    $('#hidden_estado_presentacion').val('a');
                } else {
                    $('#estado_presentacion').prop('checked', false);
                    $('#hidden_estado_presentacion').val('i');
                }

                //id_tipo = 1 (Producto Transformado)
                if(campo.TipoProd.id_tipo == 1){
                    if(campo.receta == 1){
                        $('#receta_presentacion').prop('checked', true);
                        $('#hidden_receta_presentacion').val(1);
                        $('#mensaje-ins').css('display','block');
                        $('#mensaje-ins').html('<div class="alert alert-info">'
                            +'Modificar los productos <a class="alert-link" onclick="receta()">AQUI</a>'
                            +'</div>');
                    } else {
                        $('#receta_presentacion').prop('checked', false);
                        $('#hidden_receta_presentacion').val(0);
                        $('#mensaje-ins').css('display','none');
                        $('#mensaje-ins').html('<div class="alert alert-warning">'
                            +'Ingresar los productos <a class="alert-link" onclick="receta()">AQUI</a> y luego click en Guardar'
                            +'</div>');
                    }
                    // Mostrar check receta (tp-1)
                    $('#tp-1').css('display','block');
                    // Ocultar check stock / stock_minimo (tp-2)
                    $('#tp-2').css('display','none');
                    $('#tp-4').css('display','none');
                }
                //id_tipo = 2 (Producto NO Transformado)
                else{
                    $('#mensaje-ins').css('display','none');
                    if(campo.receta == 1){
                        $('#stock_presentacion').prop('checked', true);
                        $('#tp-4').css('display','block');
                        $('#hidden_receta_presentacion').val(1);
                    } else {
                        $('#stock_presentacion').prop('checked', false);
                        $('#tp-4').css('display','none');
                        $('#hidden_receta_presentacion').val(0);
                    }
                    // Ocultar check receta (tp-1)
                    $('#tp-1').css('display','none');
                    // Mostrar check stock / stock_minimo (tp-2)
                    $('#tp-2').css('display','block');
                }
            });
        }
    });
}

/* Producto */
$(function() {
    $('#form-producto')
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

            var producto = {
                id_prod: 0,
                id_tipo: 0,
                id_catg: 0,
                id_areap: 0,
                nombre: 0,
                notas: 0,
                descripcion: 0,
                delivery: 0,
                estado: 0
            }

            producto.id_prod = $('#id_prod_producto').val();
            producto.id_tipo = 1;
            producto.id_catg = 1;
            producto.id_areap = $('#id_areap_producto').val();
            producto.nombre = $('#nombre_producto').val();
            producto.notas = $('#notas_producto').val().toUpperCase();
            producto.descripcion = $('#descripcion_producto').val();
            producto.delivery = $('#hidden_delivery_producto').val();
            producto.estado = $('#hidden_estado_producto').val();
            producto.combo = 'a';
            $.ajax({
                dataType: 'JSON',
                type: 'POST',
                url: $('#url').val()+'ajuste/producto_crud',
                data: producto,
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
                        $('#modal-producto').modal('hide');
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
                        listarCombos();
                    } else if(cod == 2) {
                        $('#modal-producto').modal('hide');
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
                        listarCombos();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(errorThrown + ' ' + textStatus);
                }   
            });

            return false;
    });
});

$(function() {
    $('#form-presentacion')
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

        var presentacion = new FormData($('#form-presentacion')[0]);

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: presentacion,
            url: $('#url').val()+'ajuste/producto_pres_crud',
            contentType: false,
            processData: false,
            success: function (cod) {
                if(cod == 0){
                    Swal.fire({   
                        title:'Proceso No Culminado',   
                        text: 'Datos duplicados correctamente',
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
                    $('#modal-presentacion').modal('hide');
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
                    listarPresentaciones($('#id_prod_presentacion').val(),$('#nombre_producto_presentacion').val());
                } else if(cod == 2) {
                    $('#modal-presentacion').modal('hide');                    
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
                    listarPresentaciones($('#id_prod_presentacion').val(),$('#nombre_producto_presentacion').val());
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown + ' ' + textStatus);
            }   
        });

        return false;

      });
});

var listarReceta = function(){
    $('#table-receta').empty();
    $.ajax({
      type: "post",
      dataType: "json",
      data: {
          id_pres: $("#id_pres_receta").val()
      },
      url: $('#url').val()+'ajuste/producto_pres_ing',
      success: function (data){
        $.each(data, function(i, item) {
            var opc_m=item.id_med;if(1==opc_m)var valor_cant=(1*item.cant).toFixed(6);else if(2==opc_m)var valor_cant=(1*item.cant).toFixed(6);else if(3==opc_m)var valor_cant=(1e3*item.cant).toFixed(6);else if(4==opc_m)var valor_cant=(1e6*item.cant).toFixed(6);else if(5==opc_m)var valor_cant=(1*item.cant).toFixed(6);else if(6==opc_m)var valor_cant=(1e3*item.cant).toFixed(6);else if(7==opc_m)var valor_cant=(2.20462*item.cant).toFixed(6);else if(8==opc_m)var valor_cant=(35.274*item.cant).toFixed(6);
            if(item.id_tipo_ins == 1){
                var tipo = '<span class="label label-warning">INSUMO</span>';
            } else if (item.id_tipo_ins == 2){
                var tipo = '<span class="label label-info">PRODUCTO</span>';
            } else{
                var tipo = '<span class="label label-info">PRODUCTO</span>';
            }
            $('#table-receta')
            .append(
              $('<tr class="active"/>')
                .append($('<td/>').html(tipo))
                .append($('<td/>').html(item.Insumo.ins_nom))
                .append($('<td/>').html(valor_cant))
                .append($('<td/>').html(item.Medida.descripcion))
                .append($('<td class="text-right"/>').html('<div class="text-right"><button type="button" class="btn btn-danger btn-xs" onclick="eliminarInsumo('+item.id_pi+');"><i class="ti-trash"></i></button></div>'))
                )
            });
        }
    });
}

/* Abrir modal para ingresar insumos/ingredientes a la receta */
var receta = function(){
    $('#modal-presentacion').modal('hide');
    $('#modal-receta').modal('show');
    $('.list-ingredientes').css('display','none');
    listarReceta();
}

/* Eliminar insumo/ingrediente de receta */
var eliminarInsumo = function(id_pi){
    $.ajax({
        type: "POST",
        url: $('#url').val()+"ajuste/producto_ingrediente_delete",
        data: {
            id_pi: id_pi
        },
        dataType: "json",
        success: function(datos){
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
            listarReceta();
        }
    });
}

$('.btn-eliminar').on('click', function(){
    $('.list-ingredientes').css('display','none');
    $("#buscar_ingrediente").val('');
    $('#cant_receta').val('');
    $('#valor_ing').text('0');
});

/* Boton cerrar modal ingredientes */
$('.btn-cerrar-receta').click( function() {
    $('#modal-receta').modal('hide');
    $('#modal-presentacion').modal('show');
});

/* PRESENTACION */

$('#receta_presentacion').on('click', function(event){
    if($('#receta_presentacion').is(':checked')){
        $('#mensaje-ins').css('display','block');
        $('#hidden_receta_presentacion').val(1);
    }else{
        $('#mensaje-ins').css('display','none');
        $('#hidden_receta_presentacion').val(0);
    }
});

$('#stock_presentacion').on('click', function(event){
    if($('#stock_presentacion').is(':checked')){
        $('#hidden_receta_presentacion').val(1);
    }else{
        $('#hidden_receta_presentacion').val(0);
    }
});

$('#impuesto_presentacion').on('click', function(event){
    if($('#impuesto_presentacion').is(':checked')){
        $('#hidden_impuesto_presentacion').val(1);
    }else{
        $('#hidden_impuesto_presentacion').val(0);
    }
});

$('#delivery_presentacion').on('click', function(event){
    if($('#delivery_presentacion').is(':checked')){
        $('#hidden_delivery_presentacion').val(1);
        $('#tp-3').css('display','block');
    }else{
        $('#hidden_delivery_presentacion').val(0);
        $('#tp-3').css('display','none');
    }
});

$('#estado_presentacion').on('click', function(event){
    if($('#estado_presentacion').is(':checked')){
        $('#hidden_estado_presentacion').val('a');
    }else{
        $('#hidden_estado_presentacion').val('i');
    }
});

/* PRESENTACION */

/* PRODUCTO */

$('#delivery_producto').on('click', function(event){
    if($('#delivery_producto').is(':checked')){
        $('#hidden_delivery_producto').val(1);
    }else{
        $('#hidden_delivery_producto').val(0);
    }
});

$('#estado_producto').on('click', function(event){
    if($('#estado_producto').is(':checked')){
        $('#hidden_estado_producto').val('a');
    }else{
        $('#hidden_estado_producto').val('i');
    }
});

$('.btn-nuevo-combo').click( function() {
    $('#id_prod_producto').val('');
    $('#notas_producto').tagsinput('removeAll');
    $('.bootstrap-tagsinput').css('display','block');
    $('#hidden_delivery_producto').val(0);
    $('#delivery_producto').prop('checked', false);
    $('#hidden_estado_producto').val('a');
    $('#estado_producto').prop('checked', true);
    $('#modal-producto').modal('show');
});

$('#modal-producto').on('hidden.bs.modal', function() {
    $(this).find('form')[0].reset();
    $('#form-producto').formValidation('resetForm', true);
    $('#estado_producto').selectpicker('val', 'a');
    $("#notas_producto").val('');
    $("#id_areap_producto").val('').selectpicker('refresh');
    $('#receta_presentacion').removeAttr('checked');
    $('#stock_presentacion').removeAttr('checked');
});

/* PRODUCTO */

$('#presentacion_presentacion').keyup(function(){
    var abrev_pres = ($(this).val().charAt(0)+''+$(this).val().charAt(1)+''+$(this).val().charAt(2)).toUpperCase();
    var abrev_prod = ($('#nomb_pres').text().charAt(0)+''+$('#nomb_pres').text().charAt(1)).toUpperCase();
    var abrev_prec = Math.floor($('#precio_presentacion').val());
    $("#cod_prod_presentacion").val(abrev_prod+''+abrev_pres+''+abrev_prec);
});

$('#precio_presentacion').keyup(function(){
    var abrev_pres = ($('#presentacion_presentacion').val().charAt(0)+''+$('#presentacion_presentacion').val().charAt(1)+''+$('#presentacion_presentacion').val().charAt(2)).toUpperCase();
    var abrev_prod = ($('#nomb_pres').text().charAt(0)+''+$('#nomb_pres').text().charAt(1)).toUpperCase();
    var abrev_prec = Math.floor($(this).val());
    $("#cod_prod_presentacion").val(abrev_prod+''+abrev_pres+''+abrev_prec);
});