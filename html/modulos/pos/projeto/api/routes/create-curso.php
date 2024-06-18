<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . "/../controller/CursoController.php";
require_once __DIR__ . "/../repository/coopex/PosDatabase.php";

try {
  $controller = new CursoController();
  $database = new PosDatabase();

  $body = $_POST;

  if ($body['id_projeto'] == null || $body['id_projeto'] == 0) {
    $res = $controller->createCurso($body);
  } else {
    $res = $controller->updateCurso($body);
  }

  http_response_code(200);
  echo json_encode($res);
} catch (Exception $th) {
  http_response_code(500);
  echo json_encode(array("error" => $th->getMessage(), "message" => "Ocorreu um erro ao cadastrar"));
}
