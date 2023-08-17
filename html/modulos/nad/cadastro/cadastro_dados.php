<pre>
<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	//print_r($_REQUEST);

	$id_menu = 22; #ID DO MENU
	$tabela  = "coopex_nad.pratica_docente"; #TABELA PRINCIPAL
	$chave	 = "id_pratica_docente"; #CAMPO CHAVE DA TABELA

	$id_registro = $_POST[$chave] ? $_POST[$chave] : "0";
	
	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");
	require_once("../../../php/utils.php");

	if(isset($_FILES)){
		
		if($_FILES['arquivo']['size']){						
			$path = $_FILES['arquivo']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);				
			$arquivo = uniqid(time()).".".$ext;
			move_uploaded_file($_FILES['arquivo']['tmp_name'], "/var/www/html/arquivos/nad/publicacoes/$arquivo");	
		}
	}	

	//exit;
	
	verificarPermissao($id_menu, $tabela, $chave, $id_registro);

	#VERFIFICA SE O FORMULÁRIO FOI ENVIADO
	if($_POST){
		extract($_POST);
		if(isset($excluir_registro)){
			excluirRegistro($tabela, $chave, $excluir_registro);
		} else {

			$enviado_para_aprovacao_reducao = false;

			#TRATAMENTOS DOS DADOS VINDOS DO FORMULÁRIOS
			#CAMPOS QUE DEVEM SER DESCARTADOS DO BIND
			//$id_departamento_post = $_POST['id_departamento'];
			//unset($_POST['id_departamento']);
			unset($_POST['termo']);
			$_POST['arquivo'] = $arquivo;

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

			//$id_departamento = $id_departamento_post;

			//print_r($id_departamento);

			// #DISCIPLINAS EQUIVALENTES---------------------------------------------------------------------------------------------------------------------------
			// if(isset($id_departamento)){
			// 	if(count($id_departamento)){
			// 		$departamento = implode(",", $id_departamento);

			// 		$sql = "DELETE FROM	coopex_nad.departamento_pratica_docente WHERE id_pratica_docente = $id_registro AND id_departamento NOT IN ( '$departamento' )";
			// 		$coopex->query($sql);
			// 		for($i=0; $i<count($id_departamento); $i++){
			// 			$departamento = trim($id_departamento[$i]);
			// 			$sql = "REPLACE INTO coopex_nad.departamento_pratica_docente (id_pratica_docente, id_departamento) VALUES ( $id_registro, '$departamento' );";
			// 			$coopex->query($sql);
			// 		}
			// 	}
			// } #--DISCIPLINAS EQUIVALENTES


			if($cadastro_sucesso){
				echo "<script>parent.cadastroOK($operacao)</script>";
			} else {
				echo "<script>parent.cadastroFalha($operacao)</script>";
			}
		}
	}
?>