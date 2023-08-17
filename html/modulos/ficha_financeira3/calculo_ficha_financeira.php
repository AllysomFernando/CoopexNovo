<pre>
<? session_start();

		print_r($_SESSION['ficha_financeira']);
		
		$_SESSION['ficha_financeira']['calculo']['ch_pacote']			= 0;
		$_SESSION['ficha_financeira']['calculo']['ch_fora_pacote'] 		= 0;
		$_SESSION['ficha_financeira']['calculo']['ch_desconto']			= 0;
		$_SESSION['ficha_financeira']['calculo']['ch_dp']				= 0;
		$_SESSION['ficha_financeira']['calculo']['valor_dp']			= 0;
		$_SESSION['ficha_financeira']['calculo']['valor_desconto_dp']	= 0;
		
		$disciplinas_total = 0;
		$disciplinas_sem_dp = 0;
		$disciplinas_com_dp = 0;
		
		$ch_total = 0;
		$ch_normal = 0;
		$ch_dp = 0;
		$ch_desconto_dp = 0;
			
		$tipo_calculo = "";

		$valor_semestre_normal  	= 0;
		$valor_semestre_dp 			= 0;
		$valor_semestre_desconto_dp = 0;
		$valor_total_sem_dp		  	= 0;




		foreach ($_SESSION['ficha_financeira']['disciplinas'] as $key => $value) {



			if($_SESSION['ficha_financeira']['disciplinas'][$key]['dp']){
				$ch_desconto_dp += $_SESSION['ficha_financeira']['disciplinas'][$key]['carga_horaria'] - ($_SESSION['ficha_financeira']['disciplinas'][$key]['carga_horaria'] / 100) * ($_SESSION['ficha_financeira']['disciplinas'][$key]['valor_desconto']);
				$ch_dp 			+= $_SESSION['ficha_financeira']['disciplinas'][$key]['carga_horaria'];
				$disciplinas_com_dp++;
			} else {
				$ch_normal 		+= $_SESSION['ficha_financeira']['disciplinas'][$key]['carga_horaria'];
				$disciplinas_sem_dp++;
			}
			$ch_total += $_SESSION['ficha_financeira']['disciplinas'][$key]['carga_horaria'];
			$disciplinas_total++;
		}


		$valor_semestre_desconto_dp = $ch_desconto_dp * $_SESSION['ficha_financeira']['valor_hora'];

		$_SESSION['ficha_financeira']['calculo']['valor_dp'] = $_SESSION['ficha_financeira']['calculo']['ch_dp'] * $_SESSION['ficha_financeira']['valor_hora'];
		$_SESSION['ficha_financeira']['calculo']['valor_desconto_dp'] = $_SESSION['ficha_financeira']['calculo']['ch_desconto'] * $_SESSION['ficha_financeira']['valor_hora'];
		$_SESSION['ficha_financeira']['calculo']['valor_dp'] = $_SESSION['ficha_financeira']['calculo']['valor_dp'] - $_SESSION['ficha_financeira']['calculo']['valor_desconto_dp'];

		//CALCULO DO SEMESTRE
		if($ch_total <  $_SESSION['ficha_financeira']['carga_horaria_pacote'] && $ch_total > 0){
			$tipo_calculo .= "CH Menor que do pacote, ";
			if($disciplinas_total <= 3){

				$tipo_calculo .= "Até 3 disciplinas, ";
				
				if($_SESSION['ficha_financeira']['id_curso'] == 1000000115){

					//$valor_semestre_normal = $ch_total * $_SESSION['ficha_financeira']['valor_hora'] + ($_SESSION['ficha_financeira']['valor_semestre'] / 6);

					$tipo_calculo .= "Medicina até de 3 disciplinas, ";
					/*$horas_retiradas_do_pacote 		 = $_SESSION['ficha_financeira']['carga_horaria_pacote'] - $ch_total;
					$valor_horas_retiradas_do_pacote = $horas_retiradas_do_pacote * $_SESSION['ficha_financeira']['valor_hora'];
					$valor_base_calculo				 = $_SESSION['ficha_financeira']['valor_semestre'] - $valor_horas_retiradas_do_pacote;
					$valor_base_calculo				 = $valor_base_calculo / 6;
					$valor_base_calculo				 = $valor_base_calculo * 5;
					$valor_semestre_normal			 = $valor_base_calculo + ($_SESSION['ficha_financeira']['valor_semestre'] / 6);*/

					$valor_total_sem_dp = $ch_normal * $_SESSION['ficha_financeira']['valor_hora'] + $_SESSION['ficha_financeira']['valor_semestre'] / 6;
					$valor_semestre_normal = $valor_total_sem_dp;
					$valor_semestre_normal = $valor_semestre_desconto_dp + $_SESSION['ficha_financeira']['valor_hora'] + $_SESSION['ficha_financeira']['valor_semestre'] / 6;
				} else {
					$valor_semestre_normal = $ch_total * $_SESSION['ficha_financeira']['valor_hora'];
				}
			} else {

				$tipo_calculo .= "Mais de 3 disciplinas, ";
				$horas_retiradas_do_pacote 		 = $_SESSION['ficha_financeira']['carga_horaria_pacote'] - $ch_total;
				$valor_horas_retiradas_do_pacote = $horas_retiradas_do_pacote * $_SESSION['ficha_financeira']['valor_hora'];
				$valor_base_calculo				 = $_SESSION['ficha_financeira']['valor_semestre'] - $valor_horas_retiradas_do_pacote;
				$valor_base_calculo				 = $valor_base_calculo / 6;
				$valor_base_calculo				 = $valor_base_calculo * 5;
				$valor_semestre_normal			 = $valor_base_calculo + ($_SESSION['ficha_financeira']['valor_semestre'] / 6);
			}
		} else if($ch_total ==  $_SESSION['ficha_financeira']['carga_horaria_pacote']){

			if($disciplinas_com_dp){
				$valor_semestre_normal = $ch_desconto_dp * $_SESSION['ficha_financeira']['valor_hora'];
			} else {
				$tipo_calculo .= "CH total igual CH do pacote";
				$valor_semestre_normal = $_SESSION['ficha_financeira']['valor_semestre'];
			}
		} else if($_SESSION['ficha_financeira']['carga_horaria_pacote'] == 0){
			$tipo_calculo .= "CH pacote zerada";
			$ch_diferenca 		= $ch_total - $_SESSION['ficha_financeira']['carga_horaria_pacote'];
			$valor_diferenca 	= $ch_diferenca * $_SESSION['ficha_financeira']['valor_hora'];
			$valor_semestre_normal 	= $valor_diferenca;

		} else if($ch_total  >  $_SESSION['ficha_financeira']['carga_horaria_pacote']){
			$tipo_calculo .= "CH total maior que CH do pacote";
			$ch_diferenca 		= $ch_total - $_SESSION['ficha_financeira']['carga_horaria_pacote'];
			$valor_diferenca 	= $ch_diferenca * $_SESSION['ficha_financeira']['valor_hora'];
			$valor_semestre_normal 	= $valor_diferenca + $_SESSION['ficha_financeira']['valor_semestre'];
		}

		$valor_semestre_dp = $ch_dp * $_SESSION['ficha_financeira']['valor_hora'];

		//CALCULO DAS DISCIPLINAS NORMAIS
		if($ch_normal <  $_SESSION['ficha_financeira']['carga_horaria_pacote'] && $ch_normal > 0){
			$tipo_calculo .= "CH Menor que do pacote, ";
			if($disciplinas_sem_dp <= 3){
				$tipo_calculo .= "Até 3 disciplinas, ";
				if($_SESSION['ficha_financeira']['id_curso'] == 1000000115){
					/*$_SESSION['ficha_financeira']['calculo']['valor_semestre'] = $valor_semestre_normal - $valor_semestre_dp;

					$_SESSION['ficha_financeira']['calculo']['valor_semestre'] = ($_SESSION['ficha_financeira']['calculo']['valor_semestre'] / 6) * 5 + ($valor_semestre_normal / 6);
					$valor_total_sem_dp = $_SESSION['ficha_financeira']['calculo']['valor_semestre'];*/
					
					$valor_total_sem_dp = $ch_normal * $_SESSION['ficha_financeira']['valor_hora'] + $_SESSION['ficha_financeira']['valor_semestre'] / 6;
					$valor_semestre_normal = $valor_total_sem_dp;
				} else {
					$valor_total_sem_dp = $ch_normal * $_SESSION['ficha_financeira']['valor_hora'];
				}
			} else {
				$tipo_calculo .= "Mais de 3 disciplinas, ";
				$horas_retiradas_do_pacote 		 = $_SESSION['ficha_financeira']['carga_horaria_pacote'] - $ch_normal;
				$valor_horas_retiradas_do_pacote = $horas_retiradas_do_pacote * $_SESSION['ficha_financeira']['valor_hora'];
				$valor_base_calculo				 = $_SESSION['ficha_financeira']['valor_semestre'] - $valor_horas_retiradas_do_pacote;
				$valor_base_calculo				 = $valor_base_calculo / 6;
				$valor_base_calculo				 = $valor_base_calculo * 5;
				$valor_total_sem_dp			 = $valor_base_calculo + ($_SESSION['ficha_financeira']['valor_semestre'] / 6);
			}
		} else if($_SESSION['ficha_financeira']['carga_horaria_pacote'] == 0){
			$tipo_calculo .= "CH do pacote zerada";
			$valor_total_sem_dp 	= $ch_total * $_SESSION['ficha_financeira']['valor_hora'];
		} else if($ch_normal ==  $_SESSION['ficha_financeira']['carga_horaria_pacote']){
			$tipo_calculo .= "CH total igual CH do pacote";
			$valor_total_sem_dp = $_SESSION['ficha_financeira']['valor_semestre'];
		} else if($ch_normal  >  $_SESSION['ficha_financeira']['carga_horaria_pacote']){
			$tipo_calculo .= "CH total maior que CH do pacote";
			$ch_diferenca 		= $ch_normal - $_SESSION['ficha_financeira']['carga_horaria_pacote'];
			$valor_diferenca 	= $ch_diferenca * $_SESSION['ficha_financeira']['valor_hora'];
			$valor_total_sem_dp 	= $valor_diferenca + $_SESSION['ficha_financeira']['valor_semestre'];
		}

		
		/*if($ch_dp == $_SESSION['ficha_financeira']['carga_horaria_pacote']){
			$tipo_calculo .= "DP com CH igual a CH do o pacote, ";
			$valor_semestre_dp = $_SESSION['ficha_financeira']['valor_semestre'];
		} else if($ch_dp > $_SESSION['ficha_financeira']['carga_horaria_pacote']){
			$tipo_calculo .= "DP com CH maior que o pacote, ";
			$ch_diferenca = $ch_dp - $_SESSION['ficha_financeira']['carga_horaria_pacote'];
			$valor_diferenca = $ch_diferenca * $_SESSION['ficha_financeira']['valor_hora'];
			$valor_semestre_dp = $valor_diferenca + $_SESSION['ficha_financeira']['valor_semestre'];
		} else if($ch_dp < $_SESSION['ficha_financeira']['carga_horaria_pacote']){
			if($disciplinas_com_dp <= 3){
				$tipo_calculo .= "DP com Até 3 disciplinas, ";
				$valor_semestre_dp = $ch_dp * $_SESSION['ficha_financeira']['valor_hora'];
			} else {
				$tipo_calculo .= "DP com mais de 3 disciplinas, ";
				$horas_retiradas_do_pacote = $_SESSION['ficha_financeira']['carga_horaria_pacote'] - $ch_dp;
				$valor_horas_retiradas_do_pacote = $horas_retiradas_do_pacote * $_SESSION['ficha_financeira']['valor_hora'];
				$valor_base_calculo				 = $_SESSION['ficha_financeira']['valor_semestre'] - $valor_horas_retiradas_do_pacote;
				$valor_base_calculo				 = $valor_base_calculo / 6;
				$valor_base_calculo				 = $valor_base_calculo * 5;
				$valor_semestre_dp			 	= $valor_base_calculo + ($_SESSION['ficha_financeira']['valor_semestre'] / 6);
			}
		}*/
		
		//CALCULO DAS DPS
			
		echo "ch_pacote -> ".$_SESSION['ficha_financeira']['carga_horaria_pacote'];
		echo "<br>";
		echo "ch_total -> ".$ch_total;
		echo "<br>";

		echo "ch_diferenca -> ".$ch_diferenca;
		echo "<br>";


		echo "disciplinas_sem_dp -> ".$disciplinas_sem_dp;
		echo "<br>";
		echo "disciplinas_com_dp -> ".$disciplinas_com_dp;
		echo "<br>";
		echo "ch_dp -> ".$ch_dp;
		echo "<br>";
		echo "ch_desconto_dp -> ".$ch_desconto_dp;
		echo "<br>";
		echo "ch_normal -> ".$ch_normal;
		echo "<br>";
		echo "calculo -> ".$tipo_calculo;
		echo "<br>";
		echo "valor_semestre_normal -> ".number_format($valor_semestre_normal, 2, ',', '.'); //VALOR TOTAL
		echo "<br>------------------------------<br>";
		echo "valor_semestre_dp -> ".number_format($valor_semestre_dp, 2, ',', '.');
		echo "<br>";
		echo "valor_semestre_desconto_dp -> ".number_format($valor_semestre_desconto_dp, 2, ',', '.'); //VALOR TOTAL DP
		echo "<br>------------------------------<br>";
		echo "valor_total_sem_dp -> ".number_format($valor_total_sem_dp, 2, ',', '.'); //AVALOR TOTAL SEM DP
		echo "<br>";
		echo "valor_total_com_desconto_dp -> ".number_format($valor_total_sem_dp + $valor_semestre_desconto_dp, 2, ',', '.'); //VALOR TOTAL COM DESCONTO DE DP
		echo "<br>";
		echo "valor_mensalidade -> ".number_format($valor_semestre_normal / 6, 2, ',', '.'); //PREVISÃO DA MENSALIDADE
		echo "<br>------------------------------<br>";
		echo $_SESSION['ficha_financeira']['calculo']['ch_desconto_dp'];


?>