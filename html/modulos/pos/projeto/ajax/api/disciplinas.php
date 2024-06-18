<?php
error_reporting(E_ALL);	
ini_set('display_errors', 1);

require_once __DIR__ . "/../DisciplinaController.php";
require_once "../../../../../php/mysql.php";

try {
  $disciplina_controller = new DisciplinaController($coopex);
  $disciplinas = $disciplina_controller->buscarTodasDisciplinas();
  echo json_encode($disciplinas);
} catch (Exception $th) {
  echo "Error: " . $th->getMessage();
}