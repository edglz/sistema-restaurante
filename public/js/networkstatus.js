const d = document,
    w = window,
    n = navigator;
export default function networkStatus()
{

    w.addEventListener("online", e => {
        console.log("Conexion reestablecida")
    })
    w.addEventListener("offline", e =>{
        console.log("Conexion perdida")
    })

}