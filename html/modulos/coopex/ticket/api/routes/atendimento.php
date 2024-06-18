<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

require_once "../../../../../php/mysql.php";
require_once "../repository/AtendimentoRepository.php";
require_once "../repository/TicketRepository.php";

$repository = new AtendimentoRepository($coopex);
$ticketRepository = new TicketRepository($coopex);

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "GET" && isset($_GET['id'])) {
  $id = $_GET['id'];
  $response = $repository->getById($id);
  echo json_encode($response);
  exit;
}

if ($method == "POST") {
  $post = new stdClass();

  $json = json_decode(file_get_contents("php://input", true));

  $post->id_ticket = $json->ticketId;
  $post->id_atendente = $json->atendenteId;

  try {
    $response = $repository->create($post);
    $ticket = $ticketRepository->getById($post->id_ticket);
    $ticket->status = 3;

    $ticketRepository->updateById($post->id_ticket, $ticket);

  } catch (\Throwable $th) {
    http_response_code(400);
    echo json_encode(array('erro' => "Ocorreu um erro ao cadastrar o ticket"));
    exit;
  }

  http_response_code(200);
  echo json_encode($response);
  exit;
}
