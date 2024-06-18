<?php
$id_menu = 22;

//print_r($_SESSION);

#VERIFICA SE O TIPO DE USUÁRIO POSSUI PERMISSÃO PARA ACESSAR TODOS OS REGISTROS
if (in_array($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'], array(1, 2, 3, 8, 9, 11))) {
	$where  = " AND 1=1 ";
} else {
	$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];
	$where  = "AND (coopex_reoferta.reoferta.id_pessoa = $id_pessoa
					OR id_departamento IN (SELECT id_departamento FROM coopex.departamento_pessoa WHERE id_pessoa = $id_pessoa)) ";
}
$id_reoferta = $_GET['id'];

$sql = "SELECT * FROM coopex_reoferta.reoferta INNER JOIN coopex_reoferta.carga_horaria USING ( id_carga_horaria ) INNER JOIN coopex.departamento USING ( id_departamento )  WHERE id_reoferta = $id_reoferta";
$reoferta = $coopex->query($sql);
$reoferta = $reoferta->fetch(PDO::FETCH_OBJ);

$sql = "SELECT valor_1 FROM coopex_reoferta.carga_horaria WHERE id_carga_horaria = $reoferta->id_carga_horaria";
$ch = $coopex->query($sql);
$ch = $ch->fetch(PDO::FETCH_OBJ);

$sql = "SELECT id_reoferta, data_vencimento FROM coopex_reoferta.pre_matricula WHERE id_reoferta = $id_reoferta";
$res_total = $coopex->query($sql);
$pre_total = $res_total->rowCount();
$row_pre_total = $res_total->fetch(PDO::FETCH_OBJ);

$sql = "SELECT id_reoferta FROM coopex_reoferta.pre_matricula WHERE id_reoferta = $id_reoferta  AND pago = 0";
$pre_total_nao_pago = $coopex->query($sql);
$pre_total_nao_pago = $pre_total_nao_pago->rowCount();

$sql = "SELECT id_reoferta FROM coopex_reoferta.pre_matricula WHERE id_reoferta = $id_reoferta AND pago = 1";
$pre_total_pago = $coopex->query($sql);
$pre_total_pago = $pre_total_pago->rowCount();

$sql = "SELECT id_reoferta FROM coopex_reoferta.pre_matricula WHERE id_reoferta = $id_reoferta AND pago = 1 and permissao_matricula = 1";
$pre_total_apto = $coopex->query($sql);
$pre_total_apto = $pre_total_apto->rowCount();

$sql = "SELECT id_reoferta FROM coopex_reoferta.matricula WHERE id_reoferta = $id_reoferta";
$matricula_total = $coopex->query($sql);
$matricula_total = $matricula_total->rowCount();

$sql = "SELECT id_reoferta FROM coopex_reoferta.matricula WHERE id_reoferta = $id_reoferta AND pago = 0";
$matricula_total_nao_pago = $coopex->query($sql);
$matricula_total_nao_pago = $matricula_total_nao_pago->rowCount();

$sql = "SELECT id_reoferta FROM coopex_reoferta.matricula WHERE id_reoferta = $id_reoferta AND pago = 1";
$matricula_total_pago = $coopex->query($sql);
$matricula_total_pago = $matricula_total_pago->rowCount();



if ($pre_total_apto > 0) {

	if ($pre_total_apto >= $reoferta->reoferta_minimo) {
		$tabela_valor = $reoferta->reoferta_minimo;
	} else {
		$tabela_valor = $pre_total_apto;
	}


	#DETERMINA O VALOR DA MATRÍCULA
	$coluna = "valor_$tabela_valor";
	$sql = "SELECT
					$coluna
				FROM
					coopex_reoferta.carga_horaria 
				WHERE
					id_carga_horaria = $reoferta->id_carga_horaria";
	$res = $coopex->query($sql);
	$valor = $res->fetch(PDO::FETCH_ASSOC);
	$valor_reoferta = $valor[$coluna];
} else {
	$valor_reoferta = 0;
	$tabela_valor = 0;
}

$sql = "SELECT
				*,
				DATE_FORMAT( data_pre_matricula, '%d/%m/%Y' ) AS data_pre_matricula,
				p.pago AS pre_pago,
				DATE_FORMAT( p.data_vencimento, '%d/%m/%Y' ) AS data_vencimento_pre,
				DATE_FORMAT( b.data_pagamento, '%d/%m/%Y' ) AS data_pagamento,
				b.valor_pago,
				pe.id_pessoa 
			FROM
				coopex.pessoa pe
				INNER JOIN coopex_reoferta.pre_matricula p USING ( id_pessoa )
				LEFT JOIN coopex_reoferta.pre_matricula_boleto b USING ( id_pre_matricula ) 
			WHERE
				p.id_reoferta = $id_reoferta 
						order BY nome";
