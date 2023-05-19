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

    $('#start,#end,#filtro_proveedor,#filtro_documento,#filtro_tipo,#filtro_estado').change( function() {
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
    id_prov = $("#filtro_proveedor").selectpicker('val');
    id_tipo_compra = $("#filtro_tipo").selectpicker('val');
    id_tipo_doc = $("#filtro_documento").selectpicker('val');
    estado = $("#filtro_estado").selectpicker('val');

	var	table =	$('#table')
	.DataTable({
        buttons: [
            {
                extend: 'excel', title: 'rep_compras', text:'Excel', className: 'btn btn-circle btn-lg btn-success waves-effect waves-dark', text: '<i class="mdi mdi-file-excel display-6" style="line-height: 10px;"></i>', titleAttr: 'Descargar Excel',
                container: '#btn-excel'
            }
        ],
		"destroy": true,
        "responsive": true,
		"dom": "tip",
		"bSort": true,
		"ajax":{
			"method": "POST",
			"url": $('#url').val()+"informe/compra_all_list",
			"data": {
                ifecha: ifecha,
                ffecha: ffecha,
                id_prov: id_prov,
                id_tipo_compra: id_tipo_compra,
                id_tipo_doc: id_tipo_doc,
                estado: estado
            }
		},
		"columns":[
            {"data":"fecha_r","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data).format('h:mm A')+'</span>';
            }},
			{"data": null,"render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data.fecha_c).format('DD-MM-Y');
            }},
            {
                "data": null,
                "render": function ( data, type, row) {
                    return data.desc_td+'<br><span class="font-12">Ser.'+data.serie_doc+' - Nro.'+data.num_doc+'</span>';
                }
            },
            {"data":"desc_prov"},
            {"data":"total","render": function ( data, type, row) {
                return '<div class="text-right bold">'+moneda+' '+formatNumber(data)+'</div>';
            }},
            {
                "data": null,
                "render": function ( data, type, row) {
                    if(data.id_tipo_compra == 1){
                        return '<span class="label label-primary">'+data.desc_tc+'</span>';
                    } else if(data.id_tipo_compra == 2){
                        return '<span class="label label-warning">'+data.desc_tc+'</span>';
                    }
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
            },
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="text-right"><div class="btn-group">'
                    +'<a href="javascript::void(0)" class="text-dark" id="new" data-toggle="dropdown" aria-expanded="false"><i data-feather="more-vertical" class="feather-sm"></i></a>'
                        +'<div class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(0px, -197px, 0px); top: 0px; left: 0px; will-change: transform;">'
                            +'<a class="dropdown-item" href="javascript:void(0)" onclick="detalle_cuota('+data.id_compra+');"><i data-feather="file-text" class="feather-sm fill-white"></i> Cuotas</a>'
                            +'<a class="dropdown-item" href="javascript:void(0)" onclick="detalle('+data.id_compra+')"><i data-feather="eye" class="feather-sm fill-white"></i> Detalle</a>'
                        +'</div>'
                    +'</div></div>';
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
                .column( 4 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            operaciones = api
                .rows()
                .data()
                .count();

            $('.compras-total').text(moneda+' '+formatNumber(total));
            $('.compras-operaciones').text(operaciones);
        }
	});

    $('#table').DataTable().on("draw", function(){
        feather.replace();
    });
};

/* Detalle de la compra */
var detalle = function(id_compra){
    var moneda = $("#moneda").val();
    $('#list-cetalle').empty();
    $('#modal-detalle').modal('show');
    $.ajax({
      type: "post",
      dataType: "json",
      data: {
          id_compra: id_compra
      },
      url: $('#url').val()+'informe/compra_all_det',
      success: function (data){
        $.each(data, function(i, item) {
            var calc = item.precio * item.cant;
            $('#list-cetalle')
            .append(
              $('<tr/>')
                .append($('<td/>').html(item.Producto.ins_cod))
                .append($('<td/>').html(item.Producto.ins_cat))
                .append($('<td/>').html(item.Producto.ins_nom))
                .append($('<td/>').html(item.cant+' <span class="label label-warning">'+item.Producto.ins_med+'</span>'))
                .append($('<td/>').html(moneda+' '+formatNumber(item.precio)))
                .append($('<td class="text-right"/>').html(moneda+' '+formatNumber(calc)))
                );
            });
        }
    });
};

/* Detalle cuotas */
var detalle_cuota = function(id_compra){
    var moneda = $("#moneda").val();
    $('#list-cuota').empty();
    $('#modal-detalle-cuota').modal('show');
    $.ajax({
        type: "POST",
        dataType: "json",
        url: $('#url').val()+"informe/compra_all_det_cuota",
        data: {
            id_compra: id_compra
        },
        success: function(data){
            $.each(data, function(i, item) {
                if (item.estado == 'p'){
                    label = 'warning';
                    nombre = 'EN PAGO';
                } else if(item.estado == 'a'){
                    label = 'primary';
                    nombre = 'CANCELADO';
                }
                $('#list-cuota')
                .append(
                  $('<tr/>')
                    .append($('<td/>').html(moment(item.fecha).format('DD-MM-Y')))
                    .append($('<td/>').html(moneda+' '+formatNumber(item.interes)))
                    .append($('<td class="text-right"/>').html(moneda+' '+formatNumber(item.total)))
                    .append($('<td class="text-center"/>')
                        .html('<span class="label label-'+label+'">'+nombre+'</span>'))
                    .append($('<td class="text-right"/>')
                        .html('<button class="btn btn-xs btn-info" onclick="detalle_sub_cuota('+item.id_credito+')"><i class="fas fa-eye"></i></button>'))
                    );
            });
        }
    });
}

/* Detalle sub cuotas */
var detalle_sub_cuota = function(id_credito){
    var moneda = $("#moneda").val();
    $('#list-sub-cuota').empty();
    $('#modal-detalle-sub-cuota').modal('show');
        $.ajax({
        type: "POST",
        dataType: "json",
        url: $('#url').val()+"informe/compra_all_det_subcuota",
        data: {
            id_credito: id_credito
        },
        success: function(data){
            $.each(data, function(i, item) {
                var egresos = (item.egreso == 1) ? '<i class="ti ti-check text-success"/>' : '<i class="ti ti-close text-danger"/>';
                $('#list-sub-cuota')
                .append(
                  $('<tr/>')
                    .append($('<td/>').html(item.Usuario.nombre))
                    .append($('<td/>').html(moment(item.fecha).format('DD-MM-Y h:mm A')))
                    .append($('<td class="text-center"/>').html(egresos))
                    .append($('<td class="text-right"/>').html(moneda+' '+formatNumber(item.importe)))
                    );
            });
        }
    });
}
