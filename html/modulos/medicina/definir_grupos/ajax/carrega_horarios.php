<?php session_start();

	if(!strstr($_SERVER['HTTP_REFERER'], "https://coopex.fag.edu.br/")){
		exit;
	}
	
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	$id_horario = $_GET['id_horario'];

	$sql = "SELECT
				*
			FROM
				medicina.horario_dia
			WHERE id_horario = $id_horario";

	echo '{"data":[';

	$res = $coopex->query($sql);
	$arr = [];
	while($row = $res->fetch(PDO::FETCH_OBJ)){
		if($row->id_dia == 1){
			$row->id_dia = "Segunda";
		} else if($row->id_dia == 2){
			$row->id_dia = "Terça";
		} else if($row->id_dia == 3){
			$row->id_dia = "Quarta";
		} else if($row->id_dia == 4){
			$row->id_dia = "Quinta";
		} else if($row->id_dia == 5){
			$row->id_dia = "Sexta";
		} 
		$arr[] = json_encode($row);
	}

	echo implode(",", $arr);
	
	echo ']}';

?>