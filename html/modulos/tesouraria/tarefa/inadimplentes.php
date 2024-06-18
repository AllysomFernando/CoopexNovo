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
			id_matricula_boleto,
			id_matricula,
			p.id_pessoa,
			nome,
			DATE ( data_matricula ) AS data_matricula,
			b.valor,
			b.data_vencimento,
			parcela,
			DATEDIFF( now(), b.data_vencimento ) AS dias_atraso,
		IF
			( MONTH ( b.data_vencimento ) = MONTH ( now()), 'Atual', 'Anterior' ) as situacao 
		FROM
			colegio.cdt_matricula m
			INNER JOIN colegio.cdt_matricula_boleto b USING ( id_matricula )
			INNER JOIN coopex.pessoa p ON m.id_pessoa = p.id_pessoa 
		WHERE
			b.data_vencimento < now() 
			AND pago = 0 
			AND situacao = 1 
			AND ativo = 1
		ORDER BY
			nome,
			parcela";
$res = $coopex->query($sql);
$boletos_total = $res->rowCount();


//INSCRITOS TOTAL
$sql = "SELECT
			p.id_pessoa,
			nome,
			DATE ( data_matricula ),
			b.valor,
			b.data_vencimento,
			parcela,
		IF
			( MONTH ( b.data_vencimento ) = MONTH ( now()), 'Atual', 'Anterior' ) 
		FROM
			colegio.cdt_matricula m
			INNER JOIN colegio.cdt_matricula_boleto b USING ( id_matricula )
			INNER JOIN coopex.pessoa p ON m.id_pessoa = p.id_pessoa 
		WHERE
			b.data_vencimento < now() 
			AND pago = 0 
			AND situacao = 1 
			AND MONTH ( b.data_vencimento ) = MONTH (
			now()) 
		ORDER BY
			nome,
			parcela";
