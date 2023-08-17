<?php session_start();
	
	/*ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
*/

	include "../../funcoes_ficha_financeira.php";
	
	calcular_ficha_financeira();

	echo $_SESSION['ficha_financeira']['calculo']['tipo_calculo'];

	echo "<br>";

	$json = '[{';
	$json .= '"carga_horaria":"'.$_SESSION['ficha_financeira']['calculo']['ch_total'].'",';
	$json .= '"valor_desconto_dp":"'.number_format($_SESSION['ficha_financeira']['calculo']['valor_desconto_dp'], 2, ',', '.').'",';
	$json .= '"valor_dp":"'.number_format($_SESSION['ficha_financeira']['calculo']['valor_dp'], 2, ',', '.').'",';
	$json .= '"valor_pacote":"'.number_format($_SESSION['ficha_financeira']['calculo']['valor_disciplinas_pacote'], 2, ',', '.').'",';
	$json .= '"valor_fora_pacote":"'.number_format($_SESSION['ficha_financeira']['calculo']['valor_disciplinas_fora_pacote'], 2, ',', '.').'",';
	$json .= '"valor_total_semestre":"'.number_format($_SESSION['ficha_financeira']['calculo']['valor_semestre'], 2, ',', '.').'",';
	
	if($_SESSION['ficha_financeira']['calculo']['desconto_dp']){
		$json .= '"valor_convalidacao":"'.number_format($_SESSION['ficha_financeira']['calculo']['valor_semestre_sem_dp'], 2, ',', '.').'",';
		$json .= '"destaque":"destaque_valor_semestre_sem_dp",';
	} else {
		$json .= '"valor_convalidacao":"'.number_format($_SESSION['ficha_financeira']['calculo']['valor_semestre'], 2, ',', '.').'",';
		$json .= '"destaque":"destaque_valor_total_semestre",';
	}

	$json .= '"valor_semestre_sem_dp":"'.number_format($_SESSION['ficha_financeira']['calculo']['valor_semestre_sem_dp'], 2, ',', '.').'",';
	$json .= '"valor_total_semestre_com_desconto":"'.number_format($_SESSION['ficha_financeira']['calculo']['valor_total_semestre_com_desconto'], 2, ',', '.').'",';
	$json .= '"valor_previsao_mensalidade_dp":"'.number_format($_SESSION['ficha_financeira']['calculo']['valor_total_semestre_com_desconto'] / 6, 2, ',', '.').'",';
	$json .= '"valor_previsao_mensalidade":"'.number_format($_SESSION['ficha_financeira']['calculo']['valor_semestre'] / 6, 2, ',', '.').'"';
	$json .= '}]';

	echo $json;

?>