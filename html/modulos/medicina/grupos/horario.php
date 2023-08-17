<!-- CSS only -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
<pre>
	<body class="p-5">
<table class="table table-striped table-bordered">
	<thead align="center">
		<th>#</th>
		<th>ESPECIALIDADE</th>
		<th>DATA</th>
		<th>DIA DA SEMANA</th>
		<th>HORÁRIO</th>
		<th>LOCAL</th>
		<th>GRUPOS</th>
		<th>QTD ALUNOS</th>
		<th>PROFESSOR</th>
		<th>OBS</th>
	</thead>

<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");

	$w[1] = "Segunda";
	$w[2] = "Terça";
	$w[3] = "Quarta";
	$w[4] = "Quinta";
	$w[5] = "Sexta";
	
	$sql = "SELECT
				*, WEEKDAY(data_disponivel) + 1 AS dia_semana
			FROM
				medicina.horario_data
			INNER JOIN medicina.horario USING (id_horario)
			INNER JOIN medicina.horario_dia USING (id_horario)
			INNER JOIN medicina.especialidade USING (id_especialidade)
			INNER JOIN medicina.`local` USING (id_local)
			INNER JOIN coopex.pessoa ON pessoa.id_pessoa = horario.id_docente
			GROUP BY
				data_disponivel,
				id_local
			ORDER BY
				data_disponivel,
				horario_inicio";
	$res = $coopex->query($sql);

	$i = 1;
	while($row = $res->fetch(PDO::FETCH_OBJ)){
		$data = date_create($row->data_disponivel);
		$data = date_format($data,"d/m/Y");

		$especialidade = utf8_encode($row->especialidade);
		$local = utf8_encode($row->local);

		$dia = $w[$row->dia_semana];

		if($row->id_grupo_aluno == 1){
			$grupo  	= "Turma Inteira";
			$cor		= "table-success";
		} else if($row->id_grupo_aluno == 2){
			$grupo 		= "Meia Turma";
			$cor		= "table-danger";
		} else if($row->id_grupo_aluno == 3){
			$grupo 		= "Grupo Inteiro";
			$cor		= "table-success";
		} else if($row->id_grupo_aluno == 4){
			$grupo 		= "Meio Grupo";
			$cor		= "table-danger";
		} else if($row->id_grupo_aluno == 5){
			$grupo 		= $row->qtd_alunos;
			if(isset($row2->grupo)){
				$turma  = $row2->grupo;
			} else {
				$turma  = "-";
			}
			$cor = "";
		}

	$sql2 = "SELECT
				*
			FROM
				medicina.cronograma
			INNER JOIN medicina.grupo USING (id_grupo)
			WHERE
				id_horario_data = $row->id_horario_data";
	$res2 = $coopex->query($sql2);
	$row2 = $res2->fetch(PDO::FETCH_OBJ);

	echo "
	<tr align='center' class='$cor'>
		<td>$i</td>
		<td>$especialidade</td>
		<td>$data</td>
		<td>$dia</td>
		<td>$row->horario_inicio - $row->horario_termino</td>
		<td>$local</td>
	";	
	
	if($row->id_grupo_aluno == 5){
		if(isset($row2->grupo)){
			echo "
			<td>$row2->grupo</td>
			<td>$grupo</td>
			";
		} else {
			echo "
			<td>-</td>
			<td>-</td>
			";
		}
	} else {
		echo "
		<td colspan=2>$grupo</td>
		";
	}	
	
	echo "
		<td>$row->nome</td>
		<td>$row->obs</td>
	</tr>
	";

		$i++;
	}

	
?>
</table>