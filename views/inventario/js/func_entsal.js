$(function() {
    moment.locale('es');
    $('#inventario').addClass("active");
    $('#i-entsal').addClass("active");
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

    $('#start, #end').change( function() {
        listar();
    });
});

var listar = function(){
    var moneda = $("#moneda").val();
    function filterGlobal () {
        $('#table').DataTable().search( 
            $('#global_filter').val()
        ).draw();
    }
	var	table =	$('#table')
	.DataTable({
		"destroy": true,
        "responsive": true,
		"dom": "tip",
		"bSort": true,
        "order": [[ 0, "desc" ]],
		"ajax":{
			"method": "POST",
			"url": $('#url').val()+"inventario/ajuste_list",
            "data": {
                ifecha : $("#start").val(),
                ffecha : $("#end").val()
            }
		},
		"columns":[
            {"data":"fecha","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data).format('h:mm A')+'</span>';
            }},
            {"data":"tipo"},
            {"data":"responsable"},
            {"data":"motivo"},
            {"data":null,"render": function ( data, type, row) {
                if(data.estado =='a'){
                    return '<div class="text-center"><span class="label label-success">APROBADO</span></div>';
                } else if (data.estado == 'i'){
                    return '<div class="text-center"><span class="label label-danger">ANULADO</span></div>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="text-right"><a href="javascript:void(0)" class="text-info edit" onclick="detalle('+data.id_tipo+','+data.id_es+');"><i data-feather="eye" class="feather-sm fill-white"></i></a>'
                    +'&nbsp;<a href="javascript:void(0)" class="text-danger delete ms-2" onclick="anular('+data.id_es+','+data.id_tipo+',\''+data.tipo+'\',\''+data.motivo+'\');"><i data-feather="trash-2" class="feather-sm fill-white"></i></a></div>';
            }}
		]
	});
    $('input.global_filter').on( 'keyup click', function () {
        filterGlobal();
    });

    $('#table').DataTable().on("draw", function(){
        feather.replace();
    });
};

var detalle = function(id_tipo,id_es){
    var moneda = $("#moneda").val();
    $('#table-detalle').empty();
    $('#modal-detalle').modal('show');
    $.ajax({
      type: "post",
      dataType: "json",
      data: {
          id_tipo: id_tipo,
          id_es: id_es
      },
      url: $('#url').val()+'inventario/ajuste_det',
      success: function (data){
        $.each(data, function(i, item) {
            var cantidad = (item.cant * 1);
            $('#table-detalle')
            .append(
              $('<tr/>')
                .append($('<td/>').html(item.Producto.ins_cod))
                .append($('<td/>').html(item.Producto.ins_cat))
                .append($('<td/>').html(item.Producto.ins_nom))
                .append($('<td/>').html(cantidad.toFixed(6)))
                .append($('<td/>').html('<span class="label label-warning">'+item.Producto.ins_med+'</span>'))
                .append($('<td class="text-right"/>').html(moneda+' '+item.cos_uni))
                );
            });
        }
    });
};

var anular = function(id_es,id_tipo,accion,concepto){
    var html_confirm = '<div>Se anulará el ajuste de stock con los siguientes datos:</div>\
        <br><div style="width: 100% !important; float: none !important;">\
        <table class="table">\
        <tr><td class="text-left">Tipo: </td><td class="text-right">'+accion+'</td></tr>\
        <tr><td class="text-left">Concepto: </td><td class="text-right">'+concepto+'</td></tr>\
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
                url: $('#url').val()+'inventario/ajuste_delete',
                type: 'POST',
                data: {
                    id_es: id_es,
                    id_tipo: id_tipo
                },
                dataType: 'json'
             })
             .done(function(response){
                if(response==1){
                    Swal.fire({
                        title: 'Proceso Terminado',
                        text: 'Datos anulados correctamente',
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