<pre>
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_menu = 24; //ID DO MENU
$tabela  = "colegio.atestado"; //TABELA PRINCIPAL
$chave	 = "id_atestado"; //CAMPO CHAVE DA TABELA

$id_registro = isset($_POST[$chave]) ? $_POST[$chave] : "0";

require_once("../../../php/config.php");
require_once("../../../php/mysql.php");
require_once("../../../php/utils.php");

print_r($_FILES);
print_r($_REQUEST);

exit;

//verificarPermissao($id_menu, $tabela, $chave, $id_registro);
//print_r($_REQUEST);
//echo 1;
//VERFIFICA SE O FORMULÁRIO FOI ENVIADO


	$nome = $_FILES["atestado"]["name"];
	$nome = explode(".", $nome);
	$ext  = end($nome);

	$_POST['data_atestado'] 	= date("Y-m-d H:i:s");
	$_POST['id_pessoa'] 		= $_SESSION['coopex']['usuario']['id_pessoa'];
	$_POST['extensao'] 		= $ext;

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

	//print_r($_FILES);

	$targetDir = "/var/www/html/arquivos/colegio/sports/atestado/";  // Diretório onde os arquivos serão armazenados


	

	$targetFile = $targetDir . basename($id_registro . ".$ext");

	// Move o arquivo do diretório temporário para o diretório de destino
	if (move_uploaded_file($_FILES["atestado"]["tmp_name"], $targetFile)) {
		//echo "O arquivo foi enviado com sucesso.";
	} else {
		//echo "Desculpe, houve um problema ao enviar o arquivo.";
	}

	if ($cadastro_sucesso) {
		//echo "<script>parent.atestadoOK($id_registro)</script>";
	} else {
		//echo "<script>parent.matriculaFalha()</script>";
	}

	//print_r($_POST);
	//exit;

?>