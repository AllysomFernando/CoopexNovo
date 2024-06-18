<pre>
<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_menu = 29; #ID DO MENU
$tabela  = "ficha_financeira.ficha_financeira"; #TABELA PRINCIPAL
$chave	 = "id_ficha_financeira"; #CAMPO CHAVE DA TABELA

$id_registro = $_POST[$chave] ? $_POST[$chave] : "0";

require_once("../../../php/config.php");
require_once("../../../php/mysql.php");
require_once("../../../php/sqlsrv.php");
require_once("../../../php/utils.php");

function tratar_valor($valor)
{
	$valor = str_replace("R$ ", "", $valor);
	$valor = str_replace(".", "", $valor);
	$valor = str_replace(",", ".", $valor);
	return $valor;
}

verificarPermissao($id_menu, $tabela, $chave, $id_registro);

#VERFIFICA SE O FORMULÁRIO FOI ENVIADO
if ($_POST) {
	extract($_POST);
	if (isset($excluir_registro)) {
		excluirRegistro($tabela, $chave, $excluir_registro);
	} else {

		if ($choque_autorizado == "true") {
			$choque_autorizado = 1;
			$_POST['choque_autorizado'] = 1;
		} else {
			$choque_autorizado = 0;
			$_POST['choque_autorizado'] = 0;
		}

		if ($pre_requisito_autorizado == "true") {
			$pre_requisito_autorizado = 1;
			$_POST['pre_requisito_autorizado'] = 1;
		} else {
			$pre_requisito_autorizado = 0;
			$_POST['pre_requisito_autorizado'] = 0;
		}

		$enviado_para_aprovacao_reducao = false;

		if (isset($_POST['valores_valor'])) {
			for ($i = 0; $i < count($_POST['valores_valor']); $i++) {
				$valor = tratar_valor($_POST['valores_valor'][$i]);
				$_POST['parcela_' . ($i + 1)] = $valor;
			}
		}

		if (isset($_POST['valores_valordp'])) {
			for ($i = 0; $i < count($_POST['valores_valordp']); $i++) {
				$valor = tratar_valor($_POST['valores_valordp'][$i]);
				$_POST['parcela_' . ($i + 1) . "dp"] = $valor;
			}
		}

		if (isset($_POST['valores_valor_diferenca'])) {
			for ($i = 0; $i < count($_POST['valores_valor_diferenca']); $i++) {
				$valor = tratar_valor($_POST['valores_valor_diferenca'][$i]);
				$_POST['diferenca' . ($i + 2)] = $valor;
			}
		}

		if (isset($_POST['reembolso'])) {
			$_POST['reembolso'] = tratar_valor($_POST['reembolso']);
		}

		#TRATAMENTOS DOS DADOS VINDOS DO FORMULÁRIOS
		#CAMPOS QUE DEVEM SER DESCARTADOS DO BIND
		unset($_POST['valores_valor']);
		unset($_POST['valores_valordp']);
		unset($_POST['valores_valor_diferenca']);

		//print_r($_POST);
		//exit;

		#VERFICA SE ESTÁ CADASTRANDO OU ALTERANDO
		if (!$id_registro) {
			$_POST['data_cadastro'] = date("Y-m-d H:i:s");
			$_POST['id_pessoa_cadastro'] = $_SESSION['coopex']['usuario']['id_pessoa'];
		}
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
		} #--VERFICA SE O USUÁRIO ESTÁ CADASTRANDO OU ALTERANDO MONTA A SQL PARA INSERT OU UPDATE


		if ($operacao == 1) {
			$sql = "INSERT INTO `ficha_financeira`.`ficha_financeira_etapa` ( `id_ficha_financeira`, `id_etapa`, `data_cadastro`, `obs` )
						VALUES ($id_registro, 1, now(), '')";
			$res = $coopex->query($sql);

			$sql = "UPDATE ficha_financeira.ficha_financeira SET id_etapa=1 WHERE (id_ficha_financeira=$id_registro)";
			$coopex->query($sql);
		}


		$sql = "DELETE FROM	ficha_financeira.ficha_financeira_disciplinas WHERE id_ficha_financeira = $id_registro ";
		gravarLog("ficha_financeira.ficha_financeira_disciplinas", $id_registro, 3, $sql, $id_registro);
		$coopex->query($sql);

		foreach ($_SESSION['ficha_financeira']['disciplinas'] as $key => $value) {
			//print_r($_SESSION['ficha_financeira']['disciplinas'][$key]);
			$dp 				= $_SESSION['ficha_financeira']['disciplinas'][$key]['dp'];
			$id_disciplina 		= $_SESSION['ficha_financeira']['disciplinas'][$key]['id_disciplina'];
			$carga_horaria 		= $_SESSION['ficha_financeira']['disciplinas'][$key]['carga_horaria'];
			$fora_pacote 		= $_SESSION['ficha_financeira']['disciplinas'][$key]['fora_pacote'];
			$id_equivalencia 	= $_SESSION['ficha_financeira']['disciplinas'][$key]['equivalencia'];
			$id_classe 			= $_SESSION['ficha_financeira']['disciplinas'][$key]['id_classe'];

			$valor_desconto = isset($_SESSION['ficha_financeira']['disciplinas'][$key]['valor_desconto']) ? tratar_valor($_SESSION['ficha_financeira']['disciplinas'][$key]['valor_desconto']) : 0;

			$sql = "REPLACE INTO ficha_financeira.ficha_financeira_disciplinas (id_ficha_financeira, id_disciplina, id_equivalencia, carga_horaria, dp, fora_do_pacote, valor_desconto, id_classe) VALUES ($id_registro, '$id_disciplina', '$id_equivalencia', '$carga_horaria', '$dp', '$fora_pacote', '$valor_desconto', '$id_classe');";
			$coopex->query($sql);
			gravarLog("ficha_financeira.ficha_financeira_disciplinas", $id_registro, 1, $sql, $id_registro);
		}

		$sql = "SELECT
						id_pessoa, id_campus 
					FROM
						pessoa 
					WHERE
						id_pessoa = " . $id_pessoa;
		$pessoa = $coopex->query($sql);
		if ($pessoa->rowCount() == 0) {
			$sql = "select * from integracao..view_integracao_usuario where id_pessoa = '$id_pessoa' order by ra";
			$res = mssql_query($sql);
			$row = mssql_fetch_assoc($res);

			$nome = str_replace("'", "", $row['nome']);

			$sql = "INSERT INTO `coopex`.`pessoa`(`id_pessoa`, `nome`, `usuario`, `email`, `id_tipo_usuario`, `cpf`, `avatar`, `id_campus`, `ra`)
						VALUES (" . $row['id_pessoa'] . ", '" . $nome . "', '" . $row['usuario'] . "', '" . $row['email'] . "', 6, '" . $row['cpf'] . "', null, '" . $row['id_faculdade'] . "', '" . $row['ra'] . "')";
			$coopex->query($sql);
		}


		if ($cadastro_sucesso) {
			echo "<script>parent.cadastroOK($operacao, $id_registro)</script>";
		} else {
			echo "<script>parent.cadastroFalha($operacao)</script>";
		}
	}
}
?>