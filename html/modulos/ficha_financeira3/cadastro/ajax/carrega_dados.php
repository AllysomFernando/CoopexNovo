<?php 
	session_start();

	$_SESSION['ficha_financeira']['valor_mensalidade'] = $_SESSION['ficha_financeira']['valor_semestre'] / 6;

	$data = [
	    'ra' => $_SESSION['ficha_financeira']['ra'],
	    'whatsapp' => $_SESSION['ficha_financeira']['whatsapp'],
	    'email' => $_SESSION['ficha_financeira']['email'],
	    'nome_academico' => $_SESSION['ficha_financeira']['nome_academico'],
	    'turno' => $_SESSION['ficha_financeira']['turno'],
	    'turma' => utf8_encode(trim($_SESSION['ficha_financeira']['link_de_turma'])),
	    'valor_semestre' => utf8_encode(trim($_SESSION['ficha_financeira']['valor_semestre'])),
	    'valor_hora' => utf8_encode(trim($_SESSION['ficha_financeira']['valor_hora'])),
	    'valor_mensalidade' => utf8_encode(trim(number_format($_SESSION['ficha_financeira']['valor_mensalidade'], 2, ',', '.'))),
	    'id_turma' => utf8_encode(trim($_SESSION['ficha_financeira']['id_turma']))
	];

	$json = json_encode([$data]);

	echo $json;
?>