<?php
	require_once("../../../../php/config.php");
	require_once("../../../../php/mysql.php");

	$id_disciplina = $_GET['id_disciplina'];
	$id_ficha_financeira = $_GET['id_ficha_financeira'];
	
	$sql = "UPDATE `ficha_financeira`.`ficha_financeira_disciplinas` SET `dp` = 1 WHERE `id_disciplina` = $id_disciplina and id_ficha_financeira = $id_ficha_financeira";
	$res = $coopex->query($sql);
	echo 1;
?>