$inscritos = $coopex->query($sql);
?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<script src="js/core.js"></script>
<iframe class="d-none" name="dados" src=""></iframe>
<form id="form_excluir_registro" method="post" target="dados" action="modulos/reoferta/cadastro/cadastro_dados.php">
	<input type="hidden" id="excluir_registro" name="excluir_registro">
</form>
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Reoferta</a></li>
		<li class="breadcrumb-item active">Consulta</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title col-6">
			<i class='subheader-icon fal fa-repeat'></i> <?php echo utf8_encode($reoferta->disciplina) ?>
			<small>
				<?php echo $reoferta->carga_horaria ?> horas
			</small>
		</h1>

	</div>
	<div class="row">


		<div class="col-xl-12">

			<div id="panel-5" class="panel">
				<div class="panel-hdr">
					<h2>
						Resumo de inscrições
					</h2>

				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<div class="frame-wrap">
							<table class="table table-bordered table-hover m-0">
								<thead class="thead-themed">
									<tr>
										<th>R$ <?php echo number_format($ch->valor_1, 2, ',', '.'); ?></th>
										<th class="text-center">Total</th>
										<th class="text-center">Não pagos</th>
										<th class="text-center">Pagos</th>
										<th class="text-center">Pagos Aptos</th>
										<th>Valor por acadêmico</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th scope="row">Pré-matriculados</th>
										<td class="text-center"><?php echo $pre_total ?></td>
										<td class="text-center"><?php echo $pre_total_nao_pago ?></td>
										<td class="text-center"><?php echo $pre_total_pago ?></td>
										<td class="text-center"><?php echo $pre_total_apto ?></td>
										<td>R$ 60,00</td>
									</tr>
									<tr>
										<th scope="row">Matriculados</th>
										<td class="text-center"><?php echo $matricula_total ?></td>
										<td class="text-center"><?php echo $matricula_total_nao_pago ?></td>
										<td class="text-center"><?php echo $matricula_total_pago ?></td>
										<td class="text-center"><?php echo "Divisão do valor por <strong>$tabela_valor</strong>" ?></td>
										<td class="bg-danger-500">R$ <?php echo number_format($valor_reoferta, 2, ',', '.'); ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Inscrições
					</h2>

				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<!-- datatable start -->
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th>Acadêmico</th>
									<th class="text-center" width="100" style="min-width: 100px">Pré-matrícula<br><?php echo converterData($reoferta->pre_inscricao_data_inicial) ?><br><?php echo converterData($reoferta->pre_inscricao_data_final) ?></th>
									<th class="text-center" width="100" style="min-width: 100px">Matrícula<br><?php echo converterData($reoferta->inscricao_data_inicial) ?><br><?php echo converterData($reoferta->inscricao_data_final) ?></th>
									<th class="text-center">Dependência na Disciplina</th>
									<th class="text-center">Matrícula no Período</th>
									<th class="text-center">Limite de Reofertas no Período</th>
									<th class="text-center">Choque de Horário</th>
									<th class="text-center">Pendência Financeira</th>
									<th class="text-center">Permissão para Matricula</th>
								</tr>
							</thead>
							<tbody>
								<?php
								while ($row = $inscritos->fetch(PDO::FETCH_OBJ)) {


									if ($row->data_pre_matricula && $row->pre_pago) {
										$situacao_pre = "Pago";
										$cor_pre = "success";
										$link_pre = '';
									} else {
										$situacao_pre = "Não pago";
										$cor_pre = "warning";
										$link_pre = 'data-toggle="modal" data-target="#modal_pre"';
									}


									$sql_matricula = "SELECT
													*,
													DATE_FORMAT( data_matricula, '%d/%m/%Y' ) AS data_matricula,
													m.pago as matricula_pago,
													DATE_FORMAT( m.data_vencimento, '%d/%m/%Y' ) AS data_vencimento_matricula,
													DATE_FORMAT( b.data_pagamento, '%d/%m/%Y' ) AS data_pagamento,
														b.valor_pago, divisao, pagamento_tesouraria
												FROM
													coopex_reoferta.matricula m
													LEFT JOIN coopex_reoferta.matricula_boleto b USING (id_matricula)
												WHERE
													m.id_reoferta = $id_reoferta
													and m.id_pessoa = " . $row->id_pessoa;
									$inscritos_matricula = $coopex->query($sql_matricula);
									$row2 = $inscritos_matricula->fetch(PDO::FETCH_OBJ);


									if (!@$row2->data_matricula) {
										$situacao_matricula = "Pendente";
										$cor_matricula = "danger";
										$link = 'data-toggle="modal" data-target="#modal_matricular"';
									} else if ($row2->data_matricula && $row2->matricula_pago) {
										$situacao_matricula = "Pago";
										$cor_matricula = "success";
										$link = 'data-toggle="modal" data-target="#modal_pagamento"';
									} else if ($row2->data_matricula) {
										$situacao_matricula = "Não pago";
										$cor_matricula = "warning";
										$link = 'data-toggle="modal" data-target="#modal_matricula"';
									}

									$tesouraria = @$row2->pagamento_tesouraria ? "Tesouraria" : "Boleto";

								?>
									<tr>
										<td class="pointer"><?php echo texto($row->nome) ?></td>
										<td class="text-center">
											<span onclick="pre_matricula_funcoes(<?php echo $row->id_pre_matricula ?>, '<?php echo $row->data_vencimento_pre ?>')" <?php echo $link_pre; ?> class="btn btn-<?php echo $cor_pre; ?> btn-sm btn-block waves-effect waves-themed posi"><?php echo $situacao_pre; ?></span>
										</td>
										<td class="text-center">
											<?php
											if (@$row2->data_matricula) {
												if ($row2->matricula_pago) {
											?>
													<span onclick="pagamento_funcoes('<?php echo $row2->data_pagamento ?>', '<?php echo $row2->data_vencimento_matricula ?>', 'R$ <?=  number_format($row2->valor_pago, 2, ',', '.'); ?>', '<?= $row2->divisao ?>', '<?=$tesouraria ?>')" <?php echo $link ?> class="btn btn-<?php echo $cor_matricula; ?> btn-sm btn-block waves-effect waves-themed"><?php echo $situacao_matricula; ?></span>
												<?php
												} else {
												?>
													<span onclick="matricula_funcoes(<?php echo $row2->id_matricula ?>, '<?php echo $row2->data_vencimento_matricula ?>')" <?php echo $link ?> class="btn btn-<?php echo $cor_matricula; ?> btn-sm btn-block waves-effect waves-themed"><?php echo $situacao_matricula; ?></span>
												<?
												}
											} else {
												?>
												<span onclick="matricular(<?php echo $row->id_pessoa ?>)" <?php echo $link ?> class="btn btn-<?php echo $cor_matricula; ?> btn-sm btn-block waves-effect waves-themed"><?php echo $situacao_matricula; ?></span>
											<?php
											}
											?>
										</td>
										<td class="text-center">
											<?php
											if ($row->dependente_disciplina) {
												$sql = "SELECT nome, DATE_FORMAT( data_autorizacao, '%d/%m/%Y - %H:%i:%s' ) AS data_autorizacao2, obs FROM coopex_reoferta.matricula_autorizacao
														INNER JOIN coopex.pessoa USING ( id_pessoa ) WHERE	id_autorizacao = 1 AND id_pre_matricula = " . $row->id_pre_matricula . " ORDER BY data_autorizacao DESC";
												$autorizacao = $coopex->query($sql);

												if ($autorizacao->rowCount()) {
													$row_autorizacao = $autorizacao->fetch(PDO::FETCH_OBJ);
											?>
													<button data-toggle="modal" data-target="#modal_autorizacao" onclick="mostrar_autorizacao('<?php echo trim(utf8_encode($row_autorizacao->nome)) ?>','<?php echo $row_autorizacao->data_autorizacao2 ?>','<?php echo trim(utf8_encode($row_autorizacao->obs)) ?>')" class="btn btn-outline-success btn-icon position-relative js-waves-off"><i class="fal fa-check"></i><span class="badge border border-light rounded-pill bg-danger-500 position-absolute pos-top pos-right">1</span></button>
												<?php
												} else {
													echo '<button href="javascript:void(0);" class="btn btn-outline-success btn-icon position-relative js-waves-off"><i class="fal fa-check"></i></button>';
												}
											} else {
												?>
												<button <?= isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][9]) ? "" : "disabled" ?> onclick="autorizar('<?php echo $row->id_pre_matricula ?>',1,'Dependência na Disciplina')" data-toggle="modal" data-target="#modal" class="btn btn-outline-danger btn-icon waves-effect waves-themed"><i class="fal fa-times"></i></button>
											<?php
											}
											?>
										</td>
										<td class="text-center">
											<?php
											if ($row->matriculado_no_periodo) {
												$sql = "SELECT nome, DATE_FORMAT( data_autorizacao, '%d/%m/%Y - %H:%i:%s' ) AS data_autorizacao, obs FROM coopex_reoferta.matricula_autorizacao
														INNER JOIN coopex.pessoa USING ( id_pessoa ) WHERE	id_autorizacao = 2 AND id_pre_matricula = " . $row->id_pre_matricula;
												$autorizacao = $coopex->query($sql);

												if ($autorizacao->rowCount()) {
													$row_autorizacao = $autorizacao->fetch(PDO::FETCH_OBJ);
											?>
													<button data-toggle="modal" data-target="#modal_autorizacao" onclick="mostrar_autorizacao('<?php echo utf8_encode($row_autorizacao->nome) ?>','<?php echo $row_autorizacao->data_autorizacao ?>','<?php echo utf8_encode($row_autorizacao->obs) ?>')" class="btn btn-outline-success btn-icon position-relative js-waves-off"><i class="fal fa-check"></i><span class="badge border border-light rounded-pill bg-danger-500 position-absolute pos-top pos-right">1</span></button>
												<?php
												} else {
													echo '<button href="javascript:void(0);" class="btn btn-outline-success btn-icon position-relative js-waves-off"><i class="fal fa-check"></i></button>';
												}
											} else {
												?>
												<button <?= isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][9]) ? "" : "disabled" ?> onclick="autorizar('<?php echo $row->id_pre_matricula ?>',2,'Matrícula no Período')" data-toggle="modal" data-target="#modal" class="btn btn-outline-danger btn-icon waves-effect waves-themed"><i class="fal fa-times"></i></button>
											<?php											}
											?>
										</td>
										<td class="text-center">
											<?php
											if ($row->reofertas_cursadas_no_periodo) {
												$sql = "SELECT nome, DATE_FORMAT( data_autorizacao, '%d/%m/%Y - %H:%i:%s' ) AS data_autorizacao, obs FROM coopex_reoferta.matricula_autorizacao
														INNER JOIN coopex.pessoa USING ( id_pessoa ) WHERE	id_autorizacao = 3 AND id_pre_matricula = " . $row->id_pre_matricula;
												$autorizacao = $coopex->query($sql);

												if ($autorizacao->rowCount()) {
													$row_autorizacao = $autorizacao->fetch(PDO::FETCH_OBJ);
											?>
													<button data-toggle="modal" data-target="#modal_autorizacao" onclick="mostrar_autorizacao('<?php echo utf8_encode($row_autorizacao->nome) ?>','<?php echo $row_autorizacao->data_autorizacao ?>','<?php echo utf8_encode($row_autorizacao->obs) ?>')" class="btn btn-outline-success btn-icon position-relative js-waves-off"><i class="fal fa-check"></i><span class="badge border border-light rounded-pill bg-danger-500 position-absolute pos-top pos-right">1</span></button>
												<?php
												} else {
													echo '<button href="javascript:void(0);" class="btn btn-outline-success btn-icon position-relative js-waves-off"><i class="fal fa-check"></i></button>';
												}
											} else {
												?>
												<button <?= isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][9]) ? "" : "disabled" ?> onclick="autorizar('<?php echo $row->id_pre_matricula ?>',3,'Limite de Reofertas no Período')" data-toggle="modal" data-target="#modal" class="btn btn-outline-danger btn-icon waves-effect waves-themed"><i class="fal fa-times"></i></button>
											<?php
											}
											?>
										</td>
										<td class="text-center">
											<?php
											if ($row->choque_de_horario) {
												$sql = "SELECT nome, DATE_FORMAT( data_autorizacao, '%d/%m/%Y - %H:%i:%s' ) AS data_autorizacao, obs FROM coopex_reoferta.matricula_autorizacao
														INNER JOIN coopex.pessoa USING ( id_pessoa ) WHERE	id_autorizacao = 4 AND id_pre_matricula = " . $row->id_pre_matricula;
												$autorizacao = $coopex->query($sql);

												if ($autorizacao->rowCount()) {
													$row_autorizacao = $autorizacao->fetch(PDO::FETCH_OBJ);
											?>
													<button data-toggle="modal" data-target="#modal_autorizacao" onclick="mostrar_autorizacao('<?php echo utf8_encode($row_autorizacao->nome) ?>','<?php echo $row_autorizacao->data_autorizacao ?>','<?php echo utf8_encode($row_autorizacao->obs) ?>')" class="btn btn-outline-success btn-icon position-relative js-waves-off"><i class="fal fa-check"></i><span class="badge border border-light rounded-pill bg-danger-500 position-absolute pos-top pos-right">1</span></button>
												<?php
												} else {
													echo '<button href="javascript:void(0);" class="btn btn-outline-success btn-icon position-relative js-waves-off"><i class="fal fa-check"></i></button>';
												}
											} else {
												?>
												<button <?= isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][9]) ? "" : "disabled" ?> onclick="autorizar('<?php echo $row->id_pre_matricula ?>',4,'Choque de Horário')" data-toggle="modal" data-target="#modal" class="btn btn-outline-danger btn-icon waves-effect waves-themed"><i class="fal fa-times"></i></button>
											<?php
											}
											?>
										</td>
										<td class="text-center">
											<?php
											if ($row->pendencia_financeira) {
												$sql = "SELECT nome, DATE_FORMAT( data_autorizacao, '%d/%m/%Y - %H:%i:%s' ) AS data_autorizacao, obs FROM coopex_reoferta.matricula_autorizacao
														INNER JOIN coopex.pessoa USING ( id_pessoa ) WHERE	id_autorizacao = 5 AND id_pre_matricula = " . $row->id_pre_matricula;
												$autorizacao = $coopex->query($sql);

												if ($autorizacao->rowCount()) {
													$row_autorizacao = $autorizacao->fetch(PDO::FETCH_OBJ);
											?>
													<button data-toggle="modal" data-target="#modal_autorizacao" onclick="mostrar_autorizacao('<?php echo utf8_encode($row_autorizacao->nome) ?>','<?php echo $row_autorizacao->data_autorizacao ?>','<?php echo utf8_encode($row_autorizacao->obs) ?>')" class="btn btn-outline-success btn-icon position-relative js-waves-off"><i class="fal fa-check"></i><span class="badge border border-light rounded-pill bg-danger-500 position-absolute pos-top pos-right">1</span></button>
												<?php
												} else {
													echo '<button href="javascript:void(0);" class="btn btn-outline-success btn-icon position-relative js-waves-off"><i class="fal fa-check"></i></button>';
												}
											} else {
												?>
												<button <?= isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][9]) ? "" : "disabled" ?> onclick="autorizar('<?php echo $row->id_pre_matricula ?>',5,'Pendência Financeira')" data-toggle="modal" data-target="#modal" class="btn btn-outline-danger btn-icon waves-effect waves-themed"><i class="fal fa-times"></i></button>
											<?php
											}
											?>
										</td>


										<td class="text-center">
											<?php
											if ($row->permissao_matricula) {
												echo '<button onclick="gerar_avaliacao(' . $row->id_pessoa . ')" class="btn btn-success btn-icon waves-effect waves-themed"><i class="fal fa-check"></i></button>';
											} else {
												echo '<button onclick="gerar_avaliacao(' . $row->id_pessoa . ')" class="btn btn-danger btn-icon waves-effect waves-themed"><i class="fal fa-times"></i></button>';
											}
											?>
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
		<?php
		if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][8])) {
		?>
			<div class="col-xl-12">
				<div id="panel-1" class="panel">
					<div class="panel-hdr">
						<h2>
							Pré-Matricular Acadêmico
						</h2>
					</div>
					<div class="panel-container show">
						<div class="panel-content">
							<div class="form-group">
								<label class="form-label" for="select2-ajax">
									Selecione o usuário do Sagres
								</label>
								<select onChange="" data-placeholder="Digite o nome do acadêmico..." class="js-consultar-usuario form-control" id="select2-ajax"></select>
							</div>

							<div id="titulos_em_aberto_resultado">
							</div>

						</div>
					</div>
				</div>
			</div>
		<?php
		}
		?>
	</div>
