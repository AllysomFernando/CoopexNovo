<?php
	require_once("../../../../php/config.php");
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	$id_registro = $_GET['id_ficha_financeira'];
	$nome = $_GET['nome'];
	
	$sql = "INSERT INTO `ficha_financeira`.`ficha_financeira_etapa` ( `id_ficha_financeira`, `id_etapa`, `data_cadastro`)
			VALUES ($id_registro, 5, now())";
	$res = $coopex->query($sql);

	$remetente		= "secretaria@fag.edu.br";
	$destinatario 	= "fichafinanceira@fag.edu.br";

	$assunto = "Ficha Financeira - Aprovada: $id_registro";
	$texto	= "<strong>$nome</strong><br>https://coopex.fag.edu.br/ficha_financeira/cadastro/$id_registro";
	email($remetente, $destinatario, $assunto, $texto);

	echo 1;
	
?>