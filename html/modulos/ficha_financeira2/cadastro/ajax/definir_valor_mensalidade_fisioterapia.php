<?php session_start();

	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	error_reporting(E_ALL);	
	ini_set('display_errors', 1);


	$valor_desconto = $_GET['valor_desconto'];

	//$_SESSION['ficha_financeira']['id_periodo_letivo'] = $id_periodo;

	$sql = "SELECT
				valor, valor_mensalidade, valor_mensalidade / 6 as mensalidade
			FROM
				ficha_financeira.valor_hora 
			WHERE
				id_valor_hora = $valor_desconto";

	$periodo = $coopex->query($sql);
	$row = $periodo->fetch(PDO::FETCH_OBJ);

	//print_r($row);
	

	$_SESSION['ficha_financeira']['valor_hora'] = $row->valor;
	$_SESSION['ficha_financeira']['valor_semestre'] = $row->valor_mensalidade;

	$row->valor = number_format($row->valor, 2, ',', '.');
	$row->valor_mensalidade = number_format($row->valor_mensalidade, 2, ',', '.');
	$row->mensalidade = number_format($row->mensalidade, 2, ',', '.');
	echo json_encode($row);
?>
