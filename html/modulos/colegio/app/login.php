<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../../../php/sqlsrv.php");

$login = 'mipadilha';
$senha = md5('FAG235');

/*
	id_tipo_pessoa = 1 = responsavel
	id_tipo_pessoa = 2 = dependente
	id_tipo_pessoa = 3 = aluno
*/


$sql = "SELECT
			aue_id_responsavel AS id_pessoa,
			pes_nu_cpf_cgc AS cpf,
			usr_cd_sexo AS sexo,
			usr_ds_nome AS nome,
			usr_ds_nickname AS usuario,
			usr_ds_email AS email,
			usr_ds_senha
		FROM
			academico..AUE_aluno_unidade_ensino
		INNER JOIN portal3..usr_usuario ON aue_id_responsavel = usr_id_pessoa
		INNER JOIN registro..PES_pessoa ON pes_id_pessoa = aue_id_responsavel 
		WHERE
			usr_ds_nickname = '$login'";

$res = mssql_query($sql);
if(mssql_num_rows($res)){
	$row = mssql_fetch_object($res);
	$row->id_tipo_pessoa = 1;
	
	

} else {

}


