<?php
	require_once("../../../../php/config.php");
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	$id_registro = $_GET['id_ficha_financeira'];


	$usuario = $_SESSION['coopex']['usuario']['usuario'];
	$sql = "INSERT INTO `ficha_financeira`.`ficha_financeira_etapa` ( `id_ficha_financeira`, `id_etapa`, `data_cadastro`, `enviado_por` )
			VALUES ($id_registro, 13, now(), '$usuario')";

	$res = $coopex->query($sql);
	
	$sql = "UPDATE ficha_financeira.ficha_financeira SET id_etapa=13 WHERE (id_ficha_financeira=$id_registro)";
	$coopex->query($sql);		
	
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
	$destinatario 	= "naae@fag.edu.br";
	$nome			= $ficha->nome;

	$assunto		= "Ficha Financeira - Desconto DP: $id_registro";
	$texto			= "Acadêmico: <b>$nome</b><br><br><a href='https://coopex.fag.edu.br/ficha_financeira/cadastro/$id_registro'>Acessar Ficha Financeira</a>";

	email($remetente, $destinatario, $assunto, $texto);

	echo 1;
?>