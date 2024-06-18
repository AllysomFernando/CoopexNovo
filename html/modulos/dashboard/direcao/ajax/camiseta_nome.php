<?php
	require_once("../../../../php/config.php");
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	$id_inscricao = $_GET['id_inscricao'];
	$nome = utf8_decode($_GET['nome']);

	$sql = "UPDATE `coopex_usuario`.`evento_inscricao`
			SET `observacao` = '$nome' WHERE `id_inscricao` = $id_inscricao";
	$res = $coopex_antigo->query($sql);