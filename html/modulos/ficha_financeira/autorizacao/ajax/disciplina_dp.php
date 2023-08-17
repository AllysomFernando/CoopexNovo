<?php session_start();
	require_once("../../../../php/sqlsrv.php");
	require_once("../../../../php/mysql.php");

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	$id_semestre = $_GET['id_semestre'];
	$_SESSION['ficha_financeira']['id_periodo_letivo'] = $id_semestre;
	$id_pessoa 	= $_GET['id_pessoa'];
	$id_periodo = $_GET['id_periodo'];
	$id_periodo = trim(str_replace("/", "", $id_periodo));
	$id_curso = $_GET['id_curso'];
	$_SESSION['ficha_financeira']['id_curso'] = $id_curso;



	echo $sql = "SELECT
				RCA1.rca_id_registro_curso,
				rtrim( alu_nu_matricula ) AS RA,
				rtrim( pes_nm_pessoa ) AS Nome,
				rtrim( atc_cd_atividade ) AS CdDisciplina,
				rtrim( atc_nm_atividade ) AS Atividade,
				atc1.atc_qt_horas AS Horas,
				rtrim( rst1.rst_ds_resultado ) AS Resultado,
				rtrim( PEL1.pel_ds_compacta ) AS PelCursada,
				hse1.hse_nu_faltas AS Faltas ,
				atc_id_unidade_responsavel,
				atc_qt_horas,
				atc_id_atividade,
				atc_cd_atividade,
				atc_nm_atividade
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
				rca1.rca_id_curso = ( SELECT crs_id_curso FROM academico..CRS_curso WHERE crs_id_curso = '$id_curso') 
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

	exit;				
	$res = mssql_query($sql);

	$array = null;
	if(mssql_num_rows($res) > 0){
	 	while($row = mssql_fetch_assoc($res)){
	 		$aux = null;

			if($row['atc_id_unidade_responsavel'] == 1000000080){

	 			$atc_id_atividade = $row['atc_id_atividade'];
	 			$sql2 = "SELECT
							atc_qt_horas, atc_cd_atividade, atc_nm_atividade
						FROM
							academico..EQV_equivalencia
							INNER JOIN academico..GCR_grade_curricular ON gcr_id_grade_curricular = eqv_id_grade_curricular
							INNER JOIN academico..ATC_atividade_curricular ON gcr_id_atividade = atc_id_atividade
							INNER JOIN academico..CRR_curriculo ON crr_id_curriculo = gcr_id_curriculo 
						WHERE
							eqv_id_atividade_equivalente = $atc_id_atividade 
							AND crr_id_curso = $id_curso";

				$sql2 = "SELECT
							rcr_id_curriculo 
						FROM
							academico..PES_pessoa,
							academico..RCR_registro_curriculo,
							academico..CRS_curso,
							academico..RCA_registro_curso_aluno,
							academico..ALU_aluno,
							academico..HIS_historico_ingresso_saida 
						WHERE
							PES_ID_PESSOA = alu_id_pessoa 
							AND alu_id_pessoa = rca_id_aluno 
							AND rca_id_curso = crs_id_curso 
							AND rcr_id_registro_curso = rca_id_registro_curso 
							AND rca_id_registro_curso = his_id_registro_curso 
							AND rcr_st_registro_saida IS NULL 
							AND his_id_forma_saida IS NULL 
							AND pes_id_pessoa = $id_pessoa 
							AND crs_id_curso = $id_curso";

				$res2 = mssql_query($sql2);
				$row2 = mssql_fetch_assoc($res2);
				$rcr_id_curriculo = $row2['rcr_id_curriculo'];

				$sql2 = "SELECT
							atc_qt_horas,
							atc_cd_atividade,
							atc_nm_atividade 
						FROM
							academico..EQV_equivalencia
							INNER JOIN academico..GCR_grade_curricular ON gcr_id_grade_curricular = eqv_id_grade_curricular
							INNER JOIN academico..ATC_atividade_curricular ON gcr_id_atividade = atc_id_atividade
							INNER JOIN academico..CRR_curriculo ON crr_id_curriculo = gcr_id_curriculo 
						WHERE
							eqv_id_atividade_equivalente = $atc_id_atividade 
							AND crr_id_curriculo = $rcr_id_curriculo";

				$res2 = mssql_query($sql2);
				$row2 = mssql_fetch_assoc($res2);
				
				$aux['atc_qt_horas'] 	 = trim($row2['atc_qt_horas']);
				$aux['atc_id_atividade'] = trim($row['atc_id_atividade']);
				$aux['atc_cd_atividade'] = trim($row['atc_cd_atividade']) ."<br><small>". trim($row2['atc_cd_atividade'])."</small>";
				$aux['atc_nm_atividade'] = utf8_encode(trim($row['atc_nm_atividade'])) ."<br><small>". trim($row2['atc_nm_atividade'])."</small>";
				$aux['atc_id_unidade_responsavel'] 	 = trim($row['atc_id_unidade_responsavel']);

				//$_SESSION['ficha_financeira']['carga_horaria_pacote'] += trim($row2['atc_qt_horas']);

	 		} else {

	 			$aux['atc_qt_horas'] 	 = trim($row['atc_qt_horas']);
	 			$aux['atc_id_atividade'] = trim($row['atc_id_atividade']);
				$aux['atc_cd_atividade'] = trim($row['atc_cd_atividade']);
				$aux['atc_nm_atividade'] = utf8_encode(trim($row['atc_nm_atividade']));
				$aux['atc_id_unidade_responsavel'] 	 = trim($row['atc_id_unidade_responsavel']);

				//$_SESSION['ficha_financeira']['carga_horaria_pacote'] += trim($row['atc_qt_horas']);

	 		}

	 		
	 		$array[] = $aux;
	 	}
	 }

	echo json_encode($array);
?>
