<?php session_start();
	require_once("../../../../php/sqlsrv.php");
	
	$id_curso = $_GET['id_curso'];

	$sql = "SELECT
				atc_id_atividade,
				atc_cd_atividade,
				atc_nm_atividade,
				atc_qt_horas 
			FROM
				academico..ATC_atividade_curricular
				INNER JOIN academico..GCR_grade_curricular ON gcr_id_atividade = atc_id_atividade
				INNER JOIN academico..CRR_curriculo ON gcr_id_curriculo = crr_id_curriculo
				INNER JOIN academico..CRS_curso ON crr_id_curso = crs_id_curso 
			WHERE
				crs_id_curso IN ($id_curso)";	
	$res = mssql_query($sql);

	$array = null;
	if(mssql_num_rows($res) > 0){
	 	while($row = mssql_fetch_assoc($res)){
	 		$aux = null;
	 		$aux['atc_id_atividade'] = $row['atc_id_atividade'];
			$aux['atc_cd_atividade'] = $row['atc_cd_atividade'];
			$aux['atc_qt_horas'] 	 = $row['atc_qt_horas'];
	 		$aux['atc_nm_atividade'] = $row['atc_cd_atividade']." - ".trim(utf8_encode($row['atc_nm_atividade']));
	 		$array[] = $aux;
	 	}
	 }

	echo json_encode($array);
?>					