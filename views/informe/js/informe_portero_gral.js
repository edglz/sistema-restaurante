$(()=>{
    "use strict";
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

    
    $(document).ready(function () {
        $_listData();
    });

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

    $('#start, #end,#estado').change( function() {
        $_listData();
        //alert(moment($('#start').val()).format('Y-MM-DD HH:mm:ss')+'////'+$('#end').val());
    });
})
var $_listData = () =>{ 
    var moneda = $("#moneda").val();
    var ifecha = $("#start").val();
    var ffecha = $("#end").val();
    var estado = $('#estado').selectpicker('val');
    

    var table = $("#table_portero").DataTable({
        buttons: [
            {
                extend: 'excel', title: 'rep_ventas_aprobadas', text:'Excel', className: 'btn btn-circle btn-lg btn-success waves-effect waves-dark', text: '<i class="mdi mdi-file-excel display-6" style="line-height: 10px;"></i>', titleAttr: 'Descargar Excel',
                container: '#btn-excel'
            }
        ],
        "destroy": true,
        "responsive": true,
        "dom": "tip",
        "bSort": true,
        "order": [[0,"desc"]],
        "ajax": {
            "method": "POST",
            "url": $('#url').val() + "informe/portero_list_ventas",
            "data": {
                ifecha : ifecha,
                ffecha: ffecha,
                estado: estado
            },
            
        },
        "columns":[
            {"data":null,"render": function ( data, type, row ) {
                 return '<i class="ti-calendar"></i> '+moment(data.fecha).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data.fecha).format('h:mm a')+'</span>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                  return '<i class="ti-user"></i>&nbsp;&nbsp;&nbsp;'+data.personas
            }},
            {"data":null,"render": function ( data, type, row ) {
                  return '<i class="ti-money"></i>&nbsp;&nbsp;&nbsp;'+ moneda + formatNumber(data.monto_ingresado)
            }},
            {"data":null,"render": function ( data, type, row ) {
                var f = Math.abs(data.monto_devuelto)
                  return '<i class="ti-back-right"></i>&nbsp;&nbsp;&nbsp;'+  moneda +  formatNumber(f)
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.nro_tar != "-"){
                    return '<i class="ti-credit-card"></i>&nbsp;&nbsp;&nbsp;'+ data.nro_tar;
                }else{
                    if(data.nro_tar == "-"){
                        return '<i class="ti-money"></i> &nbsp;&nbsp;&nbsp; <b>EFECTIVO</b>';
                    }
                }
            }},
            {"data":"total","render": function ( data, type, row ) {
                return moneda + ' ' + formatNumber(data)
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.estado != "Pagado"){
                    return '<div class="text-center"><span class="label label-warning">'+data.estado+'</span></div>'
                }else{
                    return '<div class="text-center"><span class="label label-success">'+data.estado+'</span></div>'
                }
            }},
            {"data":null, "render": (data, type, row)=>{
                var url = $("#url").val()
                var html = `<div class="dropdown text-right">
                <button class="btn btn-white" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <a class="dropdown-item" onclick="detalle(${data.id_venta})"><i class="fas fa-eye"></i> Ver</a>
                  <a class="dropdown-item" target="_blank" href="${url}informe/imprimir_venta_portero/${data.id_venta}"><i class="fas fa-print"></i> Imprimir</a>
                </div>
              </div>`
                return html
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
                    return parseFloat(a) + parseFloat(b);
                }, 0 );

            operaciones = api
                .rows()
                .data()
                .count();

            $('.ventas-total').text(moneda + ' ' + formatNumber(total));
            console.log(total)
            $('.ventas-operaciones').text(operaciones);
        }
        
    });
}
var detalle = (id)=>{
    $("#lista_pedidos").empty()
    var total = 0
    var moneda  =$("#moneda").val();
    $.ajax({
        type: "POST",
        url: $("#url").val() + "informe/get_portero_pedido",
        data: {
            id_venta: id,
        },
        success: function (data) {
            data = JSON.parse(data);
            var result = [];
            console.log(data);
            for(var i in data)
                result.push(data [i]);
         
            
            for(var x = 0; x < result[0].length; x++){
                total = (total +  parseFloat(result[0][x].total))
                $('#lista_pedidos')
                .append(
                  $('<tr/>')
                    .append($('<td width="10%"/>').html(result[0][x].cantidad))
                    .append($('<td width="60%"/>').html(result[0][x].nombre+' <span class="label label-warning">'+result[0][x].presentacion+'</span>'))
                    .append($('<td width="15%"/>').html(moneda+' '+formatNumber(result[0][x].precio)))
                    .append($('<td width="15%" class="text-right"/>').html(moneda+' '+formatNumber(result[0][x].total)))
                    ); 
            }
          
            $('.total-consumido').text(moneda+' '+formatNumber(total));
            $('.total-comision').text(moneda+' 0.00');
            $('.total-descuento').text(moneda+' 0.00');
            $('.total-facturado').text(moneda+' '+formatNumber(parseFloat(total)));

        }
    });
    $("#detalle").modal("show");
} 
