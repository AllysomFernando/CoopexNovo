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
				//echo " grupos <br>";

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

					$sql = "SELECT
								*
							FROM
								medicina.sub_grupo
							INNER JOIN medicina.horario USING (id_horario)	
							WHERE
								id_horario = $horario->id_horario ORDER BY rand()";

					//ORDER BY rand() - Ordem aleatório de grupos

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


						if(!$res_cronograma->rowCount()){
							//echo "<br>subgrupo nao alocado nesta data<br>";

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
								//echo "<br>subgrupo nao alocado nesta atividade<br>";

								//echo "<p>$sub_grupo->alunos / $horario->qtd_alunos - $horario->id_horario </p>";

								if($sub_grupo->alunos != $horario->qtd_alunos){
									//print_r($horario);
									$limite_parcial = $horario->qtd_alunos ? $horario->qtd_alunos / $sub_grupo->alunos : 0;
									$sql = "SELECT
												*
											FROM
												medicina.sub_grupo
											INNER JOIN medicina.horario USING (id_horario)
											WHERE
												id_horario = $horario->id_horario
											AND alunos <> qtd_alunos
											AND id_sub_grupo NOT IN (
												SELECT
													id_sub_grupo
												FROM
													medicina.cronograma
											)  ORDER BY rand()
											LIMIT 0, $limite_parcial";
										$res_parcial = $coopex->query($sql);
										while($row_parcial = $res_parcial->fetch(PDO::FETCH_OBJ)){
											$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
												VALUES ($horario_data->id_horario_data, $row_parcial->id_sub_grupo)";
											$coopex->query($sql);
										}
										break;

									//$parcial[] = $sub_grupo->id_sub_grupo;
									//echo "<br>$sub_grupo->id_sub_grupo<br>";
								} else {

									$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
												VALUES ($horario_data->id_horario_data, $sub_grupo->id_sub_grupo)";
									$coopex->query($sql);
									break;
								}
							}
						} 
					}
				}
			}
		}
	}




	$sql = "SELECT
				*
			FROM
				medicina.horario";
	$res_horario = $coopex->query($sql);

	while($horario = $res_horario->fetch(PDO::FETCH_OBJ)){

		echo $sql = "SELECT
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
				AND id_horario = $horario->id_horario";
		$res_horario_data = $coopex->query($sql);
		echo " total -> ".$total = $res_horario_data->rowCount();

		echo "<hr>";

		echo $sql = "SELECT
						*
					FROM
						medicina.sub_grupo
					INNER JOIN medicina.horario USING (id_horario)
					WHERE
						id_horario = $horario->id_horario
					GROUP BY
						id_grupo
					ORDER BY
						rand()";

							$res_sub_grupo = $coopex->query($sql);

		while($sub_grupo = $res_sub_grupo->fetch(PDO::FETCH_OBJ)){
			if(!$horario_data = $res_horario_data->fetch(PDO::FETCH_OBJ)){
				break;
			}
			

			echo $horario_data->id_horario_data . " - " . $sub_grupo->id_sub_grupo;
			echo "<hr>";

			echo $sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
						VALUES ($horario_data->id_horario_data, $sub_grupo->id_sub_grupo)";
			$coopex->query($sql);

		}
			echo "<hr>";
	}




	$sql = "SELECT
				*
			FROM
				medicina.horario";
	$res_horario = $coopex->query($sql);

	while($horario = $res_horario->fetch(PDO::FETCH_OBJ)){
		echo "aasdfasdfasdfasdf";
		echo $sql = "SELECT
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
				AND id_horario = $horario->id_horario";
		$res_horario_data = $coopex->query($sql);
		echo " total -> ".$total = $res_horario_data->rowCount();

		echo "<hr>";

		echo $sql = "SELECT
						*
					FROM
						medicina.sub_grupo
					INNER JOIN medicina.horario USING (id_horario)
					WHERE
						id_horario = $horario->id_horario
					GROUP BY
						id_grupo
					ORDER BY
						rand()";

		$res_sub_grupo = $coopex->query($sql);

		while($sub_grupo = $res_sub_grupo->fetch(PDO::FETCH_OBJ)){
			if(!$horario_data = $res_horario_data->fetch(PDO::FETCH_OBJ)){
				break;
			}
			

			echo $horario_data->id_horario_data . " - " . $sub_grupo->id_sub_grupo;
			echo "<hr>";

			echo $sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
						VALUES ($horario_data->id_horario_data, $sub_grupo->id_sub_grupo)";
			$coopex->query($sql);

		}
			echo "<hr>";
	}

	$sql = "SELECT
				*
			FROM
				medicina.horario";
	$res_horario = $coopex->query($sql);

	while($horario = $res_horario->fetch(PDO::FETCH_OBJ)){

		echo $sql = "SELECT
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
				AND id_horario = $horario->id_horario";
		$res_horario_data = $coopex->query($sql);
		echo " total -> ".$total = $res_horario_data->rowCount();

		echo "<hr>";

		echo $sql = "SELECT
					*
				FROM
					(
						SELECT
							*, @rn :=
						IF (@GROUP = id_grupo, @rn + 1, 1) AS rn,
						@GROUP := id_grupo
					FROM
						medicina.sub_grupo
					CROSS JOIN (SELECT @rn := 0, @GROUP := NULL) AS vars
					INNER JOIN medicina.grupo USING (id_grupo)
					WHERE
						id_horario = $horario->id_horario
					ORDER BY
						id_grupo,
						RAND()
					) sub

				ORDER BY
					RAND(),
					id_grupo
				LIMIT $total";

		$res_sub_grupo = $coopex->query($sql);

		while($sub_grupo = $res_sub_grupo->fetch(PDO::FETCH_OBJ)){
			$horario_data = $res_horario_data->fetch(PDO::FETCH_OBJ);
			

			echo $horario_data->id_horario_data . " - " . $sub_grupo->id_sub_grupo;
			echo "<hr>";

			echo $sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
						VALUES ($horario_data->id_horario_data, $sub_grupo->id_sub_grupo)";
			$coopex->query($sql);

		}
			echo "<hr>";
	}

	
?>
