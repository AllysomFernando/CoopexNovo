<?php
	require_once("../../../../../php/config.php");
	require_once("../../../../../php/mysql.php");
	require_once("../../../../../php/utils.php");

	$id_pessoa = $_POST['id_pessoa'];
	$observacao = $_POST['observacao'];
	$data = date("Y-m-d H:i:s");
	$id_responsavel = $_SESSION['coopex']['usuario']['id_pessoa'];

	$sql = "INSERT INTO tesouraria.observacao (
				`id_pessoa`,
				`observacao`,
				`data`,
				`autorizado`,
				`id_responsavel`
			)
			VALUES
				(
					'$id_pessoa',
					'$observacao',
					'$data',
					'$id_responsavel'
				)";
	$res = $coopex->query($sql);

	gravarLog('tesouraria.observacao', $id_pessoa, 1, $sql, json_encode($_GET));

	/*$sql = "UPDATE tesouraria.material
			SET quantidade = quantidade - 1
			WHERE
				id_material = $id_material";
	$coopex->query($sql);
	gravarLog('tesouraria.material', $id_material, 1, $sql, $id_material);
*/
	echo 1;
?>