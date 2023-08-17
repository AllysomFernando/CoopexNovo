<?php	
	session_start();

	//echo $_SESSION['coopex']['usuario']['id_pessoa'];

	if($_SESSION['coopex']['usuario']['id_pessoa'] == 1000095486){
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
	}

	header ('Content-type: text/html; charset=UTF-8');
	date_default_timezone_set('America/Sao_Paulo');

	function texto($texto){
		return mb_strtoupper(utf8_encode($texto), 'UTF-8');
		//return utf8_encode($texto);
	}

	$_url =  "https://coopex.fag.edu.br";
?>