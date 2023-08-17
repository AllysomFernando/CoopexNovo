<? 
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	function get_aluno($id_pessoa, $id_semestre, $id_curso){
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
					alu_nu_matricula  
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
					--pel_id_periodo_letivo = '$id_semestre' 
					--AND
					pes_id_pessoa = $id_pessoa 
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
		$res = mssql_query($sql);
		$row = mssql_fetch_object($res);

		if($row->rca_ch_matutino == "S"){
			$_SESSION['ficha_financeira']['id_turno'] = 1;
			$_SESSION['ficha_financeira']['turno'] = "Matutino";
		} else if($row->rca_ch_vespertino == "S"){
			$_SESSION['ficha_financeira']['id_turno'] = 2;
			$_SESSION['ficha_financeira']['turno'] = "Integral";
		} else if($row->rca_ch_noturno == "S"){
			$_SESSION['ficha_financeira']['id_turno'] = 3;
			$_SESSION['ficha_financeira']['turno'] = "Noturno";
		}

		$_SESSION['ficha_financeira']['ra'] = $row->alu_nu_matricula;
		$_SESSION['ficha_financeira']['link_de_turma'] = ($row->pac_ds_pacote);

		$_SESSION['ficha_financeira']['whatsapp'] = "(".trim($row->tel_cd_ddd).") ".trim($row->tel_nu_telefone);
		$_SESSION['ficha_financeira']['email'] = $row->pes_ds_email;

		$aux = explode(" ", $row->nome);
		$nome_academico = ucfirst(strtolower($aux[0]));
		$_SESSION['ficha_financeira']['nome_academico'] = $nome_academico;

		include "/var/www/html/php/mysql.php";

		$sql = "SELECT
				valor, valor_mensalidade
			FROM
				ficha_financeira.valor_hora
			WHERE id_departamento = $row->CRS_ID_CURSO
			AND id_turno = ".$_SESSION['ficha_financeira']['id_turno'];

		$periodo = $coopex->query($sql);
		$row = $periodo->fetch(PDO::FETCH_OBJ);

		$_SESSION['ficha_financeira']['valor_hora'] 		= $row->valor;
		$_SESSION['ficha_financeira']['valor_semestre'] 	= $row->valor_mensalidade;

		//return $row;
	}

	function get_turma($id_pessoa, $id_curso, $id_periodo){
		$sql = "SELECT
					pac_id_pacote
				FROM
					registro..PES_pessoa
					INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
					INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_aluno = alu_id_pessoa
					INNER JOIN academico..CRS_curso ON crs_id_curso = rca_id_curso
					INNER JOIN academico..PAC_pacote ON pac_id_turma_curso = rca_id_turma_curso 
				WHERE
					pes_id_pessoa = $id_pessoa 
					AND crs_id_curso = $id_curso 
					AND pac_id_periodo_letivo = $id_periodo";
		$res = mssql_query($sql);
		$row = mssql_fetch_object($res);

		return $row->pac_id_pacote;
	}

	function get_classe($id_semestre, $id_disciplina, $id_pessoa){
		$sql = "SELECT
					cpa_id_classe 
				FROM
					registro..PES_pessoa
					INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
					INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_aluno = alu_id_pessoa
					INNER JOIN academico..CRS_curso ON crs_id_curso = rca_id_curso
					INNER JOIN academico..PAC_pacote ON rca_id_turma_curso = pac_id_turma_curso
					INNER JOIN academico..PEL_periodo_letivo ON pel_id_periodo_letivo = pac_id_periodo_letivo
					INNER JOIN academico..CPA_classes_pacote ON pac_id_pacote = cpa_id_pacote
					INNER JOIN academico..CLA_classe_tabela ON cla_id_classe = cpa_id_classe
					INNER JOIN academico..ATC_atividade_curricular ON atc_id_atividade = cla_id_atividade_curricular
					INNER JOIN academico..TCU_turmas_curso ON tcu_id_turma_curso = rca_id_turma_curso 
				WHERE
					pes_id_pessoa = $id_pessoa 
					AND pel_id_periodo_letivo = $id_semestre 
					AND atc_id_atividade = $id_disciplina";
		$res = mssql_query($sql);
		
		if(mssql_num_rows($res)){
			$retorno = $row = mssql_fetch_object($res);
			$retorno = $row->cpa_id_classe;
		} else {
			$retorno = 0;
		}
		return $retorno;
	}

	function get_horario($id_disciplina, $id_turma, $id_periodo){
		$sql = "SELECT DISTINCT
					atc_id_atividade AS id,
					cla_id_classe,
					atc_cd_atividade,
					atc_nm_atividade,
					atc_qt_horas, 
					crs_id_curso,
					crs_nm_curso,
					cla_ds_classe,
					HRC_HR_INICIO,
					HRC_HR_TERMINO,
					HRC_DS_SALA,
					HRC_NM_DIA_SEMANA,
					HRC_DS_DIA_SEMANA
				FROM
					academico..CLA_classe,
					academico..ATC_atividade_curricular,
					academico..EQV_equivalencia,
					academico..GCR_grade_curricular,
					academico..CRR_curriculo,
					academico..CRS_curso,
					academico..HRC_HORARIO_CLASSE 
				WHERE
					cla_id_atividade_curricular = atc_id_atividade 
					AND atc_id_atividade = gcr_id_atividade 
					AND gcr_id_curriculo = crr_id_curriculo 
					AND crr_id_curso = crs_id_curso 
					AND HRC_ID_CLASSE = cla_id_classe 
					AND cla_id_periodo_letivo = $id_periodo 
					AND atc_id_atividade = cla_id_atividade_curricular 
					AND atc_id_atividade = $id_disciplina 
					AND cla_id_classe = $id_turma UNION
				SELECT
					atc_id_atividade AS id,
					cla_id_classe,
					atc_cd_atividade,
					atc_nm_atividade,
					atc_qt_horas,
					crs_id_curso,
					crs_nm_curso,
					cla_ds_classe,
					HRC_HR_INICIO,
					HRC_HR_TERMINO,
					HRC_DS_SALA,
					HRC_NM_DIA_SEMANA,
					HRC_DS_DIA_SEMANA 
				FROM
					academico..EQV_equivalencia
					INNER JOIN academico..GCR_grade_curricular ON gcr_id_grade_curricular = eqv_id_grade_curricular
					INNER JOIN academico..ATC_atividade_curricular ON gcr_id_atividade = atc_id_atividade
					INNER JOIN academico..CRR_curriculo ON crr_id_curriculo = gcr_id_curriculo
					INNER JOIN academico..CRS_curso ON crr_id_curso = crs_id_curso
					INNER JOIN academico..CLA_CLASSE ON cla_id_atividade_curricular = atc_id_atividade
					INNER JOIN academico..HRC_HORARIO_CLASSE ON HRC_ID_CLASSE = cla_id_classe 
				WHERE
					eqv_id_atividade_equivalente IN ( SELECT atc_id_atividade FROM academico..ATC_atividade_curricular, academico..CLA_CLASSE WHERE atc_id_atividade = cla_id_atividade_curricular AND atc_id_unidade_responsavel = 1000000080 AND cla_id_periodo_letivo = $id_periodo ) 
					AND atc_id_atividade = $id_disciplina 
					AND cla_id_classe = $id_turma";
		$res = mssql_query($sql);
		
		if(mssql_num_rows($res)){
			while ($row = mssql_fetch_object($res)) {
				$row->HRC_DS_DIA_SEMANA = trim($row->HRC_DS_DIA_SEMANA);
				$row->atc_nm_atividade = trim(utf8_encode(($row->atc_nm_atividade)));
				$aux[] = $row;
			}
		} else {
			$aux = null;
		}
		return $aux;
	}

?>