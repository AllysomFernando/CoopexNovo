<pre>
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_menu = 24; //ID DO MENU
$tabela = "colegio.cdt_matricula"; //TABELA PRINCIPAL
$chave = "id_matricula"; //CAMPO CHAVE DA TABELA

$id_registro = isset($_POST[$chave]) ? $_POST[$chave] : "0";

require_once("../../../php/config.php");
require_once("../../../php/mysql.php");
require_once("../../../php/utils.php");
require_once("../../../php/sqlsrv.php");

//verificarPermissao($id_menu, $tabela, $chave, $id_registro);

//VERFIFICA SE O FORMULÁRIO FOI ENVIADO
if ($_POST) {

	//print_r($_POST);

	extract($_POST);

	$id_pessoa_matricula = implode(",", $_POST['id_pessoa_matricula']);

	$pessoa = $_POST['id_pessoa_matricula'];


	if(count($_REQUEST['id_pessoa_matricula']) > 1){
		$subtotal = 70 * count($_REQUEST['id_pessoa_matricula']);
		$desconto = $subtotal - (50 * count($_REQUEST['id_pessoa_matricula']));
		$total = $subtotal - $desconto;
	} else {
		$subtotal = 70;
		$desconto = 0;
		$total = 70;
	}

	$_POST['data_matricula'] = date("Y-m-d H:i:s");
	//$_POST['data_vencimento'] 	= converterData($data_vencimento);
	$_POST['id_pessoa'] = $_SESSION['coopex']['usuario']['id_pessoa'];
	$_POST['valor'] = $total;

	unset($_POST['id_pessoa_matricula']);
	unset($_POST['id_modalidade_matricula']);

	//print_r($_POST);

	//VERFICA SE ESTÁ CADASTRANDO OU ALTERANDO
	$sql = !$id_registro ? preparaSQL($_POST, $tabela) : preparaSQL($_POST, $tabela, $chave, $$chave); //MONTA A SQL PARA INSERT OU UPDATE
	$stm = $coopex->prepare($sql); //PREPARA A EXECUÇÃO DA QUERY
	extract($_POST); //CONVERTE O POST EM VARIÁVEIS
	$dados = '';
	foreach ($_POST as $key => $value) {
		if ($value) {
			$stm->bindValue(":$key", $$key);
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

	for ($i = 0; $i < count($pessoa); $i++) {
		$id_pes = $pessoa[$i];

		$sql3 = "SELECT
					pes_id_pessoa AS id_pessoa,
					pes_nm_pessoa AS nome,
					crs_id_curso AS id_curso,
					ser_id_serie AS id_serie,
					rca_id_turma_curso AS id_turma 
				FROM
					academico..HIS_historico_ingresso_saida a
					INNER JOIN academico..FMI_forma_ingresso b ON a.his_id_forma_ingresso = fmi_id_forma_ingresso
					INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_registro_curso = a.his_id_registro_curso
					INNER JOIN academico..CRS_curso ON rca_id_curso = crs_id_curso
					INNER JOIN academico..COL_colegiado ON crs_id_unidade = col_id_colegiado
					INNER JOIN academico..FAC_faculdade ON fac_id_faculdade = col_id_faculdade
					INNER JOIN academico..SAP_situacao_aluno_periodo_letivo_view SAP0 ON rca_id_registro_curso = SAP0.sap_id_registro_curso
					INNER JOIN academico..PEL_periodo_letivo PEL0 ON PEL0.pel_id_periodo_letivo = SAP0.sap_id_periodo_letivo
					INNER JOIN academico..IAP_informacoes_aluno_periodo_view ON iap_id_registro_curso = rca_id_registro_curso
					INNER JOIN academico..SER_serie ON iap_id_serie = ser_id_serie
					INNER JOIN registro..PES_pessoa ON PES_pessoa.pes_id_pessoa = rca_id_aluno
					INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
					INNER JOIN academico..TCU_turmas_curso ON tcu_id_turma_curso = rca_id_turma_curso
					INNER JOIN academico..AUE_aluno_unidade_ensino ON aue_id_aluno = pes_id_pessoa 
				WHERE
					pel_ds_compacta = '20240' 
					AND fac_id_faculdade = 1000000006 
					AND iap_id_periodo_letivo = SAP0.sap_id_periodo_letivo 
					AND PES_ID_PESSOA = $id_pes";

		$res3 = mssql_query($sql3);
		$row3 = mssql_fetch_object($res3);
	
		$sql = "INSERT INTO `colegio`.`cdt_aluno_matricula` (`id_matricula`, `id_pessoa`, `nome`, `id_curso`, `id_serie`, `id_turma`) VALUES ($id_registro, $row3->id_pessoa, '$row3->nome', $row3->id_curso, $row3->id_serie, $row3->id_turma)";
		$coopex->query($sql);
	}



	if ($cadastro_sucesso) {
		echo "<script>parent.matriculaOK($id_registro)</script>";
	} else {
		echo "<script>parent.matriculaFalha()</script>";
	}

	//print_r($_POST);
	//exit;
}
?>