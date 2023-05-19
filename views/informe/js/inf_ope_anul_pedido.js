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

    $('#start,#end,#filtro_personal').change( function() {
        listar();
    });

});

var listar = function(){

    var moneda = $("#moneda").val();
	ifecha = $("#start").val();
    ffecha = $("#end").val();
    id_usu = $("#filtro_personal").selectpicker('val');

	var	table =	$('#table')
	.DataTable({
		"destroy": true,
        "responsive": true,
		"dom": "tip",
		"bSort": true,
		"ajax":{
			"method": "POST",
			"url": $('#url').val()+"informe/oper_anul_list",
			"data": {
                ifecha: ifecha,
                ffecha: ffecha,
                id_usu: id_usu
            }
		},
		"columns":[
            {"data":"fecha_pedido","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data).format('h:mm A')+'</span>';
            }},
            {"data":"Personal.nombres"},
            {
                "data": null,
                "render": function ( data, type, row) {
                    if(data.TipoPedido.id_tipo_pedido == 1){
                        return '<span class="label label-primary">MESA</span>';
                    } else if(data.TipoPedido.id_tipo_pedido == 2){
                        return '<span class="label label-info">MOSTRADOR</span>';
                    } else if(data.TipoPedido.id_tipo_pedido == 3){
                        return '<span class="label label-warning">DELIVERY</span>';
                    }
                }
            },
            {
                "data": null,
                "render": function ( data, type, row) {
                    return data.Producto.pro_nom+' '+data.Producto.pro_pre;
                }
            },
            {"data":"cant"},
            {"data":"precio","render": function ( data, type, row) {
                return moneda+' '+formatNumber(data);
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
                .column( 5 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            operaciones = api
                .rows()
                .data()
                .count();

            $('.anulaciones-total').text(moneda+' '+formatNumber(total));
            $('.anulaciones-operaciones').text(operaciones);
        }
	});
};

