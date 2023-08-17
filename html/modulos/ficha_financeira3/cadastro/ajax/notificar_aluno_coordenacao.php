<?php
	require_once("../../../../php/config.php");
	require_once("../../../../php/mysql.php");

	$id_registro = $_GET['id_ficha_financeira'];
	$whats = $_GET['numero_whatsapp'];
	
	$usuario = $_SESSION['coopex']['usuario']['usuario'];
	$sql = "INSERT INTO `ficha_financeira`.`ficha_financeira_etapa` ( `id_ficha_financeira`, `id_etapa`, `data_cadastro`, `forma_contato`, `contato`, `enviado_por` )
			VALUES ($id_registro, 2, now(), '1', '$whats', '$usuario')";
	$res = $coopex->query($sql);

	$sql = "UPDATE ficha_financeira.ficha_financeira SET id_etapa=2 WHERE (id_ficha_financeira=$id_registro)";
	$coopex->query($sql);

	echo 1;
?>