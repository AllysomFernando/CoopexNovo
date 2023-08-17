<pre>
<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);


	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");

	$sql = "DELETE FROM medicina.sub_grupo";
	$res = $coopex->query($sql);

	$sql = "SELECT
				*
			FROM
				medicina.horario";
	$res_horario = $coopex->query($sql);


	while($horario = $res_horario->fetch(PDO::FETCH_OBJ)){
		//print_r($horario);

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

				$qtd_sub_grupos = ceil($row_grupo->alunos_grupo / $horario->qtd_alunos);
		
				$total_alunos_grupo = $row_grupo->alunos_grupo;
				$acumulado = 0;

				for($i=1; $i<=$qtd_sub_grupos; $i++){

					$acumulado += $horario->qtd_alunos;
					//echo "<br>";


					//DEFINE A QUANTIDADE DE ALUNOS POR SUBGRUPO, POR HORÁRIO
					if($acumulado <= $row_grupo->alunos_grupo){
						$total_alunos_subgrupo = $horario->qtd_alunos;
					} else {
						$total_alunos_subgrupo = $acumulado - $row_grupo->alunos_grupo;
					}

					echo "$row_grupo->grupo/$i - $total_alunos_subgrupo";

					echo "<br>";

					$nome = "$row_grupo->grupo/$i";
					$sql = "INSERT INTO `medicina`.`sub_grupo`(`id_grupo`, `id_horario`, `alunos`, `nome`)
									VALUES ($row_grupo->id_grupo, $horario->id_horario, $total_alunos_subgrupo, '$nome')";
					$coopex->query($sql);
					//gravarLog('medicina.sub_grupo', $id_registro, 1, $sql, '');
				}

				echo "----------<br>";
			}
		}

	}


	
?>
