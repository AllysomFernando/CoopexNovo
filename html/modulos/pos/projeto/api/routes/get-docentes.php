<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../../../../php/mysql.php";
require_once __DIR__ . "/../controller/DocenteController.php";
require_once __DIR__ . "/../repository/coopex/PosDatabase.php";
require_once __DIR__ . "/../repository/coopex/DocenteRepository.php";

try {
  $client = new PosDatabase();
  $client->connect();
  $connection = $client->db;

  $repo = new DocenteRepository($connection);
  $docentes = $repo->getAll();
  echo json_encode(array("data" => $docentes));
} catch (\Throwable $th) {
  echo json_encode("Not Found");
}
