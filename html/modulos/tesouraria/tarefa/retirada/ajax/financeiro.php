<?php session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../../../../../php/mysql.php");
require_once("../../../../../php/utils.php");

require_once("../../../../../php/sqlsrv.php");

$id_pessoa = $_GET['id_usuario'];

$sql = "SELECT
				pes_id_pessoa,
				rtrim(alu_nu_matricula) AS ra,
				rtrim(pes_nm_pessoa) AS nome,
				rtrim(crs_nm_resumido) AS curso,
				ser_ds_serie AS serie,
				sap_ds_situacao AS situacao,
				rca_id_registro_curso,
				ser_id_serie
			FROM
				registro..PES_pessoa
			INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
			INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_aluno = pes_id_pessoa
			INNER JOIN academico..CRS_curso ON crs_id_curso = rca_id_curso
			INNER JOIN academico..SAP_situacao_aluno_periodo_letivo_view ON rca_id_registro_curso = sap_id_registro_curso
			INNER JOIN academico..PEL_periodo_letivo ON sap_id_periodo_letivo = pel_id_periodo_letivo
			INNER JOIN academico..IAP_informacoes_aluno_periodo_view ON iap_id_registro_curso = rca_id_registro_curso
			INNER JOIN academico..SER_serie ON iap_id_serie = ser_id_serie
			WHERE
				iap_id_periodo_letivo = 5000000244 --and sap_ds_situacao = 'Sem Status'
			AND pes_id_pessoa = $id_pessoa
			AND EXISTS (
				SELECT
					1
				FROM
					financeiro..cta_contrato_academico,
					financeiro..ctr_contrato,
					financeiro..CPL_contrato_periodo_letivo,
					financeiro..prc_parcela,
					financeiro..ttf_titulo_financeiro
				WHERE
					cta_id_contrato = ctr_id_contrato
				AND ctr_id_cliente = rca_id_aluno
				AND cpl_id_periodo_letivo = pel_id_periodo_letivo
				AND cpl_id_contrato = cta_id_contrato
				AND prc_id_contrato = cta_id_contrato
				AND ttf_id_parcela = prc_id_parcela
				AND ttf_st_situacao IN ('P', 'L', 'G', 'R', 'S')
			) --Em Compensação, liberado, Pago, Renegociado e Sem valo */
			AND EXISTS (
				SELECT
					1
				FROM
					academico..MTR_matricula
				WHERE
					mtr_id_periodo_letivo = pel_id_periodo_letivo
				AND mtr_id_registro_curso = rca_id_registro_curso
				AND mtr_id_situacao_matricula = 1000000002
				-- AND mtr_id_periodo_letivo = 5000000244
			)
			ORDER BY
				crs_nm_resumido,
				ser_ds_serie,
				pes_nm_pessoa";
$res = mssql_query($sql);
$row = mssql_fetch_assoc($res);

$id_serie = $row['ser_id_serie'];

//selecionas os boletos das matrículas
$sql = "SELECT
				*
			FROM
				tesouraria.material
			WHERE
				id_serie = $id_serie";
$material = $coopex->query($sql);

$situacao['A'] = "Aberto";
$situacao['C'] = "Cancelado";
$situacao['E'] = "Pendente";
$situacao['G'] = "Em compensação";
$situacao['L'] = "Liberado";
$situacao['P'] = "Pago";
$situacao['R'] = "Renegociado";
$situacao['S'] = "Sem valor";

$sql_observacao = "SELECT
				*
			FROM
				tesouraria.observacao
			WHERE
				id_pessoa = $id_pessoa";
$observacao = $coopex->query($sql_observacao);
$row_observacao = $observacao->fetch(PDO::FETCH_OBJ);

?>
<style>
	table tr td {
		vertical-align: middle !important;
	}
</style>

<table class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
	<thead class="bg-secondary-600">
		<tr>
			<th>RA</th>
			<th>Nome</th>
			<th>Curso</th>
			<th>Série</th>
			<th>Situa&ccedil;&atilde;o</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><strong><?= utf8_encode($row['ra']) ?></strong></td>
			<td><strong><?= utf8_encode($row['nome']) ?></strong></td>
			<td><?= utf8_encode($row['curso']) ?></td>
			<td><?= utf8_encode($row['serie']) ?></td>
			<td><?= utf8_encode($row['situacao']) ?></td>
		</tr>
	</tbody>
