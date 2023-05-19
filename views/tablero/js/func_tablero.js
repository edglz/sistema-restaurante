$(function() {  
    datosGenerales();
    count_pedido();
    sum_portero();
    $('#tablero').addClass("active");
    
    $('#start').bootstrapMaterialDatePicker({
        format: 'DD-MM-YYYY LT',
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });

    $('#end').bootstrapMaterialDatePicker({
        useCurrent: false,
        format: 'DD-MM-YYYY LT',
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });

    $('#start').change( function() {
        datosGenerales();
    });

    $('#end').change( function() {
        datosGenerales();
    });

})
var count_pedido = () => {
    $.ajax({
      type: "POST",
      url: $("#url").val() + "tablero/count_ventas",
      success: function (response) {
        console.log(response);
        var resx = parseInt(response) + 1;
        $(".ventas_portero").html(Number.parseInt(response));
      },
    });
  };
  var sum_portero = () => {
    $.ajax({
      type: "POST",
      url: $("#url").val() + "tablero/ventas_portero",
      success: function (response) {
        $(".count_ventas_portero").html($("#moneda").val() + " " + formatNumber(response));
      },
    });
  };

var datosGenerales = function(){

ifecha = $("#start").val();
ffecha = $("#end").val();
id_apc = $("#id_caja").val();

$('#lista_platos').empty();
$('#lista_productos').empty();

$.ajax({
    type: "POST",
    url: $('#url').val()+"tablero/tablero_datos",
    data: {
        //ifecha: ifecha,
        //ffecha: ffecha,
        id_apc: id_apc
    },
    dataType: "json",
    success: function(item){
        var moneda = $("#moneda").val();
        var totalVentas = (parseFloat(item['Ventas'].pago_efe) + parseFloat(item['Ventas'].pago_tar) + parseFloat(item['Ventas'].comis_tar)).toFixed(2);
        var efectivoReal = (parseFloat(item['Ventas'].pago_efe) + parseFloat(item['Ingresos'].total) - parseFloat(item['Egresos'].total)).toFixed(2);
        if(item['Ventas'].total != '0.00'){
        var efectivo = (parseFloat(item['Ventas'].pago_efe) * 100 ) / parseFloat(totalVentas);
        var tarjeta = (parseFloat(item['Ventas'].pago_tar) * 100 ) / parseFloat(totalVentas);
        //var meta = (parseFloat(totalVentas) * 100 ) / parseFloat(item['data13'].margen);
        } else { var efectivo = 0; var tarjeta = 0; var meta = 0; }

        $('.pago_efe').text(moneda+" "+formatNumber(item['Ventas'].pago_efe));
        $('.pago_tar').text(moneda+" "+formatNumber(parseFloat(item['Ventas'].pago_tar) + parseFloat(item['Ventas'].comis_tar)));
        $('.descuentos').text(moneda+" "+formatNumber(item['Ventas'].descuento));
        $('.comision-delivery').text(moneda+" "+formatNumber(item['Ventas'].comis_delivery));
        //$('.pollos-vendidos').text(item['Pollostock'].total);
        $('.pollos-stock').text(item['Pollostock'].total);
        $('.ingresos').text(moneda+" "+formatNumber(item['Ingresos'].total));
        $('.egresos').text(moneda+" "+formatNumber(item['Egresos'].total));
        $('.total_ventas').text(moneda+" "+formatNumber(totalVentas));
        $('.efectivo_real').text(moneda+" "+formatNumber(efectivoReal));
        $('.pago_efe_porcentaje').text(formatNumber(efectivo)+"%");
        $('.pago_tar_porcentaje').text(formatNumber(tarjeta)+"%");
        $('.pago_efe_progressbar').css('width', $('.pago_efe_porcentaje').text());
        $('.pago_tar_progressbar').css('width', $('.pago_tar_porcentaje').text());

        $('.monto-venta-salon').text(moneda+" "+formatNumber(item['CanalSalon'].total_ventas));
        $('.cantidad-venta-salon').text(item['CanalSalon'].cantidad_ventas);
        $('.monto-venta-mostrador').text(moneda+" "+formatNumber(item['CanalMostrador'].total_ventas));
        $('.cantidad-venta-mostrador').text(item['CanalMostrador'].cantidad_ventas);
        $('.monto-venta-delivery').text(moneda+" "+formatNumber(item['CanalDelivery'].total_ventas));
        $('.cantidad-venta-delivery').text(item['CanalDelivery'].cantidad_ventas);

        $('.monto-venta-salon-i').text(moneda+" "+formatNumber(item['CanalSalonAnulados'].total_ventas));
        $('.cantidad-venta-salon-i').text(item['CanalSalonAnulados'].cantidad_ventas);
        $('.monto-venta-mostrador-i').text(moneda+" "+formatNumber(item['CanalMostradorAnulados'].total_ventas));
        $('.cantidad-venta-mostrador-i').text(item['CanalMostradorAnulados'].cantidad_ventas);
        $('.monto-venta-delivery-i').text(moneda+" "+formatNumber(item['CanalDeliveryAnulados'].total_ventas));
        $('.cantidad-venta-delivery-i').text(item['CanalDeliveryAnulados'].cantidad_ventas);

        var pollos_vendidos = 0;
        $.each(item['Pollosvendidos'], function(i, dato) { 
            pollos_vendidos += parseFloat(dato.cantidad) * parseFloat(dato.cant);
        });
        //pollos_vendidos = pollos_vendidos;
        $('.pollos-vendidos').text(parseFloat(pollos_vendidos));
        //alert(pollos_vendidos);


        /*
        if(item['data3'].tped != undefined){
        var pedidosPorcentaje = (parseFloat(item['data3'].tped) * 100 ) / parseFloat(item['data4'].toped);
        $('#mozo').text(item['data3'].nombres+' '+item['data3'].ape_paterno);
        $('#pedidos').text(item['data3'].tped+' pedido(s)');
        $('#t_ped').text((pedidosPorcentaje).toFixed(2));
        } else {$('#pedidos').text('0 pedido(s)'); $('#mozo').text('A la espera'); $('#t_ped').text('0.00'); }
        */
        /*
        if(item['data6'].total_v != '0.00'){
        var totalVentasMesas = (parseFloat(item['data6'].total_v) / parseFloat(item['data5'].total));
        } else { var totalVentasMesas = 0; var totalVentasMostrador = 0; }
        if(item['data7'].total_v != '0.00'){
        var totalVentasMostrador = (parseFloat(item['data7'].total_v) / parseFloat(item['data8'].total));
        } else { var totalVentasMostrador = 0; }
        */
        /*
        $('.t_mesas').text(formatNumber(item['data5'].total));
        $('#pro_m').text(moneda+" "+formatNumber(totalVentasMesas));
        $('.t_most').text(formatNumber(item['data8'].total));
        $('#pro_mo').text(moneda+" "+formatNumber(totalVentasMostrador));
        $('#pa_me').text(formatNumber(item['data11'].total));
        $('#pa_mo').text(formatNumber(item['data12'].total));
        */
        //$('#meta_a').text((meta).toFixed(2)+"%");

        if(item['Platos'].length > 0){
            var con = 1;
            $.each(item['Platos'], function(i, dato) {
                var importeTodos = parseFloat(dato.cantidad) * parseFloat(dato.precio);
                var porcentajeTodos = (parseFloat(importeTodos) * 100 ) / parseFloat(item['Ventas'].total);
                $('#lista_platos')
                    .append(
                    $('<tr/>')
                    .append(
                        $('<td style="width:50px;"/>')
                        .html('<span class="round round-warning">'+con+++'</span>')
                    )
                    .append(
                        $('<td/>')
                        .html('<h6>'+dato.pro_nom+'</h6><small class="text-muted">'+dato.pro_pre+'</small>')
                    )
                    .append(
                        $('<td/>')
                        .html(formatNumber(dato.total))
                    )
                    .append(
                        $('<td/>')
                        .html(moneda+" "+formatNumber(importeTodos))
                    )
                    .append(
                        $('<td class="text-right text-success"/>')
                        .html(formatNumber(porcentajeTodos)+'%')
                    )
                )
            });
        } else {
            $('#lista_platos').html("<tr style='border-left: 2px solid #fff !important; background: #fff !important;'><td colspan='5'><div class='text-center'><h4 class='m-t-40' style='color: #d3d3d3;'><i class='mdi mdi-receipt display-3 m-t-40 m-b-10'></i><br>Realice una venta<br><small>No se encontraron datos <br>en el periodo de tiempo seleccionado</small></h4></div></td></tr>");
        }

        if(item['Productos'].length > 0){
            var cont = 1;
            $.each(item['Productos'], function(i, datu) {
                var importePlatos = parseFloat(datu.cantidad) * parseFloat(datu.precio);
                var porcentajePlatos = (parseFloat(importePlatos) * 100 ) / parseFloat(item['Ventas'].total);
                $('#lista_productos')
                  .append(
                    $('<tr/>')
                    .append(
                        $('<td style="width:50px;"/>')
                        .html('<span class="round">'+cont+++'</span>')
                    )
                    .append(
                        $('<td/>')
                        .html('<h6>'+datu.pro_nom+'</h6><small class="text-muted">'+datu.pro_pre+'</small>')
                    )
                    .append(
                        $('<td/>')
                        .html(formatNumber(datu.total))
                    )
                    .append(
                        $('<td/>')
                        .html(moneda+" "+formatNumber(importePlatos))
                    )
                    .append(
                        $('<td class="text-right text-success"/>')
                        .html(formatNumber(porcentajePlatos)+'%')
                    )
                )
            });
        } else {
            $('#lista_productos').html("<tr style='border-left: 2px solid #fff !important; background: #fff !important;'><td colspan='5'><div class='text-center'><h4 class='m-t-40' style='color: #d3d3d3;'><i class='mdi mdi-receipt display-3 m-t-40 m-b-10'></i><br>Realice una venta<br><small>No se encontraron datos <br>en el periodo de tiempo seleccionado</small></h4></div></td></tr>");
        }
    }
  });
}