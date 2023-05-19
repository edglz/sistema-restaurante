/* Combo Unidad de medida */
var comboUnidadMedida = function(cod){
    var var1=0,var2=0;1==cod?(var1=1,var2=1):2==cod?(var1=2,var2=4):3==cod&&(var1=3,var2=4);
    $('#id_med_receta').selectpicker('destroy');
    $.ajax({
        type: "POST",
        url: $('#url').val()+"ajuste/producto_combo_unimed",
        data: {
            va1: var1,
            va2: var2
        },
        success: function (response) {
            $('#id_med_receta').html(response);
            $('#id_med_receta').selectpicker();
        },
        error: function () {
            $('#id_med_receta').html('There was an error!');
        }
    });
}

$("#form-receta").submit(function(){

    if($('#cant_receta').val() == ''){
        $.toast({
            heading: 'Advertencia',
            text: 'Ingrese cantidad',
            position: 'bottom-left',
            loaderBg:'#696969',
            icon: 'warning',
            hideAfter: 3000, 
            stack: 20
        });
        return false;
    }else {

        var form = $(this);

        id_pres=$('#id_pres_receta').val();
        id_ins=$('#id_ins_receta').val();
        id_tipo_ins=$('#id_tipo_ins_receta').val();
        id_med=$('#id_med_receta').val();
        cant=$('#valor_ing').text();
      
        $.ajax({
            dataType: 'json',
            type: 'POST',
            url: $('#url').val()+'ajuste/producto_ingrediente_create',
            data: {
                id_pres: id_pres,
                id_ins: id_ins,
                id_tipo_ins: id_tipo_ins,
                id_med: id_med,
                cant: cant
            },
              
            success: function (datos) {
                $('.list-ingredientes').css('display','none');
                listarReceta();
                $('#cant_receta').val('');
                $('#valor_ing').text('0');
                $.toast({
                    heading: 'Proceso Terminado',
                    text: 'Ingrediente a&ntilde;adido',
                    position: 'bottom-left',
                    loaderBg:'#696969',
                    icon: 'success',
                    hideAfter: 3000, 
                    stack: 20
                });
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown + ' ' + textStatus);
            }   
        });
        return false;
    }
    
});

$(function() {
/* Busqueda del insumo */
$("#buscar_ingrediente").autocomplete({
    delay: 1,
    autoFocus: true,
    dataType: 'JSON',
    source: function (request, response) {
        $.ajax({
            url: $('#url').val()+'ajuste/producto_buscar_ins',
            type: "post",
            dataType: "json",
            data: {
                cadena: request.term,
                tipo: $("#cod_ti").val()
            },
            success: function (data) {
                response($.map(data, function (item) {
                    return {
                        id_ins: item.id_ins,
                        id_tipo_ins: item.id_tipo_ins,
                        cod_ins: item.ins_cod,
                        ins_nom: item.ins_cod + ' | '+item.ins_cat+' | '+item.ins_nom,
                        ins_med: item.ins_med,
                        id_med: item.id_med,
                        id_gru: item.id_gru,
                        label: item.ins_cod + ' | '+item.ins_cat+' | '+item.ins_nom
                    }
                }))
            }
        })
    },
    select: function (e, ui) {
        comboUnidadMedida(ui.item.id_gru);
        $('#id_med_receta option[value="'+ui.item.id_med+'"]').prop('selected', true);
        $('#insumo').text(ui.item.ins_nom);
        $('#id_ins_receta').val(ui.item.id_ins);
        $('#id_tipo_ins_receta').val(ui.item.id_tipo_ins);
        $('#desc_medida').text(ui.item.ins_med);
        $('#medida').text(ui.item.ins_med);
        $('.list-ingredientes').css('display','block');
        $('#buscar_ingrediente').val('');
        $('#cant_receta').focus();
        $('#cant_receta').val('');
        $('#valor_ing').text('0');
    },
    change: function() {
        $("#buscar_ingrediente").val('');
        $('#valor_ing').text('0');
        $("#cant_receta").focus();
    }
}); 

$("#buscar_ingrediente").autocomplete("option", "appendTo", ".form-receta"); 
});

$('#cant_receta').keyup( function() {
    var opc=$("#id_med_receta").val();if(1==opc){var cal=($("#cant_receta").val()/1).toFixed(6);$("#valor_ing").text(cal)}else if(2==opc){var cal=($("#cant_receta").val()/1).toFixed(6);$("#valor_ing").text(cal)}else if(3==opc){var cal=($("#cant_receta").val()/1e3).toFixed(6);$("#valor_ing").text(cal)}else if(4==opc){var cal=($("#cant_receta").val()/1e6).toFixed(6);$("#valor_ing").text(cal)}else if(5==opc){var cal=($("#cant_receta").val()/1).toFixed(6);$("#valor_ing").text(cal)}else if(6==opc){var cal=($("#cant_receta").val()/1e3).toFixed(6);$("#valor_ing").text(cal)}else if(7==opc){var cal=($("#cant_receta").val()/2.20462).toFixed(6);$("#valor_ing").text(cal)}else if(8==opc){var cal=($("#cant_receta").val()/35.274).toFixed(6);$("#valor_ing").text(cal)}
    $('#buscar_ingrediente').val('');
});

$('#id_med_receta').on('change', function(){
    var opc=$("#id_med_receta").val();if(1==opc){var cal=($("#cant_receta").val()/1).toFixed(6);$("#valor_ing").text(cal)}else if(2==opc){var cal=($("#cant_receta").val()/1).toFixed(6);$("#valor_ing").text(cal)}else if(3==opc){var cal=($("#cant_receta").val()/1e3).toFixed(6);$("#valor_ing").text(cal)}else if(4==opc){var cal=($("#cant_receta").val()/1e6).toFixed(6);$("#valor_ing").text(cal)}else if(5==opc){var cal=($("#cant_receta").val()/1).toFixed(6);$("#valor_ing").text(cal)}else if(6==opc){var cal=($("#cant_receta").val()/1e3).toFixed(6);$("#valor_ing").text(cal)}else if(7==opc){var cal=($("#cant_receta").val()/2.20462).toFixed(6);$("#valor_ing").text(cal)}else if(8==opc){var cal=($("#cant_receta").val()/35.274).toFixed(6);$("#valor_ing").text(cal)}
});

