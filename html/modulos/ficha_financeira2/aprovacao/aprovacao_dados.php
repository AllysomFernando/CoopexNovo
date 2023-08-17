<pre>
<?php

	$id_menu = 30; //ID DO MENU
	$tabela  = "ficha_financeira.ficha_financeira_etapa"; //TABELA PRINCIPAL
	$chave	 = "id_ficha_financeira_etapa"; //CAMPO CHAVE DA TABELA

	$id_registro = $_POST[$chave] ? $_POST[$chave] : "0";
	
	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");
	require_once("../../../php/utils.php");
	
	verificarPermissao($id_menu, $tabela, $chave, $id_registro);

	//print_r($_POST);

	//VERFIFICA SE O FORMULÁRIO FOI ENVIADO
	if($_POST){
		extract($_POST);		
		
		//VERFICA SE ESTÁ CADASTRANDO OU ALTERANDO
		if(!$id_registro){$_POST['data_cadastro'] = date("Y-m-d h:i:s"); $_POST['id_pessoa'] = $_SESSION['coopex']['usuario']['id_pessoa'];}
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

		
		$sql = "SELECT
					nome,
					email,
					id_campus
				FROM
					ficha_financeira.ficha_financeira
					INNER JOIN coopex.pessoa USING ( id_pessoa ) 
				WHERE
					id_ficha_financeira = ".$id_ficha_financeira;
		$res = $coopex->query($sql);
		$ficha = $res->fetch(PDO::FETCH_OBJ);
		//APROVAÇÃO REDUÇÃO DE CARGA HORÁRIA
		if($id_etapa == 3){

			$remetente		= "fichafinanceira@fag.edu.br";
			$destinatario 	= $ficha->id_campus == 1000000002 ? "secretaria@fag.edu.br" : "academicatoledo@fag.edu.br";
			$nome			= $ficha->nome;

			$assunto		= "Ficha Financeira - Aprovação: $id_ficha_financeira";
			$texto			= "Acadêmico: <b>$nome</b><br><br><a href='https://coopex.fag.edu.br/ficha_financeira/cadastro/$id_ficha_financeira'>Acessar Ficha Financeira</a>";
			email($remetente, $destinatario, $assunto, $texto);
			//email($remetente, "fernando@fag.edu.br", $assunto, $texto);

			echo "<script>parent.cadastroOK(1)</script>";
		}

		if($id_etapa == 4){

			$sql2 = "SELECT
						nome,
						email 
					FROM
						ficha_financeira.ficha_financeira
						INNER JOIN coopex.pessoa on pessoa.id_pessoa = ficha_financeira.id_pessoa_cadastro
					WHERE
						id_ficha_financeira = ".$id_ficha_financeira;
			$res2 = $coopex->query($sql2);
			$ficha2 = $res2->fetch(PDO::FETCH_OBJ);

			$remetente		= "fichafinanceira@fag.edu.br";
			$destinatario 	= $ficha2->email;
			$nome			= $ficha->nome;

			$assunto		= "Ficha Financeira - Não aprovada: $id_ficha_financeira";
			$texto			= "Acadêmico: <b>$nome</b><br><br>$obs.<br><br><a href='https://coopex.fag.edu.br/ficha_financeira/cadastro/$id_ficha_financeira'>Acessar Ficha Financeira</a>";
			email($remetente, $destinatario, $assunto, $texto);
			//email($remetente, "fernando@fag.edu.br", $assunto, $texto);

			echo "<script>parent.cadastroFalha(1)</script>";
		}
	}
?>