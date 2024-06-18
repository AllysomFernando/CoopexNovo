<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('America/Sao_Paulo');
header('Content-Type: application/json; charset=utf-8');

require_once "../../../../../php/mysql.php";
require_once "../../../../../php/services/Mailer.php";
require_once "../../../../../php/repository/CoopexPessoaRepository.php";
require_once "../repository/MensagemRepository.php";
require_once "../controllers/TicketController.php";

$pessoa_repository = new CoopexPessoaRepository();
$repository = new MensagemRepository($coopex);
$mailer = new Mailer();

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "GET" && isset($_GET['id'])) {
  $id = $_GET['id'];
  $response = $repository->getAllMensagensFromAtendimentoById($_GET['id']);
  echo json_encode($response);
  exit;
}

if ($method == "POST") {
  $post = new stdClass();

  $post->id = $_POST['id_atendimento'];
  $post->remetente = $_POST['remetente'];
  $post->destinatario = $_POST['destinatario'];
  $post->conteudo = $_POST['mensagem'];

  $ticket = $repository->getTicketByAtendimentoId($post->id);

  prepareMensagemParaDestinatario($post, $mailer, $ticket);

  try {
    $response = $repository->create($post);

    $mailer->send();
  } catch (\Throwable $th) {
    http_response_code(400);
    echo json_encode(array('erro' => "Ocorreu um erro ao enviar a mensagem"));
    exit;
  }

  http_response_code(200);
  echo json_encode($response);
  exit;
}


function prepareMensagemParaDestinatario($post, $mailer, $ticket)
{
  $pessoa_repository = new CoopexPessoaRepository();
  $remetente = $pessoa_repository->getByIdPessoa($post->remetente);
  $destinatario = $pessoa_repository->getByIdPessoa($post->destinatario);

  $first_name = explode(' ', $remetente->nome)[0];

  $mailer->setRemetente('fernando@fag.edu.br');
  $mailer->setAssunto("{$first_name} enviou uma mensagem no seu Ticket");
  $mailer->addDestinatario([array('nome' => $destinatario->nome, 'email' => $destinatario->email)]);

  $email_data['titulo_email'] = $mailer->getAssunto();
  $email_data['titulo_ticket'] = $ticket->titulo;
  $email_data['descricao_mensagem'] = utf8_decode($post->conteudo);
  $email_data['remetente'] = $remetente->nome;
  $email_data['data_envio'] = date("d/m/Y H:i:s");
  $email_data['id_ticket'] = $ticket->id;

  $template = $mailer->getTemplate($email_data, 'ticket/mensagem.php');
  $mailer->setBody($template);
}
