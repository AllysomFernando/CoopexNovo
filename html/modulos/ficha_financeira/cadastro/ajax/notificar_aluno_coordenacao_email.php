<?php
	require_once("../../../../php/config.php");
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	$id_registro = $_GET['id_ficha_financeira'];
	$email = trim($_GET['email']);

	$usuario = trim($_SESSION['coopex']['usuario']['usuario']);
	$sql = "INSERT INTO `ficha_financeira`.`ficha_financeira_etapa`
				 ( `id_ficha_financeira`, `id_etapa`, `data_cadastro`, `forma_contato`, `contato`, `enviado_por` )
			VALUES ($id_registro, 2, now(), '2', '$email', '$usuario')";
	$coopex->query($sql);

	$sql = "UPDATE ficha_financeira.ficha_financeira SET id_etapa=2 WHERE (id_ficha_financeira=$id_registro)";
	$coopex->query($sql);		

	$remetente		= "fichafinanceira@fag.edu.br";
	$destinatario 	= $email;

	$assunto = "Ficha Financeira - Aprovação: $id_registro";
	$texto	= "Olá, sua <strong>ficha financeira</strong> foi gerada e precisa de aprovação, para aprovar acesse o link: https://coopex.fag.edu.br/ficha_financeira/aprovacao/$id_registro";
	email($remetente, $destinatario, $assunto, $texto);

	echo 1;
	
?>