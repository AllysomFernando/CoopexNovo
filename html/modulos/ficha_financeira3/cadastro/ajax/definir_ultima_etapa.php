<?php
	require_once("../../../../php/config.php");
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");


	$sql = "SELECT
				* 
			FROM
				ficha_financeira.ficha_financeira_etapa 
			GROUP BY
				id_ficha_financeira 
			ORDER BY
				id_etapa DESC";
	$res = $coopex->query($sql);
	while($row = $res->fetch(PDO::FETCH_OBJ)){
echo $row->id_etapa;
	}

	
?>