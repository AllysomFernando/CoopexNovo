<?php

require_once("php/sqlsrv.php");
//require_once("./php/utils.php");

//$id_pessoa = $_GET['id_usuario'];

$vestibular = isset($_GET['id']) ? "vestibular_" . $_GET['id'] : "vestibular";
$periodo = isset($_GET['id']) ? $_GET['id'] : "20240";

// Função para obter o número de matrículas com base na faculdade
function obterNumeroMatriculas($periodoIngresso)
{
	$sql = "SELECT COUNT
				( distinct rca_id_registro_curso ) AS total 
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
				pel_ds_compacta = '$periodoIngresso' 
				AND SAP0.sap_id_periodo_letivo = PEL0.pel_id_periodo_letivo 
				AND fac_id_faculdade = 1000000006
				And his_dt_criacao is not null
				and SAP0.sap_ds_situacao = 'Matriculado'
				AND NOT EXISTS (
				SELECT
					1 
				FROM
					academico..SAP_situacao_aluno_periodo_letivo_view SAP1 
				WHERE
					SAP1.sap_id_registro_curso = SAP0.sap_id_registro_curso 
					AND SAP1.sap_id_periodo_letivo <> SAP0.sap_id_periodo_letivo 
				AND SAP1.sap_id_periodo_letivo = ( SELECT PEL1.pel_id_periodo_letivo FROM academico..PEL_periodo_letivo PEL1 WHERE PEL1.pel_id_periodo_sucessor = PEL0.pel_id_periodo_letivo ) 
				)";

	$res = mssql_query($sql);
	$row = mssql_fetch_object($res);
	return $row->total;
}

// Obter o número de matrículas para Cascavel (faculdade 1000000002)
$matriculas_total_cascavel = obterNumeroMatriculas($periodo);


$matriculas_total = $matriculas_total_cascavel;


?>
<style>
	.debug {
		border: solid red 1px;
	}

	.chart-container {
		position: relative;
		margin: auto;
		height: 80vh;
		width: 80vw;
	}
</style>

