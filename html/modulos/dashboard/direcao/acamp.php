<?php

//require_once("./php/mysql.php");
//require_once("./php/utils.php");
require_once("php/sqlsrv.php");
//$id_pessoa = $_GET['id_usuario'];

if (isset($_GET['id'])) {
	$vestibular = "vestibular_" . $_GET['id'];
} else {
	$vestibular = "vestibular";
}

//INSCRITOS TOTAL
$sql = "SELECT
		id_inscricao 
			FROM
		coopex_usuario.evento_inscricao 
			WHERE
		id_evento = 5210";
$res = $coopex_antigo->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$inscritos_total = $res->rowCount();


//INSCRITOS TOTAL
$sql = "SELECT
			id_inscricao 
				FROM
			coopex_usuario.evento_inscricao 
				WHERE
			id_evento = 5210
			AND
			pago = 1";
$res = $coopex_antigo->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$cadastros_completos = $res->rowCount();


$sql = "SELECT
		id_inscricao 
			FROM
		coopex_usuario.evento_inscricao 
			WHERE
		id_evento = 5210
		AND id_valor = 7954";
$res = $coopex_antigo->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$fundamental_1 = $res->rowCount();

$sql = "SELECT
		id_inscricao 
			FROM
		coopex_usuario.evento_inscricao 
			WHERE
		id_evento = 5210
		AND id_valor = 7953";
$res = $coopex_antigo->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$infantil = $res->rowCount();

$sql = "SELECT
		id_inscricao 
			FROM
		coopex_usuario.evento_inscricao 
			WHERE
		id_evento = 5210
		AND id_valor = 7955";
