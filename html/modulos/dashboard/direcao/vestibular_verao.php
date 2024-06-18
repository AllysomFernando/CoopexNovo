<?php

require_once("php/sqlsrv.php");
//require_once("./php/utils.php");

//$id_pessoa = $_GET['id_usuario'];



if (isset($_GET['id'])) {
	$vestibular = "vestibular_" . $_GET['id'];
	$periodo = $_GET['id'];
} else {
	$vestibular = "vestibular";
	$periodo = "20242";
}

//INSCRITOS TOTAL
$sql = "SELECT
			count( id_pessoa ) AS total 
		FROM
			$vestibular.inscricao
		INNER JOIN $vestibular.pessoa USING (
			id_pessoa 
		)";
$res = $coopex_antigo->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$inscritos_total = $row->total;

//DIAS DE CAMPANHA	
$sql = "SELECT
			dt_inscricao 
		FROM
			$vestibular.inscricao 
		GROUP BY
			DATE ( dt_inscricao ) 
		ORDER BY
			dt_inscricao";
$res = $coopex_antigo->query($sql);
$dias_campanha = $res->rowCount();

//DIAS DE INSCRICAO	
$sql = "SELECT
			dt_fim_inscricao, dt_inicio_inscricao,
			DATEDIFF( dt_fim_inscricao, dt_inicio_inscricao ) AS total 
		FROM
			$vestibular.vestibular";
$res = $coopex_antigo->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$dias_inscricao = $row->total;
$inicio_inscricao = $row->dt_inicio_inscricao;
$fim_inscricao = $row->dt_fim_inscricao;
$media = $inscritos_total  / $dias_campanha;
$previsao = ($inscritos_total  / $dias_campanha) * ($dias_inscricao - $dias_campanha);

//INSCRITOS CONFIRMADOS
$sql = "SELECT
			count( id_pessoa ) AS total 
		FROM
			$vestibular.inscricao
			INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
		WHERE
			pagamento = 1";
$res = $coopex_antigo->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$inscritos_confirmados = $row->total;

//INSCRITOS CONFIRMADOS TREINEIROS
$sql = "SELECT
			count( id_pessoa ) AS total 
		FROM
		$vestibular.inscricao
			INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
		WHERE
			pagamento = 1
		AND
			id_ensino_medio = 3";
$res = $coopex_antigo->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$inscritos_confirmados_treineiros = $row->total;

//INSCRITOS CONFIRMADOS TREINEIROS
$sql = "SELECT
			count( id_pessoa ) AS total 
		FROM
		$vestibular.inscricao
			INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
		WHERE
			pagamento = 1
		AND
			id_ensino_medio <> 3";
$res = $coopex_antigo->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$inscritos_confirmados_nao_treineiros = $row->total;

//INSCRITOS NÃO CONFIRMADOS NÃO TREINEIROS
$sql = "SELECT
			count( id_pessoa ) AS total 
		FROM
		$vestibular.inscricao
			INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
		WHERE
			pagamento = 0
		AND
			id_ensino_medio = 3";
$res = $coopex_antigo->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$inscritos_nao_confirmados_treineiros = $row->total;

//INSCRITOS CONFIRMADOS TREINEIROS
$sql = "SELECT
				count( id_pessoa ) AS total 
			FROM
			$vestibular.inscricao
				INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
			WHERE
				pagamento = 0
			AND
				id_ensino_medio <> 3";
$res = $coopex_antigo->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$inscritos_nao_confirmados_nao_treineiros = $row->total;

//INSCRITOS NÃO CONFIRMADOS
$sql = "SELECT
				count( id_pessoa ) AS total 
			FROM
			$vestibular.inscricao
				INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
			WHERE
				pagamento = 0";
$res = $coopex_antigo->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$inscritos_nao_confirmados = $row->total;

//INSCRITOS MEDICINA
$sql = "SELECT
				count( id_pessoa ) AS total 
			FROM
			$vestibular.inscricao
				INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
			WHERE
				id_curso = 1000000115";
$res = $coopex_antigo->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$inscritos_medicina = $row->total;

//INSCRITOS GERAL
$sql = "SELECT
				count( id_pessoa ) AS total 
			FROM
			$vestibular.inscricao
				INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
			WHERE
				id_curso <> 1000000115";
$res = $coopex_antigo->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$inscritos_geral = $row->total;


