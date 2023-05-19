$(function() {

    $('#compras').addClass("active");
    $('#c-compras').addClass("active");

    $('#fecha_c').bootstrapMaterialDatePicker({
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });

    $('#hora_c').bootstrapMaterialDatePicker({
        format: 'LT',
        date: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });

    $('.f').on('blur', function(e) {
        $(this).parent( ".form-group" ).addClass( "focused");
    });

    /* Busqueda de insumo/producto */
    $("#buscar_insumo").autocomplete({
        autoFocus: true,
        dataType: 'JSON',
        delay: 1,
        source: function (request, response) {
            jQuery.ajax({
                url: $('#url').val()+'compra/compra_insumo_buscar',
                type: "post",
                dataType: "json",
                data: {
                    cadena: request.term
                },
                success: function (data) {
                    response($.map(data, function (item) {
                        return {
                            id_ins: item.id_ins,
                            id_tipo_ins: item.id_tipo_ins,
                            value: item.ins_cod + ' | '+item.ins_cat+' | '+item.ins_nom,
                            nombre: item.ins_cod + ' | '+item.ins_cat+' | '+item.ins_nom,
                            ins_med: item.ins_med,
                            label: item.ins_cod + ' | '+item.ins_cat+' | '+item.ins_nom,
                        }
                    }))
                }
            })
        },
        select: function (e, ui) {
            $("#buscar_insumo").val(ui.item.nombre);
            $("#id_tipo_ins_buscar").val(ui.item.id_tipo_ins)
            $("#id_ins_buscar").val(ui.item.id_ins);
            $("#label-unidad-medida").html(ui.item.ins_med);
            $("#cantidad_buscar").focus();
        },
        change: function(e,ui){
            $("#cantidad_buscar").focus();
        }
    });

    $('.f').on('change', function(e) {
        $('#form-compra').formValidation('revalidateField', 'fecha_c');
        $('#form-compra').formValidation('revalidateField', 'hora_c');
    });

    $('#form-compra').formValidation({
        framework: 'bootstrap',
        fields: {
        }
    })

    .on('success.form.fv', function(e) {

        var form = $(this);

        if($('#filtro_cuotas').val() == 0){
            Swal.fire({   
                title:'Advertencia',   
                text: 'Complete los campos de las cuotas',
                icon: "warning", 
                confirmButtonColor: "#34d16e",   
                confirmButtonText: "Aceptar",
                allowOutsideClick: false,
                showCancelButton: false,
                showConfirmButton: true
            }, function() {
                return false
            });
        } else if($('#id_prov').val() == 0){
            Swal.fire({   
                title:'Advertencia',   
                text: 'Ingrese un proveedor a la compra',
                icon: "warning", 
                confirmButtonColor: "#34d16e",   
                confirmButtonText: "Aceptar",
                allowOutsideClick: false,
                showCancelButton: false,
                showConfirmButton: true
            }, function() {
                return false
            });
        }
        else if(compra.detalle.items.length == 0){
            Swal.fire({   
                title:'Advertencia',   
                html: 'Ingrese un elemento al detalle de la compra',
                icon: "warning", 
                confirmButtonColor: "#34d16e",   
                confirmButtonText: "Aceptar",
                allowOutsideClick: false,
                showCancelButton: false,
                showConfirmButton: true
            }, function() {
                return false
            });
        }
        else{

            if($('#id_tipo_compra').val() == 2){

                if($('#total_credito').val() == '0.00' || $('#total_credito').val() == ''){
                    $('#mensaje-credito').empty();
                    $('#mensaje-credito').html('<div class="alert alert-warning">'
                        +'<i class="fa fa-warning"></i> Ingrese el monto total de la compra <a class="alert-link link-credito-1">AQUI</a>.'
                        +'</div>');
                    return false;
                }else{

                    compra.detalle.id_prov = $('#id_prov').val();
                    compra.detalle.id_tipo_compra = $('#id_tipo_compra').val();
                    compra.detalle.id_tipo_doc = $('#id_tipo_doc').val();
                    compra.detalle.fecha_c = $('#fecha_c').val();
                    compra.detalle.hora_c = $('#hora_c').val();
                    compra.detalle.serie_doc = $('#serie_doc').val();
                    compra.detalle.num_doc = $('#num_doc').val();
                    compra.detalle.monto_total = $('#subtotal_global').val();
                    compra.detalle.monto_cuota = $("input[name='monto_cuota[]']").map(function(){return $(this).val();}).get();
                    compra.detalle.fecha_cuota = $("input[name='fecha_cuota[]']").map(function(){return $(this).val();}).get();

                    var html_confirm = '<div>Se registrará la siguiente compra</div>\
                        <br><div><span class="text-success" style="font-size: 17px;">¿Está Usted de Acuerdo?</span></div>';

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
                                type: 'POST',
                                url: $('#url').val()+'compra/compra_crud',                         
                                data: compra.detalle
                             })
                             .done(function(r){
                                var html_terminado = '<div>Datos registrados correctamente</div>\
                                    <br><a href="'+$("#url").val()+'compra" class="btn btn-success">Aceptar</button>'
                                Swal.fire({
                                    title: 'Proceso Terminado',
                                    html: html_terminado,
                                    icon: 'success',
                                    allowOutsideClick: false,
                                    allowEscapeKey : false,
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    closeOnConfirm: false,
                                    closeOnCancel: false
                                });
                             })
                             .fail(function(){
                                Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
                             });
                          });
                        },
                        allowOutsideClick: false              
                    });
                }

            return false;

            } else {

                compra.detalle.id_tipo_compra = $('#id_tipo_compra').val();
                compra.detalle.id_tipo_doc = $('#id_tipo_doc').val();
                compra.detalle.serie_doc = $('#serie_doc').val();
                compra.detalle.num_doc = $('#num_doc').val();
                compra.detalle.fecha_c = $('#fecha_c').val();
                compra.detalle.hora_c = $('#hora_c').val();
                compra.detalle.id_prov = $('#id_prov').val();
                compra.detalle.monto_total = $('#subtotal_global').val();
                compra.detalle.descuento = $('#total_descuento_aumento').val();

                var html_confirm = '<div>Se registrará la siguiente compra</div>\
                        <br><div><span class="text-success" style="font-size: 17px;">¿Está Usted de Acuerdo?</span></div>';

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
                            type: 'POST',
                            url: $('#url').val()+'compra/compra_crud',                         
                            data: compra.detalle
                         })
                         .done(function(r){
                            var html_terminado = '<div>Datos registrados correctamente</div>\
                                <br><a href="'+$("#url").val()+'compra" class="btn btn-success">Aceptar</button>'
                            Swal.fire({
                                title: 'Proceso Terminado',
                                html: html_terminado,
                                icon: 'success',
                                showConfirmButton: false
                            });
                         })
                         .fail(function(){
                            Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
                         });
                      });
                    },
                    allowOutsideClick: false              
                });

            }
            return false;
        }
        return false;
    });
});

