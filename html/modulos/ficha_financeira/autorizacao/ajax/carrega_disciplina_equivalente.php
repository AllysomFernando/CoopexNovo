<?php session_start();
	require_once("../../../../php/sqlsrv.php");

	$nome = $_GET['q'];
	$id_periodo_letivo = $_SESSION['ficha_financeira']['id_periodo_letivo'];
	$id_curso = $_SESSION['ficha_financeira']['id_curso'];

	$sql = "SELECT DISTINCT
				atc_cd_atividade,
				CONCAT ( atc_id_atividade, ':', atc_qt_horas, ':', atc_id_unidade_responsavel ) AS id,
				atc_nm_atividade,
				atc_qt_horas,
				crs_id_curso,
				crs_nm_curso 
			FROM
				academico..CLA_classe,
				academico..ATC_atividade_curricular,
				academico..EQV_equivalencia,
				academico..GCR_grade_curricular,
				academico..CRR_curriculo,
				academico..CRS_curso 
			WHERE
				cla_id_atividade_curricular = atc_id_atividade 
				AND atc_id_atividade = gcr_id_atividade 
				AND gcr_id_curriculo = crr_id_curriculo 
				AND crr_id_curso = crs_id_curso 
				AND atc_id_unidade_responsavel <> 1000000080 
				AND atc_id_atividade = cla_id_atividade_curricular 
				AND crs_id_curso = $id_curso
				AND ( atc_nm_atividade LIKE '%$nome%' OR atc_cd_atividade LIKE '%$nome%' ) UNION
			SELECT DISTINCT
				atc_cd_atividade,
				CONCAT ( atc_id_atividade, ':', atc_qt_horas, ':', atc_id_unidade_responsavel ) AS id,
				atc.atc_nm_atividade,
				atc_qt_horas,
				1000000080 AS crs_id_curso,
				'EAD' AS crs_nm_curso 
			FROM
				academico..ATC_atividade_curricular atc 
			WHERE
				atc_id_unidade_responsavel = 1000000080 
				AND ( atc_nm_atividade LIKE '%$nome%' OR atc_cd_atividade LIKE '%$nome%' ) 
				AND atc_cd_atividade IN ( SELECT atc_cd_atividade FROM academico..ATC_atividade_curricular WHERE atc_id_unidade_responsavel = 1000000080 ) UNION
			SELECT
				atc_cd_atividade,
				CONCAT ( atc_id_atividade, ':', atc_qt_horas, ':', atc_id_unidade_responsavel ) AS id,
				atc_nm_atividade,
				atc_qt_horas,
				crs_id_curso,
				crs_nm_curso 
			FROM
				academico..EQV_equivalencia
				INNER JOIN academico..GCR_grade_curricular ON gcr_id_grade_curricular = eqv_id_grade_curricular
				INNER JOIN academico..ATC_atividade_curricular ON gcr_id_atividade = atc_id_atividade
				INNER JOIN academico..CRR_curriculo ON crr_id_curriculo = gcr_id_curriculo
				INNER JOIN academico..CRS_curso ON crr_id_curso = crs_id_curso 
			WHERE
				eqv_id_atividade_equivalente IN ( SELECT atc_id_atividade FROM academico..ATC_atividade_curricular, academico..CLA_CLASSE WHERE atc_id_atividade = cla_id_atividade_curricular AND atc_id_unidade_responsavel = 1000000080  ) 
				AND ( atc_nm_atividade LIKE '%$nome%' OR atc_cd_atividade LIKE '%$nome%' ) AND crs_id_curso = $id_curso
			ORDER BY
				atc_cd_atividade";			

	$res = mssql_query($sql);
	
	$json = '{
	  "total_count": '.mssql_num_rows($res);
	
	if(mssql_num_rows($res)){
		$json .= ',
		  "incomplete_results": false,
		  "items":';
	} else {
		$json .= ',
		  "incomplete_results": false}';
	}
	
	if(mssql_num_rows($res) > 0){
		while($row = mssql_fetch_assoc($res)){
			$row['curso'] = $row['crs_nm_curso'];
			$row['text'] = $row['atc_cd_atividade']." - ".$row['atc_nm_atividade'];
			$result[] = array_map("utf8_encode", $row);
		}
	}
	
	if(mssql_num_rows($res)){
		$json .= json_encode($result)."}";
	}
	echo $json;
?>