<?php

require_once("php/sqlsrv.php");
//require_once("./php/utils.php");

//$id_pessoa = $_GET['id_usuario'];

$vestibular = isset($_GET['id']) ? "vestibular_" . $_GET['id'] : "vestibular";
$periodo = isset($_GET['id']) ? $_GET['id'] : "20241";

// Função para obter o número de matrículas com base na faculdade
function obterNumeroMatriculas($faculdadeId, $periodoIngresso)
{
	$where = $faculdadeId ? "AND fac_id_faculdade = $faculdadeId" : '';
	$sql = "SELECT
                fac_id_faculdade,
                fac_nm_faculdade,
                crs_nm_curso,
                pel_ds_compacta,
                fmi_ds_forma_ingresso,
                his_dt_ingresso
            FROM
                academico..HIS_historico_ingresso_saida a
                INNER JOIN academico..FMI_forma_ingresso b ON a.his_id_forma_ingresso = fmi_id_forma_ingresso
                INNER JOIN academico..PEL_periodo_letivo ON his_id_periodo_inicio = pel_id_periodo_letivo
                INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_registro_curso = his_id_registro_curso
                INNER JOIN academico..CRS_curso ON rca_id_curso = crs_id_curso
                INNER JOIN academico..COL_colegiado ON crs_id_unidade = col_id_colegiado
                INNER JOIN academico..FAC_faculdade ON fac_id_faculdade = col_id_faculdade
            WHERE
                pel_ds_compacta = '$periodoIngresso'
				$where";

	$res = mssql_query($sql);
	return mssql_num_rows($res);
}

// Obter o número de matrículas para Cascavel (faculdade 1000000002)
$matriculas_total_cascavel = obterNumeroMatriculas(1000000002, $periodo);
$matriculas_total_dombosco = obterNumeroMatriculas(1000000004, $periodo);

// Obter o número de matrículas para Toledo (faculdade 1100000002)
$matriculas_total_toledo = obterNumeroMatriculas(1100000002, $periodo);
$matriculas_total_toledo2 = obterNumeroMatriculas(1100000003, $periodo);

$matriculas_total_toledo += $matriculas_total_toledo2;

