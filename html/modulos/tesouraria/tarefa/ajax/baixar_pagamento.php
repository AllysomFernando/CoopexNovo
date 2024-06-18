<?php
	require_once("../../../../php/config.php");
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	$id_boleto = $_GET['id_boleto'];


	$sql = "UPDATE `colegio`.`cdt_matricula_boleto` SET `pago` = 1, `data_pagamento` = now(), `id_retorno` = 1, `valor_pago` = `valor` WHERE `id_matricula_boleto` = $id_boleto";
	$res = $coopex->query($sql);

	gravarLog('cdt_matricula_boleto', $id_boleto, 2, $sql, $id_boleto);

	echo $id_boleto;