<?php

$atendimento = $repository->atendimento->getAtendimentoByTicketId($ticket->id);

$mensagens = $repository->mensagem->getAllMensagensFromAtendimentoById($atendimento->id);

?>

<div class="" style="position: relative; overflow: hidden; width: auto; height: 100%;">

  <?php if (count($mensagens) > 0) {

    foreach ($mensagens as $mensagem) { 
      $minhaMensagem = $mensagem->remetente == $_SESSION['coopex']['usuario']['id_pessoa'];
      ?>

      <div class="d-flex h-100 flex-column" style="overflow: hidden; width: auto; height: 100%;">
        <div id="msg-<?php echo $mensagem->id; ?>" class="d-flex flex-column border-faded border-top-0 border-left-0 border-right-0 py-3 px-3 px-sm-4 px-lg-0 mr-0 mr-lg-5 flex-shrink-0">
          <div class="d-flex align-items-center flex-row ml-lg-5">
            <div class="fw-500 flex-1 d-flex flex-column">
              <div class="fs-lg <?php echo $minhaMensagem ? "color-primary-400" : ""; ?>">
                <?php echo $minhaMensagem ? $mensagem->autor . " (Eu)" : $mensagem->autor . " (Administrador)"; ?>
              </div>
            </div>
            <div class="color-fusion-200 fs-sm">
              <?php echo date_format(new DateTime($mensagem->data_envio), "d/m/Y H:i:s") ?>
            </div>
          </div>
          <div>
            <div class="pl-lg-5 ml-lg-5 pt-3 pb-4">
              <?php echo nl2br(utf8_encode($mensagem->conteudo)); ?>
            </div>
          </div>
        </div>
      </div>

  <?php }
  }

  ?>
</div>