

<div class="row">
<div class="footer-alert col-6" style="z-index: 999;">
    <!-- Agrega el alert de Bootstrap en el footer con la clase "alert-dismissible" para poder cerrarlo -->
    <div class="alert alert-danger fade show" role="alert">
        En modo demo, no se permite realizar cambios pertinentes.
    </div>
  </div>
</div>
<?php if (Session::get('loggedIn') == true) : ?><?php endif; ?><script src="https://www.google.com/cloudprint/client/cpgadget.js"></script>
<script src="<?php echo URL; ?>public/plugins/popper/popper.min.js"></script>
<script src="<?php echo URL; ?>public/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo URL; ?>public/js/jquery.slimscroll.js"></script>
<script src="<?php echo URL; ?>public/js/waves.js"></script>
<script src="<?php echo URL; ?>public/js/sidebarmenu.js"></script>
<script src="<?php echo URL; ?>public/plugins/sticky-kit-master/dist/sticky-kit.min.js"></script>
<script src="<?php echo URL; ?>public/plugins/sparkline/jquery.sparkline.min.js"></script>
<script src="<?php echo URL; ?>public/plugins/toast-master/js/jquery.toast.js"></script>
<script src="<?php echo URL; ?>public/js/jasny-bootstrap.js"></script>
<script src="<?php echo URL; ?>public/plugins/styleswitcher/jQuery.style.switcher.js"></script>
<script src="<?php echo URL; ?>public/plugins/moment/moment.js"></script>
<script src="<?php echo URL; ?>public/plugins/moment/moment-with-locales.js"></script>
<script src="<?php echo URL; ?>public/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
<script src="<?php echo URL; ?>public/plugins/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo URL; ?>public/plugins/datatables.net/export/jszip.min.js"></script>
<script src="<?php echo URL; ?>public/plugins/datatables.net/export/pdfmake.min.js"></script>
<script src="<?php echo URL; ?>public/plugins/datatables.net/export/vfs_fonts.js"></script>
<script src="<?php echo URL; ?>public/plugins/datatables.net/export/buttons.html5.min.js"></script>
<script src="<?php echo URL; ?>public/plugins/datatables.net/export/buttons.print.min.js"></script>
<script src="<?php echo URL; ?>public/plugins/datatables.net/export/dataTables.buttons.min.js"></script>
<script src="<?php echo URL; ?>public/plugins/datatables.net/export/buttons.bootstrap.min.js"></script>
<script src="<?php echo URL; ?>public/plugins/bootstrap-select/bootstrap-select.js" type="text/javascript"></script>
<script src="<?php echo URL; ?>public/plugins/formvalidation/formValidation.min.js"></script>
<script src="<?php echo URL; ?>public/plugins/formvalidation/framework/bootstrap.min.js"></script>
<script src="<?php echo URL; ?>public/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.js" type="text/javascript"></script>
<script src="<?php echo URL; ?>public/plugins/buzz/buzz.min.js"></script>
<script src="<?php echo URL; ?>public/plugins/sweetalert/sweetalert.min.js"></script>
<script src="<?php echo URL; ?>public/js/chat.js"></script>
<script src="<?php echo URL; ?>public/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
<script src="<?php echo URL; ?>public/scripts/footer.js?<?php echo time(); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>
<script src="<?php echo URL; ?>public/js/swalclass.js"></script>
<script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
<script src="https://cdn.heyzine.com/release/jquery.pdfflipbook.3.js" type="text/javascript"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
<script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@2.13.216/build/pdf.min.js"></script>
<script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script>
<script src="<?php echo URL; ?>public/js/iframer.js"></script>
<script src="<?php echo URL; ?>public/js/mousetrap.js"></script>
<script src="<?php echo URL; ?>public/js/get.js"></script>
<script src="https://cdn.tiny.cloud/1/0r0q9q5n6mu7xqs9vfzxq4t5czq9tq12i6w5t1wbz15g83vm/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script src="<?php echo URL; ?>public/js/jquerynum.js"></script><?php if (isset($this->js)) {
                                                                    foreach ($this->js as $js) echo '<script type="text/javascript" src="' . URL . 'views/' . $js . '?v=' . time() . '"></script>';
                                                                } ?>
<script>
    var today = new Date();
    var date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
    if ((!window.sessionStorage.getItem('update')) && date == '2022-6-28') {
        window.sessionStorage.setItem('update', 'true');

        Swal.fire({
            title: 'Actualización completa',
            html: 'Se ha actualizado el sistema con nuevas funciones, en el transcurso de los días habrá más actualizaciones.',
            icon: 'success'
        }).then(() => {})
    } else {}
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
      var footerAlert = document.querySelector('.footer-alert');

      // Verificar si el elemento del footer se ha eliminado
      setInterval(function() {
        if (!footerAlert || !document.body.contains(footerAlert)) {
          // El elemento del footer se ha eliminado, volver a agregarlo
          var container = document.createElement('div');
          container.classList.add('footer-alert');

          var alertElement = document.createElement('div');
          alertElement.classList.add('alert', 'alert-danger', '', 'fade', 'show');
          alertElement.setAttribute('role', 'alert');
          alertElement.innerHTML = 'En modo demo, no se permite realizar cambios pertinentes.';
          container.appendChild(alertElement);
          document.body.appendChild(container);
        }
      }, 1000); // Comprobar cada segundo
    });
  </script>