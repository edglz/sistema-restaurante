$(function () {
    listar();
});
var listar = function(){
    var table = $('#table')
    .DataTable({
        "destroy": true,
        "bSort": true,
        "dom": "ftp",
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"caja/lista_impresiones",
        },
        "order": [0, "desc"],
        "columns":[
            {"data":"fecha","render": function ( data, type, row ) {
                return '<i class="ti-calendar"></i> '+moment(data).format('DD-MM-Y');
            }},
            {"data":"fecha","render": function ( data, type, row ) {
                return '<i class="ti-time"></i> '+moment(data).format('h:mm A');
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<i class="ti-printer"> </i> ' + data.nombre_impresora 
            }},
              {"data":null,"render": function ( data, type, row ) {
                    return '<b> ' + data.tipo_impresion + '</b>'
                }},
            {"data":null,"render": function ( data, type, row ) {
                        return '<i class="ti-user"> </i> ' + data.encargado 
            }},
            {"data":null,"render": function ( data, type, row ) {
                        return '<i class="ti-package"> </i> ' + data.id_pedido 
            }},
            {"data":null,"render": function ( data, type, row ) {
              if(data.json != null){
                try {
                    var data = JSON.parse(data.json)
                    var items = data.items
                    var temp = ``
                    for(let x = 0; x < items.length; x++){
                        temp += `<b>${items[x].cantidad}   ` + items[x].producto  + `  ` + items[x].presentacion + `</b><br>`
                    }
               } catch (error) {
                   
               }
               return temp;
              }else{
                  return null;
              }
            }},
            {"data":null,"render": function ( data, type, row ) {
                if(data.status == 'a'){
                    return '<div class="text-center"><span class="label label-success">SIN ERRORES</span></div>';
                }else{
                    return '<div class="text-center"><span class="label label-danger">ERRORES AL IMPRIMIR</span></div>';
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
               if(data.json != null){
                return `<div class="text-right"><a href="javascript:fWindow('${data.url}','${encodeURIComponent(data.json)}',  '${encodeURIComponent(data.fecha)}')" class="btn btn-info text-succcess"><i class="ti-printer"></i></a></div>`
               }else{
                return '<div class="text-right"><a href="'+data.url+'" target="_blank" class="btn btn-info text-succcess"><i class="ti-printer"></i></a></div>'
               }
            }}
        ],
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;         
            operaciones = api
                .rows()
                .data()
                .count();

            $('.operaciones').text(operaciones);
        }/*,
        "fnCreatedRow": function(nRow, aData, iDataIndex){
            $(nRow).addClass("tr-left");
        }*/
    });

    $('input.global_filter').on( 'keyup click', function () {
        filterGlobal();
    });

    $('#table').DataTable().on("draw", function(){
        feather.replace();
    });

};
var configuracion_ventana = "menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes";
                
var fWindow = ($url, $json, fecha) => {
    window.open($url+$json+'&fecha_imp='+encodeURIComponent(fecha), '_blank', configuracion_ventana)
}