</main>
<script src="js/formplugins/select2/select2.bundle.js"></script>
<iframe class="d-none" name="dados"></iframe>
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-hidden="true">
	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/reoferta/inscritos/autorizacao_dados.php">
		<input type="hidden" name="id_autorizacao" id="id_autorizacao">
		<input type="hidden" name="id_pre_matricula" id="id_pre_matricula">
		<input type="hidden" name="id_reoferta" value="<?php echo $id_reoferta ?>">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="panel-hdr">
					<h2>
						Autorizar <span class="fw-300"><i id="motivo_titulo">defaults</i></span>
					</h2>
					<div class="panel-toolbar">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true"><i class="fal fa-times"></i></span>
						</button>
					</div>
				</div>
				<div class="modal-body">
					<div class="form-row">




						<div class="col-md-12 mb-3">
							<label class="form-label" for="validationCustom02">Motivo <span class="text-danger"></span></label>
							<textarea required="" id="obs" type="text" name="obs" class="form-control"></textarea>
						</div>

					</div>
				</div>
				<div class="modal-footer modal-footer pt-3 pb-3 border-faded border-left-0 border-right-0 border-bottom-0">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
					<button type="submit" class="btn btn-primary">Autorizar</button>
				</div>
			</div>
		</div>
	</form>
