<?php if (Session::get('loggedIn') == true):?>
    </div>
    <!-- <footer class="footer">
        DINAMO - RESTOBAR
        <?php if(Session::get('rol') == 5) { ?>
        <br><a href="<?php echo URL; ?>tablero/logout" class="text-danger"><i class="ti-power-off"></i> Cerrar sesi&oacute;n</a>
        <?php } ?>
    </footer> -->

</div>
<?php endif; ?>
</div>

    
    <script src="https://www.google.com/cloudprint/client/cpgadget.js"></script>

    <script src="<?php echo URL; ?>public/plugins/popper/popper.min.js"></script>
    <script src="<?php echo URL; ?>public/plugins/bootstrap/js/bootstrap.min.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="<?php echo URL; ?>public/js/jquery.slimscroll.js"></script>
    <!--Wave Effects -->
    <script src="<?php echo URL; ?>public/js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="<?php echo URL; ?>public/js/sidebarmenu.js"></script>
    <!--stickey kit -->
    <script src="<?php echo URL; ?>public/plugins/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <script src="<?php echo URL; ?>public/plugins/sparkline/jquery.sparkline.min.js"></script>
    <!--Custom JavaScript -->
    <script src="<?php echo URL; ?>public/plugins/toast-master/js/jquery.toast.js"></script>
    <script src="<?php echo URL; ?>public/js/jasny-bootstrap.js"></script>
    <!-- Style switcher -->
    <script src="<?php echo URL; ?>public/plugins/styleswitcher/jQuery.style.switcher.js"></script>
    <!-- Moment script -->
    <script src="<?php echo URL; ?>public/plugins/moment/moment.js"></script>
    <script src="<?php echo URL; ?>public/plugins/moment/moment-with-locales.js"></script>
    <!-- Material DatePicker - DateTimePicker -->
    <script src="<?php echo URL; ?>public/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
    <!-- This is data table -->
    <script src="<?php echo URL; ?>public/plugins/datatables.net/js/jquery.dataTables.min.js"></script>
    <!-- DataTables buttons scripts -->
    <script src="<?php echo URL; ?>public/plugins/datatables.net/export/jszip.min.js"></script>
    <script src="<?php echo URL; ?>public/plugins/datatables.net/export/pdfmake.min.js"></script>
    <script src="<?php echo URL; ?>public/plugins/datatables.net/export/vfs_fonts.js"></script>
    <script src="<?php echo URL; ?>public/plugins/datatables.net/export/buttons.html5.min.js"></script>
    <script src="<?php echo URL; ?>public/plugins/datatables.net/export/buttons.print.min.js"></script>
    <script src="<?php echo URL; ?>public/plugins/datatables.net/export/dataTables.buttons.min.js"></script>
    <script src="<?php echo URL; ?>public/plugins/datatables.net/export/buttons.bootstrap.min.js"></script>
    <!-- This is selectpicker -->
    <script src="<?php echo URL; ?>public/plugins/bootstrap-select/bootstrap-select.js" type="text/javascript"></script>
    <!-- This is formvalidation -->
    <script src="<?php echo URL; ?>public/plugins/formvalidation/formValidation.min.js"></script>
    <script src="<?php echo URL; ?>public/plugins/formvalidation/framework/bootstrap.min.js"></script>
    <!-- This is touchspin -->
    <script src="<?php echo URL; ?>public/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.js" type="text/javascript"></script>
    <!-- Buzz -->
    <script src="<?php echo URL; ?>public/plugins/buzz/buzz.min.js"></script>
    <!-- Sweet-Alert  -->
    <script src="<?php echo URL; ?>public/plugins/sweetalert/sweetalert.min.js"></script>
    <script src="<?php echo URL; ?>public/js/chat.js"></script>
    <!-- Tag inputs  -->
    <script src="<?php echo URL; ?>public/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>
    <!--Personal JavaScript -->
    <script src="<?php echo URL; ?>public/scripts/footer.js?<?php echo time(); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>
    <script src="<?php echo URL; ?>public/js/swalclass.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
    <script type="text/javascript" src="https://cdn.heyzine.com/release/jquery.pdfflipbook.3.js"></script>
    <!-- include FilePond plugins -->
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>

<!-- include FilePond jQuery adapter -->
<script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@2.13.216/build/pdf.min.js"></script>
<script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script>
<script src="<?php echo URL; ?>public/js/iframer.js"></script>
<script src="<?php echo URL; ?>public/js/mousetrap.js"></script>
<script src="<?php echo URL; ?>public/js/get.js"></script>
<script src="https://cdn.tiny.cloud/1/0r0q9q5n6mu7xqs9vfzxq4t5czq9tq12i6w5t1wbz15g83vm/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><!--<script src="<?php //echo URL; ?>public/js/module.js"></script>-->
<script src="<?php echo URL; ?>public/js/jquerynum.js"></script>
<?php

        if (isset($this->js))
        {
            foreach ($this->js as $js)
                echo '<script type="text/javascript" src="'.URL. 'views/' .$js.'?v='.time().'"></script>';
        }
    ?></body>
</html>
<script>
var today = new Date();
var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
console.log(date)
    if(!window.localStorage.getItem("update_20") && date == '2022-3-20'){
        Swal.fire("Alerta", "<b>LAMENTAMOS LOS INCOVENIENTES CON LA PRECUENTA, ESTAMOS HACIENDO LO POSIBLE PARA QUE FUNCIONE CORRECTAMENTE. DISCULPE LAS MOLESTIAS.</b>", "warning").then(()=>{
           
                    window.localStorage.setItem("update_20", true)
               
        })
   }
   
</script> 