</table>


<hr>

<iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 30%; background-color: #fff; top: 0; left: 0; height: 300px; display: none"></iframe>

<hr>



<div class="modal fade" id="pagamentos_modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document" id="pagamentos_modal_conteudo"></div>
</div>

<style>
	.disabled-link {
		pointer-events: none;
	}
</style>



<script>
	function retirar(select, arquivo) {

		console.log(arquivo);

		let arquivo_retirar = arquivo == "retirar" ? "retirar" : "retirar_uniforme";
		let arquivo_devolver = arquivo == "retirar" ? "devolver" : "devolver_uniforme";

		if (select.checked) {
			$.getJSON("modulos/tesouraria/esportes/retirada/ajax/" + arquivo_retirar + ".php", {
					id_pessoa: <?= $id_pessoa ?>
				})
				.done(function(json) {
					$("#titulos_em_aberto_resultado").load(
						"modulos/tesouraria/esportes/retirada/ajax/camiseta.php?id_usuario=<?= $_GET['id_usuario'] ?>");
				})
				.fail(function(jqxhr, textStatus, error) {
					var err = textStatus + ", " + error;
					console.log("Request Failed: " + err);
				});
		} else {
			$.getJSON("modulos/tesouraria/esportes/retirada/ajax/" + arquivo_devolver + ".php", {
					id_pessoa: <?= $id_pessoa ?>
				})
				.done(function(json) {
					$("#titulos_em_aberto_resultado").load(
						"modulos/tesouraria/esportes/retirada/ajax/camiseta.php?id_usuario=<?= $_GET['id_usuario'] ?>");
				})
				.fail(function(jqxhr, textStatus, error) {
					var err = textStatus + ", " + error;
					console.log("Request Failed: " + err);
				});
		}




	}
</script>
<?
$sql = "SELECT
			id_matricula_boleto,
			id_matricula,
			p.id_pessoa,
			nome,
			DATE ( data_matricula ) as data_matricula,
			b.valor,
			pago,
			data_vencimento,
			parcela,
			DATEDIFF(now(), data_vencimento) AS dias_atraso,
			IF
				( MONTH ( data_vencimento ) = MONTH ( now()), 'Atual', 'Anterior' ) as situacao
		FROM
			colegio.cdt_matricula m
			INNER JOIN colegio.cdt_matricula_boleto b USING ( id_matricula )
			INNER JOIN coopex.pessoa p ON m.id_pessoa = p.id_pessoa 
		WHERE
			ativo = 1 
			AND m.id_pessoa = $id_pessoa
		ORDER BY
			parcela";