</div>


<div class="modal fade" id="modal_autorizacao" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="panel-hdr">
				<h2>
					Autorização
				</h2>
				<div class="panel-toolbar">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true"><i class="fal fa-times"></i></span>
					</button>
				</div>
			</div>
			<div class="modal-body">
				<div class="form-row">
					<div class="col-md-12 mb-3">
						<label class="form-label" for="validationCustom02">Autorizado por <span class="text-danger"></span></label>
						<input id="nome" type="text" readonly="" class="form-control">
					</div>
				</div>
				<div class="form-row">
					<div class="col-md-12 mb-3">
						<label class="form-label" for="validationCustom02">Em <span class="text-danger"></span></label>
						<input id="data_autorizacao" type="text" readonly="" class="form-control">
					</div>
				</div>
				<div class="form-row">
					<div class="col-md-12 mb-3">
						<label class="form-label" for="validationCustom02">Motivo <span class="text-danger"></span></label>
						<input id="motivo" type="text" readonly="" class="form-control">
					</div>
				</div>
			</div>
			<div class="modal-footer pt-3 pb-3 border-faded border-left-0 border-right-0 border-bottom-0">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="modal_matricular" tabindex="-1" role="dialog" aria-hidden="true">
	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/reoferta/inscritos/matricular_dados.php">
		<input type="hidden" name="id_pessoa" id="id_pessoa">
		<input type="hidden" name="id_reoferta" value="<?php echo $id_reoferta ?>">
		<input type="hidden" name="reoferta_minimo" value="<?php echo $tabela_valor ?>">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content ">
				<div class="panel-hdr">
					<h2>
						Marticular
					</h2>
					<div class="panel-toolbar">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true"><i class="fal fa-times"></i></span>
						</button>
					</div>
				</div>

				<div class="modal-body">
					<div class="text-center">
						<button type="submit" class="btn btn-lg btn-success waves-effect waves-themed">
							<span class="fal fa-user mr-1"></span>
							Matricular Acadêmico
						</button>
					</div>
				</div>
				<div class="modal-footer pt-3 pb-3 border-faded border-left-0 border-right-0 border-bottom-0">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
				</div>
			</div>
		</div>
	</form>
