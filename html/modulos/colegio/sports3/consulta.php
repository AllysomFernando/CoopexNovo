<?php

$id_menu = 23;

$sql = "SELECT
				id_periodo,
				periodo,
				DATE_FORMAT( pre_inscricao_data_inicial, '%d/%m/%Y' ) AS pre_inscricao_data_inicial,
				DATE_FORMAT( pre_inscricao_data_final, '%d/%m/%Y' )  AS pre_inscricao_data_final,
				DATE_FORMAT( inscricao_data_inicial, '%d/%m/%Y' ) 	 AS inscricao_data_inicial,
				DATE_FORMAT( inscricao_data_final, '%d/%m/%Y' ) 	 AS inscricao_data_final,
				ativo 
			FROM
				coopex_reoferta.periodo
			WHERE
				excluido = 0	
			ORDER BY
				periodo DESC";

$sql = "SELECT
		*, sports.cancelado as cancelado,
		matricula.id_situacao_atestado AS atestado_matricula,
		atestado.id_situacao_atestado AS atestado_atestado ,
		atestado.extensao AS extensao 
		FROM
		colegio.sports
		INNER JOIN coopex.pessoa USING ( id_pessoa )
		LEFT JOIN colegio.matricula USING ( id_pessoa )
		LEFT JOIN colegio.atestado USING ( id_pessoa ) 
		ORDER BY
		nome";

$reoferta = $coopex->query($sql);

?>

