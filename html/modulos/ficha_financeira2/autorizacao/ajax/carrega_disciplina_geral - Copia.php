<?php session_start();
	require_once("../../../../php/sqlsrv.php");

	$nome = $_GET['q'];
	$id_periodo_letivo = $_SESSION['ficha_financeira']['id_periodo_letivo'];

	$sql = "SELECT
				atc_id_atividade as id,
				atc_cd_atividade,
				atc_nm_atividade,
				atc_qt_horas,
				crs_nm_curso,
				crs_id_curso 
			FROM
				academico..CPA_classes_pacote
				INNER JOIN academico..pac_pacote ON pac_id_pacote = cpa_id_pacote
				INNER JOIN academico..PEL_periodo_letivo ON pel_id_periodo_letivo = pac_id_periodo_letivo
				INNER JOIN academico..CRS_curso ON crs_id_curso = pac_id_curso
				INNER JOIN academico..CLA_CLASSE ON cla_id_classe = cpa_id_classe
				INNER JOIN academico..ATC_atividade_curricular ON atc_id_atividade = cla_id_atividade_curricular 
			WHERE
				cpa_id_pacote IN ( SELECT pac_id_pacote FROM academico..pac_pacote WHERE pac_id_periodo_letivo = $id_periodo_letivo ) 
				AND ( atc_nm_atividade LIKE '%$nome%' OR atc_cd_atividade LIKE '%$nome%' ) 
			ORDER BY
				cpa_id_pacote";	
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