<pre>
<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);


	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");

	$id_periodo = 3;

	$sql = "SELECT
				*
			FROM
				medicina.horario_data";
	$res = $coopex->query($sql);
	$semestre = $res->fetch(PDO::FETCH_OBJ);

	
	$sql = "SELECT
				*
			FROM
				medicina.grupo_periodo
			INNER JOIN medicina.horario USING (id_periodo)
			WHERE
				id_periodo = $id_periodo";
	$res = $coopex->query($sql);

	while($row = $res->fetch(PDO::FETCH_OBJ)){
		print_r($row);
		$sql = "SELECT
					*
				FROM
					medicina.grupo
				WHERE
					id_grupo_periodo = $row->id_grupo_periodo";
		$res2 = $coopex->query($sql);

		while($row2 = $res2->fetch(PDO::FETCH_OBJ)){
			//print_r($row2);
			if($row->id_grupo_aluno == 3){
				$sub_grupo = ceil($row2->alunos_grupo / $row->qtd_alunos);
			} else if($row->id_grupo_aluno == 2){
				$sub_grupo = 2;
			} else if($row->id_grupo_aluno == 3){
				$sub_grupo = 1;
			} 		

			for($i = 1; $i<=$sub_grupo; $i++){
				$sql = "INSERT INTO medicina.cronograma (id_horario, id_grupo, sub_grupo) VALUES ($row->id_horario, $row2->id_grupo, $i)";
				$coopex->query($sql);
			}

			/*echo $sql = "SELECT
						*
					FROM
						medicina.grupo_periodo
					INNER JOIN medicina.horario USING (id_periodo)
					WHERE
						id_periodo = $id_periodo";
			$res3 = $coopex->query($sql);

			while($row3 = $res3->fetch(PDO::FETCH_OBJ)){
				
				print_r($row3);

			}*/

		}

	}

	
?>
