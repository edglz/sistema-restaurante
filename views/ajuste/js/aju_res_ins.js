$(function() {
    var id_catg = '%';
    listarCategorias();
    listarInsumos(id_catg);
    $('.scroll_categoria').slimscroll({
        height: '100%'
    });
    var scroll_categoria = function () {
            var topOffset = 340;
            var height = ((window.innerHeight > 0) ? window.innerHeight : this.screen.height) - 1;
            height = height - topOffset;
            $(".scroll_categoria").css("height", (height) + "px");
    };
    $(window).ready(scroll_categoria);
    $(window).on("resize", scroll_categoria);
    /*
    var tour = new Tour({
    steps: [
        {
          element: "#step1",
          placement: "top",
          title: "Paso #01",
          content: "Agregue una categoría, para luego poder asignarlo a un insumo. <b>Ejemplo: CARNES</b>"
        },
        {
          element: "#step2",
          placement: "top",
          title: "Paso #02",
          content: "Ahora agregue un insumo y asignele sus atributos correspondientes.<br><b>Ejemplo: PECHUGA DE POLLO</b>"
        }
    ]});

    $('.startTour').click(function(){
        tour.restart();
    })
    */
});

/* Mostrar datos en la lista categorias */
var listarCategorias = function(){
    $('#ul-cat').empty();
    $.ajax({
        type: "POST",
        url: $('#url').val()+"ajuste/insumo_cat_list",
        dataType: "json",
        success: function(item){
            if(item.data.length > 0){
                $.each(item.data, function(i, campo) {
                    $('#ul-cat')
                        .append(
                        $('<li/>')
                            .html('<a href="javascript:void(0)" class="link" onclick="listarInsumos('+campo.id_catg+')">'+campo.descripcion+''
                            +'<span><i data-feather="edit" class="feather-sm fill-white" onclick="editarCategoria('+campo.id_catg+',\''+campo.descripcion+'\')"></i>'
                            +'&nbsp;<i data-feather="trash-2" class="feather-sm fill-white" onclick="eliminarCategoria('+campo.id_catg+')"></i>&nbsp;</span></a>')
                        )
                });
            }else{
                $('#ul-cat').html("<center><br><br><br><i class='mdi mdi-alert-circle display-3' style='color: #d3d3d3;'></i><br><span class='font-14' style='color: #d3d3d3;'>No hay datos disponibles</span><br><br></center>");
            }
        }
    });
}

/* Mostrar datos en la tabla insumos */
var listarInsumos = function(id_catg){
    $('#categoria').val(id_catg);
    var table = $('#table-insumos')
    .DataTable({
        "destroy": true,
        "responsive": true,
        "dom": "tp",
        "bSort": false,
        "ajax":{
            "method": "POST",
            "url": $('#url').val()+"ajuste/insumo_list",
            "data": {
                id_ins : '%',
                id_catg : id_catg
          }
        },
        "columns":[
            {"data":"ins_cod"},
            {"data":"ins_nom"},
            {"data":"ins_cat"},
            {"data":"ins_med"},
            {"data":null,"render": function ( data, type, row) {
                if(data.ins_est == 'a'){
                  return '<div class="text-right"><span class="text-navy"><i class="ti-check"></i> Si </span></div>';
                } else if (data.ins_est == 'i'){
                  return '<div class="text-right"><span class="text-danger"><i class="ti-close"></i> No </span></div>'
                }
            }},
            {"data":null,"render": function ( data, type, row ) {
                return '<div class="text-right"><a href="javascript:void(0)" class="text-info edit" onclick="editarInsumo('+data.id_ins+')"><i data-feather="edit" class="feather-sm fill-white"></i></a></div>';
            }}
        ]
    });

    $('#table-insumos').DataTable().on("draw", function(){
        feather.replace();
    });
}

/* Editar categoria */
var editarCategoria = function(id_catg,descripcion){
    $(".f").addClass("focused");
    $("#id_catg_categoria").val(id_catg);
    $("#descripcion_categoria").val(descripcion);
    $('#boton-catg').css("display","none");
    $('#nueva-catg').css("display","block");
    $('#frm-categoria').formValidation('revalidateField', 'descripcion_categoria');
}

