<?php session_start();

	$valor_desconto = $_GET['valor_desconto'];

	echo $_SESSION['ficha_financeira']['valor_semestre'] = $valor_desconto;
?>
