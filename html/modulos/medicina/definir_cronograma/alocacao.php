<pre>
<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);


	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");

	$sql = "SELECT
				*
			FROM
				medicina.semestre";
	$res = $coopex->query($sql);
	$semestre = $res->fetch(PDO::FETCH_OBJ);

	$data_inicio = $semestre->data_inicio;
	$data_fim 	 = $semestre->data_fim;
	
	$sql = "SELECT
				*
			FROM
				medicina.horario";
	$res = $coopex->query($sql);

	while($row = $res->fetch(PDO::FETCH_OBJ)){

		$sql = "SELECT
					*
				FROM
					medicina.horario_dia
				WHERE
					id_horario = $row->id_horario";
		$res2 = $coopex->query($sql);

		while($row2 = $res2->fetch(PDO::FETCH_OBJ)){
			
			$data = date_create($data_inicio);

			while($data_fim >= date_format($data,"Y-m-d")){

				//ACRESCENTA UM DIA NA DATA A CADA LAÇO
				date_add($data, date_interval_create_from_date_string("1 days"));
				
				//VERIFICA SE O DIA É CORRESPONDENTE AO DIA DA SEMANA
				if(date('w', strtotime(date_format($data,"Y-m-d"))) == $row2->id_dia){
					echo "<br>";
					echo date_format($data,"Y-m-d");
					echo " - ";
					echo date('w', strtotime(date_format($data,"Y-m-d")));
				}
			}

		}

	}

	
?>
