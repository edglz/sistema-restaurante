$(function() {
    moment.locale('es');
    listar();
    $('#inventario').addClass("active");
    $('#i-stock').addClass("active");

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

$('#filtro_tipo_ins').change( function() {
    listar();
});

var listar = function(){
    function filterGlobal () {
        $('#table').DataTable().search( 
            $('#global_filter').val()
        ).draw();
    }

    var stock_min = 0,
        stock_real = 0,
        table =	$('#table')
	    .DataTable({
            buttons: [
                {
                    extend: 'pdf', title: 'Control de Stock de Inventario '+moment().format('D MMMM YYYY, hh:mm:ss'), text:'Pdf', className: 'btn btn-circle btn-lg btn-danger waves-effect waves-dark', text: '<i class="mdi mdi-file-pdf display-6" style="line-height: 10px;"></i>', titleAttr: 'Descargar Pdf',
                    container: '#btn-pdf',customize: function ( doc ) { doc.content[1].table.widths = ['10%','10%','10%','30%','10%','15%','15%']}
                }
            ],
            "destroy": true,
            "responsive": true,
            "dom": "tip",
            "bSort":false,
    		"ajax":{
        		"method": "POST",
        		"url": $('#url').val()+"inventario/stock_list",
                "data": {
                    tipo_ins: $('#filtro_tipo_ins').val(),
                    stock_min: $("input[name='filtro_stock_minimo']").val()
                }
    		},
            "columns":[
                {
                    "data": null,
                    "render": function ( data, type, row) {
                        if(data.id_tipo_ins == 1){
                            return '<span class="label label-warning">INSUMO</span>';
                        } else if(data.id_tipo_ins == 2){
                            return '<span class="label label-success">PRODUCTO</span>';
                        }
                    }
                },
                {"data": "Producto.ins_cod"},
                {"data": "Producto.ins_cat"},
                {"data": "Producto.ins_nom"},
                {"data": "Producto.ins_med"},
                {"data": null,"render": function ( data, type, row ) {
                    stock_min = data.Producto.ins_sto - 0;
                    return '<div class="text-warning text-right">'+stock_min.toFixed(6)+'</div>';
                }},
                {"data": null,"render": function ( data, type, row ) {
                    stock_real = data.ent-data.sal;
                    return '<div class="text-success text-right">'+stock_real.toFixed(6)+'</div>';
                }}
            ]
	});

    $('input.global_filter').on( 'keyup click', function () {
        filterGlobal();
    });
};

$('#filtro_stock_minimo').on('click', function(event){
    if( $(this).is(':checked') ) {
        $('#filtro_stock_minimo').val('0');
        listar();
    } else {
        $('#filtro_stock_minimo').val('%');
        listar();
    }
});