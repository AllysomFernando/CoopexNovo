<pre>
<?php

	$id_menu = 22; //ID DO MENU
	$tabela  = "coopex_reoferta.pre_matricula"; //TABELA PRINCIPAL
	$chave	 = "id_pre_matricula"; //CAMPO CHAVE DA TABELA

	$id_registro = $_POST[$chave] ? $_POST[$chave] : "0";
	
	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");
	require_once("../../../php/utils.php");
	
	//verificarPermissao($id_menu, $tabela, $chave, $id_registro);

	//VERFIFICA SE O FORMULÁRIO FOI ENVIADO
	if($_POST){
		extract($_POST);


		$sql = "SELECT id_pessoa FROM pessoa WHERE id_pessoa = $id_pessoa";
		$pessoa = $coopex->query($sql);
		if(!$pessoa->rowCount()){
			require_once("../../../php/sqlsrv.php"); 
			$sql = "SELECT id_pessoa, nome, usuario, email, 6, cpf, NULL, id_faculdade, ra FROM integracao..view_integracao_usuario a WHERE id_pessoa = $id_pessoa";
			$res = mssql_query($sql);
			$row = mssql_fetch_object($res);

			$sql = "INSERT INTO `coopex`.`pessoa`(`id_pessoa`, `nome`, `usuario`, `email`, `id_tipo_usuario`, `cpf`, `avatar`, `id_campus`, `ra`)
					VALUES (".$row->id_pessoa.", '".$row->nome."', '".$row->usuario."', '".$row->email."', 6, '".$row->cpf."', null, '".$row->id_faculdade."', '".$row->ra."')";
			$pessoa = $coopex->query($sql);
		}

		$sql = "select pre_inscricao_data_final from coopex_reoferta.reoferta where id_reoferta = $id_reoferta";
		$res_vencimento = $coopex->query($sql);
		$vencimento = $res_vencimento->fetch(PDO::FETCH_OBJ);
			
		$_POST['data_pre_matricula'] = date("Y-m-d H:i:s");
		$_POST['data_vencimento'] = $vencimento->pre_inscricao_data_final;
		$_POST['valor'] = 60;
		
		//VERFICA SE ESTÁ CADASTRANDO OU ALTERANDO
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
		} //VERFICA SE O USUÁRIO ESTÁ CADASTRANDO OU ALTERANDO MONTA A SQL PARA INSERT OU UPDATE

		if($cadastro_sucesso){
			avaliacao_reoferta($id_pessoa, $id_reoferta);
			echo "<script>parent.prematriculaOK()</script>";
		} else {
			echo "<script>parent.prematriculaFalha()</script>";
		}
		
		//print_r($_POST);
		//exit;
	}
?>