$res = $coopex->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$data_matricula = $row->data_matricula;
?>
<div class="row">
	<div class="col-lg-12">
		<div id="panel-4" class="panel">
			<div class="panel-hdr">
				<h2>
					Matriculados
				</h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<?
					$sql3 = "SELECT
								nome
							FROM
								colegio.cdt_matricula
								INNER JOIN colegio.cdt_aluno_matricula USING ( id_matricula ) 
							WHERE
								cdt_matricula.id_pessoa = $id_pessoa";
					$res_mod = $coopex->query($sql3);
					while ($row_mod = $res_mod->fetch(PDO::FETCH_OBJ)) {
					?>
						<div class="panel-tag mb-2">
							<strong><?= utf8_encode($row_mod->nome) ?></strong>
						</div>
					<?
					}
					?>
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
					Histórico de pagamentos
				</h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content bg-subtlelight-fade">
					<h1>Data de Matrícula: <strong><?= converterData($data_matricula) ?></strong></h1>
					<table id="tabelaBoleto" class="table table-bordered table-hover  w-100">
						<thead>
							<th width="10" style="text-align: center !important;">#</th>
							<th width="10">Parcela</th>
							<th width="10">Mês Ref.</th>
							<th class="text-center" width="10">Valor</th>
							<th class="text-center" width="10">Situação</th>
							<th width="10">Vencimento</th>
							<th class="text-center" width="10">Dias de Atraso</th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</thead>
						<tbody>
							<?
							$i = 0;

							$res = $coopex->query($sql);

							while ($row = $res->fetch(PDO::FETCH_OBJ)) {
								$i++;
								$vencido = false;
								if (!$row->pago) {
									if ($row->dias_atraso > 0) {
										$vencido = true;
									}
								}

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
							?>
								<tr id="linha_boleto_<?= $row->id_matricula_boleto ?>" class="<?= $vencido ? "bg-danger-50" : "" ?>">
									<td><?= $row->id_matricula_boleto ?></td>
									<td class="text-center"><?= $row->parcela ?></td>
									<td class="text-center"><?= $row->parcela + 4 ?></td>
									<td class="text-center" style="min-width: 100px;">
										<?= $row->valor ?>
									</td>
									<td class="text-center" style="min-width: 100px;">
										<?= $row->pago ? "PAGO" : "NÃO PAGO" ?>
									</td>

									<td style="text-align: center !important;">
										<?= $row->pago ? "-" : converterData($row->data_vencimento) ?>
									</td>
									<td class="text-center"><?= $vencido ? $row->dias_atraso : "-" ?></td>

									<td>
										<a href="https://coopex.fag.edu.br/boleto/clube_da_tarefa/matricula/<?= $row->id_matricula_boleto ?>" target="_blank" title="Gerar Boleto" class="btn btn-primary btn-icon rounded-circle waves-effect waves-themed <?= $row->pago ? "d-none" : "" ?>">
											<i class="fal fa-barcode-alt"></i>
										</a>
									</td>
									<td>
										<button onclick="atualizar_vencimento(<?= $row->id_matricula ?>,<?= $row->parcela ?>)" target="_blank" title="Atualizar Vencimento" class="btn btn-success  btn-icon rounded-circle waves-effect waves-themed <?= $row->pago ? "d-none" : "" ?>">
											<i class="fal fa-calendar-alt"></i>
										</button>
									</td>

									<td>
										<button onclick="baixar_pagamento(<?= $row->id_matricula_boleto ?>)" title="Baixar Pagamento" class="btn btn-secondary  btn-icon rounded-circle waves-effect waves-themed <?= $row->pago ? "d-none" : "" ?>">
											<i class="fal fa-arrow-down"></i>
										</button>
									</td>
									<td>
										<button onclick="cancelar_boleto(<?= $row->id_matricula_boleto ?>)" title="Cancelar Boleto" class="btn btn-danger  btn-icon rounded-circle waves-effect waves-themed <?= $row->pago ? "d-none" : "" ?>">
											<i class="fal fa-trash"></i>
										</button>
									</td>
									<td>
										<button onclick="janela_notificacao(<?= $row->id_pessoa ?>,<?= $whats ?>)" title="Notificar Atraso" class="btn btn-warning  btn-icon rounded-circle waves-effect waves-themed <?= $row->pago ? "d-none" : "" ?>">
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

<div class="row">
	<div class="col-lg-12">
		<div id="panel-4" class="panel">
			<div class="panel-hdr">
				<h2>
					Notificações
				</h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<?
					$sql3 = "SELECT
							modalidade 
						FROM
							colegio.matricula
							INNER JOIN colegio.modalidade_aluno_matricula USING ( id_matricula )
							INNER JOIN colegio.modalidade USING ( id_modalidade ) 
						WHERE
							id_pessoa = $id_pessoa";
					$res_mod = $coopex->query($sql3);
					while ($row_mod = $res_mod->fetch(PDO::FETCH_OBJ)) {
					?>
						<div class="panel-tag mb-2">
							<strong><?= utf8_encode($row_mod->modalidade) ?></strong>
						</div>
					<?
					}
					?>
				</div>
			</div>
		</div>
	</div>

</div>

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



	});
</script>

<style>
	#tabelaBoleto td,
	th {
		vertical-align: middle !important;
		text-align: center !important;
	}
</style>