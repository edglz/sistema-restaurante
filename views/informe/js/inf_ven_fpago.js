$(function() {
    $('#informes').addClass("active");
    moment.locale('es');
    listar();

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

    $('#start,#end,#filtro_tipo_pago').change( function() {
        listar();
    });
});

var listar = function(){

    var moneda = $("#moneda").val();
    ifecha = $("#start").val();
    ffecha = $("#end").val();
    id_tpag = $("#filtro_tipo_pago").selectpicker('val');

    var table = $('#table')
    .DataTable({
        buttons: [
            {
                extend: 'excel', title: 'rep_ventas_aprobadas', text:'Excel', className: 'btn btn-circle btn-lg btn-success waves-effect waves-dark', text: '<i class="ti-layout-grid2"></i>', titleAttr: 'Descargar Excel',
                container: '#btn-excel'
            }
        ],
        "destroy": true,
        "responsive": true,
        "dom": "tip",
        "bSort": true,
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"informe/venta_fpago_list",
            "data": {
                ifecha: ifecha,
                ffecha: ffecha,
                id_tpag: id_tpag
            }
        },
        "columns":[
            {"data":"fec_ven","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data).format('h:mm A')+'</span>';
            }},
            {"data":"Cliente.nombre","render": function ( data, type, row ) {
                return '<div class="mayus">'+data+'</div>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                return data.desc_td
                +'<br><span class="font-12">'+data.numero+'</span>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="mayus">'+data.codigo_operacion+'</div>';
            }},
            /*
            {"data":null,"render": function ( data, type, row) {
                return '<div class="text-right bold">'+moneda+' '+formatNumber(parseFloat(data.total) + parseFloat(data.descu))+'</div>'
                +'<p class="text-right m-b-0"><i>Dscto.: -'+formatNumber(data.descu)+'</i></p>';
            }},
            */
            {"data":"total","render": function ( data, type, row) {
                return '<div class="text-right"> '+moneda+' '+formatNumber(data)+'</div>';
            }},
            {"data":"pago_efe",
                "className": "classefectivo",
                "render": function ( data, type, row) {
                
                if($('#filtro_tipo_pago').val() == 1){
                    $('.classefectivo').addClass('b-0 b-l b-r bg-efectivo');
                    $('.classtarjeta').removeClass('b-0 b-l b-r bg-tarjeta');
                } else if($('#filtro_tipo_pago').val() == 2) {
                    $('.classefectivo').removeClass('b-0 b-l b-r bg-efectivo');
                    $('.classtarjeta').addClass('b-0 b-l b-r bg-tarjeta');
                } else{
                    $('.classefectivo').removeClass('b-0 b-l b-r bg-efectivo');
                    $('.classtarjeta').removeClass('b-0 b-l b-r bg-tarjeta');
                }
                    
                return '<div class="text-right">'+moneda+' '+formatNumber(data)+'</div>';
            }},
            {"data":"pago_tar",
                "className": "classtarjeta",
                "render": function ( data, type, row) {
                return '<div class="text-right">'+moneda+' '+formatNumber(data)+'</div>';
            }}
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;

            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            efectivo_total = api
                .column( 5 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            tarjeta_total = api
                .column( 6 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            operaciones = api
                .rows()
                .data()
                .count();

            $('.efectivo-total').text(moneda+' '+formatNumber(efectivo_total));
            $('.tarjeta-total').text(moneda+' '+formatNumber(tarjeta_total));
            $('.pagos-operaciones').text(operaciones);
        }
    });
}