</div>


<div class="modal fade" id="modal_pre" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content ">
			<div class="panel-hdr">
				<h2>
					Pré-matrícula
				</h2>
				<div class="panel-toolbar">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true"><i class="fal fa-times"></i></span>
					</button>
				</div>
			</div>

			<div class="modal-body">
				<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/reoferta/inscritos/vencimento_pre.php">
					<div class="form-row">
						<input type="hidden" name="id_pre_matricula" id="baixa_pre_matricula_vencimento">
						<div class="col-md-3">
							<label class="form-label" for="validationCustom02">Data de vencimento <span class="text-danger"></span></label>
							<input data-inputmask="'mask': '99/99/9999'" id="pre_data_vencimento" name="data_vencimento" type="text" class="form-control">
						</div>
						<div class="col-md-6">
							<label class="form-label" for="validationCustom02">&nbsp;<span class="text-danger"></span></label><br>
							<button type="submit" class="btn btn-success waves-effect waves-themed">
								<span class="fal fa-calendar-alt mr-1"></span>
								Alterar data de vencimento
							</button>
						</div>

					</div>
				</form>
				<hr>
				<div class="form-row">
					<div class="col-md-6">
						<a href="" target="_blank" id="gerar_boleto_pre" type="submit" class="btn btn-warning waves-effect waves-themed">
							<span class="fal fa-barcode-read mr-1"></span>
							Gerar Boleto
						</a>
					</div>
				</div>
				<hr>
				<div class="form-row">
					<div class="col-md-6">
						<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/reoferta/inscritos/baixa_pre.php">
							<input type="hidden" name="id_pre_matricula" id="baixa_pre_matricula">
							<button type="submit" class="btn btn-danger waves-effect waves-themed">
								<span class="fal fa-check mr-1"></span>
								Dar baixa no pagamento
							</button>
						</form>
					</div>
				</div>
			</div>
			<div class="modal-footer pt-3 pb-3 border-faded border-left-0 border-right-0 border-bottom-0">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_matricula" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content ">
			<div class="panel-hdr">
				<h2>
					Matrícula
				</h2>
				<div class="panel-toolbar">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true"><i class="fal fa-times"></i></span>
					</button>
				</div>
			</div>

			<div class="modal-body">
				<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/reoferta/inscritos/vencimento_matricula.php">
					<div class="form-row">
						<input type="hidden" name="id_matricula" id="baixa_matricula_vencimento">
						<div class="col-md-3">
							<label class="form-label" for="validationCustom02">Data de vencimento <span class="text-danger"></span></label>
							<input data-inputmask="'mask': '99/99/9999'" id="matricula_data_vencimento" name="data_vencimento" type="text" class="form-control">
						</div>
						<div class="col-md-6">
							<label class="form-label" for="validationCustom02">&nbsp;<span class="text-danger"></span></label><br>
							<button type="submit" class="btn btn-success waves-effect waves-themed">
								<span class="fal fa-calendar-alt mr-1"></span>
								Alterar data de vencimento
							</button>
						</div>

					</div>
				</form>
				<hr>
				<div class="form-row">
					<div class="col-md-6">
						<a href="" target="_blank" id="gerar_boleto_matricula" type="submit" class="btn btn-warning waves-effect waves-themed">
							<span class="fal fa-barcode-read mr-1"></span>
							Gerar Boleto
						</a>
					</div>
				</div>
				<hr>
				<div class="form-row">
					<div class="col-md-6">
						<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/reoferta/inscritos/baixa_matricula.php">
							<input type="hidden" name="id_matricula" id="baixa_matricula">
							<button type="submit" class="btn btn-danger waves-effect waves-themed">
								<span class="fal fa-check mr-1"></span>
								Dar baixa no pagamento
							</button>
						</form>
					</div>
				</div>
			</div>
			<div class="modal-footer pt-3 pb-3 border-faded border-left-0 border-right-0 border-bottom-0">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
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


