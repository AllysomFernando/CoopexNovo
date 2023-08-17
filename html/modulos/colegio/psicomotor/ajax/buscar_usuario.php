<?php session_start();


	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	require_once("../../../../php/sqlsrv.php");

	$id_pessoa = $_GET['id_usuario'];
	
	$sql = "SELECT
				pes_id_pessoa,
				rtrim(alu_nu_matricula) AS ra,
				rtrim(pes_nm_pessoa) AS nome,
				rtrim(crs_nm_resumido) AS curso,
				ser_ds_serie AS serie,
				sap_ds_situacao AS situacao,
				rca_id_registro_curso,
				ser_id_serie,
				Nasc
			FROM
				registro..PES_pessoa
			INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
			INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_aluno = pes_id_pessoa
			INNER JOIN academico..CRS_curso ON crs_id_curso = rca_id_curso
			INNER JOIN integracao..view_integracao_usuario ON id_pessoa = pes_id_pessoa 
			INNER JOIN academico..SAP_situacao_aluno_periodo_letivo_view ON rca_id_registro_curso = sap_id_registro_curso
			INNER JOIN academico..PEL_periodo_letivo ON sap_id_periodo_letivo = pel_id_periodo_letivo
			INNER JOIN academico..IAP_informacoes_aluno_periodo_view ON iap_id_registro_curso = rca_id_registro_curso
			INNER JOIN academico..SER_serie ON iap_id_serie = ser_id_serie
			WHERE
				iap_id_periodo_letivo = 5000000241 --and sap_ds_situacao = 'Sem Status'
			AND pes_id_pessoa = $id_pessoa
			AND EXISTS (
				SELECT
					1
				FROM
					financeiro..cta_contrato_academico,
					financeiro..ctr_contrato,
					financeiro..CPL_contrato_periodo_letivo,
					financeiro..prc_parcela,
					financeiro..ttf_titulo_financeiro
				WHERE
					cta_id_contrato = ctr_id_contrato
				AND ctr_id_cliente = rca_id_aluno
				AND cpl_id_periodo_letivo = pel_id_periodo_letivo
				AND cpl_id_contrato = cta_id_contrato
				AND prc_id_contrato = cta_id_contrato
				AND ttf_id_parcela = prc_id_parcela
				AND ttf_st_situacao IN ('P', 'L', 'G', 'R', 'S')
			) --Em Compensação, liberado, Pago, Renegociado e Sem valo */
			AND EXISTS (
				SELECT
					1
				FROM
					academico..MTR_matricula
				WHERE
					mtr_id_periodo_letivo = pel_id_periodo_letivo
				AND mtr_id_registro_curso = rca_id_registro_curso
				AND mtr_id_situacao_matricula = 1000000002
				-- AND mtr_id_periodo_letivo = 5000000241
			)
			ORDER BY
				crs_nm_resumido,
				ser_ds_serie,
				pes_nm_pessoa";
	$res = mssql_query($sql);
	$row = mssql_fetch_assoc($res);

	$id_serie = $row['ser_id_serie'];

?>
<style>
	table tr td{
		vertical-align: middle !important;
	}
</style>

<table class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
	<thead class="bg-secondary-600">
		<tr>
			<th>RA</th>
			<th>Nome</th>
			<th>Curso</th>
			<th>Série</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><strong><?= utf8_encode($row['ra'])?></strong></td>
			<td><strong><?= utf8_encode($row['nome'])?></strong></td>
			<td><?= utf8_encode($row['curso'])?></td>
			<td><?= utf8_encode($row['serie'])?></td>
		</tr>
	</tbody>
</table>