$res = $coopex_antigo->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$fundamental_2 = $res->rowCount();


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
		<li class="breadcrumb-item active">ACAMP FAG Dashboard</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>

	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-chart-area'></i> ACAMP FAG <span class='fw-300'>Dashboard</span>
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
						<small class="m-0 l-h-n">Inscritos Total</small>
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
						<small class="m-0 l-h-n">Pagos</small>
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
						<small class="m-0 l-h-n">Não Pagos</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>
	</div>



	<div class="row">
		<div class="col-sm-6 col-xl-4" title="Total Geral de Inscritos">
			<div class="p-3 bg-primary-400 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $infantil ?>
						<small class="m-0 l-h-n">Educação Infantil</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>
		<div class="col-sm-6 col-xl-4" title="Total Geral de Inscritos">
			<div class="p-3 bg-primary-500 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $fundamental_1 ?>
						<small class="m-0 l-h-n">Fundamental I (1° ao 5º ano)</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>
		<div class="col-sm-6 col-xl-4" title="Total Geral de Inscritos">
			<div class="p-3 bg-primary-600 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $fundamental_2 ?>
						<small class="m-0 l-h-n">Fundamental II (6° ao 9° ano)</small>
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
						Gênero
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
						Forma de Pagamento
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
						Educação Infantil
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th width="10" style="text-align: center !important;">#</th>
									<th style="text-align: left !important;">Nome</th>
									<th width="10">Camiseta</th>
									<th width="10">Pago</th>
							</thead>
							<tbody>
								<?
								$i = 0;

								$sql = "SELECT
										* 
											FROM
										coopex_usuario.evento_inscricao 
										INNER JOIN coopex_usuario.evento_pessoa USING (id_pessoa)
											WHERE
										id_evento = 5210
										AND id_valor = 7953
										order by nome";
								$res = $coopex_antigo->query($sql);

								while ($row = $res->fetch(PDO::FETCH_OBJ)) {
									$i++;


								?>
									<tr>
										<td><?= $i ?></td>
										<td style="text-align: left !important;"><b><?= utf8_encode($row->nome) ?></b></td>
										<td style="text-align: left !important; min-width: 220px;">
											<div class="input-group">
												<input id="camiseta_nome_<?= $row->id_inscricao ?>" type="text" class="form-control" value="<?= utf8_encode($row->observacao) ?>">
												<div class="input-group-append">
													<button onclick="camiseta_nome(<?= $row->id_inscricao ?>)" class="btn btn-primary waves-effect waves-themed" type="button" id="button-addon2"><i class="fal fa-save"></i></button>
												</div>
											</div>
										</td>
										<td style="min-width: 100px;">
											<select id="camiseta_<?= $row->id_inscricao ?>" onchange="alterar_tamanho(<?= $row->id_inscricao ?>)" name="inscricao_tamanho_camiseta" class="form-control select2">
												<option <?= $row->camiseta_tamanho == "2" ? "selected" : "" ?> value="2">2</option>
												<option <?= $row->camiseta_tamanho == "4" ? "selected" : "" ?> value="4">4</option>
												<option <?= $row->camiseta_tamanho == "6" ? "selected" : "" ?> value="6">6</option>
												<option <?= $row->camiseta_tamanho == "8" ? "selected" : "" ?> value="8">8</option>
												<option <?= $row->camiseta_tamanho == "10" ? "selected" : "" ?> value="10">10</option>
												<option <?= $row->camiseta_tamanho == "12" ? "selected" : "" ?> value="12">12</option>
												<option <?= $row->camiseta_tamanho == "14" ? "selected" : "" ?> value="14">14</option>

												<option disabled="" value="">---------------</option>
												<option <?= $row->camiseta_tamanho == "PP" ? "selected" : "" ?> value="PP">PP</option>
												<option <?= $row->camiseta_tamanho == "P" ? "selected" : "" ?> value="P">P</option>
												<option <?= $row->camiseta_tamanho == "M" ? "selected" : "" ?> value="M">M</option>
												<option <?= $row->camiseta_tamanho == "G" ? "selected" : "" ?> value="G">G</option>
												<option <?= $row->camiseta_tamanho == "GG" ? "selected" : "" ?> value="GG">GG</option>
												<option <?= $row->camiseta_tamanho == "EG" ? "selected" : "" ?> value="EG">EG</option>
												<option <?= $row->camiseta_tamanho == "EXG" ? "selected" : "" ?> value="EXG">EXG</option>
												<option <?= $row->camiseta_tamanho == "EXGG" ? "selected" : "" ?> value="EXGG">EXGG</option>
											</select>
										</td>
										<td><?= $row->pago ? "Sim" : "-" ?></td>



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
						Fundamental I (1° ao 5º ano)
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th width="10" style="text-align: center !important;">#</th>
									<th style="text-align: left !important;">Nome</th>
									<th width="10">Camiseta</th>
									<th width="10">Pago</th>
							</thead>
							<tbody>
								<?
								$i = 0;

								$sql = "SELECT
										* 
											FROM
										coopex_usuario.evento_inscricao 
										INNER JOIN coopex_usuario.evento_pessoa USING (id_pessoa)
											WHERE
										id_evento = 5210
										AND id_valor = 7954
										order by nome";
								$res = $coopex_antigo->query($sql);

								while ($row = $res->fetch(PDO::FETCH_OBJ)) {
									$i++;


								?>
									<tr>
										<td><?= $i ?></td>
										<td style="text-align: left !important;"><b><?= utf8_encode($row->nome) ?></b></td>
										<td style="text-align: left !important; min-width: 220px;">
											<div class="input-group">
												<input id="camiseta_nome_<?= $row->id_inscricao ?>" type="text" class="form-control" value="<?= utf8_encode($row->observacao) ?>">
												<div class="input-group-append">
													<button onclick="camiseta_nome(<?= $row->id_inscricao ?>)" class="btn btn-primary waves-effect waves-themed" type="button" id="button-addon2"><i class="fal fa-save"></i></button>
												</div>
											</div>
										</td>
										<td style="min-width: 100px;">
											<select id="camiseta_<?= $row->id_inscricao ?>" onchange="alterar_tamanho(<?= $row->id_inscricao ?>)" name="inscricao_tamanho_camiseta" class="form-control select2">
												<option <?= $row->camiseta_tamanho == "2" ? "selected" : "" ?> value="2">2</option>
												<option <?= $row->camiseta_tamanho == "4" ? "selected" : "" ?> value="4">4</option>
												<option <?= $row->camiseta_tamanho == "6" ? "selected" : "" ?> value="6">6</option>
												<option <?= $row->camiseta_tamanho == "8" ? "selected" : "" ?> value="8">8</option>
												<option <?= $row->camiseta_tamanho == "10" ? "selected" : "" ?> value="10">10</option>
												<option <?= $row->camiseta_tamanho == "12" ? "selected" : "" ?> value="12">12</option>
												<option <?= $row->camiseta_tamanho == "14" ? "selected" : "" ?> value="14">14</option>

												<option disabled="" value="">---------------</option>
												<option <?= $row->camiseta_tamanho == "PP" ? "selected" : "" ?> value="PP">PP</option>
												<option <?= $row->camiseta_tamanho == "P" ? "selected" : "" ?> value="P">P</option>
												<option <?= $row->camiseta_tamanho == "M" ? "selected" : "" ?> value="M">M</option>
												<option <?= $row->camiseta_tamanho == "G" ? "selected" : "" ?> value="G">G</option>
												<option <?= $row->camiseta_tamanho == "GG" ? "selected" : "" ?> value="GG">GG</option>
												<option <?= $row->camiseta_tamanho == "EG" ? "selected" : "" ?> value="EG">EG</option>
												<option <?= $row->camiseta_tamanho == "EXG" ? "selected" : "" ?> value="EXG">EXG</option>
												<option <?= $row->camiseta_tamanho == "EXGG" ? "selected" : "" ?> value="EXGG">EXGG</option>
											</select>
										</td>
										<td><?= $row->pago ? "Sim" : "-" ?></td>



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
						Fundamental II (6° ao 9° ano)
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th width="10" style="text-align: center !important;">#</th>
									<th style="text-align: left !important;">Nome</th>
									<th width="10">Camiseta</th>
									<th width="10">Pago</th>
							</thead>
							<tbody>
								<?
								$i = 0;

								$sql = "SELECT
										* 
											FROM
										coopex_usuario.evento_inscricao 
										INNER JOIN coopex_usuario.evento_pessoa USING (id_pessoa)
											WHERE
										id_evento = 5210
										AND id_valor = 7955
										order by nome";
								$res = $coopex_antigo->query($sql);

								while ($row = $res->fetch(PDO::FETCH_OBJ)) {
									$i++;


								?>
									<tr>
										<td><?= $i ?></td>
										<td style="text-align: left !important;"><b><?= utf8_encode($row->nome) ?></b></td>
										<td style="text-align: left !important; min-width: 220px;">
											<div class="input-group">
												<input id="camiseta_nome_<?= $row->id_inscricao ?>" type="text" class="form-control" value="<?= utf8_encode($row->observacao) ?>">
												<div class="input-group-append">
													<button onclick="camiseta_nome(<?= $row->id_inscricao ?>)" class="btn btn-primary waves-effect waves-themed" type="button" id="button-addon2"><i class="fal fa-save"></i></button>
												</div>
											</div>
										</td>
										<td style="min-width: 100px;">
											<select id="camiseta_<?= $row->id_inscricao ?>" onchange="alterar_tamanho(<?= $row->id_inscricao ?>)" name="inscricao_tamanho_camiseta" class="form-control select2">
												<option <?= $row->camiseta_tamanho == "2" ? "selected" : "" ?> value="2">2</option>
												<option <?= $row->camiseta_tamanho == "4" ? "selected" : "" ?> value="4">4</option>
												<option <?= $row->camiseta_tamanho == "6" ? "selected" : "" ?> value="6">6</option>
												<option <?= $row->camiseta_tamanho == "8" ? "selected" : "" ?> value="8">8</option>
												<option <?= $row->camiseta_tamanho == "10" ? "selected" : "" ?> value="10">10</option>
												<option <?= $row->camiseta_tamanho == "12" ? "selected" : "" ?> value="12">12</option>
												<option <?= $row->camiseta_tamanho == "14" ? "selected" : "" ?> value="14">14</option>

												<option disabled="" value="">---------------</option>
												<option <?= $row->camiseta_tamanho == "PP" ? "selected" : "" ?> value="PP">PP</option>
												<option <?= $row->camiseta_tamanho == "P" ? "selected" : "" ?> value="P">P</option>
												<option <?= $row->camiseta_tamanho == "M" ? "selected" : "" ?> value="M">M</option>
												<option <?= $row->camiseta_tamanho == "G" ? "selected" : "" ?> value="G">G</option>
												<option <?= $row->camiseta_tamanho == "GG" ? "selected" : "" ?> value="GG">GG</option>
												<option <?= $row->camiseta_tamanho == "EG" ? "selected" : "" ?> value="EG">EG</option>
												<option <?= $row->camiseta_tamanho == "EXG" ? "selected" : "" ?> value="EXG">EXG</option>
												<option <?= $row->camiseta_tamanho == "EXGG" ? "selected" : "" ?> value="EXGG">EXGG</option>
											</select>
										</td>
										<td><?= $row->pago ? "Sim" : "-" ?></td>



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
						Por Turma
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th style="text-align: center !important;">Série</th>
									<th style="text-align: left !important;">Alunos</th>
							</thead>
							<tbody>
								<?
								$i = 0;

								$sql = "SELECT
											p.id_usuario 
										FROM
											coopex_usuario.evento_inscricao
											INNER JOIN coopex_usuario.evento_pessoa p USING ( id_pessoa ) 
										WHERE
											id_evento = 5210 
										ORDER BY
											id_usuario,
											nome";
								$res = $coopex_antigo->query($sql);
								$id_usuario = array();
								while($row = $res->fetch(PDO::FETCH_OBJ)) {
									$id_usuario[] = $row->id_usuario;
								}


								$usuario = implode($id_usuario, ",");

								$sql = "SELECT
											ser_ds_serie,
											COUNT(DISTINCT i.PES_ID_PESSOA) AS quantidade_alunos
										FROM
											academico..HIS_historico_ingresso_saida a
											INNER JOIN academico..FMI_forma_ingresso b ON a.his_id_forma_ingresso = b.fmi_id_forma_ingresso
											INNER JOIN academico..RCA_registro_curso_aluno c ON c.rca_id_registro_curso = a.his_id_registro_curso
											INNER JOIN academico..CRS_curso d ON d.crs_id_curso = c.rca_id_curso
											INNER JOIN academico..COL_colegiado e ON e.col_id_colegiado = d.crs_id_unidade
											INNER JOIN academico..FAC_faculdade f ON f.fac_id_faculdade = e.col_id_faculdade
											INNER JOIN academico..SAP_situacao_aluno_periodo_letivo_view SAP0 ON c.rca_id_registro_curso = SAP0.sap_id_registro_curso
											INNER JOIN academico..PEL_periodo_letivo PEL0 ON PEL0.pel_id_periodo_letivo = SAP0.sap_id_periodo_letivo
											INNER JOIN academico..IAP_informacoes_aluno_periodo_view g ON g.iap_id_registro_curso = c.rca_id_registro_curso
											INNER JOIN academico..SER_serie h ON h.ser_id_serie = g.iap_id_serie
											INNER JOIN registro..PES_pessoa i ON i.pes_id_pessoa = c.rca_id_aluno
											INNER JOIN academico..ALU_aluno j ON j.alu_id_pessoa = i.pes_id_pessoa
											INNER JOIN academico..TCU_turmas_curso k ON k.tcu_id_turma_curso = c.rca_id_turma_curso 
										WHERE
											PEL0.pel_ds_compacta = '20240' 
											AND f.fac_id_faculdade = 1000000006 
											AND g.iap_id_periodo_letivo = SAP0.sap_id_periodo_letivo 
											AND i.PES_ID_PESSOA IN (
												$usuario
											) 
										GROUP BY
											ser_ds_serie 
										ORDER BY
											ser_ds_serie;
										";
				
					$res = mssql_query($sql);
					while($row = mssql_fetch_object($res)){

								?>
									<tr>
										<td style="text-align: left !important;"><b><?= utf8_encode($row->ser_ds_serie) ?></b></td>

										<td style="text-align: left !important;"><b><?= ($row->quantidade_alunos) ?></b></td>


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
	function alterar_tamanho(id_inscricao) {
		var tamanho = $("#camiseta_" + id_inscricao).val();
		$.getJSON("modulos/dashboard/direcao/ajax/camiseta_acamp.php", {
			id_inscricao: id_inscricao,
			tamanho: tamanho
		})
	}

	function camiseta_nome(id_inscricao) {
		var nome = $("#camiseta_nome_" + id_inscricao).val();
		$.getJSON("modulos/dashboard/direcao/ajax/camiseta_nome.php", {
			id_inscricao: id_inscricao,
			nome: nome
		})
	}

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
				( data_inscricao ) AS dia,
				count( id_inscricao ) AS qtd 
			FROM
				coopex_usuario.evento_inscricao 
			WHERE
				id_evento = 5210 
			GROUP BY
				DATE ( data_inscricao )";
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
					COUNT( id_inscricao ) AS qtd,
					HOUR( data_inscricao ) AS hora
				FROM
				coopex_usuario.evento_inscricao 
				WHERE
				id_evento = 5210 
				and
					HOUR( data_inscricao ) = $i	
				GROUP BY
					HOUR(data_inscricao) 
				ORDER BY
					HOUR(data_inscricao)";
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
				id_pessoa 
			FROM
				coopex_usuario.evento_inscricao
				INNER JOIN evento_pessoa USING ( id_pessoa ) 
			WHERE
				id_evento = 5210 
				AND sexo = 'M'";
	$res = $coopex_antigo->query($sql);
	$android = $res->rowCount();

	$sql = "SELECT
				id_pessoa 
			FROM
				coopex_usuario.evento_inscricao
				INNER JOIN evento_pessoa USING ( id_pessoa ) 
			WHERE
				id_evento = 5210 
				AND sexo = 'F'";
	$res = $coopex_antigo->query($sql);
	$iphone = $res->rowCount();


	?>
	const ctx2 = document.getElementById('pagamentos');
	new Chart(ctx2, {
		type: 'doughnut',
		data: {
			labels: ['Masculino', 'Feminino'],
			datasets: [{
				label: ' Cadastros: ',
				data: [<?= $android ?>, <?= $iphone ?>],
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
				id_inscricao 
			FROM
				coopex_usuario.evento_inscricao
				INNER JOIN coopex_usuario.evento_inscricao_cartao USING ( id_inscricao ) 
			WHERE
				id_evento = 5210";
	$res = $coopex_antigo->query($sql);
	$cartao = $res->rowCount();

	?>
	const ctx2222 = document.getElementById('top5_estados');
	new Chart(ctx2222, {
		type: 'doughnut',
		data: {
			labels: ['Cartão', 'Boleto'],
			datasets: [{
				label: ' Pagamentos: ',
				data: [<?= $cartao ?>, <?= $cadastros_completos - $cartao ?>],
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