<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
<script src="js/core.js"></script>
<script src="modulos/proficiencia/certificados/js/script.js"></script>

<!-- proficiencia/certificados/gerador -->
<?
require_once('ajax/conecta.php');

$sql = "select b.id_evento from coopex_cascavel.projeto as a 
  inner join coopex_usuario.evento_projeto as b on a.id_projeto = b.id_projeto
  where a.titulo like '%proficiência%' and  a.projeto_periodo_final < CURDATE() ORDER BY a.id_projeto desc limit 1";
$stm = $conexao->prepare($sql);
$stm->execute();
$rs = $stm->fetchAll(PDO::FETCH_OBJ);

?>
<style>
  table tr td {
    vertical-align: middle !important;
  }

  table {
    padding: 15px;
  }
</style>

<main id="js-page-content" role="main" class="page-content">
  <ol class="breadcrumb page-breadcrumb">
    <li class="breadcrumb-item"><a href="javascript:void(0);">PROFICIÊNCIA</a></li>
    <li class="breadcrumb-item active">Certificados</li>

    <li class="position-absolute pos-top pos-right d-none d-sm-block"><code>ID: 01</code></li>
  </ol>
  <div class="subheader">
    <h1 class="subheader-title">
      <i class='subheader-icon fal fa-barcode-read'></i> Gerador de certificados
      <small>
        Certificados.
      </small>
    </h1>

  </div>

  <div class="row">
    <div class="col-xl-12">
      <div id="panel-1" class="panel">
        <div class="panel-hdr">
          <h2>
            Importar Arquivo.
          </h2>
          <div class="panel-toolbar">
            <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
            <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
            <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
          </div>
        </div>
        <div class="panel-container show">
          <div class="panel-content">
            <form class="needs-validation" novalidate="" method="post" action="modulos/proficiencia/certificados/ajax/cadastro.php" enctype="multipart/form-data">
              <div class="form-row">
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Arquivo com os aprovados<span class="text-danger">*</span></label>
                  <input type="file" class="form-control" name="fileUpload" id="fileUpload" placeholder="" accept=".csv" required>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Data da prova <span class="text-danger">*</span></label>
                  <input type="date" class="form-control" name="data" id="data" placeholder="" required>
                </div>
              </div>
              <button class="btn btn-primary ml-auto" type="submit"><?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar" ?></button>
            </form>

          </div>
          <div class="panel-hdr">
            <h2>
              Certificado Gerados
            </h2>
            <div class="panel-toolbar">
              <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
              <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
              <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
            </div>
          </div>
          <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
            <thead class="bg-primary-600">

              <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Ação</th>
              </tr>
            </thead>
            <tbody>
              <?
              $query = "SELECT * FROM `certificados` where cadastro_usuario = 4185";
              $stmCert = $conexao->prepare($query);
              $stmCert->execute();
              $results = $stmCert->fetchAll(PDO::FETCH_OBJ);

              foreach ($results as $value) {
                $text = explode('</strong>', $value->texto);
                $nome = explode('<strong>', $text[0]);
                //echo $nome[1] . '<br>';
                echo '<tr>';
                echo '<td>' . $value->id_certificado . '</td>';
                echo '<td>' . $nome[1] . '</td>';
                echo '<td style="display:flex; align-items: baseline;padding-top:10px"><a href="proficiencia/certificados/altera/' . $value->id_certificado . '" class="btn btn-success btn-icon rounded-circle waves-effect waves-themed mr-2"><i class="fal fa-pencil"></i></a>
                  <p class="btn btn-danger btn-icon rounded-circle waves-effect waves-themed" onclick="del(' . $value->id_certificado . ')" ><i class="fal fa-times"></i></p>
                  </td>';
                echo '</tr>';
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php
  if (isset($_GET['id']) && $_GET['id'] == 1) {
    echo "<script>alert('Certificados gerados.')</script>";
  } else if (isset($_GET['id']) && $_GET['id'] == 2) {
    echo "<script>alert('Não tem certificados para serem gerados.')</script>";
  }
  ?>
</main>

<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>


<script>
  function del(certificado) {
    // @ts-ignore
    $.ajax({
      type: 'POST',
      url: 'proficiencia/certificados/deleta/',
      data: {
        id_certificado: certificado
      },

      success: function(rs) {
        alert('Certificado excluido.')
        document.location.reload(true);
      }
    })
  }
</script>