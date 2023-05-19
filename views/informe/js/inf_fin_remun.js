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

    $('#start, #end, #filtro_personal, #filtro_estado').change( function() {
        listar();
    });

});

var listar = function(){

    var moneda = $("#moneda").val();
	ifecha = $("#start").val();
    ffecha = $("#end").val();
    id_per = $("#filtro_personal").val();
    estado = $("#filtro_estado").val();

	var	table =	$('#table')
	.DataTable({
		"destroy": true,
        "responsive": true,
		"dom": "tip",
		"bSort": true,
		"ajax":{
			"method": "POST",
			"url": $('#url').val()+"informe/finanza_rem_list",
			"data": {
                ifecha: ifecha,
                ffecha: ffecha,
                id_per: id_per,
                estado: estado
            }
		},
		"columns":[
            {"data":"fecha_re","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data).format('h:mm A')+'</span>';
            }},
            {"data": "Caja.desc_caja"},
            {"data": "desc_usu"},
            {"data": "desc_per"},
            {"data": "motivo"},
            {
                "data": "importe",
                "render": function ( data, type, row) {
                    return '<div class="text-right bold-d"> '+moneda+' '+formatNumber(data)+'</div>';
                }
            },
            {
                "data": null,
                "render": function ( data, type, row ) {
                    if(data.estado == 'a'){
                        return '<div class="text-center"><span class="label label-success">APROBADO</span></div>';
                    } else if(data.estado == 'i'){
                        return '<div class="text-center"><span class="label label-danger">ANULADO</span></div>';
                    }
                }
            }
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

            $('.remuneraciones-total').text(moneda+' '+formatNumber(total));
            $('.remuneraciones-operaciones').text(operaciones);
        }
	});
}