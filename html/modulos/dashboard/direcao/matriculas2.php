<?php

require_once("php/sqlsrv.php");
//require_once("./php/utils.php");

//$id_pessoa = $_GET['id_usuario'];



if (isset($_GET['id'])) {
	$vestibular = "vestibular_" . $_GET['id'];
	$periodo = $_GET['id'];
} else {
	$vestibular = "vestibular";
	$periodo = "20241";
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

$sql = "SELECT
				id_curso,
				COUNT ( id_curso )  AS total
			FROM
				integracao..view_integracao_usuario 
			WHERE
				periodo_ingresso = '$periodo' 
			GROUP BY
				id_curso 
			ORDER BY
				id_curso";
$res = mssql_query($sql);

$matriculas_total = 0;
while ($row = mssql_fetch_object($res)) {
	$matriculas[$row->id_curso] = $row->total ? $row->total : 0;
	$matriculas_total += $row->total;
}

$sql = "SELECT
				id_curso,
				COUNT ( id_curso )  AS total
			FROM
				integracao..view_integracao_usuario 
			WHERE
				periodo_ingresso = '$periodo' 
				AND id_faculdade = 1000000002 	
			GROUP BY
				id_curso 
			ORDER BY
				id_curso";
$res = mssql_query($sql);

$matriculas_total_cascavel = 0;
while ($row = mssql_fetch_object($res)) {
	$matriculas[$row->id_curso] = $row->total ? $row->total : 0;
	$matriculas_total_cascavel += $row->total;
}

$sql = "SELECT
				id_curso,
				COUNT ( id_curso )  AS total
			FROM
				integracao..view_integracao_usuario 
			WHERE
				periodo_ingresso = '$periodo' 
				AND id_faculdade = 1100000002 	
			GROUP BY
				id_curso 
			ORDER BY
				id_curso";
$res = mssql_query($sql);

$matriculas_total_toledo = 0;
while ($row = mssql_fetch_object($res)) {
	$matriculas[$row->id_curso] = $row->total ? $row->total : 0;
	$matriculas_total_toledo += $row->total;
}


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

	<!-- <div class="btn-group<?= $mobile ? "-vertical" : "" ?>" role="group" aria-label="Group C">
		<a href="https://coopex.fag.edu.br/dashboard/direcao/matriculas" type="button" class="btn btn-<?= !isset($_GET['id']) ? "primary" : "light" ?> waves-effect waves-themed">2024/1</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/matriculas/20232" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20232 ? "primary" : "light" ?> waves-effect waves-themed">2023/2</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/matriculas/20231" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20231 ? "primary" : "light" ?> waves-effect waves-themed">2023/1</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/matriculas/20222" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20222 ? "primary" : "light" ?> waves-effect waves-themed">2022/2</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/matriculas/20221" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20221 ? "primary" : "light" ?> waves-effect waves-themed">2022/1</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/matriculas/20212" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20212 ? "primary" : "light" ?> waves-effect waves-themed">2021/2</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/matriculas/20211" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20211 ? "primary" : "light" ?> waves-effect waves-themed">2021/1</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/matriculas/20202" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20202 ? "primary" : "light" ?> waves-effect waves-themed">2020/2</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/matriculas/20201" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20201 ? "primary" : "light" ?> waves-effect waves-themed">2020/1</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/matriculas/20192" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20192 ? "primary" : "light" ?> waves-effect waves-themed">2019/2</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/matriculas/20191" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20191 ? "primary" : "light" ?> waves-effect waves-themed">2019/1</a>
	</div> -->

	<div class="row mt-4">
		<div class="col-sm-6 col-xl-5" title="Total Geral de Inscritos">
			<div class="p-3 bg-primary-300 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $matriculas_total ?>
						<small class="m-0 l-h-n">Matrículas Total</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>

		<div class="col-sm-6 col-xl-4" title="Total Geral de Inscritos">
			<div class="p-3 bg-success-500 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $matriculas_total_cascavel ?>
						<small class="m-0 l-h-n">Matrículas Cascavel</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>

		<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
			<div class="p-3 bg-danger-500 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $matriculas_total_toledo ?>
						<small class="m-0 l-h-n">Matrículas Toledo</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<div id="panel-4" class="panel">
				<?


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
				periodo_ingresso = '$periodo' 
			GROUP BY
				id_curso 
			ORDER BY
				id_curso";
					$res = mssql_query($sql);

					while ($row = mssql_fetch_object($res)) {
						$matriculas[$row->id_curso] = $row->total ? $row->total : 0;
					}

				?>
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
										<th style="text-align: left !important;">Curso</th>
										<th>Matriculados</th>
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
											<td style="text-align: left !important;"><b><?= utf8_encode($row->ds_curso) ?></b></td>
											<td><?= isset($matriculas[$row->id_curso]) ? $matriculas[$row->id_curso] : 0 ?></td>
											<?
											if (!$mobile) {
											?>
												<td><?= $row->total ?></td>
												<td><?= $row_pago->total ?></td>
												<td><?= isset($row_pago->total) ? $row_pago->total - $row_ausente->total : 0 ?></td>
												<td><?= isset($row_ausente->total) ? $row_ausente->total : 0 ?></td>
												<td><?= number_format(@$row_ausente->total * 100 / $row->total, 2) ?>%</td>
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
	</div>


	<div class="row">
		<div class="col-lg-12">
			<div id="panel-4" class="panel">
				<?
					if (!isset($_GET['id'])) {


						$sql = "SELECT
				count(* ) AS total 
			FROM
				$vestibular.inscricao a
				INNER JOIN $vestibular.resultado b ON a.id_inscricao = b.CodCandidato
				INNER JOIN $vestibular.curso USING ( id_curso ) 
			WHERE
				id_campus = 2 
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
				id_campus = 2 
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
				id_campus = 2 
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
				id_campus = 2 
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
				id_campus = 2 
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
				periodo_ingresso = '$periodo' 
			GROUP BY
				id_curso 
			ORDER BY
				id_curso";
						$res = mssql_query($sql);

						while ($row = mssql_fetch_object($res)) {
							$matriculas[$row->id_curso] = $row->total ? $row->total : 0;
						}
					}
				?>
				<div class="panel-hdr">
					<h2>
						Resultado<span class="fw-300"><i>Campus Toledo</i></span>
					</h2>
					Média Geral de Ausentes:&nbsp;&nbsp; <strong>
						<td><?= number_format($row_total_ausente->total * 100 / $row_total_geral->total, 2) ?>%</td>
					</strong>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="dt-basic-example2" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th style="text-align: left !important;">Curso</th>
									<th>Matriculados</th>
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
											id_campus = 2 
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
										<td style="text-align: left !important;"><b><?= utf8_encode($row->ds_curso) ?></b></td>
										<td><?= isset($matriculas[$row->id_curso]) ? $matriculas[$row->id_curso] : 0 ?></td>
										<?
										if (!$mobile) {
										?>
											<td><?= $row->total ?></td>
											<td><?= $row_pago->total ?></td>
											<td><?= isset($row_ausente->total) ? $row_pago->total - $row_ausente->total : 0 ?></td>
											<td><?= isset($row_ausente->total) ? $row_pago->total : 0 ?></td>
											<td><?= number_format(@$row_ausente->total * 100 / $row->total, 2) ?>%</td>
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
	const ctx7 = document.getElementById('por_curso_toledo');
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
	});
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
		$hora[] = "'" . $row->hora . "'";
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

	$(document).ready(function() {

		$('.select2').select2();


		$('#dt-basic-example').dataTable({
			pageLength: 999,
			order: [
				[1, 'desc']
			]
		});

		$('#dt-basic-example2').dataTable({
			pageLength: 999,
			order: [
				[1, 'desc']
			]
		});
	});
</script>