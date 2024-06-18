<?php session_start();
	require_once("../../../../php/sqlsrv.php");
	
	$id_grade = $_GET['id_grade'];
	$id_grade = substr($id_grade, 0,4);

	$sql = "SELECT top
				1 pel_id_periodo_letivo,
				pel_ds_historico 
			FROM
				academico..PEL_periodo_letivo 
			WHERE
				pel_ch_periodo_vigente = 'S' 
				AND pel_ch_periodo_web = 'S' 
				AND pel_ch_periodo_ferias = 'N'
				and pel_id_periodicidade = 1000000001
			ORDER BY
				pel_ds_historico DESC";	

	$sql = "SELECT
				TOP 4 pel_id_periodo_letivo,
				pel_ds_historico
			FROM
				academico..PEL_periodo_letivo
			WHERE
				pel_id_periodo_letivo = 5000000197 or pel_id_periodo_letivo = 5000000198 or pel_id_periodo_letivo = 5000000203
			ORDER BY
				pel_ds_historico asc";	
				
							
	$res = mssql_query($sql);

	$array = null;
	if(mssql_num_rows($res) > 0){
	 	while($row = mssql_fetch_assoc($res)){
	 		$aux = null;
	 		$aux['id_periodo_letivo'] 	= $row['pel_id_periodo_letivo'];
			$aux['periodo_letivo'] 		= $row['pel_ds_historico'];
	 		$array[] = $aux;
	 	}
	 }

	echo json_encode($array);
?>					