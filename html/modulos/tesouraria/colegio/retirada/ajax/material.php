<?php session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../../../../../php/mysql.php");
require_once("../../../../../php/utils.php");

require_once("../../../../../php/sqlsrv.php");

$id_periodo_letivo = 5000000244;

$id_pessoa = $_GET['id_usuario'];

$sql = "SELECT
				ano 
			FROM
				tesouraria.material 
			GROUP BY
				ano 
			ORDER BY
				ano DESC
			LIMIT 1";
$res = $coopex->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$ano = $row->ano;

$sql = "SELECT
			pes_id_pessoa,
			rtrim(alu_nu_matricula) AS ra,
			rtrim(pes_nm_pessoa) AS nome,
			rtrim(crs_nm_resumido) AS curso,
			ser_ds_serie AS serie,
			sap_ds_situacao AS situacao,
			rca_id_registro_curso,
			ser_id_serie,
			pel_id_periodo_letivo,
			rca_id_registro_curso
		FROM
			registro..PES_pessoa
		INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
		INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_aluno = pes_id_pessoa
		INNER JOIN academico..CRS_curso ON crs_id_curso = rca_id_curso
		INNER JOIN academico..SAP_situacao_aluno_periodo_letivo_view ON rca_id_registro_curso = sap_id_registro_curso
		INNER JOIN academico..PEL_periodo_letivo ON sap_id_periodo_letivo = pel_id_periodo_letivo
		INNER JOIN academico..IAP_informacoes_aluno_periodo_view ON iap_id_registro_curso = rca_id_registro_curso
		INNER JOIN academico..SER_serie ON iap_id_serie = ser_id_serie
		WHERE
			iap_id_periodo_letivo = $id_periodo_letivo
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
		)
	
		ORDER BY
			crs_nm_resumido,
			ser_ds_serie,
			pes_nm_pessoa";
$res = mssql_query($sql);
//$row = mssql_fetch_assoc($res);


$matriculado = 0;
while ($row = mssql_fetch_assoc($res)) {
	if ($row['pel_id_periodo_letivo'] == $id_periodo_letivo) {
		$matriculado = 1;
	}
	$id_serie = $row['ser_id_serie'];
}
$res = mssql_query($sql);
$row = mssql_fetch_assoc($res);

/*
$sql2 = "SELECT
		ser_id_serie AS id_serie
		FROM
		academico..HIS_historico_ingresso_saida a
		INNER JOIN academico..FMI_forma_ingresso b ON a.his_id_forma_ingresso = fmi_id_forma_ingresso
		INNER JOIN academico..PEL_periodo_letivo PEL0 ON his_id_periodo_inicio = PEL0.pel_id_periodo_letivo
		INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_registro_curso = his_id_registro_curso
		INNER JOIN academico..CRS_curso ON rca_id_curso = crs_id_curso
		INNER JOIN academico..COL_colegiado ON crs_id_unidade = col_id_colegiado
		INNER JOIN academico..FAC_faculdade ON fac_id_faculdade = col_id_faculdade
		INNER JOIN academico..SAP_situacao_aluno_periodo_letivo_view SAP0 ON rca_id_registro_curso = SAP0.sap_id_registro_curso
		INNER JOIN academico..IAP_informacoes_aluno_periodo_view ON iap_id_registro_curso = rca_id_registro_curso
		INNER JOIN academico..SER_serie ON iap_id_serie = ser_id_serie
		INNER JOIN registro..PES_pessoa ON PES_pessoa.pes_id_pessoa = rca_id_aluno
		INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
		INNER JOIN academico..TCU_turmas_curso ON tcu_id_turma_curso = rca_id_turma_curso 
		WHERE
		pel_ds_compacta = '20240' 
		AND SAP0.sap_id_periodo_letivo = PEL0.pel_id_periodo_letivo 
		AND fac_id_faculdade = 1000000006 
		AND PES_ID_PESSOA = $id_pessoa ";
$res2 = mssql_query($sql2);
$row2 = mssql_fetch_assoc($res2);

$id_serie = $row2['id_serie'];*/

//selecionas os boletos das matrículas
$sql = "SELECT
				*
			FROM
				tesouraria.material
			WHERE
				id_serie = $id_serie
			AND
				ano = $ano";

$material = $coopex->query($sql);

$situacao['A'] = "Aberto";
$situacao['C'] = "Cancelado";
$situacao['E'] = "Pendente";
$situacao['G'] = "Em compensação";
$situacao['L'] = "Liberado";
$situacao['P'] = "Pago";
$situacao['R'] = "Renegociado";
$situacao['S'] = "Sem valor";

