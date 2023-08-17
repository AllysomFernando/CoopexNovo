<?php session_start();

	$_SESSION['ficha_financeira']['valor_mensalidade'] = $_SESSION['ficha_financeira']['valor_semestre'] / 6;
	
	$json = '[{';
	
	$json .= '"ra":"'.$_SESSION['ficha_financeira']['ra'].'",';
	$json .= '"whatsapp":"'.$_SESSION['ficha_financeira']['whatsapp'].'",';
	$json .= '"email":"'.$_SESSION['ficha_financeira']['email'].'",';
	$json .= '"nome_academico":"'.$_SESSION['ficha_financeira']['nome_academico'].'",';
	$json .= '"turno":"'.$_SESSION['ficha_financeira']['turno'].'",';
	$json .= '"turma":"'.utf8_encode(trim($_SESSION['ficha_financeira']['link_de_turma'])).'",';
	$json .= '"valor_semestre":"'.$_SESSION['ficha_financeira']['valor_semestre'].'",';
	$json .= '"valor_hora":"'.$_SESSION['ficha_financeira']['valor_hora'].'",';
	$json .= '"valor_mensalidade":"'.(trim(number_format($_SESSION['ficha_financeira']['valor_mensalidade'], 2, ',', '.'))).'",';
	$json .= '"id_turma":"'.$_SESSION['ficha_financeira']['id_turma'].'"';
	
	$json .= '}]';

	echo $json;

?>