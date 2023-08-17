<pre>
<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	$id_menu = 48; #ID DO MENU
	$tabela  = "medicina.horario"; #TABELA PRINCIPAL
	$chave	 = "id_horario"; #CAMPO CHAVE DA TABELA

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

			if($id_grupo_aluno == 1){
				$_POST['qtd_alunos'] = "null";
			} else if($id_grupo_aluno == 2){
				$_POST['qtd_alunos'] = "null";
			}

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

					$id_horario_dia		= $json->id_horario_dia;
					$horario_inicio		= $json->horario_inicio;
					$horario_termino	= $json->horario_termino;

					if($json->id_dia == "Segunda"){
						$id_dia = 1;
					} else if($json->id_dia == "Terça"){
						$id_dia = 2;
					} else if($json->id_dia == "Quarta"){
						$id_dia = 3;
					} else if($json->id_dia == "Quinta"){
						$id_dia = 4;
					} else if($json->id_dia == "Sexta"){
						$id_dia = 5;
					} 


					if($acao == "i"){
						$sql = "INSERT INTO `medicina`.`horario_dia`(`id_horario`, `id_dia`, `horario_inicio`, `horario_termino`)
								VALUES ($id_registro, '$id_dia', '$horario_inicio', '$horario_termino')";
						$coopex->query($sql);
						gravarLog('medicina.horario_dia', $id_registro, 1, $sql, '');
					} else if($acao == "u"){
						#SE ESTIVER EDITANDO ALTERA OS REGISTROS SE NÃO INSERE
						if($id_dia){
							$sql = "UPDATE `medicina`.`horario_dia`
									SET `id_dia` = '$id_dia', `horario_inicio` = '$horario_inicio', `horario_termino` = '$horario_termino' WHERE `id_horario_dia` = $id_horario_dia";
							$coopex->query($sql);
							gravarLog('medicina.horario_dia', $id_registro, 2, $sql, '');
						} else {
							$sql = "INSERT INTO `medicina`.`horario_dia`(`id_horario`, `id_dia`, `horario_inicio`, `horario_termino`, `ies`, `carga_horaria`, `ementa`)
								VALUES ($id_registro, '$id_dia', '$horario_inicio', '$horario_termino', '$ies', '$carga_horaria', '$ementa')";
							$coopex->query($sql);
							gravarLog('medicina.horario_dia', $id_registro, 1, $sql, '');	
						}
					} else if($acao == "d"){
						if($id_dia){
							$sql = "DELETE FROM `medicina`.`horario_dia` WHERE `id_horario_dia` = $id_horario_dia";
							$coopex->query($sql);
							gravarLog('medicina.horario_dia', $id_registro, 3, $sql, '');
						}
					}

					$sql_del = "DELETE FROM medicina.horario_data WHERE id_horario = $id_registro";
					$coopex->query($sql_del);


					//---------

					$sql = "SELECT
								*
							FROM
								medicina.semestre";
					$res = $coopex->query($sql);
					$semestre = $res->fetch(PDO::FETCH_OBJ);

					$data_inicio = $semestre->data_inicio;
					$data_fim 	 = $semestre->data_fim;
					
					$sql = "SELECT
								*
							FROM
								medicina.horario
							WHERE id_horario = $id_registro";
					$res = $coopex->query($sql);

					while($row = $res->fetch(PDO::FETCH_OBJ)){

						$sql = "SELECT
									*
								FROM
									medicina.horario_dia
								WHERE
									id_horario = $row->id_horario";
						$res2 = $coopex->query($sql);

						while($row2 = $res2->fetch(PDO::FETCH_OBJ)){
							
							$data = date_create($data_inicio);

							while($data_fim >= date_format($data,"Y-m-d")){

								//ACRESCENTA UM DIA NA DATA A CADA LAÇO
								date_add($data, date_interval_create_from_date_string("1 days"));
								
								//VERIFICA SE O DIA É CORRESPONDENTE AO DIA DA SEMANA
								if(date('w', strtotime(date_format($data,"Y-m-d"))) == $row2->id_dia){
									$data_banco = date_format($data,"Y-m-d");
									echo '<br>';

									$sql = "SELECT
												*
											FROM
												medicina.feriado
											WHERE
												data_feriado = '$data_banco'";
									$res3 = $coopex->query($sql);

									if(!$res3->rowCount()){
										$sql_data = "INSERT INTO medicina.horario_data (`id_horario`, `data_disponivel`, `id_dia`)
													 VALUES ('$id_registro', '$data_banco', $row2->id_dia)";
										$coopex->query($sql_data);
									}
								}
							}

						}

					}
					//---------


				}
					
			} #--CRONOGRAMA

	





			if($cadastro_sucesso){
				echo "<script>parent.cadastroOK($operacao)</script>";
			} else {
				echo "<script>parent.cadastroFalha($operacao)</script>";
			}
		}
	}
?>
