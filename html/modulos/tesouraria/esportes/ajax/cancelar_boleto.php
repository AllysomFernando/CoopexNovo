<?php
	require_once("../../../../php/config.php");
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	$id_boleto = $_GET['id_boleto'];


	$sql = "UPDATE `colegio`.`matricula_boleto` SET `ativo` = 0 WHERE `id_matricula_boleto` = $id_boleto";
	$res = $coopex->query($sql);

	gravarLog('matricula_boleto', $id_boleto, 3, $sql, $id_boleto);

	echo $id_boleto;