<?php

	//session_start();
	
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	$frase = $_REQUEST['frase'];
	$id_noticia = $_REQUEST['id_noticia'];

	$sql = "SELECT
				count(id_noticia) as total
			FROM
				noticia 
			WHERE
				palavra_chave  = '$frase'
			AND
				id_noticia <> $id_noticia";
	$res = $coopex->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);
	echo $row->total;

	