<pre>
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_menu = 112; #ID DO MENU
$tabela = isset($_POST["graduacao_campus"]) && $_POST["graduacao_campus"] == "toledo" ? "graduacao_toledo" : "graduacao"; #TABELA PRINCIPAL
$chave = "graduacao_id"; #CAMPO CHAVE DA TABELA

$id_registro = $_POST[$chave] ? $_POST[$chave] : "0";

require_once("../../../php/config.php");
require_once("../../../php/mysql.php");
require_once("../../../php/utils.php");
require_once("ajax/utils.php");

print_r($_POST);

verificarPermissao($id_menu, $tabela, $chave, $id_registro);

#VERFIFICA SE O FORMULÁRIO FOI ENVIADO
if ($_POST) {
	$_POST['graduacao_valor'] = str_replace("R$", "", $_POST['graduacao_valor']);
	$_POST['graduacao_valor'] = str_replace(" ", "", $_POST['graduacao_valor']);
	$_POST['graduacao_valor'] = "R$ " . $_POST['graduacao_valor'];
	$_POST['graduacao_cor'] = str_replace("#", "", $_POST['graduacao_cor']);
	extract($_POST);
	if (isset($excluir_registro)) {
		$sql = "DELETE FROM $tabela WHERE $chave = :excluir_registro";
		$stm = $coopex->prepare($sql);
		$stm->bindValue(":excluir_registro", $id_registro);

		try { //NÃO ALTERAR------------------------------------------------------------------------------------------------------------------------------------
			$google->beginTransaction();
			$stm->execute();
			$google->commit();
			gravarLog($tabela, $id_registro, 3, $sql, "$chave => $id_registro");
			echo "<script>parent.exclusaoOK()</script>";
		} catch (PDOException $e) {
			gravarLog($tabela, $id_registro, 3, $sql, $id_registro, $e->getMessage());
			echo "<script>parent.exclusaoFalha()</script>";
		}
	} else {

		// echo var_dump($_POST);

		unset($_POST['graduacao_campus']);
		unset($_POST['files']);

		if ($tabela == "graduacao_toledo") {
			unset($_POST['graduacao_estrutura_lab']);
			unset($_POST['files']);
			unset($_POST['graduacao_semana_academica']);
			unset($_POST['graduacao_semana_academica_linha1']);
			unset($_POST['graduacao_semana_academica_linha2']);
			unset($_POST['graduacao_semana_academica_ano']);
			unset($_POST['graduacao_semana_academica_periodo']);
			unset($_POST['graduacao_semana_academica_id_coopex']);
			unset($_POST['graduacao_estrutura_lab']);
			unset($_POST['graduacao_facebook']);
			unset($_POST['graduacao_mediacenter']);
			unset($_POST['graduacao_podcast']);
			unset($_POST['graduacao_spotify']);
			unset($_POST['graduacao_valor2']);
			unset($_POST['graduacao_youtube2']);
		}

		$sql = !$id_registro ? preparaSQLComParametrosVazios($_POST, $tabela) : preparaSQLComParametrosVazios($_POST, $tabela, $chave, $$chave); //MONTA A SQL PARA INSERT OU UPDATE
		$stm = $google->prepare($sql); //PREPARA A EXECUÇÃO DA QUERY
		extract($_POST); //CONVERTE O POST EM VARIÁVEIS
		$dados = '';
		foreach ($_POST as $key => $value) {
			if ($value == "" || empty($value)) {
				$stm->bindValue(":$key", utf8_decode($$key), PDO::PARAM_NULL);
			} else {
				echo $$key;
				$stm->bindValue(":$key", utf8_decode($$key));
			}
			$dados .= "$key => $value\n";
		} //PREPARA OS BINDS VINDOS POR POST 
		$registro = 0;
		$cadastro_sucesso = false;
		$operacao = !$id_registro ? 1 : 2;

		echo $sql;
		// exit;

		try {
			$coopex->beginTransaction();
			$stm->execute();
			$last_id = $coopex->lastInsertId();
			$coopex->commit();
			$id_registro = !$id_registro ? $last_id : $id_registro;

			gravarLog($tabela, $id_registro, $operacao, $sql, $dados);
			$cadastro_sucesso = true;
		} catch (PDOException $e) {
			gravarLog($tabela, !$id_registro ? 0 : $id_registro, $operacao, $sql, $dados, $e->getMessage());
			//coopex->rollback();
			$cadastro_sucesso = false;
			print "Error!: " . $e->getMessage() . "</br>" . print_r($sql) . "</br>" . $dados;
		} #--VERFICA SE O USUÁRIO ESTÁ CADASTRANDO OU ALTERANDO MONTA A SQL PARA INSERT OU UPDATE


		if ($cadastro_sucesso) {
			echo "<script>parent.cadastroOK($operacao)</script>";
		} else {
			echo "<script>parent.cadastroFalha($operacao)</script>";
		}
	}
}
?>