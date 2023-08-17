<?php session_start();
	require_once("../../../../php/sqlsrv.php");

	$id_semestre = $_GET['id_semestre'];
	$id_curso = $_GET['id_curso'];

	$sql = "SELECT
				1 AS pac_id_pacote,
				'Personalizado' AS pac_ds_pacote
			UNION
			SELECT
				pac_id_pacote,
				pac_ds_pacote 
			FROM
				academico..PAC_pacote 
			WHERE
				pac_id_periodo_letivo = $id_semestre 
				AND pac_id_curso = $id_curso";	
	$res = mssql_query($sql);

	$array = null;
	if(mssql_num_rows($res) > 0){
	 	while($row = mssql_fetch_assoc($res)){
	 		$aux = null;
	 		$aux['id_pacote'] = $row['pac_id_pacote'];
			$aux['pacote'] = utf8_encode($row['pac_ds_pacote']);
	 		$array[] = $aux;
	 	}
	 }

	echo json_encode($array);
?>					