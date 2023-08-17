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




?>