$(function() {
    // document.oncontextmenu=function(){return!1},document.onselectstart=function(){return"text"!=event.srcElement.type&&"textarea"!=event.srcElement.type&&"password"!=event.srcElement.type?!1:!0},window.sidebar&&(document.onmousedown=function(e){var t=e.target;return"SELECT"==t.tagName.toUpperCase()||"INPUT"==t.tagName.toUpperCase()||"TEXTAREA"==t.tagName.toUpperCase()||"PASSWORD"==t.tagName.toUpperCase()?!0:!1}),document.ondragstart=function(){return!1};
    // $(document).keydown(function(e){return 123!=e.keyCode&&((!e.ctrlKey||!e.shiftKey||73!=e.keyCode)&&void 0)});
    feather.replace();
    changeThemeColor();
    contadorSunatSinEnviar();
    // setInterval(contadorSunatSinEnviar, 10000000);
    contadorPedidosPreparados();
    // setInterval(contadorPedidosPreparados, 1000000);
    moment.locale('es');
    $('.scroll_pedpre').slimscroll({
        height: 300
    });
    $(".s").addClass("focused");
});
var registraImpresion = (nombre_imp, tipo_imp, id_pedido, url, json) =>{
    $.ajax({
        type: "POST",
        url: $("#url").val() + 'venta/registra_impresion',
        data: {
            nombre_imp : nombre_imp,
            tipo_imp: tipo_imp,
            id_ped : id_pedido,
            url: url,
            json: json
        },
        success: function (response) {
            if(response.msj == 1){
                //IMPRESION REGISTRADA
            }else{
                //ALGO ESTÁ PASANDO...
            }
        },
        
        error: function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown + ' ' + textStatus);
        }    
    });
}
var label = function(){
    $(".s").addClass("focused");
}

$(".listar-pedidos-preparados").on("click", function(){
    listarPedidosPreparados();
});

var contadorSunatSinEnviar = function(){
    $.ajax({     
        type: "post",
        dataType: "json",
        url: $("#url").val()+'venta/contadorSunatSinEnviar',
        success: function (data){
            var variable = (data.total > 0) ? data.total : '<i class="ti ti-check"></i>';
            $('.cont-sunat').html(variable);
        }
    })
}

var contadorPedidosPreparados = function(){
    $('.t-notify').removeClass('notify');
    $.ajax({     
        type: "post",
        dataType: "json",
        url: $("#url").val()+'venta/contadorPedidosPreparados',
        success: function (data){
            $.each(data, function(i, item) {
                var cantidadPedido = parseInt(item.cantidad);
                if(parseInt(cantidadPedido) > 0){
                    $('.t-notify').addClass('notify');
                    var sound = new buzz.sound("assets/sound/ding_ding", {
                        formats: [ "ogg", "mp3", "aac" ]
                    });
                    sound.play();
                }
            });
        }
    })
}

var listarPedidosPreparados = function(){
    $('.lista-pedidos-preparados').empty();
    $.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: $("#url").val()+'venta/listarPedidosPreparados',
        success: function (item) {
            if (item.data.length != 0) {
                $.each(item.data, function(i, campo) {
                    $('.lista-pedidos-preparados')
                    .append('<a href="javascript:void(0)" onclick="pedidoEntregado('+campo.id_pedido+','+campo.id_pres+',\''+campo.fecha_pedido+'\')">'
                        +'<div class="btn btn-success btn-circle"><i class="ti-check"></i></div> '
                        +'<div class="mail-contnet"><h5>'+campo.cantidad+' '+campo.nombre_prod+' <span class="label label-warning">'+campo.pres_prod+'</span></h5>'
                        +'<span class="mail-desc">'+campo.desc_salon+' - Mesa: '+campo.nro_mesa+'</span> <span class="time">'+moment(campo.fecha_envio).fromNow()+'</span>'
                        +'</div></a>');
                });
            } else {
                $('.lista-pedidos-preparados').html('<div class="col-sm-12 p-t-20 text-center"><h6>No tiene pedidos preparados</h6></div>');
            }
        }
    });
}

