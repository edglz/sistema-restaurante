$(function() {
    $('#config').addClass("active");
});

$('.opt-ped').on('click', function(){
	var html_confirm = '<div>Para poder eliminar los datos, \
		no deben existir pedidos aperturados en el sistema<br></div><br>\
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
	            url: $("#url").val()+'ajuste/optimizar_pedidos',
	            type: 'POST',
	            dataType: 'json'
	         })
	         .done(function(response){
	         	if(response == 1){
	         		Swal.fire({
		                title: 'Proceso Terminado',
		                text: 'El sistema se ha optimizado correctamente',
		                icon: 'success',
		                confirmButtonColor: "#34d16e",   
		                confirmButtonText: "Aceptar"
		            });  
	         	}else{
		            Swal.fire({
		                title: 'Proceso No Culminado',
		                text: 'Asegurese de no tener un pedido abierto',
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
});

$('.opt-ven').on('click', function(){
	var html_confirm = '<div>La eliminación de estos registros es útil cuando sólo han sido de prueba y se desea comenzar a operar con datos reales.</div><br>\
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
	            url: $("#url").val()+'ajuste/optimizar_ventas',
	            type: 'POST',
	            dataType: 'json'
	         })
	         .done(function(response){
	         	if(response == 1){
	         		Swal.fire({
		                title: 'Proceso Terminado',
		                text: 'El sistema se ha restaurado correctamente',
		                icon: 'success',
		                confirmButtonColor: "#34d16e",   
		                confirmButtonText: "Aceptar"
		            });  
	         	}else{
		            Swal.fire({
		                title: 'Proceso No Culminado',
		                text: 'Asegurese de no tener pedidos abiertos',
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
});

$('.opt-prod').on('click', function(){
	var html_confirm = '<div>La eliminación de estos registros es útil cuando sólo han sido de prueba y se desea comenzar a operar con datos reales.</div><br>\
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
	            url: $("#url").val()+'ajuste/optimizar_productos',
	            type: 'POST',
	            dataType: 'json'
	         })
	         .done(function(response){
	         	if(response == 1){
	         		Swal.fire({
		                title: 'Proceso Terminado',
		                text: 'El sistema se ha restaurado correctamente',
		                icon: 'success',
		                confirmButtonColor: "#34d16e",   
		                confirmButtonText: "Aceptar"
		            });  
	         	}else{
		            Swal.fire({
		                title: 'Proceso No Culminado',
		                text: 'Asegurese de no tener pedidos abiertos',
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
});

$('.opt-ins').on('click', function(){
	var html_confirm = '<div>La eliminación de estos registros es útil cuando sólo han sido de prueba y se desea comenzar a operar con datos reales.</div><br>\
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
	            url: $("#url").val()+'ajuste/optimizar_insumos',
	            type: 'POST',
	            dataType: 'json'
	         })
	         .done(function(response){
	         	if(response == 1){
	         		Swal.fire({
		                title: 'Proceso Terminado',
		                text: 'El sistema se ha restaurado correctamente',
		                icon: 'success',
		                confirmButtonColor: "#34d16e",   
		                confirmButtonText: "Aceptar"
		            });  
	         	}else{
		            Swal.fire({
		                title: 'Proceso No Culminado',
		                text: 'Asegurese de no tener pedidos abiertos',
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
});

$('.opt-clientes').on('click', function(){
	var html_confirm = '<div>La eliminación de estos registros es útil cuando sólo han sido de prueba y se desea comenzar a operar con datos reales.</div><br>\
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
	            url: $("#url").val()+'ajuste/optimizar_clientes',
	            type: 'POST',
	            dataType: 'json'
	         })
	         .done(function(response){
	         	if(response == 1){
	         		Swal.fire({
		                title: 'Proceso Terminado',
		                text: 'El sistema se ha restaurado correctamente',
		                icon: 'success',
		                confirmButtonColor: "#34d16e",   
		                confirmButtonText: "Aceptar"
		            });  
	         	}else{
		            Swal.fire({
		                title: 'Proceso No Culminado',
		                text: 'Asegurese de no tener pedidos abiertos',
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
});

$('.opt-proveedores').on('click', function(){
	var html_confirm = '<div>La eliminación de estos registros es útil cuando sólo han sido de prueba y se desea comenzar a operar con datos reales.</div><br>\
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
	            url: $("#url").val()+'ajuste/optimizar_proveedores',
	            type: 'POST',
	            dataType: 'json'
	         })
	         .done(function(response){
	         	if(response == 1){
	         		Swal.fire({
		                title: 'Proceso Terminado',
		                text: 'El sistema se ha restaurado correctamente',
		                icon: 'success',
		                confirmButtonColor: "#34d16e",   
		                confirmButtonText: "Aceptar"
		            });  
	         	}else{
		            Swal.fire({
		                title: 'Proceso No Culminado',
		                text: 'Asegurese de no tener pedidos abiertos',
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
});

$('.opt-mesas').on('click', function(){
	var html_confirm = '<div>La eliminación de estos registros es útil cuando sólo han sido de prueba y se desea comenzar a operar con datos reales.</div><br>\
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
	            url: $("#url").val()+'ajuste/optimizar_mesas',
	            type: 'POST',
	            dataType: 'json'
	         })
	         .done(function(response){
	         	if(response == 1){
	         		Swal.fire({
		                title: 'Proceso Terminado',
		                text: 'El sistema se ha restaurado correctamente',
		                icon: 'success',
		                confirmButtonColor: "#34d16e",   
		                confirmButtonText: "Aceptar"
		            });  
	         	}else{
		            Swal.fire({
		                title: 'Proceso No Culminado',
		                text: 'Asegurese de no tener pedidos abiertos',
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
});