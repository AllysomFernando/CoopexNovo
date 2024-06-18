<?php
	require_once("../../../../../php/config.php");
	require_once("../../../../../php/mysql.php");
	require_once("../../../../../php/utils.php");

	$id_pessoa = $_GET['id_pessoa'];
	$data_retirada = date("Y-m-d H:i:s");
	$id_responsavel = $_SESSION['coopex']['usuario']['id_pessoa'];
	
	$sql = "UPDATE `colegio`.`sports` SET `retirada` = 0, `data_retirada` = null, `id_pessoa_retirada` = null WHERE `id_pessoa` = $id_pessoa";
	$coopex->query($sql);
	gravarLog('colegio.sports', $id_pessoa, 1, $sql, $id_pessoa);

	echo 1;
?>