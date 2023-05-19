$(function () {
   listarCategorias()
   listar()
   $("#preparaciones").addClass('active');
   $("#i-preparados").addClass('active');
});
var listarCategorias = function(){
    $('#ul-cat').empty();
    $.ajax({
        type: "POST",
        url: $('#url').val()+"receta/get_catg_preparados",
        dataType: "json",
        success: function(item){
            console.log(item);
            if(item.data.length > 0){
                for(let x of item.data){
                    $("#ul-cat").append(
                        $("<li/>").html(
                            `
                            <a href="javascript:;" class="link" onclick="listar(${x.id_catg})">${x.nombre}
                            <span><i data-feather="edit" class="feather-sm fill-white" onclick="load_form(${x.id_catg}, '${x.nombre}', '${x.estado}' )"></i>
                            &nbsp;<i data-feather="trash-2" class="feather-sm fill-white" onclick="borrar(${x.id_catg})"></i>&nbsp;</span></a>
                            `
                        )
                    )
                    feather.replace();
                }
            }else{
                $('#ul-cat').html("<center><br><br><br><i class='mdi mdi-alert-circle display-3' style='color: #d3d3d3;'></i><br><br><span class='font-18' style='color: #d3d3d3;'>No hay datos disponibles</span><br></center>");
            }
        }
    });
}
var listar = function(id = '%'){
    function filterGlobal () {
            $('#table-recetas').DataTable().search( 
                $('.global_filter').val()
            ).draw();
    }
	var	table =	$('#table-recetas')
	.DataTable({
		"destroy": true,
        "responsive": true,
		"dom": "tip",
		"bSort": true,
		"ajax":{
			"method": "POST",
			"url": $('#url').val()+"receta/get_preparados",
			"data": {
              id_catg : id
            }
		},
		"columns":[
            {"data":null,"render": function ( data, type, row ) {
                return '<b>'+data.nombre+'</b>';
            }},
            {
                "data": null,
                "render": function ( data, type, row ) {
                    if(data.estado == 'a'){
                        return '<div class="text-center"><span class="text-navy"><i class="ti-check"></i> Si </span></div>';
                    } else if (data.estado == 'i'){
                        return '<div class="text-center"><span class="text-danger"><i class="ti-close"></i> No </span></div>'
                    }
                }
            },
            {"data":null,"render": function ( data, type, row ) {
                return `
                <button class="btn btn-outline-danger"><i class="fa fa-trash"></i> </button>
                <button class="btn btn-outline-info"><i class="fa fa-edit"></i> </button>
                `;
            }}
		],
	});
$('input.global_filter').on( 'keyup click', function () {
                filterGlobal();
            });
        
            $('#table-recetas').DataTable().on("draw", function(){
                feather.replace();
    }); 
};

function borrar(id){
    Swal.fire({
        title: 'Mensaje',
        html: 'Necesitamos confirmación para borrar esta categoria<br><h2>¿Desea borrar la categoría?</h2>',
        icon: 'question',
        allowOutsideClick: false,
        allowEscapeKey: false,
        backdrop: '#4bb0e3',
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Confirmar',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise(function (resolve) {
                $.ajax({
                    type: "POST",
                    url: $("#url").val() + 'receta/crud_preparados/delete',
                    data: {
                        id_catg : id
                    },
                    dataType: "json",
                })
                    .done(function (response) {
                        if (response.msj == 1) {
                            Swal.fire({ 
                                title: 'Notificación',
                                html: 'Tarea completada',
                                icon: 'success'
                            }).then(() => {
                                listarCategorias();
                            })
                        } else {
                            Swal.fire({
                                title: 'Notificación',
                                html: 'Error al completar la tarea, datos protegidos',
                                icon: 'error'
                            })
                        }
                    })
                    .fail(function () {
                        Swal.fire('Oops...', 'Problemas con la conexión a internet!', 'error');
                    });
            });
        }
    })
}
function agregar(){
    console.log('agregar')
}
const hide_form = () =>{
    clean_inputs();
    $("#display-categoria-nuevo").hide()
    $(".display-categoria-list").show()
}
$("#form-categoria").on("submit",(e)=>{
    e.preventDefault();
    crud_preparados();
})
$(".btn-categoria-cancelar").on("click", ()=>{
    hide_form()
})
const clean_inputs = () => {
    $("#descripcion_categoria").val('')
    $("#id_catg_categoria").val('')
    $("#estado_categoria").prop('checked',  true)
}
function load_form(id_catg = '', nombre = '', estado = ''){
    $("#display-categoria-nuevo").show()
    $(".display-categoria-list").hide()
    if(id_catg != ''){
        $("#descripcion_categoria").val(nombre)
        $("#id_catg_categoria").val(id_catg)
        estado == 'a' ? $("#estado_categoria").prop('checked',  true) : $("#estado_categoria").prop('checked',  false)
    }else{
        clean_inputs();
    }
}
function crud_preparados(){
    let data = {
        nombre : $("#descripcion_categoria").val(),
        estado : $("#estado_categoria").prop('checked') ? 'a' : 'b',
        id_catg : $("#id_catg_categoria").val(),
    }
    var method =  $("id_catg").val() == '' ? 'insert' : 'update'
    if(data.nombre != ''){
        $.ajax({
            type: "POST",
            url: $("#url").val() + 'receta/crud_preparados/'+method,
            data: data,
            dataType: "json",
            success: function (response) {
                if(response.msj == 1){
                    Swal.fire({
                        title: 'Correcto',
                        html : 'Tarea completada',
                        icon: 'success'
                    }).then(()=>{
                        listarCategorias();
                        hide_form()
                    })
                }else{
                    Swal.fire({
                        title: 'Error',
                        html: 'Hubo un error al hacer esta tarea, intenta de nuevo',
                        icon: 'error'
                    }).then(()=>{
                        hide_form();
                        listarCategorias();
                    })
                }
            }
        });
    }else{
        Swal.fire({
            title: 'Error',
            html: 'Rellena todos los campos',
            icon: 'error'
        })
    }
}