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
	$destinatario 	= $ficha->email;
	$nome			= $ficha->nome;

	$assunto		= "Ficha Financeira - Finalizada: $id_registro";
	$texto			= "Olá <b>$nome</b>*, sua Ficha Financeira foi finalizada, seus boletos foram atualizados e podem ser retirados no Sagres na aba Financeiro.";

	email($remetente, $destinatario, $assunto, $texto);


	$texto	= "Olá *$nome*, sua Ficha Financeira foi finalizada, seus boletos foram atualizados e podem ser retirados no Sagres na aba Financeiro.";

	$url = "https://simplechat.com.br/api/send/065eb096f036b233b928b4ae9b1a6ffb";
	$handle = curl_init($url);
	curl_setopt($handle, CURLOPT_POST, true); curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); curl_setopt($handle, CURLOPT_RETURNTRANSFER,1); curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true); curl_setopt($handle, CURLOPT_POSTFIELDS, [
		'nome' => 'Teste',
		'message' => $texto,
		'celular' => "55".$_SESSION['ficha_financeira']['whatsapp']
		//'celular' => "554599911388"
	]);
	$dados = curl_exec($handle);
	curl_close($handle);

	echo 1;
?>