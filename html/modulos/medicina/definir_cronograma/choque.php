<pre>
<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);


	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");


	exit;

	$sql = "DELETE FROM medicina.sub_grupo";
	$res = $coopex->query($sql);

	$sql = "SELECT
				*
			FROM
				medicina.horario where excluido = 0";
	$res_horario = $coopex->query($sql);

	while($horario = $res_horario->fetch(PDO::FETCH_OBJ)){
		//print_r($horario);
		$dois_grupos = 0;

		$sql = "SELECT
					*
				FROM
					medicina.grupo_periodo
				WHERE
					id_periodo = $horario->id_periodo";
		$res_grupo_periodo = $coopex->query($sql);

		echo "---------------------------------------------------<br>";

		while($grupo_periodo = $res_grupo_periodo->fetch(PDO::FETCH_OBJ)){

			$sql = "SELECT
						*
					FROM
						medicina.grupo
					WHERE
						id_grupo_periodo = $grupo_periodo->id_grupo_periodo";
			$res_grupo = $coopex->query($sql);

			while($row_grupo = $res_grupo->fetch(PDO::FETCH_OBJ)){


				if($horario->id_grupo_aluno == 1){ //TURMA INTEIRA
					
					echo "<br>Turma Inteira<br>";
					echo "1 - $grupo_periodo->alunos_turma";

					$nome = "1";
					$sql = "INSERT INTO `medicina`.`sub_grupo`(`id_grupo`, `id_horario`, `alunos`, `nome`, `nome_grupo`)
									VALUES ($row_grupo->id_grupo, $horario->id_horario, $grupo_periodo->alunos_turma, '$nome', '$nome')";
					$coopex->query($sql);

					break;

				} else if($horario->id_grupo_aluno == 2){ //MEIA TURMA
					
					echo "<br>Meia Turma<br>";
					echo "<hr>";
					echo $total_alunos_subgrupo = ceil($grupo_periodo->alunos_turma / 2);
					echo "<hr>";

					$qtd_sub_grupos = ceil($grupo_periodo->alunos_turma / $grupo_periodo->alunos_grupo);



					for($i=1; $i<=$qtd_sub_grupos; $i++){ 
						echo "$i - $total_alunos_subgrupo<br>";
						$nome = $i;

						$sql = "INSERT INTO `medicina`.`sub_grupo`(`id_grupo`, `id_horario`, `alunos`, `nome`, `nome_grupo`)
										VALUES ($row_grupo->id_grupo, $horario->id_horario, $total_alunos_subgrupo, '$nome', '$nome')";
						$coopex->query($sql);
					}
					for($i=1; $i<=$qtd_sub_grupos; $i++){ 
						echo "$i - $total_alunos_subgrupo<br>";
						$nome = $i;

						$sql = "INSERT INTO `medicina`.`sub_grupo`(`id_grupo`, `id_horario`, `alunos`, `nome`, `nome_grupo`)
										VALUES ($row_grupo->id_grupo, $horario->id_horario, $total_alunos_subgrupo, '$nome''$nome')";
						$coopex->query($sql);
					}

					break;

				} else if($horario->id_grupo_aluno == 3){ // GRUPO INTEIRO
					
					$qtd_sub_grupos = ceil($grupo_periodo->alunos_turma / $grupo_periodo->alunos_grupo);
					echo "<br>Grupo Inteiro<br>";

					for($i=1; $i<=$qtd_sub_grupos; $i++){ 
						echo "$i - $grupo_periodo->alunos_grupo<br>";
						$nome = $i;

						$sql = "INSERT INTO `medicina`.`sub_grupo`(`id_grupo`, `id_horario`, `alunos`, `nome`, `nome_grupo`)
										VALUES ($row_grupo->id_grupo, $horario->id_horario, $grupo_periodo->alunos_grupo, '$nome', '$nome')";
						$coopex->query($sql);
					}

					break;

				} else if($horario->id_grupo_aluno == 4){ //MEIO GRUPO
					
					echo "<br>Meio Grupo<br>";

					$total_alunos_subgrupo = ceil($row_grupo->alunos_grupo / 2);

					for($i=1; $i<=2; $i++){ 
						echo "$row_grupo->grupo/$i - $total_alunos_subgrupo<br>";
						$nome = "$row_grupo->grupo/$i";

						$sql = "INSERT INTO `medicina`.`sub_grupo`(`id_grupo`, `id_horario`, `alunos`, `nome`, `nome_grupo`)
										VALUES ($row_grupo->id_grupo, $horario->id_horario, $total_alunos_subgrupo, '$nome', $row_grupo->grupo)";
						$coopex->query($sql);
					}

					//break;

				} else if($horario->id_grupo_aluno == 5){ //NÚMERO ESPECÍFICO
					
					$qtd_sub_grupos = ceil($row_grupo->alunos_grupo / $horario->qtd_alunos);
					echo "<br>Número Específico<br>";

					$total_alunos_grupo = $row_grupo->alunos_grupo;
					$acumulado = 0;

					for($i=1; $i<=$qtd_sub_grupos; $i++){
		
						$acumulado += $horario->qtd_alunos;        
						if($acumulado <= $row_grupo->alunos_grupo){
							$total_alunos_subgrupo = $horario->qtd_alunos;
						} else {
							$total_alunos_subgrupo = $row_grupo->alunos_grupo - ($acumulado - $horario->qtd_alunos);
						}

						echo "$row_grupo->grupo/$i - $total_alunos_subgrupo<br>";
						$nome = "$row_grupo->grupo/$i";

						$sql = "INSERT INTO `medicina`.`sub_grupo`(`id_grupo`, `id_horario`, `alunos`, `nome`, `nome_grupo`)
										VALUES ($row_grupo->id_grupo, $horario->id_horario, $total_alunos_subgrupo, '$nome', $row_grupo->grupo)";
						$coopex->query($sql);
						//gravarLog('medicina.sub_grupo', $id_registro, 1, $sql, '');
					}
				} else if($horario->id_grupo_aluno == 6){ //DOIS GRUPOS

					$qtd_sub_grupos = ceil($grupo_periodo->alunos_turma / $grupo_periodo->alunos_grupo);
					echo "<br>Grupo Inteiro<br>";

					for($i=1; $i<=$qtd_sub_grupos; $i++){ 
						echo "$i - $grupo_periodo->alunos_grupo<br>";
						$nome = $i;

						$sql = "INSERT INTO `medicina`.`sub_grupo`(`id_grupo`, `id_horario`, `alunos`, `nome`, `nome_grupo`)
										VALUES ($row_grupo->id_grupo, $horario->id_horario, $grupo_periodo->alunos_grupo, '$nome', '$nome')";
						$coopex->query($sql);
					}

					break;

				}
			}
		}

	}


	
?>
