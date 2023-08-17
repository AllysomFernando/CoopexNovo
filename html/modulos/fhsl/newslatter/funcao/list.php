<?php
define('__ROOT__', dirname(dirname(__FILE__)));
$user = $_SESSION['coopex']['usuario']['id_pessoa'];
require(__ROOT__ . '/ajax/conecta.php');

$sql = "SELECT * FROM coopex_fhsl.funcao";
$evento = $coopex->prepare($sql);
$evento->execute();
$dados = $evento->fetchAll(PDO::FETCH_OBJ);

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
<script src="js/core.js"></script>

<main id="js-page-content" role="main" class="page-content">
  <ol class="breadcrumb page-breadcrumb">
    <li class="breadcrumb-item"><a href="javascript:void(0);">FHSL</a></li>
    <li class="breadcrumb-item active">E-mails</li>

    <li class="position-absolute pos-top pos-right d-none d-sm-block"><code>ID: 03</code></li>
  </ol>
  <div class="subheader">
    <h1 class="subheader-title">
      <i class='subheader-icon fal fa-barcode-read'></i> Relação de funções

    </h1>
    <div class="subheader-title col-6 text-right" id="btn_cadastro">
      <a href="/fhsl/newslatter/funcao/cadastro">
        <button type="button" class=" btn btn-lg btn-primary waves-effect waves-themed">

          Cadastro de Funções
        </button>
      </a>
    </div>
  </div>
  <div class="row">
    <div class="col-xl-12">
      <div id="panel-1" class="panel">
        <div class="panel-hdr">
          <h2>
            Funções
          </h2>
          <div class="panel-toolbar">
            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
            <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
          </div>
        </div>

        <div class="panel-container show">
          <div class="panel-content">
            <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
              <thead class="bg-primary-600">
                <tr>
                  <th>#</th>
                  <th>Nome</th>
                  <th>Ação</th>
                </tr>
              </thead>
              <tbody>
                <?php
                foreach ($dados as $row) {
                ?>
                  <tr>
                    <td><strong><?php echo $row->idFuncao ?></strong></td>
                    <td><?php echo $row->nomeFuncao ?></td>
                    <td style="width: 100px" class="text-center">
                      <a href="/fhsl/newslatter/funcao/cadastro/<?php echo $row->idFuncao; ?>" class="btn btn-success btn-icon rounded-circle waves-effect waves-themed mr-2"><i class="fal fa-pencil"></i></a>
                      <!-- <a href="/fhsl/newslatter/emails/cadastro/<?php echo $row->idTemplate; ?>" class="btn btn-dark btn-icon rounded-circle waves-effect waves-themed mr-2"><i class="fal fa-paper-plane"></i></a> -->
                      <a onclick="inativa(<?php echo $row->id_vaga; ?>)" class="btn btn-danger btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-times"></i></a>
                    </td>
                  </tr>
                <?php
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>