var listIng = []
var isIng = ""
var index = 1

var listaPresentaciones = (id) => {
  $.ajax({
    type: "POST",
    url: $("#url").val() + "receta/listaPresentaciones/" + id,
    dataType: "json",
    success: function (response) {
      $("#n_pres").empty();
      $("#n_pres").append("<option>Seleccione una presentación</option>");
      $("#n_pres").selectpicker();
      for (x of response.data) {
        $("#n_pres").append(
          `<option value="${x.id_pres}">${x.presentacion}</option>`
        );
      }
    },
  });
};
$(function () {
  $("#n_producto").change(function () {
    $v = $("#n_producto").val();
    if ($v != "") {
      $("#n_pres").attr("disabled", false);
      listaPresentaciones($v);
    } else {
      $("#n_pres").attr("disabled", true);
      $("#n_pres").empty();
    }
  });
});
$(function () {
  $("#add_prep").on("click", () =>{
    $("#modal_prep").modal("show")
    verificaElementos();
    
  })
});
$(function () {
  tinymce.init({
    selector: "textarea",
    height: 700,
    force_br_newlines: !0,
    force_p_newlines: !1,
    forced_root_block: "",
    mode: "specific_textareas",
    editor_selector: "prepend_editor",
    setup: function(e) {
        e.on("init", function() {
            var a = e.getContent();
            e.setContent(a);
        });
    },
    content_style: "body { font-family:Helvetica,Arial,sans-serif; font-size:14px } img{max-width: 100%; max-height:100%;}",
    plugins: "code print preview fullpage searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor wordcount  imagetools  contextmenu colorpicker textpattern help",
    toolbar: "a11ycheck addcomment showcomments casechange checklist code export formatpainter pageembed permanentpen table insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
    toolbar_mode: "floating",
    tinycomments_mode: "embedded",
    language: "es",
    branding: !1,
}).then(()=>{
    lista_datos_receta();
})
});

var verificaElementos = () => {
  if(listIng.length >= 1){
    ImprimeIngredientes()
  }else{
    $('#ingr_rec').html("<center><br><br><br><i class='mdi mdi-alert-circle display-3' style='color: #d3d3d3;'></i><br><br><span class='font-18' style='color: #d3d3d3;'>No hay datos disponibles</span><br></center>");
  }
}
$(function () {
  $("#new_ingrediente").on('click', () => {
    $("#modal_prep").modal("hide")
    $("#modal_ing").modal("show")
  })
  $('#modal_ing').on('hidden.bs.modal', function () {
    $("#modal_prep").modal("show")

    verificaElementos()
  })
});
$(function () {
  $(".numeric").numeric({ decimal : ".",  negative : false, scale: 3 });
});

$('#non_prep').change(function () {
  var val = this.checked ? this.value : 'off';
  isIng = val
  if(val == "on"){
    $("#prep_c").css('display', 'block')
    $("#no_prep").css('display', 'none')
  }else{
    $("#prep_c").css('display', 'none')
    $("#no_prep").css('display', 'block')
  }
});

$(()=>{
  //EVENTO DE AGREGAR INREDIENTES
  $("#agregar_ing").on("click", function () {
    var $nombre_Ing = ""
    if(isIng == "on"){
      $nombre_Ing = $("#ing_name").val()
    }else{
      $nombre_Ing = $("#ing_name_text").val()
    }
    var $unidad_medida = $("#unit_medida").val()
    var $cantidad = $("#cantidad").val()
      let dart = {
        'index' : index,
        'nombre_ing' : $nombre_Ing.toUpperCase(),
        'unidad' : $unidad_medida.toUpperCase(),
        'cantidad' : $cantidad,
        'complete' : $cantidad + '  ' + $unidad_medida + '   ' + $nombre_Ing 
      }
      //VERIFICACION DE DATOS SI ESTÁN VACIOS
      if(dart.nombre_ing != "" && dart.cantidad != "" && dart.unidad != ""){
        listIng.push(dart);
        index = index + 1 
        console.log(index) 
        console.table(listIng)
        Swal.fire({
          title: 'Ingrediente agregado',
          html : 'Se ha agregado ' + dart.complete + ' a la lista de ingredientes', 
          icon : 'success'
        }).then(()=>{
          $("#modal_ing").modal("hide")
          $("#modal_prep").modal("show")
          verificaElementos()
      
        })
      }else{
        Swal.fire({
          title: 'Notificación',
          html: 'Favor de ingresar todos los datos, todos los campos son necesarios para su registro,',
          icon: 'error'
        })
      }

  });
})

