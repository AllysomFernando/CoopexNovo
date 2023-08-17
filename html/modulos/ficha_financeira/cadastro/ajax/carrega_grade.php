<?php session_start();
	require_once("../../../../php/sqlsrv.php");

	$id_curso = $_GET['id_curso'];

	$sql = "SELECT TOP
				1 crr_id_curriculo,
				pel_ds_compacta 
			FROM
				academico..CRR_curriculo
				INNER JOIN academico..CRS_curso ON crs_id_curso = crr_id_curso
				INNER JOIN academico..PEL_periodo_letivo ON crr_id_periodo_letivo_inicio = pel_id_periodo_letivo 
			WHERE
				crs_id_curso = $id_curso 
			ORDER BY
				pel_ds_compacta DESC";	
	$res = mssql_query($sql);

	$array = null;
	if(mssql_num_rows($res) > 0){
	 	while($row = mssql_fetch_assoc($res)){
	 		$aux = null;
	 		$aux['id_curriculo'] = $row['crr_id_curriculo'];
			$aux['grade'] = $row['pel_ds_compacta'];
	 		$array[] = $aux;
	 	}
	 }

	echo json_encode($array);
?>					