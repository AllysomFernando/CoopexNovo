<?php
	require_once("../../../../php/config.php");
	require_once("../../../../php/mysql.php");

	$id_registro = $_GET['id_ficha_financeira'];
	$whats = $_GET['numero_whatsapp'];
	
	$sql = "INSERT INTO `ficha_financeira`.`ficha_financeira_etapa` ( `id_ficha_financeira`, `id_etapa`, `data_cadastro`, `forma_contato`, `contato` )
			VALUES ($id_registro, 2, now(), '1', '$whats')";
	$res = $coopex->query($sql);
	echo 1;
?>