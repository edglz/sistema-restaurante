$(function() {
    moment.locale('es');
    ComboInsumoProducto(1);
    listar();
    $('#inventario').addClass("active");
    $('#i-karval').addClass("active");

    $('#start').bootstrapMaterialDatePicker({
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });

    $('#end').bootstrapMaterialDatePicker({
        useCurrent: false,
        format: 'DD-MM-YYYY',
        time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });

});

$('#start, #end, #id_ip').change( function() {
    listar();
});

$("#tipo_ip").change(function(){
    $('#id_ip').find('option').remove();
    $("#tipo_ip option:selected").each(function(){
    id_tipo_ins=$(this).val();
       $.post("ComboInsumoProducto",{id_tipo_ins: id_tipo_ins},function(data){
           $("#id_ip").html(data);
           $('#id_ip').selectpicker();
           $('#id_ip').selectpicker('refresh');
       });
    });
})

var ComboInsumoProducto = function(id_tipo_ins){
    $('#id_ip').find('option').remove();
    $.ajax({
        type: "POST",
        url: $('#url').val()+"inventario/ComboInsumoProducto",
        data: {id_tipo_ins: id_tipo_ins},
        success: function (response) {
            $('#id_ip').html(response);
            $('#id_ip').selectpicker();
            $('#id_ip').selectpicker('refresh');
        },
        error: function () {
            $('#id_ip').html('There was an error!');
        }
    });
}

