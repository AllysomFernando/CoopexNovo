<?php session_start();

	if(!strstr($_SERVER['HTTP_REFERER'], "https://coopex.fag.edu.br/")){
		exit;
	}
	
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	error_reporting(E_ALL);	
	ini_set('display_errors', 1);


	$id_horario		= $_REQUEST['id_horario'];
	$id_horario_data= $_REQUEST['id_horario_data'];
	$id_sub_grupo	= $_REQUEST['id_sub_grupo'];
	$id_pessoa		= $_REQUEST['id_pessoa'];

	print_r($_REQUEST);

	$sql = "DELETE
			FROM
				medicina.sub_grupo_pessoa
			WHERE
				id_horario = $id_horario
			AND id_pessoa = $id_pessoa";
	$coopex->query($sql);

	$sql = "INSERT INTO medicina.sub_grupo_pessoa (
				id_horario,
				id_horario_data,
				id_sub_grupo,
				id_pessoa
			)
			VALUES
				(
					$id_horario,
					$id_horario_data,
					$id_sub_grupo,
					$id_pessoa
				)";
	$coopex->query($sql);

	/*$sql = "SELECT
				id_sub_grupo
			FROM
				medicina.cronograma
			WHERE
				id_cronograma = $id_cronograma";

	$res = $coopex->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);

	$id_sub_grupo = $row->id_sub_grupo;


	

	echo $sql = "INSERT INTO medicina.cronograma (`id_horario_data`, `id_sub_grupo`) VALUES ('$id_horario_data', '$id_sub_grupo')";
	$coopex->query($sql);*/


?>