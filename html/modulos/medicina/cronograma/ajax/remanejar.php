<?php session_start();

	if(!strstr($_SERVER['HTTP_REFERER'], "https://coopex.fag.edu.br/")){
		exit;
	}
	
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	error_reporting(E_ALL);	
	ini_set('display_errors', 1);


	$id_horario_data 	= $_REQUEST['id_horario_data'];
	$id_cronograma 		= $_REQUEST['id_cronograma'];


	$sql = "SELECT
				id_sub_grupo
			FROM
				medicina.cronograma
			WHERE
				id_cronograma = $id_cronograma";

	$res = $coopex->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);

	$id_sub_grupo = $row->id_sub_grupo;


	echo $sql = "UPDATE medicina.cronograma SET `situacao`='1' WHERE (`id_cronograma`='$id_cronograma')";
	$coopex->query($sql);

	echo $sql = "INSERT INTO medicina.cronograma (`id_horario_data`, `id_sub_grupo`) VALUES ('$id_horario_data', '$id_sub_grupo')";
	$coopex->query($sql);


?>