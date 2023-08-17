<?php
require_once('ajax/conecta.php');
$sql3 = "select obs from candidato_vaga where id_candidato = :id";
$stm5 = $coopex->prepare($sql3);
$stm5->bindValue(':id', isset($dados[0]->id_candidato));
$stm5->execute();
$dados5 = $stm5->fetchAll(PDO::FETCH_OBJ);

?>
<div class="modal fade" style="overflow:hidden;" id="historico" tabindex="-1" role="dialog" aria-labelledby="artigos" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="height:auto;">
      <div class="modal-body">
        <div class="artigo">
          <h3><span class="font-bold-modal">Adicionar Observações</span></h3>
        </div>

        <!-- <form id="formDados"> -->
        <textarea rows="12" cols="58" style="border:1px solid #E5E5E5; border-radius: 15px;margin-bottom: 2rem; padding:2rem" id="texto"><?php echo isset($dados5[0]->obs); ?></textarea>
        <button type="button" class="btn btn-primary ml-auto" onclick="teste()">Gravar</button>

        <!-- </form> -->
      </div>

    </div>
  </div>
</div>
<script>
  function teste() {
    $.ajax({
      type: 'POST',
      url: 'https://coopex.fag.edu.br/modulos/rh/vagas/ajax/cad_historico.php',
      data: {
        id_painel: <?php echo isset($dados[0]->id_candidato) ?>,
        historico: document.getElementById("texto").value,
      },
      dataType: 'json',
      success: function(response) {

        $('#historico').modal('hide');

      }
    });
  }
</script>