<?php
require_once('ajax/conecta.php');





$sql = "select * from certificados where id_certificado = :id_certificado";
$inscritos = $conexao->prepare($sql);
$inscritos->BindValue(':id_certificado', $_GET['id']);
$inscritos->execute();
$dados = $inscritos->fetchAll(PDO::FETCH_OBJ);

// echo $_GET['id'];

$text = explode('</strong>', $dados[0]->texto);
$nome = explode('<strong>', $text[0]);

$text2 = explode('-', $dados[0]->titulo);

$text3 = explode('</strong>', $dados[0]->texto);
$nota = explode('<strong>', $text[2]);

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
<script src="js/core.js"></script>

<main id="js-page-content" role="main" class="page-content">
  <ol class="breadcrumb page-breadcrumb">
    <li class="breadcrumb-item"><a href="javascript:void(0);">PROEFICIÊNCIA</a></li>
    <li class="breadcrumb-item active">Alteração de incrição</li>

    <li class="position-absolute pos-top pos-right d-none d-sm-block"><code>ID: 03</code></li>
  </ol>
  <div class="subheader">
    <h1 class="subheader-title">
      <i class='subheader-icon fal fa-barcode-read'></i> Alterar Inscrição

    </h1>

  </div>
  <div class="row">
    <div class="col-xl-12">
      <div id="panel-1" class="panel">
        <div class="panel-hdr">
          <h2>
            Inscrição
          </h2>
          <div class="panel-toolbar">
            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
            <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
          </div>
        </div>
        <div class="panel-container show">
          <div class="panel-content">
            <form class="needs-validation" novalidate="" method="post" action="modulos/proficiencia/certificados/ajax/grava.php">
              <div class="form-row">
                <input type="text" class="form-control" name="id_certificado" id="id_certificado" value="<?php echo $dados[0]->id_certificado ?>" hidden>
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Nome<span class="text-danger"></span></label>
                  <input type="text" class="form-control" name="nome_old" value="<?php echo $nome[1] ?>" hidden require>
                  <input type="text" class="form-control" name="nome_new" value="<?php echo $nome[1] ?>">
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Idioma<span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="idioma_old" value="<?php echo substr($text2[1], 1) ?>" hidden>
                  <input type="text" class="form-control" name="idioma_new" value="<?php echo substr($text2[1], 1) ?>" require>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Nota<span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="nota_old" value="<?php echo $nota[1] ?>" hidden>
                  <input type="text" class="form-control" name="nota_new" value="<?php echo $nota[1] ?>" disabled>
                </div>
              </div>
              <button class="btn btn-primary ml-auto" type="submit"><?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar" ?></button>
            </form>


          </div>

        </div>


      </div>

    </div>

  </div>

</main>

<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>