const ImprimeIngredientes = () => {
  $("#ingr_rec").empty();
  var $temp = ''
  for(let i = 0; i < listIng.length ; i++){
    $temp = $temp + `
      <tr>
        <td>${listIng[i].complete}</td>
        <td><button class="btn btn-danger" onclick="borra_ingrediente(${listIng[i].index})"><i class="fa fa-trash"></i></button></td>
      </tr>
    ` 
  }
  $("#ingr_rec").append(`
    <table>
    <tr>
    <th>Ingrediente</th>
    <th>Opciones</th>
    </tr>
    ${$temp}
    </table>
    `
    
    )
  
}
const borra_ingrediente = (id) => {
listIng = listIng.filter((item) => item.index !== id);
  Swal.fire({
    title: 'Ingrediente eliminado',
    html: 'Se ha eliminado el ingrediente',
    icon: 'success'
  }).then(()=>
  {
    verificaElementos();
  })
}

$("#btn-guardar-receta").on('click', ()=>{
    let data = {
      'id_rec' : $("#id_receta").val(),
      'nombre_rec' : $("#rec_nomb").val(),
      'catg_id' : $("#n_catg").val(),
      'pres_id' : $("#n_pres").val(),
      'content_prep' :  tinymce.get('prep_body').getContent(),
    	'ingredientes' : listIng      
    }
    if(data.nombre_rec != '' && data.catg_id != '' && data.pres_id != '' && data.content_prep != ''){
      if(listIng.length > 0){
         $.ajax({
           type: "POST",
           url: $("#url").val() + "receta/crud_rec",
           data: data,
           success: function (data) {
            if(data){
              Swal.fire({   
                  title:'Proceso Terminado',   
                  text: 'Datos añadidos correctamente',
                  icon: "success", 
                  confirmButtonColor: "#34d16e",   
                  confirmButtonText: "Aceptar",
                  allowOutsideClick: false,
                  showCancelButton: false,
                  showConfirmButton: true
              }).then(()=>{
                window.location.replace($("#url").val() + 'receta/')
              })
          }else if(data.msj == -1){
              Swal.fire({   
                  title:'Proceso No Culminado',   
                  text: 'Error al registrar',
                  icon: "error", 
                  confirmButtonColor: "#34d16e",   
                  confirmButtonText: "Aceptar",
                  allowOutsideClick: false,
                  showCancelButton: false,
                  showConfirmButton: true
              }).then(()=>{
                window.location.replace($("#url").val() + 'receta/')
              })
          }
           }
         });
      }else{
        Swal.fire({
          title: 'Error',
          html: 'Se necesita al menos 1 ingrediente para la receta',
          icon: 'error'
        })
      }
    }else{
      Swal.fire({
        title: 'Error',
        html: 'Favor de rellenar todos los campos',
        icon: 'error'
      })
    }
})


const lista_datos_receta = () => {
    var id = $("#id_receta").val()
     $.ajax({
        type: "POST",
        url: $("#url").val() + "receta/ver_receta/"+id,
        dataType: "json",
        success: function (response) {
            console.log(response)
            tinymce.get('prep_body').setContent(response.data.receta)
            $("#rec_nomb").val(response.data.nombre)
            for(let x of response.data.ingredientes){
                const indexa = {
                    index: index  
                };
                var final = Object.assign(indexa, x)
                listIng.push(final)
                index += 1
            }
            console.log(listIng)
        }
    });
}