var compra = {
    detalle: {
        id_prov: 0,
        id_tipo_compra: 0,
        id_tipo_doc: 0,
        fecha_c: 0,
        hora_c: 0,
        serie_doc: '',
        num_doc: '',
        monto_total: 0,
        descuento: 0,
        cuotas_credito: 0,
        //monto_int: 0,
        monto_cuota: [],
        //imcuota: [],
        fecha_cuota: [],
        //observaciones: 0,
        igv: 0,
        total: 0,
        subtotal: 0,
        items: []
    },

    /* Encargado de agregar un producto a nuestra colección */
    registrar: function(item)
    {
        
        var existe = false;

        item.total = (item.cantidad_insumo * item.precio_insumo);
        
        this.detalle.items.forEach(function(x){
            if(x.id_ins_insumo === item.id_ins_insumo && x.id_tipo_ins_insumo === item.id_tipo_ins_insumo) {
                x.cantidad_insumo += item.cantidad_insumo;
                x.precio_insumo += item.precio_insumo;
                x.total += item.total;
                existe = true;
            }
        });

        if(!existe) this.detalle.items.push(item);

        this.refrescar();

    },

    /* Encargado de actualizar el precio/cantidad de un producto */
    actualizar: function(id, row)
    {
        /* Capturamos la fila actual para buscar los controles por sus nombres */

        row = $(row).closest('.warning-element');

        /* Buscamos la columna que queremos actualizar */
        $(this.detalle.items).each(function(indice, fila){
            if(indice == id)
            {
                /* Agregamos un nuevo objeto para reemplazar al anterior */
                compra.detalle.items[indice] = {
                    id_tipo_ins_insumo: row.find("input[name='id_tipo_ins_insumo']").val(),
                    id_ins_insumo: row.find("input[name='id_ins_insumo']").val(),
                    nombre_insumo: row.find("h6[name='nombre_insumo']").text(),
                    unidad_medida_insumo: row.find("span[name='unidad_medida_insumo']").text(),
                    cantidad_insumo: row.find("input[name='cantidad_insumo']").val(),
                    precio_insumo: row.find("input[name='precio_insumo']").val(),
                };

                compra.detalle.items[indice].total = parseFloat(compra.detalle.items[indice].precio_insumo) * parseFloat(compra.detalle.items[indice].cantidad_insumo);
                return false;
            }
        });

        this.refrescar();
    },

    /* Encargado de retirar el producto seleccionado */
    retirar: function(id)
    {
        /* Declaramos un ID para cada fila */
        $(this.detalle.items).each(function(indice, fila){
            if(indice == id)
            {
                compra.detalle.items.splice(id, 1);
                return false;
            }
        })

        this.refrescar();
    },

    /* Refresca todo los productos elegidos */
    refrescar: function()
    {
        this.detalle.total = 0;

        /* Declaramos un id y calculamos el total */
        $(this.detalle.items).each(function(indice, fila){
            compra.detalle.items[indice].id = indice;
            compra.detalle.total += fila.total;
        })

        /* Calculamos el subtotal e IGV */
        this.detalle.igv      = (this.detalle.total * 0.18).toFixed(2); // 18 % El IGV y damos formato a 2 deciamles
        this.detalle.subtotal = (this.detalle.total - this.detalle.igv).toFixed(2); // Total - IGV y formato a 2 decimales
        this.detalle.total    = parseFloat(this.detalle.total).toFixed(2);

        var template   = $.templates("#table-detalle-template");
        var htmlOutput = template.render(this.detalle);
        var da = this.detalle.total;

        $("#table-detalle").html(htmlOutput);
        $("#subtotal_global").val(da);
        $(".subtotal_global").text(formatNumber(da));
        $(".total_global").text(formatNumber(da));
        $(".total_credito_").val(formatNumber(da));
        if(da == 0){$("#total_descuento_aumento").val('0.00');}
        montoTotal();
        feather.replace();
    }
}

