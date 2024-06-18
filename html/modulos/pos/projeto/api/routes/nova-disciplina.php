<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../controller/DisciplinaController.php";
require_once "../../../../../php/mysql.php";

try {
  $disciplina_controller = new DisciplinaController($coopex);

  $nome = $_POST['novo_disciplina_nome'];
  $carga_horaria = $_POST['novo_disciplina_ch'];
  $ementa = $_POST['novo_disciplina_ementa'];
  $cadastrado_por = $_POST['cadastrado_por'];
  $json_docente = json_decode($_POST['docentes']);

  $res = $disciplina_controller->adicionarDisciplina($nome, $json_docente[0]->id, $carga_horaria, $ementa, $cadastrado_por);

  foreach ($json_docente as $docente) {
    $disciplina_controller->adicionarEstruturaCurricular($res->id, $docente->id);
  }

  $disciplina = $disciplina_controller->buscarDisciplinaPorId($res->id);

  echo json_encode($disciplina);
} catch (Exception $th) {
  http_response_code(500);
  echo "Error: " . $th->getMessage();
}
