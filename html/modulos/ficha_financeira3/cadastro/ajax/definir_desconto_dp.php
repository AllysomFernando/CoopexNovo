<?php session_start();

	$id_disciplina = $_GET['id_disciplina'];
	$valor_desconto = $_GET['valor_desconto'];

	echo $_SESSION['ficha_financeira']['disciplinas'][$id_disciplina]['valor_desconto']  = $valor_desconto;
?>
