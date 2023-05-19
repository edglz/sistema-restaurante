var facturador = {
    detalle: {
        id_tipo: 0,
        motivo: 0,
        items:    []
    },

    /* Encargado de agregar un producto a nuestra colección */
    registrar: function(item)
    {
        var existe = false;
        
        //item.total = (item.cantidad * item.precio);
        
        this.detalle.items.forEach(function(x){
            if(x.id_ins_insumo === item.id_ins_insumo) {
                x.cantidad_insumo += item.cantidad_insumo;
                existe = true;
            }
        });

        if(!existe) {
            this.detalle.items.push(item);
        }

        this.refrescar();
    },

    /* Encargado de actualizar el precio/cantidad de un producto */
    actualizar: function(id, row)
    {
        /* Capturamos la fila actual para buscar los controles por sus nombres */
        row = $(row).closest('.list-group-item');

        /* Buscamos la columna que queremos actualizar */
        $(this.detalle.items).each(function(indice, fila){
            if(indice == id)
            {
                /* Agregamos un nuevo objeto para reemplazar al anterior */
                facturador.detalle.items[indice] = {
                    id_ins_insumo: row.find("input[name='id_ins_insumo']").val(),
                    id_tipo_ins_insumo: row.find("input[name='id_tipo_ins_insumo']").val(),
                    nombre_insumo: row.find("input[name='nombre_insumo']").val(),
                    unidad_medida_insumo: row.find("span[name='unidad_medida_insumo']").text(),
                    cantidad_insumo: row.find("input[name='cantidad_insumo']").val(),
                    precio_insumo:   row.find("input[name='precio_insumo']").val(),
                };
                return false;
            }
        })

        this.refrescar();
    },

    /* Encargado de retirar el producto seleccionado */
    retirar: function(id)
    {
        /* Declaramos un ID para cada fila */
        $(this.detalle.items).each(function(indice, fila){
            if(indice == id)
            {
                facturador.detalle.items.splice(id, 1);
                return false;
            }
        })

        this.refrescar();
    },

    /* Refresca todo los productos elegidos */
    refrescar: function()
    {
        /* Declaramos un id y calculamos el total */
        $(this.detalle.items).each(function(indice, fila){
            facturador.detalle.items[indice].id = indice;
        })

        var template   = $.templates("#table-detalle-template");
        var htmlOutput = template.render(this.detalle);

        $("#table-detalle").html(htmlOutput);
        feather.replace();
    }
};

