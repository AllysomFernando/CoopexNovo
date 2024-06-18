<?php

require_once("php/sqlsrv.php");
//require_once("./php/utils.php");

//$id_pessoa = $_GET['id_usuario'];

$vestibular = isset($_GET['id']) ? "vestibular_" . $_GET['id'] : "vestibular";
$periodo = isset($_GET['id']) ? $_GET['id'] : "20240";

// Função para obter o número de matrículas com base na faculdade

$sql = "SELECT COUNT
						( DISTINCT rca_id_registro_curso ) AS total,
						crs_nm_resumido,
						ser_ds_serie,
						crs_id_curso,
						tcu_ch_matutino,
						tcu_ds_turma_curso,
					CASE
							tcu_ch_matutino 
							WHEN 'S' THEN
							'MATUTINO' ELSE 'VESPERTINO' 
						END turno 
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
					WHERE
						pel_ds_compacta = '20240' 
						AND fac_id_faculdade = 1000000006 
						AND iap_id_periodo_letivo = SAP0.sap_id_periodo_letivo 
						AND ( SAP0.sap_ds_situacao = 'Matriculado' OR SAP0.sap_ds_situacao = 'Sem Status' ) 
					GROUP BY
						crs_nm_resumido,
						ser_ds_serie,
						tcu_ds_turma_curso,
						crs_id_curso,
						tcu_ch_matutino 
					ORDER BY
						crs_nm_resumido,
						turno,
						ser_ds_serie,
						tcu_ds_turma_curso";

$res = mssql_query($sql);
$total = 0;
$infantil = 0;
$fundamental = 0;
$medio = 0;
$matutino = 0;
$vespertino = 0;

while ($row = mssql_fetch_object($res)) {
	if ($row->crs_id_curso == 5000000630) {
		$arr_fundamental[] = $row;
		//echo $row->total . " - ";
		$fundamental += $row->total;
	} else if ($row->crs_id_curso == 5000000632) {
		$arr_medio[] = $row;
		//echo $row->total . " - ";
		$medio += $row->total;
	} else if ($row->crs_id_curso == 5000000631) {
		$arr_infantil[] = $row;
		//echo $row->total . " - ";
		$infantil += $row->total;
	}



	//print_r($row);

	if ($row->tcu_ch_matutino == "S") {
		$matutino += $row->total;
	} else {
		$vespertino += $row->total;
	}

	$total += $row->total;
	$dados[] = $row;
}


// Inicialize um array para armazenar os totais agrupados
$totaisAgrupados = [];

// Percorra os dados para agrupar os totais pela coluna crs_nm_resumido e tcu_ch_matutino
foreach ($dados as $dado) {
	$chave = $dado->crs_nm_resumido . '-' . $dado->tcu_ch_matutino;
	if (!isset($totaisAgrupados[$chave])) {
		$totaisAgrupados[$chave] = 0;
	}
	$totaisAgrupados[$chave] += $dado->total;
}

$totaisAgrupadosSerie = [];
foreach ($dados as $dado) {
	$chave = $dado->crs_nm_resumido . '-' . $dado->tcu_ch_matutino . '-' . $dado->ser_ds_serie;
	if (!isset($totaisAgrupadosSerie[$chave])) {
		$totaisAgrupadosSerie[$chave] = 0;
	}
	$totaisAgrupadosSerie[$chave] += $dado->total;
}

$totaisAgrupadosTurma = [];
foreach ($dados as $dado) {
	$chave = $dado->crs_nm_resumido . '-' . $dado->tcu_ch_matutino . '-' . $dado->ser_ds_serie . '-' . $dado->tcu_ds_turma_curso;
	if (!isset($totaisAgrupadosTurma[$chave])) {
		$totaisAgrupadosTurma[$chave] = 0;
	}
	$totaisAgrupadosTurma[$chave] += $dado->total;
}
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
			<i class='subheader-icon fal fa-chart-area'></i> Matrículas <span class='fw-300'>GERAL</span>
		</h1>
	</div>

	<div class="row">
		<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
			<div class="p-3 bg-danger-300 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $total ?>
						<small class="m-0 l-h-n">Alunos Matriculados 2024</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>

		<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
			<div class="p-3 bg-success-600 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $infantil ?>
						<small class="m-0 l-h-n">Educação Infantil</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>

		<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
			<div class="p-3 bg-success-700 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $fundamental ?>
						<small class="m-0 l-h-n">Ensino Fundamental</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>

		<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
			<div class="p-3 bg-success-800 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $medio ?>
						<small class="m-0 l-h-n">Ensino Médio</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>






		<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
			<div class="p-3 bg-primary-500 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $matutino ?>
						<small class="m-0 l-h-n">Matutino</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>

		<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
			<div class="p-3 bg-primary-700 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $vespertino ?>
						<small class="m-0 l-h-n">Vespertino</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>

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
								<?php foreach ($totaisAgrupados as $chave => $total) : ?>
									<?php list($curso, $periodo) = explode('-', $chave); ?>
									<tr>
										<td style="text-align: left !important;"><?= utf8_encode($curso) ?></td>
										<td><?= $periodo == "S" ? 'Matutino' : 'Vespertino' ?></td>
										<td><?= $total ?></td>
									</tr>
								<?php endforeach; ?>

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
								<?php foreach ($totaisAgrupadosSerie as $chave => $total) : ?>
									<?php list($curso, $periodo, $serie) = explode('-', $chave); ?>
									<tr>
										<td style="text-align: left !important;"><?= utf8_encode($curso) ?></td>
										<td><?= $periodo == "S" ? 'Matutino' : 'Vespertino' ?></td>
										<td><?= utf8_encode($serie) ?></td>
										<td><?= $total ?></td>
									</tr>
								<?php endforeach; ?>

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
								<?php foreach ($totaisAgrupadosTurma as $chave => $total) : ?>
									<?php list($curso, $periodo, $serie, $turma) = explode('-', $chave); ?>
									<tr>
									<td style="text-align: left !important;"><?= utf8_encode($curso) ?></td>
										<td><?= $periodo == "S" ? 'Matutino' : 'Vespertino' ?></td>
										<td><?= utf8_encode($serie) ?></td>
										<td><?= utf8_encode($turma) ?></td>
										<td><?= $total ?></td>
									</tr>
								<?php endforeach; ?>

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