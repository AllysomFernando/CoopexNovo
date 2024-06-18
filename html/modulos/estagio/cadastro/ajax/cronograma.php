<?php session_start();

// if (!strstr($_SERVER['HTTP_REFERER'], "https://coopex.fag.edu.br/")) {
// 	exit;
// }

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-type:application/json");

require_once("../../../../php/mysql.php");
require_once("../../../../php/utils.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$id_estagio = $_GET['id_estagio'];
$sql = "SELECT
				0 as indice,
				id_cronograma,
				id_estagio,
				data, 
				TIME_FORMAT(carga_horaria, '%H:%i') as carga_horaria, 
				descricao
			FROM
				estagio.cronograma
			WHERE id_estagio = $id_estagio";


$periodo = $coopex->query($sql);
$arr = [];
while ($row = $periodo->fetch(PDO::FETCH_OBJ)) {
	$arr[] = $row;
}

$response = array("data" => $arr);

// echo implode(",", $arr);

// echo ']}';

echo json_encode($response);

?>