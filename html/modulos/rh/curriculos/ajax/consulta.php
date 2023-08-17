<?php session_start();

require_once("../../../../php/mysql.php");
require_once("conecta.php");

$id_reoferta = $_GET['vaga'];

//selecionas os boletos das matrículas
$sql = "SELECT
	*
FROM
	vagas
  where id_vaga = :id
  order by titulo asc";

$vagas = $coopex->prepare($sql);
$vagas->bindValue(':id', $id_reoferta);
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
            <a onclick="inativa()" class="btn btn-danger btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-times"></i></a>
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
  </script>
<?php
} else {
  echo utf8_decode('<div class="alert alert-danger" role="alert"><strong>Ops!</strong> Nenhum título encontrado.</div>');
}
?>