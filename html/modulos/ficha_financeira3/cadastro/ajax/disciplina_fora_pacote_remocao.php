<?php session_start();
	require_once("../../../../php/sqlsrv.php");

	$id_disciplina 	= $_GET['id_disciplina'];
	$id_pessoa 		= $_GET['id_pessoa'];
	$id_curso 		= $_GET['id_curso'];

	$sql = "SELECT
				atc_id_atividade,
				atc_cd_atividade,
				atc_nm_atividade,
				atc_qt_horas 
			FROM
				academico..ATC_atividade_curricular 
			WHERE
				atc_id_atividade = $id_disciplina";	
	$res = mssql_query($sql);


	$array = null;
	if(mssql_num_rows($res) > 0){
	 	while($row = mssql_fetch_assoc($res)){

	 		$sql2 = "SELECT
						RCA1.rca_id_registro_curso,
						rtrim( alu_nu_matricula ) AS RA,
						rtrim( pes_nm_pessoa ) AS Nome,
						rtrim( atc_cd_atividade ) AS CdDisciplina,
						rtrim( atc_nm_atividade ) AS Atividade,
						atc1.atc_qt_horas AS Horas,
						rtrim( rst1.rst_ds_resultado ) AS Resultado,
						rtrim( PEL1.pel_ds_compacta ) AS PelCursada,
						hse1.hse_nu_faltas AS Faltas 
					FROM
						academico..ATC_atividade_curricular ATC1,
						academico..RCA_registro_curso_aluno RCA1,
						academico..RCR_registro_curriculo,
						academico..CRR_curriculo,
						academico..ALU_aluno,
						academico..PES_pessoa,
						academico..HSE_historico_escolar HSE1,
						academico..GCR_grade_curricular,
						academico..RST_resultado RST1,
						academico..PEL_periodo_letivo PEL1 
					WHERE
						rca1.rca_id_curso = ( SELECT crs_id_curso FROM academico..CRS_curso WHERE crs_id_curso = '$id_curso' ) 
						AND rca1.rca_id_aluno = alu_id_pessoa 
						AND alu_id_pessoa = $id_pessoa 
						AND rca1.rca_id_aluno = pes_id_pessoa 
						AND rca1.rca_id_forma_saida IS NULL 
						AND rcr_id_registro_curso = rca1.rca_id_registro_curso 
						AND rcr_st_registro_saida IS NULL 
						AND crr_id_curriculo = rcr_id_curriculo 
						AND gcr_id_curriculo = rcr_id_curriculo 
						AND gcr_id_atividade = atc_id_atividade 
						AND atc_id_atividade = hse1.hse_id_atividade 
						AND hse1.hse_id_registro_curso = RCA1.rca_id_registro_curso 
						AND RST1.rst_id_resultado = hse_id_resultado 
						AND RST1.rst_id_resultado IN ( 1000000005, 1000000006, 1000000007, 1000000011 ) 
						AND HSE1.hse_id_periodo_inicio = pel_id_periodo_letivo 
						AND atc_id_atividade = $id_disciplina 
						AND EXISTS (
						SELECT
							1 
						FROM
							academico..AVL_avaliacao_curricular 
						WHERE
							avl_id_atividade = gcr_id_atividade 
						AND avl_st_atividade NOT IN ( 'C', 'Q', 'D', 'L', 'U', 'M', 'E' )) 
						AND NOT EXISTS (
						SELECT
							1 
						FROM
							academico..HSE_historico_escolar HSE2,
							academico..EQV_equivalencia 
						WHERE
							hse2.hse_id_registro_curso = rca1.rca_id_registro_curso 
							AND eqv_id_atividade_equivalente = hse2.hse_id_atividade 
							AND eqv_id_grade_curricular = gcr_id_grade_curricular 
						AND hse2.hse_id_resultado IN ( 1000000001, 1000000002, 1000000003, 1000000004 )) 
					ORDER BY
						pes_nm_pessoa";
				$res2 = mssql_query($sql2);
				$dp   = mssql_num_rows($res2);

	 		$aux = null;
	 		$aux['atc_id_atividade'] = trim($row['atc_id_atividade']);
			$aux['atc_cd_atividade'] = trim($row['atc_cd_atividade']);
			$aux['atc_nm_atividade'] = trim(utf8_encode($row['atc_nm_atividade']));
			$aux['atc_qt_horas'] 	 = trim($row['atc_qt_horas']);
			$aux['dp'] 	 			 = $dp;

			if($aux['dp']){
				$divisao = 2;
			} else {
				$divisao = 1;
			}

			unset($_SESSION['ficha_financeira']['disciplinas'][$id_disciplina]);

			$_SESSION['ficha_financeira']['carga_horaria'] -= trim($row['atc_qt_horas']);
			$_SESSION['ficha_financeira']['carga_horaria_disciplinas_fora_pacote'] -= trim($row['atc_qt_horas']);
			$_SESSION['ficha_financeira']['carga_horaria_real_disciplinas_fora_pacote'] -= trim($row['atc_qt_horas'] / $divisao);
	 		$array[] = $aux;
	 	}
	 }



	echo json_encode($array);
?>					
