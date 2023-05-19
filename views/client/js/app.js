$(function () {
  Swal.fire({
     title: "ENLACE GENERADO PARA VER LA CARTILLA DE PRODUCTOS", 
     html: `<a class="btn btn-primary" id="btn_op" href="${$("#url").val() + $("#carta").val()}">VER CARTILLA</a>`,
     showConfirmButton: false,
     allowOutsideClick: false,
     allowOutsideClick: false,
     allowEscapeKey: false,
     hideOnOverlayClick: false,
     hideOnContentClick: false,
     backdrop: "#000000"
  })
  $('#btn_op').pdfFlipbook({ key: '761d28a1a0ce5414' });
});
