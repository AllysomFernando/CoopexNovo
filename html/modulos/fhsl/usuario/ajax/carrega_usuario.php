<?php session_start();
$user = $_SESSION['coopex']['usuario']['id_pessoa'];
require_once("../../../../php/mysql.php");
require_once("conecta.php");

// $id_reoferta = $_GET['reoferta'];

//selecionas os boletos das matrículas

$sql = "SELECT id, user FROM coopex_fhsl.users";
$vagas = $coopex->prepare($sql);
$vagas->execute();
$dados = $vagas->fetchAll(PDO::FETCH_OBJ);

if ($vagas->rowCount() > 0) {
?>
  <style>
    table tr td {
      vertical-align: middle !important;
    }
  </style>

  <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
    <thead class="bg-primary-600">
      <tr>
        <!-- <th>Titulo</th> -->
        <th>Descrição</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php

      // $dados = $vagas->fetchAll(PDO::FETCH_OBJ);
      foreach ($dados as $row) {
      ?>
        <tr>
          <!-- <td><strong><?php echo $row->titulo ?></strong></td> -->
          <td><?php echo $row->user ?></td>

          <td style="width: 100px" class="text-center">
            <a href="/fhsl/usuario/cadastro/<?php echo $row->id; ?>" class="btn btn-success btn-icon rounded-circle waves-effect waves-themed mr-2"><i class="fal fa-pencil"></i></a>
          </td>
        </tr>
      <?php
      }

      ?>
    </tbody>
  </table>

  <div class="modal fade" id="pagamentos_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" id="pagamentos_modal_conteudo">
    </div>
  </div>

  <script>
    function inativa(id) {

      console.log("passou");
      $.ajax({
        type: 'POST',
        url: '/modulos/rh/vagas/ajax/cadastro_dados.php',
        data: {
          acao: 'inativa',
          id_vaga: id,
        },
        dataType: 'json',
        success: function(response) {

          window.location.reload(true);
        }
      });
    };
  </script>
<?php
} else {
  echo utf8_decode('<div class="alert alert-danger" role="alert"><strong>Ops!</strong> Nenhuma Vaga encontrada.</div>');
}
?>