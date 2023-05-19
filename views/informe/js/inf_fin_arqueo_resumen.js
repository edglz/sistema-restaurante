var moneda = $("#moneda").val();
$(function() {
    defaultdata();
    venta_list(1,'a');
    $('.nav-egr').css('display','none');
    $('#informes').addClass("active");
    $('.scroll-1').slimscroll({
        height: '100%'
    });
    $('.scroll-2').slimscroll({
        height: '100%'
    });
    $('.scroll-3').slimscroll({
        height: '100%'
    });
    var var1 = function () {
            var topOffset = 440;
            var height = ((window.innerHeight > 0) ? window.innerHeight : this.screen.height) - 1;
            height = height - topOffset;
            $(".scroll-1").css("height", (height) + "px");
    };
    $(window).ready(var1);
    $(window).on("resize", var1);
    var var2 = function () {
            var topOffset = ($('#opc_02').val() == 1) ? 400 : 305;
            var height = ((window.innerHeight > 0) ? window.innerHeight : this.screen.height) - 1;
            height = height - topOffset;
            $(".scroll-2").css("height", (height) + "px");
    };
    $(window).ready(var2);
    $(window).on("resize", var2);
    var var3 = function () {
            var topOffset = 378;
            var height = ((window.innerHeight > 0) ? window.innerHeight : this.screen.height) - 1;
            height = height - topOffset;
            $(".scroll-3").css("height", (height) + "px");
    };
    $(window).ready(var3);
    $(window).on("resize", var3);
})

var ingresos = function(){
    $('.text-tab').text('Ingresos');
    $('.nav-ing').css('display','block');
    $('.nav-egr').css('display','none');
    $('.nav-ing-1').addClass('active');
    $('.nav-ing-2').removeClass('active');
    $('.panel-ing-1').addClass('active');
    $('.panel-ing-2').removeClass('active');
    $('.nav-egr-1').removeClass('active');
    $('.panel-egr-1').removeClass('active');
    $('.btn-est-1').addClass('active');
    $('.btn-est-2').removeClass('active');
    venta_list(1,'a');
}

var egresos = function(){
    $('.text-tab').text('Egresos');
    $('.nav-ing').css('display','none');
    $('.nav-egr').css('display','block');
    $('.nav-ing-1').removeClass('active');
    $('.nav-ing-2').removeClass('active');
    $('.panel-ing-1').removeClass('active');
    $('.panel-ing-2').removeClass('active');
    $('.nav-egr-1').addClass('active');
    $('.panel-egr-1').addClass('active');
    $('.btn-est-1').addClass('active');
    $('.btn-est-2').removeClass('active');
    caja_list_e('a');
}

