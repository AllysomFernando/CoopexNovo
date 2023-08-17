<?php session_start();

	$id_disciplina 			= $_GET['id_disciplina'];
	$id_equivalente 		= $_GET['id_equivalente'];
	$ch 					= $_GET['ch'];
	$id_unidade_responsavel	= $_GET['id_unidade_responsavel'];

	$_SESSION['ficha_financeira']['disciplinas'][$id_disciplina]['equivalencia'] = $id_equivalente;

	if($_SESSION['ficha_financeira']['disciplinas'][$id_disciplina]['ead']){
		echo $_SESSION['ficha_financeira']['disciplinas'][$id_disciplina]['carga_horaria'] = $ch;
	}
	
?>