/* Eliminar categoria */
var eliminarCategoria = function(id_catg){
    $.ajax({
        type: "POST",
        url: $('#url').val()+"ajuste/insumo_cat_delete",
        data: {
            id_catg: id_catg
        },
        dataType: "json",
        success: function(data){
            if(data == 1){
                Swal.fire({   
                    title:'Proceso Terminado',   
                    text: 'Datos eliminados correctamente',
                    icon: "success", 
                    confirmButtonColor: "#34d16e",   
                    confirmButtonText: "Aceptar",
                    allowOutsideClick: false,
                    showCancelButton: false,
                    showConfirmButton: true
                }, function() {
                    return false
                });
                listarCategorias();
            }else if(data == 0){
                Swal.fire({   
                    title:'Proceso No Culminado',   
                    text: 'Datos protegidos',
                    icon: "error", 
                    confirmButtonColor: "#34d16e",   
                    confirmButtonText: "Aceptar",
                    allowOutsideClick: false,
                    showCancelButton: false,
                    showConfirmButton: true
                }, function() {
                    return false
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown + ' ' + textStatus);
        }
    });
}

/* Editar insumo */
var editarInsumo = function(id_ins){
    $(".f").addClass("focused");
    $('#id_catg').find('option').remove();
    ComboCatg();
    var id_catg = '%';
    $('#modal-insumo').modal('show');
    $("#id_ins").val(id_ins);
    $.ajax({
      type: "POST",
      url: $('#url').val()+"ajuste/insumo_list",
      data: {
            id_ins : id_ins,
            id_catg : id_catg
      },
      dataType: "json",
      success: function(item){
        $.each(item.data, function(i, campo) {
            $('#nomb_ins').val(campo.ins_nom);
            $('#cod_ins').val(campo.ins_cod);
            $('#id_med').selectpicker('val', campo.id_med);
            $('#id_catg').selectpicker('val', campo.id_catg);
            $('#id_catg').selectpicker();
            $('#id_catg').selectpicker('refresh');
            $('#stock_min').val(campo.ins_sto);
            $('#cos_uni').val(campo.ins_cos);
            $('#estado').selectpicker('val', campo.ins_est);
        });
      }
    });
}

/* Combo categoria */
var ComboCatg = function(){
    $('#id_catg').find('option').remove();
    $.ajax({
        type: "POST",
        url: $('#url').val()+"ajuste/insumo_combo_cat",
        dataType: "json",
        success: function(data){
            $.each(data, function (index, value) {
                $('#id_catg').append("<option value='" + value.id_catg + "'>" + value.descripcion + "</option>");
                $('#id_catg').selectpicker();
                $('#id_catg').selectpicker('refresh');
                $('#id_catg').selectpicker('val', $('#categoria').val());
                $('#id_catg').selectpicker('refresh');           
            });
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log(errorThrown + ' ' + textStatus);
        } 
    });
}

$(function() {

    $('#frm-categoria')
        .formValidation({
            framework: 'bootstrap',
            excluded: ':disabled',
            fields: {
        }
    })
    .on('success.form.fv', function(e) {

        e.preventDefault();
        var $form = $(e.target),
        fv = $form.data('formValidation');

        var form = $(this);

        var categoria = {
            id_catg: 0,
            descripcion: 0
        }

        categoria.id_catg = $('#id_catg_categoria').val();
        categoria.descripcion = $('#descripcion_categoria').val();

        $.ajax({
            dataType: 'JSON',
            type: 'POST',
            url: $('#url').val()+'ajuste/insumo_cat_crud',
            data: categoria,
            success: function (cod) {
                if(cod == 0){
                    Swal.fire({   
                        title:'Proceso No Culminado',   
                        text: 'Datos duplicados',
                        icon: "error", 
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "Aceptar",
                        allowOutsideClick: false,
                        showCancelButton: false,
                        showConfirmButton: true
                    }, function() {
                        return false
                    });
                } else if(cod == 1){
                    var cat = '%';
                    listarCategorias();
                    listarInsumos(cat);
                    $('#categoria').val(''); 
                    $('#descripcion_categoria').val('');
                    $("#id_catg_categoria").val('');
                    $('#boton-catg').css("display","block");
                    $('#nueva-catg').css("display","none");
                    Swal.fire({   
                        title:'Proceso Terminado',   
                        text: 'Datos registrados correctamente',
                        icon: "success", 
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "Aceptar",
                        allowOutsideClick: false,
                        showCancelButton: false,
                        showConfirmButton: true
                    }, function() {
                        return false
                    });
                } else if(cod == 2) {
                    var cat = '%';
                    listarCategorias();
                    listarInsumos(cat);
                    $('#descripcion_categoria').val('');
                    $("#id_catg_categoria").val('');
                    $('#boton-catg').css("display","block");
                    $('#nueva-catg').css("display","none");
                    Swal.fire({   
                        title:'Proceso Terminado',   
                        text: 'Datos actualizados correctamente',
                        icon: "success", 
                        confirmButtonColor: "#34d16e",   
                        confirmButtonText: "Aceptar",
                        allowOutsideClick: false,
                        showCancelButton: false,
                        showConfirmButton: true
                    }, function() {
                        return false
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(errorThrown + ' ' + textStatus);
            }   
        });
        return false;
    });
});

$(function() {
    $('#form-insumo')
      .formValidation({
          framework: 'bootstrap',
          excluded: ':disabled',
          fields: {
          }
      })
      .on('success.form.fv', function(e) {

          if ($('#id_catg').val().trim() === '') {
            toastr.warning('Seleccione una categoria.');
            $('.btn-guardar').removeAttr('disabled');
            $('.btn-guardar').removeClass('disabled');
            return false;

          } else {

            e.preventDefault();
            var $form = $(e.target),
            fv = $form.data('formValidation');
            
            var form = $(this);

            var insumo = {
              id_ins: 0,
              id_catg: 0,
              id_med: 0,
              cod_ins: 0,
              nomb_ins: 0,
              stock_min: 0,
              cos_uni: 0,
              estado: 0
            }

            insumo.id_ins = $('#id_ins').val();
            insumo.id_catg = $('#id_catg').val();
            insumo.id_med = $('#id_med').val();
            insumo.cod_ins = $('#cod_ins').val();
            insumo.nomb_ins = $('#nomb_ins').val();
            insumo.stock_min = $('#stock_min').val();
            insumo.cos_uni = $('#cos_uni').val();
            insumo.estado = $('#estado').val();

            $.ajax({
                dataType: 'JSON',
                type: 'POST',
                url: $('#url').val()+'ajuste/insumo_crud',
                data: insumo,
                success: function (cod) {
                    if(cod == 0){
                        Swal.fire({   
                            title:'Proceso No Culminado',   
                            text: 'Datos duplicados',
                            icon: "error", 
                            confirmButtonColor: "#34d16e",   
                            confirmButtonText: "Aceptar",
                            allowOutsideClick: false,
                            showCancelButton: false,
                            showConfirmButton: true
                        }, function() {
                            return false
                        });
                    } else if(cod == 1){
                        $('#modal-insumo').modal('hide');
                        listarInsumos(insumo.id_catg);
                        Swal.fire({   
                            title:'Proceso Terminado',   
                            text: 'Datos registrados correctamente',
                            icon: "success", 
                            confirmButtonColor: "#34d16e",   
                            confirmButtonText: "Aceptar",
                            allowOutsideClick: false,
                            showCancelButton: false,
                            showConfirmButton: true
                        }, function() {
                            return false
                        });
                    } else if(cod == 2) {
                        $('#modal-insumo').modal('hide');
                        listarInsumos(insumo.id_catg);
                        Swal.fire({   
                            title:'Proceso Terminado',   
                            text: 'Datos actualizados correctamente',
                            icon: "success", 
                            confirmButtonColor: "#34d16e",   
                            confirmButtonText: "Aceptar",
                            allowOutsideClick: false,
                            showCancelButton: false,
                            showConfirmButton: true
                        }, function() {
                            return false
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(errorThrown + ' ' + textStatus);
                }   
            });

          return false;
      }
    });
});

$('.btn-nuevo-categoria').click( function() {
    $(".f").removeClass("focused");
    $('#boton-catg').css("display","none");
    $('#nueva-catg').css("display","block");
    $('#descripcion_categoria').val('');
    $("#descripcion_categoria").focus();
    $('#id_catg_categoria').val('');
    $('#frm-categoria').formValidation('revalidateField', 'descripcion_categoria');
});

/* Boton cancelar categoria */
$('.btn-ccatg').click( function() {
    $(".f").removeClass("focused");
    $('#boton-catg').css("display","block");
    $('#nueva-catg').css("display","none");
    $('#descripcion_categoria').val('');
    $("#descripcion_categoria").focus();
    $('#id_catg_categoria').val('');
});

/* Boton nuevo insumo */
$('.btn-nuevo-insumo').click( function() {
    $(".f").removeClass("focused");
    $('#id_ins').val('');
    ComboCatg();
    $('#modal-insumo').modal('show');
    //cod_insumo();
});

$('#nomb_ins').keyup(function(){
    var value = $( this ).val().charAt(0);
    var text_select = $("#id_catg option:selected").text().charAt(0);
    var table = $('#table-insumos').DataTable();
    var info = table.page.info();
    var total_reg = 1 + info.recordsTotal;
    $("#cod_ins").val(text_select+''+mayusPrimera(value)+''+('000'+total_reg).slice(-3));
});

$('#id_catg').change(function(){
    var text_select = $("#id_catg option:selected").text().charAt(0);
    var text_nombre = $("#nomb_ins").val().charAt(0);
    var table = $('#table-insumos').DataTable();
    var info = table.page.info();
    var total_reg = 1 + info.recordsTotal;
    $("#cod_ins").val(text_select+''+mayusPrimera(text_nombre)+''+('000'+total_reg).slice(-3));
});

var cod_insumo = function(){
    var text_select = $("#id_catg option:selected").text().charAt(0);
    var text_nombre = $("#nomb_ins").val().charAt(0);
    var table = $('#table-insumos').DataTable();
    var info = table.page.info();
    var total_reg = 1 + info.recordsTotal;
    $("#cod_ins").val(text_select+''+mayusPrimera(text_nombre)+''+('000'+total_reg).slice(-3));
}

$('#modal-insumo').on('hidden.bs.modal', function() {
    $(this).find('form')[0].reset();
    $('#form-insumo').formValidation('resetForm', true);
    $('#estado').selectpicker('val', 'a');
    $("#id_med").val('').selectpicker('refresh');
});