<?php session_start();

	$total_horas = $_GET['total_horas'];

	echo $_SESSION['ficha_financeira']['carga_horaria_personalizada']  = $total_horas;
?>
