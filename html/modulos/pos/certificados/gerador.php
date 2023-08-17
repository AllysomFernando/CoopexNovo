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
    <li class="breadcrumb-item"><a href="javascript:void(0);">Pós Graduação</a></li>
    <li class="breadcrumb-item active">Certificados</li>

    <li class="position-absolute pos-top pos-right d-none d-sm-block"><code>ID: 02</code></li>
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
            <form class="needs-validation" novalidate="" method="post" action="modulos/pos/certificados/ajax/cadastro.php" enctype="multipart/form-data">
              <div class="form-row">
                <div class="col-md-12 mb-3">
                  <label class="form-label" for="validationCustom02">Arquivo com os aprovados<span class="text-danger">*</span></label>
                  <input type="file" class="form-control" name="fileUpload" id="fileUpload" placeholder="" accept=".csv" required>
                </div>
              </div>
              <button class="btn btn-primary ml-auto" type="submit"><?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar" ?></button>
              <a class="btn btn-success ml-auto" style="color:'#fff'" href="https://coopex.fag.edu.br/modulos/pos/certificados/IMPORT.CSV" download>Download exemplo</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
  if (isset($_GET['id']) && $_GET['id'] == 1) {
    echo "<script>alert('Certificados gerados.')
      window.location.href='https://coopex.fag.edu.br/pos/certificados/gerador';
    </script>";
  } else if (isset($_GET['id']) && $_GET['id'] == 2) {
    echo "<script>alert('Não tem certificados para serem gerados.')
    window.location.href='https://coopex.fag.edu.br/pos/certificados/gerador';
    </script>";
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