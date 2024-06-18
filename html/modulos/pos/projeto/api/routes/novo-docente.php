<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../controller/FileController.php";
require_once __DIR__ . "/../repository/coopex/DocenteRepository.php";
require_once __DIR__ . "/../repository/coopex/PosDatabase.php";
require_once __DIR__ . "/../models/Docente.php";
require_once "../../../../../php/mysql.php";

header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Sao_Paulo');

if ($_POST) {
  $client = new PosDatabase();
  $client->connect();
  $connection = $client->db;
  $file_controller = new FileController();
  $repository = new DocenteRepository($connection);

  try {

    $foto = "";
    $foto['name'] = "blank.jpg";

    $certificado = "";

    $docente_cpf = str_replace(array(".", "-", " ", "_"), "",$_POST['docente_cpf']);

    $alreadyExists = $repository->getByCpf($docente_cpf);

    if ($alreadyExists && isset($alreadyExists->id_docente)) {
      http_response_code(500);
      echo json_encode(array("error" => "Já existe um docente cadastrado com esse CPF"));
      exit;
    }

    if (isset($_FILES["docente_foto"]) && !empty($_FILES["docente_foto"]["name"])) {
      $foto = $_FILES["docente_foto"];
      $foto = $file_controller->uploadFotoPerfilDocente($foto);
    } else {
      $foto['name'] = "blank.jpg";
    }

    if (isset($_FILES["docente_certificado"]) && !empty($_FILES["docente_certificado"]["name"])) {
      $certificado = $_FILES["docente_certificado"];
      $certificado = $file_controller->uploadCertificadoDocente($certificado);
    } else {
      $certificado['name'] = '';
    }

    if (isset($_FILES["docente_aceite"]) && !empty($_FILES["docente_aceite"]["name"])) {
      $aceite_file = $_FILES["docente_aceite"];
      $aceite_file = $file_controller->uploadTermoAceiteDocente($aceite_file);
    } else {
      $aceite_file['name'] = '';
    }

    if (isset($_FILES["docente_uso_imagem"]) && !empty($_FILES["docente_uso_imagem"]["name"])) {
      $uso_imagem_file = $_FILES["docente_uso_imagem"];
      $uso_imagem_file = $file_controller->uploadTermoUsoImagemDocente($uso_imagem_file);
    } else {
      $uso_imagem_file['name'] = '';
    }

    $docente = new Docente();
    $docente->build(null, $_POST['docente_titulacao'], $_POST['docente_nome'], $docente_cpf, $_POST['docente_ies'], $_POST['docente_cidade'], $_POST['docente_descricao'], $_POST['docente_curriculo'], $foto['name'], $certificado["name"], 'BR', $aceite_file['name'], $uso_imagem_file['name'], 0);

    $data = $repository->create($docente);

    http_response_code(200);
    echo json_encode($data);
  } catch (Exception $th) {
    http_response_code(500);
    echo json_encode(array("error" => $th->getMessage()));
  }
}
