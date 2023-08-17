<pre>
<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	$id_menu = 41; #ID DO MENU
	$tabela  = "pos.projeto"; #TABELA PRINCIPAL
	$chave	 = "id_projeto"; #CAMPO CHAVE DA TABELA

	$id_registro = $_POST[$chave] ? $_POST[$chave] : "0";
	
	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");
	require_once("../../../php/utils.php");

	//print_r($_POST);
	
	verificarPermissao($id_menu, $tabela, $chave, $id_registro);

	#VERFIFICA SE O FORMULÁRIO FOI ENVIADO
	if($_POST){
		extract($_POST);
		if(isset($excluir_registro)){
			excluirRegistro($tabela, $chave, $excluir_registro);
		} else {

			$enviado_para_aprovacao_reducao = false;

			unset($_POST['cronograma']);

			#TRATAMENTOS DOS DADOS VINDOS DO FORMULÁRIOS
			#CAMPOS QUE DEVEM SER DESCARTADOS DO BIND
			/*unset($_POST['id_disciplina_equivalente']);
			unset($_POST['id_academico_autorizado']);
			unset($_POST['pre_inscricao_data_inicial_fixo']);
			unset($_POST['pre_inscricao_data_final_fixo']);
			unset($_POST['inscricao_data_inicial_fixo']);
			unset($_POST['inscricao_data_final_fixo']);
			
			unset($_POST['enviar_aprovacao']);

			if(isset($enviar_aprovacao_reducao)){
				echo $enviado_para_aprovacao_reducao = true;
				unset($_POST['enviar_aprovacao_reducao']);
			}*/

			#SE O PERIODO NÃO FOR DIFERENTE DO PRÉ-DEFINIDO PEGA AS DATAS DO PERÍODO SE NÃO PEGAS AS DATAS INFORMADAS
			if($valor_diferente == "true"){
				$valor_diferente = 1;
				/*$_POST['pre_inscricao_data_inicial'] = converterData($pre_inscricao_data_inicial);
				$_POST['pre_inscricao_data_final'] 	 = converterData($pre_inscricao_data_final);
				$_POST['inscricao_data_inicial']	 = converterData($inscricao_data_inicial);
				$_POST['inscricao_data_final'] 		 = converterData($inscricao_data_final);*/
				$_POST['valor_diferente'] = 1;
			} else {
				$valor_diferente = 0;
				/*$_POST['pre_inscricao_data_inicial'] = converterData($pre_inscricao_data_inicial_fixo);
				$_POST['pre_inscricao_data_final'] 	 = converterData($pre_inscricao_data_final_fixo);
				$_POST['inscricao_data_inicial']	 = converterData($inscricao_data_inicial_fixo);
				$_POST['inscricao_data_final'] 		 = converterData($inscricao_data_final_fixo);*/
				$_POST['valor_diferente'] = 0;
			}

			
		
			#VERFICA SE ESTÁ CADASTRANDO OU ALTERANDO
			if(!$id_registro){$_POST['data_cadastro'] = date("Y-m-d H:i:s"); $_POST['id_pessoa'] = $_SESSION['coopex']['usuario']['id_pessoa'];}
			$sql = !$id_registro ? preparaSQL($_POST, $tabela) : preparaSQL($_POST, $tabela, $chave, $$chave); //MONTA A SQL PARA INSERT OU UPDATE
			$stm = $coopex->prepare($sql); //PREPARA A EXECUÇÃO DA QUERY
			extract($_POST); //CONVERTE O POST EM VARIÁVEIS
			$dados = ''; foreach ($_POST as $key => $value) { if($value){ $stm->bindValue(":$key", utf8_decode($$key)); $dados .= "$key => $value\n"; }} //PREPARA OS BINDS VINDOS POR POST 
			$registro = 0;
			$cadastro_sucesso = false;
			$operacao = !$id_registro ? 1 : 2;
			try { 
				$coopex->beginTransaction();
				$stm->execute();
				$last_id = $coopex->lastInsertId();
				$coopex->commit();
				$id_registro = !$id_registro ? $last_id : $id_registro;
				gravarLog($tabela, $id_registro, $operacao, $sql, $dados);
				$cadastro_sucesso = true;
			} catch(PDOException $e) {
				gravarLog($tabela, !$id_registro ? 0 : $id_registro, !$id_registro ? 1 : 2, $sql, $dados, $e->getMessage());
				//coopex->rollback();
				$cadastro_sucesso = false;
				print "Error!: " . $e->getMessage() . "</br>" . print_r($sql) . "</br>". $dados;
			} #--VERFICA SE O USUÁRIO ESTÁ CADASTRANDO OU ALTERANDO MONTA A SQL PARA INSERT OU UPDATE



			#CRONOGRAMA
			print_r($cronograma);
			if(isset($cronograma)){
				$aux = explode(";", $cronograma);
				for($i=1; $i<count($aux); $i++){
					$acao = substr($aux[$i], 0,1);
					$json = substr($aux[$i], 1);
					$json = (json_decode($json));

					$id_disciplina	= $json->id_disciplina;
					$disciplina		= $json->disciplina;
					$docente		= $json->docente;
					$titulacao		= $json->titulacao;
					$ies			= $json->ies;
					$carga_horaria	= $json->carga_horaria;
					$ementa			= $json->ementa;

					if($acao == "i"){
						echo $sql = "INSERT INTO `pos`.`estrutura_curricular`(`id_projeto`, `disciplina`, `docente`, `titulacao`, `ies`, `carga_horaria`, `ementa`)
								VALUES ($id_registro, '$disciplina', '$docente', '$titulacao', '$ies', '$carga_horaria', '$ementa')";
						$coopex->query($sql);
						gravarLog('pos.estrutura_curricular', $id_registro, 1, $sql, '');
					} else if($acao == "u"){
						#SE ESTIVER EDITANDO ALTERA OS REGISTROS SE NÃO INSERE
						if($id_disciplina){
							echo $sql = "UPDATE `pos`.`estrutura_curricular` SET `disciplina` = '$disciplina', `docente` = '$docente', `titulacao` = '$titulacao', `ies` = '$ies', `carga_horaria` = '$carga_horaria', `ementa` = '$ementa' WHERE `id_disciplina` = $id_disciplina";
							$coopex->query($sql);
							gravarLog('pos.estrutura_curricular', $id_registro, 2, $sql, '');
						} else {
							echo $sql = "INSERT INTO `pos`.`estrutura_curricular`(`id_projeto`, `disciplina`, `docente`, `titulacao`, `ies`, `carga_horaria`, `ementa`)
								VALUES ($id_registro, '$disciplina', '$docente', '$titulacao', '$ies', '$carga_horaria', '$ementa')";
							$coopex->query($sql);
							gravarLog('pos.estrutura_curricular', $id_registro, 1, $sql, '');	
						}
					} else if($acao == "d"){
						if($id_disciplina){
							echo $sql = "DELETE FROM `pos`.`estrutura_curricular` WHERE `id_disciplina` = $id_disciplina";
							$coopex->query($sql);
							gravarLog('pos.estrutura_curricular', $id_registro, 3, $sql, '');
						}
					}
				}
			} #--CRONOGRAMA

	


			#APROVAÇÃO FINAL
			if(isset($enviar_aprovacao)){
				/*$sql = "UPDATE `pos`.`projeto` SET `enviado_aprovacao` = 1, `data_envio_aprovacao` = now() WHERE `id_reoferta` = $id_registro";
				$coopex->query($sql);
				
				$assunto 		= "Reoferta - Aprovação";
				$remetente 		= $_SESSION['coopex']['usuario']['email'];
				$destinatario 	= "andreia@fag.edu.br";

				$texto = "Disciplina: <b>$disciplina</b><br>
						  Solicitante: <b>".$_SESSION['coopex']['usuario']['nome']."</b>
				<br><br>
				<a href='https://coopex.fag.edu.br/pos/projeto/cadastro/$id_registro'>Acessar Reoferta</a>";

				echo email($remetente, $destinatario, $assunto, $texto);*/
			} #--APROVAÇÃO FINAL


			if($cadastro_sucesso){
				//echo "<script>parent.cadastroOK($operacao)</script>";
			} else {
				//echo "<script>parent.cadastroFalha($operacao)</script>";
			}
		}
	}
?>
