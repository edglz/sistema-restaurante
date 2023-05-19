document.addEventListener('DOMContentLoaded', 
() => {
    $("#frm-cliente").on("submit", function(e) {
        e.preventDefault();
        const data = $(this).serializeArray();
        $.ajax({
            type: "POST",
            url: "/login/registra_cliente",
            data: data,
            dataType: "json",
            success: function (response) {
                const {message, handler} = response;
                if(handler == "success"){
                    Swal.fire({
                        title: 'Correcto',
                        html: message,
                        icon: 'success'
                    }).then(()=>{
                        location.href = '/login/cliente';
                    })
                }else{
                    Swal.fire({
                        title: 'Error',
                        html: message,
                        icon: 'error'
                    })
                }
            }
        }).fail(()=>{
            Swal.fire({
                title: 'Error',
                html: "Rellena todos los campos",
                icon: 'error'
            }) 
        })
    })
})