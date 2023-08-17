<?php
	require_once("../../../../php/config.php");
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	$id_registro = $_GET['id_ficha_financeira'];
	$email = $_GET['email'];
	
	$sql = "INSERT INTO `ficha_financeira`.`ficha_financeira_etapa` ( `id_ficha_financeira`, `id_etapa`, `data_cadastro`, `forma_contato`, `contato` )
			VALUES ($id_registro, 7, now(), '2', '$email')";
	$res = $coopex->query($sql);

	$remetente		= "fichafinanceira@fag.edu.br";
	$destinatario 	= $email;

	$assunto = "Ficha Financeira - Aprovação de Valores: $id_registro";
	$texto	= "O(A) ACADÊMICO(A) que esta subscreve  manifesta, desde já, a ciência de que a presente Ficha Financeira faz parte do Contrato Particular de Prestação de Serviços Educacionais firmado entre as partes como se nele estivesse transcrita, obrigando-se a adimpli-la fielmente. Para aprovar acesse o link: https://coopex.fag.edu.br/ficha_financeira/aprovacaovalor/$id_registro";
	email($remetente, $destinatario, $assunto, $texto);
	echo 1;
?>