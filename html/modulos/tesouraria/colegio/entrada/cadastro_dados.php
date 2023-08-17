<pre>
<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	print_r($_POST);

	$id_menu = 68;
	$tabela  = "tesouraria.entrada";
	$chave	 = "id_entrada";

	$id_registro = $_POST[$chave] ? $_POST[$chave] : "0";
	
	require_once("/var/www/html/php/config.php");
	require_once("/var/www/html/php/mysql.php");
	require_once("/var/www/html/php/utils.php");
	
	verificarPermissao($id_menu, $tabela, $chave, $id_registro);

	//VERFIFICA SE O FORMULÁRIO FOI ENVIADO
	if($_POST){

		if(!$id_registro){
			$_POST['data_entrada'] = date("Y-m-d H:i:s");
			$_POST['id_pessoa'] = $_SESSION['coopex']['usuario']['id_pessoa'];
		}


		extract($_POST); //NÃO ALTERAR---------------------------------------------------------------------

		if(isset($excluir_registro)){
			excluirRegistro($tabela, $chave, $excluir_registro);
		} else {

			//VERFICA SE O USUÁRIO ESTÁ CADASTRANDO OU ALTERANDO MONTA A SQL PARA INSERT OU UPDATE
			$sql = !$id_registro ? preparaSQL($_POST, $tabela) : preparaSQL($_POST, $tabela, $chave, $$chave);
			$stm = $coopex->prepare($sql); //PREPARA A EXECUÇÃO DA QUERY-------------------------------------------------------------------------------------------

			//TRATA OS DADOS VINDOS DO FORMULÁRIO
			
			
			//PREPARA OS BINDS VINDOS POR POST--------------------------------------------------------------------------------------------------------------------
			$dados = ''; foreach ($_POST as $key => $value) { if($value){ $stm->bindValue(":$key", $$key); $dados .= "$key => $value\n"; } } 

			//PRAPARA BINDS ADICIONAIS
			//$stm->bindValue(":ativo", $ativo);

			try { //NÃO ALTERAR---------------------------------------------------------------------------------------
		        $coopex->beginTransaction();
		        $stm->execute();
		        $last_id = $coopex->lastInsertId();
		        $coopex->commit();
		        $operacao = !$id_registro ? 1 : 2;
				gravarLog($tabela, !$id_registro ? $last_id : $id_registro, $operacao, $sql, $dados);
				$cadastro_sucesso = true;
		    } catch(PDOException $e) {

		    	//die("asdf");
		    	gravarLog($tabela, !$id_registro ? 0 : $id_registro, !$id_registro ? 1 : 2, $sql, $dados, $e->getMessage());
		        //$coopex->rollback();
		       // print "Error!: " . $e->getMessage() . "</br>";
		        $operacao = !$id_registro ? 1 : 2;
		        $cadastro_sucesso = false;
		    } //----------------------------------------------------------------------------------------------------------

		    $sql = "UPDATE tesouraria.material
					SET quantidade = quantidade + $quantidade
					WHERE
						id_material = $id_material";
			$coopex->query($sql);


			if($cadastro_sucesso){
				echo "<script>parent.cadastroOK($operacao)</script>";
			} else {
				echo "<script>parent.cadastroFalha($operacao)</script>";
			}

		}
	}

?>