$(function() {
    moment.locale('es');
    $('#inventario').addClass("active");
    $('#i-entsal').addClass("active");

    $('#form').formValidation({
        framework: 'bootstrap',
        fields: {
        }
    })

    .on('success.form.fv', function(e) {
        var form = $(this);

        if(facturador.detalle.items.length == 0){
        	Swal.fire({   
                title:'Advertencia',   
                text: 'Ingrese elementos al detalle',
                icon: "warning", 
                confirmButtonColor: "#34d16e",   
                confirmButtonText: "Aceptar",
                allowOutsideClick: false,
                showCancelButton: false,
                showConfirmButton: true
            }, function() {
                return false
            });
        }else{
            facturador.detalle.id_tipo = $('#id_tipo').val();
            facturador.detalle.id_responsable = $('#id_responsable').val();
        	facturador.detalle.motivo = $('#motivo').val();

            var html_confirm = '<div>Se registrará el siguiente ajuste de stock</div>\
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
                        url: $('#url').val()+'inventario/ajuste_crud',                         
                        data: facturador.detalle
                     })
                     .done(function(r){
                        var html_terminado = '<div>Datos registrados correctamente</div>\
                            <br><a href="'+$("#url").val()+'inventario/ajuste" class="btn btn-success">Aceptar</button>'
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
    });
    
    $("#buscar_insumo").autocomplete({
        autoFocus: true,
        dataType: 'JSON',
        delay: 1,
        source: function (request, response) {
            jQuery.ajax({
                url: $('#url').val()+'inventario/ajuste_insumo_buscar',
                type: "post",
                dataType: "json",
                data: {
                    cadena: request.term
                },
                success: function (data) {
                    response($.map(data, function (item) {
                        return {
                            id: item.id_ins,
                            value: item.ins_nom,
                            ins_nom: item.ins_cod + ' | '+item.ins_cat+' | '+item.ins_nom,
                            id_tipo_ins: item.id_tipo_ins,
                            id_med: item.id_med,
                            ins_med: item.ins_med,
                            id_gru: item.id_gru,
                            label: item.ins_cod + ' | '+item.ins_cat+' | '+item.ins_nom
                        }
                    }))
                }
            })
        },
        select: function (e, ui) {
        	$('.nvo-ins').css('display','block');
        	comboUnidadMedida(ui.item.id_gru);
			$('#medida_buscar option[value="'+ui.item.id_med+'"]').prop('selected', true);
            $("#id_ins_buscar").val(ui.item.id);
            $("#id_tipo_ins_buscar").val(ui.item.id_tipo_ins);
            $("#label-insumo").text(ui.item.ins_nom);
            $("#label-medida").text(ui.item.ins_med);
            $("#label-unidad-medida").text(ui.item.ins_med);
            $("#cantidad_buscar").focus();
        },
	    change: function() {
	        $("#buscar_insumo").val('');
	    }
    });
});

/* Combo Unidad de medida */
var comboUnidadMedida = function(cod){
    var var1=0,var2=0;1==cod?(var1=1,var2=1):2==cod?(var1=2,var2=4):3==cod&&(var1=3,var2=4);
    $('#medida_buscar').selectpicker('destroy');
    $.ajax({
        type: "POST",
        url: $('#url').val()+"inventario/combomedida",
        data: {
        	va1: var1,
        	va2: var2
        },
        success: function (response) {
            $('#medida_buscar').html(response);
            $('#medida_buscar').selectpicker();
        },
        error: function () {
            $('#medida_buscar').html('There was an error!');
        }
    });
}

$(".btn-agregar-insumo").click(function(){
    $('#form').formValidation('revalidateField', 'id_tipo');
	$('#form').formValidation('revalidateField', 'motivo');
    var id_ins_insumo = $("#id_ins_buscar"),
    	id_tipo_ins_insumo = $("#id_tipo_ins_buscar"),
        nombre_insumo = $("#label-insumo").text(),
        unidad_medida_insumo = $("#label-medida").text(),
        cantidad_insumo = $("#cantidad_equivalente_buscar").text(),
        precio_insumo = $("#precio_buscar");

    // Validaciones
    if(id_ins_insumo.val() === '0') {
        Swal.fire({   
            title:'Advertencia',   
            text: 'Ingrese elementos al detalle',
            icon: "warning", 
            confirmButtonColor: "#34d16e",   
            confirmButtonText: "Aceptar",
            allowOutsideClick: false,
            showCancelButton: false,
            showConfirmButton: true
        }, function() {
            return false
        });
    }else if(!isNumber($('#cantidad_buscar').val())) {
        Swal.fire({   
            title:'Advertencia',   
            text: 'Ingrese la cantidad al elemento seleccionado',
            icon: "warning", 
            confirmButtonColor: "#34d16e",   
            confirmButtonText: "Aceptar",
            allowOutsideClick: false,
            showCancelButton: false,
            showConfirmButton: true
        }, function() {
            return false
        });
    } else if(!isNumber($('#precio_buscar').val())) {
        Swal.fire({   
            title:'Advertencia',   
            text: 'Ingrese el precio al elemento seleccionado',
            icon: "warning", 
            confirmButtonColor: "#34d16e",   
            confirmButtonText: "Aceptar",
            allowOutsideClick: false,
            showCancelButton: false,
            showConfirmButton: true
        }, function() {
            return false
        });
    }else{
        if(id_tipo_ins_insumo.val() == 1){
            tipo = 'Insumo';
        }else{
            tipo = 'Producto';
        }
        facturador.registrar({
            id_ins_insumo: parseInt(id_ins_insumo.val()),
            id_tipo_ins_insumo: parseInt(id_tipo_ins_insumo.val()),
            nombre_insumo: nombre_insumo,
            tipo: tipo,
            unidad_medida_insumo: unidad_medida_insumo,
            cantidad_insumo: parseFloat(cantidad_insumo),
            precio_insumo: parseFloat(precio_insumo.val()).toFixed(2),
        });
        id_ins_insumo.val('0');
        precio_insumo.val('');
        $("#buscar_insumo").val('');
        $('.nvo-ins').css('display','none');
        $("#cantidad_buscar").val('');
        $('#cantidad_equivalente_buscar').text('0');
    }
});

$('.btn-eliminar-insumo').on('click', function(){
    $('.nvo-ins').css('display','none');
    $("#buscar_insumo").val('');
    $('#precio_buscar').val('');
    $('#cantidad_buscar').val('');
    $('#cantidad_equivalente_buscar').text('0');
});

$('#cantidad_buscar').on('keyup', function(){
	var opc=$("#medida_buscar").val();if(1==opc){var cal=($("#cantidad_buscar").val()/1).toFixed(6);$("#cantidad_equivalente_buscar").text(cal)}else if(2==opc){var cal=($("#cantidad_buscar").val()/1).toFixed(6);$("#cantidad_equivalente_buscar").text(cal)}else if(3==opc){var cal=($("#cantidad_buscar").val()/1e3).toFixed(6);$("#cantidad_equivalente_buscar").text(cal)}else if(4==opc){var cal=($("#cantidad_buscar").val()/1e6).toFixed(6);$("#cantidad_equivalente_buscar").text(cal)}else if(5==opc){var cal=($("#cantidad_buscar").val()/1).toFixed(6);$("#cantidad_equivalente_buscar").text(cal)}else if(6==opc){var cal=($("#cantidad_buscar").val()/1e3).toFixed(6);$("#cantidad_equivalente_buscar").text(cal)}else if(7==opc){var cal=($("#cantidad_buscar").val()/2.20462).toFixed(6);$("#cantidad_equivalente_buscar").text(cal)}else if(8==opc){var cal=($("#cantidad_buscar").val()/35.274).toFixed(6);$("#cantidad_equivalente_buscar").text(cal)}
	$("#buscar_insumo").val('');
});

$('#medida_buscar').on('change', function(){
    var opc=$("#medida_buscar").val();if(1==opc){var cal=($("#cantidad_buscar").val()/1).toFixed(6);$("#cantidad_equivalente_buscar").text(cal)}else if(2==opc){var cal=($("#cantidad_buscar").val()/1).toFixed(6);$("#cantidad_equivalente_buscar").text(cal)}else if(3==opc){var cal=($("#cantidad_buscar").val()/1e3).toFixed(6);$("#cantidad_equivalente_buscar").text(cal)}else if(4==opc){var cal=($("#cantidad_buscar").val()/1e6).toFixed(6);$("#cantidad_equivalente_buscar").text(cal)}else if(5==opc){var cal=($("#cantidad_buscar").val()/1).toFixed(6);$("#cantidad_equivalente_buscar").text(cal)}else if(6==opc){var cal=($("#cantidad_buscar").val()/1e3).toFixed(6);$("#cantidad_equivalente_buscar").text(cal)}else if(7==opc){var cal=($("#cantidad_buscar").val()/2.20462).toFixed(6);$("#cantidad_equivalente_buscar").text(cal)}else if(8==opc){var cal=($("#cantidad_buscar").val()/35.274).toFixed(6);$("#cantidad_equivalente_buscar").text(cal)}
});

function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}