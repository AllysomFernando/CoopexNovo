<?php session_start();

	if(!strstr($_SERVER['HTTP_REFERER'], "https://coopex.fag.edu.br/")){
		exit;
	}
	
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	$id_reoferta = $_GET['id_reoferta'];
	//$id_reoferta = 532;

	$sql = "SELECT
				id_cronograma, data_reoferta, horario_inicio, horario_termino, SEC_TO_TIME(TRUNCATE(TIME_TO_SEC(TIMEDIFF(horario_termino, horario_inicio)) * 1.2, 0)) as horas
			FROM
				coopex_reoferta.cronograma
			WHERE id_reoferta = $id_reoferta";

	echo '{"data":[';

	$periodo = $coopex->query($sql);
	$arr = [];
	while($row = $periodo->fetch(PDO::FETCH_OBJ)){
		$arr[] = json_encode($row);
	}

	echo implode(",", $arr);
	
	echo ']}';

?>