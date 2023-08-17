<?php session_start();
	require_once("../../../../php/sqlsrv.php");

	$nome = utf8_decode($_GET['q']);
	$id_periodo_letivo = $_SESSION['ficha_financeira']['id_periodo_letivo'];

	$id_faculdade = $_SESSION['coopex']['usuario']['id_faculdade'];

	

	$sql = "SELECT DISTINCT
				atc_cd_atividade,
				atc_id_atividade AS id,
				atc.atc_nm_atividade,
				atc_qt_horas,
				COALESCE(crs_id_curso, 1000000080) AS crs_id_curso,
				COALESCE(crs_nm_curso, 'EAD') AS crs_nm_curso,
				atc_id_unidade_responsavel
			FROM
				academico..ATC_atividade_curricular atc
				LEFT JOIN academico..CLA_classe ON cla_id_atividade_curricular = atc_id_atividade
				LEFT JOIN academico..GCR_grade_curricular ON gcr_id_atividade = atc_id_atividade
				LEFT JOIN academico..CRR_curriculo ON crr_id_curriculo = gcr_id_curriculo
				LEFT JOIN academico..CRS_curso ON crr_id_curso = crs_id_curso
				LEFT JOIN academico..EQV_equivalencia ON eqv_id_grade_curricular = gcr_id_grade_curricular
			WHERE
				(
					(
						atc_id_unidade_responsavel <> 1000000080
						AND cla_id_periodo_letivo = $id_periodo_letivo
						AND atc_id_atividade = cla_id_atividade_curricular
					)
					OR atc_id_unidade_responsavel = 1000000080
					OR atc_id_atividade IN (
						SELECT
							atc_id_atividade
						FROM
							academico..ATC_atividade_curricular,
							academico..CLA_classe
						WHERE
							atc_id_atividade = cla_id_atividade_curricular
							AND atc_id_unidade_responsavel = 1000000080
							AND cla_id_periodo_letivo = $id_periodo_letivo
					)
				)
				AND (
					atc_nm_atividade LIKE '%$nome%'
					OR atc_cd_atividade LIKE '%$nome%'
				)
			ORDER BY
				atc_nm_atividade";
	$res = mssql_query($sql);

	//exit;
	
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
	
	$anterior = "";
	if(mssql_num_rows($res) > 0){
		while($row = mssql_fetch_assoc($res)){
			if($row['atc_id_unidade_responsavel'] == 1000000080){
				if($anterior <> $row['atc_cd_atividade']){
					$row['curso'] = $row['atc_id_unidade_responsavel'] == 1000000080 ? "EAD" : $row['crs_nm_curso'];
					$row['text'] = $row['atc_cd_atividade']." - ".$row['atc_nm_atividade'];
					$result[] = array_map("utf8_encode", $row);
				}
			} else {
				$row['curso'] = $row['atc_id_unidade_responsavel'] == 1000000080 ? "EAD" : $row['crs_nm_curso'];
				$row['text'] = $row['atc_cd_atividade']." - ".$row['atc_nm_atividade'];
				$result[] = array_map("utf8_encode", $row);
			}

			
			$anterior = $row['atc_cd_atividade'];
		}
	}
	
	if(mssql_num_rows($res)){
		$json .= json_encode($result)."}";
	}
	echo $json;
?>