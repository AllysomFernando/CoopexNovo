<?php session_start();
$user = $_SESSION['coopex']['usuario']['id_pessoa'];
require_once("../../../../php/mysql.php");
require_once("conecta.php");

// $id_reoferta = $_GET['reoferta'];

//selecionas os boletos das matrículas

$sql = "select * from coopex_ceefag.banco_talentos ORDER BY id_cadastro desc";
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
        <th>Nome</th>
        <th>E-mail</th>
        <th>CPF</th>
        <th>Tipo</th>
        <th>Instituição</th>
        <th>Curso</th>
        <th>Ano</th>
        <th>Anexo</th>
      </tr>
    </thead>
    <tbody>
      <?php

      $dados = $vagas->fetchAll(PDO::FETCH_OBJ);
      foreach ($dados as $row) {
      ?>
        <tr>
          <td><strong><?php echo $row->nome ?></strong></td>
          <td><?php echo $row->email ?></td>
          <td><?php echo $row->cpf ?></td>
          <td><?php echo $row->tipo ?></td>
          <td><?php echo $row->instituicao ?></td>
          <td><?php echo $row->curso ?></td>
          <td><?php echo $row->ano ?></td>
          <td><a href="https://www.fag.edu.br/novo/arquivos/curriculos/<?php echo $row->anexo ?>"><?php echo $row->anexo ?></td>

          
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