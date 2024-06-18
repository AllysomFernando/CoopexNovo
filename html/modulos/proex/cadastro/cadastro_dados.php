<pre>
<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	$id_menu = 24; #ID DO MENU
	$tabela  = "proex.proex"; #TABELA PRINCIPAL
	$chave	 = "id_reoferta"; #CAMPO CHAVE DA TABELA

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
			echo $tabela, $chave, $excluir_registro;
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

					$sql = "DELETE FROM	proex.disciplina_equivalente WHERE id_reoferta = $id_registro AND id_disciplina NOT IN ( '$disciplina_equivalente' )";
					$coopex->query($sql);

					for($i=0; $i<count($id_disciplina_equivalente); $i++){
						$disciplina_equivalente = trim($id_disciplina_equivalente[$i]);
						$sql = "REPLACE INTO proex.disciplina_equivalente (id_reoferta, id_disciplina) VALUES ( $id_registro, '$disciplina_equivalente' );";
						$coopex->query($sql);
					}
				}
			} #--DISCIPLINAS EQUIVALENTES


			#ACADÊMICOS AUTORIZADOS
			if(isset($id_academico_autorizado)){
				if(count($id_academico_autorizado)){
					$academico_autorizado = implode(",", $id_academico_autorizado);

					$sql = "DELETE FROM	proex.academico_autorizado WHERE id_reoferta = $id_registro AND id_usuario NOT IN ( $academico_autorizado )";
					$coopex->query($sql);

					for($i=0; $i<count($id_academico_autorizado); $i++){
						$academico_autorizado = $id_academico_autorizado[$i];
						$sql = "REPLACE INTO proex.academico_autorizado (id_reoferta, id_usuario) VALUES ( $id_registro, $academico_autorizado );";
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
					$atividade  	  = $json->descricao;
					
					if($acao == "i"){
						echo $sql = "INSERT INTO `proex`.`cronograma`(`id_reoferta`, `data_reoferta`, `horario_inicio`, `horario_termino`, `atividade`)
								VALUES ($id_registro, '$data_reoferta', '$horario_inicio', '$horario_termino', '$atividade')";
						$coopex->query($sql);
						gravarLog('proex.cronograma', $id_registro, 1, $sql, '');
					} else if($acao == "u"){
						#SE ESTIVER EDITANDO ALTERA OS REGISTROS SE NÃO INSERE
						if($operacao == 2){
							echo $sql = "UPDATE `proex`.`cronograma` SET `data_reoferta` = '$data_reoferta', `horario_inicio` = '$horario_inicio', `horario_termino` = '$horario_termino', `atividade` = '$atividade' WHERE `id_cronograma` = $id_cronograma";
							$coopex->query($sql);
							gravarLog('proex.cronograma', $id_registro, 2, $sql, '');
						} else {
							echo $sql = "INSERT INTO `proex`.`cronograma`(`id_reoferta`, `data_reoferta`, `horario_inicio`, `horario_termino`, `atividade`)
								VALUES ($id_registro, '$data_reoferta', '$horario_inicio', '$horario_termino','$atividade')";
							$coopex->query($sql);
							gravarLog('proex.cronograma', $id_registro, 1, $sql, '');	
						}
					} else if($acao == "d"){
						if($id_cronograma){
							echo $sql = "DELETE FROM `proex`.`cronograma` WHERE `id_cronograma` = $id_cronograma";
							$coopex->query($sql);
							gravarLog('proex.cronograma', $id_registro, 3, $sql, '');
						}
					}
				}
			} #--CRONOGRAMA


			


			


			if($cadastro_sucesso){
				if($_SESSION['coopex']['usuario']['id_pessoa'] != 5000246669){
					echo "<script>parent.cadastroOK($operacao)</script>";
				}
			} else {
				echo "<script>parent.cadastroFalha($operacao)</script>";
			}
		}
	}
?>