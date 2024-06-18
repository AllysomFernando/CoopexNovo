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
				( * ) AS total 
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

$sql = "SELECT
			count(* ) AS total 
		FROM
			colegio.sports";

$res = $coopex->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);


$sql = "SELECT
			count(* ) AS total 
		FROM
			colegio.sports where pagamento = 1";

$res = $coopex->query($sql);
$row_pago = $res->fetch(PDO::FETCH_OBJ);
$pagos = $row_pago->total;

$sql = "SELECT
			count(* ) AS total 
		FROM
			colegio.sports where pagamento = 0";

$res = $coopex->query($sql);
$row_pago = $res->fetch(PDO::FETCH_OBJ);
$nao_pagos = $row_pago->total;

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
			<i class='subheader-icon fal fa-chart-area'></i> Pré-Matrículas
		</h1>
	</div>

	<div class="row">
		<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
			<div class="p-3 bg-primary-500 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $row->total ?>
						<small class="m-0 l-h-n">Pré-Matrículas geral</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>

		<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
			<div class="p-3 bg-success-500 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $pagos ?>
						<small class="m-0 l-h-n">Pré-Matrículas pagas</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>

		<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
			<div class="p-3 bg-danger-300 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $nao_pagos ?>
						<small class="m-0 l-h-n">Pré-Matrículas não pagas</small>
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
						Não pagos<span class="fw-300"><i>Pré-matrícula</i></span>
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="dt-basic-example" class="table table-bordered table-hover  w-100">
							<thead>
								<tr>
									<th>#</th>
									<th style="text-align: left !important;">Nome</th>
									<th>Data Inscrição</th>
									<th>Dias de Atraso</th>
							</thead>
							<tbody>
								<?
								$sql = "SELECT
											nome,
											date(data_cadastro) AS data_cadastro,
											DATEDIFF( NOW(), data_cadastro ) AS dias_passados 
										FROM
											colegio.sports
											INNER JOIN coopex.pessoa USING ( id_pessoa ) 
										WHERE
											pagamento = 0";

								$res = $coopex->query($sql);
								$i = 1;
								
								while ($row = $res->fetch(PDO::FETCH_OBJ)) {
									$cor = $row->dias_passados <= 2 ? "bg-success-50" : "";
								?>
									<tr class="<?= $cor ?>">
										<td style="text-align: center !important;"><?= $i++ ?></td>
										<td style="text-align: left !important;"><?= utf8_encode($row->nome) ?></td>
										<td style="text-align: center !important;"><?= converterData($row->data_cadastro) ?></td>
										<td><?= ($row->dias_passados) ?></td>
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
						Pré-inscritos pagos<span class="fw-300"><i>Não matriculados</i></span>
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="dt-basic-example" class="table table-bordered table-hover  w-100">
							<thead>
								<tr>
									<th>#</th>
									<th style="text-align: left !important;">Nome</th>
									<th>Data Pagamento</th>
									<th>Dias Passados</th>
							</thead>
							<tbody>
								<?
								$sql = "SELECT
								*,
								nome,
								date(data_pagamento) AS data_cadastro,
								DATEDIFF( NOW(), data_pagamento ) AS dias_passados
							FROM
								colegio.sports
								INNER JOIN coopex.pessoa USING ( id_pessoa ) 
							WHERE
								pagamento = 1 
								AND id_pessoa NOT IN (
								SELECT
									id_pessoa 
							FROM
							colegio.matricula) order by dias_passados desc";

								$res = $coopex->query($sql);
								$i = 1;
								
								while ($row = $res->fetch(PDO::FETCH_OBJ)) {
									$cor = $row->dias_passados <= 2 ? "bg-success-50" : "";
								?>
									<tr class="<?= $cor ?>">
										<td style="text-align: center !important;"><?= $i++ ?></td>
										<td style="text-align: left !important;"><?= utf8_encode($row->nome) ?></td>
										<td style="text-align: center !important;"><?= converterData($row->data_cadastro) ?></td>
										<td><?= ($row->dias_passados) ?></td>
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