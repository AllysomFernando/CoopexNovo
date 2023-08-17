<pre>
<?php

	$id_menu = 22; //ID DO MENU
	$tabela  = "coopex_reoferta.solicitacao_revisao"; //TABELA PRINCIPAL
	$chave	 = "id_solicitacao_revisao"; //CAMPO CHAVE DA TABELA

	$id_registro = $_POST[$chave] ? $_POST[$chave] : "0";
	
	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");
	require_once("../../../php/utils.php");
	
	//verificarPermissao($id_menu, $tabela, $chave, $id_registro);

	//VERFIFICA SE O FORMULÁRIO FOI ENVIADO
	if($_POST){
		extract($_POST);


		$sql = "select * from coopex_reoferta.autorizacao where id_autorizacao = $id_autorizacao";
		$res = $coopex->query($sql);
		$autorizacao = $res->fetch(PDO::FETCH_OBJ);

		$sql = "select * from coopex_reoferta.reoferta where id_reoferta = $id_reoferta";
		$res = $coopex->query($sql);
		$reoferta = $res->fetch(PDO::FETCH_OBJ);
			
		$_POST['data_solicitacao'] = date("Y-m-d H:i:s");
		
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

			$remetente 		= $_SESSION['coopex']['usuario']['email'];
			$destinatario 	= $autorizacao->email;
			$justificativa  = utf8_decode($justificativa);
		
				$assunto 		= "Soliticação de Revisão";
				$texto = "Nome: <b>".$_SESSION['coopex']['usuario']['nome']."</b><br>
				<br>Reoferta: <b>$reoferta->disciplina</b>
				<br>

				Motivo: <b>$autorizacao->descricao</b><br>
				Justificativa: <b>$justificativa</b><br><br>
				<a href='https://coopex.fag.edu.br/reoferta/inscritos/$id_reoferta'>Acessar Reoferta</a>";


			email($remetente, $destinatario, $assunto, $texto);

			echo "<script>parent.solicitacao_revisaoOK()</script>";
		} else {
			echo "<script>parent.solicitacao_revisaoFalha()</script>";
		}
		
		//print_r($_POST);
		//exit;
	}
?>