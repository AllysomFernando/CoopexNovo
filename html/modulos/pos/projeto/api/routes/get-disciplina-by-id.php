<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../controller/DisciplinaController.php";
require_once "../../../../../php/mysql.php";

try {
  $disciplina_controller = new DisciplinaController($coopex);
  $id = $_GET['id'];
  $res = $disciplina_controller->buscarDisciplinaPorId($id);
  echo json_encode($res);
  exit;
} catch (Exception $th) {
  echo "Error: " . $th->getMessage();
}