$res2 = $coopex->query($sql);
$mes_atual = $res2->rowCount();



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
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">COOPEX</a></li>
		<li class="breadcrumb-item">BI</li>
		<li class="breadcrumb-item active">Clube da Tarefa</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>

	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-chart-area'></i> Clube da Tarefa <span class='fw-300'>| Inadimplentes</span>
		</h1>

	</div>

	<div class="row">
		<div class="col-sm-6 col-xl-4" title="Total Geral de Inscritos">
			<div class="p-3 bg-primary-300 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $boletos_total ?>
						<small class="m-0 l-h-n">Boletos vencidos</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>
		<div class="col-sm-6 col-xl-4" title="Total Geral de Inscritos">
			<div class="p-3 bg-success-600 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $mes_atual ?>
						<small class="m-0 l-h-n">Mês atual</small>
					</h3>
				</div>
				<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
			</div>
		</div>
		<div class="col-sm-6 col-xl-4" title="Total Geral de Inscritos">
			<div class="p-3 bg-danger-600 rounded overflow-hidden position-relative text-white mb-g">
				<div class="">
					<h3 class="display-4 d-block l-h-n m-0 fw-500">
						<?= $boletos_total - $mes_atual ?>
						<small class="m-0 l-h-n">Meses anteriores</small>
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
						Inadimplentes
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content bg-subtlelight-fade">
						<table id="tabelaBoleto" class="table table-bordered table-hover  w-100">
							<thead>

								<th width="10" style="text-align: center !important;">#</th>
								<th style="text-align: left !important;">Nome</th>
								<th width="10">Matrícula</th>
								<th width="10">Valor</th>
								<th width="10">Parcela</th>
								<th width="10">Vencimento</th>
								<th width="10">Dias de Atraso</th>
								<th width="10">Mês</th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</thead>
							<tbody>
								<?
								$i = 0;


								while ($row = $res->fetch(PDO::FETCH_OBJ)) {
									$i++;

									$sql2 = "SELECT
												tel_cd_ddd AS ddd,
												tel_nu_telefone AS telefone,
												pes_nm_pessoa AS nome  
											FROM
												academico..AUE_aluno_unidade_ensino a
												INNER JOIN academico..TEL_telefone b ON a.aue_id_responsavel = tel_id_pessoa
												INNER JOIN academico..PES_pessoa c ON a.aue_id_aluno = c.pes_id_pessoa 
											WHERE
													aue_id_aluno = $row->id_pessoa";
									$res2 = mssql_query($sql2);
									$row2 = mssql_fetch_assoc($res2);
									$whats = trim($row2['ddd']) . trim($row2['telefone']);
									$whats = str_replace('-', '', $whats);
								?>
									<tr id="linha_boleto_<?= $row->id_matricula_boleto ?>" class="<?= $row->situacao == "Anterior" ? "bg-danger-50" : "" ?>">
										<td><?= $row->id_matricula_boleto ?></td>
										<td style="text-align: left !important;"><b><?= utf8_encode($row->nome) ?></b></td>
										<td style="text-align: center !important;">
											<?= converterData($row->data_matricula) ?>
										</td>
										<td style="min-width: 100px;">
											<?= $row->valor ?>
										</td>
										<td><?= $row->parcela ?></td>
										<td style="text-align: center !important;">
											<?= converterData($row->data_vencimento) ?>
										</td>
										<td><?= $row->dias_atraso ?></td>
										<td style="text-align: center !important;">
											<?= $row->situacao ?>
										</td>
										<td>
											<a href="https://coopex.fag.edu.br/boleto/clube_da_tarefa/matricula/<?= $row->id_matricula_boleto ?>" target="_blank" title="Gerar Boleto" onclick="carregar_boletos(<?= $row->id_pessoa ?>)" class="btn btn-primary btn-icon rounded-circle waves-effect waves-themed">
												<i class="fal fa-barcode-alt"></i>
											</a>
										</td>
										<td>
											<button onclick="atualizar_vencimento(<?= $row->id_matricula ?>,<?= $row->parcela ?>)" target="_blank" title="Atualizar Vencimento" class="btn btn-success  btn-icon rounded-circle waves-effect waves-themed">
												<i class="fal fa-calendar-alt"></i>
											</button>
										</td>

										<td>
											<button onclick="baixar_pagamento(<?= $row->id_matricula_boleto ?>)" title="Baixar Pagamento" class="btn btn-secondary  btn-icon rounded-circle waves-effect waves-themed">
												<i class="fal fa-arrow-down"></i>
											</button>
										</td>
										<td>
											<button onclick="cancelar_boleto(<?= $row->id_matricula_boleto ?>)" title="Cancelar Boleto" class="btn btn-warning  btn-icon rounded-circle waves-effect waves-themed">
												<i class="fal fa-trash"></i>
											</button>
										</td>
										<td>
											<button onclick="notificar(<?= $row->id_pessoa ?>,<?= $whats ?>)" title="Notificar Atraso" class="btn btn-danger  btn-icon rounded-circle waves-effect waves-themed">
												<i class="fal fa-exclamation"></i>
											</button>
										</td>


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

	<div class="modal fade" id="modal_pagamento" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content ">
				<div class="panel-hdr">
					<h2>
						Matrícula - Pagamento
					</h2>
					<div class="panel-toolbar">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true"><i class="fal fa-times"></i></span>
						</button>
					</div>
				</div>

				<div class="modal-body">

					<div class="form-row">
						<input type="hidden" name="id_matricula" id="baixa_matricula_vencimento">
						<table class="table table-bordered table-hover">

							<tr>
								<td>Data de pagamento</td>
								<td id="data_pagamento" class="form-label">Data de Pagamento</td>
							</tr>
							<tr>
								<td>Valor pago</td>
								<td id="valor_pago" class="form-label">Valor pago</td>
							</tr>
							<tr>
								<td>Divisão</td>
								<td id="divisao" class="form-label">Divisão</td>
							</tr>
							<tr>
								<td>Tipo de Pagamento</td>
								<td id="tipo_pagamento" class="form-label">Tipo de Pagamento</td>
							</tr>
							<tr style="display: none;">
								<td>Data de vencimento</td>
								<td id="data_vencimento" class="form-label">Data de vencimento</td>
							</tr>

						</table>

					</div>


				</div>
				<div class="modal-footer pt-3 pb-3 border-faded border-left-0 border-right-0 border-bottom-0">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="default-example-modal-lg-center" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Notificar Acadêmico</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true"><i class="fal fa-times"></i></span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label class="form-label">Notificar por Whatsapp</label>
						<div class="input-group input-group-lg bg-white shadow-inset-2">
							<div class="input-group-prepend">
								<span class="input-group-text bg-transparent border-right-0 py-1 px-3 text-success">
									<i class="fab fa-whatsapp" style="font-size: 24px"></i>
								</span>
							</div>
							<input type="text" onkeyup="alterar_link_whatsapp()" class="form-control border-left-0 bg-transparent pl-0" id="whatsapp" value="<?= $whats ?>">
							<div class="input-group-append">
								<?
								//echo $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'];
								if ($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 3) {

								?>
									<a onclick="notificar_aluno_tesouraria()" class="btn btn-default waves-effect waves-themed" type="button">Enviar</a>
								<?
								} else {
								?>
									<a id="link_whatsapp" href="" target="_blank" onclick="notificar_aluno_coordenacao()" class="btn btn-default waves-effect waves-themed" type="button">Enviar</a>
								<?
								}
								?>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="form-label">Notificar por e-mail</label>
						<div class="input-group input-group-lg bg-white shadow-inset-2">
							<div class="input-group-prepend">
								<span class="input-group-text bg-transparent border-right-0 py-1 px-3 text-success">
									<i class="fal fa-at" style="font-size: 24px"></i>
								</span>
							</div>
							<input type="text" class="form-control border-left-0 bg-transparent pl-0" id="email" value="<?= $email ?>">
							<div class="input-group-append">
								<button onclick="<?= $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 3 ? "notificar_aluno_tesouraria_email()" : "notificar_aluno_coordenacao_email()" ?>" class="btn btn-default waves-effect waves-themed" type="button">Enviar</button>
							</div>
						</div>
					</div>

					<input type="hidden" id="nome_academico">


				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
				</div>
			</div>
		</div>
	</div>
</main>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script>
	function carregar_boletos($id_pessoa) {

	}

	function atualizar_vencimento(id_matricula, parcela) {
		$.getJSON("https://coopex.fag.edu.br/php/registro_bradesco/clube_da_tarefa_matricula_individual.php", {
			id: id_matricula,
			parcela: parcela
		})
	}

	function alterar_tamanho(id_inscricao) {
		var tamanho = $("#camiseta_" + id_inscricao).val();
		$.getJSON("modulos/dashboard/direcao/ajax/camiseta_acamp.php", {
			id_inscricao: id_inscricao,
			tamanho: tamanho
		})
	}

	function baixar_pagamento(id_boleto) {
		$.getJSON("modulos/tesouraria/tarefa/ajax/baixar_pagamento.php", {
				id_boleto: id_boleto
			})
			.done(function(data) {
				$("#linha_boleto_" + data).remove();
				Swal.fire({
					type: "success",
					title: "Boleto baixado com sucesso!",
					showConfirmButton: false,
					timer: 1500
				});
			});
	}

	function cancelar_boleto(id_boleto) {
		$.getJSON("modulos/tesouraria/tarefa/ajax/cancelar_boleto.php", {
				id_boleto: id_boleto
			})
			.done(function(data) {
				$("#linha_boleto_" + data).remove();
				Swal.fire({
					type: "success",
					title: "Boleto cancelado com sucesso!",
					showConfirmButton: false,
					timer: 1500
				});
			});
	}

	function janela_notificacao(id_pessoa, telefone) {

	}



	function notificar(id_pessoa, telefone) {
		$.getJSON("modulos/tesouraria/tarefa/ajax/notificar_aluno_tesouraria.php", {
				id_pessoa: id_pessoa,
				telefone: telefone
			})
			.done(function(data) {
				//$("#linha_boleto_" + data).remove();
				Swal.fire({
					type: "success",
					title: "Notificação enviada com sucesso!",
					showConfirmButton: false,
					timer: 1500
				});
			});
	}



	$(document).ready(function() {

		$('#tabelaBoleto').dataTable({
			responsive: true,
			pageLength: 15,
			stateSave: true
		});

	});
</script>

<style>
	table td,
	th {
		vertical-align: middle !important;
		text-align: center !important;
	}
</style>

<!-- <script src="js/app.bundle.js"></script> -->
<!-- The order of scripts is irrelevant. Please check out the plugin pages for more details about these plugins below: -->
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>