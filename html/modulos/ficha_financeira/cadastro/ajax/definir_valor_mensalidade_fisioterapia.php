<?php session_start();

	$valor_desconto = $_GET['valor_desconto'];

	$_SESSION['ficha_financeira']['valor_semestre'] = $valor_desconto;

	echo json_encode(number_format($valor_desconto / 6, 2, ',', '.'));
?>