$previsao_inscritos = $inscritos_total + $previsao;

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
		<li class="breadcrumb-item active">Vestibular Dashboard</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>

	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-chart-area'></i> Vestibular <span class='fw-300'>Dashboard</span>
		</h1>
		<div class="d-flex mr-4">
			<div class="mr-2">
				<span class="peity-donut" data-peity="{ &quot;fill&quot;: [&quot;#967bbd&quot;, &quot;#ccbfdf&quot;],  &quot;innerRadius&quot;: 14, &quot;radius&quot;: 20 }"><?= ($inscritos_medicina * 100) / $inscritos_total ?>/100</span>
			</div>
			<div>
				<label class="fs-sm mb-0 mt-2 mt-md-0">Medicina</label>
				<h4 class="font-weight-bold mb-0"><?= number_format(($inscritos_medicina * 100) / $inscritos_total, 2, ',', '.') ?>%</h4>
			</div>
		</div>
		<div class="d-flex mr-0">
			<div class="mr-2">
				<span class="peity-donut" data-peity="{ &quot;fill&quot;: [&quot;#2196F3&quot;, &quot;#9acffa&quot;],  &quot;innerRadius&quot;: 14, &quot;radius&quot;: 20 }"><?= ($inscritos_geral * 100) / $inscritos_total ?>/100</span>
			</div>
			<div>
				<label class="fs-sm mb-0 mt-2 mt-md-0">Geral</label>
				<h4 class="font-weight-bold mb-0"><?= number_format(($inscritos_geral * 100) / $inscritos_total, 2, ',', '.') ?>%</h4>
			</div>
		</div>
	</div>



	<div class="btn-group<?= $mobile ? "-vertical" : "" ?>" role="group" aria-label="Group C">
		<a href="https://coopex.fag.edu.br/dashboard/direcao/vestibular" type="button" class="btn btn-<?= !isset($_GET['id']) ? "primary" : "light" ?> waves-effect waves-themed">2024/2</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/vestibular/20241" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20232 ? "primary" : "light" ?> waves-effect waves-themed">2024/1</a>
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
	</div>

	<div class="row mt-5">
		<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
			<div class="p-3 bg-primary-300 rounded overflow-hidden position-relative text-white mb-g">
				<div class="" style="height: 188px;">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $inscritos_total ?>
						<small class="m-0 l-h-n">Inscritos Total</small>
						<small class="m-0 l-h-n">Média diária: <?= round($media) ?> inscrições</small>
					</h3>
					<?
					if ($vestibular == "vestibular") {
					?>
						<h3 class="display-4 d-block l-h-n m-0 fw-500 mt-5">
							<?= round($previsao_inscritos) ?>
							<small class="m-0 l-h-n">Previsão de Inscritos</small>
						</h3>
					<?
					}
					?>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>
		<div class="col-sm-6 col-xl-2">
			<div title="Total de Inscritos Confirmados" class="p-3 bg-success-400 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $inscritos_confirmados ?>
						<small class="m-0 l-h-n">Total Pagos</small>
					</h3>
				</div>
				<i class="fal fa-check-circle position-absolute pos-right pos-bottom opacity-15  mb-n1 mr-n4" style="font-size: 6rem;"></i>
			</div>
			<div title="Total de Inscritos Não Confirmados" class="p-3 bg-danger-200 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $inscritos_nao_confirmados ?>
						<small class="m-0 l-h-n">Total Não Pagos</small>
					</h3>
				</div>
				<i class="fal fa-times-circle position-absolute pos-right pos-bottom opacity-15 mb-n5 mr-n6" style="font-size: 8rem;"></i>
			</div>
		</div>
		<div class="col-sm-6 col-xl-2">
			<div title="Total de Treineiros Confirmados" class="p-3 bg-primary-500 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $inscritos_confirmados_treineiros ?>
						<small class="m-0 l-h-n">Treineiros</small>
					</h3>
				</div>
				<i class="fal fa-check-circle position-absolute pos-right pos-bottom opacity-15  mb-n1 mr-n4" style="font-size: 6rem;"></i>
			</div>
			<div title="Total de Treineiros Não Confirmados" class="p-3 bg-primary-300 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $inscritos_nao_confirmados_treineiros ?>
						<small class="m-0 l-h-n">Treineiros</small>
					</h3>
				</div>
				<i class="fal fa-times-circle position-absolute pos-right pos-bottom opacity-15 mb-n5 mr-n6" style="font-size: 8rem;"></i>
			</div>
		</div>
		<div class="col-sm-6 col-xl-2">
			<div title="Ensino Médio Concluído Confirmados" class="p-3 bg-success-600 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $inscritos_confirmados_nao_treineiros ?>
						<small class="m-0 l-h-n">Ens. M. Concluído</small>
					</h3>
				</div>
				<i class="fal fa-check-circle position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n4" style="font-size: 6rem;"></i>
			</div>
			<div title="Ensino Médio Concluído Não Confirmados" class="p-3 bg-danger-500 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $inscritos_nao_confirmados_nao_treineiros ?>
						<small class="m-0 l-h-n">Ens. M. Concluído</small>
					</h3>
				</div>
				<i class="fal fa-times-circle position-absolute pos-right pos-bottom opacity-15 mb-n5 mr-n6" style="font-size: 8rem;"></i>
			</div>
		</div>
		<div class="col-sm-6 col-xl-3">
			<div title="Total de Candidatos em potencial - Soma de Não Treineiros" class="p-3 bg-info-800 rounded overflow-hidden position-relative text-white mb-g">
				<div class="" style="height: 187px;">
					<?

					$candidatos_potencial = $inscritos_confirmados_nao_treineiros + $inscritos_nao_confirmados_nao_treineiros;
					?>
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $candidatos_potencial ?>
						<small class="m-0 l-h-n">Candidatos em potencial</small>
						<?
						if (!isset($_GET['id'])) {
							$media_potencial = ($inscritos_confirmados_nao_treineiros + $inscritos_nao_confirmados_nao_treineiros) * 100 / ($inscritos_total + $previsao);

						?>
							<small class="m-0 l-h-n">Média diária: <?= round($candidatos_potencial / $dias_inscricao) ?> inscrições</small>
						<?
						}
						?>
					</h3>
					<?
					if (isset($_GET['id'])) {
						$sql = "SELECT
									sum(total) as total
								FROM
									vestibular.ingresso 
								WHERE
									id_periodo_letivo = " . $_GET['id'];
						$res = $coopex->query($sql);
						$row = $res->fetch(PDO::FETCH_OBJ);
						$matriculados = $row->total;
					?>
						<h3 class="display-4 d-block l-h-n m-0 fw-500 mt-5">
							<?= $matriculados ?>
							<small class="m-0 l-h-n">Matriculados</small>
							<small class="m-0 l-h-n"><?= number_format($matriculados * 100 / ($inscritos_confirmados_nao_treineiros + $inscritos_nao_confirmados_nao_treineiros), 2) ?>%</small>
						</h3>
					<?
					} else {
					?>
						<h3 class="display-4 d-block l-h-n m-0 fw-500 mt-5">
							<?= round($candidatos_potencial * $previsao_inscritos / $inscritos_total) ?>
							<small class="m-0 l-h-n">Previsão de Candidatos Aptos</small>
						</h3>
					<?
					}
					?>
				</div>
				<i class="fal fa-users position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>

		<div class="col-lg-12">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Resumo da Campanha
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table class="table table-sm m-0">
							<tr>
								<th>Início</th>
								<th>Término</th>
								<th>Total de Dias</th>
								<th>Dias passados</th>
								<th>Dias restantes</th>
							</tr>
							<tr>
								<td><?= converterData($inicio_inscricao) ?></td>
								<td><?= converterData($fim_inscricao) ?></td>
								<td><b><?= $dias_inscricao ?></b></td>
								<td><?= $dias_campanha ?></td>
								<td><?= $dias_inscricao - $dias_campanha ?></td>
							</tr>
						</table>
					</div>
				</div>
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

		<div class="col-lg-8">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Inscrições por dia da semana
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<div class="col-12">
							<canvas id="dia_da_semana"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Pagamentos
					</h2>
				</div>
				<div class=" show">
					<div class="panel-content bg-subtlelight-fade">
						<div>
							<canvas class="p-3" id="pagamentos"></canvas>
						</div>
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
						Cidades - TOP 10 <span class="fw-300"><i>(Exceto Cascavel)</i></span>
					</h2>
				</div>
				<div class=" show">
					<div class="panel-content bg-subtlelight-fade">
						<canvas class="p-3" id="top5_cidades"></canvas>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Estados - TOP 5 <span class="fw-300"><i>(Exceto Paraná)</i></span>
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
			<div id="panel-2" class="panel" data-panel-sortable data-panel-collapsed data-panel-close>
				<div class="panel-hdr">
					<h2>
						Por <span class="fw-300"><i>Localidade</i></span>
					</h2>
				</div>
				<div class="panel-container show container">
					<div class="row">
						<div class="col-8">
							<div id="geochart-colors"></div>
						</div>
						<div class="col-4 overflow-auto" style="max-height: 400px;">
							<table class="table">
								<tr>
									<th>Cidade</th>
									<th>Inscritos</th>
								</tr>
								<tbody id="tabela_cidade"></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Como Soube
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table class="table table-sm">
							<?
							$sql = "SELECT
										ds_como_soube,
										count(*) AS qtd 
									FROM
										$vestibular.inscricao
										INNER JOIN $vestibular.como_soube USING ( id_como_soube ) 
									GROUP BY
										id_como_soube 
									ORDER BY
										qtd DESC";
							$res = $coopex_antigo->query($sql);
							while ($row = $res->fetch(PDO::FETCH_OBJ)) {
							?>
								<tr>
									<td class="text-left"><?= utf8_encode($row->ds_como_soube) ?></td>
									<td><b><?= $row->qtd ?></b></td>
									<td><?= round(($row->qtd * 100 / $inscritos_total), 2) ?>%</td>
								</tr>
							<?
							}
							?>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-8">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Banca Especial
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<div class="row">
							<div class="col-6">
								<h4>Geral</h4>
								<table class="table table-sm">
									<tr>
										<th class="text-left">Motivo</th>
										<th>Qtd</th>
									</tr>
									<?
									$sql = "SELECT
											ds_banca_especial,
											count(*) AS qtd 
										FROM
										$vestibular.pessoa 
										WHERE
											tp_banca_especial = 1 
										GROUP BY
											ds_banca_especial 
										ORDER BY
											qtd DESC";
									$res = $coopex_antigo->query($sql);
									while ($row = $res->fetch(PDO::FETCH_OBJ)) {
									?>
										<tr>
											<td class="text-left"><?= utf8_encode($row->ds_banca_especial) ?></td>
											<td><b><?= $row->qtd ?></b></td>
										</tr>
									<?
									}
									?>
								</table>
							</div>
							<div class="col-6">
								<h4>Sabatistas</h4>
								<table class="table table-sm">
									<?
									function banca_especial($curso, $pagamento, $vestibular, $pdo)
									{
										$sql = "SELECT
										ds_banca_especial
									FROM
										$vestibular.pessoa
										INNER JOIN $vestibular.inscricao USING ( id_pessoa ) 
									WHERE
										tp_banca_especial = 1 
										AND ($pagamento)
										AND ( ds_banca_especial LIKE '%saba%' OR ds_banca_especial LIKE '%adv%' ) 
										AND id_curso $curso";
										$res = $pdo->query($sql);
										return $res->rowCount();
									}

									$medicina = banca_especial("= 1000000115", "pagamento = 0 OR pagamento = 1", $vestibular, $coopex_antigo);
									$medicina_pago = banca_especial("= 1000000115", "pagamento = 1", $vestibular, $coopex_antigo);
									$medicina_nao_pago = banca_especial("= 1000000115", "pagamento = 0", $vestibular, $coopex_antigo);

									$geral = banca_especial("<> 1000000115", "pagamento = 0 OR pagamento = 1", $vestibular, $coopex_antigo);
									$geral_pago = banca_especial("<> 1000000115", "pagamento = 1", $vestibular, $coopex_antigo);
									$geral_nao_pago = banca_especial("<> 1000000115", "pagamento = 0", $vestibular, $coopex_antigo);

									?>
									<tr>
										<th class="text-left">Curso</th>
										<th>Inscritos</th>
										<th>Não Confirmados</th>
										<th>Confirmados</th>
									</tr>
									<tr>
										<td class="text-left">Medicina</td>
										<td><b><?= $medicina ?></b></td>
										<td><?= $medicina_nao_pago ?></td>
										<td><b><?= $medicina_pago ?></b></td>
									</tr>
									<tr>
										<td class="text-left">Geral</td>
										<td><b><?= $geral ?></b></td>
										<td><?= $geral_nao_pago ?></td>
										<td><b><?= $geral_pago ?></b></td>
									</tr>
									<tr>
										<td class="text-left"><b>Total</b></td>
										<td><b><?= $medicina + $geral ?></b></td>
										<td><b><?= $medicina_nao_pago + $geral_nao_pago ?></b></td>
										<td><b><?= $medicina_pago + $geral_pago ?></b></td>
									</tr>

								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?
		$sql = "SELECT
				count( ins.id_curso ) AS qtd,
				cur.ds_curso,
				cam.ds_campus,
				ins.id_curso 
			FROM
				$vestibular.inscricao ins
				INNER JOIN $vestibular.curso cur USING ( id_curso )
				INNER JOIN $vestibular.curso_campus cca USING ( id_curso )
				INNER JOIN $vestibular.campus cam ON cam.id_campus = cca.id_campus
				INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
			WHERE
				cca.id_campus = 1 
			GROUP BY
				ins.id_curso,
				cam.id_campus 
			ORDER BY
				qtd DESC,
				cur.ds_curso";
		$res = $coopex_antigo->query($sql);
		$total_cascavel = 0;
		while ($row = $res->fetch(PDO::FETCH_OBJ)) {
			$total_cascavel += $row->qtd;
		}
		?>

		<div class="col-lg-12">
			<div id="panel-4" class="panel">
				<div class="panel-hdr">
					<h2>
						Inscritos por curso <span class="fw-300"><i>Cascavel</i> - <b class="fw-700"><?= $total_cascavel ?></b></span>
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th rowspan="2" style="text-align: left !important;">#</th>
									<th rowspan="2" style="text-align: left !important;">Curso</th>
									<th rowspan="2">Inscritos</th>
									<th rowspan="2">%</th>
									<?
									if (!$mobile) {
									?>
										<th colspan="2" title="Pagos">Pagamento</th>
										<th colspan="2" title="Treineiros">Treineiros</th>
										<th colspan="2" title="Ensimo Médio Concluído">E.M. Concluído</th>
										<th rowspan="2" title="Candidatos Aptos">Candidatos<br>Aptos</th>
										<th rowspan="2 title=" Possíveis Candidaros">Candidatos<br>em Potencial</th>
										<th rowspan="2" style="text-align: left !important;">Turno</th>
								</tr>
							<?
									}
							?>
							<?
							if (!$mobile) {
							?>
								<tr>
									<th title="Confirmados"><i class="fal fa-check fs-1"></i></th>
									<th title="Não Confirmados"><i class="fal fa-times"></i></th>
									<th title="Confirmados"><i class="fal fa-check"></i></th>
									<th title="Não Confirmados"><i class="fal fa-times"></i></th>
									<th title="Confirmados"><i class="fal fa-check"></i></th>
									<th title="Não Confirmados"><i class="fal fa-times"></i></th>
								</tr>
							<?
							}
							?>
							</thead>
							<tbody>
								<?
								$i = 0;

								$res = $coopex_antigo->query($sql);

								while ($row = $res->fetch(PDO::FETCH_OBJ)) {
									$i++;

									if ($row->qtd < 15) {
										$cor = "#E8CAF3";
									} else if ($row->qtd < 40) {
										$cor = "#FDFFBB";
									} else {
										$cor = "#D5F3CA";
									}

									$sql_pago = "SELECT
												count( id_pessoa ) AS total 
											FROM
											$vestibular.inscricao
												INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
											WHERE
												pagamento = 1
											AND id_curso = $row->id_curso";
									$res_pago = $coopex_antigo->query($sql_pago);
									$row_pago = $res_pago->fetch(PDO::FETCH_OBJ);
									$pago = $row_pago->total;

									$sql_pago = "SELECT
												count( id_pessoa ) AS total 
											FROM
											$vestibular.inscricao
												INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
											WHERE
												pagamento = 0
											AND id_curso = $row->id_curso";
									$res_pago = $coopex_antigo->query($sql_pago);
									$row_pago = $res_pago->fetch(PDO::FETCH_OBJ);
									$nao_pago = $row_pago->total;

									$sql_pago = "SELECT
												count( id_pessoa ) AS total 
											FROM
											$vestibular.inscricao
												INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
											WHERE
												id_ensino_medio = 3
											AND
												pagamento = 1	
											AND id_curso = $row->id_curso";
									$res_pago = $coopex_antigo->query($sql_pago);
									$row_pago = $res_pago->fetch(PDO::FETCH_OBJ);
									$treineiro = $row_pago->total;

									$sql_pago = "SELECT
												count( id_pessoa ) AS total 
											FROM
											$vestibular.inscricao
												INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
											WHERE
												id_ensino_medio <> 3
											AND
												pagamento = 1	
											AND id_curso = $row->id_curso";
									$res_pago = $coopex_antigo->query($sql_pago);
									$row_pago = $res_pago->fetch(PDO::FETCH_OBJ);
									$nao_treineiro = $row_pago->total;

									$sql_pago = "SELECT
												count( id_pessoa ) AS total 
											FROM
											$vestibular.inscricao
												INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
											WHERE
												id_ensino_medio = 3
											AND
												pagamento = 0	
											AND id_curso = $row->id_curso";
									$res_pago = $coopex_antigo->query($sql_pago);
									$row_pago = $res_pago->fetch(PDO::FETCH_OBJ);
									$treineiro_nao_pago = $row_pago->total;

									$sql_pago = "SELECT
												count( id_pessoa ) AS total 
											FROM
											$vestibular.inscricao
												INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
											WHERE
												id_ensino_medio <> 3
											AND
												pagamento = 0	
											AND id_curso = $row->id_curso";
									$res_pago = $coopex_antigo->query($sql_pago);
									$row_pago = $res_pago->fetch(PDO::FETCH_OBJ);
									$nao_treineiro_nao_pago = $row_pago->total;
								?>
									<tr style="background-color: <?= $cor ?>;">

										<td><?= $i ?></td>
										<td style="text-align: left !important;"><b><?= utf8_encode($row->ds_curso) ?></b></td>
										<td><?= $row->qtd ?></td>
										<td title="Porcentagem do total de inscritos"><?= number_format($row->qtd * 100 / $inscritos_total, 2) ?>%</td>
										<?
										if (!$mobile) {
										?>
											<td title="<?= number_format($pago * 100 / $row->qtd, 2) ?>%"><?= $pago ?></td>
											<td title="<?= number_format($nao_pago * 100 / $row->qtd, 2) ?>%"><?= $nao_pago ?></td>
											<td title="<?= number_format($treineiro * 100 / $row->qtd, 2) ?>%"><?= $treineiro ?></td>
											<td title="<?= number_format($treineiro_nao_pago * 100 / $row->qtd, 2) ?>%"><?= $treineiro_nao_pago ?></td>
											<td title="<?= number_format($nao_treineiro * 100 / $row->qtd, 2) ?>%"><b><?= $nao_treineiro ?></b></td>
											<td title="<?= number_format($nao_treineiro_nao_pago * 100 / $row->qtd, 2) ?>%"><?= $nao_treineiro_nao_pago ?></td>
											<td title="<?= number_format(($pago - $treineiro) * 100 / $row->qtd, 2) ?>%"><b><?= $pago - $treineiro ?></b></td>
											<td title="<?= number_format(($nao_treineiro + $nao_treineiro_nao_pago) * 100 / $row->qtd, 2) ?>%"><?= $nao_treineiro + $nao_treineiro_nao_pago ?></td>

											<td style="text-align: left !important;">
												<?
												$sql_turno = "SELECT
																* 
															FROM
																$vestibular.turno
																INNER JOIN $vestibular.curso_turno USING ( id_turno ) 
															WHERE
																id_curso = " . $row->id_curso;
												$res_turno = $coopex_antigo->query($sql_turno);

												while ($row_turno = $res_turno->fetch(PDO::FETCH_OBJ)) {
													$id_turno = $row_turno->id_turno;
													$id_curso = $row->id_curso;

													$sql_turno2 = "SELECT
																	count( * ) AS qtd,
																	turno 
																FROM
																	$vestibular.inscricao
																	INNER JOIN $vestibular.turno USING ( id_turno )
																	INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
																WHERE
																	id_curso = $id_curso 
																	AND id_campus = 1 
																	AND id_turno = $id_turno";
													$res_turno2 = $coopex_antigo->query($sql_turno2);
													$row_turno2 = $res_turno2->fetch(PDO::FETCH_OBJ)
												?>
													<div class="">
														<span title="<?= number_format($row_turno2->qtd * 100 / $row->qtd, 2) ?>%"><?= $row_turno2->qtd . " " . $row_turno2->turno ?></span>
													</div>
												<?
												}
												?>
											</td>
										<?
										}
										?>
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
						Inscritos por curso por Cidade<span class="fw-300"><i>Campus Cascavel</i></span>
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th style="text-align: left !important;">#</th>
									<th style="text-align: left !important;">Curso</th>
									<th>Inscritos</th>
									<th>Cidade 1</th>
									<th>Cidade 2</th>
									<th>Cidade 3</th>
									<th>Cidade 4</th>
									<th>Cidade 5</th>
							</thead>
							<tbody>
								<?
								$i = 0;

								$sql = "SELECT
											count( ins.id_curso ) AS qtd,
											cur.ds_curso,
											cam.ds_campus,
											ins.id_curso 
										FROM
											$vestibular.inscricao ins
											INNER JOIN $vestibular.curso cur USING ( id_curso )
											INNER JOIN $vestibular.curso_campus cca USING ( id_curso )
											INNER JOIN $vestibular.campus cam ON cam.id_campus = cca.id_campus
											INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
										WHERE
											cca.id_campus = 1 
										GROUP BY
											ins.id_curso,
											cam.id_campus 
										ORDER BY
											qtd DESC,
											cur.ds_curso";
								$res = $coopex_antigo->query($sql);

								while ($row = $res->fetch(PDO::FETCH_OBJ)) {
									$i++;
									$sql_pago = "SELECT
													ds_cidade,
													count(*) AS total,
													id_curso 
												FROM
													$vestibular.pessoa
													INNER JOIN $vestibular.inscricao USING ( id_pessoa ) 
												WHERE
													id_curso = $row->id_curso 
												GROUP BY
													ds_cidade 
												ORDER BY
													total DESC
												LIMIT 5";
									$res_pago = $coopex_antigo->query($sql_pago);

								?>
									<tr style="background-color: <?= $cor ?>;">
										<td><?= $i ?></td>
										<td style="text-align: left !important;"><b><?= utf8_encode($row->ds_curso) ?></b></td>
										<td><?= $row->qtd ?></td>

										<?
										while ($row_pago = $res_pago->fetch(PDO::FETCH_OBJ)) {
										?>
											<td><b><?= utf8_encode($row_pago->ds_cidade) ?></b><br><?= $row_pago->total ?></td>


										<?
										}
										?>
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
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Inscritos por curso <span class="fw-300"><i>Cascavel</i></span>
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<div>
							<canvas id="por_curso"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>


		<?
		$sql = "SELECT
					count( ins.id_curso ) AS qtd,
					cur.ds_curso,
					cam.ds_campus,
					ins.id_curso 
				FROM
					$vestibular.inscricao ins
					INNER JOIN $vestibular.curso cur USING ( id_curso )
					INNER JOIN $vestibular.curso_campus cca USING ( id_curso )
					INNER JOIN $vestibular.campus cam ON cam.id_campus = cca.id_campus
					INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
				WHERE
					cca.id_campus = 2 
				GROUP BY
					ins.id_curso,
					cam.id_campus 
				ORDER BY
					qtd DESC,
					cur.ds_curso";
		$res = $coopex_antigo->query($sql);
		$total_toledo = 0;
		while ($row = $res->fetch(PDO::FETCH_OBJ)) {
			$total_toledo += $row->qtd;
		}
		?>
		<div class="col-lg-12 d-none">
			<div id="panel-4" class="panel">
				<div class="panel-hdr">
					<h2>
						Inscritos por curso <span class="fw-300"><i>Toledo</i> - <b class="fw-700"><?= $total_toledo ?></b></span>
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th rowspan="2" style="text-align: left !important;">#</th>
									<th rowspan="2" style="text-align: left !important;">Curso</th>
									<th rowspan="2">Inscritos</th>
									<th rowspan="2">%</th>
									<?
									if (!$mobile) {
									?>
										<th colspan="2" title="Pagos">Pagamento</th>
										<th colspan="2" title="Treineiros">Treineiros</th>
										<th colspan="2" title="Ensimo Médio Concluído">E.M. Concluído</th>
										<th rowspan="2" title="Candidatos Aptos">Candidatos<br>Aptos</th>
										<th rowspan="2 title=" Possíveis Candidaros">Candidatos<br>em Potencial</th>

										<th rowspan="2" style="text-align: left !important;">Turno</th>
								</tr>
							<?
									}

									if (!$mobile) {
							?>
								<tr>
									<th title="Confirmados"><i class="fal fa-check fs-1"></i></th>
									<th title="Não Confirmados"><i class="fal fa-times"></i></th>
									<th title="Confirmados"><i class="fal fa-check"></i></th>
									<th title="Não Confirmados"><i class="fal fa-times"></i></th>
									<th title="Confirmados"><i class="fal fa-check"></i></th>
									<th title="Não Confirmados"><i class="fal fa-times"></i></th>
								</tr>
							<?
									}
							?>
							</thead>
							<tbody>
								<?
								$i = 0;

								$res = $coopex_antigo->query($sql);

								while ($row = $res->fetch(PDO::FETCH_OBJ)) {
									$i++;

									if ($row->qtd < 15) {
										$cor = "#E8CAF3";
									} else if ($row->qtd < 40) {
										$cor = "#FDFFBB";
									} else {
										$cor = "#D5F3CA";
									}

									$sql_pago = "SELECT
												count( id_pessoa ) AS total 
											FROM
											$vestibular.inscricao
												INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
											WHERE
												pagamento = 1
											AND id_curso = $row->id_curso";
									$res_pago = $coopex_antigo->query($sql_pago);
									$row_pago = $res_pago->fetch(PDO::FETCH_OBJ);
									$pago = $row_pago->total;

									$sql_pago = "SELECT
												count( id_pessoa ) AS total 
											FROM
											$vestibular.inscricao
												INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
											WHERE
												pagamento = 0
											AND id_curso = $row->id_curso";
									$res_pago = $coopex_antigo->query($sql_pago);
									$row_pago = $res_pago->fetch(PDO::FETCH_OBJ);
									$nao_pago = $row_pago->total;

									$sql_pago = "SELECT
												count( id_pessoa ) AS total 
											FROM
											$vestibular.inscricao
												INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
											WHERE
												id_ensino_medio = 3
											AND
												pagamento = 1	
											AND id_curso = $row->id_curso";
									$res_pago = $coopex_antigo->query($sql_pago);
									$row_pago = $res_pago->fetch(PDO::FETCH_OBJ);
									$treineiro = $row_pago->total;

									$sql_pago = "SELECT
												count( id_pessoa ) AS total 
											FROM
											$vestibular.inscricao
												INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
											WHERE
												id_ensino_medio <> 3
											AND
												pagamento = 1	
											AND id_curso = $row->id_curso";
									$res_pago = $coopex_antigo->query($sql_pago);
									$row_pago = $res_pago->fetch(PDO::FETCH_OBJ);
									$nao_treineiro = $row_pago->total;

									$sql_pago = "SELECT
												count( id_pessoa ) AS total 
											FROM
											$vestibular.inscricao
												INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
											WHERE
												id_ensino_medio = 3
											AND
												pagamento = 0	
											AND id_curso = $row->id_curso";
									$res_pago = $coopex_antigo->query($sql_pago);
									$row_pago = $res_pago->fetch(PDO::FETCH_OBJ);
									$treineiro_nao_pago = $row_pago->total;

									$sql_pago = "SELECT
												count( id_pessoa ) AS total 
											FROM
											$vestibular.inscricao
												INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
											WHERE
												id_ensino_medio <> 3
											AND
												pagamento = 0	
											AND id_curso = $row->id_curso";
									$res_pago = $coopex_antigo->query($sql_pago);
									$row_pago = $res_pago->fetch(PDO::FETCH_OBJ);
									$nao_treineiro_nao_pago = $row_pago->total;
								?>
									<tr style="background-color: <?= $cor ?>;">
										<td><?= $i ?></td>
										<td style="text-align: left !important;"><b><?= utf8_encode($row->ds_curso) ?></b></td>
										<td><?= $row->qtd ?></td>
										<td title="Porcentagem do total de inscritos"><?= number_format($row->qtd * 100 / $inscritos_total, 2) ?>%</td>
										<?
										if (!$mobile) {
										?>
											<td title="<?= number_format($pago * 100 / $row->qtd, 2) ?>%"><?= $pago ?></td>
											<td title="<?= number_format($nao_pago * 100 / $row->qtd, 2) ?>%"><?= $nao_pago ?></td>
											<td title="<?= number_format($treineiro * 100 / $row->qtd, 2) ?>%"><?= $treineiro ?></td>
											<td title="<?= number_format($treineiro_nao_pago * 100 / $row->qtd, 2) ?>%"><?= $treineiro_nao_pago ?></td>
											<td title="<?= number_format($nao_treineiro * 100 / $row->qtd, 2) ?>%"><b><?= $nao_treineiro ?></b></td>
											<td title="<?= number_format($nao_treineiro_nao_pago * 100 / $row->qtd, 2) ?>%"><?= $nao_treineiro_nao_pago ?></td>
											<td title="<?= number_format(($pago - $treineiro) * 100 / $row->qtd, 2) ?>%"><b><?= $pago - $treineiro ?></b></td>
											<td title="<?= number_format(($nao_treineiro + $nao_treineiro_nao_pago) * 100 / $row->qtd, 2) ?>%"><?= $nao_treineiro + $nao_treineiro_nao_pago ?></td>

											<td style="text-align: left !important;">
												<?
												$sql_turno = "SELECT
																* 
															FROM
																$vestibular.turno
																INNER JOIN $vestibular.curso_turno USING ( id_turno ) 
															WHERE
																id_curso = " . $row->id_curso;
												$res_turno = $coopex_antigo->query($sql_turno);

												while ($row_turno = $res_turno->fetch(PDO::FETCH_OBJ)) {
													$id_turno = $row_turno->id_turno;
													$id_curso = $row->id_curso;

													$sql_turno2 = "SELECT
																	count( * ) AS qtd,
																	turno 
																FROM
																	$vestibular.inscricao
																	INNER JOIN $vestibular.turno USING ( id_turno )
																	INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
																WHERE
																	id_curso = $id_curso 
																	AND id_campus = 1 
																	AND id_turno = $id_turno";
													$res_turno2 = $coopex_antigo->query($sql_turno2);
													$row_turno2 = $res_turno2->fetch(PDO::FETCH_OBJ)
												?>
													<div class="">
														<span title="<?= number_format($row_turno2->qtd * 100 / $row->qtd, 2) ?>%"><?= $row_turno2->qtd . " " . $row_turno2->turno ?></span>
													</div>
												<?
												}
												?>
											</td>
										<?
										}
										?>
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

		<div class="col-lg-12 d-none">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Inscritos por curso <span class="fw-300"><i>Toledo</i></span>
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<div>
							<canvas id="por_curso_toledo"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-12">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Comparativo com Edições Anteriores
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<div>
							<canvas id="comparativo"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>




		<?
		if (isset($_GET['id'])) {

		?>


			<div class="col-lg-12">
				<div id="panel-4" class="panel">
					<div class="panel-hdr">
						<h2>
							Ingressantes por curso <span class="fw-300"><i>Cascavel</i></span>
						</h2>
					</div>
					<div class="panel-container show">
						<div class="panel-content bg-subtlelight-fade">
							<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
								<thead>
									<tr>
										<th rowspan="2" style="text-align: left !important;">#</th>
										<th rowspan="2" style="text-align: left !important;">Curso</th>
										<th rowspan="2">Inscritos</th>
										<th rowspan="2">Candidatos Aptos</th>
										<th rowspan="2">Matriculados</th>
										<th rowspan="2">%</th>

									</tr>
								</thead>
								<tbody>
									<?
									$i = 0;

									$sql = "SELECT
											count( ins.id_curso ) AS qtd,
											cur.ds_curso,
											cam.ds_campus,
											ins.id_curso 
										FROM
											$vestibular.inscricao ins
											INNER JOIN $vestibular.curso cur USING ( id_curso )
											INNER JOIN $vestibular.curso_campus cca USING ( id_curso )
											INNER JOIN $vestibular.campus cam ON cam.id_campus = cca.id_campus
											INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
										WHERE
											cca.id_campus = 1 
										GROUP BY
											ins.id_curso,
											cam.id_campus 
										ORDER BY
											qtd DESC,
											cur.ds_curso";
									$res = $coopex_antigo->query($sql);

									while ($row = $res->fetch(PDO::FETCH_OBJ)) {
										$i++;

										if ($row->qtd < 15) {
											$cor = "#E8CAF3";
										} else if ($row->qtd < 40) {
											$cor = "#FDFFBB";
										} else {
											$cor = "#D5F3CA";
										}

										$sql2 = "SELECT
														sum(total) as total
													FROM
														vestibular.ingresso 
													WHERE
														id_curso = $row->id_curso
													AND	
														id_periodo_letivo = " . $_GET['id'];
										$res2 = $coopex->query($sql2);
										$row2 = $res2->fetch(PDO::FETCH_OBJ);
										$matriculados = $row2->total;

										$sql_pago = "SELECT
													count( id_pessoa ) AS total 
												FROM
												$vestibular.inscricao
													INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
												WHERE
													id_ensino_medio <> 3
												AND
													pagamento = 1	
												AND id_curso = $row->id_curso";
										$res_pago = $coopex_antigo->query($sql_pago);
										$row_pago = $res_pago->fetch(PDO::FETCH_OBJ);
										$nao_treineiro = $row_pago->total;

										$sql_pago = "SELECT
													count( id_pessoa ) AS total 
												FROM
												$vestibular.inscricao
													INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
												WHERE
													id_ensino_medio = 3
												AND
													pagamento = 0	
												AND id_curso = $row->id_curso";
										$res_pago = $coopex_antigo->query($sql_pago);
										$row_pago = $res_pago->fetch(PDO::FETCH_OBJ);
										$treineiro_nao_pago = $row_pago->total;

										$sql_pago = "SELECT
													count( id_pessoa ) AS total 
												FROM
												$vestibular.inscricao
													INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
												WHERE
													id_ensino_medio <> 3
												AND
													pagamento = 0	
												AND id_curso = $row->id_curso";
										$res_pago = $coopex_antigo->query($sql_pago);
										$row_pago = $res_pago->fetch(PDO::FETCH_OBJ);
										$nao_treineiro_nao_pago = $row_pago->total;
									?>
										<tr style="background-color: <?= $cor ?>;">
											<td><?= $i ?></td>
											<td style="text-align: left !important;"><b><?= utf8_encode($row->ds_curso) ?></b></td>
											<td><?= $row->qtd ?></td>
											<td title="Porcentagem do total de inscritos"><?= $nao_treineiro + $nao_treineiro_nao_pago ?></td>
											<td title="Porcentagem do total de inscritos"><?= $matriculados ?></td>
											<td title="Porcentagem do total de inscritos"><?= number_format($matriculados * 100 / $row->qtd, 2) ?></td>
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

		<?
		}
		?>
	</div>
</main>

<?
if (!isset($_GET['id'])) {


	$sql = "SELECT
				count(* ) AS total 
			FROM
				$vestibular.inscricao a
				INNER JOIN $vestibular.resultado b ON a.id_inscricao = b.CodCandidato
				INNER JOIN $vestibular.curso USING ( id_curso ) 
			WHERE
				id_campus = 1 
			AND
				CodCurso <> 129	
			ORDER BY
				total DESC";
	$res = $coopex_antigo->query($sql);
	$row_total_geral = $res->fetch(PDO::FETCH_OBJ);
	$row_total_geral->total;

	$sql = "SELECT
				count(* ) AS total 
			FROM
				$vestibular.inscricao a
				INNER JOIN $vestibular.resultado b ON a.id_inscricao = b.CodCandidato
				INNER JOIN $vestibular.curso USING ( id_curso ) 
			WHERE
				id_campus = 1 
			AND
				CodCurso = 129	
			ORDER BY
				total DESC";
	$res = $coopex_antigo->query($sql);
	$row_total_medicina = $res->fetch(PDO::FETCH_OBJ);
	$row_total_medicina->total;

	$sql = "SELECT
				count(*) AS total 
			FROM
				$vestibular.inscricao a
				INNER JOIN $vestibular.resultado b ON a.id_inscricao = b.CodCandidato
				INNER JOIN $vestibular.curso USING ( id_curso ) 
			WHERE
				id_campus = 1 
			AND
				motivo = 'Ausente'
			AND
				CodCurso <> 129		
			ORDER BY
				total DESC";
	$res = $coopex_antigo->query($sql);
	$row_total_ausente_geral = $res->fetch(PDO::FETCH_OBJ);
	$row_total_ausente_geral->total;

	$sql = "SELECT
				count(*) AS total 
			FROM
				$vestibular.inscricao a
				INNER JOIN $vestibular.resultado b ON a.id_inscricao = b.CodCandidato
				INNER JOIN $vestibular.curso USING ( id_curso ) 
			WHERE
				id_campus = 1 
			AND
				motivo = 'Ausente'
			AND
				CodCurso = 129		
			ORDER BY
				total DESC";
	$res = $coopex_antigo->query($sql);
	$row_total_ausente_medicina = $res->fetch(PDO::FETCH_OBJ);
	$row_total_ausente_medicina->total;

	$sql = "SELECT
				count(*) AS total 
			FROM
				$vestibular.inscricao a
				INNER JOIN $vestibular.resultado b ON a.id_inscricao = b.CodCandidato
				INNER JOIN $vestibular.curso USING ( id_curso ) 
			WHERE
				id_campus = 1 
			AND
				motivo = 'Ausente'
			ORDER BY
				total DESC";
	$res = $coopex_antigo->query($sql);
	$row_total_ausente = $res->fetch(PDO::FETCH_OBJ);

	
	$sql = "SELECT
				id_curso,
				COUNT ( id_curso )  AS total
			FROM
				integracao..view_integracao_usuario 
			WHERE
				periodo_ingresso = '20241' 
			GROUP BY
				id_curso 
			ORDER BY
				id_curso";
	$res = mssql_query($sql);
	
	while($row = mssql_fetch_object($res)){
		$matriculas[$row->id_curso] = $row->total ? $row->total : 0;
	}
				
?>
	<main id="js-page-content" role="main" class="page-content d-none">

		<div class="subheader">
			<h1 class="subheader-title">
				<i class='subheader-icon fal fa-chart-area'></i> Vestibular <span class='fw-300'>Resultados</span>
			</h1>
			<div class="d-flex mr-4">
				<div class="mr-2">
					<span class="peity-donut" data-peity="{ &quot;fill&quot;: [&quot;#967bbd&quot;, &quot;#ccbfdf&quot;],  &quot;innerRadius&quot;: 14, &quot;radius&quot;: 20 }"><?= ($inscritos_medicina * 100) / $inscritos_total ?>/100</span>
				</div>
				<div>
					<label class="fs-sm mb-0 mt-2 mt-md-0">Ausentes Medicina</label>
					<h4 class="font-weight-bold mb-0"><?= number_format(($row_total_ausente_medicina->total * 100) / $row_total_medicina->total, 2, ',', '.') ?>%</h4>
				</div>
			</div>
			<div class="d-flex mr-0">
				<div class="mr-2">
					<span class="peity-donut" data-peity="{ &quot;fill&quot;: [&quot;#2196F3&quot;, &quot;#9acffa&quot;],  &quot;innerRadius&quot;: 14, &quot;radius&quot;: 20 }"><?= ($inscritos_geral * 100) / $inscritos_total ?>/100</span>
				</div>
				<div>
					<label class="fs-sm mb-0 mt-2 mt-md-0">Ausentes Geral</label>
					<h4 class="font-weight-bold mb-0"><?= number_format(($row_total_ausente_geral->total * 100) / $row_total_geral->total, 2, ',', '.') ?></h4>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<div id="panel-4" class="panel">
					
					<div class="panel-hdr">
						<h2>
							Resultado<span class="fw-300"><i>Campus Cascavel</i></span>
						</h2>
						Média Geral de Ausentes:&nbsp;&nbsp; <strong>
							<td><?= number_format($row_total_ausente->total * 100 / $row_total_geral->total, 2) ?>%</td>
						</strong>
					</div>
					<div class="panel-container show">
						<div class="panel-content bg-subtlelight-fade">
							<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
								<thead>
									<tr>
										<th style="text-align: left !important;">#</th>
										<th style="text-align: left !important;">Curso</th>
										<?
											if (!$mobile) {
										?>
										<th>Inscritos</th>
										<th>Confirmados</th>
										<th>Presentes</th>
										<th>Ausentes</th>
										<th>% Ausentes</th>
										<?
											}
										?>
										<th>Matriculados</th>
								</thead>
								<tbody>
									<?
									$i = 0;


									$sql = "SELECT
											id_curso,
											ds_curso,
											count(* ) AS total 
										FROM
											$vestibular.inscricao a
											LEFT JOIN $vestibular.resultado b ON a.id_inscricao = b.CodCandidato
											INNER JOIN $vestibular.curso USING ( id_curso ) 
										WHERE
											id_campus = 1 
										GROUP BY
											id_curso 
										ORDER BY
											total DESC";
									$res = $coopex_antigo->query($sql);

									while ($row = $res->fetch(PDO::FETCH_OBJ)) {
										$i++;
										$sql = "SELECT
													count(*) AS total 
												FROM
													$vestibular.inscricao a
													LEFT JOIN $vestibular.resultado b ON a.id_inscricao = b.CodCandidato
													INNER JOIN $vestibular.curso USING ( id_curso ) 
												WHERE
													id_curso = $row->id_curso
												AND
													pagamento = 1		
												GROUP BY
													id_curso 
												ORDER BY
													total DESC";
										$res_pago = $coopex_antigo->query($sql);
										$row_pago = $res_pago->fetch(PDO::FETCH_OBJ);

										$sql = "SELECT
													count(*) AS total 
												FROM
													$vestibular.inscricao a
													INNER JOIN $vestibular.resultado b ON a.id_inscricao = b.CodCandidato
													INNER JOIN $vestibular.curso USING ( id_curso ) 
												WHERE
													id_curso = $row->id_curso
												AND
													motivo = 'Ausente' 		
												GROUP BY
													id_curso 
												ORDER BY
													total DESC";
										$res_ausente = $coopex_antigo->query($sql);
										$row_ausente = $res_ausente->fetch(PDO::FETCH_OBJ);
									?>
										<tr style="background-color: <?= $cor ?>;">
											<td><?= $i ?></td>
											<td style="text-align: left !important;"><b><?= utf8_encode($row->ds_curso) ?></b></td>
											<?
												if (!$mobile) {
											?>
											<td><?= $row->total ?></td>
											<td><?= $row_pago->total ?></td>
											<td><?= isset($row_ausente->total) ? $row_pago->total - $row_ausente->total : 0 ?></td>
											<td><?= isset($row_ausente->total) ? $row_ausente->total : 0 ?></td>
											<td><?= number_format(@$row_ausente->total * 100 / $row->total, 2) ?>%</td>
											<?
												}
											?>
											<td><?=isset($matriculas[$row->id_curso]) ? $matriculas[$row->id_curso] : 0?></td>
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

<?
}
?>

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
	const ctx4 = document.getElementById('por_curso');
	new Chart(ctx4, {
		type: 'bar',
		data: {
			labels: [<?= implode(",", $curso) ?>],
			datasets: [{
				label: ' Inscrições: ',
				data: [<?= implode(",", $qtd) ?>],
				borderWidth: 1
			}]
		},
		options: {
			indexAxis: 'y',
			scales: {
				y: {
					beginAtZero: true
				}
			}
		}
	});


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
					id_campus = 2	
				GROUP BY
					id_curso
				ORDER BY qtd DESC";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$curso[] = "'" . utf8_encode($row->curso) . "'";
		$qtd[] = $row->qtd;
	}
	?>

	/*const ctx7 = document.getElementById('por_curso_toledo');
	new Chart(ctx7, {
		type: 'bar',
		data: {
			labels: [<?= implode(",", $curso) ?>],
			datasets: [{
				label: ' Inscrições: ',
				data: [<?= implode(",", $qtd) ?>],
				borderWidth: 1
			}]
		},
		options: {
			indexAxis: 'y',
			scales: {
				y: {
					beginAtZero: true
				}
			}
		}
	});*/
	//INSCRIÇÕES POR CURSO----------------------------


	//INSCRIÇÕES POR DIA
	<?
	unset($qtd);
	unset($dia);
	$sql = "SELECT
			concat(DAY(dt_inscricao), '/' , MONTH(dt_inscricao)) as dia,
			COUNT( id_inscricao ) AS qtd 
		FROM
			$vestibular.inscricao 
		GROUP BY
			DATE (
			dt_inscricao 
			)";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$dia[] = "'" . $row->dia . "'";
		$qtd[] = $row->qtd;
	}

	$sql = "SELECT
			DATE(dt_inscricao) as dia,
				COUNT( id_inscricao ) AS qtd 
			FROM
				$vestibular.inscricao 
			WHERE
				id_curso = 1000000115 
			GROUP BY
			DATE(
				dt_inscricao 
				)";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$qtd_medicina[] = $row->qtd;
	}

	$sql = "SELECT
			DATE(dt_inscricao) as dia,
				COUNT( id_inscricao ) AS qtd 
			FROM
				$vestibular.inscricao 
			WHERE
				id_curso <> 1000000115 
			GROUP BY
			DATE(
				dt_inscricao 
				)";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$qtd_geral[] = $row->qtd;
	}


	?>
	const ctx3 = document.getElementById('por_dia');
	new Chart(ctx3, {
		type: 'bar',
		data: {
			labels: [<?= implode(",", $dia) ?>],
			datasets: [{
				label: ' Inscrições: ',
				data: [<?= implode(",", $qtd) ?>],
				borderWidth: 1,
				order: 3,
			}, {
				label: 'Medicina',
				data: [<?= implode(",", $qtd_medicina) ?>],
				type: 'line',
				// this dataset is drawn on top
				order: 1,
				pointStyle: 'circle',
				pointRadius: 5,
				pointHoverRadius: 15
			}, {
				label: 'Geral',
				data: [<?= implode(",", $qtd_geral) ?>],
				type: 'line',
				// this dataset is drawn on top
				order: 2,
				pointStyle: 'circle',
				pointRadius: 5,
				pointHoverRadius: 15
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



	//INSCRIÇÕES POR DIA DA SEMANA
	<?
	unset($qtd);
	$sql = "SELECT
				CASE
						
					WHEN
						WEEKDAY( dt_inscricao ) = 6 THEN
							'Domingo' 
							WHEN WEEKDAY( dt_inscricao ) = 0 THEN
							'Segunda' 
							WHEN WEEKDAY( dt_inscricao ) = 1 THEN
							'Terça' 
							WHEN WEEKDAY( dt_inscricao ) = 2 THEN
							'Quarta' 
							WHEN WEEKDAY( dt_inscricao ) = 3 THEN
							'Quinta' 
							WHEN WEEKDAY( dt_inscricao ) = 4 THEN
							'Sexta' 
							WHEN WEEKDAY( dt_inscricao ) = 5 THEN
							'Sábado' 
						END AS dia_da_semana,
						COUNT( id_inscricao ) AS qtd,
						WEEKDAY( dt_inscricao ) 
					FROM
						$vestibular.inscricao 
					GROUP BY
						WEEKDAY( dt_inscricao ) 
					ORDER BY
						WEEKDAY(
						dt_inscricao 
					)";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$dia_da_semana[] = "'" . $row->dia_da_semana . "'";
		$qtd[] = $row->qtd;
	}
	unset($qtd_medicina);
	$sql = "SELECT
				COUNT( id_inscricao ) AS qtd 
			FROM
				$vestibular.inscricao 
			WHERE
				id_curso = 1000000115 
			GROUP BY
				WEEKDAY( dt_inscricao ) 
			ORDER BY
				WEEKDAY(
				dt_inscricao 
				)";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$qtd_medicina[] = $row->qtd;
	}

	unset($qtd_geral);
	$sql = "SELECT
				COUNT( id_inscricao ) AS qtd 
			FROM
				$vestibular.inscricao 
			WHERE
				id_curso <> 1000000115 
			GROUP BY
				WEEKDAY( dt_inscricao ) 
			ORDER BY
				WEEKDAY(
				dt_inscricao 
				)";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$qtd_geral[] = $row->qtd;
	}



	?>
	const ctx = document.getElementById('dia_da_semana');
	new Chart(ctx, {
		type: 'bar',
		data: {
			labels: [<?= implode(",", $dia_da_semana) ?>],
			datasets: [{
				label: ' Inscrições: ',
				data: [<?= implode(",", $qtd) ?>],
				borderWidth: 1,
				order: 3
			}, {
				label: 'Medicina',
				data: [<?= implode(",", $qtd_medicina) ?>],
				type: 'line',
				// this dataset is drawn on top
				order: 1,
				pointStyle: 'circle',
				pointRadius: 5,
				pointHoverRadius: 15
			}, {
				label: 'Geral',
				data: [<?= implode(",", $qtd_geral) ?>],
				type: 'line',
				// this dataset is drawn on top
				order: 2,
				pointStyle: 'circle',
				pointRadius: 5,
				pointHoverRadius: 15
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
	//INSCRIÇÕES POR DIA DA SEMANA----------------------------



	//INSCRIÇÕES POR HORA
	<?
	unset($qtd);
	unset($qtd_medicina);
	unset($qtd_geral);

	for ($i = 0; $i < 24; $i++) {

		$sql = "SELECT
					COUNT( id_inscricao ) AS qtd,
					HOUR( dt_inscricao ) AS hora
				FROM
					$vestibular.inscricao 
				WHERE
					HOUR( dt_inscricao ) = $i	
				GROUP BY
					HOUR(dt_inscricao) 
				ORDER BY
					HOUR(dt_inscricao)";
		$res = $coopex_antigo->query($sql);
		$row = $res->fetch(PDO::FETCH_OBJ);
		$hora[] = "'$i'";
		$qtd[] = isset($row->qtd) ? $row->qtd : 0;

		$sql = "SELECT
					COUNT( id_inscricao ) AS qtd 
				FROM
					$vestibular.inscricao 
				WHERE
					id_curso = 1000000115 
				AND
					HOUR( dt_inscricao ) = $i	
				GROUP BY
					HOUR( dt_inscricao ) 
				ORDER BY
					HOUR(dt_inscricao)";
		$res = $coopex_antigo->query($sql);
		$row = $res->fetch(PDO::FETCH_OBJ);
		$qtd_medicina[] = isset($row->qtd) ? $row->qtd : 0;


		$sql = "SELECT
					COUNT( id_inscricao ) AS qtd 
				FROM
					$vestibular.inscricao 
				WHERE
					id_curso <> 1000000115 
				AND
					HOUR( dt_inscricao ) = $i	
				GROUP BY
					HOUR( dt_inscricao ) 
				ORDER BY
					HOUR(dt_inscricao)";
		$res = $coopex_antigo->query($sql);
		$row = $res->fetch(PDO::FETCH_OBJ);
		$qtd_geral[] = isset($row->qtd) ? $row->qtd : 0;
	}



	?>
	const ctx30 = document.getElementById('hora');
	new Chart(ctx30, {
		type: 'bar',
		data: {
			labels: [<?= implode(",", $hora) ?>],
			datasets: [{
				label: ' Inscrições: ',
				data: [<?= implode(",", $qtd) ?>],
				borderWidth: 1,
				order: 3
			}, {
				label: 'Medicina',
				data: [<?= implode(",", $qtd_medicina) ?>],
				type: 'line',
				// this dataset is drawn on top
				order: 1,
				pointStyle: 'circle',
				pointRadius: 5,
				pointHoverRadius: 15
			}, {
				label: 'Geral',
				data: [<?= implode(",", $qtd_geral) ?>],
				type: 'line',
				// this dataset is drawn on top
				order: 2,
				pointStyle: 'circle',
				pointRadius: 5,
				pointHoverRadius: 15
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
				COUNT( id_inscricao ) AS qtd 
			FROM
				$vestibular.inscricao 
			WHERE
				pagamento = 0";
	$res = $coopex_antigo->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);
	$qtd_nao_pago = $row->qtd;

	$sql = "SELECT
				COUNT( id_inscricao ) AS qtd 
			FROM
				$vestibular.inscricao 
			WHERE
				pagamento = 1 
				AND id_cortesia = 0";
	$res = $coopex_antigo->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);
	$qtd_boleto = $row->qtd;

	$sql = "SELECT
				COUNT( id_inscricao ) AS qtd 
			FROM
				$vestibular.inscricao 
			WHERE
				pagamento = 1 
				AND id_cortesia > 0";
	$res = $coopex_antigo->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);
	$qtd_cortesia = $row->qtd;

	?>
	const ctx2 = document.getElementById('pagamentos');
	new Chart(ctx2, {
		type: 'doughnut',
		data: {
			labels: ['Boleto', 'Não Pagos', 'Cortesia'],
			datasets: [{
				label: ' Inscrições: ',
				data: [<?= $qtd_boleto ?>, <?= $qtd_nao_pago ?>, <?= $qtd_cortesia ?>],
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


	//TOP 5 CIDADES
	<?
	unset($curso);
	unset($total);
	$sql = "SELECT
				ds_cidade as curso,
				count(*) AS total 
			FROM
				$vestibular.pessoa
				INNER JOIN $vestibular.inscricao USING ( id_pessoa ) 
			WHERE
				ds_cidade <> 'Cascavel' 
			GROUP BY
				ds_cidade 
			ORDER BY
				total DESC 
				LIMIT 10";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$curso[] = "'" . utf8_encode($row->curso) . "'";
		$total[] = $row->total;
	}

	?>
	const ctx22 = document.getElementById('top5_cidades');
	new Chart(ctx22, {
		type: 'doughnut',
		data: {
			labels: [<?= implode(",", $curso) ?>],
			datasets: [{
				label: ' Inscrições: ',
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
	//TOP 5 CIDADES----------------------------


	//TOP 5 ESTADOS
	<?
	unset($estado);
	unset($total);
	$sql = "SELECT
				sg_estado as estado,
				count(*) AS total 
			FROM
				$vestibular.pessoa
				INNER JOIN $vestibular.inscricao USING ( id_pessoa ) 
			WHERE
			sg_estado <> 'PR' 
			GROUP BY
				sg_estado 
			ORDER BY
				total DESC 
				LIMIT 5";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$estado[] = "'" . utf8_encode($row->estado) . "'";
		$total[] = $row->total;
	}

	?>
	const ctx222 = document.getElementById('top5_estados');
	new Chart(ctx222, {
		type: 'doughnut',
		data: {
			labels: [<?= implode(",", $estado) ?>],
			datasets: [{
				label: ' Inscrições: ',
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







	//COMPRATIVO
	<?
	unset($qtd);
	unset($dia);
	$i = 1;
	$sql = "SELECT
				count( * ) as qtd,
				date(dt_inscricao) as data
			FROM
				$vestibular.inscricao 
			GROUP BY
				date( dt_inscricao ) 
			ORDER BY
				dt_inscricao";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$data_20242[] = "'" . $row->data . "'";
		$dia_20242[] = $i;
		$qtd_20242[] = $row->qtd;
		$i++;
	}

	$sql = "SELECT
				count( * ) as qtd,
				date(dt_inscricao) as data
			FROM
				vestibular_20241.inscricao 
			GROUP BY
				date( dt_inscricao ) 
			ORDER BY
				dt_inscricao";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$data_20241[] = "'" . $row->data . "'";
		$dia_20241[] = $i;
		$qtd_20241[] = $row->qtd;
		$i++;
	}



	$sql = "SELECT
				count( * ) as qtd,
				date(dt_inscricao) as data
			FROM
				vestibular_20231.inscricao 
			GROUP BY
				date( dt_inscricao ) 
			ORDER BY
				dt_inscricao
			LIMIT 0, $i";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$qtd_20231[] = $row->qtd;
	}

	$sql = "SELECT
				count( * ) as qtd,
				date(dt_inscricao) as data
			FROM
				vestibular_20221.inscricao 
			WHERE date(dt_inscricao) > '2021-08-23'
			GROUP BY
				date( dt_inscricao ) 
			ORDER BY
				dt_inscricao
			LIMIT 0, $i";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$qtd_20221[] = $row->qtd;
	}

	$sql = "SELECT
				count( * ) as qtd,
				date(dt_inscricao) as data
			FROM
				vestibular_20211.inscricao 
			GROUP BY
				date( dt_inscricao ) 
			ORDER BY
				dt_inscricao
			LIMIT 0, $i";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$qtd_20211[] = $row->qtd;
	}

	$sql = "SELECT
				count( * ) as qtd,
				date(dt_inscricao) as data
			FROM
				vestibular_20201.inscricao 
			WHERE date(dt_inscricao) > '2019-09-02'	
			GROUP BY
				date( dt_inscricao ) 
			ORDER BY
				dt_inscricao
			LIMIT 0, $i";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$qtd_20201[] = $row->qtd;
	}

	$sql = "SELECT
				count( * ) as qtd,
				date(dt_inscricao) as data
			FROM
				vestibular_20191.inscricao 
			GROUP BY
				date( dt_inscricao ) 
			ORDER BY
				dt_inscricao
			LIMIT 0, $i";
	$res = $coopex_antigo->query($sql);
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		$qtd_20191[] = $row->qtd;
	}

	?>
	var atual = [<?= implode(",", $qtd_20241) ?>];

	const ctx10 = document.getElementById('comparativo');
	new Chart(ctx10, {
		type: 'bar',
		data: {
			labels: [<?= implode(",", $dia_20241) ?>],
			datasets: [{
				label: '2024/1 - Atual',
				data: [<?= implode(",", $qtd_20241) ?>],
				borderWidth: 1,
				order: 3,
			}, {
				label: '2023/1 - Verão',
				data: [<?= implode(",", $qtd_20231) ?>],
				type: 'line',
				// this dataset is drawn on top
				order: 1,
				pointStyle: 'circle',
				pointRadius: 5,
				pointHoverRadius: 15
			}, {
				label: '2022/1 - Verão',
				data: [<?= implode(",", $qtd_20221) ?>],
				type: 'line',
				// this dataset is drawn on top
				order: 2,
				pointStyle: 'circle',
				pointRadius: 5,
				pointHoverRadius: 15
			}, {
				label: '2021/1 - Verão',
				data: [<?= implode(",", $qtd_20211) ?>],
				type: 'line',
				// this dataset is drawn on top
				order: 2,
				pointStyle: 'circle',
				pointRadius: 5,
				pointHoverRadius: 15
			}, {
				label: '2020/1 - Verão',
				data: [<?= implode(",", $qtd_20201) ?>],
				type: 'line',
				// this dataset is drawn on top
				order: 2,
				pointStyle: 'circle',
				pointRadius: 5,
				pointHoverRadius: 15
			}, {
				label: '2019/1 - Verão',
				data: [<?= implode(",", $qtd_20191) ?>],
				type: 'line',
				// this dataset is drawn on top
				order: 2,
				pointStyle: 'circle',
				pointRadius: 5,
				pointHoverRadius: 15
			}]
		},
		options: {
			scales: {
				y: {
					beginAtZero: true
				}
			},
			plugins: {
				tooltip: {
					callbacks: {
						afterBody: function(data) {
							console.log(data[0]);
							var context = data[0];
							var calc = context.parsed.y * 100 / atual[context.dataIndex];

							if (context.parsed.y > atual[context.dataIndex]) {
								var calc = calc - 100
								label = calc.toFixed(1) + "% melhor que o atual";
							} else {
								var calc = calc - 100
								label = Math.abs(calc.toFixed(1)) + "% pior que o atual";
							}

							var multistringText = [''];
							multistringText.push(context.parsed.y + ' Inscritos');
							multistringText.push(label);

							return multistringText;
						}
					},
					titleFont: {
						size: 14
					},
					bodyFont: {
						size: 14
					},
					footerFont: {
						size: 14
					}
				}
			}
		}
	});
	//COMPRATIVO----------------------------
</script>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<?
unset($uf);
$sql = "SELECT
				sg_estado,
				count(*) AS qtd,
				ds_pais
			FROM
				$vestibular.inscricao
				INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
			GROUP BY
				sg_estado";
$res = $coopex_antigo->query($sql);
while ($row = $res->fetch(PDO::FETCH_OBJ)) {
	if ($row->ds_pais == 'Brasil') {
		$uf[] = "['BR-$row->sg_estado', $row->qtd]";
	} else {
		$uf[] = "['PY', $row->qtd]";
	}
}
?>
<script type="text/javascript">
	google.charts.load('current', {
		'packages': ['geochart'],
		// Note: Because this chart requires geocoding, you'll need mapsApiKey.
		// See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
		'mapsApiKey': 'AIzaSyCFPS0Z8hQn5l_5aKmDrMSjffNqYDDmGfQ'
	});
	google.charts.setOnLoadCallback(drawRegionsMap);

	function drawRegionsMap() {
		var data = google.visualization.arrayToDataTable([
			['State', 'Views'],
			<?= implode(",", $uf) ?>

		]);

		var options = {
			region: 'BR',
			resolution: 'provinces',
			colorAxis: {
				colors: ['#00853f', 'black', '#e31b23']
			},
			backgroundColor: '#ffffff',
			datalessRegionColor: '#F5FCFF',
			defaultColor: '#F5FCFF',
		};

		var chart = new google.visualization.GeoChart(document.getElementById('geochart-colors'));
		google.visualization.events.addListener(chart, 'select', stateClickHandler);

		chart.draw(data, options);

		function stateClickHandler() {
			var selectedItem = chart.getSelection()[0];
			if (selectedItem) {
				var stateName = data.getValue(selectedItem.row, 0); // Obtém o nome do estado
				// Chame a função que você deseja com o nome do estado como parâmetro
				suaFuncao(stateName);
			}
		}

	};

	function suaFuncao(stateName) {
		uf = stateName.split("-");
		$("#tabela_cidade").load("modulos/dashboard/direcao/ajax/carrega_cidade.php?uf=" + uf[1] + "&vestibular=" + '<?= $vestibular ?>');
	}
</script>