$matriculas_total = $matriculas_total_cascavel + $matriculas_total_toledo + $matriculas_total_dombosco;


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
			<i class='subheader-icon fal fa-chart-area'></i> Matrículas <span class='fw-300'>Resultados</span>
		</h1>
	</div>

	<div class="row">
		<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
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

		<div class="col-sm-6 col-xl-3" title="Total Geral de Inscritos">
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
			<div class="p-3 bg-primary-500 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $matriculas_total_dombosco ?>
						<small class="m-0 l-h-n">Matrículas Dombosco</small>
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


				// Função para obter o total com base nos critérios
				function obterTotal($vestibular, $coopex_antigo, $campus, $codCurso = null, $motivo = null)
				{
					$sql = "SELECT COUNT(*) AS total
								FROM $vestibular.inscricao a
								INNER JOIN $vestibular.resultado b ON a.id_inscricao = b.CodCandidato
								INNER JOIN $vestibular.curso USING (id_curso)
								WHERE id_campus = $campus";

					if ($codCurso !== null) {
						$sql .= " AND CodCurso = $codCurso";
					} else {
						$sql .= " AND CodCurso <> 129";
					}

					if ($motivo !== null) {
						$sql .= " AND motivo = '$motivo'";
					}

					$sql .= " ORDER BY total DESC";

					$res = $coopex_antigo->query($sql);
					$row = $res->fetch(PDO::FETCH_OBJ);
					return $row->total;
				}

				$row_total_geral = obterTotal($vestibular, $coopex_antigo, 1);
				$row_total_medicina = obterTotal($vestibular, $coopex_antigo, 1, 129);
				$row_total_ausente_geral = obterTotal($vestibular, $coopex_antigo, 1, null, 'Ausente');
				$row_total_ausente_medicina = obterTotal($vestibular, $coopex_antigo, 1, 129, 'Ausente');
				$row_total_ausente = obterTotal($vestibular, $coopex_antigo, 1, null, 'Ausente');

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
						<td><?= number_format($row_total_ausente * 100 / $row_total_geral, 2) ?>%</td>
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
										<td><b><?= isset($matriculas[$row->id_curso]) ? $matriculas[$row->id_curso] : 0 ?></b></td>
										<?
										if (!$mobile) {
										?>
											<td><?= $row->total ?></td>
											<td><?= isset($row_pago->total) ? $row_pago->total : 0 ?></td>
											<td><?= isset($row_pago->total) ? (isset($row_ausente->total) ? $row_pago->total - $row_ausente->total : 0) : 0 ?></td>
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
				$row_total_geral = obterTotal($vestibular, $coopex_antigo, 2);
				$row_total_ausente = obterTotal($vestibular, $coopex_antigo, 2, null, 'Ausente');
				
				if (!isset($_GET['id'])) {

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
						<td><?= number_format($row_total_ausente * 100 / $row_total_geral, 2) ?>%</td>
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
										<td><b><?= isset($matriculas[$row->id_curso]) ? $matriculas[$row->id_curso] : 0 ?></b></td>
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

	<div class="row">
		<div class="col-lg-12">
			<div id="panel-4" class="panel">
				<div class="panel-hdr">
					<h2>
						Matrículas - Compatarivo com ano anterior
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="dt-basic-example55" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th style="text-align: left !important;">Dia</th>
									<th>Dia Semana</th>
									<th>Data</th>
									<th>2024/1</th>
									<th>Data</th>
									<th>2023/1</th>
									<th>%</th>
							</thead>
							<tbody>
								<?


								$inicio_20231 = '2022-10-27';
								$inicio_20241 = '2023-10-26';

								// Defina as duas datas que você deseja subtrair
								$data1 = new DateTime(date('y-m-d'));
								$data2 = new DateTime('2023-10-26');

								// Calcule a diferença em dias
								$diferenca = $data1->diff($data2)->days;

								setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

								$sql = "SELECT COUNT
											( * ) AS total 
										FROM
											academico..HIS_historico_ingresso_saida a
											INNER JOIN academico..FMI_forma_ingresso b ON a.his_id_forma_ingresso = fmi_id_forma_ingresso
											INNER JOIN academico..PEL_periodo_letivo ON his_id_periodo_inicio = pel_id_periodo_letivo 
										WHERE
											CONVERT ( DATE, his_dt_criacao ) < '$inicio_20241' 
	
											AND pel_ds_compacta = '20241'";
								$res = mssql_query($sql);
								$row = mssql_fetch_object($res);
								$matriculas_anteriores_20241 = $row->total;

								$sql = "SELECT COUNT
											( * ) AS total 
										FROM
											academico..HIS_historico_ingresso_saida a
											INNER JOIN academico..FMI_forma_ingresso b ON a.his_id_forma_ingresso = fmi_id_forma_ingresso
											INNER JOIN academico..PEL_periodo_letivo ON his_id_periodo_inicio = pel_id_periodo_letivo 
										WHERE
											CONVERT ( DATE, his_dt_criacao ) < '$inicio_20231' 
											AND pel_ds_compacta = '20231'";
								$res = mssql_query($sql);
								$row = mssql_fetch_object($res);
								$matriculas_anteriores_20231 = $row->total;

								$matriculas_20241_total = $matriculas_anteriores_20241;
								$matriculas_20231_total = $matriculas_anteriores_20231;

								?>
								<tr>
									<td><b>0</b></td>
									<td style="text-transform: capitalize;">Anteriores</td>
									<td>-</td>
									<td><b><?= $matriculas_anteriores_20241 ?></b></td>
									<td>-</td>
									<td><b><?= $matriculas_anteriores_20231 ?></b></td>
									<td>
										<i class="fal fa-caret-<?= $matriculas_anteriores_20231 > $matriculas_anteriores_20241 ? "down" : "up" ?> color-<?= $matriculas_20231 > $matriculas_20241 ? "danger" : "success" ?>-500 ml-1"></i>
										<?= round(($matriculas_anteriores_20241 * 100 / $matriculas_anteriores_20231)) - 100 ?>%
									</td>
								</tr>
								<?

								for ($i = 0; $i < $diferenca + 1; $i++) {
									$dataAtual_20241 = new DateTime($inicio_20241);
									$dataAmanha_20241 = $dataAtual_20241->modify("+$i day");
									$data_20241 = $dataAmanha_20241->format('Y-m-d');

									$dataAtual_20231 = new DateTime($inicio_20231);
									$dataAmanha_20231 = $dataAtual_20231->modify("+$i day");
									$data_20231 = $dataAmanha_20231->format('Y-m-d');

									$sql = "SELECT
													*
												FROM
													academico..HIS_historico_ingresso_saida a
													INNER JOIN academico..FMI_forma_ingresso b ON a.his_id_forma_ingresso = fmi_id_forma_ingresso
													inner join academico..PEL_periodo_letivo on his_id_periodo_inicio = pel_id_periodo_letivo
						
												WHERE
													CONVERT ( DATE, his_dt_criacao  ) = '$data_20241'
													and pel_ds_compacta = '20241'";
									$res = mssql_query($sql);
									$matriculas_20241 = mssql_num_rows($res);
									$matriculas_20241_total += $matriculas_20241;

									$sql = "SELECT
													*
												FROM
													academico..HIS_historico_ingresso_saida a
													INNER JOIN academico..FMI_forma_ingresso b ON a.his_id_forma_ingresso = fmi_id_forma_ingresso
													inner join academico..PEL_periodo_letivo on his_id_periodo_inicio = pel_id_periodo_letivo
												WHERE
													CONVERT ( DATE, his_dt_criacao  ) = '$data_20231'
													and pel_ds_compacta = '20231'";
									$res = mssql_query($sql);
									$matriculas_20231 = mssql_num_rows($res);
									$matriculas_20231_total += $matriculas_20231;



									// $diferenca conterá o número de dias de diferença
									//echo "A diferença em dias é: " . $diferenca;
								?>
									<tr>
										<td><b><?= $i + 1 ?></b></td>
										<td style="text-transform: capitalize;"><?= utf8_encode(strftime('%A', $dataAtual_20241->getTimestamp())) ?></td>
										<td><?= converterData($data_20241) ?></td>
										<td><b><?= $matriculas_20241 ?></b></td>
										<td><?= converterData($data_20231) ?></td>
										<td><b><?= $matriculas_20231 ?></b></td>
										<td>
											<i class="fal fa-caret-<?= $matriculas_20231 > $matriculas_20241 ? "down" : "up" ?> color-<?= $matriculas_20231 > $matriculas_20241 ? "danger" : "success" ?>-500 ml-1"></i>
											<?= $matriculas_20231 ? round(($matriculas_20241 * 100 / $matriculas_20231)) - 100 : 0 ?>%
										</td>
									</tr>
								<?
								}
								?>
								<tr style="background-color: #eee;">
									<td colspan="3"><b>TOTAL</b></td>

					
									<td><b><?= $matriculas_20241_total ?></b></td>
									<td></td>
									<td><b><?= $matriculas_20231_total ?></b></td>
									<td>
										<i class="fal fa-caret-<?= $matriculas_20231_total > $matriculas_20241_total ? "down" : "up" ?> color-<?= $matriculas_20231 > $matriculas_20241 ? "danger" : "success" ?>-500 ml-1"></i>
										<?= round(($matriculas_20241_total * 100 / $matriculas_20231_total)) - 100 ?>%
									</td>
								</tr>
							</tbody>

						</table>
						<!-- datatable end -->
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-6">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Forma de Ingresso <span class="fw-300"><i>Cascavel</i></span>
					</h2>
				</div>
				<div class=" show">
					<div class="panel-content bg-subtlelight-fade">
						<canvas class="p-3" id="forma_ingresso_cascavel"></canvas>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Forma de Ingresso <span class="fw-300"><i>Toledo</i></span>
					</h2>
				</div>
				<div class=" show">
					<div class="panel-content bg-subtlelight-fade">
						<canvas class="p-3" id="forma_ingresso_toledo"></canvas>
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
	//TOP 5 CIDADES
	<?
	unset($curso);
	unset($total);
	$sql = "SELECT COUNT
				( * ) as total,
				fmi_ds_forma_ingresso as curso
			FROM
				academico..HIS_historico_ingresso_saida a
				INNER JOIN academico..FMI_forma_ingresso b ON a.his_id_forma_ingresso = fmi_id_forma_ingresso
				INNER JOIN academico..PEL_periodo_letivo ON his_id_periodo_inicio = pel_id_periodo_letivo
				INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_registro_curso = his_id_registro_curso
				INNER JOIN academico..CRS_curso ON rca_id_curso = crs_id_curso
				INNER JOIN academico..COL_colegiado ON crs_id_unidade = col_id_colegiado
				INNER JOIN academico..FAC_faculdade ON fac_id_faculdade = col_id_faculdade 
			WHERE
				pel_ds_compacta = '20241' 
				AND (fac_id_faculdade = 1000000002 OR fac_id_faculdade = 1000000004)
				AND fmi_ds_forma_ingresso NOT LIKE '%retorno%' 
			GROUP BY
				fmi_ds_forma_ingresso";
	$res = mssql_query($sql);
	while ($row = mssql_fetch_object($res)) {
		$curso[] = "'" . utf8_encode($row->curso) . "'";
		$total[] = $row->total;
	}

	?>
	const ctx23 = document.getElementById('forma_ingresso_cascavel');
	new Chart(ctx23, {
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

	//TOP 5 CIDADES
	<?
	unset($curso);
	unset($total);
	$sql = "SELECT COUNT
				( * ) as total,
				fmi_ds_forma_ingresso as curso
			FROM
				academico..HIS_historico_ingresso_saida a
				INNER JOIN academico..FMI_forma_ingresso b ON a.his_id_forma_ingresso = fmi_id_forma_ingresso
				INNER JOIN academico..PEL_periodo_letivo ON his_id_periodo_inicio = pel_id_periodo_letivo
				INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_registro_curso = his_id_registro_curso
				INNER JOIN academico..CRS_curso ON rca_id_curso = crs_id_curso
				INNER JOIN academico..COL_colegiado ON crs_id_unidade = col_id_colegiado
				INNER JOIN academico..FAC_faculdade ON fac_id_faculdade = col_id_faculdade 
			WHERE
				pel_ds_compacta = '20241' 
				AND (fac_id_faculdade = 1100000002 OR fac_id_faculdade = 1100000003)
				AND fmi_ds_forma_ingresso NOT LIKE '%vestibular%' 
			GROUP BY
				fmi_ds_forma_ingresso";
	$res = mssql_query($sql);
	while ($row = mssql_fetch_object($res)) {
		$curso[] = "'" . utf8_encode($row->curso) . "'";
		$total[] = $row->total;
	}

	

	$sql = "SELECT COUNT
				( * ) as total,
				fmi_ds_forma_ingresso as curso
			FROM
				academico..HIS_historico_ingresso_saida a
				INNER JOIN academico..FMI_forma_ingresso b ON a.his_id_forma_ingresso = fmi_id_forma_ingresso
				INNER JOIN academico..PEL_periodo_letivo ON his_id_periodo_inicio = pel_id_periodo_letivo
				INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_registro_curso = his_id_registro_curso
				INNER JOIN academico..CRS_curso ON rca_id_curso = crs_id_curso
				INNER JOIN academico..COL_colegiado ON crs_id_unidade = col_id_colegiado
				INNER JOIN academico..FAC_faculdade ON fac_id_faculdade = col_id_faculdade 
			WHERE
				pel_ds_compacta = '20241' 
				AND (fac_id_faculdade = 1100000002 OR fac_id_faculdade = 1100000003)
				AND fmi_ds_forma_ingresso LIKE '%vestibular%' 
			GROUP BY
				fmi_ds_forma_ingresso";
	$res = mssql_query($sql);
	$soma = 0;
	while ($row = mssql_fetch_object($res)) {
		$soma += $row->total;
	}

	$curso[] = "'VESTIBULAR'";
	$total[] = $soma;

	?>
	const ctx22 = document.getElementById('forma_ingresso_toledo');
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