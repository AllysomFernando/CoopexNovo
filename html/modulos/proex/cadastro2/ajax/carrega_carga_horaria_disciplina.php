<?php session_start();
	
	require_once("../../../../php/sqlsrv.php");

	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	$id_disciplina = $_GET['id_disciplina'];

	$sql = "SELECT
				atc_qt_horas 
			FROM
				academico..ATC_atividade_curricular 
			WHERE
				atc_id_atividade IN ($id_disciplina)";	
	$res = mssql_query($sql);

	if(mssql_num_rows($res) > 0){
	 	$row = mssql_fetch_assoc($res);
	 }

	echo json_encode($row['atc_qt_horas']);
?>					