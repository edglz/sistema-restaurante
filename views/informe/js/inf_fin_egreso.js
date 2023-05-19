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

    $('#start, #end, #filtro_cajero, #filtro_tipo_gasto, #filtro_estado').change( function() {
        listar();
    });

    /* BOTON DATATABLES */
    var org_buildButton = $.fn.DataTable.Buttons.prototype._buildButton;
    $.fn.DataTable.Buttons.prototype._buildButton = function(config, collectionButton) {
    var button = org_buildButton.apply(this, arguments);
    $(document).one('init.dt', function(e, settings, json) {
        if (config.container && $(config.container).length) {
           $(button.inserter[0]).detach().appendTo(config.container)
        }
    })    
    return button;
    }
});

var listar = function(){

    var moneda = $("#moneda").val();
	ifecha = $("#start").val();
    ffecha = $("#end").val();    
    id_usu = $('#filtro_cajero').selectpicker('val');
    tipo_gasto = $('#filtro_tipo_gasto').selectpicker('val');
    estado = $('#filtro_estado').selectpicker('val');

	var	table =	$('#table')
	.DataTable({
        buttons: [
            {
                extend: 'excel', title: 'rep_egeresos', text:'Excel', className: 'btn btn-circle btn-lg btn-success waves-effect waves-dark', text: '<i class="mdi mdi-file-excel display-6" style="line-height: 10px;"></i>', titleAttr: 'Descargar Excel',
                container: '#btn-excel'
            }
        ],
		"destroy": true,
        "responsive": true,
		"dom": "tip",
		"bSort": true,
		"ajax":{
			"method": "POST",
			"url": $('#url').val()+"informe/finanza_egr_list",
			"data": {
                ifecha: ifecha,
                ffecha: ffecha,
                id_usu: id_usu,
                tipo_gasto: tipo_gasto,
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
            {"data": "des_tg"},
            {"data": "responsable"},
            {"data": "motivo"},
            {
                "data": "importe",
                "render": function ( data, type, row) {
                    return '<p class="text-right bold-d"> '+moneda+' '+formatNumber(data)+'</p>';
                }
            },
            {
                "data": null,
                "render": function ( data, type, row ) {
                    if(data.estado == 'a'){
                        return '<p class="text-center"><span class="label label-success">APROBADO</span></p>';
                    } else if(data.estado == 'i'){
                        return '<p class="text-center"><span class="label label-danger">ANULADO</span></p>';
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
                .column( 6 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            operaciones = api
                .rows()
                .data()
                .count();

            $('.egresos-total').text(moneda+' '+formatNumber(total));
            $('.egresos-operaciones').text(operaciones);
        }
	});
}