<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script>
	function pre_matricula_funcoes(id_pre_matricula, data_vencimento) {
		$("#gerar_boleto_pre").attr("href", "https://coopex.fag.edu.br/boleto/reoferta/prematricula/" + id_pre_matricula);
		$("#baixa_pre_matricula").val(id_pre_matricula);
		$("#baixa_pre_matricula_vencimento").val(id_pre_matricula);
		$("#pre_data_vencimento").val(data_vencimento);
	}

	function matricula_funcoes(id_matricula, data_vencimento) {
		$("#gerar_boleto_matricula").attr("href", "https://coopex.fag.edu.br/boleto/reoferta/matricula/" + id_matricula);
		$("#baixa_matricula").val(id_matricula);
		$("#baixa_matricula_vencimento").val(id_matricula);
		$("#matricula_data_vencimento").val(data_vencimento);
	}

	function pagamento_funcoes(data_pagamento, data_vencimento, valor_pago, divisao, tipo_pagamento) {
		$("#data_vencimento").html(data_vencimento);
		$("#data_pagamento").html(data_pagamento);
		$("#valor_pago").html(valor_pago);
		$("#divisao").html(divisao);
		$("#tipo_pagamento").html(tipo_pagamento);
	}

	function autorizar(id_pre_matricula, id_autorizacao, motivo) {
		$("#motivo_titulo").html(motivo);
		$("#motivo").html(motivo);
		$("#id_pre_matricula").val(id_pre_matricula);
		$("#id_autorizacao").val(id_autorizacao);
	}

	function mostrar_autorizacao(nome, data_autorizacao, obs) {
		$("#nome").val(nome);
		$("#data_autorizacao").val(data_autorizacao);
		$("#motivo").val(obs);
	}

	function matricular(id_pessoa) {
		$("#id_pessoa").val(id_pessoa);
	}

	function gerar_avaliacao(id_pessoa) {
		$.ajax({
				url: "modulos/reoferta/inscricao/avaliacao_permissao.php",
				type: 'post',
				data: {
					id_pessoa: id_pessoa,
					id_reoferta: '<?php echo $id_reoferta ?>'
				},
				beforeSend: function() {
					$("#resultado").html("ENVIANDO...");
				}
			})
			.done(function(msg) {
				//avaliacaoOK();
			})
			.fail(function(jqXHR, textStatus, msg) {
				alert(msg);
			});
	}

	function avaliacaoOK() {

		$("#obs").val('');
		$("#id_pre_matricula").val('');
		$("#id_autorizacao").val('');
		$("#modal .close").click();

		Swal.fire({
			type: "success",
			title: "Avaliação gerada com sucesso!",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				document.location.reload(true)
			}
		});
	}

	function baixaMatriculaOK() {

		$("#obs").val('');
		$("#id_pre_matricula").val('');
		$("#id_autorizacao").val('');
		$("#modal_matricula .close").click();

		Swal.fire({
			type: "success",
			title: "Pagamento baixado com sucesso!",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				document.location.reload(true)
			}
		});
	}


	function baixaPrematriculaOK() {

		$("#obs").val('');
		$("#id_pre_matricula").val('');
		$("#id_autorizacao").val('');
		$("#modal_pre .close").click();

		Swal.fire({
			type: "success",
			title: "Pagamento baixado com sucesso!",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				document.location.reload(true)
			}
		});
	}

	function vencimentoPrematriculaOK() {

		$("#obs").val('');
		$("#id_pre_matricula").val('');
		$("#id_autorizacao").val('');
		$("#modal_pre .close").click();

		Swal.fire({
			type: "success",
			title: "Vencimento alterado com sucesso!",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				//document.location.reload(true);
			}
		});
	}

	function vencimentoMatriculaOK() {

		$("#obs").val('');
		$("#id_matricula").val('');
		$("#id_autorizacao").val('');
		$("#modal_matricula .close").click();

		Swal.fire({
			type: "success",
			title: "Vencimento alterado com sucesso!",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				//document.location.reload(true);
			}
		});
	}


	function matriculaOK() {

		$("#obs").val('');
		$("#id_pre_matricula").val('');
		$("#id_autorizacao").val('');
		$("#modal_matricular .close").click();

		Swal.fire({
			type: "success",
			title: "Acadêmico matriculado com sucesso!",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				document.location.reload(true)
			}
		});
	}


	function autorizacaoOK() {


		$("#obs").val('');
		$("#id_pre_matricula").val('');
		$("#id_autorizacao").val('');
		$("#modal .close").click();

		Swal.fire({
			type: "success",
			title: "Autorização efetuada com sucesso!",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {

				document.location.reload(true)
			}
		});
	}

	//MENSAGEM DE FALHA NO CADASTRO
	function autorizacaoFalha() {
		$("#obs").val('');
		$("#id_pre_matricula").val('');
		$("#id_autorizacao").val('');
		$("#modal .close").click();

		Swal.fire({
			type: "error",
			title: "Falha ao autorizar",
			showConfirmButton: false,
			timer: 1500
		});
	}

	$(document).ready(function() {
		$(":input").inputmask();

		$('#dt-basic-example').dataTable({
			responsive: true,
			paging: false,
			pageLength: 15,
			autoWidth: true,
			stateSave: true,
			order: [
				[0, 'asc']
			],
			"columnDefs": [{
					"orderable": false,
					"targets": 3
				},
				{
					"orderable": false,
					"targets": 4
				},
				{
					"orderable": false,
					"targets": 5
				},
				{
					"orderable": false,
					"targets": 6
				},
				{
					"orderable": false,
					"targets": 7
				},
				{
					"orderable": false,
					"targets": 8
				}
			]
			// ,
			// rowGroup:
			// {
			//     dataSrc: 1
			// }
		});


		$(".js-sweetalert2-example-13").on("click", function() {
			Swal.fire({
				title: "Observação",
				input: "text",
				inputAttributes: {
					autocapitalize: "off"
				},
				showCancelButton: true,
				confirmButtonText: "Autorizar",
				showLoaderOnConfirm: true,
				preConfirm: function preConfirm(login) {
					return fetch("//api.github.com/users/".concat(login))
						.then(function(response) {
							if (!response.ok) {
								throw new Error(response.statusText);
							}

							return response.json();
						})
						.catch(function(error) {
							Swal.showValidationMessage("Request failed: ".concat(error));
						});
				},
				allowOutsideClick: function allowOutsideClick() {
					return !Swal.isLoading();
				}
			}).then(function(result) {
				if (result.value) {
					Swal.fire({
						title: "".concat(result.value.login, "'s avatar"),
						imageUrl: result.value.avatar_url
					});
				}
			});
		}); //Dynamic queue example

	});

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