$sql_observacao = "SELECT
				*
			FROM
				tesouraria.observacao
			WHERE
				id_pessoa = $id_pessoa";
$observacao = $coopex->query($sql_observacao);
$row_observacao = $observacao->fetch(PDO::FETCH_OBJ);

$sql_autorizacao = "SELECT
						* 
					FROM
						tesouraria.autorizacao a
					INNER JOIN coopex.pessoa b ON a.id_responsavel = b.id_pessoa 
					WHERE
						a.id_pessoa = $id_pessoa";
$autorizacao = $coopex->query($sql_autorizacao);
$row_autorizacao = $autorizacao->fetch(PDO::FETCH_OBJ);

?>
<style>
	table tr td {
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
			<th>Situa&ccedil;&atilde;o</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><strong><?= utf8_encode($row['ra']) ?></strong></td>
			<td><strong><?= utf8_encode($row['nome']) ?></strong></td>
			<td><?= utf8_encode($row['curso']) ?></td>
			<td><?= utf8_encode($row['serie']) ?></td>
			<td class="<?= $matriculado ? "bg-success-50" : "bg-danger-50" ?>">
				<?= $matriculado ? "Matriculado" : "Não Matriculado" ?></td>
		</tr>
	</tbody>
</table>

<table class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
	<thead class="bg-secondary-600">
		<tr>
			<th>Material</th>
			<th>Valor</th>
			<th class="text-center">Situação</th>
			<th>Data Pagamento</th>
		</tr>
	</thead>
	<tbody>
		<?
		$sql = "SELECT
						ttf_ds_titulo AS material,
						ttf_vl_face AS valor,
						ttf_st_situacao AS situacao,
						FORMAT (
							ttf_dt_cadastro,
							'yyyy-MM-dd HH:mm:ss'
						) AS data_pagamento
					FROM
						financeiro..TTF_titulo_financeiro
					WHERE
						ttf_id_tipo_titulo = 1000000566
					AND ttf_dt_referencia >= '2024-01-01 00:00:00.000'
					AND ttf_st_situacao IN ('P', 'L', 'G', 'R', 'S')
					AND ttf_id_cliente_fornecedor = $id_pessoa";
		$res = mssql_query($sql);

		while ($row = mssql_fetch_assoc($res)) {
			?>
			<tr>
				<td><strong><?= utf8_encode($row['material']) ?></strong></td>
				<td><strong>R$ <?= number_format($row['valor'], 2, ',', '.') ?></strong></td>
				<td class="text-center">
					<span class="badge badge-<?php echo $row['situacao'] == "P" ? "success" : "warning" ?> badge-pill">
						<?= $situacao[$row['situacao']] ?>
					</span>
				</td>
				<td><?= converterDataHora($row['data_pagamento']) ?></td>
			</tr>
		<?
		}
		?>
	</tbody>
</table>

<hr>

<iframe class="d-none" name="dados" src=""
	style="position: fixed; z-index: 999999999999; width: 30%; background-color: #fff; top: 0; left: 0; height: 300px"></iframe>

<?
if (!$matriculado) {
	if ($autorizacao->rowCount()) {
		?>
		<div class="col-md-12 mb-3 mt-2">
			Autorizado em: <strong><?= converterDataHora($row_autorizacao->data) ?></strong><br>
			Por: <strong><?= $row_autorizacao->nome ?></strong>
		</div>
		<hr>
	<?
	} else {


		?>
		<form method="post" id="form_autorizacao" target="dados"
			action="modulos/tesouraria/colegio/retirada/ajax/autorizacao.php">
			<input type="hidden" name="id_pessoa" value="<?= $id_pessoa ?>">
			<div class="custom-control custom-checkbox ">
				<input type="checkbox" class="custom-control-input" id="botao_autorizar">
				<label class="custom-control-label" for="botao_autorizar">Autorizar Retirada</label>
			</div>
			<textarea required name="observacao"
				class="form-control col-md-12"><?php echo isset($row_autorizacao->observacao) ? utf8_encode($row_autorizacao->observacao) : "" ?></textarea>
			<button class="form-control btn-primary mt-3" type="submit">Salvar</button>
		</form>
	<?
	}
}
?>

<?
if ($autorizacao->rowCount()) {
	?>
	<form method="post" target="dados" action="modulos/tesouraria/colegio/retirada/ajax/observacao.php">
		<input type="hidden" name="id_pessoa" value="<?= $id_pessoa ?>">
		<div class="col-md-12 mb-3 mt-2">
			<label class="form-label" for="validationCustom03">Observações</label>
			<textarea name="observacao"
				class="form-control col-md-12"><?php echo isset($row_observacao->observacao) ? utf8_encode($row_observacao->observacao) : "" ?></textarea>
			<br>
			<button class="form-control btn-primary" type="submit">Salvar</button>
		</div>

	</form>

<?
}
?>

<hr>

<table id="tabela_material" class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline"
	style="<?= $autorizacao->rowCount() ? "" : "" ?>">  <!--TODO: verificar condição -->
	<thead class="bg-primary-600">
		<tr>
			<th>Material</th>
			<th class="text-center">Estoque</th>
			<th>Retidado</th>
			<th>Data</th>
			<th>Usuário</th>
		</tr>
	</thead>
	<tbody>
		<?php
		while ($row = $material->fetch(PDO::FETCH_OBJ)) {

			$sql2 = "SELECT
							*
						FROM
							tesouraria.retirada a
						INNER JOIN coopex.pessoa b ON a.id_responsavel = b.id_pessoa
						WHERE
							id_material = $row->id_material
						AND
							a.id_pessoa = $id_pessoa";
			$material2 = $coopex->query($sql2);
			$row_retirado = $material2->fetch(PDO::FETCH_OBJ);
			//print_r($row_retirado);
			$check = $material2->rowCount() ? "checked" : "";
			$retirado = $material2->rowCount() ? "Retirado" : "Não retirado";
			$data_retirada = $material2->rowCount() ? converterDataHora($row_retirado->data_retirada) : "";

			$usuario = isset($row_retirado->usuario) ? $row_retirado->usuario : "";
			?>
			<tr>
				<td><?= utf8_encode($row->material) ?></td>
				<td class="text-center"><?= ($row->quantidade) ?></td>
				<td>
					<div class="custom-control custom-switch">
						<input <?= !$row->quantidade ? "disabled" : "" ?> 	<?= $check ?>
							onchange="retirar(<?= $row->id_material ?>, this)" type="checkbox" class="custom-control-input"
							id="<?= $row->id_material ?>">
						<label class="custom-control-label" for="<?= $row->id_material ?>"><?= $retirado ?></label>
					</div>
				</td>
				<td><?= $data_retirada ?></td>
				<td><?= $usuario ?></td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>

<div class="modal fade" id="pagamentos_modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document" id="pagamentos_modal_conteudo"></div>
</div>

<div class="panel-container show">
	<div
		class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
		<a target="_blank" href="https://coopex.fag.edu.br/tesouraria/colegio/retirada/contrato/<?= $id_pessoa ?>"
			class="btn btn-primary" type="submit">Contrato</a>
		<a target="_blank" href="https://coopex.fag.edu.br/tesouraria/colegio/retirada/declaracao/<?= $id_pessoa ?>"
			class="btn btn-primary ml-auto" type="submit">Declaração de Retirada</a>
	</div>
</div>

<script>


	function autorizar() {
		$("#tabela_material").show();
		$("#botao_autorizar").attr('disabled', 'disabled');
	}

	function retirar(id, select) {

		console.log(select.checked)

		if (select.checked) {
			$.getJSON("modulos/tesouraria/colegio/retirada/ajax/retirar.php", {
				id_material: id,
				id_pessoa: <?= $id_pessoa ?>
			})
				.done(function (json) {
					$("#titulos_em_aberto_resultado").load("modulos/tesouraria/colegio/retirada/ajax/material.php?id_usuario=<?= $_GET['id_usuario'] ?>");
				})
				.fail(function (jqxhr, textStatus, error) {
					var err = textStatus + ", " + error;
					console.log("Request Failed: " + err);
				});
		} else {
			$.getJSON("modulos/tesouraria/colegio/retirada/ajax/devolver.php", {
				id_material: id,
				id_pessoa: <?= $id_pessoa ?>
			})
				.done(function (json) {
					$("#titulos_em_aberto_resultado").load("modulos/tesouraria/colegio/retirada/ajax/material.php?id_usuario=<?= $_GET['id_usuario'] ?>");
				})
				.fail(function (jqxhr, textStatus, error) {
					var err = textStatus + ", " + error;
					console.log("Request Failed: " + err);
				});
		}

	}
</script>