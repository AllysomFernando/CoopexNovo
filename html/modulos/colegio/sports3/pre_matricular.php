<pre>
<?php

	$id_menu = 93; //ID DO MENU
	$tabela  = "colegio.sports"; //TABELA PRINCIPAL
	$chave	 = "id_sports"; //CAMPO CHAVE DA TABELA
	$valor_unidade = 120;

	$id_registro = $_POST[$chave] ? $_POST[$chave] : "0";
	
	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");
	require_once("../../../php/utils.php");

	
	
	//verificarPermissao($id_menu, $tabela, $chave, $id_registro);

	//VERFIFICA SE O FORMULÁRIO FOI ENVIADO
	if($_POST){
		extract($_POST);

		print_r($id_modalidade);

		unset($_POST['id_modalidade']);

		$_POST['id_pessoa'] = $_SESSION['coopex']['usuario']['id_pessoa'];
		$_POST['data_cadastro'] = date("Y-m-d H:i:s");
		$_POST['data_vencimento'] = date('Y-m-d', strtotime('+2 days', strtotime(date("Y-m-d"))));
		$_POST['valor'] = 120;
		
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

		for($i=0; $i<count($id_modalidade); $i++){
			$id_mod = $id_modalidade[$i];
			$aux = explode(",", $id_mod);
			$modalidade = $aux[0];
			$fila = $aux[1] > 0 ? 0 : 1;
			$vagas = $aux[1];
			$sql = "INSERT INTO `colegio`.`modalidade_aluno` (`id_sports`, `id_modalidade`, `fila`, `qtd_vaga`) VALUES ($id_registro, $modalidade,$fila,'$vagas')";
			$coopex->query($sql);
		}

		if($cadastro_sucesso){
			echo "<script>parent.compraOK($id_registro)</script>";
		} else {
			//echo "<script>parent.prematriculaFalha()</script>";
		}
		
		//print_r($_POST);
		//exit;
	}
?>