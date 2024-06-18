<?php

//require_once("./php/mysql.php");
//require_once("./php/utils.php");

//$id_pessoa = $_GET['id_usuario'];

if (isset($_GET['id'])) {
	$vestibular = "vestibular_" . $_GET['id'];
} else {
	$vestibular = "vestibular";
}

//INSCRITOS TOTAL
$sql = "SELECT
			id_cadastro 
		FROM
			xp.v_cadastro 
		GROUP BY
			telefone";
$res = $coopex_antigo->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$inscritos_total = $res->rowCount();


//INSCRITOS TOTAL
$sql = "SELECT
			id_cadastro 
		FROM
			xp.v_cadastro 
		WHERE
			cursoPretendido <> '' 
		GROUP BY
			telefone";
$res = $coopex_antigo->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$cadastros_completos = $res->rowCount();

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
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">COOPEX</a></li>
		<li class="breadcrumb-item">BI</li>
		<li class="breadcrumb-item active">FAGX Dashboard</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>

	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-chart-area'></i> FAGX <span class='fw-300'>Dashboard</span>
		</h1>

	</div>



	<!-- <div class="btn-group<?= $mobile ? "-vertical" : "" ?>" role="group" aria-label="Group C">
		<a href="https://coopex.fag.edu.br/dashboard/direcao/vestibular" type="button" class="btn btn-<?= !isset($_GET['id']) ? "primary" : "light" ?> waves-effect waves-themed">2024/1</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/vestibular/20232" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20232 ? "primary" : "light" ?> waves-effect waves-themed">2023/2</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/vestibular/20231" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20231 ? "primary" : "light" ?> waves-effect waves-themed">2023/1</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/vestibular/20222" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20222 ? "primary" : "light" ?> waves-effect waves-themed">2022/2</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/vestibular/20221" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20221 ? "primary" : "light" ?> waves-effect waves-themed">2022/1</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/vestibular/20212" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20212 ? "primary" : "light" ?> waves-effect waves-themed">2021/2</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/vestibular/20211" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20211 ? "primary" : "light" ?> waves-effect waves-themed">2021/1</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/vestibular/20202" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20202 ? "primary" : "light" ?> waves-effect waves-themed">2020/2</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/vestibular/20201" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20201 ? "primary" : "light" ?> waves-effect waves-themed">2020/1</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/vestibular/20192" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20192 ? "primary" : "light" ?> waves-effect waves-themed">2019/2</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/vestibular/20191" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20191 ? "primary" : "light" ?> waves-effect waves-themed">2019/1</a>
	</div> -->

	<div class="row">
		<div class="col-sm-6 col-xl-4" title="Total Geral de Inscritos">
			<div class="p-3 bg-primary-300 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $inscritos_total ?>
						<small class="m-0 l-h-n">Cadastros Total</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>
		<div class="col-sm-6 col-xl-4" title="Total Geral de Inscritos">
			<div class="p-3 bg-success-600 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $cadastros_completos ?>
						<small class="m-0 l-h-n">Cadastros Completos</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>
		<div class="col-sm-6 col-xl-4" title="Total Geral de Inscritos">
			<div class="p-3 bg-danger-600 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $inscritos_total - $cadastros_completos ?>
						<small class="m-0 l-h-n">Cadastros Incompletos</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>

	</div>



	<div class="row">
		<div class="col-lg-12">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Inscrições por dia
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">

						<canvas id="por_dia"></canvas>

					</div>
				</div>
			</div>
		</div>


		<div class="col-lg-12">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Inscrições por hora do dia
					</h2>
				</div>
				<div class=" show">
					<div class="panel-content bg-subtlelight-fade">
						<canvas id="hora"></canvas>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Plataformas
					</h2>
				</div>
				<div class=" show">
					<div class="panel-content bg-subtlelight-fade">
						<canvas class="p-3" id="pagamentos"></canvas>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Estilo Musical
					</h2>
				</div>
				<div class=" show">
					<div class="panel-content bg-subtlelight-fade">
						<canvas class="p-3" id="top5_estados"></canvas>
					</div>
				</div>
			</div>
		</div>

	

		<div class="col-lg-12">
			<div id="panel-4" class="panel">
				<div class="panel-hdr">
					<h2>
						Curso Pretendido
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th width="10" style="text-align: center !important;">#</th>
									<th style="text-align: left !important;">Curso</th>
									<th width="10">Cadastros</th>
							</thead>
							<tbody>
								<?
								$i = 0;

								$sql = "SELECT
											cursoPretendido,
											count(*) AS total 
										FROM
											xp.v_cadastro 
										WHERE
											cursoPretendido <> '' 
										GROUP BY
											cursoPretendido 
										ORDER BY
											total DESC";
								$res = $coopex_antigo->query($sql);

								while ($row = $res->fetch(PDO::FETCH_OBJ)) {
									$i++;
									

								?>
									<tr>
										<td><?= $i ?></td>
										<td style="text-align: left !important;"><b><?= utf8_encode($row->cursoPretendido) ?></b></td>
										<td><?= $row->total ?></td>

										
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

		<div class="col-lg-12">
			<div id="panel-4" class="panel">
				<div class="panel-hdr">
					<h2>
						Colégio
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th width="10" style="text-align: center !important;">#</th>
									<th style="text-align: left !important;">Colégio</th>
									<th style="text-align: left !important;">Cidade</th>
									<th width="10">Cadastros</th>
							</thead>
							<tbody>
								<?
								$i = 0;

								$sql = "SELECT
											colegio,
											cidade,
											count(*) AS total 
										FROM
											xp.v_cadastro a
											INNER JOIN xp.colegios b ON a.colegio = b.nome_colegio 
										WHERE
											colegio <> '' 
										GROUP BY
											colegio,
											cidade 
										ORDER BY
											total DESC";
								$res = $coopex_antigo->query($sql);

								while ($row = $res->fetch(PDO::FETCH_OBJ)) {
									$i++;
									

								?>
									<tr>
										<td><?= $i ?></td>
										<td style="text-align: left !important;"><b><?= utf8_encode($row->colegio) ?></b></td>
										<td style="text-align: left !important;"><b><?= utf8_encode($row->cidade) ?></b></td>
										<td><?= $row->total ?></td>

										
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

		<div class="col-lg-12">
			<div id="panel-4" class="panel">
				<div class="panel-hdr">
					<h2>
						Cidades
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th width="10" style="text-align: center !important;">#</th>
									<th style="text-align: left !important;">Cidade</th>
									<th width="10">Cadastros</th>
							</thead>
							<tbody>
								<?
								$i = 0;

								$sql = "SELECT
											cidade,
											count(*) AS total 
										FROM
											xp.v_cadastro a
											INNER JOIN xp.colegios b ON a.colegio = b.nome_colegio 
										WHERE
											colegio <> '' 
										GROUP BY
											colegio,
											cidade 
										ORDER BY
											total DESC";
								$res = $coopex_antigo->query($sql);

								while ($row = $res->fetch(PDO::FETCH_OBJ)) {
									$i++;
									

								?>
									<tr>
										<td><?= $i ?></td>
										<td style="text-align: left !important;"><b><?= utf8_encode($row->cidade) ?></b></td>
										<td><?= $row->total ?></td>

										
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
<style>
	table td,
	th {
		vertical-align: middle !important;
		text-align: center !important;
	}
</style>





<!-- <script src="js/app.bundle.js"></script> -->
<!-- The order of scripts is irrelevant. Please check out the plugin pages for more details about these plugins below: -->
<script src="js/statistics/peity/peity.bundle.js"></script>
<script src="js/statistics/flot/flot.bundle.js"></script>
<script src="js/statistics/easypiechart/easypiechart.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
	//INSCRIÇÕES POR CURSO
	<?
	unset($qtd);
	unset($curso);
	$sql = "SELECT
					ds_curso as curso,
					count(*)  as qtd
				FROM
					$vestibular.inscricao
					INNER JOIN $vestibular.curso USING ( id_curso ) 
				WHERE
					id_campus = 1
				GROUP BY
					id_curso
				ORDER BY qtd DESC";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$curso[] = "'" . utf8_encode($row->curso) . "'";
		$qtd[] = $row->qtd;
	}
	?>
	



	//INSCRIÇÕES POR DIA
	<?
	unset($qtd);
	unset($dia);
	$sql = "SELECT DATE
				( data_cadastro ) AS dia,
				count( id_cadastro ) AS qtd 
			FROM
				( SELECT * FROM xp.v_cadastro GROUP BY telefone ) AS teste 
			GROUP BY
				DATE ( data_cadastro ) 
			HAVING
				qtd > 10";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$dia[] = "'" . converterData($row->dia) . "'";
		$qtd[] = $row->qtd;
	}



	?>
	const ctx3 = document.getElementById('por_dia');
	new Chart(ctx3, {
		type: 'bar',
		data: {
			labels: [<?= implode(",", $dia) ?>],
			datasets: [{
				label: ' Cadastros: ',
				data: [<?= implode(",", $qtd) ?>],
				borderWidth: 1,
				order: 3,
			}]
		},
		options: {
			scales: {
				y: {
					beginAtZero: true
				}
			}
		}
	});
	//INSCRIÇÕES POR DIA----------------------------



	



	//INSCRIÇÕES POR HORA
	<?
	unset($qtd);
	unset($qtd_medicina);
	unset($qtd_geral);

	for ($i = 0; $i < 24; $i++) {

		$sql = "SELECT
					COUNT( id_cadastro ) AS qtd,
					HOUR( data_cadastro ) AS hora
				FROM
					xp.v_cadastro 
				WHERE
					HOUR( data_cadastro ) = $i	
				GROUP BY
					HOUR(data_cadastro) 
				ORDER BY
					HOUR(data_cadastro)";
		$res = $coopex_antigo->query($sql);
		$row = $res->fetch(PDO::FETCH_OBJ);
		$hora[] = $i;
		$qtd[] = isset($row->qtd) ? $row->qtd : 0;
	
	}

	?>
	const ctx30 = document.getElementById('hora');
	new Chart(ctx30, {
		type: 'bar',
		data: {
			labels: [<?= implode(",", $hora) ?>],
			datasets: [{
				label: ' Cadastros: ',
				data: [<?= implode(",", $qtd) ?>],
				borderWidth: 1,
				order: 3
			}]
		},
		options: {
			scales: {
				y: {
					beginAtZero: true
				}
			}
		}
	});
	//INSCRIÇÕES POR HORA----------------------------



	//PAGAMENTOS
	<?
	$sql = "SELECT
				*
			FROM
				xp.v_cadastro 
				where navegador like '%android%'
			GROUP BY
				telefone";
	$res = $coopex_antigo->query($sql);
	$android = $res->rowCount();

	$sql = "SELECT
				*
			FROM
				xp.v_cadastro 
				where navegador like '%iphone%'
			GROUP BY
				telefone";
	$res = $coopex_antigo->query($sql);
	$iphone = $res->rowCount();

	$sql = "SELECT
				*
			FROM
				xp.v_cadastro 
				where navegador not like '%iphone%' and navegador not like '%android%'
			GROUP BY
				telefone";
	$res = $coopex_antigo->query($sql);
	$nao_identificado = $res->rowCount();

	?>
	const ctx2 = document.getElementById('pagamentos');
	new Chart(ctx2, {
		type: 'doughnut',
		data: {
			labels: ['Android', 'iPhone', 'Não Identificado'],
			datasets: [{
				label: ' Cadastros: ',
				data: [<?= $android ?>, <?= $iphone ?>, <?= $nao_identificado ?>],
				borderWidth: 1
			}]
		},
		options: {
			scales: {
				y: {
					beginAtZero: true
				}
			}
		}
	});
	//PAGAMENTOS----------------------------




	//TOP 5 ESTADOS
	<?
	unset($estado);
	unset($total);
	$sql = "SELECT
				count( estilo_musical ) AS total,
				REPLACE ( estilo_musical, 'sertaneijo', 'sertanejo' ) AS estilo_musical 
			FROM
				xp.v_cadastro 
			WHERE
				estilo_musical <> '' 
			GROUP BY
				estilo_musical 
			ORDER BY
				total DESC 
				LIMIT 10";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$estado[] = "'" . utf8_encode($row->estilo_musical) . "'";
		$total[] = $row->total;
	}

	?>
	const ctx2222 = document.getElementById('top5_estados');
	new Chart(ctx2222, {
		type: 'doughnut',
		data: {
			labels: [<?= implode(",", $estado) ?>],
			datasets: [{
				label: ' Cadastros: ',
				data: [<?= implode(",", $total) ?>],
				borderWidth: 1
			}]
		},
		options: {
			scales: {
				y: {
					beginAtZero: true
				}
			}
		}
	});
	//TOP 5 ESTADOS----------------------------







</script>


