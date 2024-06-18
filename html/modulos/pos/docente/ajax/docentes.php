<?php session_start();

require_once("../../../../php/mysql.php");
require_once("../../../../php/utils.php");
require_once "DocenteController.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
  $controller = new DocenteController($coopex);
  $docentes = $controller->buscarTodosDocentes();
  echo json_encode(array("data" => $docentes));
} catch (\Throwable $th) {
  echo json_encode("Not Found");
}

?>