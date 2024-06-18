<?php session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../../../../php/mysql.php");
require_once("../../../../php/utils.php");
require_once("../../../../php/sqlsrv.php");

$sql = "SELECT
			* 
		FROM
			vestibular.curso";
$res = $coopex_antigo->query($sql);
while ($row = $res->fetch(PDO::FETCH_OBJ)) {
	
	$sql2 = "SELECT
				* 
			FROM
				vestibular.periodo_letivo";
	$res2 = $coopex_antigo->query($sql2);
	while ($row2 = $res2->fetch(PDO::FETCH_OBJ)) {
		$sql3 = "SELECT
					CRS_ID_CURSO,
					rtrim( crs_nm_resumido ) AS Curso,
					RTRIM( PELINGRESSO.pel_ds_compacta ) AS Ingresso,
				CASE
						rca_ch_matutino 
						WHEN 'S' THEN
						'Matutino' ELSE 'Noturno' 
					END AS Turno,
					rtrim( alu_nu_matricula ) AS RA,
					RTRIM( tcu_ds_turma_curso ) AS Turma,
					rtrim( pes_nm_pessoa ) AS Nome,
					RTRIM( PELINICIOCURRICULO.pel_ds_compacta ) AS InicioCurriculo,
					RTRIM( crr_nm_titulo ) AS Curriculo,
					CONVERT ( CHAR ( 4 ), format ( iac_pc_cumprido, 'N1' ) ) + '%' AS PctCumprido,
					iac_nu_total_ch_curso AS CHCurso,
					iac_nu_total_ch_integralizada CHIntegralizada 
				FROM
					registro..PES_pessoa
					INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_aluno = pes_id_pessoa
					INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
					INNER JOIN academico..CRS_curso ON crs_id_curso = rca_id_curso
					INNER JOIN academico..HIS_historico_ingresso_saida ON his_id_registro_curso = rca_id_registro_curso
					INNER JOIN academico..PEL_periodo_letivo PELINGRESSO ON PELINGRESSO.pel_id_periodo_letivo = his_id_periodo_inicio
					INNER JOIN academico..CRR_curriculo ON crr_id_curso = rca_id_curso
					INNER JOIN academico..RCR_registro_curriculo ON rcr_id_registro_curso = rca_id_registro_curso 
					AND rcr_id_curriculo = crr_id_curriculo
					INNER JOIN academico..PEL_periodo_letivo PELINICIOCURRICULO ON PELINICIOCURRICULO.pel_id_periodo_letivo = crr_id_periodo_letivo_inicio
					INNER JOIN academico..iac_integralizacao_aluno_curso ON iac_id_registro_curso = rca_id_registro_curso
					LEFT OUTER JOIN academico..TCU_turmas_curso ON tcu_id_turma_curso = rca_id_turma_curso 
				WHERE
					CRS_ID_CURSO = $row->id_curso 
					AND PELINGRESSO.pel_ds_compacta = '$row2->id_periodo_letivo'
				ORDER BY
					Ingresso,
					Nome";	
		$res3 = mssql_query($sql3);

		if(mssql_num_rows($res3) > 0){
			$total = mssql_num_rows($res3);
			$sql4 = "INSERT INTO `vestibular`.`ingresso` ( `id_curso`, `id_periodo_letivo`, `total` )
							VALUES(
								$row->id_curso,
								$row2->id_periodo_letivo,
								$total
							)";
			$coopex->query($sql4);
		}
	}
}


	
?>