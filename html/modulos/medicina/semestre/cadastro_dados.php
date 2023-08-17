<pre>
<?php
	$id_menu = 56;
	$tabela  = "medicina.semestre";
	$chave	 = "id_semestre";

	$id_registro = $_POST[$chave] ? $_POST[$chave] : "0";
	
	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");
	require_once("../../../php/utils.php");
	
	verificarPermissao($id_menu, $tabela, $chave, $id_registro);

	$_POST['data_inicio'] 	= converterData($_POST['data_inicio']);
	$_POST['data_fim'] 		= converterData($_POST['data_fim']);

	//VERFIFICA SE O FORMULÁRIO FOI ENVIADO
	if($_POST){
		extract($_POST); //NÃO ALTERAR-------------------------------------------------------------------------------------------------------------------------

		if(isset($excluir_registro)){
			excluirRegistro($tabela, $chave, $excluir_registro);
		} else {


			

			//VERFICA SE O USUÁRIO ESTÁ CADASTRANDO OU ALTERANDO MONTA A SQL PARA INSERT OU UPDATE
			$sql = !$id_registro ? preparaSQL($_POST, $tabela) : preparaSQL($_POST, $tabela, $chave, $$chave);
			$stm = $coopex->prepare($sql); //PREPARA A EXECUÇÃO DA QUERY-------------------------------------------------------------------------------------------

			


			//PREPARA OS BINDS VINDOS POR POST--------------------------------------------------------------------------------------------------------------------
			$dados = ''; foreach ($_POST as $key => $value) { if($value){ $stm->bindValue(":$key", $$key); $dados .= "$key => $value\n"; } } 

			//PRAPARA BINDS ADICIONAIS
			//$stm->bindValue(":ativo", $ativo);

			try { //NÃO ALTERAR------------------------------------------------------------------------------------------------------------------------------------
		        $coopex->beginTransaction();
		        $stm->execute();
		        $last_id = $coopex->lastInsertId();
		        $coopex->commit();
		        $operacao = !$id_registro ? 1 : 2;
				gravarLog($tabela, !$id_registro ? $last_id : $id_registro, $operacao, $sql, $dados);
				echo "<script>parent.cadastroOK($operacao)</script>";
		    } catch(PDOException $e) {

		    	//die("asdf");
		    	gravarLog($tabela, !$id_registro ? 0 : $id_registro, !$id_registro ? 1 : 2, $sql, $dados, $e->getMessage());
		        //$coopex->rollback();
		       // print "Error!: " . $e->getMessage() . "</br>";
		        $operacao = !$id_registro ? 1 : 2;
		        echo "<script>parent.cadastroFalha($operacao)</script>";
		    } //---------------------------------------------------------------------------------------------------------------------------------------------------
		}
	}

?>