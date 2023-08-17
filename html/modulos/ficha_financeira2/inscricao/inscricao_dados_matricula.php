<pre>
<?php

	$id_menu = 24; //ID DO MENU
	$tabela  = "coopex_reoferta.matricula"; //TABELA PRINCIPAL
	$chave	 = "id_matricula"; //CAMPO CHAVE DA TABELA

	$id_registro = $_POST[$chave] ? $_POST[$chave] : "0";
	
	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");
	require_once("../../../php/utils.php");
	
	//verificarPermissao($id_menu, $tabela, $chave, $id_registro);

	//VERFIFICA SE O FORMULÁRIO FOI ENVIADO
	if($_POST){
		extract($_POST);

		#VERFIFICA A DIVISÃO DO VALOR
		$sql = "SELECT
					count( * ) AS total_inscritos 
				FROM
					coopex_reoferta.pre_matricula 
				WHERE
					id_reoferta = $id_reoferta 
					AND permissao_matricula = 1";
		$res = $coopex->query($sql);
		$reoferta = $res->fetch(PDO::FETCH_OBJ);

		if($reoferta->total_inscritos >= $_POST['reoferta_minimo']){
			$tabela_valor = $_POST['reoferta_minimo'];
		} else {
			$tabela_valor = $reoferta->total_inscritos;
		}

		#DETERMINA O VALOR DA MATRÍCULA
		$coluna = "valor_$tabela_valor";
		$sql = "SELECT
					$coluna
				FROM
					coopex_reoferta.carga_horaria 
				WHERE
					carga_horaria = $carga_horaria";
		$res = $coopex->query($sql);
		$valor = $res->fetch(PDO::FETCH_ASSOC);	
		$valor_reoferta = $valor[$coluna];					


		$_POST['data_matricula'] 	= date("Y-m-d H:i:s");
		$_POST['data_vencimento'] 	= converterData($data_vencimento);
		$_POST['id_pessoa'] 		= $_SESSION['coopex']['usuario']['id_pessoa'];
		$_POST['valor'] 			= $valor_reoferta;
		$_POST['divisao']			= $tabela_valor;

		unset($_POST['reoferta_minimo']);
		unset($_POST['carga_horaria']);
		
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

			

			echo "<script>parent.matriculaOK()</script>";
		} else {
			echo "<script>parent.matriculaFalha()</script>";
		}
		
		//print_r($_POST);
		//exit;
	}
?>