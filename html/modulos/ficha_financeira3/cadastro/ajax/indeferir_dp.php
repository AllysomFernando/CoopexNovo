<?php
	require_once("../../../../php/config.php");
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	$id_registro = $_GET['id_ficha_financeira'];
	$nome = $_GET['nome'];
	

	$usuario = $_SESSION['coopex']['usuario']['usuario'];
	$sql = "INSERT INTO `ficha_financeira`.`ficha_financeira_etapa` (`id_ficha_financeira`, `id_etapa`, `data_cadastro`, `enviado_por`)
			VALUES ($id_registro, 16, now(), '$usuario')";		

	$res = $coopex->query($sql);

	$sql = "UPDATE ficha_financeira.ficha_financeira SET id_etapa = 16 WHERE (id_ficha_financeira=$id_registro)";
	$coopex->query($sql);

	$remetente		= "fichafinanceira@fag.edu.br";
	$destinatario 	= "tesouraria@fag.edu.br";

	$assunto = "Ficha Financeira - Desconto DP Indeferido: $id_registro";
	$texto	= "<strong>$nome</strong><br>https://coopex.fag.edu.br/ficha_financeira/cadastro/$id_registro";
	email($remetente, $destinatario, $assunto, $texto);

	echo 1;
	
?>