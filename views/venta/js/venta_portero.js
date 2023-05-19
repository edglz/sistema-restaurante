/* ESTADO DE LOS PEDIDOS
a = aperturado/abierto/activo/
b = preparacion
c = en camino
d = despachado/entregado/cerrado
z = anulado
*/
const URLDOM = $("#url").val();
const tipo_pedido = 4;
$(function(){
    "use strict";
    
    //CREAMOS EL EVENTO QUE ESCUCHA AL BOTON DE NUEVO PEDIDO
    $(".btn-nuevo-pedido").on("click", ()=>{
        $(".new").hide();
        $("#container_nuevo").show(2000);
        $(".form_pedido").css("background-color", "#ffff");
        $('.numero-personas').TouchSpin({
            buttondown_class: "btn btn-link text-success",
            buttonup_class: "btn btn-link text-success",
            min: 1,
            mousewheel: false, 
            max: 20
        });
    })  
   
    $(document).ready(function () {
        // validarApertura();
        $_listData();
    });
})
// var validarApertura = function(){
//     if($('#cod_ape').val() == 0 && $('#rol_usr').val() != 1){
//         var html_confirm = '<div>Para poder realizar esta operación es necesario Aperturar Caja</div>\
//             <br>\
//             <div>Contacte al encatgado para que pueda hacer el pedido.<br>\
//             <br>';
//         Swal.fire({
//             title: 'Advertencia',
//             html: html_confirm,
//             icon: 'warning',
//             allowOutsideClick: false,
//             allowEscapeKey : false,
//             showCancelButton: false,
//             showConfirmButton: false,
//             closeOnConfirm: false,
//             closeOnCancel: false
//         });
//     }
// }

var $_listData = () =>{ 
    var table = $("#table").DataTable({
        "destroy": true,
        "responsive": true,
        "dom": "tip",
        "bSort": true,
        "order": [[0,"desc"]],
        "ajax": {
            "method": "POST",
            "url": $('#url').val() + "venta/pedidos_portero",
            
        },
        "columns":[
            {"data":null,"render": function ( data, type, row ) {
                 return '<i class="ti-calendar"></i> '+moment(data.fecha_pedido).format('DD-MM-Y')
                +'<br><span class="font-12"><i class="ti-time"></i> '+moment(data.fecha_pedido).format('h:mm a')+'</span>';
            }},
            {"data":null,"render": function ( data, type, row ) {
                  return '<i class="ti-user"></i>&nbsp;&nbsp;&nbsp;'+data.personas
            }},
            {"data":null,"render": function ( data, type, row ) {
                    return `<div class="text-center"><a href="${URLDOM}venta/orden/${data.id_pedido}" class="btn btn-success btn-block">Ir al pedido</a></div>`
            }}    
        ]
    });
    
    table.on('draw', function (data) {
        if(table.page.info().recordsDisplay > 0){
            $(".u4").remove();
        }
    });

}