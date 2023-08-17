<pre>
<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");

	/*$sql = "SELECT
				*
			FROM
				medicina.semestre";
	$res = $coopex->query($sql);
	$semestre = $res->fetch(PDO::FETCH_OBJ);

	$data_inicio = $semestre->data_inicio;
	$data_fim 	 = $semestre->data_fim;*/
	
	$sql = "SELECT
				*
			FROM
				medicina.horario";
	$res = $coopex->query($sql);

	while($row = $res->fetch(PDO::FETCH_OBJ)){

		echo "<p>Horário: $row->id_horario</p>";
		echo "<p>---------------</p>";

		$sql = "SELECT
					*
				FROM
					medicina.horario_dia
				WHERE
					id_horario = $row->id_horario";
		$res2 = $coopex->query($sql);


		while($row2 = $res2->fetch(PDO::FETCH_OBJ)){

			echo "<p>     Horário Dia: $row2->id_horario_dia</p>";
			echo "<p>-------------------------</p>";
			
			$sql = "SELECT
						*
					FROM
						medicina.grupo_periodo
					WHERE
						id_periodo = $row->id_periodo";
			$res3 = $coopex->query($sql);

			while($row3 = $res3->fetch(PDO::FETCH_OBJ)){

				echo "<p>          Grupo Período: $row3->id_periodo</p>";
				echo "<p>-----------------------------------</p>";
				
				$sql = "SELECT
						*
					FROM
						medicina.grupo
					WHERE
						id_grupo_periodo = $row3->id_grupo_periodo";
				$res4 = $coopex->query($sql);

				while($row4 = $res4->fetch(PDO::FETCH_OBJ)){
					
					if($row->id_grupo_aluno == 1){
						//echo "Truma Inteira";
						$limit = 1;
					} else if($row->id_grupo_aluno == 2){
						//echo "Meia Turma";
						$limit = 2;
					} else if($row->id_grupo_aluno == 3){
						//echo "Grupo Inteiro";
						$limit = 1;
					} else if($row->id_grupo_aluno == 4){
						//echo "Meio Grupo";
						$limit = 2;
					} else if($row->id_grupo_aluno == 5){
						//echo "Número Específico";
						$limit = $row4->alunos_grupo / $row->qtd_alunos;
					}

					$sql = "SELECT
								id_horario_data
							FROM
								medicina.cronograma
							WHERE
								id_grupo = $row4->id_grupo
							AND id_horario = $row->id_horario";
					$res5 = $coopex->query($sql);

					if(!$res5->rowCount()){

						$sql = "SELECT
									*
								FROM
									medicina.horario_data
								WHERE
									id_horario_data NOT IN (
										SELECT
											id_horario_data
										FROM
											medicina.cronograma
									)
								AND id_horario = $row->id_horario	
								ORDER BY
									data_disponivel
								LIMIT $limit";


						$res5 = $coopex->query($sql);

						$sub_grupo = 1;
						while($row5 = $res5->fetch(PDO::FETCH_OBJ)){
							
							$sql = "INSERT INTO medicina.cronograma (id_horario, id_horario_data, id_grupo, sub_grupo)
									VALUES ('$row->id_horario', '$row5->id_horario_data', '$row4->id_grupo', '$sub_grupo')";
							$coopex->query($sql);		
							$sub_grupo++;		

						}
					}

				}

			}

		}

	}

	
?>
