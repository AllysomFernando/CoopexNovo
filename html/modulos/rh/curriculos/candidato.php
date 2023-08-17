<?php
$user = $_SESSION['coopex']['usuario']['id_pessoa'];
require_once('ajax/conecta.php');

if (isset($_GET['id'])) {
  $sql = "select a.*,b.classificacao,b.nome,b.pcd from painel_vaga as a 
          inner join candidato_vaga as b on a.id_candidato = b.id_candidato
          where id = :id
          ";
  $stm = $coopex->prepare($sql);
  $stm->bindValue(':id', $_GET['id']);
  $stm->execute();
  $dados = $stm->fetchAll(PDO::FETCH_OBJ);
}

?>

<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
<script src="js/core.js"></script>

<main id="js-page-content" role="main" class="page-content">
  <ol class="breadcrumb page-breadcrumb">
    <li class="breadcrumb-item"><a href="javascript:void(0);">RH</a></li>
    <li class="breadcrumb-item active">Candidato</li>

    <li class="position-absolute pos-top pos-right d-none d-sm-block"><code>ID: 03</code></li>
  </ol>
  <div class="subheader">
    <h1 class="subheader-title">
      <i class='subheader-icon fal fa-barcode-read'></i> RH
      <small>
        Cadastro candidato
      </small>
    </h1>

  </div>
  <div class="row">
    <div class="col-xl-12">
      <div id="panel-1" class="panel">
        <div class="panel-hdr">
          <h2>
            Perfil
          </h2>
          <div class="panel-toolbar">
            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
            <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
          </div>
        </div>
        <div class="panel-container show">
          <div class="panel-content">
            <form class="needs-validation" novalidate="" method="post" action="modulos/rh/vagas/ajax/altera_dados.php">
              <div class="form-row">
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Nome <span class="text-danger">*</span></label>

                  <input type="text" class="form-control" name="nome" placeholder="" value="<?php echo isset($dados[0]->nome) ? $dados[0]->nome : "" ?>" disabled>

                  <input type="text" class="form-control" name="vaga_old" id="vaga_old" placeholder="" value="<?php echo $dados[0]->id_vaga ?>" hidden>
                  <input type="text" class="form-control" name="candidato" id="candidato" placeholder="" value="<?php echo $dados[0]->id_candidato ?>" hidden>
                  <input type="text" class="form-control" name="id" id="id" placeholder="" value="<?php echo $_GET['id']; ?>" hidden>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Classificação <span class="text-danger ">*</span></label>
                  <select class="form-control" name="classificacao" onchange="historico()">
                    <option value="0" <?php echo $dados[0]->classificacao == 0 ? "selected" : "" ?>>Não Avaliado</option>
                    <option value="1" <?php echo $dados[0]->classificacao == 1 ? "selected" : "" ?>>Não Aprovado</option>
                    <option value="2" <?php echo $dados[0]->classificacao == 2 ? "selected" : "" ?>>Em Avaliação</option>
                    <option value="3" <?php echo $dados[0]->classificacao == 3 ? "selected" : "" ?>>Aprovado</option>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Vaga <span class="text-danger">*</span></label>
                  <select class="form-control" name="vaga"">
                    <?php
                    $vagas = "select * from vagas ";
                    $stm1 = $coopex->prepare($vagas);
                    $stm1->execute();
                    $dados1 = $stm1->fetchAll(PDO::FETCH_OBJ);
                    echo $dados[0]->id_vaga;
                    foreach ($dados1 as $row) { ?>
                      <option value=" <?php echo $row->id_vaga; ?>" <?php echo $row->id_vaga == $dados[0]->id_vaga ? "selected" : '' ?>><?php echo $row->ativo == 0 ? $row->titulo . ' - inativo' : $row->titulo; ?></option>
                  <?php
                    } ?>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">PCD <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="nome" placeholder="" value="<?php echo isset($dados[0]->pcd) ? $dados[0]->pcd : "" ?>" disabled>
                </div>
              </div>

              <button class="btn btn-primary ml-auto" type="submit"><?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar" ?></button>
              <button class="btn btn-warning ml-auto" type="button" onclick="historico()">Adicionar observação</button>
            </form>

          </div>
        </div>
      </div>
    </div>

  </div>
  <?php include 'modal.php' ?>
</main>

<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>

<script>
  function historico() {
    console.log('entrei aqui ')
    $("#historico").modal()
  }
</script>