$('#btn-agregar-insumo').click(function() {
    if($('#id_ins_buscar').val() == '' || $('#id_ins_buscar').val() == '0' || $('#cantidad_buscar').val() == '' || $('#precio_buscar').val() == ''){
        Swal.fire({   
            title:'Advertencia',   
            text: 'Complete los campos de búsqueda',
            icon: "warning", 
            confirmButtonColor: "#34d16e",   
            confirmButtonText: "Aceptar",
            allowOutsideClick: false,
            showCancelButton: false,
            showConfirmButton: true
        }, function() {
            return false
        });
    } else {
        compra.registrar({
            id_tipo_ins_insumo: parseInt($("#id_tipo_ins_buscar").val()),
            id_ins_insumo: parseInt($("#id_ins_buscar").val()),
            nombre_insumo: $("#buscar_insumo").val(),
            unidad_medida_insumo: $("#label-unidad-medida").text(),
            cantidad_insumo: parseFloat($("#cantidad_buscar").val()),
            precio_insumo: parseFloat($("#precio_buscar").val()).toFixed(2),
        });
        $("#id_ins_buscar").val('');
        $("#buscar_insumo").val('');
        $("#cantidad_buscar").val('');
        $("#precio_buscar").val('');
        $("#buscar_insumo").focus();
    }
});