<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Escola de Esportes</a></li>
		<li class="breadcrumb-item active">Consulta</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title col-6">
			<i class='subheader-icon fal fa-repeat'></i> Escola de Esportes
		</h1>

	</div>
	<div class="row">
		<div class="col-xl-12">
			<div id="panel-1" class="panel">

				<div class="panel-container show">
					<div class="panel-content">
						<!-- datatable start -->
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th>ID</th>
									<th>Aluno</th>
									<th>Pago</th>
									<th>Boleto</th>
									<th>Atestado</th>
									<th>Nome Camiseta</th>
									<th>Tamanho</th>
									<th>#</th>
								</tr>
							</thead>
							<tbody>
								<?php
								while ($row = $reoferta->fetch(PDO::FETCH_OBJ)) {
									if($row->cancelado){
										$situacao = "Cancelado";
										$cor = "danger";
									} else if ($row->id_matricula) {
										$situacao = "Matriculado";
										$cor = "success";
									} else {
										$situacao = "Não<br>Matriculado";
										$cor = "warning";
									}

									?>
									<tr>
										<td valign="middle" class="pointer"><?php echo ($row->id_sports) ?></td>
										<td valign="middle" class="pointer"><?php echo utf8_encode($row->nome) ?></td>
										<td>
											<div class="custom-control custom-switch">
												<input onchange="baixar_pagamento(<?= $row->id_sports ?>, this)"
													<?= $row->pagamento ? "disabled" : "" ?> 	<?= $row->pagamento ? "checked" : "" ?> onchange="retirar(<?= $row->pagamento ?>, this)" type="checkbox"
													class="custom-control-input" id="pgto_<?= $row->id_sports ?>">
												<label class="custom-control-label"
													for="pgto_<?= $row->id_sports ?>"></label>
											</div>
										</td>
										<td align="center">
											<a target="_blank"
												href="https://coopex.fag.edu.br/boleto/sports/<?= $row->id_pessoa ?>"
												class="">
												<? if (!$row->pagamento) { ?>
													<span class="fal fa-barcode mr-1"></span><br>Boleto
												<? } ?>
											</a>
										</td>
										<td align="center">
											<?
											//if ($row->id_atestado) {
											?>
											<div class="custom-control custom-switch">
												<input onchange="baixar_atestado(<?= $row->id_pessoa ?>, this)"
													<?= $row->atestado_matricula || $row->atestado_atestado ? "checked" : "" ?> onchange="retirar(<?= $row->pagamento ?>, this)" type="checkbox"
													class="custom-control-input" id="atestado_<?= $row->id_sports ?>">
												<label class="custom-control-label"
													for="atestado_<?= $row->id_sports ?>"></label>
											</div>
											<?
											if ($row->extensao != 'sec') {
												?>
												<a target="_blank"
													href="https://coopex.fag.edu.br/arquivos/colegio/sports/atestado/<?= $row->id_atestado ?>.<?= $row->extensao ?>">Visualizar</a>
											<? } else { ?>
												<span>Secretaria</span>
											<?
											}
											//}
											?>
										</td>
										<td>
											<div class="input-group">
												<input id="camiseta_nome_<?= $row->id_sports ?>" type="text"
													class="form-control" value="<?= utf8_encode($row->nome_camiseta) ?>">
												<div class="input-group-append">
													<button onclick="camiseta_nome(<?= $row->id_sports ?>)"
														class="btn btn-primary waves-effect waves-themed" type="button"
														id="button-addon2"><i class="fal fa-save"></i></button>
												</div>
											</div>
										</td>

										<td style="min-width: 100px;">
											<select id="camiseta_<?= $row->id_sports ?>"
												onchange="alterar_tamanho(<?= $row->id_sports ?>)"
												class="form-control select2">
												<?
												$sql = "SELECT
															* 
														FROM
															colegio.camiseta_tamanho";
												$camiseta = $coopex->query($sql);
												while ($row_camiseta = $camiseta->fetch(PDO::FETCH_OBJ)) {
													?>
													<option <?= $row_camiseta->id_camiseta_tamanho == $row->id_camiseta_tamanho ? "selected" : "" ?> value="<?= $row_camiseta->id_camiseta_tamanho ?>">
														<?= $row_camiseta->tamanho ?></option>
												<?
												}
												?>

											</select>
										</td>
										<td><span
												class="badge badge-<?php echo $cor; ?> badge-pill"><?php echo $situacao; ?></span>
										</td>

									</tr>
									<?php
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

<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script>
	$(document).ready(function () {
		$('#dt-basic-example').dataTable({
			responsive: true,
			"aaSorting": [],
			stateSave: true
		});
	});

	function alterar_tamanho(id_sports) {
		var tamanho = $("#camiseta_" + id_sports).val();
		$.getJSON("modulos/colegio/sports/ajax/camiseta_tamanho.php", {
			id_sports: id_sports,
			tamanho: tamanho
		})
	}

	function camiseta_nome(id_sports) {
		var nome = $("#camiseta_nome_" + id_sports).val();
		$.getJSON("modulos/colegio/sports/ajax/camiseta_nome.php", {
			id_sports: id_sports,
			nome: nome
		})
	}

	function baixar_pagamento(id_sports, select) {

		console.log(select.checked)

		if (select.checked) {
			$.getJSON("modulos/colegio/sports/ajax/baixar_pagamento.php", {
				id_sports: id_sports
			})

		} else {
			$.getJSON("modulos/colegio/sports/ajax/retirar_pagamento.php", {
				id_sports: id_sports
			})
		}

	}

	function baixar_atestado(id_pessoa, select) {

		console.log(select.checked)

		if (select.checked) {
			$.getJSON("modulos/colegio/sports/ajax/baixar_atestado.php", {
				id_pessoa: id_pessoa
			})
		} else {
			$.getJSON("modulos/colegio/sports/ajax/retirar_atestado.php", {
				id_pessoa: id_pessoa
			})
		}

	}

	function exclusaoOK() {
		Swal.fire({
			type: "success",
			title: "Registro excluido com sucesso!",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				document.location.reload(true)
			}
		});
	}

	function exclusaoFalha() {
		Swal.fire({
			type: "error",
			title: "Falha ao excluir registro",
			showConfirmButton: true
		});
	}
</script>
</body>

</html>