<?php
$user = $_SESSION['coopex']['usuario']['id_pessoa'];
require_once('ajax/conecta.php');
if (isset($_GET['id'])) {
  $sql = "SELECT * FROM coopex_fhsl.users where id = :id";
  $stm = $coopex->prepare($sql);
  $stm->bindValue(':id', $_GET['id']);
  $stm->execute();
  $dados = $stm->fetchAll(PDO::FETCH_OBJ);
}
$status = isset($dados[0]->_status) ? $dados[0]->_status : 0;

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
<script src="js/core.js"></script>

<main id="js-page-content" role="main" class="page-content">
  <ol class="breadcrumb page-breadcrumb">
    <li class="breadcrumb-item"><a href="javascript:void(0);">FHSL</a></li>
    <li class="breadcrumb-item active">Usuário</li>

    <li class="position-absolute pos-top pos-right d-none d-sm-block"><code>ID: 02</code></li>
  </ol>
  <div class="subheader">
    <h1 class="subheader-title">
      <i class='subheader-icon fal fa-barcode-read'></i> FHSL
      <small>
        Cadastro usuários
      </small>
    </h1>

  </div>
  <div class="row">
    <div class="col-xl-12">
      <div id="panel-1" class="panel">
        <div class="panel-hdr">
          <h2>
            Cadastro
          </h2>
          <div class="panel-toolbar">
            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
            <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
          </div>
        </div>
        <div class="panel-container show">
          <div class="panel-content">
            <form class="needs-validation" novalidate="" method="post" action="modulos/fhsl/usuario/ajax/cadastro_dados.php">
              <div class="form-row">
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Usuário: <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="usuario" placeholder="" value="<?php echo isset($dados[0]->user) ? $dados[0]->user : "" ?>" required>
                  <input type="text" class="form-control" name="id" id="id" placeholder="" value="<?php echo isset($dados[0]->id) ? $dados[0]->id : 0 ?>" hidden>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Senha: <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="passwd" placeholder="" value="<?php echo isset($dados[0]->passwd) ? base64_decode($dados[0]->passwd) : "" ?>" required>
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