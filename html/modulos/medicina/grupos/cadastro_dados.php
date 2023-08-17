<pre>
<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	$id_menu = 49; #ID DO MENU
	$tabela  = "medicina.grupo_periodo"; #TABELA PRINCIPAL
	$chave	 = "id_grupo_periodo"; #CAMPO CHAVE DA TABELA

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

			unset($_POST['cronograma']);

			
			#TRATAMENTOS DOS DADOS VINDOS DO FORMULÁRIOS
			#CAMPOS QUE DEVEM SER DESCARTADOS DO BIND
			/*unset($_POST['id_dia_equivalente']);
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

			
			
		
			#VERFICA SE ESTÁ CADASTRANDO OU ALTERANDO
			if(!$id_registro){$_POST['data_cadastro'] = date("Y-m-d H:i:s"); $_POST['id_pessoa'] = $_SESSION['coopex']['usuario']['id_pessoa'];}
			echo $sql = !$id_registro ? preparaSQL($_POST, $tabela) : preparaSQL($_POST, $tabela, $chave, $$chave); //MONTA A SQL PARA INSERT OU UPDATE
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

					$grupo		= $json->grupo;
					$alunos_grupo		= $json->alunos_grupo;

					if($acao == "i"){
						echo $sql = "INSERT INTO `medicina`.`grupo`(`id_grupo_periodo`, `grupo`, `alunos_grupo`)
								VALUES ($id_registro, '$grupo', '$alunos_grupo')";
						$coopex->query($sql);
						gravarLog('medicina.grupo', $id_registro, 1, $sql, '');
					}




					/*if($acao == "i"){
						echo $sql = "INSERT INTO `medicina`.`grupo`(`id_horario`, `id_dia`, `horario_inicio`, `horario_termino`)
								VALUES ($id_registro, '$id_dia', '$horario_inicio', '$horario_termino')";
						$coopex->query($sql);
						gravarLog('medicina.grupo', $id_registro, 1, $sql, '');
					} else if($acao == "u"){
						#SE ESTIVER EDITANDO ALTERA OS REGISTROS SE NÃO INSERE
						if($id_dia){
							echo $sql = "UPDATE `medicina`.`grupo` SET `id_dia` = '$id_dia', `horario_inicio` = '$horario_inicio', `horario_termino` = '$horario_termino' WHERE `id_grupo` = $id_grupo";
							$coopex->query($sql);
							gravarLog('medicina.grupo', $id_registro, 2, $sql, '');
						} else {
							echo $sql = "INSERT INTO `medicina`.`grupo`(`id_horario`, `id_dia`, `horario_inicio`, `horario_termino`, `ies`, `carga_horaria`, `ementa`)
								VALUES ($id_registro, '$id_dia', '$horario_inicio', '$horario_termino', '$ies', '$carga_horaria', '$ementa')";
							$coopex->query($sql);
							gravarLog('medicina.grupo', $id_registro, 1, $sql, '');	
						}
					} else if($acao == "d"){
						if($id_dia){
							echo $sql = "DELETE FROM `medicina`.`grupo` WHERE `id_grupo` = $id_grupo";
							$coopex->query($sql);
							gravarLog('medicina.grupo', $id_registro, 3, $sql, '');
						}
					}*/
				}
					
			} #--CRONOGRAMA

	





			if($cadastro_sucesso){
				//echo "<script>parent.cadastroOK($operacao)</script>";
			} else {
				//echo "<script>parent.cadastroFalha($operacao)</script>";
			}
		}
	}
?>
