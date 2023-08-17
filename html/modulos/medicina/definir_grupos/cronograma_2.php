<pre>
<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);


	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");


	$sql = "DELETE FROM medicina.cronograma";
	$res = $coopex->query($sql);


	$sql = "SELECT
				*
			FROM
				medicina.horario";
	$res_horario = $coopex->query($sql);

	$limite_grupo = 0;
	$mais_grupos = 0;
	while($horario = $res_horario->fetch(PDO::FETCH_OBJ)){

		$sql = "SELECT
					*
				FROM
					medicina.horario_data
				WHERE
					id_horario = $horario->id_horario
				ORDER BY
					data_disponivel";
		$res_horario_data = $coopex->query($sql);
	
		while($horario_data = $res_horario_data->fetch(PDO::FETCH_OBJ)){

			//echo "<br>";

			$sql = "SELECT
					*
					FROM
						medicina.cronograma
					INNER JOIN medicina.horario_data a USING (id_horario_data)
					WHERE
						 data_disponivel = '$horario_data->data_disponivel'
					AND a.id_horario = $horario->id_horario";
			$res_cronograma = $coopex->query($sql);	



			//echo ">$horario->id_grupo_aluno<";

			if($horario->id_grupo_aluno == 1){

				$limite_grupo = 0;

				$sql = "SELECT
							*
						FROM
							medicina.sub_grupo
						WHERE
							id_horario = $horario->id_horario";
				$res_sub_grupo = $coopex->query($sql);
				
				while($sub_grupo = $res_sub_grupo->fetch(PDO::FETCH_OBJ)){

						//echo "<br>subgrupo nao alocado nesta atividade<br>";

						$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
									VALUES ($horario_data->id_horario_data, $sub_grupo->id_sub_grupo)";
						$coopex->query($sql);
						break;
					 		
				}

			} else if($horario->id_grupo_aluno == 6){

				$sql = "SELECT
							*
						FROM
							medicina.sub_grupo
						WHERE
							id_horario = $horario->id_horario";
				$res_sub_grupo = $coopex->query($sql);
				$total_de_grupos = $res_sub_grupo->rowCount();

				$sql = "SELECT
							*
						FROM
							medicina.sub_grupo
						WHERE
							id_horario = $horario->id_horario
							LIMIT $limite_grupo, $horario->qtd_alunos";
				$res_sub_grupo = $coopex->query($sql);
				
				$i = 0;
				while($row_sub_grupo = $res_sub_grupo->fetch(PDO::FETCH_OBJ)){
					$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
								VALUES ($horario_data->id_horario_data, $row_sub_grupo->id_sub_grupo)";
					$coopex->query($sql);
				}
				$limite_grupo += $horario->qtd_alunos;

				if($limite_grupo > $total_de_grupos){
					$limite_grupo = 0;
				}

				//break;

			} else {

				$limite_grupo = 0;

				if(!$res_cronograma->rowCount()){
					//echo "<br>$horario_data->data_disponivel - data disponível para o horário - $horario->id_horario<br>";

					//$row_cronograma = $res_horario_data->fetch(PDO::FETCH_OBJ);

					//GRUPOS COM TODOS OS ALUNOS
					$sql = "SELECT
								*
							FROM
								medicina.sub_grupo
							INNER JOIN medicina.horario USING (id_horario)
							WHERE
								alunos <> qtd_alunos
							AND id_horario = $horario->id_horario";

					$res_sub_grupo = $coopex->query($sql);
					
					while($sub_grupo = $res_sub_grupo->fetch(PDO::FETCH_OBJ)){
						$sql = "SELECT
									*
								FROM
									medicina.cronograma
								INNER JOIN medicina.horario_data a USING (id_horario_data)
								INNER JOIN medicina.sub_grupo USING (id_sub_grupo)
								WHERE
									nome = '$sub_grupo->nome'
								AND data_disponivel = '$horario_data->data_disponivel'";

						$res_cronograma = $coopex->query($sql);

						//SE O SUBGRUPO NÃO ESTIVER ALOCADO NESTA DATA
						if(!$res_cronograma->rowCount()){

							$sql = "SELECT
									*
								FROM
									medicina.cronograma
								INNER JOIN medicina.horario_data a USING (id_horario_data)
								INNER JOIN medicina.sub_grupo USING (id_sub_grupo)
								WHERE
									id_sub_grupo = $sub_grupo->id_sub_grupo
								AND a.id_horario = $horario->id_horario";

							$res = $coopex->query($sql);


							

								
							if(!$res->rowCount()){
								
								//echo "<br>$sub_grupo->alunos - $horario->qtd_alunos<br>";

								if($sub_grupo->alunos != $horario->qtd_alunos){

									//echo "<br>$horario_data->data_disponivel<br>";

									$arr['id_horario'] = $horario->id_horario;
									$arr['id_sub_grupo'] = $sub_grupo->id_sub_grupo;
									$arr['data_disponivel'] = $horario_data->data_disponivel;

									$parcial[] = $arr;


								} else {

									$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
												VALUES ($horario_data->id_horario_data, $sub_grupo->id_sub_grupo)";
									$coopex->query($sql);
								}
								break;
							}
						} 
					}
				}
			}
		}
	}


	print_r($parcial);


	$sql = "SELECT
				*
			FROM
				medicina.horario";
	$res_horario = $coopex->query($sql);

	$limite_grupo = 0;
	$mais_grupos = 0;
	while($horario = $res_horario->fetch(PDO::FETCH_OBJ)){

		$sql = "SELECT
					*
				FROM
					medicina.horario_data
				WHERE
					id_horario = $horario->id_horario
				ORDER BY
					data_disponivel";
		$res_horario_data = $coopex->query($sql);
	
		while($horario_data = $res_horario_data->fetch(PDO::FETCH_OBJ)){

			//echo "<br>";

			$sql = "SELECT
					*
					FROM
						medicina.cronograma
					INNER JOIN medicina.horario_data a USING (id_horario_data)
					WHERE
						 data_disponivel = '$horario_data->data_disponivel'
					AND a.id_horario = $horario->id_horario";
			$res_cronograma = $coopex->query($sql);	



			//echo ">$horario->id_grupo_aluno<";

			if($horario->id_grupo_aluno == 1){

				$limite_grupo = 0;

				$sql = "SELECT
							*
						FROM
							medicina.sub_grupo
						WHERE
							id_horario = $horario->id_horario";
				$res_sub_grupo = $coopex->query($sql);
				
				while($sub_grupo = $res_sub_grupo->fetch(PDO::FETCH_OBJ)){

						//echo "<br>subgrupo nao alocado nesta atividade<br>";

						$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
									VALUES ($horario_data->id_horario_data, $sub_grupo->id_sub_grupo)";
						$coopex->query($sql);
						break;
					 		
				}

			} else if($horario->id_grupo_aluno == 6){

				$sql = "SELECT
							*
						FROM
							medicina.sub_grupo
						WHERE
							id_horario = $horario->id_horario";
				$res_sub_grupo = $coopex->query($sql);
				$total_de_grupos = $res_sub_grupo->rowCount();

				$sql = "SELECT
							*
						FROM
							medicina.sub_grupo
						WHERE
							id_horario = $horario->id_horario
							LIMIT $limite_grupo, $horario->qtd_alunos";
				$res_sub_grupo = $coopex->query($sql);
				
				$i = 0;
				while($row_sub_grupo = $res_sub_grupo->fetch(PDO::FETCH_OBJ)){
					$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
								VALUES ($horario_data->id_horario_data, $row_sub_grupo->id_sub_grupo)";
					$coopex->query($sql);
				}
				$limite_grupo += $horario->qtd_alunos;

				if($limite_grupo > $total_de_grupos){
					$limite_grupo = 0;
				}

				//break;

			} else {

				$limite_grupo = 0;

				if(!$res_cronograma->rowCount()){
					//echo "<br>$horario_data->data_disponivel - data disponível para o horário - $horario->id_horario<br>";

					//$row_cronograma = $res_horario_data->fetch(PDO::FETCH_OBJ);

					//GRUPOS COM TODOS OS ALUNOS
					$sql = "SELECT
								*
							FROM
								medicina.sub_grupo
							INNER JOIN medicina.horario USING (id_horario)	
							WHERE
								id_horario = $horario->id_horario
								AND alunos <> qtd_alunos";

					$res_sub_grupo = $coopex->query($sql);
					
					while($sub_grupo = $res_sub_grupo->fetch(PDO::FETCH_OBJ)){
						$sql = "SELECT
									*
								FROM
									medicina.cronograma
								INNER JOIN medicina.horario_data a USING (id_horario_data)
								INNER JOIN medicina.sub_grupo USING (id_sub_grupo)
								WHERE
									nome = '$sub_grupo->nome'
								AND data_disponivel = '$horario_data->data_disponivel'";

						$res_cronograma = $coopex->query($sql);

						//SE O SUBGRUPO NÃO ESTIVER ALOCADO NESTA DATA
						if(!$res_cronograma->rowCount()){

							$sql = "SELECT
									*
								FROM
									medicina.cronograma
								INNER JOIN medicina.horario_data a USING (id_horario_data)
								INNER JOIN medicina.sub_grupo USING (id_sub_grupo)
								WHERE
									id_sub_grupo = $sub_grupo->id_sub_grupo
								AND a.id_horario = $horario->id_horario";

							$res = $coopex->query($sql);
						

								
							if(!$res->rowCount()){
								
								echo "<br>$sub_grupo->alunos - $horario->qtd_alunos<br>";

								//for($i=0; $i<$horario->qtd_alunos; $i++){
							
									/*$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
												VALUES ($horario_data->id_horario_data, $sub_grupo->id_sub_grupo)";
									$coopex->query($sql);*/
								//}
								break;
								
							}
						} 
					}
				}
			}
		}
	}
?>
