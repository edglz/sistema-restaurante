<style>  .filepond--credits {
    display: none;
  }
  .mdi-48px { font-size: 82px; }
</style>
<input type="hidden" id="url" value="<?php echo URL; ?>"/>
<input type="hidden" id="moneda" value="<?php echo Session::get('moneda'); ?>"/>
<input type="hidden" id="cod_ti" value="3"/>
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h4 class="m-b-0 m-t-0">Cartilla</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo URL; ?>tablero" class="link">Inicio</a></li>
            <li class="breadcrumb-item active">Mi cartilla</li>
        </ol>
    </div>
</div>
<div class="row">
  <div class="col-lg-6 pt-4">
  <div class="card text-left">
    <img class="card-img-top" src="holder.js/100px180/" alt="">
    <div class="card-body">
      <h4 class="card-title">Sube la cartilla</h4>
      <p class="card-text">Selecciona la cartilla, la cual solo se aceptan archivos PDF</p>
      <input type="file" class="my-pond" name="cartilla" id="upload_carta" />
      </form>
    </div>
  </div>
  </div>
  <div class="col-lg-4 pt-5">
    <div class="card-deck">
      <div class="card">
        <img class="card-img-top" src="public/images/qr.jpg">
        <div class="card-body">
          <h4 class="card-title">Imprime el QR</h4>
          <p class="card-text">Da click aquí para imprimir el Codigo QR</p>
          <div class="col-12 text-center">
            <h1 class="font-light m-b-0 mdi-48px "><a href="<?php echo URL;?>carta/imprime_carta" target="_blank" class="link"><i class="mdi mdi-receipt text-muted"></i></a></h1>                         
            </div>
        </div>
      </div>
    </div>
  </div>
</div>