<?php

require_once("php/sqlsrv.php");
//require_once("./php/utils.php");

$sql = "SELECT
			count(*) as total
		FROM
			colegio.cdt_aluno_matricula";

$res = $coopex->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$total = $row->total;

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
			<i class='subheader-icon fal fa-chart-area'></i> Clube da Tarefa
		</h1>
	</div>

	<div class="row">
		<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
			<div class="p-3 bg-primary-500 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $row->total ?>
						<small class="m-0 l-h-n">Matrículas</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>

		<!-- <div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
			<div class="p-3 bg-success-500 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $pagos ?>
						<small class="m-0 l-h-n">Matrículas pagas</small>
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
		</div> -->



	</div>



	<div class="row">
		<div class="col-lg-12">
			<div id="panel-4" class="panel">
				<div class="panel-hdr">
					<h2>
						Matriculados<span class="fw-300"><i>Clube da Tarefa</i></span>
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
									<th>Curso</th>
									<th>Série</th>
									<th>Turma</th>
							</thead>
							<tbody>
								<?
								$sql = "SELECT
											*,
											DATE ( data_matricula ) AS data_matricula 
										FROM
											colegio.cdt_matricula
											INNER JOIN colegio.cdt_aluno_matricula USING ( id_matricula )
											INNER JOIN colegio.curso USING ( id_curso )
											INNER JOIN colegio.serie USING ( id_serie )
											INNER JOIN colegio.turma USING ( id_turma ) 
										WHERE
											situacao = 1 
										ORDER BY
											cdt_aluno_matricula.id_pessoa";

								$res = $coopex->query($sql);
								$i = 1;

								while ($row = $res->fetch(PDO::FETCH_OBJ)) {
								?>
									<tr class="<?= $cor ?>">
										<td><?= $i++ ?></td>
										<td style="text-align: left !important;"><?= utf8_encode($row->nome) ?></td>
										<td><?= converterData($row->data_matricula) ?></td>
										<td ><?= utf8_encode($row->curso) ?></td>
										<td><?= utf8_encode($row->serie) ?></td>
										<td><?= utf8_encode($row->turma) ?></td>
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
