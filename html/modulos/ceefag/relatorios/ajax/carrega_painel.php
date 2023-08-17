<?php session_start();
$user = $_SESSION['coopex']['usuario']['id_pessoa'];
require_once("../../../../php/mysql.php");
require_once("conecta.php");
date_default_timezone_set('America/Sao_Paulo');

// $id_reoferta = $_GET['reoferta'];

//selecionas os boletos das matrículas
$sql = "SELECT distinct
b.id_vaga,
b.titulo,
c.Instituicao,
a.abertura,
a.expectativa,
a.encerramento,
a.area,
a.perfil,
a.recrutador,
CASE
WHEN a._status = 1 THEN
'publicada'
WHEN a._status = 2 THEN
'congelada'
WHEN a._status = 0 THEN
'encerrada'
END AS _status
FROM
historico_vaga AS a
INNER JOIN vagas AS b ON a.id_vaga = b.id_vaga
INNER JOIN `local` AS c ON b.`local` = c.id_local
INNER JOIN pessoa_local AS d on c.id_local = d.id_local
WHERE
d.id_pessoa = :id_user
ORDER BY
a.id DESC";

$curriculo = $coopex->prepare($sql);
$curriculo->BindValue(':id_user', $user);

$curriculo->execute();
$dados = $curriculo->fetchAll(PDO::FETCH_OBJ);



function farol($status,$expectativa,$encerramento){
  if($status == 'publicada' and $expectativa <= date('YYYY-MM-DD')){
    return 'Prazo seguro';
  }else if($status == 'congelada') {
    return 'Congelada';
  }else if ($encerramento != null and $encerramento <= $expectativa){
    return 'Fechada no prazo';
  }
}
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
      <th>#</th>
      <th>VAGA</th>
      <th>ÁREA</th>
      <th>PERFIL</th>
      <th>RECRUTADOR</th>
      <th>LOCAL</th>
      <th>DATA PUBLICAÇÃO</th>
      <th>EXPECTATIVA DE FECHAMENTO</th>
      <th>DATA FECHAMENTO</th>
      <th>STATUS</th>
      <th>FAROL</th>
    </tr>
  </thead>
  <tbody>
    <?php

    
    foreach ($dados as $row) {
    ?>
      <tr>
        <td><?php echo $row->id_vaga?></td>
        <td><a href="https://coopex.fag.edu.br/rh/vagas/cadastro/<?php echo $row->id_vaga?>"><?php echo $row->titulo?></a></td>
        <td><?php echo $row->area?></td>
        <td><?php echo $row->perfil?></td>
        <td><?php echo $row->recrutador?></td>
        <td><?php echo $row->Instituicao?></td>
        <td><?php echo $row->abertura?></td>
        <td><?php echo $row->expectativa?></td>
        <td><?php echo $row->encerramento?></td>
        <td><?php echo $row->_status?></td>
        <td><?php echo farol($row->_status,$row->expectativa,$row->encerramento)?></td>
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