var pedidoEntregado = function(id_pedido,id_pres,fecha_pedido){
    $.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: 'venta/pedidoEntregado',
        data: {
            id_pedido: id_pedido,
            id_pres: id_pres,
            fecha_pedido: fecha_pedido
        },
        success: function (data) {
            contadorPedidosPreparados();
            listarPedidosPreparados();
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown + ' ' + textStatus);
        }   
    });
}

function formatNumber(num) {
    if (!num || num == 'NaN') return '0.00';
    if (num == 'Infinity') return '&#x221e;';
    num = num.toString().replace(/\$|\,/g, '');
    if (isNaN(num))
        num = "0";
    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num * 100 + 0.50000000001);
    cents = num % 100;
    num = Math.floor(num / 100).toString();
    if (cents < 10)
        cents = "0" + cents;
    for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3) ; i++)
        num = num.substring(0, num.length - (4 * i + 3)) + ',' + num.substring(num.length - (4 * i + 3));
    return (((sign) ? '' : '-') + num + '.' + cents);
}

//BLOQUEO DE CARACTERES
$(".letMay input").keypress(function(event) {
    var valueKey=String.fromCharCode(event.which);
    var keycode=event.which;
    if(valueKey.search('[A-ZÁÉÍÓÚÑ ]')!=0 && keycode!=8 && keycode!=20){
        return false;
    }
});

$(".letNumMay input").keypress(function(event) {
    var valueKey=String.fromCharCode(event.which);
    var keycode=event.which;
    if(valueKey.search('[0-9,A-ZÁÉÍÓÚÑ ]')!=0 && keycode!=8 && keycode!=20){
        return false;
    }
});

$(".letMin input").keypress(function(event) {
    var valueKey=String.fromCharCode(event.which);
    var keycode=event.which;
    if(valueKey.search('[a-záéíóúñ ]')!=0 && keycode!=8 && keycode!=20){
        return false;
    }
});

$(".letNumMin input").keypress(function(event) {
    var valueKey=String.fromCharCode(event.which);
    var keycode=event.which;
    if(valueKey.search('[0-9,a-záéíóúñ ]')!=0 && keycode!=8 && keycode!=20){
        return false;
    }
});

$(".letMayMin input").keypress(function(event) {
    var valueKey=String.fromCharCode(event.which);
    var keycode=event.which;
    if(valueKey.search('[aA-zZáÁéÉíÍóÓúÚñÑ ]')!=0 && keycode!=8 && keycode!=20){
        return false;
    }
});

$(".letNumMayMin input").keypress(function(event) {
    var valueKey=String.fromCharCode(event.which);
    var keycode=event.which;
    if(valueKey.search('[0-9,aA-zZáÁéÉíÍóÓúÚñÑ/ ]')!=0 && keycode!=8 && keycode!=20){
        return false;
    }
});

$(".letNumMayMin textarea").keypress(function(event) {
    var valueKey=String.fromCharCode(event.which);
    var keycode=event.which;
    if(valueKey.search('[0-9,aA-zZáÁéÉíÍóÓúÚñÑ/ ]')!=0 && keycode!=8 && keycode!=20){
        return false;
    }
});

$(".dec input").keypress(function(event) {
    var valueKey=String.fromCharCode(event.which);
    var keycode=event.which;
    if(valueKey.search('[0-9.]')!=0 && keycode!=8){
        return false;
    }
});

$(".ent input").keypress(function(event) {
    var valueKey=String.fromCharCode(event.which);
    var keycode=event.which;
    if(valueKey.search('[0-9]')!=0 && keycode!=8){
        return false;
    }
});

$("input,textarea").on('paste', function(e){
    e.preventDefault();
})

$("input,textarea").on('copy', function(e){
    e.preventDefault();
})

$(".input-mayus").keyup(function(e) {
    $(this).val($(this).val().toUpperCase());
});

function mayus(e) {
    e.value = e.value.toUpperCase();
}

function mayusPrimera(string){
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function changeThemeColor() {
    var metaThemeColor = document.querySelector("meta[name=theme-color]");
    metaThemeColor.setAttribute("content", "#444");
    setTimeout(function() {
        changeThemeColor();
    }, 3000);
}

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};

