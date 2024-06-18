<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . "/../repository/coopex/ParceirosRepository.php";
require_once __DIR__ . "/../repository/coopex/PosDatabase.php";

try {
  $client = new PosDatabase();
  $client->connect();
  $connection = $client->db;
  $repository = new ParceirosRepository($connection);

  $res = $repository->getByCursoId($_GET['id']);

  http_response_code(200);
  echo json_encode((object) array("data" => $res));
  // echo json_encode($res);
  exit;
} catch (Exception $th) {
  http_response_code(500);
  echo json_encode(array("error" => $th->getMessage()));
  exit;
}
