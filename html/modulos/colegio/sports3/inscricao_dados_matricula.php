<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_menu = 24; //ID DO MENU
$tabela = "colegio.matricula"; //TABELA PRINCIPAL
$chave = "id_matricula"; //CAMPO CHAVE DA TABELA

$id_registro = isset($_POST[$chave]) ? $_POST[$chave] : "0";

require_once("../../../php/config.php");
require_once("../../../php/mysql.php");
require_once("../../../php/utils.php");

//verificarPermissao($id_menu, $tabela, $chave, $id_registro);

//VERFIFICA SE O FORMULÁRIO FOI ENVIADO
if ($_POST) {

	print_r($_POST);

	extract($_POST);

	$id_modalidade = implode(",", $_POST['id_modalidade_matricula']);

	$modalidade = $_POST['id_modalidade_matricula'];

	$sql = "SELECT
			SUM( valor ) AS total,
			SUM( desconto ) AS desconto 
		FROM
			colegio.modalidade 
		WHERE
			id_modalidade IN ($id_modalidade)";
	$res = $coopex->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);

	if (count($_POST['id_modalidade_matricula']) > 1) {
		$subtotal = $row->total;
		$desconto = $row->total - $row->desconto;
		$total = $row->total - $desconto;
	} else {
		$subtotal = $row->total;
		$desconto = 0;
		$total = $row->total;
	}


	$_POST['data_matricula'] = date("Y-m-d H:i:s");
	//$_POST['data_vencimento'] 	= converterData($data_vencimento);
	$_POST['id_pessoa'] = $_SESSION['coopex']['usuario']['id_pessoa'];
	$_POST['valor'] = $total;


	unset($_POST['id_modalidade_matricula']);

	//VERFICA SE ESTÁ CADASTRANDO OU ALTERANDO
	$sql = !$id_registro ? preparaSQL($_POST, $tabela) : preparaSQL($_POST, $tabela, $chave, $$chave); //MONTA A SQL PARA INSERT OU UPDATE
	$stm = $coopex->prepare($sql); //PREPARA A EXECUÇÃO DA QUERY
	extract($_POST); //CONVERTE O POST EM VARIÁVEIS
	$dados = '';
	foreach ($_POST as $key => $value) {
		if ($value) {
			$stm->bindValue(":$key", utf8_decode($$key));
			$dados .= "$key => $value\n";
		}
	} //PREPARA OS BINDS VINDOS POR POST 
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
	} catch (PDOException $e) {
		gravarLog($tabela, !$id_registro ? 0 : $id_registro, !$id_registro ? 1 : 2, $sql, $dados, $e->getMessage());
		//coopex->rollback();
		$cadastro_sucesso = false;
		print "Error!: " . $e->getMessage() . "</br>" . print_r($sql) . "</br>" . $dados;
	} //VERFICA SE O USUÁRIO ESTÁ CADASTRANDO OU ALTERANDO MONTA A SQL PARA INSERT OU UPDATE

	for ($i = 0; $i < count($modalidade); $i++) {
		$id_mod = $modalidade[$i];
		$sql = "INSERT INTO `colegio`.`modalidade_aluno_matricula` (`id_matricula`, `id_modalidade`) VALUES ($id_registro, $id_mod)";
		$coopex->query($sql);
	}

	$sql = "UPDATE colegio.matricula
			JOIN colegio.atestado ON matricula.id_pessoa = atestado.id_pessoa 
			SET matricula.id_atestado = atestado.id_atestado,
			matricula.data_atestado = atestado.data_atestado,
			matricula.id_atestado = atestado.id_atestado,
			matricula.id_situacao_atestado = atestado.id_situacao_atestado,
			matricula.extensao = atestado.extensao";
	$res = $coopex->query($sql);

	

	//print_r($_FILES);

	/*$targetDir = "/var/www/html/arquivos/colegio/sports/atestado/";  // Diretório onde os arquivos serão armazenados


		  $nome = $_FILES["atestado"]["name"];
		  $nome = explode(".", $nome);
		  $ext  = end($nome);

		  $targetFile = $targetDir . basename($id_registro . ".$ext");

		  // Move o arquivo do diretório temporário para o diretório de destino
		  if (move_uploaded_file($_FILES["atestado"]["tmp_name"], $targetFile)) {
			  //echo "O arquivo foi enviado com sucesso.";
		  } else {
			  //echo "Desculpe, houve um problema ao enviar o arquivo.";
		  }*/

	if ($cadastro_sucesso) {
		echo "<script>parent.matriculaOK($id_registro)</script>";
	} else {
		echo "<script>parent.matriculaFalha()</script>";
	}

	//print_r($_POST);
	//exit;
}
?>