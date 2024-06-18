<?php session_start();

require_once("../../../../php/mysql.php");
require_once("../../../../php/utils.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$id_cronograma = $_GET['id_periodo'];

$sql = "SELECT
				DATE_FORMAT(pre_inscricao_data_inicio, '%d/%m/%Y') as pre_inscricao_data_inicio,
				DATE_FORMAT(pre_inscricao_data_final, '%d/%m/%Y') as pre_inscricao_data_final,
				DATE_FORMAT(inscricao_data_inicial, '%d/%m/%Y') as inscricao_data_inicial,
				DATE_FORMAT(inscricao_data_final, '%d/%m/%Y') as inscricao_data_final
			FROM
				coopex_reoferta.periodo
			WHERE id_periodo = $id_cronograma";

$periodo = $coopex->query($sql);
$row = $periodo->fetch(PDO::FETCH_OBJ);
echo json_encode($row);

?>