var defaultdata = function(){
    $.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: $('#url').val()+'informe/finanza_arq_resumen_default',
        data: {
            cod_ape: $('#cod_ape').val()
        },
        success: function (item) {
            if ( item.Apertura.estado == 'a' ) {
                $(".bg-estado").addClass('bg-info');
                $(".text-estado").text('APERTURADO');
                var fechaCierre = '-';
            } else{
                $(".bg-estado").addClass('bg-danger');
                $(".text-estado").text('CERRADO');
                var fechaCierre = moment(item.Apertura.fecha_cierre).format('Do MMMM YYYY, hh:mm A');              
            }
            $(".text-codigo").text('COD0'+item.Apertura.id_apc);
            var fechaApertura = moment(item.Apertura.fecha_aper).format('Do MMMM YYYY, hh:mm A');
            $(".c-usuario").text(item.Apertura.desc_per);
            $(".c-caja").text(item.Apertura.desc_caja);
            $(".c-turno").text(item.Apertura.desc_turno);
            $(".c-fecha-apertura").text(fechaApertura);
            $(".c-fecha-cierre").text(fechaCierre);
            $(".c-monto-apertura").text(moneda+' '+formatNumber(item.Apertura.monto_aper));

            var totalIng = (parseFloat(item.total) + parseFloat(item.Ingresos.total)).toFixed(2);
            $('.c-total-ingreso').html(moneda+' '+formatNumber(totalIng));

            var totalEgr = (parseFloat(item.EgresosA.total) + parseFloat(item.EgresosB.total) ).toFixed(2);
            $('.c-total-egreso').html(moneda+' '+formatNumber(totalEgr));

            var montoEstimado = (parseFloat(item.Apertura.monto_aper) + parseFloat(totalIng) - parseFloat(totalEgr)).toFixed(2);
            $(".c-monto-estimado").html(moneda+' '+formatNumber(montoEstimado));

            $(".c-monto-cierre").html(moneda+' '+formatNumber(item.Apertura.monto_cierre));

            var montoEfectivo = (parseFloat(item.Apertura.monto_aper) + (parseFloat(totalIng) - parseFloat(item.pago_tar)) - parseFloat(totalEgr)).toFixed(2);
            $(".c-monto-efectivo").html(moneda+' '+formatNumber(montoEfectivo));

            var montoDiferencia = (parseFloat(montoEfectivo) - parseFloat(item.Apertura.monto_cierre)).toFixed(2);
            $(".c-monto-diferencia").html(moneda+' '+formatNumber(montoDiferencia * -1));
            if(montoDiferencia > 0){
                $(".name-c-monto-diferencia").html('(Faltante)');
            } else if(montoDiferencia < 0){
                $(".name-c-monto-diferencia").html('(Sobrante)');
            } else {
                $(".name-c-monto-diferencia").html('');
            }
            
            $(".c-monto-tarjeta").html(moneda+' '+formatNumber(item.pago_tar));

            $('.c-pollos-stock').text(item.Apertura.stock_pollo);
        }
    });
};

var venta_list = function(cod_filtro,estado){
    $('.btn-est-1').addClass('active');
    $('.btn-est-2').removeClass('active');
    $('#list-venta').empty();
    var total = 0,
        desc = 0,
        count = 1;
    $.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: $('#url').val()+'informe/finanza_arq_resumen_venta_list',
        data : {
            cod_ape: $('#cod_ape').val(),
            cod_filtro : cod_filtro,
            estado : estado
        },
        success: function (data) {
            if(data.length > 0){                
                $.each(data, function(i, item) {
                    bg = (item.estado == 'a') ? bg = "" : bg = "#ffe0e0";
                    total += parseFloat(item.monto_total);
                    desc += parseFloat(item.desc_monto);
                    $('#list-venta')
                    .append(
                        $('<tr class="tr-left" style="background:'+bg+'"/>')
                        .append(
                            $('<td/>')
                            .html(item.desc_td)
                        )
                        .append(
                            $('<td/>')
                            .html(item.ser_doc+'-'+item.nro_doc)
                        )
                        .append(
                            $('<td/>')
                            .html(moneda+' '+formatNumber(item.desc_monto))
                        )
                        .append(
                            $('<td class="text-right"/>')
                            .html(moneda+' '+formatNumber(item.monto_total))
                        )
                    );                    
                });
            } else {
                $('#list-venta').html("<tr style='border-left: 2px solid #fff !important; background: #fff !important;'><td colspan='5'><div class='text-center'><h4 class='m-t-40' style='color: #c3c3c3;'><i class='mdi mdi-alert-circle display-3 m-t-40 m-b-10'></i><br><small>No se encontraron datos</small></h4></div></td></tr>");
            }
            $('.ventas-monto').text(moneda+' '+formatNumber(total));
            $('.ventas-desc').text(moneda+' '+formatNumber(desc));
            $('.ventas-oper').text(data.length);
        }
    });
}

