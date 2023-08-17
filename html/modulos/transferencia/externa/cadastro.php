<?php
$id_menu = 86;
$chave	 = "id_transferencia_externa";

if (isset($_GET['id'])) {
	$$chave = $_GET['id'];
	$sql = "SELECT
				*
			FROM
				transferencia.transferencia_externa
			INNER JOIN coopex.departamento USING (id_departamento)
			INNER JOIN transferencia.egresso using (id_egresso)	
			INNER JOIN transferencia.tipo_ingresso using(id_ingresso)
			WHERE id_transferencia_externa = " . $_GET['id'];

	$res = $coopex->query($sql);
	$row1 = $res->fetch(PDO::FETCH_OBJ);
} else {
	$$chave = 0;
}

?>
<script src="js/core.js"></script>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Ficha Transferência</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu ?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-calendar'></i> Ficha Transferência
			<small>
				Cadastro de Ficha de Transferência
			</small>
		</h1>
	</div>
	<iframe class="d-none" name="dados" src=""></iframe>
	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/transferencia/externa/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave ?>" value="<?php echo $$chave ? $$chave : 0 ?>">
		<div class="row">
			<div class="col-xl-12">
				<div id="panel-2" class="panel">
					<div class="panel-hdr">
						<h2>
							Ficha Transferência
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content">
							<div class="form-row">
								<div class="col-md-6 mb-6">
									<label class="form-label" for="validationCustom03">Acadêmico <span class="text-danger">*</span></label>
									<input id="academico" name="academico" class="form-control" type="text" value="<?php echo isset($row1->academico) ? $row1->academico : "" ?>" style="text-align: right; font-weight: bold;">
									<div class="invalid-feedback">
										Campo obrigatório, preencha o período, Ex.: <?php echo date("Y") ?>/2
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-6 mb-6">
									<label class="form-label" for="validationCustom03">Curso <span class="text-danger">*</span></label>
									<?php

									if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][5]) || isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][6])) {
										$where = " WHERE graduacao = 1 ";
									} else {
										$where = " WHERE graduacao = 1 AND id_pessoa = " . $_SESSION['coopex']['usuario']['id_pessoa'];
									}

									$sql = "SELECT
												id_departamento,
												departamento 
											FROM
												coopex.departamento

											GROUP BY
												id_departamento 
											ORDER BY
												departamento";

									$curso = $coopex->query($sql);
									?>
									<select id="id_departamento" name="id_departamento" class="select2 form-control" required="">
										<option value="">Selecione o Curso</option>
										<?php
										while ($row = $curso->fetch(PDO::FETCH_OBJ)) {
											$selecionado = '';
											if ($row1->id_departamento == $row->id_departamento) {
												$selecionado = 'selected=""';
											}
										?>
											<option <?php echo isset($row1->id_departamento) ? $selecionado : "" ?> value="<?php echo $row->id_departamento ?>"><?php echo utf8_encode($row->departamento) ?></option>
										<?php
										}
										?>
									</select>
									<div class="invalid-feedback">
										Selecione o curso
									</div>
								</div>
							</div>

							<div class="form-row">
								<div class="col-md-6 mb-6">
									<label class="form-label" for="validationCustom03">Instituição de Origem <span class="text-danger">*</span></label>
									<input id="instituicao_origem" name="instituicao_origem" class="form-control" type="text" value="<?php echo isset($row1->instituicao_origem) ? $row1->instituicao_origem : "" ?>" style="text-align: right; font-weight: bold;">

									<div class="invalid-feedback">
										Campo obrigatório, preencha o período, Ex.: <?php echo date("Y") ?>/2
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-6 mb-6">
									<label class="form-label" for="validationCustom03">É aluno <span class="text-danger">*</span></label>
									<?php

									if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][5]) || isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][6])) {
										$where = " WHERE graduacao = 1 ";
									} else {
										$where = " WHERE graduacao = 1 AND id_pessoa = " . $_SESSION['coopex']['usuario']['id_pessoa'];
									}

									$sql = "SELECT
												*
											FROM
												transferencia.transferencia_externa
											INNER JOIN transferencia.egresso using (id_egresso)
											GROUP BY
											tipo_egresso
											ORDER BY
											tipo_egresso";

									$egresso = $coopex->query($sql);
									?>
									<select id="id_egresso" name="id_egresso" class="select2 form-control" required="">
										<option value="">Selecione o tipo</option>
										<?php
										while ($row = $egresso->fetch(PDO::FETCH_OBJ)) {
											$selecionado = '';
											if ($row1->tipo_egresso == $row1->tipo_egresso) {
												$selecionado = 'selected=""';
											}
										?>
											<option <?php echo isset($row1->tipo_egresso) ? $selecionado : "" ?> value="<?php echo $row->id_egresso ?>"><?php echo utf8_encode($row->tipo_egresso) ?></option>
										<?php
										}
										?>
									</select>
									<div class="invalid-feedback">
										Selecione o curso
									</div>
								</div>
							</div>

							<div class="form-row">
								<div class="col-md-6 mb-6">
									<label class="form-label" for="validationCustom03">Solicita ingresso na FAG através de <span class="text-danger">*</span></label>
									<?php

									if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][5]) || isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][6])) {
										$where = " WHERE graduacao = 1 ";
									} else {
										$where = " WHERE graduacao = 1 AND id_pessoa = " . $_SESSION['coopex']['usuario']['id_pessoa'];
									}

									$sql = "SELECT
												*
											FROM
												transferencia.transferencia_externa
											INNER JOIN transferencia.ingresso using(id_ingresso)
											GROUP BY
											tipo_ingresso
											ORDER BY
											tipo_ingresso";

									$ingresso = $coopex->query($sql);
									?>
									<select id="id_ingresso" name="id_ingresso" class="select2 form-control" required="">
										<option value="">Selecione o tipo</option>
										<?php
										while ($row = $ingresso->fetch(PDO::FETCH_OBJ)) {
											$selecionado = '';
											if ($row1->tipo_ingresso == $row1->tipo_ingresso) {
												$selecionado = 'selected=""';
											}
										?>
											<option <?php echo isset($row1->tipo_ingresso) ? $selecionado : "" ?> value="<?php echo $row->id_ingresso ?>"><?php echo utf8_encode($row->tipo_ingresso) ?></option>
										<?php
										}
										?>
									</select>
									<div class="invalid-feedback">
										Campo obrigatório, preencha o período, Ex.: <?php echo date("Y") ?>/2
									</div>
								</div>
							</div>

							<div class="form-row">
								<div class="col-md-6 mb-6">
									<div class="custom-control custom-switch">
										<?php

										if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][5]) || isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][6])) {
											$where = " WHERE graduacao = 1 ";
										} else {
											$where = " WHERE graduacao = 1 AND id_pessoa = " . $_SESSION['coopex']['usuario']['id_pessoa'];
										}

										$sql = "SELECT
												*
											FROM
												transferencia.transferencia_externa
											INNER JOIN transferencia.matriculado using (id_matriculado)
											GROUP BY
											tipo_matriculado
											ORDER BY
											tipo_matriculado";

										$matriculado = $coopex->query($sql);
										?>
										<input type="hidden" id="id_matriculado" name="id_matriculado" value="<?php echo isset($matriculado->id_matriculado) && $matriculado->id_matriculado ? "0" : "1" ?>">
										<input onchange="$('#select_matriculado').val(this.checked)" <?php echo isset($row1->id_matriculado) && $row1->id_matriculado ? "checked" : "" ?> contenteditable="" type="checkbox" class="custom-control-input is-invalid" id="select_matriculado">
										<label class="custom-control-label" for="select_matriculado">Está matriculado no curso FAG/DB/IAG</label>
									</div>
								</div>
							</div>


							<label class="form-label" for="validationCustom03">Histórico escolar contém informações<span class="text-danger">*</span></label>
							<div class="form-row">
								<div class="col-md-6 mb-6">
									<div class="custom-control custom-switch">
										<input type="hidden" id="ingresso_superior" name="ingresso_superior" value="<?php echo isset($row1->ingresso_superior) && $row1->ingresso_superior ? "0" : "1" ?>">
										<input onchange="$('#select_ingresso_superior').val(this.checked)" <?php echo isset($row1->ingresso_superior) && $row1->ingresso_superior ? "checked" : "" ?> contenteditable="" type="checkbox" class="custom-control-input is-invalid" id="select_ingresso_superior">
										<label class="custom-control-label" for="select_ingresso_superior">Do ingresso no ensino superior</label>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-6 mb-6">
									<div class="custom-control custom-switch">
										<input type="hidden" id="enade" name="enade" value="<?php echo isset($row1->enade) && $row1->enade ? "0" : "1" ?>">
										<input onchange="$('#select_enade').val(this.checked)" <?php echo isset($row1->enade) && $row1->enade ? "checked" : "" ?> contenteditable="" type="checkbox" class="custom-control-input is-invalid" id="select_enade">
										<label class="custom-control-label" for="select_enade">Do enade</label>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-6 mb-6">
									<div class="custom-control custom-switch">
										<input type="hidden" id="manutencao" name="manutencao" value="<?php echo isset($row1->manutencao) && $row1->manutencao ? "0" : "1" ?>">
										<input onchange="$('#select_manutencao').val(this.checked)" <?php echo isset($row1->manutencao) && $row1->manutencao ? "checked" : "" ?> contenteditable="" type="checkbox" class="custom-control-input is-invalid" id="select_manutencao">
										<label class="custom-control-label" for="select_manutencao">Da manutenção do vínculo com a IES de origem</label>
									</div>
								</div>
							</div>
							<label class="form-label" for="validationCustom03">Os planos de ensino estão em anexo<span class="text-danger">*</span></label>

							<div class="form-row">
								<div class="col-md-6 mb-6">
									<div class="custom-control custom-switch">
										<input type="hidden" id="id_plano" name="id_plano" value="<?php echo isset($row1->id_plano) && $row1->id_plano ? "0" : "1" ?>">
										<input onchange="$('#select_planos').val(this.checked)" <?php echo isset($row1->id_plano) && $row1->id_plano ? "checked" : "" ?> contenteditable="" type="checkbox" class="custom-control-input is-invalid" id="select_planos">
										<label class="custom-control-label" for="select_planos">Planos em anexo</label>
									</div>
								</div>
							</div>

							<div class="form-row">
								<div class="col-md-6 mb-6">
									<label class="form-label" for="validationCustom03">Parecer final: <span class="text-danger">*</span></label>
									<textarea id="parecer_final" class="form-control" name="parecer_final"><?php echo isset($row1->parecer_final) ? $row1->parecer_final : "" ?></textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
						<button class="btn btn-primary ml-auto" type="submit"><?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar" ?></button>
					</div>
				</div>
			</div>
		</div>
	</form>
</main>

<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<script src="js/formplugins/select2/select2.bundle.js"></script>

<script>
	function cadastroOK(operacao) {
		var msg = operacao == 1 ? "Registro cadastrado com sucesso" : "Registro alterado com sucesso";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				location.href("https://coopex.fag.edu.br/colegio/ficha_avaliacao");
			}
		});
	}

	function cadastroFalha(operacao) {
		var msg = operacao == 1 ? "Falha ao cadastrar dados" : "Falha ao alterar dados";
		Swal.fire({
			type: "error",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				window.history.back();
			}
		});
	}

	// Example starter JavaScript for disabling form submissions if there are invalid fields
	(function() {

		$('.select2').select2();
		'use strict';
		window.addEventListener('load', function() {
			// Fetch all the forms we want to apply custom Bootstrap validation styles to
			var forms = document.getElementsByClassName('needs-validation');
			// Loop over them and prevent submission
			var validation = Array.prototype.filter.call(forms, function(form) {
				form.addEventListener('submit', function(event) {
					if (form.checkValidity() === false) {
						event.preventDefault();
						event.stopPropagation();
					}
					form.classList.add('was-validated');
				}, false);
			});
		}, false);
	})();
</script>