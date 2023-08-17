<pre>
<?php


	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");
	require_once("../../../php/utils.php");

	#PRE MATRÍCULA
	$sql = "SELECT
				* 
			FROM
				coopex_reoferta.reoferta
				INNER JOIN coopex.departamento USING ( id_departamento ) 
			WHERE
				notificacao = 0 
				AND id_campus = 1000000002 
				AND id_parecer = 2 
				AND pre_inscricao_data_final < date( now( ) )";

	$res = $coopex->query($sql);
	
	while($reoferta = $res->fetch(PDO::FETCH_OBJ)){

		$disciplina = utf8_encode($reoferta->disciplina);
		$id_reoferta = $reoferta->id_reoferta;
		$assunto	= "Reoferta Pré: $disciplina";
		$texto		= "Disciplina: <b>$disciplina</b><br><br>
		<a href='https://coopex.fag.edu.br/reoferta/inscritos/$id_reoferta'>Acessar Reoferta</a>";

		$remetente = "reofertas@fag.edu.br";

		echo email($remetente, "fernando@fag.edu.br", $assunto, $texto);
		echo email($remetente, "secretaria@fag.edu.br", $assunto, $texto);
		echo email($remetente, "tesouraria@fag.edu.br", $assunto, $texto);
		echo email($remetente, "reofertas@fag.edu.br", $assunto, $texto);
	}

	$sql = "UPDATE `coopex_reoferta`.`reoferta`
			SET `notificacao` = 1
			AND id_parecer = 2
			WHERE pre_inscricao_data_final < date( now( ) )";

	$coopex->query($sql);



	#MATRÍCULA
	$sql = "SELECT
				* 
			FROM
				coopex_reoferta.reoferta 
				INNER JOIN coopex.departamento USING ( id_departamento ) 
			WHERE
				notificacao_matricula = 0 
				AND id_campus = 1000000002
				AND id_parecer = 2
				AND inscricao_data_final < date( now( ) )";

	$res = $coopex->query($sql);
	
	while($reoferta = $res->fetch(PDO::FETCH_OBJ)){

		$disciplina = utf8_encode($reoferta->disciplina);
		$id_reoferta = $reoferta->id_reoferta;
		$assunto	= "Reoferta Matrícula: $disciplina";
		$texto		= "Disciplina: <b>$disciplina</b><br><br>
		<a href='https://coopex.fag.edu.br/reoferta/inscritos/$id_reoferta'>Acessar Reoferta</a>";

		$remetente = "reofertas@fag.edu.br";

		echo email($remetente, "fernando@fag.edu.br", $assunto, $texto);
		echo email($remetente, "secretaria@fag.edu.br", $assunto, $texto);
		echo email($remetente, "tesouraria@fag.edu.br", $assunto, $texto);
		echo email($remetente, "reofertas@fag.edu.br", $assunto, $texto);
	}

	$sql = "UPDATE `coopex_reoferta`.`reoferta`
			SET `notificacao_matricula` = 1
			AND id_parecer = 2
			WHERE inscricao_data_final < date( now( ) )";

	$coopex->query($sql);







	#PRE MATRÍCULA
	$sql = "SELECT
				* 
			FROM
				coopex_reoferta.reoferta
				INNER JOIN coopex.departamento USING ( id_departamento ) 
			WHERE
				notificacao = 0 
				AND id_campus = 1100000002 
				AND id_parecer = 2 
				AND pre_inscricao_data_final < date( now( ) )";

	$res = $coopex->query($sql);
	
	while($reoferta = $res->fetch(PDO::FETCH_OBJ)){

		$disciplina = utf8_encode($reoferta->disciplina);
		$id_reoferta = $reoferta->id_reoferta;
		$assunto	= "Reoferta Pré: $disciplina";
		$texto		= "Disciplina: <b>$disciplina</b><br><br>
		<a href='https://coopex.fag.edu.br/reoferta/inscritos/$id_reoferta'>Acessar Reoferta</a>";

		$remetente = "reofertas@fag.edu.br";

		echo email($remetente, "fernando@fag.edu.br", $assunto, $texto);
		echo email($remetente, "academicatoledo@fag.edu.br", $assunto, $texto);
		echo email($remetente, "tesourariatoledo@fag.edu.br", $assunto, $texto);
	}

	$sql = "UPDATE `coopex_reoferta`.`reoferta`
			SET `notificacao` = 1
			AND id_parecer = 2
			WHERE pre_inscricao_data_final < date( now( ) )";

	$coopex->query($sql);



	#MATRÍCULA
	$sql = "SELECT
				* 
			FROM
				coopex_reoferta.reoferta 
				INNER JOIN coopex.departamento USING ( id_departamento ) 
			WHERE
				notificacao_matricula = 0 
				AND id_campus = 1100000002
				AND id_parecer = 2
				AND inscricao_data_final < date( now( ) )";

	$res = $coopex->query($sql);
	
	while($reoferta = $res->fetch(PDO::FETCH_OBJ)){

		$disciplina = utf8_encode($reoferta->disciplina);
		$id_reoferta = $reoferta->id_reoferta;
		$assunto	= "Reoferta Matrícula: $disciplina";
		$texto		= "Disciplina: <b>$disciplina</b><br><br>
		<a href='https://coopex.fag.edu.br/reoferta/inscritos/$id_reoferta'>Acessar Reoferta</a>";

		$remetente = "reofertas@fag.edu.br";

		echo email($remetente, "fernando@fag.edu.br", $assunto, $texto);
		echo email($remetente, "academicatoledo@fag.edu.br", $assunto, $texto);
		echo email($remetente, "tesourariatoledo@fag.edu.br", $assunto, $texto);
	}

	$sql = "UPDATE `coopex_reoferta`.`reoferta`
			SET `notificacao_matricula` = 1
			AND id_parecer = 2
			WHERE inscricao_data_final < date( now( ) )";

	$coopex->query($sql);
	
?>