<?php



	$id_menu = 22; //ID DO MENU
	$tabela  = "coopex_reoferta.matricula_autorizacao"; //TABELA PRINCIPAL
	$chave	 = "id_pre_matricula"; //CAMPO CHAVE DA TABELA

	$id_registro =  "0";
	
	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");
	require_once("../../../php/utils.php");
	
	//verificarPermissao($id_menu, $tabela, $chave, $id_registro);

	//VERFIFICA SE O FORMULÁRIO FOI ENVIADO
	if($_POST){
		extract($_POST);

		//print_r($_POST);

		$id_reoferta = $_POST['id_reoferta'];
			
		$_POST['data_autorizacao'] = date("Y-m-d H:i:s");
		$_POST['id_pessoa'] = $_SESSION['coopex']['usuario']['id_pessoa'];

		unset($_POST['id_reoferta']);
		
		//VERFICA SE ESTÁ CADASTRANDO OU ALTERANDO
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
		} //VERFICA SE O USUÁRIO ESTÁ CADASTRANDO OU ALTERANDO MONTA A SQL PARA INSERT OU UPDATE

		
		if($cadastro_sucesso){
			if($_POST['id_autorizacao'] == 1){
				$coluna = "dependente_disciplina";
			} else if($_POST['id_autorizacao'] == 2){
				$coluna = "matriculado_no_periodo";
			} else if($_POST['id_autorizacao'] == 3){
				$coluna = "reofertas_cursadas_no_periodo";
			} else if($_POST['id_autorizacao'] == 4){
				$coluna = "choque_de_horario";
			} else if($_POST['id_autorizacao'] == 5){
				$coluna = "pendencia_financeira";
			}

			$sql = "UPDATE coopex_reoferta.pre_matricula SET $coluna = 1 WHERE id_pre_matricula = ".$id_pre_matricula;
			$coopex->query($sql);


			$sql = "SELECT * FROM coopex_reoferta.pre_matricula WHERE id_pre_matricula = ".$id_pre_matricula;
			$res = $coopex->query($sql);
			$pre = $res->fetch(PDO::FETCH_OBJ);

			//print_r($pre);


			if($pre->dependente_disciplina &&
				$pre->matriculado_no_periodo &&
				$pre->dependente_disciplina && 
				$pre->reofertas_cursadas_no_periodo && 
				$pre->choque_de_horario && 
				$pre->pendencia_financeira 	
			){
				$sql = "UPDATE coopex_reoferta.pre_matricula SET permissao_matricula = 1 WHERE id_pre_matricula = ".$id_pre_matricula;
				$coopex->query($sql);
			}

			echo "<script>parent.autorizacaoOK()</script>";
		} else {
			echo "<script>parent.autorizacaoFalha()</script>";
		}
		
		//print_r($_POST);
		//exit;
	}
?>