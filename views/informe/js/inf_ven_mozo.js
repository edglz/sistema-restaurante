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

    $('#start,#end,#filtro_mozo').change( function() {
        listar();
    });
});

var listar = function(){

    var moneda = $("#moneda").val();
    ifecha = $("#start").val();
    ffecha = $("#end").val();
    id_mozo = $("#filtro_mozo").selectpicker('val');

    var table = $('#table')
    .DataTable({
        "destroy": true,
        "dom": "tip",
        "bSort": true,
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"informe/venta_mozo_list",
            "data": {
                ifecha: ifecha,
                ffecha: ffecha,
                id_mozo: id_mozo
            }
        },
        "columns":[
            {"data":"fec_ven","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data).format('h:mm A')+'</span>';
            }},
            {"data":"Mozo.nombre","render": function ( data, type, full, meta ) {
                return '<div class="mayus">'+data+'</div>';
            }},
            {"data":"Cliente.nombre"},
            {"data":"desc_td"},
            {"data":"numero"},
            {"data":"total","render": function ( data, type, full, meta ) {
                return '<div class="text-right bold"> '+moneda+' '+formatNumber(data)+'</div>';
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
 
            total = api
                .column( 5 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            operaciones = api
                .rows()
                .data()
                .count();

            $('.mozo-total').text(moneda+' '+formatNumber(total));
            $('.mozo-operaciones').text(operaciones);
        }
    });
}