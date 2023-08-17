<?php
$id_menu = 83;
$chave = "id_ficha_psicomotor";

if (isset($_GET['id'])) {
	$$chave = $_GET['id'];
	$sql = "SELECT
				*
			FROM
				colegio.ficha_psicomotor
			INNER JOIN coopex.pessoa USING (id_pessoa)	
			WHERE id_ficha_psicomotor = " . $_GET['id'];

	$res = $coopex->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);
} else {
	$$chave = 0;
}

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/core.js"></script>

<main id="js-page-content" role="main" class="page-content">

	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Ficha Psicomotor</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu ?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-calendar'></i> Ficha Psicomotor
			<small>
				Cadastro de Ficha Psicomotor
			</small>
		</h1>
	</div>
	<iframe class="d-none" name="dados" src=""></iframe>

	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/colegio/psicomotor/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave ?>" value="<?php echo $$chave ? $$chave : 0 ?>">
		<div class="row">
			<div class="col-xl-12">
				<div id="panel-2" class="panel">
					<div class="panel-hdr">
						<h2>
							Ficha Psicomotor
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content p-0">
							<div class="panel-container show">
								<div class="panel-content">
									<?php
									if (isset($_GET['id'])) {
									?>
										<input class="form-control" type="text" value="<?= $row->nome ?>">
										<input name="id_pessoa" class="form-control" type="hidden" value="<?= $row->id_pessoa ?>">
									<?php
									} else {
									?>
										<div class="form-group">
											<label class="form-label" for="select2-ajax">
												Selecione o usuário do Sagres
											</label>
											<select name="id_pessoa" onChange="" data-placeholder="Nome do aluno..." class="js-consultar-usuario form-control" id="select2-ajax"></select>
										</div>

										<div id="titulos_em_aberto_resultado">
										</div>

									<?php
									}
									?>
								</div>
							</div>
							<div class="panel-content">
								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Altura (Metros)<span class="text-danger"> * </span></label>
										<input id="altura" title="Altura" autocomplete="off" name="altura" class="form-control decimal" type="text" value="<?php echo isset($row->altura) ? $row->altura : "" ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=",">
										<div class="invalid-feedback">
											Campo obrigatório, preencha o período, Ex.: <?php echo date("Y") ?>/2
										</div>
									</div>

									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Peso (Kg)<span class="text-danger"> * </span></label>
										<input id="peso" title="Peso" autocomplete="off" name="peso" class="form-control decimal" type="text" value="<?php echo isset($row->peso) ? $row->peso : "" ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=",">

										<div class="invalid-feedback">
											Campo obrigatório, preencha o período, Ex.: <?php echo date("Y") ?>/2
										</div>
									</div>
								</div>

								<div class="form-row">
									<div class="col-md-3 mb-3">
										<div class="frame-wrap mb-0">
											<label class="form-label" for="validationCustom03">Coordenação Viso-Manual<span class="text-danger"> * </span></label>
											<br>
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->coord_viso_manual) && strtoupper($row->coord_viso_manual) == "M") ? "active" : "" ?>">
													<input type="radio" name="coord_viso_manual" id="option1-cvm" <?php echo (isset($row->coord_viso_manual) && strtoupper($row->coord_viso_manual) == "M") ? "checked" : "" ?> value="M">
													<img src="../../../img/favicon/icon-psicomotor-media.svg" alt="" width="50px" />
													<br>
													Normal
												</label>
												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->coord_viso_manual) && strtoupper($row->coord_viso_manual) == "F") ? "active" : "" ?>">
													<input type="radio" name="coord_viso_manual" id="option2-cvm" <?php echo (isset($row->coord_viso_manual) && strtoupper($row->coord_viso_manual) == "F") ? "checked" : "" ?> value="F">
													<img src="../../../img/favicon/icon-psicomotor-facil.svg" alt="" width="50px" />
													<br>
													Fácil
												</label>
												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->coord_viso_manual) && strtoupper($row->coord_viso_manual) == "D") ? "active" : "" ?>">
													<input type="radio" name="coord_viso_manual" id="option3-cvm" <?php echo (isset($row->coord_viso_manual) && strtoupper($row->coord_viso_manual) == "D") ? "checked" : "" ?> value="D">
													<img src="../../../img/favicon/icon-psicomotor-dificil.svg" alt="" width="50px" />
													<br>
													Difícil
												</label>
											</div>
										</div>
									</div>

									<div class="col-md-3 mb-3">
										<div class="frame-wrap mb-0">
											<label class="form-label" for="validationCustom03">Controle Postural<span class="text-danger"> * </span></label>
											<br>
											<div class="btn-group btn-group-toggle" data-toggle="buttons">

												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->controle_postural) && strtoupper($row->controle_postural) == "M") ? "active" : "" ?>">
													<input type="radio" name="controle_postural" id="option1-cp" <?php echo (isset($row->controle_postural) && strtoupper($row->controle_postural) == "M") ? "checked" : "" ?> value="M">
													<img src="../../../img/favicon/icon-psicomotor-media.svg" alt="" width="50px" />
													<br>
													Normal
												</label>

												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->controle_postural) && strtoupper($row->controle_postural) == "F") ? "active" : "" ?>">
													<input type="radio" name="controle_postural" id="option2-cp" <?php echo (isset($row->controle_postural) && strtoupper($row->controle_postural) == "F") ? "checked" : "" ?> value="F">
													<img src="../../../img/favicon/icon-psicomotor-facil.svg" alt="" width="50px" />
													<br>
													Fácil
												</label>

												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->controle_postural) && strtoupper($row->controle_postural) == "D") ? "active" : "" ?>">
													<input type="radio" name="controle_postural" id="option3-cp" <?php echo (isset($row->controle_postural) && strtoupper($row->controle_postural) == "D") ? "checked" : "" ?> value="D">
													<img src="../../../img/favicon/icon-psicomotor-dificil.svg" alt="" width="50px" />
													<br>
													Difícil
												</label>

											</div>
										</div>
									</div>

									<div class="col-md-3 mb-3">
										<div class="frame-wrap mb-0">
											<label class="form-label" for="validationCustom03">Organização Perceptiva<span class="text-danger"> * </span></label>
											<br>
											<div class="btn-group btn-group-toggle" data-toggle="buttons">

												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->organizacao_perceptiva) && strtoupper($row->organizacao_perceptiva) == "M") ? "active" : "" ?>">
													<input type="radio" name="organizacao_perceptiva" id="option1-op" <?php echo (isset($row->organizacao_perceptiva) && strtoupper($row->organizacao_perceptiva) == "M") ? "checked" : "" ?> value="M">
													<img src="../../../img/favicon/icon-psicomotor-media.svg" alt="" width="50px" />
													<br>
													Normal
												</label>

												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->organizacao_perceptiva) && strtoupper($row->organizacao_perceptiva) == "F") ? "active" : "" ?> ">
													<input type="radio" name="organizacao_perceptiva" id="option2-op" <?php echo (isset($row->organizacao_perceptiva) && strtoupper($row->organizacao_perceptiva) == "F") ? "checked" : "" ?> value="F">
													<img src="../../../img/favicon/icon-psicomotor-facil.svg" alt="" width="50px" />
													<br>
													Fácil
												</label>

												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->organizacao_perceptiva) && strtoupper($row->organizacao_perceptiva) == "D") ? "active" : "" ?>">
													<input type="radio" name="organizacao_perceptiva" id="option3-op" <?php echo (isset($row->organizacao_perceptiva) && strtoupper($row->organizacao_perceptiva) == "D") ? "checked" : "" ?> value="D">
													<img src="../../../img/favicon/icon-psicomotor-dificil.svg" alt="" width="50px" />
													<br>
													Difícil
												</label>

											</div>
										</div>
									</div>

									<div class="col-md-3 mb-3">
										<div class="frame-wrap mb-0">
											<label class="form-label" for="validationCustom03">Coordenação Dinâmica<span class="text-danger"> * </span></label>
											<br>
											<div class="btn-group btn-group-toggle" data-toggle="buttons">

												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->coord_dinamica) && strtoupper($row->coord_dinamica) == "M") ? "active" : "" ?>">
													<input type="radio" name="coord_dinamica" id="option1-cd" <?php echo (isset($row->coord_dinamica) && strtoupper($row->coord_dinamica) == "M") ? "checked" : "" ?> value="M">
													<img src="../../../img/favicon/icon-psicomotor-media.svg" alt="" width="50px" />
													<br>
													Normal
												</label>

												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->coord_dinamica) && strtoupper($row->coord_dinamica) == "F") ? "active" : "" ?>">
													<input type="radio" name="coord_dinamica" id="option2-cd" <?php echo (isset($row->coord_dinamica) && strtoupper($row->coord_dinamica) == "F") ? "checked" : "" ?> value="F">
													<img src="../../../img/favicon/icon-psicomotor-facil.svg" alt="" width="50px" />
													<br>
													Fácil
												</label>

												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->coord_dinamica) && strtoupper($row->coord_dinamica) == "D") ? "active" : "" ?>">
													<input type="radio" name="coord_dinamica" id="option3-cd" <?php echo (isset($row->coord_dinamica) && strtoupper($row->coord_dinamica) == "D") ? "checked" : "" ?> value="D">
													<img src="../../../img/favicon/icon-psicomotor-dificil.svg" alt="" width="50px" />
													<br>
													Difícil
												</label>

											</div>
										</div>
									</div>

									<div class="col-md-3 mb-3">
										<div class="frame-wrap mb-0">
											<label class="form-label" for="validationCustom03">Controle do Próprio Corpo<span class="text-danger"> * </span></label>
											<br>
											<div class="btn-group btn-group-toggle" data-toggle="buttons">

												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->controle_proprio_corpo) && strtoupper($row->controle_proprio_corpo) == "M") ? "active" : "" ?>">
													<input type="radio" name="controle_proprio_corpo" id="option1-cpc" <?php echo (isset($row->controle_proprio_corpo) && strtoupper($row->controle_proprio_corpo) == "M") ? "checked" : "" ?> value="M">
													<img src="../../../img/favicon/icon-psicomotor-media.svg" alt="" width="50px" />
													<br>
													Normal
												</label>

												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->controle_proprio_corpo) && strtoupper($row->controle_proprio_corpo) == "F") ? "active" : "" ?>">
													<input type="radio" name="controle_proprio_corpo" id="option2-cpc" <?php echo (isset($row->controle_proprio_corpo) && strtoupper($row->controle_proprio_corpo) == "F") ? "checked" : "" ?> value="F">
													<img src="../../../img/favicon/icon-psicomotor-facil.svg" alt="" width="50px" />
													<br>
													Fácil
												</label>

												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->controle_proprio_corpo) && strtoupper($row->controle_proprio_corpo) == "D") ? "active" : "" ?>">
													<input type="radio" name="controle_proprio_corpo" id="option3-cpc" <?php echo (isset($row->controle_proprio_corpo) && strtoupper($row->controle_proprio_corpo) == "D") ? "checked" : "" ?> value="D">
													<img src="../../../img/favicon/icon-psicomotor-dificil.svg" alt="" width="50px" />
													<br>
													Difícil
												</label>

											</div>
										</div>
									</div>

									<div class="col-md-3 mb-3">
										<div class="frame-wrap mb-0">
											<label class="form-label" for="validationCustom03">Linguagem<span class="text-danger"> * </span></label>
											<br>
											<div class="btn-group btn-group-toggle" data-toggle="buttons">

												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->linguagem) && strtoupper($row->linguagem) == "M") ? "active" : "" ?>">
													<input type="radio" name="linguagem" id="option1-l" <?php echo (isset($row->linguagem) && strtoupper($row->linguagem) == "M") ? "checked" : "" ?> value="M">
													<img src="../../../img/favicon/icon-psicomotor-media.svg" alt="" width="50px" />
													<br>
													Normal
												</label>

												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->linguagem) && strtoupper($row->linguagem) == "F") ? "active" : "" ?>">
													<input type="radio" name="linguagem" id="option2-l" <?php echo (isset($row->linguagem) && strtoupper($row->linguagem) == "F") ? "checked" : "" ?> value="F">
													<img src="../../../img/favicon/icon-psicomotor-facil.svg" alt="" width="50px" />
													<br>
													Fácil
												</label>

												<label class="btn btn-info waves-effect waves-themed <?php echo (isset($row->linguagem) && strtoupper($row->linguagem) == "D") ? "active" : "" ?>">
													<input type="radio" name="linguagem" id="option3-l" <?php echo (isset($row->linguagem) && strtoupper($row->linguagem) == "D") ? "checked" : "" ?> value="D">
													<img src="../../../img/favicon/icon-psicomotor-dificil.svg" alt="" width="50px" />
													<br>
													Difícil
												</label>

											</div>
										</div>
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
		</div>
	</form>

</main>

<script src="js/jquery.maskMoney.min.js?a" type="text/javascript"></script>
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
				window.history.back();
				//document.location.reload(true)
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


	$(document).ready(function() {

		$('.decimal').maskMoney();


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
		$("#titulos_em_aberto_resultado").load("modulos/colegio/psicomotor/ajax/buscar_usuario.php?id_usuario=" + data.id);

	});
</script>