var venta_delivery_list = function(estado){
    $('#list-venta-delivery').empty();
    var total = 0,
        desc = 0,
        count = 1;
    $.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: $('#url').val()+'informe/finanza_arq_resumen_venta_delivery_list',
        data : {
            cod_ape: $('#cod_ape').val(),
            estado : estado
        },
        success: function (data) {
            if(data.length > 0){                
                $.each(data, function(i, item) {
                    bg = (item.estado == 'a') ? bg = "" : bg = "#ffe0e0";
                    total += parseFloat(item.monto_total);
                    desc += parseFloat(item.descu);
                    $('#list-venta-delivery')
                    .append(
                        $('<tr class="tr-left" style="background:'+bg+'"/>')
                        .append(
                            $('<td/>')
                            .html(count++)
                        )
                        .append(
                            $('<td/>')
                            .html(item.desc_td)
                        )
                        .append(
                            $('<td/>')
                            .html(item.ser_doc+'-'+item.nro_doc)
                        )
                        .append(
                            $('<td/>')
                            .html(moneda+' '+formatNumber(item.descu))
                        )
                        .append(
                            $('<td class="text-right"/>')
                            .html(moneda+' '+formatNumber(item.monto_total))
                        )
                    );                    
                });
            } else {
                $('#list-venta-delivery').html("<tr style='border-left: 2px solid #fff !important; background: #fff !important;'><td colspan='5'><div class='text-center'><h4 class='m-t-40' style='color: #c3c3c3;'><i class='mdi mdi-alert-circle display-3 m-t-40 m-b-10'></i><br><small>No se encontraron datos</small></h4></div></td></tr>");
            }
            $('.ventas-delivery-monto').text(moneda+' '+formatNumber(total));
            $('.ventas-delivery-desc').text(moneda+' '+formatNumber(desc));
            $('.ventas-delivery-oper').text(data.length);
        }
    });
}

var caja_list_i = function(estado){
    $('.btn-est-1').addClass('active');
    $('.btn-est-2').removeClass('active');
    $('#list-caja-i').empty();
    var total = 0,
        count = 1;
    $.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: $('#url').val()+'informe/finanza_arq_resumen_caja_list_i',
        data : {
            id_apc : $('#cod_ape').val(),
            estado : estado
        },
        success: function (data) {
            if(data.length > 0){
                $.each(data, function(i, item) {
                    bg = (item.estado == 'a') ? bg = "" : bg = "#ffe0e0";
                    total += parseFloat(item.importe);                
                    $('#list-caja-i')
                    .append(
                        $('<tr class="tr-left" style="background:'+bg+'"/>')
                        .append(
                            $('<td/>')
                            .html(item.responsable)
                        )
                        .append(
                            $('<td/>')
                            .html(item.motivo)
                        )
                        .append(
                            $('<td class="text-right"/>')
                            .html(moneda+' '+formatNumber(item.importe))
                        )
                    );
                });
            } else {
                $('#list-caja-i').html("<tr style='border-left: 2px solid #fff !important; background: #fff !important;'><td colspan='3'><div class='text-center'><h4 class='m-t-40' style='color: #c3c3c3;'><i class='mdi mdi-alert-circle display-3 m-t-40 m-b-10'></i><br><small>No se encontraron datos</small></h4></div></td></tr>");
            }
            $('.caja-monto').text(moneda+' '+formatNumber(total));
            $('.caja-oper').text(data.length);
        }
    });
}

var caja_list_e = function(estado){
    $('.btn-est-1').addClass('active');
    $('.btn-est-2').removeClass('active');
    $('#list-caja-e').empty();
    var total = 0,
        count = 1;
    $.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: $('#url').val()+'informe/finanza_arq_resumen_caja_list_e',
        data : {
            id_apc : $('#cod_ape').val(),
            estado : estado
        },
        success: function (data) {
            if(data.length > 0){
                $.each(data, function(i, item) {
                    bg = (item.estado == 'a') ? bg = "" : bg = "#ffe0e0";
                    total += parseFloat(item.importe);
                    $('#list-caja-e')
                    .append(
                        $('<tr class="tr-left" style="background:'+bg+'"/>')
                        .append(
                            $('<td width="15%"/>')
                            .html(item.des_tg)
                        )
                        .append(
                            $('<td width="30%"/>')
                            .html(item.responsable)
                        )
                        .append(
                            $('<td width="35%"/>')
                            .html(item.motivo)
                        )
                        .append(
                            $('<td class="text-right" width="20%"/>')
                            .html(moneda+' '+formatNumber(item.importe))
                        )
                    );
                });
            } else {
                $('#list-caja-e').html("<tr style='border-left: 2px solid #fff !important; background: #fff !important;'><td colspan='3'><div class='text-center'><h4 class='m-t-40' style='color: #c3c3c3;'><i class='mdi mdi-alert-circle display-3 m-t-40 m-b-10'></i><br><small>No se encontraron datos</small></h4></div></td></tr>");
            }            
            $('.caja-monto').text(moneda+' '+formatNumber(total));
            $('.caja-oper').text(data.length);
        }
    });
}

