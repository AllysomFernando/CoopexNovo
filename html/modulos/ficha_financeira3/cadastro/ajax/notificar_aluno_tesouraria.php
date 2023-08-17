<?php
	require_once("../../../../php/config.php");
	require_once("../../../../php/mysql.php");

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	$id_registro = $_GET['id_ficha_financeira'];
	$whats = $_GET['whats'];
	$whats = str_replace("(", "", $whats);
	$whats = str_replace(")", "", $whats);
	$whats = str_replace(" ", "", $whats);
	$whats = str_replace("-", "", $whats);
	$whats = str_replace("%20", "", $whats);
	//$whats = "45999113888";

	$texto	= "Declaração de aceite para alteração de disciplinas e previsão de mensalidades:
O(A) ACADÊMICO(A) que esta subscreve manifesta, desde já, a ciência de que as alterações de disciplinas na grade curricular do presente semestre poderão acarretar em mudanças no valor das mensalidades. Para aprovar acesse o link: https://coopex.fag.edu.br/ficha_financeira/aprovacaovalor/$id_registro";

	$url = "https://simplechat.com.br/api/send/065eb096f036b233b928b4ae9b1a6ffb";
	$handle = curl_init($url);
	curl_setopt($handle, CURLOPT_POST, true); curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); curl_setopt($handle, CURLOPT_RETURNTRANSFER,1); curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true); curl_setopt($handle, CURLOPT_POSTFIELDS, [
		'nome' => 'Teste',
		'message' => $texto,
		'celular' => "55".$whats
		//'celular' => "554599911388"
	]);
	$dados = curl_exec($handle);
	curl_close($handle);

	$dados = json_decode($dados);
	if(isset($dados->success)){

		$usuario = $_SESSION['coopex']['usuario']['usuario'];
		$sql = "INSERT INTO `ficha_financeira`.`ficha_financeira_etapa` ( `id_ficha_financeira`, `id_etapa`, `data_cadastro`, `forma_contato`, `contato`, `enviado_por` )
			VALUES ($id_registro, 7, now(), '1', '$whats', '$usuario')";

		$coopex->query($sql);

		$sql = "UPDATE ficha_financeira.ficha_financeira SET id_etapa=7 WHERE id_ficha_financeira=$id_registro";
		$coopex->query($sql);

		echo 1;
	} else {
		echo 0;
	}

?>