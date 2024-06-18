<pre>
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("php/sqlsrv.php");

$sql = "SELECT
			* 
		FROM
			colegio.acamp";

$res = $coopex->query($sql);

while ($row = $res->fetch(PDO::FETCH_OBJ)) {
	//print_r($row->id_pessoa);

	$sql2 = "SELECT
				aue_nm_responsavel as responsavel
			FROM
				academico..AUE_aluno_unidade_ensino 
			WHERE
				aue_id_aluno = $row->id_usuario";

	$res2 = mssql_query($sql2);
	$row2 = mssql_fetch_object($res2);

	print_r($row2);

	$sql3 = "UPDATE `colegio`.`acamp` SET `responsavel` = '".$row2->responsavel."' WHERE `id_usuario` = $row->id_usuario";

	try {
		$coopex->query($sql3);
	} catch (Exception $e) {
		echo 'Exceção capturada: ',  $e->getMessage(), "\n";
	}
}