var productos = function(){
    pollos_vendidos();
    $('#list-productos').empty();
    var total = 0,
        count = 1;

    $.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: $('#url').val()+'informe/finanza_arq_resumen_productos',
        data : {
            id_apc: $('#cod_ape').val()
        },
        success: function (data) {
            if(data.length > 0){
                $.each(data, function(i, item) {
                    total = parseFloat(item.cantidad) * parseFloat(item.precio);
                    $('#list-productos ')
                    .append(
                        $('<tr class="tr-left"/>')
                        .append(
                            $('<td/>')
                            .html(item.Producto.pro_nom+' <span class="label label-warning">'+item.Producto.pro_pre+'</span>')
                        )
                        .append(
                            $('<td class="text-right"/>')
                            .html(item.cantidad)
                        )
                        .append(
                            $('<td class="text-right"/>')
                            .html(moneda+' '+formatNumber(total))
                        )
                    );
                });
            } else {
                $('#list-productos').html("<tr style='border-left: 2px solid #fff !important; background: #fff !important;'><td colspan='4'><div class='text-center'><h4 class='m-t-40' style='color: #c3c3c3;'><i class='mdi mdi-alert-circle display-3 m-t-40 m-b-10'></i><br><small>No se encontraron datos</small></h4></div></td></tr>");
            }
        }
    });
}

var anulaciones = function(){
    $('#list-anulaciones').empty();
    var total = 0,
        sub_total = 0,
        oper = 0,
        count = 1;

    $.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: $('#url').val()+'informe/finanza_arq_resumen_anulaciones',
        data : {
            cod_ape: $('#cod_ape').val()
        },
        success: function (data) {
            if(data.length > 0){
                $.each(data, function(i, item) {
                    sub_total = parseFloat(item.cant) * parseFloat(item.precio);
                    total += parseFloat(sub_total);
                    $('#list-anulaciones')
                    .append(
                        $('<tr class="tr-left"/>')
                        .append(
                            $('<td/>')
                            .html(item.Producto.pro_nom+' <span class="label label-warning">'+item.Producto.pro_pre+'</span>')
                        )
                        .append(
                            $('<td class="text-right"/>')
                            .html(item.cant)
                        )
                        .append(
                            $('<td class="text-right"/>')
                            .html(moneda+' '+formatNumber(sub_total))
                        )
                    );
                });                
            } else {
                $('#list-anulaciones').html("<tr style='border-left: 2px solid #fff !important; background: #fff !important;'><td colspan='4'><div class='text-center'><h4 class='m-t-40' style='color: #c3c3c3;'><i class='mdi mdi-alert-circle display-3 m-t-40 m-b-10'></i><br><small>No se encontraron datos</small></h4></div></td></tr>");
            }
            $('.anul-monto').text(moneda+' '+formatNumber(total));
            $('.anul-oper').text(data.length);
        }
    });
}

var pollos_vendidos = function(){
    $.ajax({
        type: "POST",
        url: $('#url').val()+"tablero/tablero_datos",
        data: {
            id_apc: $('#cod_ape').val()
        },
        dataType: "json",
        success: function(item){
            var pollos_vendidos = 0;
            $.each(item['Pollosvendidos'], function(i, dato) { 
                pollos_vendidos += parseFloat(dato.cantidad) * parseFloat(dato.cant);
            });
            $('.c-pollos-vendidos').text(parseFloat(pollos_vendidos));
        }
    });
}

var canales = function(){
    $.ajax({
        type: "POST",
        url: $('#url').val()+"tablero/tablero_datos",
        data: {
            id_apc: $('#cod_ape').val()
        },
        dataType: "json",
        success: function(item){
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
        }
    });
}