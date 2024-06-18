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
								crs_id_curso as id_curso,
				tcu_ch_matutino,
			CASE
					tcu_ch_matutino 
					WHEN 'S' THEN
					'1' ELSE '2' 
				END turno,
				ser_id_serie as id_serie,
				tcu_id_turma_curso as id_turma
			FROM
				academico..HIS_historico_ingresso_saida a
				INNER JOIN academico..FMI_forma_ingresso b ON a.his_id_forma_ingresso = fmi_id_forma_ingresso
				INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_registro_curso = a.his_id_registro_curso
				INNER JOIN academico..CRS_curso ON rca_id_curso = crs_id_curso
				INNER JOIN academico..COL_colegiado ON crs_id_unidade = col_id_colegiado
				INNER JOIN academico..FAC_faculdade ON fac_id_faculdade = col_id_faculdade
				INNER JOIN academico..SAP_situacao_aluno_periodo_letivo_view SAP0 ON rca_id_registro_curso = SAP0.sap_id_registro_curso
				INNER JOIN academico..PEL_periodo_letivo PEL0 ON PEL0.pel_id_periodo_letivo = SAP0.sap_id_periodo_letivo
				INNER JOIN academico..IAP_informacoes_aluno_periodo_view ON iap_id_registro_curso = rca_id_registro_curso
				INNER JOIN academico..SER_serie ON iap_id_serie = ser_id_serie
				INNER JOIN registro..PES_pessoa ON PES_pessoa.pes_id_pessoa = rca_id_aluno
				INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
				INNER JOIN academico..TCU_turmas_curso ON tcu_id_turma_curso = rca_id_turma_curso 
			WHERE
				pel_ds_compacta = '20240' 
				AND fac_id_faculdade = 1000000006 
				AND iap_id_periodo_letivo = SAP0.sap_id_periodo_letivo 
				AND PES_ID_PESSOA = $row->id_usuario";

	$res2 = mssql_query($sql2);
	$row2 = mssql_fetch_object($res2);

	print_r($row2);

	$sql3 = "UPDATE `colegio`.`acamp` SET `id_curso` = $row2->id_curso, `id_turma` = '$row2->id_turma', `id_serie` = '$row2->id_serie' WHERE `id_usuario` = $row->id_usuario";

	try {
		$coopex->query($sql3);
	} catch (Exception $e) {
		echo 'Exceção capturada: ',  $e->getMessage(), "\n";
	}
}
