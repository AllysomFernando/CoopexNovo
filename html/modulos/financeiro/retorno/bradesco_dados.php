<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");
	require_once("../../../php/utils.php");

	if(isset($_FILES)){
		
		if($_FILES['arquivo_retorno']['size']){						
			$path = $_FILES['arquivo_retorno']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);				
			$arquivo = uniqid(time()).".".$ext;
			move_uploaded_file($_FILES['arquivo_retorno']['tmp_name'], "/var/www/html/arquivos/financeiro/retorno/bradesco/$arquivo");	
		}

		$ponteiro = file_get_contents("/var/www/html/arquivos/financeiro/retorno/bradesco/$arquivo");
		$ponteiro = explode("\n", $ponteiro);

		for($i=0; $i<count($ponteiro); $i++){
			$linha = $ponteiro[$i]."<br>";

			if($i == 0){
				$data_retorno = substr($linha, 94, 6);	
				$data_retorno = '20'.substr($data_retorno,4,2).'-'.substr($data_retorno,2,2).'-'.substr($data_retorno,0,2);
				
				$sql = "INSERT INTO coopex_financeiro.retorno_bradesco VALUES (null,'$arquivo','".$data_retorno."', now(),'".$_SESSION['coopex']['usuario']['id_pessoa']."');";
				$coopex->query($sql);
				$id_retorno = $coopex->lastInsertId();

			} else {

				$seu_numero = substr($linha,116, 10);

				if(strstr($seu_numero, "-")){
					$aux = explode("-", $seu_numero);
					$evento    = $aux[0];
					$inscricao = $aux[1];

					$nosso_numero = intval(substr($linha, 70, 11));
					
					$valor_pago = substr($linha, 253, 13);	
					$valor_pago = substr($valor_pago, 0, (strlen($valor_pago) - 2)).".".substr($valor_pago, (strlen($valor_pago) - 2), 2);
					$valor_pago = floatval($valor_pago);
					
					$data_pagamento = substr($linha, 110, 6);	
					$data_pagamento = '20'.substr($data_pagamento,4,2).'-'.substr($data_pagamento,2,2).'-'.substr($data_pagamento,0,2);

					if($valor_pago > 0){

						$sql = "INSERT INTO `coopex_financeiro`.`retorno_bradesco_pagamento`(`nosso_numero`, `seu_numero`, `data_pagamento`, `valor_pago`, `id_retorno`)
								VALUES ('$nosso_numero', '$seu_numero', '$data_pagamento', $valor_pago, $id_retorno)";
						$coopex->query($sql);
						
						if($evento == 'REPB'){
							$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `pago` = 1, `data_pagamento` = '$data_pagamento' WHERE `id_pre_matricula` = $inscricao";
							$coopex->query($sql);

							$sql = "UPDATE `coopex_reoferta`.`pre_matricula_boleto` SET `pago` = 1, `data_pagamento` = '$data_pagamento', `valor_pago` = $valor_pago, `id_retorno` = $id_retorno
									WHERE `id_pre_matricula` = $inscricao";
							$coopex->query($sql);
						}

						if($evento == 'BIGJ'){
							

							$sql = "UPDATE `colegio`.`big_jump_meia` SET `pagamento` = 1, `data_pagamento` = '$data_pagamento' WHERE `id_big_jump_meia` = $inscricao";
							$coopex->query($sql);

							$sql = "UPDATE `colegio`.`big_jump_boleto` SET `pago` = 1, `data_pagamento` = '$data_pagamento', `valor_pago` = $valor_pago, `id_retorno` = $id_retorno
									WHERE `id_big_jump_meia` = $inscricao";
							$coopex->query($sql);
						}

						if($evento == 'SPOR'){

							$sql = "UPDATE `colegio`.`sports` SET `pagamento` = 1, `data_pagamento` = '$data_pagamento' WHERE `id_sports` = $inscricao";
							$coopex->query($sql);

							$sql = "UPDATE `colegio`.`sports_boleto` SET `pago` = 1, `data_pagamento` = '$data_pagamento', `valor_pago` = $valor_pago, `id_retorno` = $id_retorno
									WHERE `nosso_numero` = '$nosso_numero'";
							$coopex->query($sql);

						}

						if($evento == 'SPOM'){

							/*$sql = "UPDATE `colegio`.`matricula_boleto` SET `pagamento` = 1, `data_pagamento` = '$data_pagamento' WHERE `id_sports` = $inscricao";
							$coopex->query($sql);*/

							$sql = "UPDATE `colegio`.`matricula_boleto` SET `pago` = 1, `data_pagamento` = '$data_pagamento', `valor_pago` = $valor_pago, `id_retorno` = $id_retorno
									WHERE `nosso_numero` = '$nosso_numero'";
							$coopex->query($sql);

						}

						if($evento == 'CDTM'){

							/*$sql = "UPDATE `colegio`.`matricula_boleto` SET `pagamento` = 1, `data_pagamento` = '$data_pagamento' WHERE `id_sports` = $inscricao";
							$coopex->query($sql);*/

							$sql = "UPDATE `colegio`.`cdt_matricula_boleto` SET `pago` = 1, `data_pagamento` = '$data_pagamento', `valor_pago` = $valor_pago, `id_retorno` = $id_retorno
									WHERE `nosso_numero` = '$nosso_numero'";
							$coopex->query($sql);

						}

						if($evento == 'REMB'){
							$sql = "UPDATE `coopex_reoferta`.`matricula` SET `pago` = 1, `data_pagamento` = '$data_pagamento' WHERE `id_matricula` = $inscricao";
							$coopex->query($sql);

							$sql = "UPDATE `coopex_reoferta`.`matricula_boleto` SET `pago` = 1, `data_pagamento` = '$data_pagamento', `valor_pago` = $valor_pago, `id_retorno` = $id_retorno
									WHERE `id_matricula` = $inscricao";
							$coopex->query($sql);

							#COMENTAR CASO BATA O DESESPERO NO PESSOAL DA SECRETARIA
							$sql = "SELECT
										nome,
										disciplina,
										id_reoferta
									FROM
										coopex_reoferta.matricula
										INNER JOIN coopex.pessoa USING ( id_pessoa )
										INNER JOIN coopex_reoferta.reoferta USING ( id_reoferta ) 
									WHERE
										id_matricula = $inscricao";
							$res = $coopex->query($sql);
							$aviso = $res->fetch(PDO::FETCH_OBJ);

							$remetente 		= $_SESSION['coopex']['usuario']['email'];
							$destinatario 	= "secretaria@fag.edu.br";
						
							$assunto 		= "Reoferta: Pagamento de Matrícula - #$inscricao";
							$texto = "Nome: <b>$aviso->nome</b>
							<br>Reoferta: <b>$aviso->disciplina</b>
							<br><br>
						
							<a href='https://coopex.fag.edu.br/reoferta/inscritos/$aviso->id_reoferta'>Acessar Reoferta</a>";
							email($remetente, $destinatario, $assunto, $texto);
							#COMENTAR CASO BATA O DESESPERO NO PESSOAL DA SECRETARIA
						}

					}

				}

			}
		}
		echo "<script>parent.retornoOK($id_retorno)</script>";		
	}
?>