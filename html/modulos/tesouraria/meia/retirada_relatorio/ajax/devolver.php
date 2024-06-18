<?php
	require_once("../../../../../php/config.php");
	require_once("../../../../../php/mysql.php");
	require_once("../../../../../php/utils.php");

	$id_material = $_GET['id_material'];
	$id_pessoa = $_GET['id_pessoa'];
	$data_retirada = date("Y-m-d H:i:s");
	$id_responsavel = $_SESSION['coopex']['usuario']['id_pessoa'];

	$sql = "DELETE FROM tesouraria.retirada WHERE (id_material='$id_material' AND id_pessoa='$id_pessoa')";
	$res = $coopex->query($sql);

	gravarLog('tesouraria.retirada', $id_material, 1, $sql, json_encode($_GET));

	$sql = "UPDATE tesouraria.material
			SET quantidade = quantidade + 1
			WHERE
				id_material = $id_material";
	$coopex->query($sql);
	gravarLog('tesouraria.material', $id_material, 1, $sql, $id_material);

	echo 1;
?>