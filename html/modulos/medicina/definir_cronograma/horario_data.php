<pre>
<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);


	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");


	$sql_del = "DELETE FROM medicina.horario_data";
	$coopex->query($sql_del);


	$sql = "SELECT
				*
			FROM
				medicina.semestre
			WHERE
				id_semestre = 7";
	$res = $coopex->query($sql);
	$semestre = $res->fetch(PDO::FETCH_OBJ);

	$data_inicio = $semestre->data_inicio;
	$data_fim 	 = $semestre->data_fim;
	


	$sql = "SELECT
				*
			FROM
				medicina.horario where excluido = 0";
	$res = $coopex->query($sql);
	
	while($row = $res->fetch(PDO::FETCH_OBJ)){

		echo $sql = "SELECT
					*
				FROM
					medicina.horario_dia
				WHERE
					id_horario = $row->id_horario";
		$res2 = $coopex->query($sql);

		while($row2 = $res2->fetch(PDO::FETCH_OBJ)){

			print_r($row2);
			
			$data = date_create($data_inicio);

			while($data_fim >= date_format($data,"Y-m-d")){

				//ACRESCENTA UM DIA NA DATA A CADA LAÇO
				
				
				//VERIFICA SE O DIA É CORRESPONDENTE AO DIA DA SEMANA
				if(date('w', strtotime(date_format($data,"Y-m-d"))) == $row2->id_dia){
					$data_banco = date_format($data,"Y-m-d");

					$sql = "SELECT
								*
							FROM
								medicina.feriado
							WHERE
								data_feriado = '$data_banco'";
					$res3 = $coopex->query($sql);

					if(!$res3->rowCount()){
						echo $sql_data = "INSERT INTO medicina.horario_data (`id_horario`, `data_disponivel`, `id_dia`, `horario_inicio`, `horario_termino`)
									 VALUES ($row->id_horario, '$data_banco', $row2->id_dia, '$row2->horario_inicio','$row2->horario_termino')";
						$coopex->query($sql_data);
						echo "<hr>";
					}
				}

				date_add($data, date_interval_create_from_date_string("1 days"));
			}

		}

	}

	
?>
