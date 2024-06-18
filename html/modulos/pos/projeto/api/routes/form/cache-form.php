<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../../controller/DisciplinaController.php";
require_once "../../../../../../php/mysql.php";

try {

  if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $id = $_GET['id_pessoa'];
    $sql = "SELECT * FROM pos.cache_form e WHERE e.id_pessoa = $id";
    $query = $coopex->query($sql);
    $res = $query->fetch(PDO::FETCH_OBJ);

    echo json_encode($res ? $res : new stdClass());
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $body = json_decode(file_get_contents("php://input", true));

    // var_dump($body);
    // echo json_encode($body->entries);
    // exit;

    $sql = "SELECT * FROM pos.cache_form e WHERE e.id_pessoa = " . $body->id_pessoa;
    $query = $coopex->query($sql);
    $cache = $query->fetch(PDO::FETCH_OBJ);

    if (isset($cache->id) && $cache->id) {
      $sql = "UPDATE pos.cache_form SET entries=:entries WHERE id_pessoa = :id_pessoa";
      $stmt = $coopex->prepare($sql);
      $stmt->bindParam(":id_pessoa", $body->id_pessoa);
      $stmt->bindValue(":entries", json_encode($body->entries));
      $stmt->execute();
    } else {
      $sql = "INSERT INTO pos.cache_form (id_pessoa, entries) VALUES (:id_pessoa, :entries)";
      $stmt = $coopex->prepare($sql);
      $stmt->bindParam(":id_pessoa", $body->id_pessoa);
      $stmt->bindValue(":entries", json_encode($body->entries));
      $stmt->execute();
    }

    echo json_encode((object) array("message" => "Formulário cadastrado com sucesso"));
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
    $body = json_decode(file_get_contents("php://input", true));

    $sql = "DELETE FROM pos.cache_form WHERE id_pessoa = :id_pessoa";
    $stmt = $coopex->prepare($sql);
    $stmt->bindParam(":id_pessoa", $body->id_pessoa);
    $stmt->execute();

    echo json_encode((object) array("message" => "Formulário excluido com sucesso"));
    exit;
  }
} catch (Exception $th) {
  echo "Error: " . $th->getMessage();
}
