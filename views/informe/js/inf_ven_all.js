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

    $('#start, #end, #tipo_ped, #tipo_doc, #estado, #cliente').change( function() {
        listar();
        //alert(moment($('#start').val()).format('Y-MM-DD HH:mm:ss')+'////'+$('#end').val());
    });

    $('.scroll_detalle').slimscroll({
        height: '100%'
    });
    var scroll_detalle = function () {
        var topOffset = 405;
        var height = ((window.innerHeight > 0) ? window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        $(".scroll_detalle").css("height", (height) + "px");
    };
    $(window).ready(scroll_detalle);
    $(window).on("resize", scroll_detalle);

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
    tped = $("#tipo_ped").selectpicker('val');
    tdoc = $("#tipo_doc").selectpicker('val');
    estado = $('#estado').selectpicker('val');
    cliente = $('#cliente').selectpicker('val');

    var table = $('#table')
    .DataTable({
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
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"informe/venta_all_list",
            "data": {
                ifecha: ifecha,
                ffecha: ffecha,
                tped: tped,
                tdoc: tdoc,
                estado: estado,
                cliente: cliente
            }
        },
        "columns":[
            {"data":"fec_ven","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data).format('h:mm A')+'</span>';
            }},
            {"data":"desc_caja","render": function ( data, type, row ) {
                return '<div class="mayus">'+data+'</div>';
            }},
            {"data":"Cliente.nombre","render": function ( data, type, row ) {
                return '<div class="mayus">'+data+'</div>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.desc_tipo == 1){
                    var tooltip = ' <i class="ti-info-alt text-warning font-10" data-original-title="Cortesia" data-toggle="tooltip" data-placement="top"></i>';
                } else if(data.desc_tipo == 3){
                    var tooltip = ' <i class="ti-info-alt text-warning font-10" data-original-title="Credito Personal: '+data.Personal.nombres+'" data-toggle="tooltip" data-placement="top"></i>';
                } else {
                    var tooltip = '';
                }
                return data.desc_td
                +'<br><span class="font-12">'+data.ser_doc+'-'+data.nro_doc+'</span>'+tooltip;
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.id_tped == 1){
                    return 'SALON'
                    +'<br><span class="font-12">'+data.Pedido.desc_salon+' - Mesa: '+data.Pedido.nro_mesa+'</span>';
                } else if(data.id_tped == 2){
                   return 'MOSTRADOR';
                } 
                else if(data.id_tped == 3) {
                    return 'DELIVERY';
                }else{
                    return 'PORTERO';
                }
            }},
            {"data":"total","render": function ( data, type, row) {
                return '<div class="text-right bold m-b-0"> '+moneda+' '+formatNumber(data)+'</div>';
            }},
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
                .column( 5 /*, { search: 'applied', page: 'current'} */)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

            operaciones = api
                .rows()
                .data()
                .count();

            $('.ventas-total').text(moneda+' '+formatNumber(total));
            $('.ventas-operaciones').text(operaciones);
        }
    });
    $('body').tooltip({selector: '[data-toggle="tooltip"]'});    
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
    $('.title-detalle').text(doc+': '+num);
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
            $('.total-comision').text(moneda+' '+totalcomision);
            $('.total-descuento').text(moneda+' '+totaldescuento);
            $('.total-facturado').text(moneda+' '+formatNumber(parseFloat(totalconsumido)+parseFloat(totalcomision)-parseFloat(totaldescuento)));
        }
    });
};