<main id="js-page-content" role="main" class="page-content">

	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-chart-area'></i> Matrículas <span class='fw-300'>NOVAS</span>
		</h1>
	</div>

	<div class="row">
		<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
			<div class="p-3 bg-danger-300 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $matriculas_total ?>
						<small class="m-0 l-h-n">Matrículas Novas 2024</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>
		<?
		$sql = "SELECT COUNT
		( distinct rca_id_registro_curso ) AS total,
		crs_nm_resumido 
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
		And his_dt_criacao is not null
		and SAP0.sap_ds_situacao = 'Matriculado'
		AND NOT EXISTS (
		SELECT
			1 
		FROM
			academico..SAP_situacao_aluno_periodo_letivo_view SAP1 
		WHERE
			SAP1.sap_id_registro_curso = SAP0.sap_id_registro_curso 
			AND SAP1.sap_id_periodo_letivo <> SAP0.sap_id_periodo_letivo 
			AND SAP1.sap_id_periodo_letivo = ( SELECT PEL1.pel_id_periodo_letivo FROM academico..PEL_periodo_letivo PEL1 WHERE PEL1.pel_id_periodo_sucessor = PEL0.pel_id_periodo_letivo ) 
		) 
	GROUP BY
		crs_nm_resumido";

		$res = mssql_query($sql);
		$cor = 500;
		while ($row = mssql_fetch_assoc($res)) {
		?>
			<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
				<div class="p-3 bg-success-<?= $cor ?> rounded overflow-hidden position-relative text-white mb-g">
					<div class="">
						<h3 class="display-4 d-block l-h-n m-0 fw-500">
							<?= $row['total'] ?>
							<small class="m-0 l-h-n"><?= utf8_encode($row['crs_nm_resumido']) ?></small>
						</h3>
					</div>
					<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
				</div>
			</div>
		<?
			$cor += 100;
		}
		?>


		<?
		$sql = "SELECT COUNT
		( DISTINCT rca_id_registro_curso ) AS total,

	CASE
			tcu_ch_matutino 
			WHEN 'S' THEN
			'MATUTINO' ELSE 'VESPERTINO' 
		END turno 
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
		And his_dt_criacao is not null
		and SAP0.sap_ds_situacao = 'Matriculado'
		AND NOT EXISTS (
		SELECT
			1 
		FROM
			academico..SAP_situacao_aluno_periodo_letivo_view SAP1 
		WHERE
			SAP1.sap_id_registro_curso = SAP0.sap_id_registro_curso 
			AND SAP1.sap_id_periodo_letivo <> SAP0.sap_id_periodo_letivo 
			AND SAP1.sap_id_periodo_letivo = ( SELECT PEL1.pel_id_periodo_letivo FROM academico..PEL_periodo_letivo PEL1 WHERE PEL1.pel_id_periodo_sucessor = PEL0.pel_id_periodo_letivo ) 
		) 
	GROUP BY
		tcu_ch_matutino 
	ORDER BY
		turno";

		$res = mssql_query($sql);
		$cor = 500;
		while ($row = mssql_fetch_assoc($res)) {
		?>
			<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
				<div class="p-3 bg-primary-<?= $cor ?> rounded overflow-hidden position-relative text-white mb-g">
					<div class="">
						<h3 class="display-4 d-block l-h-n m-0 fw-500">
							<?= $row['total'] ?>
							<small class="m-0 l-h-n"><?= utf8_encode($row['turno']) ?></small>
						</h3>
					</div>
					<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
				</div>
			</div>
		<?
			$cor += 100;
		}
		?>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<div id="panel-4" class="panel">
				<div class="panel-hdr">
					<h2>
						Matrículas<span class="fw-300"><i>por turno</i></span>
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th style="text-align: left !important;">Curso</th>
									<th>Turno</th>
									<th>Matriculados</th>
							</thead>
							<tbody>
								<?
								$sql = "SELECT COUNT
											( DISTINCT rca_id_registro_curso ) AS total,
											crs_nm_resumido,
										CASE
												tcu_ch_matutino 
												WHEN 'S' THEN
												'MATUTINO' ELSE 'VESPERTINO' 
											END turno 
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
											And his_dt_criacao is not null
											and SAP0.sap_ds_situacao = 'Matriculado'
											AND NOT EXISTS (
											SELECT
												1 
											FROM
												academico..SAP_situacao_aluno_periodo_letivo_view SAP1 
											WHERE
												SAP1.sap_id_registro_curso = SAP0.sap_id_registro_curso 
												AND SAP1.sap_id_periodo_letivo <> SAP0.sap_id_periodo_letivo 
												AND SAP1.sap_id_periodo_letivo = ( SELECT PEL1.pel_id_periodo_letivo FROM academico..PEL_periodo_letivo PEL1 WHERE PEL1.pel_id_periodo_sucessor = PEL0.pel_id_periodo_letivo ) 
											) 
										GROUP BY
											crs_nm_resumido,
											tcu_ch_matutino 
										ORDER BY
											crs_nm_resumido,
											turno";

								$res = mssql_query($sql);
								while ($row = mssql_fetch_object($res)) {
								?>
									<tr style="background-color: <?= $cor ?>;">
										<td style="text-align: left !important;"><?= utf8_encode($row->crs_nm_resumido) ?></b></td>
										<td style="text-align: left !important;"><?= utf8_encode($row->turno) ?></td>
										<td><b><?= $row->total ?></b></td>
									</tr>
								<?
								}
								?>

							</tbody>

						</table>
						<!-- datatable end -->
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<div id="panel-4" class="panel">
				<div class="panel-hdr">
					<h2>
						Matrículas<span class="fw-300"><i>por série</i></span>
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th style="text-align: left !important;">Curso</th>
									<th>Turno</th>
									<th>Série</th>
									<th>Matriculados</th>
							</thead>
							<tbody>
								<?
								$sql = "SELECT COUNT
											( DISTINCT rca_id_registro_curso ) AS total,
											crs_nm_resumido,
											ser_ds_serie,
										CASE
												tcu_ch_matutino 
												WHEN 'S' THEN
												'MATUTINO' ELSE 'VESPERTINO' 
											END turno 
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
											And his_dt_criacao is not null
											and SAP0.sap_ds_situacao = 'Matriculado'
											AND NOT EXISTS (
											SELECT
												1 
											FROM
												academico..SAP_situacao_aluno_periodo_letivo_view SAP1 
											WHERE
												SAP1.sap_id_registro_curso = SAP0.sap_id_registro_curso 
												AND SAP1.sap_id_periodo_letivo <> SAP0.sap_id_periodo_letivo 
												AND SAP1.sap_id_periodo_letivo = ( SELECT PEL1.pel_id_periodo_letivo FROM academico..PEL_periodo_letivo PEL1 WHERE PEL1.pel_id_periodo_sucessor = PEL0.pel_id_periodo_letivo ) 
											) 
										GROUP BY
											crs_nm_resumido,
											ser_ds_serie,
											tcu_ch_matutino 
										ORDER BY
											crs_nm_resumido,
											turno,
											ser_ds_serie";

								$res = mssql_query($sql);
								while ($row = mssql_fetch_object($res)) {
								?>
									<tr style="background-color: <?= $cor ?>;">
										<td style="text-align: left !important;"><?= utf8_encode($row->crs_nm_resumido) ?></td>
										<td style="text-align: left !important;"><?= utf8_encode($row->turno) ?></td>
										<td><?= utf8_encode($row->ser_ds_serie) ?></td>
										<td><b><?= $row->total ?></b></td>
									</tr>
								<?
								}
								?>

							</tbody>

						</table>
						<!-- datatable end -->
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<div id="panel-4" class="panel">
				<div class="panel-hdr">
					<h2>
						Matrículas<span class="fw-300"><i>por turma</i></span>
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th style="text-align: left !important;">Curso</th>
									<th>Turno</th>
									<th>Série</th>
									<th>Turma</th>
									<th>Matriculados</th>
							</thead>
							<tbody>
								<?
								$sql = "SELECT COUNT
											( DISTINCT rca_id_registro_curso ) AS total,
											crs_nm_resumido,
											ser_ds_serie,
											tcu_ds_turma_curso,
										CASE
												tcu_ch_matutino 
												WHEN 'S' THEN
												'MATUTINO' ELSE 'VESPERTINO' 
											END turno 
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
											And his_dt_criacao is not null
											and SAP0.sap_ds_situacao = 'Matriculado'
											AND NOT EXISTS (
											SELECT
												1 
											FROM
												academico..SAP_situacao_aluno_periodo_letivo_view SAP1 
											WHERE
												SAP1.sap_id_registro_curso = SAP0.sap_id_registro_curso 
												AND SAP1.sap_id_periodo_letivo <> SAP0.sap_id_periodo_letivo 
												AND SAP1.sap_id_periodo_letivo = ( SELECT PEL1.pel_id_periodo_letivo FROM academico..PEL_periodo_letivo PEL1 WHERE PEL1.pel_id_periodo_sucessor = PEL0.pel_id_periodo_letivo ) 
											) 
										GROUP BY
											crs_nm_resumido,
											ser_ds_serie,
											tcu_ds_turma_curso,
											tcu_ch_matutino 
										ORDER BY
											crs_nm_resumido,
											turno,
											ser_ds_serie,
											tcu_ds_turma_curso";

								$res = mssql_query($sql);
								while ($row = mssql_fetch_object($res)) {
								?>
									<tr style="background-color: <?= $cor ?>;">
										<td style="text-align: left !important;"><?= utf8_encode($row->crs_nm_resumido) ?></td>
										<td style="text-align: left !important;"><?= utf8_encode($row->turno) ?></td>
										<td><?= utf8_encode($row->ser_ds_serie) ?></td>
										<td><?= utf8_encode($row->tcu_ds_turma_curso) ?></td>
										<td><b><?= $row->total ?></b></td>
									</tr>
								<?
								}
								?>

							</tbody>

						</table>
						<!-- datatable end -->
					</div>
				</div>
			</div>
		</div>
	</div>


</main>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>


<style>
	table td,
	th {
		vertical-align: middle !important;
		text-align: center !important;
	}
</style>

<!-- <script src="js/app.bundle.js"></script> -->
<!-- The order of scripts is irrelevant. Please check out the plugin pages for more details about these plugins below: -->

<script src="js/datagrid/datatables/datatables.bundle.js"></script>