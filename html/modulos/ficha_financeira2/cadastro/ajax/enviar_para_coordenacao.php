<?php
	require_once("../../../../php/config.php");
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	$id_registro = $_GET['id_ficha_financeira'];
	$obs = $_GET['obs'];

	$sql = "SELECT
				email
			FROM
				ficha_financeira.ficha_financeira a
			INNER JOIN coopex.log b ON a.id_ficha_financeira = b.id_registro
			INNER JOIN coopex.pessoa c ON b.id_pessoa = c.id_pessoa
			WHERE
				id_ficha_financeira = $id_registro
			AND tabela = 'ficha_financeira.ficha_financeira_disciplinas'
			GROUP BY
				a.id_pessoa";
	$res = $coopex->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);
	$destinatario = $row->email;

	
	$sql = "INSERT INTO `ficha_financeira`.`ficha_financeira_etapa` ( `id_ficha_financeira`, `id_etapa`, `data_cadastro`)
			VALUES ($id_registro, 12, now())";
	$res = $coopex->query($sql);

	$remetente		= "secretaria@fag.edu.br";

	$assunto = "Ficha Financeira - Devolvida: $id_registro";
	$texto	= "<strong>$obs</strong><br>https://coopex.fag.edu.br/ficha_financeira/cadastro/$id_registro";
	email($remetente, $destinatario, $assunto, $texto);

	echo 1;
	
?>