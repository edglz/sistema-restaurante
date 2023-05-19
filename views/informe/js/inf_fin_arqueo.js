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

    $('#start, #end, #filtro_cajero').change( function() {
        listar();
    });
});

var listar = function(){

    var moneda = $("#moneda").val();
    ifecha = $("#start").val();
    ffecha = $("#end").val();
    id_usu = $('#filtro_cajero').selectpicker('val');

    function filterGlobal () {
        $('#table').DataTable().search( 
            $('#global_filter').val()
        ).draw();
    }

    var table = $('#table')
    .DataTable({
        "destroy": true,
        "responsive": true,
        "dom": "tip",
        "bSort": true,
        "order": [[1,"desc"]],
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"informe/finanza_arq_list",
            "data": {
                ifecha: ifecha,
                ffecha: ffecha,
                id_usu: id_usu
            }
        },
        "columns":[
            {
                "data": "id_apc",
                "render": function ( data, type, row ) {
                    return 'COD0'+data;
                }
            },
            {"data":"fecha_aper","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data).format('h:mm A')+'</span>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.estado == 'a'){
                    return '-';
                } else if(data.estado == 'c'){
                    return '<i class="ti-calendar"></i> '+moment(data.fecha_cierre).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data.fecha_cierre).format('h:mm A')+'</span>';
                }
            }},
            {"data": "desc_per"},
            {"data": "desc_caja"},
            {"data": "desc_turno"},
            {
                "data": null,
                "render": function ( data, type, row ) {
                    if(data.estado == 'a'){
                        return '<div class="text-center"><span class="label label-success">APERTURADO</span></div>';
                    } else if(data.estado == 'c'){
                        return '<div class="text-center"><span class="label label-danger">CERRADO</span></div>';
                    }
                }
            },
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="text-right"><div class="btn-group">'
                    +'<a href="javascript::void(0)" class="text-dark" id="new" data-toggle="dropdown" aria-expanded="false"><i data-feather="more-vertical" class="feather-sm"></i></a>'
                        +'<div class="dropdown-menu" x-placement="top-start" style="position: absolute; transform: translate3d(0px, -197px, 0px); top: 0px; left: 0px; will-change: transform;">'
                            +'<a class="dropdown-item" href="'+$('#url').val()+'informe/finanza_arq_resumen/'+data.id_apc+'"><i data-feather="eye" class="feather-sm fill-white"></i> Detalle</a>'
                            +'<a class="dropdown-item" href="'+$("#url").val()+'informe/finanza_arq_imp/'+data.id_apc+'" target="_blank"><i data-feather="printer" class="feather-sm fill-white"></i> Imprimir</a>'
                            +'<a class="dropdown-item" href="'+$("#url").val()+'informe/excel_imp_rep/'+data.id_apc+'" target="_blank"><i data-feather="printer" class="feather-sm fill-white"></i> Excel</a>'
                        +'</div>'
                    +'</div></div>';
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