var listar = function(){

    tipo_ip = $("#tipo_ip").val();
    id_ip = $("#id_ip").val();
    ifecha = $("#start").val();
    ffecha = $("#end").val();

    var stock_entradas = 0,
        stock_salidas = 0,
        stock_final = 0,
        stock_inicial = 0,
        medida = 0;

    $.ajax({
        type: "POST",
        url: $('#url').val()+"inventario/kardex_list",
        data: { 
            tipo_ip: tipo_ip,
            id_ip: id_ip,
            ifecha: ifecha,
            ffecha: ffecha
        },
        dataType: "json",
        success: function (item) {
            if (item.data.length != 0) {
                $.each(item.data, function(i, campo) {
                    if(campo.estado == 'a'){
                        medida = campo.Medida.ins_med;
                        stock_entradas += parseFloat(campo.cantidad_entrada);
                        stock_salidas += parseFloat(campo.cantidad_salida);
                        stock_final = parseFloat(campo.Stock.total);
                    }
                });
                stock_inicial = stock_final - (stock_entradas - stock_salidas);
                $('.stock-inicial').html((stock_inicial).toFixed(4)+' <sup>'+medida+'</sup>');
                $('.stock-entradas').html((stock_entradas).toFixed(4)+' <sup>'+medida+'</sup>');
                $('.stock-salidas').html((stock_salidas).toFixed(4)+' <sup>'+medida+'</sup>');
                $('.stock-final').html((stock_final).toFixed(4)+' <sup>'+medida+'</sup>');
            } else {
                $('.stock-inicial').html('0.0000');
                $('.stock-entradas').html('0.0000');
                $('.stock-salidas').html('0.0000');
                $('.stock-final').html('0.0000');
            }
        }
    });

    var stock = 0,
        desc = '';

    var cante = 0,
        cants = 0,
        cantt = 0,
        ent = 0,
        sal = 0,
        tote = 0,
        tots = 0,
        tott = 0,
        scu = 0,
        medida = '';
        table = $('#table')
        .DataTable({
            "destroy": true,
            "responsive": true,
            "dom": "tip",
            "bSort": false,
            "pageLength": 50,
            "ajax":{
            "method": "POST",
            "url": $('#url').val()+"inventario/kardex_list",
            "data": {
                ifecha: ifecha,
                ffecha: ffecha,
                tipo_ip: tipo_ip,
                id_ip: id_ip
                }
            },
            "columns":[
                {"data":"fecha_r","render": function ( data, type, row ) {
                    return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y')
                            +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data).format('h:mm A')+'</span>';
                }},
                {"data": null,"render": function ( data, type, row ) {
                    if(data.estado == 'a'){
                        var estado = '';
                        var label = '';
                    } else {
                        var estado = 'text-danger';
                        var label = '<span class="label label-danger">ANULADO</span>';
                    }
                    if(data.id_tipo_ope == 1){
                        return 'ENTRADA, POR COMPRA. '+label+'<br><span class="font-12 '+estado+'">'+data.Comp.desc_td+' '+data.Comp.ser_doc+'-'+data.Comp.nro_doc+'</span>';
                    }else if(data.id_tipo_ope == 2){
                        return 'SALIDA, POR VENTA. '+label+'<br><span class="font-12 '+estado+'">'+data.Comp.desc_td+' '+data.Comp.ser_doc+'-'+data.Comp.nro_doc+'</span>';
                    }else if(data.id_tipo_ope == 3){
                        return 'ENTRADA, POR AJUSTE DE STOCK. '+label+'<br><span class="font-12 '+estado+'">Responsable:, '+data.Comp.responsable+', '+data.Comp.motivo+'</span>';
                    }else if(data.id_tipo_ope == 4){
                        return 'SALIDA, POR AJUSTE DE STOCK. '+label+'<br><span class="font-12 '+estado+'">Responsable, '+data.Comp.responsable+', '+data.Comp.motivo+'</span>';
                    }
                }},
                {"data": "cantidad_entrada",
                "className": "text-success text-left",
                "render": function ( data, type, row ) {
                    if(data == 0){
                        return '-';
                    } else {
                        return data;
                    }
                }},
                {"data": "costo_entrada","render": function ( data, type, row ) {
                    if(data == 0){
                        return '-';
                    } else {
                        return formatNumber(data);
                    }
                }},
                {"data": "total_entrada","render": function ( data, type, row ) {
                    if(data == 0){
                        return '-';
                    } else {
                        return data;
                    }
                }},
                {"data": "cantidad_salida",
                "className": "text-danger text-left",
                "render": function ( data, type, row ) {
                    if(data == 0){
                        return '-';
                    } else {
                        return formatNumber(data);
                    }
                }},
                {"data": "costo_salida","render": function ( data, type, row ) {
                    if(data == 0){
                        return '-';
                    } else {
                        return formatNumber(data);
                    }
                }},
                {"data": "total_salida","render": function ( data, type, row ) {
                    if(data == 0){
                        return '-';
                    } else {
                        return formatNumber(data);
                    }
                }},
                {"data": null,"render": function ( data, type, row ) {
                   
                    if(data.id_tipo_ope == 1 || data.id_tipo_ope == 3){
                        var ent = data.cant;
                    } else {
                        ent = 0;
                    }

                    if(data.id_tipo_ope == 2 || data.id_tipo_ope == 4){
                        var sal = data.cant;
                    } else {
                        sal = 0;
                    }
              
                    if(data.estado == 'a'){
                        cantt = (ent-sal) + cantt;
                        return '<div class="text-info text-center">'+(stock_inicial + cantt).toFixed(6)+'</div>';
                    } else {
                        return '<div class="text-info text-center">'+(stock_inicial + cantt).toFixed(6)+'</div>';
                    }
                    
                }},
                {"data": null,"render": function ( data, type, row ) {
                   
                    if(data.id_tipo_ope == 1 || data.id_tipo_ope == 3){
                        scu = formatNumber(data.cos_uni);
                    } else if(data.id_tipo_ope == 2 || data.id_tipo_ope == 4){
                        scu = formatNumber(data.Precio.cos_pro);
                    }               
                    return scu;
                }},
                {"data": null,"render": function ( data, type, row ) {
                    //medida = data.Medida.ins_med;
                    tott = (scu * (stock_inicial + cantt)).toFixed(6);
                    return '<div class="font-medium text-center">'+formatNumber(tott)+'</div>';
                }},
            ],
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;

                var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
                };

                /*
                //stock_entradas = api
                   // .column( 2 /*, { search: 'applied', page: 'current'} */
                    //.data()
                   // .reduce( function (a, b) {
                    //    return intVal(a) + intVal(b);
                  //  }, 0 );

                //stock_salidas = api
                    //.column( 5 /*, { search: 'applied', page: 'current'} */)
                    //.data()
                    //.reduce( function (a, b) {
                     //   return intVal(a) + intVal(b);
                   // }, 0 );


                /*
                api.column(2, { page: 'current'}).every(
                function(){
                    stock_entradas = api.cells(null,this.index(), { page: 'current'})
                    .render('display')
                    .reduce(function (a,b){
                        var x = parseFloat(a) || 0;
                        var y = parseFloat(b) || 0;
                        return x + y;
                    }, 0);
                });

                api.column(5, { page: 'current'}).every(
                function(){
                    stock_salidas = api.cells(null,this.index(), { page: 'current'})
                    .render('display')
                    .reduce(function (a,b){
                        var x = parseFloat(a) || 0;
                        var y = parseFloat(b) || 0;
                        return x + y;
                    }, 0);
                });    
                */    

                //$('.stock-entradas').html((stock_entradas).toFixed(4)+' <sup>'+medida+'</sup>');
                //$('.stock-salidas').html((stock_salidas).toFixed(4)+' <sup>'+medida+'</sup>');
                //$('.stock-total').html((stock_entradas - stock_salidas).toFixed(4)+' <sup>'+medida+'</sup>');
            }
    });
};