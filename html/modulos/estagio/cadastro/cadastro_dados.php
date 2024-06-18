<pre>
<?php
session_start();

print_r($_SESSION['coopex']['usuario']['permissao'][115]);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_menu = 115; #ID DO MENU
$tabela = "estagio.estagio"; #TABELA PRINCIPAL
$chave = "id_estagio"; #CAMPO CHAVE DA TABELA

$id_registro = $_POST[$chave] ? $_POST[$chave] : "0";

require_once("../../../php/config.php");
require_once("../../../php/mysql.php");
require_once("../../../php/utils.php");

$_POST['data_cadastro'] = date_create()->format('Y-m-d H:i:s');
print_r($_POST);

verificarPermissao($id_menu, $tabela, $chave, $id_registro);

#VERFIFICA SE O FORMULÁRIO FOI ENVIADO
if ($_POST) {
	extract($_POST);
	if (isset($excluir_registro)) {
		excluirRegistro($tabela, $chave, $excluir_registro);
	} else {
		extract($_POST); //CONVERTE O POST EM VARIÁVEIS

		$enviado_para_aprovacao_reducao = false;

		unset($_POST['cronograma']);


		#VERFICA SE ESTÁ CADASTRANDO OU ALTERANDO
		if (!$id_registro) {
			$_POST['data_cadastro'] = date("Y-m-d H:i:s");
			$_POST['id_pessoa'] = $_SESSION['coopex']['usuario']['id_pessoa'];
		}
		$sql = !$id_registro ? preparaSQL($_POST, $tabela) : preparaSQL($_POST, $tabela, $chave, $$chave); //MONTA A SQL PARA INSERT OU UPDATE
		$stm = $coopex->prepare($sql); //PREPARA A EXECUÇÃO DA QUERY
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
		} #--VERFICA SE O USUÁRIO ESTÁ CADASTRANDO OU ALTERANDO MONTA A SQL PARA INSERT OU UPDATE

		#CRONOGRAMA
		if (isset($cronograma)) {
			$aux = json_decode($cronograma);
			for ($i = 0; $i < count($aux); $i++) {
				$acao = $aux[$i]->acao;

				$id_cronograma = $aux[$i]->id_cronograma;
				$data = $aux[$i]->data != "" ? $aux[$i]->data : date("Y-m-d");
				$carga_horaria  = $aux[$i]->carga_horaria . ":00";
				$descricao  	  = $aux[$i]->descricao;

				if ($acao == "i") {
					echo $sql = "INSERT INTO `estagio`.`cronograma`(`id_estagio`, `data`, `carga_horaria`, `descricao`)
								VALUES ($id_registro, '$data', '$carga_horaria', '$descricao')";
					$coopex->query($sql);
					gravarLog('estagio.cronograma', $id_registro, 1, $sql, '');
				} else if ($acao == "u") {
					#SE ESTIVER EDITANDO ALTERA OS REGISTROS SE NÃO INSERE
					if (isset($_GET['id']) || $id_cronograma != "") {
						echo $sql = "UPDATE `estagio`.`cronograma` SET `data` = '$data', `carga_horaria` = '$carga_horaria', `descricao` = '$descricao' WHERE `id_cronograma` = $id_cronograma";
						$coopex->query($sql);
						gravarLog('estagio.cronograma', $id_registro, 2, $sql, '');
					} else {
						echo $sql = "INSERT INTO `estagio`.`cronograma`(`id_estagio`, `data`, `carga_horaria`, `descricao`)
						VALUES ($id_registro, '$data', '$carga_horaria', '$descricao')";
						$coopex->query($sql);
						gravarLog('estagio.cronograma', $id_registro, 1, $sql, '');
					}
				} else if ($acao == "d") {
					if ($id_cronograma) {
						echo $sql = "DELETE FROM `estagio`.`cronograma` WHERE `id_cronograma` = $id_cronograma";
						$coopex->query($sql);
						gravarLog('estagio.cronograma', $id_registro, 3, $sql, '');
					}
				}
			}
		} #--CRONOGRAMA

		if ($cadastro_sucesso) {
			echo "<script>parent.cadastroOK($operacao)</script>";
		} else {
			echo "<script>parent.cadastroFalha($operacao)</script>";
		}
	}
}
?>