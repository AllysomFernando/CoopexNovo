<?php
	require_once("../../../../php/sqlsrv.php");

	$nome = $_GET['q'];

	$sql = "SELECT
				atc_cd_atividade as id,
				atc_cd_atividade,
				atc_nm_atividade,
				atc_qt_horas,
				crs_nm_curso,
				crs_id_curso
			FROM
				academico..ATC_atividade_curricular
				INNER JOIN academico..GCR_grade_curricular ON gcr_id_atividade = atc_id_atividade
				INNER JOIN academico..CRR_curriculo ON gcr_id_curriculo = crr_id_curriculo
				INNER JOIN academico..CRS_curso ON crr_id_curso = crs_id_curso
				WHERE
					( atc_nm_atividade LIKE '%$nome%' OR atc_cd_atividade LIKE '%$nome%' ) 
					AND crs_id_curso_inep IS NOT NULL 
				ORDER BY
					atc_nm_atividade";	
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
			$aux = explode(" - ", $row['crs_nm_curso']);
			//echo count($aux);
			if(count($aux)){
				if(isset($aux[1])){
					$row['curso'] = $aux[1];
				}
			}

			$row['text'] = $row['atc_cd_atividade']." - ".$row['atc_nm_atividade'];
			$result[] = array_map("utf8_encode", $row);
		}
	}
	
	if(mssql_num_rows($res)){
		$json .= json_encode($result)."}";
	}
	echo $json;
?>