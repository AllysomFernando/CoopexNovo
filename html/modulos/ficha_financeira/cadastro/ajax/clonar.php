<?php session_start();
	
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	$id_ficha = $_GET['id_ficha_financeira'];


	$sql = "SELECT
				*
			FROM
				ficha_financeira.ficha_financeira
			WHERE
				id_ficha_financeira = $id_ficha";

	$ficha = $coopex->query($sql);
	$row = $ficha->fetch(PDO::FETCH_OBJ);
	
	$sql = "INSERT INTO ficha_financeira.ficha_financeira (`id_curso`, `id_pessoa`, `id_grade`, `id_semestre`, `data_cadastro`, `id_pessoa_cadastro`, `observacao`, `id_turma`) VALUES ('$row->id_curso', '$row->id_pessoa', '$row->id_grade', '$row->id_semestre', now(), '$row->id_pessoa_cadastro', '$row->observacao', '$row->id_turma')";

	$coopex->query($sql);
	$novo_id = $coopex->lastInsertId();




	$sql = "SELECT
				*
			FROM
				ficha_financeira.ficha_financeira_disciplinas
			WHERE
				id_ficha_financeira = $id_ficha";

	$disciplinas = $coopex->query($sql);
	
	while($row = $disciplinas->fetch(PDO::FETCH_OBJ)){
		$sql = "INSERT INTO ficha_financeira.ficha_financeira_disciplinas (`id_ficha_financeira`, `id_disciplina`, `id_equivalencia`, `carga_horaria`, `dp`, `fora_do_pacote`, `valor_desconto`, `id_classe`) VALUES ($novo_id, '$row->id_disciplina', '$row->id_equivalencia', '$row->carga_horaria', '$row->dp', '$row->fora_do_pacote', '$row->valor_desconto', '$row->id_classe')";
		$coopex->query($sql);		
	}

	$sql = "INSERT INTO ficha_financeira.ficha_financeira_etapa (`id_ficha_financeira`, `id_etapa`, `data_cadastro`) VALUES ($novo_id, 1, now())";
	$coopex->query($sql);

	$sql = "INSERT INTO ficha_financeira.ficha_financeira_etapa (`id_ficha_financeira`, `id_etapa`, `data_cadastro`) VALUES ($id_ficha, 14, now())";
	$coopex->query($sql);

	$sql = "UPDATE ficha_financeira.ficha_financeira SET id_etapa=14 WHERE (id_ficha_financeira=$id_ficha)";
	$coopex->query($sql);

	echo $novo_id;

?>