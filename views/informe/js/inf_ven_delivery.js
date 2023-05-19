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

    $('#start, #end, #filtro_tipo_entrega, #filtro_repartidor').change( function() {
        listar();
    });

    $('.scroll_detalle').slimscroll({
        height: '100%'
    });
    var scroll_detalle = function () {
        var topOffset = 400;
        var height = ((window.innerHeight > 0) ? window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        $(".scroll_detalle").css("height", (height) + "px");
    };
    $(window).ready(scroll_detalle);
    $(window).on("resize", scroll_detalle);
});

var listar = function(){

    var moneda = $("#moneda").val();
    ifecha = $("#start").val();
    ffecha = $("#end").val();
    tipo_entrega = $('#filtro_tipo_entrega').selectpicker('val');
    id_repartidor = $('#filtro_repartidor').selectpicker('val');

    var table = $('#table')
    .DataTable({
        "destroy": true,
        "responsive": true,
        "dom": "tip",
        "bSort": true,
        "order": [[0,"desc"]],
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"informe/venta_delivery_list",
            "data": {
                ifecha: ifecha,
                ffecha: ffecha,
                tipo_entrega: tipo_entrega,
                id_repartidor: id_repartidor
            }
        },
        "columns":[
            {"data":"fec_ven","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data).format('h:mm A')+'</span>';
            }},
            {"data":"Caja.desc_caja","render": function ( data, type, row ) {
                return '<div class="mayus">'+data+'</div>';
            }},
            {"data":"Cliente.nombre","render": function ( data, type, row ) {
                return '<div class="mayus">'+data+'</div>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.id_repartidor == 2222){
                    return '<div class="mayus">RAPPI</div>';
                } else if(data.id_repartidor == 3333){
                    return '<div class="mayus">UBER</div>';
                } else if(data.id_repartidor == 4444){
                    return '<div class="mayus">GLOVO</div>';
                } else {
                    return '<div class="mayus">'+data.desc_repartidor+'</div>';                    
                }
            }},
            {
                "data": null,
                "render": function ( data, type, row) {
                    return data.desc_td+'<br><span class="font-12">Ser.'+data.ser_doc+' - Nro.'+data.nro_doc+'</span>';
                }
            },
            {
                "data": null,
                "render": function ( data, type, row ) {
                    if(data.tipo_entrega == 1){
                        return '<div class="text-center"><span class="label label-primary">A DOMICILIO</span></div>';
                    } else if(data.tipo_entrega == 2){
                        return '<div class="text-center"><span class="label label-inverse">POR RECOGER</span></div>';
                    }
                }
            },
            {"data":"total","render": function ( data, type, row) {
                return '<div class="text-right bold m-b-0"> '+moneda+' '+formatNumber(data)+'</div>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="text-right"><div class="btn-group">'
                    +'<a href="javascript::void(0)" class="text-dark" id="new" data-toggle="dropdown" aria-expanded="false"><i data-feather="more-vertical" class="feather-sm"></i></a>'
                        +'<div class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(0px, -197px, 0px); top: 0px; left: 0px; will-change: transform;">'
                            +'<a class="dropdown-item" href="javascript:void(0)" onclick="detalle('+data.id_ven+',\''+data.desc_td+'\',\''+data.ser_doc+'-'+data.nro_doc+'\')"><i data-feather="eye" class="feather-sm fill-white"></i> Detalle</a>'
                            +'<a class="dropdown-item" href="'+$("#url").val()+'informe/venta_all_imp/'+data.id_ven+'" target="_blank"><i data-feather="printer" class="feather-sm fill-white"></i> Imprimir</a>'
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
                .column( 6 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            operaciones = api
                .rows()
                .data()
                .count();

            $('.ventas-total').text(moneda+' '+formatNumber(total));
            $('.ventas-neta').text(moneda+' '+formatNumber(total*0.7));
            $('.ventas-operaciones').text(operaciones);
        }
    });

    $('#table').DataTable().on("draw", function(){
        feather.replace();
    });
};

var detalle = function(id_venta,doc,num){
    var moneda = $("#moneda").val();
    var totalconsumido = 0,
        totalcomision = 0,
        totaldescuento = 0;
    $('#lista_pedidos').empty();
    $('#detalle').modal('show');
    $('.title-d').text(doc+' - '+num);
    $.ajax({
      type: "post",
      dataType: "json",
      data: {
          id_venta: id_venta
      },
      url: $('#url').val()+'informe/venta_all_det',
      success: function (data){
        $.each(data, function(i, item) {
            var calc = item.precio * item.cantidad;
            $('#lista_pedidos')
            .append(
              $('<tr/>')
                .append($('<td width="10%"/>').html(item.cantidad))
                .append($('<td width="60%"/>').html(item.Producto.pro_nom+' <span class="label label-warning">'+item.Producto.pro_pre+'</span>'))
                .append($('<td width="15%"/>').html(moneda+' '+formatNumber(item.precio)))
                .append($('<td width="15%" class="text-right"/>').html(moneda+' '+formatNumber(calc)))
                );
            totalconsumido += calc;
            totalcomision = item.Comision.total;
            totaldescuento = item.Descuento.total;
            });
            
            $('.total-consumido').text(moneda+' '+formatNumber(totalconsumido));
            $('.total-descuento').text(moneda+' '+totaldescuento);
            $('.total-facturado').text(moneda+' '+formatNumber(totalconsumido-totaldescuento));
        }
    });
};