<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("api/factories/RepositoryFactory.php");
require_once("partials/ticket-badge.php");

$id_menu = 119;
$repository = new RepositoryFactory();

$ticket = $repository->ticket->getById($_GET['id']);
$existeAtendimento = $repository->atendimento->getAtendimentoByTicketId($_GET['id']);
$usuario = $repository->coopex_antigo->getUserInfoByUserId($ticket->id_usuario);
$admin = $repository->coopex_antigo->getUserInfoByUserId($_SESSION['coopex']['usuario']['id_pessoa']);
$id_admin = $_SESSION['coopex']['usuario']['id_pessoa'];

$isAdmin = $_SESSION['coopex']['usuario']['sistema']['tipo_usuario'] == "ADMINISTRADOR";

$badge = getTicketPanelBadge($ticket->status);

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css?<?php echo time() ?>">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/cropperjs/cropper.css">
<link rel="stylesheet" media="screen, print" href="css/fa-solid.css">
<script src="js/core.js?<?php echo time() ?>"></script>

<main id="js-page-content" role="main" class="page-content">

  <?php
  if (!isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][1]) && ($ticket->id_usuario != $id_admin && !$isAdmin) ) {
  ?>
    <div class="alert border-danger bg-transparent text-secondary fade show" role="alert">
      <div class="d-flex align-items-center">
        <div class="alert-icon">
          <span class="icon-stack icon-stack-md">
            <i class="base-7 icon-stack-3x color-danger-900"></i>
            <i class="fal fa-ticket-alt"></i>
          </span>
        </div>
        <div class="flex-1">
          <span class="h5 color-danger-900">Seu usuário não possui permissão para acessar esta tela</span>
        </div>
        <a href="javascript:solicitarPermissao()" class="btn btn-outline-danger btn-sm btn-w-m">Solicitar acesso</a>
      </div>
    </div>
  <?php
    exit;
  }
  ?>

  <ol class="breadcrumb page-breadcrumb">
    <li class="breadcrumb-item"><a href="https://coopex.fag.edu.br/coopex/ticket/consulta">Ticket</a></li>
    <li class="breadcrumb-item active">Atendimento</li>
    <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID.
        <?php echo $id_menu ?>c
      </span></li>
  </ol>
  <div class="subheader">
    <h1 class="subheader-title">
      <i class='subheader-icon fal fa-ticket-alt'></i> Atendimento - Ticket <?php echo $ticket->id ?>
    </h1>
  </div>

  <iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 30%; background-color: #fff; top: 0; left: 0; height: 270px"></iframe>

  <div id="panel-ticket-info" class="panel">
    <div class="panel-hdr">
      <h2>
        Informações do Ticket
      </h2>
      <div class="panel-toolbar ml-2">
        <h5 class="m-0">
          <span class="badge <?php echo $badge->class_name ?> fw-400 l-h-n">
            <?php echo $badge->message ?>
          </span>
        </h5>
      </div>
    </div>
    <div class="panel-container show">
      <div class="panel-content">
        <h2>
          <strong><?php echo $ticket->titulo ?></strong>
        </h2>
        <h4>
          <?php echo nl2br($ticket->descricao) ?>
        </h4>
        <p><strong>URL: </strong><?php echo $ticket->url ? $ticket->url : "Não consta" ?></p>
        <p><strong>Data de envio: </strong><?php echo date_format(new DateTime($ticket->data_envio), "d/m/Y") ?></p>
        <?php require("partials/tabela-info-usuario.php") ?>
      </div>
    </div>
  </div>

  <?php if (!$existeAtendimento) { ?>

    <input type="text" name="id_ticket" id="id_ticket" value="<?php echo $ticket->id; ?>" hidden>
    <input type="text" name="id_atendente" id="id_atendente" value="<?php echo $id_admin; ?>" hidden>

    <button id="btn-atendimento" type="button" class="btn btn-primary btn-lg btn-block waves-effect waves-themed">
      Iniciar atendimento
    </button>

  <?php } ?>

  <?php if ($existeAtendimento) { ?>

    <div class="panel" id="panel-2">
      <div class="panel-hdr">
        <h2>Histórico de atendimento</h2>
      </div>
      <div class="panel-container">
        <div class="panel-content">

          <?php include("partials/historico-mensagem.php") ?>

          <hr>
          <?php if ($ticket->status != 1 && $ticket->status != 2) { ?>
            <form id="form-mensagem" action="" method="post">
              <?php

              $remetente = $_SESSION['coopex']['usuario']['id_pessoa'];
              $destinatario = $ticket->id_usuario == $remetente ? $atendimento->id_atendente : $ticket->id_usuario;

              ?>
              <div class="form-group">
                <input type="text" name="id_atendimento" id="mensagem-atendimento" value="<?php echo $atendimento->id ?>" hidden>
                <input type="text" name="remetente" id="mensagem-remetente" value="<?php echo $remetente ?>" hidden>
                <input type="text" name="destinatario" id="mensagem-destinatario" value="<?php echo $destinatario ?>" hidden>
                <textarea style="resize: none;" name="mensagem" id="mensagem" cols="30" rows="10" class="form-control" placeholder="Escreva aqui a sua mensagem" required></textarea>
              </div>
              <button class="btn btn-primary btn-block" type="submit">
                Enviar Mensagem
              </button>
            </form>
          <?php } ?>

          <div class="d-flex align-items-center justify-content-center mt-4">
            <?php if ($ticket->status != 2 && $ticket->status != 1) { ?>
              <button class="btn btn-danger w-100 mr-4" id="cancelarTicket" type="button">
                Cancelar Ticket
              </button>
            <?php } ?>
            <?php if ($isAdmin && $ticket->status != 1 && $ticket->status != 2) { ?>
              <button class="btn btn-success w-100" id="finalizarTicket" type="button">
                Finalizar Ticket
              </button>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>

  <?php } ?>

</main>

<script src="js/formplugins/select2/select2.bundle.js?<?php echo time() ?>"></script>
<script src="js/moment-with-locales.js?<?php echo time() ?>"></script>
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js?<?php echo time() ?>"></script>

<script>
  function cadastroOK(operacao, mensagem = "") {
    var msg = mensagem ? mensagem : "Atendimento iniciado";
    Swal.fire({
      type: "success",
      title: msg,
      showConfirmButton: false,
      timer: 1500,
      onClose: () => {
        window.location.reload();
      }
    });
  }

  function cadastroFalha(operacao) {
    var msg = operacao == 1 ? "Não foi possível inicar o atendimento" : "Falha ao alterar dados";
    Swal.fire({
      type: "error",
      title: msg,
      showConfirmButton: false,
      timer: 1500
    });
  }


  $(document).ready(async function() {

    (function() {
      'use strict';
      window.addEventListener('load', function() {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
          form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
      }, false);
    })();
  })
</script>

<script>
  $("#btn-atendimento").click(function(e) {

    const ticket = document.querySelector("#id_ticket")
    const admin = document.querySelector("#id_atendente")

    $.ajax({
      type: "POST",
      url: "modulos/coopex/ticket/api/routes/atendimento.php",
      data: JSON.stringify({
        ticketId: ticket.value,
        atendenteId: admin.value
      }),
      success: function(data) {
        cadastroOK(1)
      },
      error: function(xhr, text, err) {
        cadastroFalha(1)
        console.log(xhr)
      }
    });
  });

  $("#form-mensagem").submit(function(e) {

    e.preventDefault();

    var form = $(this);

    $.ajax({
      type: "POST",
      url: "modulos/coopex/ticket/api/routes/mensagem.php",
      data: form.serialize(),
      success: function(data) {
        cadastroOK(1, "Mensagem enviada com sucesso")
        console.log(data)
      },
      error: function(xhr, text, error) {
        cadastroFalha(1)
        console.log(xhr)
      }
    });
  });

  $("#finalizarTicket").click(function(e) {
    $.ajax({
      type: "POST",
      url: "modulos/coopex/ticket/api/routes/ticket/finalizar.php?id=<?php echo $ticket->id ?>",
      success: function(data) {
        cadastroOK(1, "Ticket finalizado com sucesso")
      },
      error: function(xhr, text, error) {
        cadastroFalha(1)
        console.log(xhr)
      }
    });
  });

  $("#cancelarTicket").click(function(e) {
    $.ajax({
      type: "POST",
      url: "modulos/coopex/ticket/api/routes/ticket/cancelar.php?id=<?php echo $ticket->id ?>",
      success: function(data) {
        cadastroOK(1, "Ticket cancelado com sucesso")
      },
      error: function(xhr, text, error) {
        cadastroFalha(1)
        console.log(xhr)
      }
    });
  });
</script>