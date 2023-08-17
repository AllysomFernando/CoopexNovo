<pre>
<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");

	//LIMPA A TABELA CRONOGRAMA
	$sql = "DELETE FROM medicina.cronograma";
	$res = $coopex->query($sql);

	function cronograma(){

		$coopex = new PDO("mysql:dbname=coopex;host=10.0.0.41", 'fernando', 'indioveio');

		$sql = "SELECT
					*
				FROM
					medicina.horario where excluido = 0";
		$res_horario = $coopex->query($sql);
		$res_horario->rowCount();
		$limite_grupo = 0;
		$mais_grupos = 0;
		while($horario = $res_horario->fetch(PDO::FETCH_OBJ)){
			//echo "<hr>";
			//echo "<h1>1º</h1>";
			$sql = "SELECT
						*
					FROM
						horario
					INNER JOIN horario_data USING (id_horario)
					WHERE
						id_horario = $horario->id_horario
					AND id_horario_data NOT IN (
						SELECT
							id_horario_data
						FROM
							cronograma
						ORDER BY
							data_disponivel
					)";
			//echo "<hr>";
			$res_horario_data = $coopex->query($sql);

			
			if($horario->id_grupo_aluno == 1){
				//SE A TURMA INTEIRA
				//turma_inteira($horario->id_horario);
			} else if($horario->id_grupo_aluno == 6){
				//SE FOREM 2 GURPOS POR VEZ
				//dois_grupos($horario->id_horario);
			} else if($horario->id_grupo_aluno == 3){
				//SE FOR O GRUPO INTEIRO
				//grupo_inteiro($horario->id_horario);
			} else if($horario->id_grupo_aluno == 4){
				//SE FOR MEIO GRUPO
				//meio_grupo($horario->id_horario);
			} else if($horario->id_grupo_aluno == 2){
				//SE FOR MEIA TURMA
				//meia_turma($horario->id_horario);
			} else if($horario->id_grupo_aluno == 5){
				numero_especifico($horario->id_horario, $horario->qtd_alunos);
			}
		}


	}


	function turma_inteira($id_horario){
		
		$coopex = new PDO("mysql:dbname=coopex;host=10.0.0.41", 'fernando', 'indioveio');

		$sql = "SELECT
					*
				FROM
					medicina.horario_data
				WHERE
					id_horario = $id_horario
				ORDER BY
					rand()";
		$res_horario_data = $coopex->query($sql);	

		$sql = "SELECT
					*
				FROM
					medicina.sub_grupo
				WHERE
					id_horario = $id_horario";
		$res_sub_grupo = $coopex->query($sql);	
		$sub_grupo = $res_sub_grupo->fetch(PDO::FETCH_OBJ);
		
		while($horario_data = $res_horario_data->fetch(PDO::FETCH_OBJ)){
			$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
						VALUES ($horario_data->id_horario_data, $sub_grupo->id_sub_grupo)";
			$coopex->query($sql);
		}

	}

	
	function dois_grupos($id_horario){
		
		$coopex = new PDO("mysql:dbname=coopex;host=10.0.0.41", 'fernando', 'indioveio');

		$sql = "SELECT
					*
				FROM
					medicina.horario_data
				WHERE
					id_horario = $id_horario
				ORDER BY
					rand()";
		$res_horario_data = $coopex->query($sql);	

		$sql = "SELECT
					*
				FROM
					medicina.sub_grupo
				WHERE
					id_horario = $id_horario";
		$res_sub_grupo = $coopex->query($sql);	
		$total_sub_grupo = $res_sub_grupo->rowCount();
		
		while($sub_grupo = $res_sub_grupo->fetch(PDO::FETCH_OBJ)){
			$arr_sub_grupo[] = $sub_grupo;
		}

		shuffle($arr_sub_grupo);

		$i = 0;
		while($horario_data = $res_horario_data->fetch(PDO::FETCH_OBJ)){
			
			$id_sub_grupo = $arr_sub_grupo[$i]->id_sub_grupo;
			if(!verificar_choque($horario_data->data_disponivel,
									$arr_sub_grupo[$i]->nome_grupo,
									$horario_data->horario_inicio,
									$horario_data->horario_termino)){
				$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
							VALUES ($horario_data->id_horario_data, $id_sub_grupo)";
				$coopex->query($sql);
			}
			
			$i++;

			$id_sub_grupo = $arr_sub_grupo[$i]->id_sub_grupo;
			if(!verificar_choque($horario_data->data_disponivel,
									$arr_sub_grupo[$i]->nome_grupo,
									$horario_data->horario_inicio,
									$horario_data->horario_termino)){
				$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
							VALUES ($horario_data->id_horario_data, $id_sub_grupo)";
				$coopex->query($sql);
			}
			$i++;

			if($i == $total_sub_grupo){
				$i = 0;
				shuffle($arr_sub_grupo);
			}
		}

	}


	

	function meia_turma($id_horario){

		$coopex = new PDO("mysql:dbname=coopex;host=10.0.0.41", 'fernando', 'indioveio');

		$sql = "SELECT
					*
				FROM
					medicina.horario_data
				WHERE
					id_horario = $id_horario
				ORDER BY
					rand()";
		$res_horario_data = $coopex->query($sql);	

		$sql = "SELECT
					*
				FROM
					medicina.sub_grupo
				WHERE
					id_horario = $id_horario";
		$res_sub_grupo = $coopex->query($sql);	
		$total_sub_grupo = $res_sub_grupo->rowCount();
		$arr_sub_grupo = [];
		while($sub_grupo = $res_sub_grupo->fetch(PDO::FETCH_OBJ)){
			$arr_sub_grupo[] = $sub_grupo;
		}

		if(count($arr_sub_grupo)){
			shuffle($arr_sub_grupo);
		

			$i = 0;
			while($horario_data = $res_horario_data->fetch(PDO::FETCH_OBJ)){
				
				$id_sub_grupo = $arr_sub_grupo[$i]->id_sub_grupo;
				
				$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
							VALUES ($horario_data->id_horario_data, $id_sub_grupo)";
				$coopex->query($sql);

				$i++;

				if($i == $total_sub_grupo){
					$i = 0;
					shuffle($arr_sub_grupo);
				}
			}
		}

	}


	function grupo_inteiro($id_horario){

		$coopex = new PDO("mysql:dbname=coopex;host=10.0.0.41", 'fernando', 'indioveio');

		$sql = "SELECT
					*
				FROM
					medicina.horario_data
				WHERE
					id_horario = $id_horario
				ORDER BY
					rand()";
		$res_horario_data = $coopex->query($sql);	
		$total_horario_data = $res_horario_data->rowCount();
		while($horario_data = $res_horario_data->fetch(PDO::FETCH_OBJ)){
			$arr_horario_data[] = $horario_data;
		}
		
		$sql = "SELECT
					*
				FROM
					medicina.sub_grupo
				WHERE
					id_horario = $id_horario";
		$res_sub_grupo = $coopex->query($sql);	
		$total_sub_grupo = $res_sub_grupo->rowCount();
		while($sub_grupo = $res_sub_grupo->fetch(PDO::FETCH_OBJ)){
			$arr_sub_grupo[] = $sub_grupo;
		}

		//se o numero de encontros for maior que o número de grupos
		if($total_horario_data > $total_sub_grupo){

			//faz o looping inteiro e faz a alocação
			$inteiro = floor($total_horario_data / $total_sub_grupo);

			$cont_sub_grupo = 0;
			for($i=0; $i<$inteiro*$total_sub_grupo; $i++){

				$id_horario_data = $arr_horario_data[$i]->id_horario_data;
				$id_sub_grupo    = $arr_sub_grupo[$cont_sub_grupo]->id_sub_grupo;

				if(!verificar_choque($arr_horario_data[$i]->data_disponivel,
									$arr_sub_grupo[$cont_sub_grupo]->nome_grupo,
									$arr_horario_data[$i]->horario_inicio,
									$arr_horario_data[$i]->horario_termino)){
					$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
							VALUES ($id_horario_data, $id_sub_grupo)";
					$coopex->query($sql);		
				} else {
					$arr_horario_data_choque[] = $arr_horario_data[$i];
					$arr_sub_grupo_choque[]    = $arr_sub_grupo[$cont_sub_grupo];
				}

				
				if($cont_sub_grupo < $total_sub_grupo-1){
					$cont_sub_grupo++;	
				} else {
					$cont_sub_grupo = 0;
				}

			}

			//embaralha os encontros restantes e faz a alocação
			shuffle($arr_sub_grupo);
			for($i; $i<$total_horario_data; $i++){

				$id_horario_data = $arr_horario_data[$i]->id_horario_data;
				$id_sub_grupo    = $arr_sub_grupo[$cont_sub_grupo]->id_sub_grupo;

				if(!verificar_choque($arr_horario_data[$i]->data_disponivel,
									$arr_sub_grupo[$cont_sub_grupo]->nome_grupo,
									$arr_horario_data[$i]->horario_inicio,
									$arr_horario_data[$i]->horario_termino)){

					$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
								VALUES ($id_horario_data, $id_sub_grupo)";
					$coopex->query($sql);
				} else {

					$arr_horario_data_choque[] = $arr_horario_data[$i];
					$arr_sub_grupo_choque[]    = $arr_sub_grupo[$cont_sub_grupo];
				}

				if($cont_sub_grupo < $total_sub_grupo-1){
					$cont_sub_grupo++;	
				} else {
					$cont_sub_grupo = 0;
				}

			}

		} else {

			for($i=0; $i<$total_horario_data; $i++){

				$id_horario_data = $arr_horario_data[$i]->id_horario_data;
				$id_sub_grupo    = $arr_sub_grupo[$i]->id_sub_grupo;
				
				if(!verificar_choque($arr_horario_data[$i]->data_disponivel,
									$arr_sub_grupo[$i]->nome_grupo,
									$arr_horario_data[$i]->horario_inicio,
									$arr_horario_data[$i]->horario_termino)){
					$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
								VALUES ($id_horario_data, $id_sub_grupo)";
					$coopex->query($sql);
				} else {

					$arr_horario_data_choque[] = $arr_horario_data[$i];
					$arr_sub_grupo_choque[]    = $arr_sub_grupo[$cont_sub_grupo];
				}	

			}

		}

	}

	


	function meio_grupo($id_horario){
		
		$coopex = new PDO("mysql:dbname=coopex;host=10.0.0.41", 'fernando', 'indioveio');

		$sql = "SELECT
					*
				FROM
					medicina.horario_data
				WHERE
					id_horario = $id_horario
				ORDER BY
					rand()";
		$res_horario_data = $coopex->query($sql);	

		$sql = "SELECT
					*
				FROM
					medicina.sub_grupo
				WHERE
					id_horario = $id_horario";
		$res_sub_grupo = $coopex->query($sql);	
		$total_sub_grupo = $res_sub_grupo->rowCount();
		
		while($sub_grupo = $res_sub_grupo->fetch(PDO::FETCH_OBJ)){
			$arr_sub_grupo[] = $sub_grupo;
		}

		shuffle($arr_sub_grupo);

		$i = 0;
		while($horario_data = $res_horario_data->fetch(PDO::FETCH_OBJ)){
			
			$id_sub_grupo = $arr_sub_grupo[$i]->id_sub_grupo;
			if(!verificar_choque($horario_data->data_disponivel,
								$arr_sub_grupo[$i]->nome_grupo,
								$horario_data->horario_inicio,
								$horario_data->horario_termino)){
				$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
							VALUES ($horario_data->id_horario_data, $id_sub_grupo)";
				$coopex->query($sql);
			}

			$i++;

			if($i == $total_sub_grupo){
				$i = 0;
				shuffle($arr_sub_grupo);
			}
		}

	}

	function numero_especifico($id_horario, $qtd_alunos){
		
		$coopex = new PDO("mysql:dbname=coopex;host=10.0.0.41", 'fernando', 'indioveio');

		//SELECIONA O TOTAL DE HORÁRIOS DISPONÍVEIS
		$sql = "SELECT
					*
				FROM
					medicina.horario_data
				WHERE
					id_horario = $id_horario
				ORDER BY
					rand()";
		$res_horario_data = $coopex->query($sql);	
		$total_horario_data = $res_horario_data->rowCount();

		//SELECIONA O TOTAL DE SUBGRUPOS DISPONÍVEIS
		$sql = "SELECT
					*
				FROM
					medicina.sub_grupo
				INNER JOIN medicina.grupo USING (id_grupo)	
				INNER JOIN medicina.horario USING (id_horario)
				WHERE
					id_horario = $id_horario
				AND alunos = qtd_alunos";
		$res_sub_grupo = $coopex->query($sql);	
		$total_sub_grupo = $res_sub_grupo->rowCount();


		$sql = "SELECT
					*
				FROM
					medicina.sub_grupo
				INNER JOIN medicina.grupo USING (id_grupo)
				INNER JOIN medicina.horario USING (id_horario)
				WHERE
					id_horario = $id_horario
				AND alunos = qtd_alunos
				GROUP BY
					id_grupo";
		$res_sub_grupo = $coopex->query($sql);

		while($sub_grupo = $res_sub_grupo->fetch(PDO::FETCH_OBJ)){
			$arr_sub_grupo[] = $sub_grupo;
		}
		$arr_sub_grupo = [];
		$sql = "SELECT
					*
				FROM
					medicina.sub_grupo
				INNER JOIN medicina.grupo USING (id_grupo)
				INNER JOIN medicina.horario USING (id_horario)
				WHERE
					id_horario = $id_horario
				AND alunos = qtd_alunos
				GROUP BY
					id_grupo";
		$res_sub_grupo = $coopex->query($sql);
		while($sub_grupo = $res_sub_grupo->fetch(PDO::FETCH_OBJ)){
			$arr_sub_grupo[] = $sub_grupo;
		}

		$sql = "SELECT
					*
				FROM
					medicina.sub_grupo
				INNER JOIN medicina.horario USING (id_horario)
				WHERE
					id_horario = $id_horario
				AND alunos <> qtd_alunos";
		$res_sub_grupo_parcial = $coopex->query($sql);	
		$total_sub_grupo_parcial = $res_sub_grupo_parcial->rowCount();
		
		while($sub_grupo_parcial = $res_sub_grupo_parcial->fetch(PDO::FETCH_OBJ)){
			$arr_sub_grupo_parcial[] = $sub_grupo_parcial;
		}



			//shuffle($arr_sub_grupo);
		//shuffle($arr_sub_grupo_parcial);

		/*print_r($arr_sub_grupo);
		echo "<hr>";
		print_r($arr_sub_grupo_parcial);*/

		//print_r($arr_sub_grupo);
		$i = 0;
		$j = 0;

		if(count($arr_sub_grupo)){

			while($horario_data = $res_horario_data->fetch(PDO::FETCH_OBJ)){


				if(!verificar_choque($horario_data->data_disponivel,
									$arr_sub_grupo[$i]->nome_grupo,
									$horario_data->horario_inicio,
									$horario_data->horario_termino)){
					$id_sub_grupo = $arr_sub_grupo[$i]->id_sub_grupo;
					$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
								VALUES ($horario_data->id_horario_data, $id_sub_grupo)";
					$coopex->query($sql);
				} else {
					$_SESSION['choque'] = $id_sub_grupo;
				}
				

				
				$i++;

				if($i > $total_sub_grupo){

					
					$i = 0;

					for($x = $j; $x < $j + ($arr_sub_grupo_parcial[$x]->qtd_alunos / $arr_sub_grupo_parcial[$x]->alunos); $x++){

						//echo $x;

						$id_sub_grupo_parcial = $arr_sub_grupo_parcial[$x]->id_sub_grupo;
						$sql = "INSERT INTO `medicina`.`cronograma`(`id_horario_data`, `id_sub_grupo`)
									VALUES ($horario_data->id_horario_data, $id_sub_grupo_parcial)";
						$coopex->query($sql);
					}

					//echo $x;
					
					shuffle($arr_sub_grupo);
				}


				if($i == count($arr_sub_grupo)){
					$i = 0;
				}
			}
		}

		return $arr_sub_grupo;


	}

	function verificar_choque($data, $grupo, $horario_inicio, $horario_termino){
		
		$coopex = new PDO("mysql:dbname=coopex;host=10.0.0.41", 'fernando', 'indioveio');

		$sql_teste = "SELECT
							horario_inicio, horario_termino,
							ADDTIME(horario_inicio, '-1:00') AS horario_inicio_limite,
							ADDTIME(horario_termino, '1:00') AS horario_termino_limite
						FROM
							medicina.cronograma
						INNER JOIN medicina.horario_data a USING (id_horario_data)
						INNER JOIN medicina.sub_grupo USING (id_sub_grupo)
						INNER JOIN medicina.grupo USING (id_grupo)
						INNER JOIN medicina.horario b ON a.id_horario = b.id_horario
						WHERE
							data_disponivel = '$data'
						AND teorica = 0
						AND nome_grupo = $grupo
						HAVING
							('$horario_inicio' BETWEEN horario_inicio_limite
						AND horario_termino_limite) OR ('$horario_termino' BETWEEN horario_inicio_limite
						AND horario_termino_limite)";
			$res_teste = $coopex->query($sql_teste);
			if($res_teste->rowCount()){
				
				//$choque++;
				//echo $sql_teste;
				
				echo "JÁ TEM $data, $grupo, $horario_inicio, $horario_termino";
				$row = $res_teste->fetch(PDO::FETCH_OBJ);
				print_r($row);
				echo "<hr>";
			}
		

			return $res_teste->rowCount();
	}

	$sql = "SELECT
				*
			FROM
				medicina.horario
			INNER JOIN medicina.horario_data USING (id_horario)
			WHERE
				id_periodo = 1
			AND
				excluido = 0	
			AND 
				id_horario_data NOT IN (
					SELECT
						id_horario_data
					FROM
						medicina.cronograma
					ORDER BY
						data_disponivel
				)";
	//echo "<hr>";
	$res_horario_data = $coopex->query($sql);
	echo $res_horario_data->rowCount();

	cronograma();

	print_r($_SESSION['choque']);
	
?>
