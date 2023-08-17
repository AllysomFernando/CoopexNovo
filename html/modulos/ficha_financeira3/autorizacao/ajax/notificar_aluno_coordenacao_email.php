<?php
	require_once("../../../../php/config.php");
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	$id_registro = $_GET['id_ficha_financeira'];
	$email = $_GET['email'];
	
	$sql = "INSERT INTO `ficha_financeira`.`ficha_financeira_etapa` ( `id_ficha_financeira`, `id_etapa`, `data_cadastro`, `forma_contato`, `contato` )
			VALUES ($id_registro, 2, now(), '2', '$email')";
	$res = $coopex->query($sql);

	$remetente		= "fichafinanceira@fag.edu.br";
	$destinatario 	= $email;

	$assunto = "Ficha Financeira - Aprovação: $id_registro";
	$texto	= "Olá, sua <strong>ficha financeira</strong> foi gerada e precisa de aprovação, para aprovar acesse o link: https://coopex.fag.edu.br/ficha_financeira/aprovacao/$id_registro";
	email($remetente, $destinatario, $assunto, $texto);

	echo 1;
	
?>