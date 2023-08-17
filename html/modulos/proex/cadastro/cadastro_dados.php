<pre>
<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	$id_menu = 22; #ID DO MENU
	$tabela  = "coopex_reoferta.reoferta"; #TABELA PRINCIPAL
	$chave	 = "id_reoferta"; #CAMPO CHAVE DA TABELA

	$id_registro = $_POST[$chave] ? $_POST[$chave] : "0";
	
	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");
	require_once("../../../php/utils.php");

	print_r($_POST);
	
	verificarPermissao($id_menu, $tabela, $chave, $id_registro);

	#VERFIFICA SE O FORMULÁRIO FOI ENVIADO
	if($_POST){
		extract($_POST);
		if(isset($excluir_registro)){
			excluirRegistro($tabela, $chave, $excluir_registro);
		} else {

			$enviado_para_aprovacao_reducao = false;

			#TRATAMENTOS DOS DADOS VINDOS DO FORMULÁRIOS
			#CAMPOS QUE DEVEM SER DESCARTADOS DO BIND
			unset($_POST['id_disciplina_equivalente']);
			unset($_POST['id_academico_autorizado']);
			unset($_POST['pre_inscricao_data_inicial_fixo']);
			unset($_POST['pre_inscricao_data_final_fixo']);
			unset($_POST['inscricao_data_inicial_fixo']);
			unset($_POST['inscricao_data_final_fixo']);
			unset($_POST['cronograma']);
			unset($_POST['enviar_aprovacao']);

			if(isset($enviar_aprovacao_reducao)){
				echo $enviado_para_aprovacao_reducao = true;
				unset($_POST['enviar_aprovacao_reducao']);
			}

			#SE O PERIODO NÃO FOR DIFERENTE DO PRÉ-DEFINIDO PEGA AS DATAS DO PERÍODO SE NÃO PEGAS AS DATAS INFORMADAS
			if($periodo_diferente == "true"){
				$periodo_diferente = 1;
				$_POST['pre_inscricao_data_inicial'] = converterData($pre_inscricao_data_inicial);
				$_POST['pre_inscricao_data_final'] 	 = converterData($pre_inscricao_data_final);
				$_POST['inscricao_data_inicial']	 = converterData($inscricao_data_inicial);
				$_POST['inscricao_data_final'] 		 = converterData($inscricao_data_final);
				$_POST['periodo_diferente'] = 1;
			} else {
				$periodo_diferente = 0;
				$_POST['pre_inscricao_data_inicial'] = converterData($pre_inscricao_data_inicial_fixo);
				$_POST['pre_inscricao_data_final'] 	 = converterData($pre_inscricao_data_final_fixo);
				$_POST['inscricao_data_inicial']	 = converterData($inscricao_data_inicial_fixo);
				$_POST['inscricao_data_final'] 		 = converterData($inscricao_data_final_fixo);
				$_POST['periodo_diferente'] = 0;
			}

			
			#VERFICA A REDUÇÃO DE CARGA HORÁRIA
			$sql = "SELECT
						carga_horaria
					FROM
						coopex_reoferta.carga_horaria
					WHERE id_carga_horaria = ".$id_carga_horaria;

			$res = $coopex->query($sql);
			$reoferta = $res->fetch(PDO::FETCH_OBJ);
			
			if($reoferta->carga_horaria < $_POST['carga_horaria_disciplina']){
				$reoferta->carga_horaria*100 / $_POST['carga_horaria_disciplina'];	
			} #--VERFICA A REDUÇÃO DE CARGA HORÁRIA
			

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


			#DISCIPLINAS EQUIVALENTES---------------------------------------------------------------------------------------------------------------------------
			if(isset($id_disciplina_equivalente)){
				if(count($id_disciplina_equivalente)){
					$disciplina_equivalente = implode(",", $id_disciplina_equivalente);

					$sql = "DELETE FROM	coopex_reoferta.disciplina_equivalente WHERE id_reoferta = $id_registro AND id_disciplina NOT IN ( '$disciplina_equivalente' )";
					$coopex->query($sql);

					for($i=0; $i<count($id_disciplina_equivalente); $i++){
						$disciplina_equivalente = trim($id_disciplina_equivalente[$i]);
						$sql = "REPLACE INTO coopex_reoferta.disciplina_equivalente (id_reoferta, id_disciplina) VALUES ( $id_registro, '$disciplina_equivalente' );";
						$coopex->query($sql);
					}
				}
			} #--DISCIPLINAS EQUIVALENTES


			#ACADÊMICOS AUTORIZADOS
			if(isset($id_academico_autorizado)){
				if(count($id_academico_autorizado)){
					$academico_autorizado = implode(",", $id_academico_autorizado);

					$sql = "DELETE FROM	coopex_reoferta.academico_autorizado WHERE id_reoferta = $id_registro AND id_usuario NOT IN ( $academico_autorizado )";
					$coopex->query($sql);

					for($i=0; $i<count($id_academico_autorizado); $i++){
						$academico_autorizado = $id_academico_autorizado[$i];
						$sql = "REPLACE INTO coopex_reoferta.academico_autorizado (id_reoferta, id_usuario) VALUES ( $id_registro, $academico_autorizado );";
						$coopex->query($sql);
					}
				}
			} #--ACADÊMICO AUTORIZADO


			#CRONOGRAMA
			if(isset($cronograma)){
				$aux = explode(";", $cronograma);
				for($i=1; $i<count($aux); $i++){
					$acao = substr($aux[$i], 0,1);
					$json = substr($aux[$i], 1);
					$json = (json_decode($json));

					$id_cronograma 	  = $json->id_cronograma;
					$data_reoferta    = $json->data_reoferta;
					$horario_inicio   = $json->horario_inicio;
					$horario_termino  = $json->horario_termino;

					if($acao == "i"){
						echo $sql = "INSERT INTO `coopex_reoferta`.`cronograma`(`id_reoferta`, `data_reoferta`, `horario_inicio`, `horario_termino`)
								VALUES ($id_registro, '$data_reoferta', '$horario_inicio', '$horario_termino')";
						$coopex->query($sql);
						gravarLog('coopex_reoferta.cronograma', $id_registro, 1, $sql, '');
					} else if($acao == "u"){
						#SE ESTIVER EDITANDO ALTERA OS REGISTROS SE NÃO INSERE
						if(isset($_GET['id'])){
							echo $sql = "UPDATE `coopex_reoferta`.`cronograma` SET `data_reoferta` = '$data_reoferta', `horario_inicio` = '$horario_inicio', `horario_termino` = '$horario_termino' WHERE `id_cronograma` = $id_cronograma";
							$coopex->query($sql);
							gravarLog('coopex_reoferta.cronograma', $id_registro, 2, $sql, '');
						} else {
							echo $sql = "INSERT INTO `coopex_reoferta`.`cronograma`(`id_reoferta`, `data_reoferta`, `horario_inicio`, `horario_termino`)
								VALUES ($id_registro, '$data_reoferta', '$horario_inicio', '$horario_termino')";
							$coopex->query($sql);
							gravarLog('coopex_reoferta.cronograma', $id_registro, 1, $sql, '');	
						}
					} else if($acao == "d"){
						if($id_cronograma){
							echo $sql = "DELETE FROM `coopex_reoferta`.`cronograma` WHERE `id_cronograma` = $id_cronograma";
							$coopex->query($sql);
							gravarLog('coopex_reoferta.cronograma', $id_registro, 3, $sql, '');
						}
					}
				}
			} #--CRONOGRAMA


			#VERFICA A REDUÇÃO DE CARGA HORÁRIA
			if($enviado_para_aprovacao_reducao){
				$sql = "SELECT
							carga_horaria
						FROM
							coopex_reoferta.carga_horaria
						WHERE id_carga_horaria = ".$id_carga_horaria;

				$res = $coopex->query($sql);
				$reoferta = $res->fetch(PDO::FETCH_OBJ);
				
				if($reoferta->carga_horaria < $_POST['carga_horaria_disciplina']){

					$reducao = round(100 - ($reoferta->carga_horaria*100 / $_POST['carga_horaria_disciplina']), 2);

					$sql = "UPDATE `coopex_reoferta`.`reoferta` SET `enviado_aprovacao_reducao` = 1, `data_envio_aprovacao_reducao` = now() WHERE `id_reoferta` = $id_registro";
					$coopex->query($sql);

					$assunto 		= "Reoferta - Redução de Carga Horária";
					$remetente 		= $_SESSION['coopex']['usuario']['email'];
					$destinatario 	= "jaqueline@fag.edu.br";

					$texto = "Disciplina: <b>$disciplina</b><br>
							  Solicitante: <b>".$_SESSION['coopex']['usuario']['nome']."</b><br><br>
							  Redução de: <b>".$_POST['carga_horaria_disciplina']. "</b> para <b>" .$reoferta->carga_horaria. "</b> = <b>" .$reducao."%</b>
					<br><br>
					<a href='https://coopex.fag.edu.br/reoferta/cadastro/cadastro/$id_registro'>Acessar Reoferta</a>";

					echo email($remetente, $destinatario, $assunto, $texto);

				}
			} #--VERFICA A REDUÇÃO DE CARGA HORÁRIA


			#APROVAÇÃO FINAL
			if(isset($enviar_aprovacao)){
				$sql = "UPDATE `coopex_reoferta`.`reoferta` SET `enviado_aprovacao` = 1, `data_envio_aprovacao` = now() WHERE `id_reoferta` = $id_registro";
				$coopex->query($sql);
				
				$assunto 		= "Reoferta - Aprovação";
				$remetente 		= $_SESSION['coopex']['usuario']['email'];
				$destinatario 	= "andreia@fag.edu.br";

				$texto = "Disciplina: <b>$disciplina</b><br>
						  Solicitante: <b>".$_SESSION['coopex']['usuario']['nome']."</b>
				<br><br>
				<a href='https://coopex.fag.edu.br/reoferta/cadastro/cadastro/$id_registro'>Acessar Reoferta</a>";

				echo email($remetente, $destinatario, $assunto, $texto);
			} #--APROVAÇÃO FINAL


			if($cadastro_sucesso){
				echo "<script>parent.cadastroOK($operacao)</script>";
			} else {
				echo "<script>parent.cadastroFalha($operacao)</script>";
			}
		}
	}
?>