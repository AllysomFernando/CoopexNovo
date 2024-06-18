<?php session_start();
	
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	$id_departamento = $_GET['id_curso'];

	$tabela = $id_departamento == 1000000115 ? 2 : 3;

	$sql = "SELECT
				id_carga_horaria,
				carga_horaria
			FROM
				coopex_reoferta.carga_horaria
				INNER JOIN coopex.departamento USING ( tabela_reoferta ) 
			WHERE
				id_departamento = $id_departamento 
			AND
				tabela_reoferta = $tabela
			ORDER BY
				carga_horaria";

	$periodo = $coopex->query($sql);			
	$array = null;
 	while($row = $periodo->fetch(PDO::FETCH_OBJ)){
 		$aux = null;
 		$aux['id_carga_horaria'] 	= $row->id_carga_horaria;
		$aux['carga_horaria'] 		= $row->carga_horaria;
 		$array[] = $aux;
 	}

	echo json_encode($array);			

?>