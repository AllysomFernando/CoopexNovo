<?php
	require_once("../../../../php/config.php");
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	$id_registro = $_GET['id_ficha_financeira'];
	
	$sql = "INSERT INTO `ficha_financeira`.`ficha_financeira_etapa` ( `id_ficha_financeira`, `id_etapa`, `data_cadastro`)
			VALUES ($id_registro, 13, now())";
	$res = $coopex->query($sql);

	$sql = "UPDATE ficha_financeira.ficha_financeira SET id_etapa=13 WHERE (id_ficha_financeira=$id_registro)";
	$coopex->query($sql);

	

	echo 1;
?>