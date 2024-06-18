<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('America/Sao_Paulo');
header('Content-Type: application/json; charset=utf-8');

require_once "../../../../../php/mysql.php";
require_once "../../../../../php/services/Mailer.php";
require_once "../../../../../php/repository/CoopexPessoaRepository.php";
require_once "../repository/TicketRepository.php";
require_once "../controllers/TicketController.php";

$mailer = new Mailer();
$coopex->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
$coopex->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET CHARACTER SET utf8");

$repository = new TicketRepository($coopex);
$controller = new TicketController($repository, $mailer);
$coopex_pessoa = new CoopexPessoaRepository();

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "GET" && !isset($_GET['id'])) {
  $response = $controller->getAllTickets();
  echo json_encode($response);
  exit;
}

if ($method == "GET" && isset($_GET['id'])) {
  $id = $_GET['id'];
  $response = $controller->getTicketById($id);
  echo json_encode($response);
  exit;
}

if ($method == "POST" && !isset($_GET['id'])) {
  $post = new stdClass();

  $post->id_usuario = $_POST['id_usuario'];
  $post->titulo = $_POST['titulo'];
  $post->descricao = $_POST['descricao'];
  $post->url = $_POST['url'];

  try {
    $response = $controller->createTicket($post);

    $controller->prepareEmail($post, $response->id);
    $pessoa = $coopex_pessoa->getByIdPessoa($post->id_usuario);

    $email_data['titulo_email'] = $controller->mailer->getAssunto();
    $email_data['titulo_ticket'] = $post->titulo;
    $email_data['descricao_ticket'] = $post->descricao;
    $email_data['remetente'] = $pessoa->nome;
    $email_data['data_envio'] = $response->data_envio;
    $email_data['id_ticket'] = $response->id;

    $template = $controller->mailer->getTemplate($email_data, 'ticket/ticket.php');
    $controller->mailer->setBody($template);

    $controller->mailer->send();
  } catch (\Throwable $th) {
    http_response_code(400);
    echo json_encode(array('erro' => "Ocorreu um erro ao cadastrar o ticket: " . $th->getMessage()));
    exit;
  }

  http_response_code(200);
  echo json_encode($response);
  exit;
}
