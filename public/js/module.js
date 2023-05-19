const d = document,
    w = window,
    n = navigator;
d.addEventListener("DOMContentLoaded", ()=>{
    networkStatus()
})
function networkStatus()
{
    w.addEventListener("online", e => {
        isOnline();
    })
    w.addEventListener("offline", e =>{
        isOnline();
    })

}
const isOnline = () => {
    if(n.onLine){
        Swal.fire({
            title: "Conexión reestablecida",
            html: "Se ha reestablecido la conexión a internet"
        })
    }else{
        Swal.fire({
            title: "Conexión perdida",
            html: "Se ha perdido la conexión a internet, favor de verificar tu conexion",
            icon: "error",
            showConfirmButton: false,
            allowOutsideClick: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            hideOnOverlayClick: false,
            hideOnContentClick: false,
        })
    }
}