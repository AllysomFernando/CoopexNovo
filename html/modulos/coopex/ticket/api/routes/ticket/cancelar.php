<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

require_once "../../../../../../php/mysql.php";
require_once "../../../../../../php/services/Mailer.php";
require_once "../../../../../../php/repository/CoopexPessoaRepository.php";
require_once "../../repository/TicketRepository.php";
require_once "../../repository/AtendimentoRepository.php";
require_once "../../controllers/TicketController.php";

$pessoa_repository = new CoopexPessoaRepository();
$repository = new TicketRepository($coopex);
$atendimentoRepository = new AtendimentoRepository($coopex);
$mailer = new Mailer();
$controller = new TicketController($repository, $mailer);

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST" && isset($_GET['id'])) {
  $id_ticket = $_GET['id'];

  try {
    $pessoa_repository = new CoopexPessoaRepository();

    $response = $controller->setCancelado($id_ticket);

    $ticket = $repository->getById($id_ticket);
    $atendimento = $atendimentoRepository->getAtendimentoByTicketId($id_ticket);

    $atendimentoRepository->finalizarAtendimento($atendimento->id);

    $remetente = $pessoa_repository->getByIdPessoa($atendimento->id_atendente);
    $destinatario = $pessoa_repository->getByIdPessoa($ticket->id_usuario);

    $first_name = explode(' ', $remetente->nome)[0];

    $mailer->setRemetente('fernando@fag.edu.br');
    $mailer->setAssunto("Seu ticket foi finalizado");
    $mailer->addDestinatario([array('nome' => $destinatario->nome, 'email' => $destinatario->email)]);

    $email_data['titulo_email'] = 'Seu ticket foi finalizado';
    $email_data['titulo_ticket'] = 'Seu ticket foi finalizado';
    $email_data['finalizado_por'] = $first_name;
    $email_data['remetente'] = $remetente->nome;
    $email_data['ticket_status'] = 'Cancelado';
    $email_data['id_ticket'] = $id_ticket;

    $template = $mailer->getTemplate($email_data, 'ticket/finalizado.php');
    $mailer->setBody($template);

    $mailer->send();
  } catch (\Throwable $th) {
    http_response_code(400);
    echo json_encode(array('erro' => "Ocorreu um erro ao atualizar o ticket"));
    exit;
  }

  http_response_code(200);
  echo json_encode($response);
  exit;
}
