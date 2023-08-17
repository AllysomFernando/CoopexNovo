<?php session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	//if(!strstr($_SERVER['HTTP_REFERER'], "https://coopex.fag.edu.br/")){
		//exit;
	//}
	
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	$id_grupo = $_GET['id_grupo'];
	$id_pessoa = $_GET['id_pessoa'];

	$sql = "INSERT INTO medicina.grupo_pessoa (id_grupo, id_pessoa) VALUES ('$id_grupo', '$id_pessoa')";
	$coopex->query($sql);


	$sql = "SELECT
			id_pessoa, id_campus 
		FROM
			coopex.pessoa 
		WHERE
			id_pessoa = $id_pessoa";

	$pessoa = $coopex->query($sql);

	if ($pessoa->rowCount() == 0) {
		require_once("../../../../php/sqlsrv.php");

		$sql = "SELECT
				TOP 1 *
			FROM
				integracao..view_integracao_usuario
			WHERE
				id_pessoa = '$id_pessoa'";
		$res = mssql_query($sql);
		$row = mssql_fetch_assoc($res);


		$sql = "INSERT INTO `coopex`.`pessoa`(`id_pessoa`, `nome`, `usuario`, `email`, `id_tipo_usuario`, `cpf`, `avatar`, `id_campus`, `ra`)
			VALUES (" . $row['id_pessoa'] . ", '" . $row['nome'] . "', '" . $row['usuario'] . "', '" . $row['email'] . "', 6, '" . $row['cpf'] . "', null, '" . $row['id_faculdade'] . "', '" . $row['ra'] . "')";
		$coopex->query($sql);

		$sql = "UPDATE `pessoa` SET `id_tipo_usuario`='18' WHERE (`id_pessoa`='$id_pessoa')";
		$coopex->query($sql);
	}

?>