var montoTotal = function(){
    var sub_total = $("#subtotal_global").val(),
        descuento_aumento = $("#total_descuento_aumento").val();
    if($("#tipo_compra").val() == 1){
        total = parseFloat(sub_total) - parseFloat(descuento_aumento);
    }else if($("#tipo_compra").val() == 2){
        total = parseFloat(sub_total) + parseFloat(descuento_aumento);
    }
    $(".total_global").text(formatNumber(total));
    $(".total_credito_").val(formatNumber(total));
}

$('#total_descuento_aumento').on('keyup', function(){
    montoTotal();   
});

$('#id_tipo_compra').on('change', function(){
    if($('#id_tipo_compra').val() == 1){
        $("#tipo_compra").val('1');
        $("#filtro_cuotas").val('1');
        $(".text-desaum").text('Descuento');
        $("#total_descuento_aumento").val('0.00');
        $("#mensaje-credito").css('display','none');
        compra.refrescar();
    } else if($('#id_tipo_compra').val() == 2){
        $("#tipo_compra").val('2');
        $(".text-desaum").text('Aumento');
        $("#total_descuento_aumento").val('0.00');
        $("#mensaje-credito").css('display','block');
        compra.refrescar();
    }
});

/* Busqueda de proveedores */
$("#buscar_proveedor").autocomplete({
    delay: 1,
    autoFocus: true,
    dataType: 'JSON',
    source: function (request, response) {
        $.ajax({     
            type: "post",
            dataType: "json",
            url: $('#url').val()+'compra/compra_proveedor_buscar',
            data: {
                cadena: request.term
            },
            success: function (data) {
                response($.map(data, function (item) {
                    return {
                        id_prov: item.id_prov,
                        nombre: item.razon_social,
                        ruc: item.ruc,
                        label: item.razon_social
                    }
                }))
            }
        })
    },
    select: function (e, ui) {
        $("#id_prov").val(ui.item.id_prov);
        $("#datos_proveedor").val(ui.item.nombre); 
    },
    change: function() {
        $("#buscar_proveedor").val('');
        $("#buscar_proveedor").focus();
    }
});

/* Limpiar datos del proveedor */
$('#btnProvLimpiar').click(function() {
    $("#id_prov").val('0');
    $("#buscar_proveedor").val('');
    $("#datos_proveedor").val('');
});

/* Nro de Cuotas*/
$('#cuotas_credito').on('keyup', function(event){
    $('#table-cuotas').empty();
    var numero_cuotas = $('#cuotas_credito').val();
    if(numero_cuotas > 0){
    for (var i=0; i < numero_cuotas; i++) {
        var monto_cuota = ($('#total_credito').val() / numero_cuotas).toFixed(2);
        $('#table-cuotas')
        .append(
            $('<tr/>')
                .append($('<td class="dec"/>').html('<input type="text" class="form-control monto-cuota" name="monto_cuota[]" value="'+monto_cuota+'" autocomplete="off"/>'))
                .append($('<td/>').html('<input type="text" class="form-control datematerial fecha-cuota" name="fecha_cuota[]" readonly="off" autocomplete="off"/>'))
            );
        $('.datematerial').bootstrapMaterialDatePicker({
            format: 'DD-MM-YYYY',
            lang: 'es-do',
            time: false,
            cancelText: 'Cancelar',
            okText: 'Aceptar'
        });
    }}else{
        var monto_cuota = $('#total_credito').val();
        $('#table-cuotas')
        .append(
            $('<tr/>')
                .append($('<td class="dec"/>').html('<input type="text" class="form-control monto-cuota" name="monto_cuota[]" value="'+monto_cuota+'" autocomplete="off"/>'))
                .append($('<td/>').html('<input type="text" class="form-control datematerial fecha-cuota" name="fecha_cuota[]" readonly="off" autocomplete="off"/>'))
            );
        $('.datematerial').bootstrapMaterialDatePicker({
            format: 'DD-MM-YYYY',
            lang: 'es-do',
            time: false,
            cancelText: 'Cancelar',
            okText: 'Aceptar'
        });
    }
});

