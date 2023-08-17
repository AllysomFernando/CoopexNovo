<?php session_start();

require_once("../../../../php/mysql.php");
require_once("conecta.php");

$id_reoferta = $_GET['reoferta'];

//selecionas os boletos das matrículas
$sql = "SELECT
	c.nome AS nome,
	a.data_pagamento AS pagamento_pre,
	a.valor AS valor_pre,
	a.pago AS status_pre,
	b.data_pagamento AS pagamento_matricula,
	b.valor AS valor_matricula,
	b.pago AS status_matricula
FROM
	pre_matricula AS a
INNER JOIN coopex.pessoa AS c ON a.id_pessoa = c.id_pessoa
LEFT JOIN matricula AS b ON (
	a.id_reoferta = b.id_reoferta
	AND a.id_pessoa = b.id_pessoa
)
WHERE
	a.id_reoferta = $id_reoferta
  order by c.nome asc";

$matricula = $coopex->prepare($sql);
$matricula->execute();


function corrigeData($data)
{
  if (empty($data)) {
    return '';
  } else {
    $data1 = explode("-", $data);
    return $data1[2] . '/' . $data1[1] . '/' . $data1[0];
  }
}

if ($matricula->rowCount() > 0) {
?>
  <style>
    table tr td {
      vertical-align: middle !important;
    }
  </style>

  <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
    <thead class="bg-primary-600">
      <tr>
        <th>Name</th>
        <th>Data pagamento pré matrícula</th>
        <th>Valor pago pré matrícula</th>
        <th>Status pré matrícula</th>
        <th>Data pagamento matrícula</th>
        <th>Valor pago matrícula</th>
        <th>Status matrícula</th>

      </tr>
    </thead>
    <tbody>
      <?php

      $dados = $matricula->fetchAll(PDO::FETCH_OBJ);
      foreach ($dados as $row) {
      ?>
        <tr>
          <td><strong><?php echo $row->nome ?></strong></td>
          <td><?php echo corrigeData($row->pagamento_pre) ?></td>
          <td align="right"><?php echo number_format($row->valor_pre, 2, ',', '.'); ?></td>
          <td><span class="badge badge-<?php echo $row->status_pre == 1 ? 'success' : 'danger' ?> badge-pill"><?php echo $row->status_pre ? 'Pago' : 'Em aberto' ?></span></td>
          <td><?php echo corrigeData($row->pagamento_matricula) ?></td>
          <td align="right"><?php echo number_format($row->valor_matricula, 2, ',', '.'); ?></td>
          <td><span class="badge badge-<?php echo $row->status_matricula == 1 ? 'success' : 'danger' ?> badge-pill"><?php echo $row->status_matricula ? 'Pago' : 'Em aberto' ?></span></td>
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