<script>
	$(document).ready(function() {


		$(function() {
			$('.select2').select2();

			$(".js-consultar-usuario").select2({
				ajax: {
					url: "modulos/_core/buscar_usuario.php",
					dataType: 'json',
					delay: 250,
					data: function(params) {
						return {
							q: params.term, // search term
							page: params.page
						};
					},
					processResults: function(data, params) {
						// parse the results into the format expected by Select2
						// since we are using custom formatting functions we do not need to
						// alter the remote JSON data, except to indicate that infinite
						// scrolling can be used
						console.log(data);
						params.page = params.page || 1;

						return {
							results: data.items,
							pagination: {
								more: (params.page * 30) < data.total_count
							}
						};
					},
					cache: true
				},
				placeholder: 'Search for a repository',
				escapeMarkup: function(markup) {
					return markup;
				}, // let our custom formatter work
				minimumInputLength: 3,
				templateResult: formatoUsuario,
				templateSelection: formatoTextoUsuario
			});

			function formatRepo(repo) {
				if (repo.loading) {
					return repo.text;
				}

				var markup = "<div class='select2-result-repository clearfix d-flex'>" +
					"<div class='select2-result-repository__avatar mr-2'><img src='https://www2.fag.edu.br/coopex3/img/demo/avatars/avatar-" + repo.sexo + ".png' class='width-2 height-2 mt-1 rounded-circle' /></div>" +
					"<div class='select2-result-repository__meta'>" +
					"<div><span class='select2-result-repository__title fs-lg fw-500'>" + repo.nome + "</span>" + " (" + repo.usuario + ")</div>";


				markup += "<div class='select2-result-repository__description fs-xs opacity-80 mb-1'>" + repo.tipo_descricao + "</div>";

				markup += "</div></div>";

				return markup;
			}

			function formatRepoSelection(repo) {
				return repo.nome || '';
			}

		});
	});

	$('#select2-ajax').on('select2:select', function(e) {
		var data = e.params.data;
		console.log(data.id);

		$.ajax({
				url: "modulos/reoferta/inscricao/inscricao_dados_pre.php",
				type: 'post',
				data: {
					id_pessoa: data.id,
					id_reoferta: '<?php echo $id_reoferta ?>',
					data_vencimento: '<?php echo $reoferta->pre_inscricao_data_final ?>'
				},
				beforeSend: function() {
					$("#resultado").html("ENVIANDO...");
				}
			})
			.done(function(msg) {
				document.location.reload(true)
			})
			.fail(function(jqXHR, textStatus, msg) {
				alert(msg);
			});

	});
</script>