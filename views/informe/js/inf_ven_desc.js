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

    $('#start,#end,#filtro_desc_tipo').change( function() {
        listar();
    });
});

var listar = function(){

    var moneda = $("#moneda").val();
    ifecha = $("#start").val();
    ffecha = $("#end").val();
    desc_tipo = $("#filtro_desc_tipo").val();

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
            "url": $('#url').val()+"informe/venta_desc_list",
            "data": {
                ifecha: ifecha,
                ffecha: ffecha,
                desc_tipo
            }
        },
        "columns":[
            {"data":"fec_ven","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data).format('h:mm A')+'</span>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                return data.desc_td
                +'<br><span class="font-12">'+data.numero+'</span>';
            }},
            {"data":"desc_tipo","render": function ( data, type, row ) {
                if(data == 1){
                    var tipo = 'CORTESIA';
                } else if (data == 2){
                    var tipo = 'DESCUENTO';                    
                } else if (data == 3){
                    var tipo = 'CREDITO PERSONAL';                    
                } else {
                    var tipo = '-';
                }
                return '<div class="mayus">'+tipo+'</div>';
            }},
            {"data":"desc_motivo"},
            {"data":"desc_usu"},
            {"data":null,"render": function ( data, type, row) {
                if(data.desc_tipo == 1 || data.desc_tipo == 3){
                    var total_con_desc = (parseFloat(data.stotal)).toFixed(2);
                } else {
                    var total_con_desc = (parseFloat(data.total) + parseFloat(data.desc_monto)).toFixed(2);
                }                
                return '<p class="text-right bold m-b-0"> '+moneda+' '+(total_con_desc)+'</p>';
            }},
            {"data":"desc_monto",
                "className": "b-0 b-l b-r bg-descuento",
                "render": function ( data, type, row) {
                return '<p class="text-right">'+moneda+' '+formatNumber(data)+'</p>';
            }},
            {"data":"total","render": function ( data, type, row) {
                return '<p class="text-right bold m-b-0"> '+moneda+' '+formatNumber(data)+'</p>';
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
 
            total = api
                .column( 6 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            operaciones = api
                .rows()
                .data()
                .count();

            $('.descuentos-total').text(moneda+' '+formatNumber(total));
            $('.descuentos-operaciones').text(operaciones);
        }
    });
}