/* Boton modal cuotas - validar datos del formulario de las cuotas, fechas, intereses */
$('.btn-aceptar-cuota').on('click', function(e){
    var fecha_cuota = $(".fecha-cuota").toArray().some(function(el){
        return $(el).val().length < 1
    });
    var monto_cuota = $(".monto-cuota").toArray().some(function(el){
        return $(el).val().length < 1
    });
    if($('#total_credito').val() == '' || $('#total_credito').val() == '0.00'){
        Swal.fire({   
            title:'Advertencia',   
            text: 'Agregue productos al detalle',
            icon: "warning", 
            confirmButtonColor: "#34d16e",   
            confirmButtonText: "Aceptar",
            allowOutsideClick: false,
            showCancelButton: false,
            showConfirmButton: true
        }, function() {
            return false
        });
    } else if($('#cuotas_credito').val() == ''){
        Swal.fire({   
            title:'Advertencia',   
            text: 'Ingrese número de cuotas',
            icon: "warning", 
            confirmButtonColor: "#34d16e",   
            confirmButtonText: "Aceptar",
            allowOutsideClick: false,
            showCancelButton: false,
            showConfirmButton: true
        }, function() {
            return false
        });
    } else if(fecha_cuota){
        Swal.fire({   
            title:'Advertencia',   
            text: 'Ingrese fecha a las cuotas',
            icon: "warning", 
            confirmButtonColor: "#34d16e",   
            confirmButtonText: "Aceptar",
            allowOutsideClick: false,
            showCancelButton: false,
            showConfirmButton: true
        }, function() {
            return false
        });
    } else if(monto_cuota){
        Swal.fire({   
            title:'Advertencia',   
            text: 'Ingrese monto a las cuotas',
            icon: "warning", 
            confirmButtonColor: "#34d16e",   
            confirmButtonText: "Aceptar",
            allowOutsideClick: false,
            showCancelButton: false,
            showConfirmButton: true
        }, function() {
            return false
        });
    } else {
        $('#modal-credito').modal('hide');
        $('#filtro_cuotas').val('1');
    }
});

$('#modal-credito').on('hidden.bs.modal', function() {
    $('#mensaje-credito').empty();
    if($('#total_credito').val() == '' || $('#total_credito').val() == '0.00' || $('#cuotas_credito').val() == '0' || $('#cuotas_credito').val() == '' || $('input[name="fecha_cuota[]"]').val() == ''){
        $('#filtro_cuotas').val('0');
        $('#mensaje-credito').html('<div class="alert alert-danger m-b-0">'
            +'Adevertencia, Completar campos en blanco <a class="alert-link" data-toggle="modal" data-target="#modal-credito">AQUI</a>.</div>');
    }else{
        $('#filtro_cuotas').val('1');
        $('#mensaje-credito').html('<div class="alert alert-info m-b-0">'
            +'Click <a class="alert-link" data-toggle="modal" data-target="#modal-credito">AQUI</a> si necesita modificar las cuotas de la compra.'
            +'</div>');
    }
});

$('.link-credito-1').on('click', function(e){
    if(compra.detalle.items.length == 0){
        Swal.fire({   
            title:'Advertencia',   
            html: 'Ingrese productos al detalle de la compra, para poder ingresar las cuotas',
            icon: "warning", 
            confirmButtonColor: "#34d16e",   
            confirmButtonText: "Aceptar",
            allowOutsideClick: false,
            showCancelButton: false,
            showConfirmButton: true
        }, function() {
            return false
        });
        $('#filtro_cuotas').val('0');
    }else{
        $('#modal-credito').modal('show');
        $('#filtro_cuotas').val('1');
    }
});

function ponerElCursorAlFinal(id)
{
    var obj = $("#"+id),

    // Guardamos en una variable el contenido
    val = obj.val();
    obj.focus().val("").val(val);
    obj.scrollTop(obj[0].scrollHeight);
}