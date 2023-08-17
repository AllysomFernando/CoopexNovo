<?php session_start();
	require_once("../../../../php/sqlsrv.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/modulos/ficha_financeira/funcoes_sagres.php");

	$id_semestre 	= $_GET['id_semestre'];
	$id_disciplina 	= $_GET['id_disciplina'];
	$id_classe 		= $_GET['id_classe'];

	$array = get_horario($id_disciplina, $id_classe, $id_semestre);
	

	echo json_encode($array);
?>					
