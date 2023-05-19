var moneda = $("#moneda").val();
$(function() {
	$('#informes').addClass("active");
	$('#start').bootstrapMaterialDatePicker({
        format: 'DD-MM-YYYY LT',
        //time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });

    $('#end').bootstrapMaterialDatePicker({
        useCurrent: false,
        format: 'DD-MM-YYYY LT',
        //time: false,
        lang: 'es-do',
        cancelText: 'Cancelar',
        okText: 'Aceptar'
    });

    $('#start,#end,#filtro_personal').change( function() {
        listar_egresos();
        listar_creditos();
    });
});

var listar_egresos = function(){

	ifecha = $("#start").val();
    ffecha = $("#end").val();
    id_personal = $("#filtro_personal").selectpicker('val');

	var	table =	$('#table-1')
	.DataTable({
		"destroy": true,
        "responsive": true,
		"dom": "tip",
		"bSort": true,
		"ajax":{
			"method": "POST",
			"url": $('#url').val()+"informe/finanza_adel_list_a",
			"data": {
                ifecha: ifecha,
                ffecha: ffecha,
                id_personal: id_personal
            }
		},
		"columns":[
            {"data":"fecha_re","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data).format('h:mm A')+'</span>';
            }},
            {"data":"desc_usu"},
            {"data":"motivo"},
            {"data":"importe","render": function ( data, type, row) {
                return '<div class="text-right bold">'+moneda+' '+formatNumber(data)+'</div>';
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
                .column( 3 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            operaciones = api
                .rows()
                .data()
                .count();

            $('.ventas-total-1').text(formatNumber(total));
            $('.ventas-operaciones-1').text(operaciones);
            var monto_global = parseFloat(total) + parseFloat($('.ventas-total-2').text().replace(/,/g, ""));
            $('.monto-global').text(formatNumber(monto_global));
        }
	});
};

var listar_creditos = function(){

	ifecha = $("#start").val();
    ffecha = $("#end").val();
    id_personal = $("#filtro_personal").selectpicker('val');

	var	table =	$('#table-2')
	.DataTable({
		"destroy": true,
        "responsive": true,
		"dom": "tip",
		"bSort": true,
		"ajax":{
			"method": "POST",
			"url": $('#url').val()+"informe/finanza_adel_list_b",
			"data": {
                ifecha: ifecha,
                ffecha: ffecha,
                id_personal: id_personal
            }
		},
		"columns":[
            {"data":"fec_ven","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data).format('h:mm A')+'</span>';
            }},
            {"data":"desc_usu"},
            {
                "data": null,
                "render": function ( data, type, row) {
                    return data.desc_td+'<br><span class="font-12">Ser.'+data.ser_doc+' - Nro.'+data.nro_doc+'</span>';
                }
            },
            {"data":"total_venta","render": function ( data, type, row) {
                return '<div class="text-right bold">'+moneda+' '+formatNumber(data)+'</div>';
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
                .column( 3 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            operaciones = api
                .rows()
                .data()
                .count();

            $('.ventas-total-2').text(formatNumber(total));
            $('.ventas-operaciones-2').text(operaciones);
            var monto_global = parseFloat(total) + parseFloat($('.ventas-total-1').text().replace(/,/g, ""));
            $('.monto-global').text(formatNumber(monto_global));
        }
	});
};