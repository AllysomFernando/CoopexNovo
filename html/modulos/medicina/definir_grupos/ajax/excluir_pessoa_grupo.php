<?php session_start();

	if(!strstr($_SERVER['HTTP_REFERER'], "https://coopex.fag.edu.br/")){
		exit;
	}
	
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	$id_grupo = $_GET['id_grupo'];
	$id_pessoa = $_GET['id_pessoa'];

	$sql = "DELETE FROM medicina.grupo_pessoa WHERE id_grupo = '$id_grupo' AND id_pessoa = '$id_pessoa'";
	$coopex->query($sql);

	$sql = "UPDATE `pessoa` SET `id_tipo_usuario`='6' WHERE (`id_pessoa`='$id_pessoa')";
		$coopex->query($sql);

?>