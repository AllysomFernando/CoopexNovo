<pre>
<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	$id_menu = 33; #ID DO MENU
	$tabela  = "ficha_financeira.autorizacao"; #TABELA PRINCIPAL
	$chave	 = "id_autorizacao"; #CAMPO CHAVE DA TABELA

	$id_registro = $_POST[$chave] ? $_POST[$chave] : "0";
	
	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");
	require_once("../../../php/sqlsrv.php");
	require_once("../../../php/utils.php");

	
	verificarPermissao($id_menu, $tabela, $chave, $id_registro);

	#VERFIFICA SE O FORMULÁRIO FOI ENVIADO
	if($_POST){
		extract($_POST);
		if(isset($excluir_registro)){
			excluirRegistro($tabela, $chave, $excluir_registro);
		} else {

			


			//print_r($_POST);

			//exit;
					
			#VERFICA SE ESTÁ CADASTRANDO OU ALTERANDO
			if(!$id_registro){$_POST['data_cadastro'] = date("Y-m-d H:i:s"); $_POST['id_pessoa_cadastro'] = $_SESSION['coopex']['usuario']['id_pessoa'];}
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

			
			


			$sql = "SELECT
						id_pessoa, id_campus 
					FROM
						pessoa 
					WHERE
						id_pessoa = ".$id_pessoa;
			$pessoa = $coopex->query($sql);
			if($pessoa->rowCount() == 0){
				$sql = "select * from integracao..view_integracao_usuario where id_pessoa = '$id_pessoa' order by ra";	
				$res = mssql_query($sql);
				$row = mssql_fetch_assoc($res);

				
			}

			
			if($cadastro_sucesso){
				echo "<script>parent.cadastroOK($operacao, $id_registro)</script>";
			} else {
				echo "<script>parent.cadastroFalha($operacao)</script>";
			}
		}
	}
?>