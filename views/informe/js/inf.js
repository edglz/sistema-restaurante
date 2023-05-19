$(function() {
	$('#navbar-c').addClass("white-bg");
    $('#informes').addClass("active");
});
// Must be scoped in an async function

$(".ventas").click(function() {
	$('#list_a').empty();
	$('#list_a').append(
		$('<div class="panel panel-default panel-shadow animated flipInX"/>')
			.append(
			$('<div class="panel-body no-padding"/>')
			.append(
				$('<div class="list-group"/>')
				.append('<a class="list-group-item link font-14" href="informe/venta_all">Todas las ventas</a>')
				.append('<a class="list-group-item link font-14" href="informe/venta_del">Ventas por delivery</a>')
				.append('<a class="list-group-item link font-14" href="informe/venta_culqi">Ventas por Culqi</a>')
				.append('<a class="list-group-item link font-14" href="informe/venta_prod">Ventas por producto</a>')
				.append('<a class="list-group-item link font-14" href="informe/venta_mozo">Ventas por mesero</a>')
				.append('<a class="list-group-item link font-14" href="informe/venta_fpago">Formas de pago</a>')
				.append('<a class="list-group-item link font-14" href="informe/venta_desc">Descuentos</a>')
				)
			)
		)
});
$(".general").on("click", ()=>{
	$('#list_a').empty();
	$('#list_a').append(
		$('<div class="panel panel-default panel-shadow animated flipInX"/>')
			.append(
			$('<div class="panel-body no-padding"/>')
			.append(
				$('<div class="list-group"/>')
				.append('<a class="list-group-item link font-14" onclick="inf_general();">Informe general en PDF</a>')
				)
			)
		)
})
// $(".portero").on('click', () =>{
// 	$('#list_a').empty();
// 	$('#list_a').append(
// 		$('<div class="panel panel-default panel-shadow animated flipInX"/>')
// 			.append(
// 			$('<div class="panel-body no-padding"/>')
// 			.append(
// 				$('<div class="list-group"/>')
// 				.append('<a class="list-group-item link font-14" href="informe/portero_all">Todas las ventas</a>')
// 				.append('<a class="list-group-item link font-14" href="informe/ingreso_portero">Ingresos</a>')
// 				.append('<a class="list-group-item link font-14" href="informe/egreso_portero">Egresos</a>')
// 				.append('<a class="list-group-item link font-14" href="informe/venta_prod_portero">Ventas por producto (Portero)</a>')
// 				.append('<a class="list-group-item link font-14" href="informe/aperturas_portero">Aperturas y Cierres</a>')
// 				)
// 			)
// 		)
// })
$(".compras").click(function() {
	$('#list_a').empty();
	$('#list_a').append(
		$('<div class="panel panel-default panel-shadow animated flipInX"/>')
			.append(
			$('<div class="panel-body no-padding"/>')
			.append(
				$('<div class="list-group"/>')
				.append('<a class="list-group-item link font-14" href="informe/compra_all">Todas las compras</a>')
				)
			)
		)
});

$(".finanzas").click(function() {
	$('#list_a').empty();
	$('#list_a').append(
		$('<div class="panel panel-default panel-shadow animated flipInX"/>')
			.append(
			$('<div class="panel-body no-padding"/>')
			.append(
				$('<div class="list-group"/>')
				.append('<a class="list-group-item link font-14" href="informe/finanza_arq">Aperturas y cierres</a>')
				.append('<a class="list-group-item link font-14" href="informe/finanza_ing">Todos los ingresos</a>')
				.append('<a class="list-group-item link font-14" href="informe/finanza_egr">Todos los egresos</a>')
				.append('<a class="list-group-item link font-14" href="informe/finanza_rem">Egresos por remuneraci&oacute;n</a>')
				.append('<a class="list-group-item link font-14" href="informe/finanza_adel">Personal</a>')
				)
			)
		)
});

$(".otros").click(function() {
	$('#list_a').empty();
	$('#list_a').append(
		$('<div class="panel panel-default panel-shadow animated flipInX"/>')
			.append(
			$('<div class="panel-body no-padding"/>')
			.append(
				$('<div class="list-group"/>')
				.append('<a class="list-group-item link font-14" href="informe/oper_anul">Anulaciones de pedidos</a>')
				)
			)
		)
});


var datex = $("#fecha_gral").val();

const inf_general = ()=>{
	Swal.fire({
		title: "Favor de ingresar el codigo",
		html: `Para poder imprimir, necesitamos el código de impresión el cual es: 4+dia+mes+año+97 Ejemplo: <b>4${datex}97</b>`,
		input: 'text',
		showCancelButton: true ,
		cancelButtonText: 'Cancelar',
		confirmButtonText: 'Imprimir',
		confirmButtonColor: 'green'
		}).then((result) => {
		if (result.value) {
			var queryText  = result.value
				$.ajax({
					type: "POST",
					url: $("#url").val() + "informe/informe_general",
					data: {
						rep_cod : queryText
					},
					dataType: "json",
					success: function (response) {
						if(response.data.registros > 0){
							Swal.fire({
								title: 'Se ha encontrado un reporte!', 
								html: `<a class="btn btn-danger btn-block" target="_blank" href="${$("#url").val()}informe/informe_pdf/${queryText}"><i class="fas fa-file-pdf"></i> Descargar</a>`,
								showConfirmButton: false,
								showCancelButton: true,
								cancelButtonText: 'Salir',
								cancelButtonColor: 'green',
								icon: 'success'
							})
						}else{
							Swal.fire({
								title: 'Notificación',
								html: 'No se ha encontrado ningún reporte relacionado con el código proporcionado.<br>Favor de ingresar uno correcto',
								icon: 'error'
							})
						}
					}
				});
		}});
}
