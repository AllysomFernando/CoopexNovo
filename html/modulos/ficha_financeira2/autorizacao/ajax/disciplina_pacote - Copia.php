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
	$id_turma = isset($_GET['id_turma']) ? $_GET['id_turma'] : 0;
	$id_periodo = trim(str_replace("/", "", $id_periodo));
	$id_curso = $_GET['id_curso'];
	$_SESSION['ficha_financeira']['id_curso'] = $id_curso;

	$_SESSION['ficha_financeira']['carga_horaria'] 								= 0;
	$_SESSION['ficha_financeira']['carga_horaria_pacote'] 						= 0;
	$_SESSION['ficha_financeira']['carga_horaria_disciplinas_pacote'] 			= 0;
	$_SESSION['ficha_financeira']['carga_horaria_real_disciplinas_pacote'] 		= 0;
	$_SESSION['ficha_financeira']['carga_horaria_disciplinas_fora_pacote'] 		= 0;
	$_SESSION['ficha_financeira']['carga_horaria_real_disciplinas_fora_pacote'] = 0;
	$_SESSION['ficha_financeira']['desconto_dp'] = 0;

	//unset($_SESSION['ficha_financeira']['disciplinas']);

	if($id_turma){
		$sql = "SELECT TOP
					1 pac_id_pacote, alu_nu_matricula, rca_ch_matutino, rca_ch_vespertino, rca_ch_noturno, pac_ds_pacote 
				FROM
					academico..rca_registro_curso_aluno
					INNER JOIN academico..crs_curso ON crs_id_curso = rca_id_curso
					INNER JOIN academico..OCP_ocorrencia_por_periodo ON ocp_id_registro_curso = rca_id_registro_curso
					INNER JOIN academico..OCA_ocorrencia_academica ON oca_id_ocorrencia = ocp_id_ocorrencia_corrente
					INNER JOIN academico..ALU_aluno ON alu_id_pessoa = rca_id_aluno
					INNER JOIN academico..PEL_periodo_letivo ON pel_id_periodo_letivo = ocp_id_periodo_letivo
					INNER JOIN academico..PAC_pacote ON pac_id_turma_curso = rca_id_turma_curso 
					INNER JOIN academico..HIS_historico_ingresso_saida ON his_id_registro_curso = rca_id_registro_curso
				WHERE
					rca_id_forma_saida IS NULL 
					AND rca_id_turma_curso IS NOT NULL 
					AND oca_ds_ocorrencia = 'MATRICULADO' 
					AND pac_id_pacote = '$id_turma' 
				ORDER BY
					pel_ds_compacta,
					crs_nm_resumido,
					alu_nu_matricula";
			
		$res = mssql_query($sql);
		$row = mssql_fetch_assoc($res);
		$id_pacote = $id_turma;

	} else {

		$sql = "SELECT DISTINCT TOP
					1 pes_id_pessoa AS id,
					pes_nm_pessoa AS nome,
					'ALUNO' AS tipo,
					crs_nm_curso AS curso,
					usr_ds_nickname AS usuario,
					pef_cd_sexo AS sexo,
					CRS_ID_CURSO,
					tel_cd_ddd,
					tel_nu_telefone,
					pes_ds_email,
					pac_ds_pacote,
					rca_ch_noturno,
					rca_ch_matutino,
					rca_ch_vespertino,
					alu_nu_matricula,
					pac_id_pacote  
				FROM
					academico..PEL_periodo_letivo,
					registro..PES_pessoa
					INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
					INNER JOIN academico..usr_usuario ON alu_id_pessoa = usr_id_pessoa
					INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_aluno = pes_id_pessoa
					INNER JOIN academico..CRS_curso ON crs_id_curso = rca_id_curso
					INNER JOIN academico..COL_colegiado ON col_id_colegiado = crs_id_unidade
					INNER JOIN academico..pef_pessoa_fisica ON pef_id_pessoa = pes_id_pessoa
					INNER JOIN registro..TEL_telefone ON tel_id_pessoa = pes_id_pessoa
					INNER JOIN academico..PAC_pacote ON pac_id_turma_curso = rca_id_turma_curso 
				WHERE
					pel_id_periodo_letivo = '$id_semestre' 
					AND pes_id_pessoa = $id_pessoa 
					AND CRS_ID_CURSO = '$id_curso' 
					AND tel_ch_preferencial = 'S' 
					AND EXISTS (
					SELECT
						1 
					FROM
						financeiro..cta_contrato_academico,
						financeiro..ctr_contrato,
						financeiro..CPL_contrato_periodo_letivo 
					WHERE
						cta_id_contrato = ctr_id_contrato 
						AND ctr_id_cliente = rca_id_aluno 
						AND cpl_id_periodo_letivo = pel_id_periodo_letivo 
					AND cpl_id_contrato = cta_id_contrato 
					)
					ORDER BY
						pac_ds_pacote DESC";
		$sql40 = "SELECT TOP
					1 pac_id_pacote, alu_nu_matricula, rca_ch_matutino, rca_ch_vespertino, rca_ch_noturno, pac_ds_pacote 
				FROM
					academico..rca_registro_curso_aluno
					INNER JOIN academico..crs_curso ON crs_id_curso = rca_id_curso
					INNER JOIN academico..OCP_ocorrencia_por_periodo ON ocp_id_registro_curso = rca_id_registro_curso
					INNER JOIN academico..OCA_ocorrencia_academica ON oca_id_ocorrencia = ocp_id_ocorrencia_corrente
					INNER JOIN academico..ALU_aluno ON alu_id_pessoa = rca_id_aluno
					INNER JOIN academico..PEL_periodo_letivo ON pel_id_periodo_letivo = ocp_id_periodo_letivo
					INNER JOIN academico..PAC_pacote ON pac_id_turma_curso = rca_id_turma_curso 
					INNER JOIN academico..HIS_historico_ingresso_saida ON his_id_registro_curso = rca_id_registro_curso
				WHERE
					
					rca_id_turma_curso IS NOT NULL 
					AND oca_ds_ocorrencia = 'MATRICULADO' 
					AND pac_id_periodo_letivo = '$id_semestre' 
					AND alu_id_pessoa = $id_pessoa 
					AND rca_id_curso = $id_curso
				ORDER BY
					pel_ds_compacta,
					crs_nm_resumido,
					alu_nu_matricula";
			
		$res = mssql_query($sql);
		$row = mssql_fetch_assoc($res);
		$id_pacote = $row['pac_id_pacote'];
	}

	//echo $sql;

	$_SESSION['ficha_financeira']['id_turma'] = $id_pacote;

	if($row['rca_ch_matutino'] == "S"){

		$_SESSION['ficha_financeira']['id_turno'] = 1;
		$_SESSION['ficha_financeira']['turno'] = "Matutino";

	} else if($row['rca_ch_vespertino'] == "S"){

		$_SESSION['ficha_financeira']['id_turno'] = 2;
		$_SESSION['ficha_financeira']['turno'] = "Integral";

	} else if($row['rca_ch_noturno'] == "S"){

		$_SESSION['ficha_financeira']['id_turno'] = 3;
		$_SESSION['ficha_financeira']['turno'] = "Noturno";
		
	}

	$_SESSION['ficha_financeira']['ra'] = $row['alu_nu_matricula'];
	//$_SESSION['ficha_financeira']['id_pessoa'] = $row['pes_id_pessoa'];
	$_SESSION['ficha_financeira']['link_de_turma'] = $row['pac_ds_pacote'];


	$sql = "SELECT
				valor, valor_mensalidade
			FROM
				ficha_financeira.valor_hora
			WHERE id_departamento = $id_curso
			AND id_turno = ".$_SESSION['ficha_financeira']['id_turno'];

	$periodo = $coopex->query($sql);
	$row = $periodo->fetch(PDO::FETCH_OBJ);
	$_SESSION['ficha_financeira']['valor_hora'] = $row->valor;

	
	if(isset($_SESSION['ficha_financeira']['id_ficha_financeira'])){
		$sql = "SELECT
					valor_mensalidade 
				FROM
					ficha_financeira.ficha_financeira 
				WHERE
					id_ficha_financeira = ".$_SESSION['ficha_financeira']['id_ficha_financeira'];
		$ficha = $coopex->query($sql);
		$row_ficha = $ficha->fetch(PDO::FETCH_OBJ);

		if($row_ficha->valor_mensalidade){
			$_SESSION['ficha_financeira']['valor_semestre'] = $row_ficha->valor_mensalidade;
		} else {
			$_SESSION['ficha_financeira']['valor_semestre'] = $row->valor_mensalidade;
		}
	} else {
		$_SESSION['ficha_financeira']['valor_semestre'] = $row->valor_mensalidade;
	}



	$sql = "SELECT
				atc_id_atividade,
				atc_cd_atividade,
				atc_nm_atividade,
				atc_qt_horas,
				atc_id_unidade_responsavel
			FROM
				academico..CPA_classes_pacote
				INNER JOIN academico..pac_pacote 				ON pac_id_pacote = cpa_id_pacote
				INNER JOIN academico..PEL_periodo_letivo 		ON pel_id_periodo_letivo = pac_id_periodo_letivo
				INNER JOIN academico..CRS_curso 				ON crs_id_curso = pac_id_curso
				INNER JOIN academico..CLA_CLASSE 				ON cla_id_classe = cpa_id_classe
				INNER JOIN academico..ATC_atividade_curricular 	ON atc_id_atividade = cla_id_atividade_curricular 
			WHERE
				cpa_id_pacote IN ( SELECT pac_id_pacote FROM academico..pac_pacote WHERE pac_id_periodo_letivo = $id_semestre ) 
				AND pac_id_pacote = $id_pacote";	
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
				
				$aux['atc_qt_horas'] 	 			= trim($row2['atc_qt_horas']);
				$aux['atc_id_atividade'] 			= trim($row['atc_id_atividade']);
				$aux['atc_cd_atividade'] 			= trim($row['atc_cd_atividade']) ."<br><small>". trim($row2['atc_cd_atividade'])."</small>";
				$aux['atc_nm_atividade'] 			= utf8_encode(trim($row['atc_nm_atividade'])) ."<br><small>". utf8_encode(trim($row2['atc_nm_atividade']))."</small>";
				$aux['atc_id_unidade_responsavel'] 	= trim($row['atc_id_unidade_responsavel']);
				$_SESSION['ficha_financeira']['carga_horaria_pacote'] += trim($row2['atc_qt_horas']);
				
	 		} else {

	 			$aux['atc_qt_horas'] 	 			= trim($row['atc_qt_horas']);
	 			$aux['atc_id_atividade'] 			= trim($row['atc_id_atividade']);
				$aux['atc_cd_atividade'] 			= trim($row['atc_cd_atividade']);
				$aux['atc_nm_atividade'] 			= utf8_encode(trim($row['atc_nm_atividade']));
				$aux['atc_id_unidade_responsavel'] 	= trim($row['atc_id_unidade_responsavel']);
				$_SESSION['ficha_financeira']['carga_horaria_pacote'] += trim($row['atc_qt_horas']);
	 		}


	 		
	 		$array[] = $aux;
	 	}
	 }

	echo json_encode($array);
?>
