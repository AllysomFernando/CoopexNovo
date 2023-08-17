<?php session_start();

	$id_disciplina 	= $_GET['id_disciplina'];
	$id_classe 		= $_GET['id_classe'];

	echo $_SESSION['ficha_financeira']['disciplinas'][$id_disciplina]['id_classe']  = $id_classe;
?>
