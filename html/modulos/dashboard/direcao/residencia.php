<?php

//require_once("./php/mysql.php");
//require_once("./php/utils.php");

//$id_pessoa = $_GET['id_usuario'];



if (isset($_GET['id'])) {
	$vestibular = "residencia_" . $_GET['id'];
} else {
	$vestibular = "residencia";
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


function treineiro($id_treineiro, $pagamento)
{
	global $vestibular;
	global $coopex_antigo;

	$sql = "SELECT
				count( id_pessoa ) AS total 
			FROM
			$vestibular.inscricao
				INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
			WHERE
				pagamento = $pagamento
			AND
				id_ensino_medio = $id_treineiro";
	$res = $coopex_antigo->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);
	return $row->total;
}
$treineiro_confirmado = treineiro(0, 1);
$treineiro_nao_confirmado = treineiro(0, 0);

$medico_confirmado = treineiro(1, 1);
$medico_nao_confirmado = treineiro(1, 0);

$formando_confirmado = treineiro(2, 1);
$formando_nao_confirmado = treineiro(2, 0);

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
		<li class="breadcrumb-item active">Residência Médica Dashboard</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>

	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-chart-area'></i> Residência Médica <span class='fw-300'>Dashboard</span>
		</h1>

	</div>



	<div class="btn-group<?= $mobile ? "-vertical" : "" ?>" role="group" aria-label="Group C">
		<a href="https://coopex.fag.edu.br/dashboard/direcao/residencia" type="button" class="btn btn-<?= !isset($_GET['id']) ? "primary" : "light" ?> waves-effect waves-themed">2024</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/residencia/5" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20232 ? "primary" : "light" ?> waves-effect waves-themed">2023/2</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/residencia/4" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20232 ? "primary" : "light" ?> waves-effect waves-themed">2023</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/residencia/3" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20231 ? "primary" : "light" ?> waves-effect waves-themed">2022/2</a>
		<a href="https://coopex.fag.edu.br/dashboard/direcao/residencia/2" type="button" class="btn btn-<?= isset($_GET['id']) && $_GET['id'] == 20222 ? "primary" : "light" ?> waves-effect waves-themed">2022</a>
	</div>

	<div class="row mt-5">
		<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
			<div class="p-3 bg-primary-300 rounded overflow-hidden position-relative text-white mb-g">
				<div class="" style="height: 188px;">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $inscritos_total ?>
						<small class="m-0 l-h-n">Inscritos Total</small>
						<small class="m-0 l-h-n">Média diária: <strong><?= round($media) ?></strong> inscrições</small>
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
		<div class="col-sm-6 col-xl-3">
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
						<?= $treineiro_confirmado ?>
						<small class="m-0 l-h-n">Treineiros</small>
					</h3>
				</div>
				<i class="fal fa-check-circle position-absolute pos-right pos-bottom opacity-15  mb-n1 mr-n4" style="font-size: 6rem;"></i>
			</div>
			<div title="Total de Treineiros Não Confirmados" class="p-3 bg-primary-300 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $treineiro_nao_confirmado ?>
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
						<?= $medico_confirmado ?>
						<small class="m-0 l-h-n">Médico</small>
					</h3>
				</div>
				<i class="fal fa-check-circle position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n4" style="font-size: 6rem;"></i>
			</div>
			<div title="Ensino Médio Concluído Não Confirmados" class="p-3 bg-danger-500 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $medico_nao_confirmado ?>
						<small class="m-0 l-h-n">Médico</small>
					</h3>
				</div>
				<i class="fal fa-times-circle position-absolute pos-right pos-bottom opacity-15 mb-n5 mr-n6" style="font-size: 8rem;"></i>
			</div>
		</div>
		<div class="col-sm-6 col-xl-2">
			<div title="Ensino Médio Concluído Confirmados" class="p-3 bg-success-600 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $formando_confirmado ?>
						<small class="m-0 l-h-n">Formando</small>
					</h3>
				</div>
				<i class="fal fa-check-circle position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n4" style="font-size: 6rem;"></i>
			</div>
			<div title="Ensino Médio Concluído Não Confirmados" class="p-3 bg-danger-500 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $formando_nao_confirmado ?>
						<small class="m-0 l-h-n">Formando</small>
					</h3>
				</div>
				<i class="fal fa-times-circle position-absolute pos-right pos-bottom opacity-15 mb-n5 mr-n6" style="font-size: 8rem;"></i>
			</div>
		</div>

		<?
			if(!isset($_GET['id'])){
		?>
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
	<?
		}
	?>



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



		<?
		$sql = "SELECT
						count( ins.id_curso ) AS qtd,
						cur.ds_curso,
						ins.id_curso 
					FROM
						$vestibular.inscricao ins
						INNER JOIN $vestibular.curso cur USING ( id_curso )
						INNER JOIN $vestibular.pessoa USING ( id_pessoa ) 
					GROUP BY
						ins.id_curso 
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
						Inscritos por especialidade
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
										<th colspan="2" title="Ensimo Médio Concluído">Médicos/Formandos</th>
										<th rowspan="2" title="Candidatos Aptos">Candidatos<br>Aptos</th>
										<th rowspan="2 title=" Possíveis Candidaros">Candidatos<br>em Potencial</th>
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
						Inscritos por especialidade
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

		<div class="col-lg-6">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Instituição de Origem
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table class="table table-sm">
							<?
							$sql = "SELECT
										pessoal_instituicao,
										count(*) AS qtd 
									FROM
										$vestibular.pessoa
									GROUP BY
										pessoal_instituicao 
									ORDER BY
										qtd DESC";
							$res = $coopex_antigo->query($sql);
							while ($row = $res->fetch(PDO::FETCH_OBJ)) {
							?>
								<tr>
									<td class="text-left"><?= strtoupper(utf8_encode($row->pessoal_instituicao)) ?></td>
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

		<div class="col-lg-6">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Ano de Conclusão
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table class="table table-sm">
							<?
							$sql = "SELECT
										pessoal_instituicao_ano,
										count(*) AS qtd 
									FROM
										$vestibular.pessoa
									GROUP BY
									pessoal_instituicao_ano 
									ORDER BY
										qtd DESC";
							$res = $coopex_antigo->query($sql);
							while ($row = $res->fetch(PDO::FETCH_OBJ)) {
								$ano = $row->pessoal_instituicao_ano ? $row->pessoal_instituicao_ano : "NÃO INFORMADO";
							?>
								<tr>
									<td class="text-left"><?= $ano ?></td>
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

		<div class="col-12">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Relação de Inscritos
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table class="table table-sm">
							<tr>
								<td class="text-left">Nome</td>
								<td class="text-left">Curso</td>
								<td>Pago</td>
								<td>Telefone</td>
								<td>Telefone</td>
							</tr>
							<?
							$sql = "SELECT
										ds_nome,
										ds_curso,
										pagamento,
										nu_telefone1,
										nu_telefone2,
										id_inscricao
									FROM
										$vestibular.inscricao
										INNER JOIN $vestibular.pessoa USING ( id_pessoa )
										INNER JOIN $vestibular.curso USING ( id_curso ) 
									ORDER BY
										ds_nome";
							$res = $coopex_antigo->query($sql);
							while ($row = $res->fetch(PDO::FETCH_OBJ)) {
								$pago = $row->pagamento ? "SIM" : "<a href='https://www2.fag.edu.br/residencia/inscricao/informacoes/boleto/".$row->id_inscricao."'>BOLETO</a>";
								$nu_telefone1 = preg_replace('/\D/', '', $row->nu_telefone1);
								$nu_telefone2 = preg_replace('/\D/', '', $row->nu_telefone2);
							?>
								<tr>
									<td class="text-left"><?= utf8_encode($row->ds_nome) ?></td>
									<td class="text-left"><?= utf8_encode($row->ds_curso) ?></td>
									<td><?= $pago  ?></td>
									<td><a href="https://api.whatsapp.com/send?phone=55<?=$nu_telefone1?>"><?= ($row->nu_telefone1) ?></a></td>
									<td><a href="https://api.whatsapp.com/send?phone=55<?=$nu_telefone1?>"><?= ($row->nu_telefone2) ?></a></td>
								</tr>
							<?
							}
							?>
						</table>
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
		$hora[] = "'" . $i . "'";
		$qtd[] = isset($row->qtd) ? $row->qtd : 0;
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
				pagamento = 1";
	$res = $coopex_antigo->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);
	$qtd_boleto = $row->qtd;


	?>
	const ctx2 = document.getElementById('pagamentos');
	new Chart(ctx2, {
		type: 'doughnut',
		data: {
			labels: ['Boleto', 'Não Pagos'],
			datasets: [{
				label: ' Inscrições: ',
				data: [<?= $qtd_boleto ?>, <?= $qtd_nao_pago ?>],
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