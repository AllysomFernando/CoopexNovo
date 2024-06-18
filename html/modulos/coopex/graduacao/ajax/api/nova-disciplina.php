<?php
error_reporting(E_ALL);	
ini_set('display_errors', 1);

require_once __DIR__ . "/../DisciplinaController.php";
require_once "../../../../../php/mysql.php";

try {
  $disciplina_controller = new DisciplinaController($coopex);
  
  $nome = $_POST['novo_disciplina_nome'];
  $docente = $_POST['id_docente'];
  $carga_horaria = $_POST['novo_disciplina_ch'];
  $ementa = $_POST['novo_disciplina_ementa'];
  
  $res = $disciplina_controller->adicionarDisciplina($nome, $docente, $carga_horaria, $ementa);
  echo json_encode($res);
} catch (Exception $th) {
  echo "Error: " . $th->getMessage();
}
