<?php
$user = $_SESSION['coopex']['usuario']['id_pessoa'];
require_once('ajax/conecta.php');
if (isset($_GET['id'])) {
  $sql = " SELECT
	a.*, b.*
FROM
	historico_vaga AS a
INNER JOIN vagas AS b ON a.id_vaga = b.id_vaga
WHERE
	a.id_vaga = :id
ORDER BY
	a.id DESC
LIMIT 1";


  $stm = $coopex->prepare($sql);
  $stm->bindValue(':id', $_GET['id']);
  $stm->execute();
  $dados = $stm->fetchAll(PDO::FETCH_OBJ);

  

}
$status = isset($dados[0]->_status) ? $dados[0]->_status : 0 ;

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
<script src="js/core.js"></script>

<main id="js-page-content" role="main" class="page-content">
  <ol class="breadcrumb page-breadcrumb">
    <li class="breadcrumb-item"><a href="javascript:void(0);">RH</a></li>
    <li class="breadcrumb-item active">Vagas</li>

    <li class="position-absolute pos-top pos-right d-none d-sm-block"><code>ID: 02</code></li>
  </ol>
  <div class="subheader">
    <h1 class="subheader-title">
      <i class='subheader-icon fal fa-barcode-read'></i> Vagas
      <small>
        Cadastro vagas
      </small>
    </h1>

  </div>
  <div class="row">
    <div class="col-xl-12">
      <div id="panel-1" class="panel">
        <div class="panel-hdr">
          <h2>
            Vagas
          </h2>
          <div class="panel-toolbar">
            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
            <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
          </div>
        </div>
        <div class="panel-container show">
          <div class="panel-content">
            <form class="needs-validation" novalidate="" method="post" action="modulos/rh/vagas/ajax/cadastro_dados2.php">
              <div class="form-row">
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Titulo da vaga <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="titulo" placeholder="" value="<?php echo isset($dados[0]->titulo) ? $dados[0]->titulo : "" ?>" required>
                  <input type="text" class="form-control" name="id" id="id" placeholder="" value="<?php echo isset($dados[0]->id) ? $dados[0]->id : 0 ?>" hidden>
                  <input type="text" class="form-control" name="id_vaga" id="id_vaga" placeholder="" value="<?php echo isset($dados[0]->id_vaga) ? $dados[0]->id_vaga : 0 ?>" hidden>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Descrição da vaga <span class="text-danger">*</span></label>
                  <textarea class="form-control" name="descricao" rows="10"><?php echo isset($dados[0]->descricao) ? $dados[0]->descricao : null ?></textarea>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Status <span class="text-danger">*</span></label>
                  <select class="form-control" name="status">
                    <option value="1" <?php echo $status == 1 ? 'selected' : '' ?>>Ativo</option>
                    <option value="2" <?php echo $status == 2 ? 'selected' : '' ?>>Congelada</option>
                    <option value="0" <?php echo $status == 0 ? 'selected' : '' ?>>Inativo</option>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Local <span class="text-danger">*</span></label>
                  <select class="form-control" name="cidade" required>
                    <?php
                    $sql1 = "SELECT a.* FROM local as a inner join pessoa_local as b on a.id_local = b.id_local where id_pessoa = " . $user;
                    $stm1 = $coopex->prepare($sql1);
                    $stm1->execute();
                    $dados1 = $stm1->fetchAll(PDO::FETCH_OBJ);

                    foreach ($dados1 as $row) { ?>
                      <option value="<?php echo $row->id_local ?>" <?php $row->id_local == isset($dados[0]->local) ? 'selected' : '' ?>><?php echo $row->Instituicao ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Expectativa de Fechamento <span class="text-danger">*</span></label>
                  <input type="datetime-local" class="form-control" name="expectativa" placeholder="" value="<?php echo isset($dados[0]->expectativa) ? $dados[0]->expectativa : "" ?>" required>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Área <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="area" placeholder="" value="<?php echo isset($dados[0]->area) ? $dados[0]->area : "" ?>" required>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Perfil <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="perfil" placeholder="" value="<?php echo isset($dados[0]->perfil) ? $dados[0]->perfil : "" ?>" required>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Recrutador <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="" placeholder="" value="<?php echo isset($dados[0]->recrutador) ? $dados[0]->recrutador : $_SESSION['coopex']['usuario']['primeiro_nome'] ?>"  disabled>
                  <input type="text" class="form-control" name="recrutador" placeholder="" value="<?php echo isset($dados[0]->recrutador) ? $dados[0]->recrutador : $_SESSION['coopex']['usuario']['primeiro_nome'] ?>"  hidden>
                </div>
              </div>
              <button class="btn btn-primary ml-auto" type="submit"><?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar" ?></button>
            </form>
          </div>
        </div>
      </div>
      <?php if (isset($_GET['id'])) { ?>
        <div id="panel-1" class="panel">
          <div class="panel-hdr">
            <h2>
              Curriculos
            </h2>
            <div class="panel-toolbar">
              <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
              <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
              <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
            </div>
          </div>
          <div class="panel-container show">
            <div class="panel-content">

              <div id="curriculos"></div>

            </div>

          </div>


        </div>
      <?php } ?>

    </div>

  </div>
  <?php include 'modal.php' ?>
</main>

<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>

<script>
  $(document).ready(function() {
    $("#curriculos").load("modulos/rh/vagas/ajax/carrega_curriculo.php?id_vaga=" + $("#id_vaga").val());
  });

  function historico() {
    console.log('entrei aqui ')
    // $("#historico").modal()
  }
</script>