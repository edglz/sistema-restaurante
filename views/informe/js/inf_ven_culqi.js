$(function() {
    $('#informes').addClass("active");
    moment.locale('es');
    listar();

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

    $('#start, #end, #filtro_tipo_entrega').change( function() {
        listar();
    });
});

var listar = function(){

    var moneda = $("#moneda").val();
    ifecha = $("#start").val();
    ffecha = $("#end").val();
    tipo_entrega = $('#filtro_tipo_entrega').selectpicker('val');

    var table = $('#table')
    .DataTable({
        "destroy": true,
        "responsive": true,
        "dom": "tip",
        "bSort": true,
        "order": [[0,"desc"]],
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"informe/venta_culqi_list",
            "data": {
                ifecha: ifecha,
                ffecha: ffecha,
                tipo_entrega: tipo_entrega
            }
        },
        "columns":[
            {"data":"fecha_pedido","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data).format('h:mm A')+'</span>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                return data.nombre_cliente+'<br><span class="font-12">'+data.email_cliente+'</span>';
            }},
            {
                "data": null,
                "render": function ( data, type, row) {
                    return data.desc_td+'<br><span class="font-12">Ser.'+data.ser_doc+' - Nro.'+data.nro_doc+'</span>';
                }
            },
            {"data":"total","render": function ( data, type, row) {
                return '<div class="text-right bold m-b-0"> '+moneda+' '+formatNumber(data)+'</div>';
            }},
            {"data":"total","render": function ( data, type, row) {
                var comision = data * 0.042;
                return '<div class="text-right bold m-b-0"> '+moneda+' '+formatNumber(comision)+'</div>';
            }},
            {"data":null,"render": function ( data, type, row) {
                var comision = data.total * 0.042;
                var igv = comision * data.igv;
                return '<div class="text-right bold m-b-0"> '+moneda+' '+formatNumber(igv)+'</div>';
            }},
            {"data":null,"render": function ( data, type, row) {
                var comision = data.total * 0.042;
                var igv = comision * data.igv;
                var total = data.total - comision - igv;
                return '<div class="text-right bold m-b-0"> '+moneda+' '+formatNumber(total)+'</div>';
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
                .column( 3 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            operaciones = api
                .rows()
                .data()
                .count();

            var comision = total * 0.042;
            var igv = comision * 0.18;
            var monto_total = total - comision - igv;
            $('.ventas-operaciones').text(operaciones);
            $('.ventas-total').text(moneda+' '+formatNumber(total));
            $('.ventas-comision').text(moneda+' '+formatNumber(comision));
            $('.ventas-igv').text(moneda+' '+formatNumber(igv));
            $('.ventas-recibir').text(moneda+' '+formatNumber(monto_total));
        }
    });
};