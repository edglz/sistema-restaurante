var moneda = $("#moneda").val();
$(function() {
    moment.locale('es');
    $('#compras').addClass("active");
    $('#c-compras').addClass("active");
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
    
    $('#start,#end,#filtro_documento,#filtro_proveedor,#filtro_tipo,#filtro_estado').change( function() {
        listar();
    });
});

/* Mostrar datos en la tabla */
var listar = function(){
    
	ifecha = $("#start").val();
    ffecha = $("#end").val();
    id_prov = $("#filtro_proveedor").selectpicker('val');
    id_tipo_compra = $("#filtro_tipo").selectpicker('val');
    id_tipo_doc = $("#filtro_documento").selectpicker('val');
    estado = $("#filtro_estado").selectpicker('val');

	var	table =	$('#table01')
	.DataTable({
		"destroy": true,
        "responsive": true,
		"dom": "tip",
		"bSort": true,
        "order": [[0,"desc"]],
		"ajax":{
			"method": "POST",
			"url": $('#url').val()+"compra/compra_list",
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
                        return '<div class="text-left"><span class="label label-primary">'+data.desc_tc+'</span></div>';
                    } else if(data.id_tipo_compra == 2){
                        return '<div class="text-left"><span class="label label-warning">'+data.desc_tc+'</span></div>';
                    }
                }
            },
            {"data":null,"render": function ( data, type, row ) {
                if(data.estado == 'a'){
                    return '<div class="text-center"><span class="label label-success">APROBADO</span></div>';
                }else if(data.estado == 'i'){
                    return '<div class="text-center"><span class="label label-danger">ANULADO</span></div>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="text-right"><a href="javascript:void(0)" class="text-info edit" onclick="detalle('+data.id_compra+');"><i data-feather="eye" class="feather-sm fill-white"></i></a>'
                    +'&nbsp;<a href="javascript:void(0)" class="text-danger delete ms-2" onclick="anular('+data.id_compra+',\''+data.desc_td+' '+data.serie_doc+'-'+data.num_doc+'\',\''+data.total+'\');"><i data-feather="trash-2" class="feather-sm fill-white"></i></a></div>';
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

    $('#table01').DataTable().on("draw", function(){
        feather.replace();
    });
};

/* Detalle de la compra */
var detalle = function(id_compra){
    $('#table02').empty();
    $('#modal').modal('show');
    $.ajax({
      type: "post",
      dataType: "json",
      data: {
          id_compra: id_compra
      },
      url: $('#url').val()+'compra/compra_det',
      success: function (data){
        $.each(data, function(i, item) {
            var importe = item.precio * item.cant;
            var cantidad = (item.cant * 1);
            $('#table02')
            .append(
              $('<tr/>')
                .append($('<td/>').html(item.Producto.ins_cod))
                .append($('<td/>').html(item.Producto.ins_cat))
                .append($('<td/>').html(item.Producto.ins_nom))
                .append($('<td/>').html(cantidad.toFixed(6)+' <span class="label label-warning">'+item.Producto.ins_med+'</span>'))
                .append($('<td/>').html(moneda+' '+formatNumber(item.precio)))
                .append($('<td class="text-right"/>').html(moneda+' '+formatNumber(importe)))
                );
            });
        }
    });
};

var anular = function(id_compra,documento,total){
    var html_confirm = '<div>Se anulará la compra con los siguientes datos:</div>\
        <br><div style="width: 100% !important; float: none !important;">\
        <table class="table">\
        <tr><td class="text-left">Documento: </td><td class="text-right">'+documento+'</td></tr>\
        <tr><td class="text-left">Importe: </td><td class="text-right">'+moneda+' '+formatNumber(total)+'</td></tr>\
        </table>\
        </div>Así mismo se descontará las cantidades del stock.<br><br>\
        <div><span class="text-success" style="font-size: 17px;">¿Está Usted de Acuerdo?</span></div>';

    Swal.fire({
        title: 'Necesitamos de tu Confirmación',
        html: html_confirm,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#34d16e',
        confirmButtonText: 'Si, Adelante!',
        cancelButtonText: "No!",
        showLoaderOnConfirm: true,
        preConfirm: function() {
          return new Promise(function(resolve) {
             $.ajax({
                url: $('#url').val()+'compra/compra_delete',
                type: 'POST',
                data: {id_compra: id_compra},
                dataType: 'json'
             })
             .done(function(response){
                if(response==1){
                    Swal.fire({
                        title: 'Proceso Terminado',
                        text: 'Datos registrados correctamente',
                        icon: 'success',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "Aceptar"
                    });
                    listar();
                }else if(response==0){
                    Swal.fire({
                        title: 'Proceso No Culminado',
                        text: 'Datos procesados anteriormente',
                        icon: 'error',
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "Aceptar"
                    });
                }
                
             })
             .fail(function(){
                Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
             });
          });
        },
        allowOutsideClick: false              
    });
}