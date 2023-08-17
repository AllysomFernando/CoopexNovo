<?php session_start();

require_once("../../../../php/mysql.php");
require_once("conecta.php");

// $id_reoferta = $_GET['reoferta'];

//selecionas os boletos das matrículas
$sql = "SELECT
a.id,
a.id_candidato,
b.nome,
a.id_vaga,
a.curriculo AS anexo,
b.classificacao,
b.email,
b.fone,
b.cpf,
c.local
FROM
painel_vaga AS a
INNER JOIN candidato_vaga AS b ON a.id_candidato = b.id_candidato
INNER JOIN vagas AS c ON a.id_vaga = c.id_vaga
WHERE
a.id_vaga = :id_vaga
AND b.classificacao > 0
ORDER BY
b.classificacao ASC";

$curriculo = $coopex->prepare($sql);
$curriculo->BindValue(':id_vaga', $_GET['id_vaga']);

$curriculo->execute();

$dados = $curriculo->fetchAll(PDO::FETCH_OBJ);
// print_r($_GET);
if ($curriculo->rowCount() > 0) {
?>

  <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">

  <style>
    table tr td {
      vertical-align: middle !important;
    }
  </style>

  <table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
    <thead class="bg-primary-600">
      <tr>
        <th>Nome</th>
        <th>Telefone</th>
        <th>E-mail</th>
        <th>Arquivo</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php

      
      foreach ($dados as $row) {
      ?>
        <tr>
          <td><a href="https://coopex.fag.edu.br/rh/vagas/candidato/<?php echo $row->id ?>"><strong><?php echo $row->nome ?></strong></a></td>
          <td><?php echo $row->fone ?></td>
          <td><?php echo $row->email ?></td>
          <?php 
           if($row->local == '3'){
            $url = 'https://www4.fag.edu.br/api_fhsl/uploads/'.$row->anexo;
           }else{
            $url = 'https://www.fag.edu.br/novo/arquivos/curriculos/'.$row->anexo;
           };
           
          ?>
          <td><a href="<?php echo $url ?>" download><?php echo $row->anexo ?></a></td>

          <td style="width: 100px" class="text-center">
            <?php if ($row->classificacao == 1) { ?>
              <a onclick="apto(<?php echo $row->id_candidato; ?>)" class="btn btn-success btn-icon rounded-circle waves-effect waves-themed mr-2"><i class="fa fa-thumbs-up" style="color: #fff"></i></a>
              <a onclick="inapto(<?php echo $row->id_candidato; ?>)" class="btn btn-danger btn-icon rounded-circle waves-effect waves-themed"><i class="fa fa-thumbs-down" style="color: #fff"></i></a>
            <?php } ?>
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

      $(":input").inputmask();

      $('.data').datepicker({
        todayHighlight: true,
        orientation: "bottom left",
        locale: "pt-BR"
      });
    });

    function inapto(id) {

      console.log("passou");
      $.ajax({
        type: 'POST',
        url: '/modulos/rh/vagas/ajax/cadastro_dados.php',
        data: {
          acao: 'inapto',
          id_candidato: id,
        },
        dataType: 'json',
        success: function(response) {

          window.location.reload(true);
        }

      });
    };

    function apto(id) {

      console.log("passou");
      $.ajax({
        type: 'POST',
        url: '/modulos/rh/vagas/ajax/cadastro_dados.php',
        data: {
          acao: 'apto',
          id_candidato: id,
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
  echo ('<div class="alert alert-danger" role="alert"><strong>Ops!</strong> Nenhum Currículo encontrado.</div>');
}
?>