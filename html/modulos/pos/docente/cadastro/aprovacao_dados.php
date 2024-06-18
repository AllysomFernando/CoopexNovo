<pre>
<?php

	$id_menu = 22; //ID DO MENU
	$tabela  = "coopex_reoferta.reoferta"; //TABELA PRINCIPAL
	$chave	 = "id_reoferta"; //CAMPO CHAVE DA TABELA

	$id_registro = $_POST[$chave] ? $_POST[$chave] : "0";
	
	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");
	require_once("../../../php/utils.php");
	
	verificarPermissao($id_menu, $tabela, $chave, $id_registro);

	//print_r($_POST);

	//VERFIFICA SE O FORMULÁRIO FOI ENVIADO
	if($_POST){
		extract($_POST);		

		if(isset($parecer_data)){
			$_POST['parecer_data'] = date("Y-m-d H:i:s");
		}
		if(isset($parecer_data_reducao)){
			$_POST['parecer_data_reducao'] = date("Y-m-d H:i:s");
		}

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

		//APROVAÇÃO REDUÇÃO DE CARGA HORÁRIA
		if(isset($id_parecer_reducao)){

			$sql = "SELECT
						email,
						disciplina 
					FROM
						coopex_reoferta.reoferta
						INNER JOIN coopex.pessoa USING ( id_pessoa ) 
					WHERE
						id_reoferta = ".$id_reoferta;
			$res = $coopex->query($sql);
			$reoferta = $res->fetch(PDO::FETCH_OBJ);


			$remetente		= $_SESSION['coopex']['usuario']['email'];
			echo $destinatario 	= $reoferta->email;
			$disciplina		= $reoferta->disciplina;

			if($id_parecer_reducao == 2){
				$assunto	= "Reoferta - Redução de Carga Horária Aprovada";
				echo $texto		= "Disciplina: <b>$disciplina</b>
				<br><br>
				<a href='https://coopex.fag.edu.br/reoferta/cadastro/cadastro/$id_registro'>Acessar Reoferta</a>";
				echo email($remetente, $destinatario, $assunto, $texto);
			} if($id_parecer_reducao == 3){
				$assunto	= "Reoferta - Redução de Carga Horária Reprovada";
				$texto		= "Disciplina: <b>$disciplina</b><br>
						  Motivo: <b>".$parecer_observacao_reducao."</b>
				<br><br>
				<a href='https://coopex.fag.edu.br/reoferta/cadastro/cadastro/$id_registro'>Acessar Reoferta</a>";
				echo email($remetente, $destinatario, $assunto, $texto);
			}
		}

		//APROVAÇÃO FINAL
		if(isset($id_parecer)){

			$sql = "SELECT
					email,
					disciplina 
				FROM
					coopex_reoferta.reoferta
					INNER JOIN coopex.pessoa USING ( id_pessoa ) 
				WHERE
					id_reoferta = ".$id_reoferta;
			$res = $coopex->query($sql);
			$reoferta = $res->fetch(PDO::FETCH_OBJ);

			$remetente 		= $_SESSION['coopex']['usuario']['email'];
			$destinatario 	= $reoferta->email;
			$disciplina		= $reoferta->disciplina;

			if($id_parecer == 2){
				$assunto 		= "Reoferta Aprovada";
				$texto = "Disciplina: <b>$disciplina</b>
				<br><br>
				<a href='https://coopex.fag.edu.br/reoferta/cadastro/cadastro/$id_registro'>Acessar Reoferta</a>";
			} if($id_parecer == 3){
				$assunto 		= "Reoferta Reprovada";
				$texto = "Disciplina: <b>$disciplina</b><br>
						  Motivo: <b>".$parecer_observacao."</b>
				<br><br>
				<a href='https://coopex.fag.edu.br/reoferta/cadastro/cadastro/$id_registro'>Acessar Reoferta</a>";
			}

			echo email($remetente, $destinatario, $assunto, $texto);
		}

		if($cadastro_sucesso){
			echo "<script>parent.cadastroOK($operacao)</script>";
		} else {
			echo "<script>parent.cadastroFalha($operacao)</script>";
		}
	}
?>