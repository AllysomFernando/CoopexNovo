<?php session_start();

require_once("../../../../php/mysql.php");
require_once("conecta.php");

// $id_reoferta = $_GET['reoferta'];

//selecionas os boletos das matrículas
// $sql = "SELECT id_evento FROM evento_projeto where titulo like 'EXAME DE PROFICIÊNCIA%' order by id_evento desc limit 1 ";
// $evento = $conexao->prepare($sql);
// $evento->execute();
// $dados = $evento->fetchAll(PDO::FETCH_OBJ);


$sql = "select a.id_inscricao, b.nome,b.cpf,b.email,c.tipo,b.senha,a.pago from evento_inscricao as a
INNER JOIN evento_pessoa as b on a.id_pessoa = b.id_pessoa
INNER JOIN evento_valores as c on a.id_valor = c.id_valor
where a.id_evento = :id_evento";

$inscritos = $conexao->prepare($sql);
$inscritos->BindValue(':id_evento', $_GET['id']);

$inscritos->execute();



if ($inscritos->rowCount() > 0) {
?>
  <style>
    table tr td {
      vertical-align: middle !important;
    }
  </style>

  <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
    <thead class="bg-primary-600">
      <tr>
        <th>Inscrição</th>
        <th>Nome</th>
        <th>CPF</th>
        <th>E-mail</th>
        <th>Senha</th>
        <th>Linguagem</th>
        <th>Status</th>
        <th>Ação</th>
      </tr>
    </thead>
    <tbody>
      <?php

      $dados1 = $inscritos->fetchAll(PDO::FETCH_OBJ);
      foreach ($dados1 as $row) {
      ?>
        <tr>
          <td><strong><?php echo $row->id_inscricao ?></strong></td>
          <td><?php echo $row->nome ?></td>
          <td><?php echo $row->cpf ?></td>
          <td><?php echo $row->email ?></td>
          <td><?php echo $row->senha ?></td>
          <td><?php echo $row->tipo ?></td>
          
          <td><?php echo $row->pago == 0 ? 'Aguardando pagamento' : 'Pago' ?></td>



          <td style="width: 100px" class="text-center">

            <a href="/proficiencia/relatorios/altera/<?php echo $row->id_inscricao; ?>" class="btn btn-success btn-icon rounded-circle waves-effect waves-themed mr-2"><i class="fal fa-pencil"></i></a>
            <!-- <a onclick="inativa(<?php echo $row->id_vaga; ?>)" class="btn btn-danger btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-times"></i></a> -->
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