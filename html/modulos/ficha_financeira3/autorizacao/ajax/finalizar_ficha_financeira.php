<?php
	require_once("../../../../php/config.php");
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	$id_registro = $_GET['id_ficha_financeira'];
	
	$sql = "INSERT INTO `ficha_financeira`.`ficha_financeira_etapa` ( `id_ficha_financeira`, `id_etapa`, `data_cadastro`)
			VALUES ($id_registro, 10, now())";
	$res = $coopex->query($sql);

	$sql = "SELECT
					nome,
					email,
					id_campus
				FROM
					ficha_financeira.ficha_financeira
					INNER JOIN coopex.pessoa USING ( id_pessoa ) 
				WHERE
					id_ficha_financeira = ".$id_registro;
	$res = $coopex->query($sql);
	$ficha = $res->fetch(PDO::FETCH_OBJ);

	$remetente		= "fichafinanceira@fag.edu.br";
	echo $destinatario 	= $ficha->email;
	$nome			= $ficha->nome;

	$assunto		= "Ficha Financeira - Finalizada: $id_registro";
	$texto			= "Olá <b>$nome</b>, seus boletos atualizados podem ser retirados no Sagres na aba Financeiro.";

	email($remetente, $destinatario, $assunto, $texto);

	echo 1;
?>