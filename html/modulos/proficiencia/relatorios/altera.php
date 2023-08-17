<?php
require_once('ajax/conecta.php');





$sql = "select a.*, b.nome,b.cpf,b.email,c.tipo,b.senha,b.id_pessoa from evento_inscricao as a
INNER JOIN evento_pessoa as b on a.id_pessoa = b.id_pessoa
INNER JOIN evento_valores as c on a.id_valor = c.id_valor
where a.id_inscricao = :id_inscricao";

$inscritos = $conexao->prepare($sql);
$inscritos->BindValue(':id_inscricao', $_GET['id']);
$inscritos->execute();
$dados = $inscritos->fetchAll(PDO::FETCH_OBJ);
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
            <form class="needs-validation" novalidate="" method="post" action="modulos/proficiencia/relatorios/ajax/grava.php">
              <div class="form-row">
                <input type="text" class="form-control" name="inscricao" id="inscricao" value="<?php echo $dados[0]->id_inscricao ?>" hidden>
                <input type="text" class="form-control" name="evento" id="evento" value="<?php echo $dados[0]->id_evento ?>" hidden>
                <input type="text" class="form-control" name="pessoa" id="pessoa" value="<?php echo $dados[0]->id_pessoa ?>" hidden>
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Nome<span class="text-danger"></span></label>
                  <input type="text" class="form-control" value="<?php echo $dados[0]->nome ?>" >
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">E-mail<span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="email" value="<?php echo $dados[0]->email ?>" require>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">CPF<span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="cpf" value="<?php echo $dados[0]->cpf ?>" require>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Idioma<span class="text-danger">*</span></label>
                  <select class="form-control" name="idioma">
                    <?php if ($dados[0]->tipo == 'Ingles') { ?>
                      <option value="Ingles" selected>Inglês</option>
                      <option value="Espanhol">Espanhol</option>
                    <?php } else { ?>
                      <option value="Ingles">Inglês</option>
                      <option value="Espanhol" selected>Espanhol</option>
                    <?php } ?>

                  </select>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Senha<span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="senha" value="<?php echo $dados[0]->senha ?>" disabled>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Situação<span class="text-danger">*</span></label>
                  <select class="form-control" name="pago">
                    <?php if ($dados[0]->pago == 1) { ?>
                      <option value="1" selected>Pago</option>
                      <option value="0">Pendente de pagamento</option>
                    <?php } else { ?>
                      <option value="1">Pago</option>
                      <option value="0" selected>Pendente de pagamento</option>
                    <?php } ?>

                  </select>

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