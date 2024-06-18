<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . "/../controller/CursoController.php";

try {
  $controller = new CursoController();

  $id = $_GET['id'];

  $res = $controller->getCursoById($id);
  http_response_code(200);
  echo json_encode($res);
} catch (Exception $th) {
  http_response_code(500);
  echo json_encode(array("error" => $th->getMessage()));
}
