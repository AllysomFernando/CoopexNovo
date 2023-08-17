<?php session_start();
$user = $_SESSION['coopex']['usuario']['id_pessoa'];
require_once("../../../../php/mysql.php");
require_once("conecta.php");

// $id_reoferta = $_GET['reoferta'];

//selecionas os boletos das matrículas

$sql = "SELECT
b.*
FROM
historico_vaga AS a
INNER JOIN vagas AS b ON a.id_vaga = b.id_vaga
INNER JOIN pessoa_local as c on b.`local` = c.id_local
WHERE
a._status = 1 and c.id_pessoa = ".$user."
ORDER BY
a.id DESC";
$vagas = $coopex->prepare($sql);
$vagas->execute();

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
        <th>Titulo</th>
        <th>Descrição</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php

      $dados = $vagas->fetchAll(PDO::FETCH_OBJ);
      foreach ($dados as $row) {
      ?>
        <tr>
          <td><strong><?php echo $row->titulo ?></strong></td>
          <td><?php echo $row->descricao ?></td>

          <td style="width: 100px" class="text-center">

            <a href="/rh/vagas/cadastro/<?php echo $row->id_vaga; ?>" class="btn btn-success btn-icon rounded-circle waves-effect waves-themed mr-2"><i class="fal fa-pencil"></i></a>
            <a onclick="inativa(<?php echo $row->id_vaga; ?>)" class="btn btn-danger btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-times"></i></a>
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
    $(document).ready(function() {
      // $('#dt-basic-example').dataTable({
      //   responsive: true,
      //   pageLength: 95,

      //   rowGroup: {
      //     dataSrc: 0
      //   },
      //   "columnDefs": [{
      //     "visible": false,
      //     "targets": 0
      //   }]
      // });

      // $(".botao_receber_matricula").click(function() {
      //   pagamentos($(this).attr('value'), 'reoferta_matricula');
      // });
      // $(".botao_receber_prematricula").click(function() {
      //   pagamentos($(this).attr('value'), 'reoferta_pre_inscricao');
      // });

      // function pagamentos(id, tabela) {
      //   $("#pagamentos_modal_conteudo").load("modulos/tesouraria/receber/ajax/titulos_em_aberto_valores.php?id_registro=" + id + "&tabela=" + tabela);
      // }

      $(":input").inputmask();

      $('.data').datepicker({
        todayHighlight: true,
        orientation: "bottom left",
        locale: "pt-BR"
      });
    });

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
        // window.location.reload();
      });
    };
  </script>
<?php
} else {
  echo utf8_decode('<div class="alert alert-danger" role="alert"><strong>Ops!</strong> Nenhuma Vaga encontrada.</div>');
}
?>