<?php session_start();

	if(!strstr($_SERVER['HTTP_REFERER'], "https://coopex.fag.edu.br/")){
		exit;
	}
	
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	$id_projeto = $_GET['id_projeto'];
	//$id_reoferta = 532;

	$sql = "SELECT
				*
			FROM
				pos.estrutura_curricular
			WHERE id_projeto = $id_projeto";

	echo '{"data":[';

	$res = $coopex->query($sql);
	$arr = [];
	while($row = $res->fetch(PDO::FETCH_OBJ)){
		$arr[] = json_encode($row);
	}

	echo implode(",", $arr);
	
	echo ']}';

?>