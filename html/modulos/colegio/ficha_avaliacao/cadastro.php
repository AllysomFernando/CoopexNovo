<?php
$id_menu = 82;
$chave	 = "id_ficha_avaliacao";

if (isset($_GET['id'])) {
	$$chave = $_GET['id'];
	$sql = "SELECT
				*
			FROM
				colegio.ficha_avaliacao
			INNER JOIN coopex.pessoa USING (id_pessoa)	
			WHERE id_ficha_avaliacao = " . $_GET['id'];

	$res = $coopex->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);
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
		<li class="breadcrumb-item"><a href="javascript:void(0);">Ficha Avaliação</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu ?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-calendar'></i> Ficha Avaliação
			<small>
				Cadastro de Ficha Avaliação
			</small>
		</h1>
	</div>
	<iframe class="d-none" name="dados" src=""></iframe>
	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/colegio/ficha_avaliacao/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave ?>" value="<?php echo $$chave ? $$chave : 0 ?>">
		<div class="row">
			<div class="col-xl-12">
				<div id="panel-2" class="panel">
					<div class="panel-hdr">
						<h2>
							Ficha Avaliação
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
									<?
									if (isset($_GET['id'])) {
									?>
										<input class="form-control" type="text" value="<?= $row->nome ?>">
										<input name="id_pessoa" class="form-control" type="hidden" value="<?= $row->id_pessoa ?>">
									<?
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

									<?
									}
									?>
								</div>
							</div>
							<div class="panel-content">
								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Altura <span class="text-danger">*</span></label>
										<input id="estatura" title="1" autocomplete="off" name="estatura" class="form-control decimal" type="text" value="<?php echo isset($row->estatura) ? $row->estatura : "" ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=".">
										<div class="invalid-feedback">
											Campo obrigatório, preencha o período, Ex.: <?php echo date("Y") ?>/2
										</div>
									</div>

									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Peso <span class="text-danger">*</span></label>
										<input id="massa_corporal" title="1" autocomplete="off" name="massa_corporal" class="form-control decimal" type="text" value="<?php echo isset($row->massa_corporal) ? $row->massa_corporal : "" ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=",">

										<div class="invalid-feedback">
											Campo obrigatório, preencha o período, Ex.: <?php echo date("Y") ?>/2
										</div>
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Envergadura <span class="text-danger">*</span></label>
										<input id="envergadura" title="1" autocomplete="off" name="envergadura" class="form-control decimal" type="text" value="<?php echo isset($row->envergadura) ? $row->envergadura : "" ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=",">

										<div class="invalid-feedback">
											Campo obrigatório, preencha o período, Ex.: <?php echo date("Y") ?>/2
										</div>
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Perímetro Cintura <span class="text-danger">*</span></label>
										<input id="massa_corporal" title="1" autocomplete="off" name="perimetro_cintura" class="form-control decimal" type="text" value="<?php echo isset($row->perimetro_cintura) ? $row->perimetro_cintura : "" ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=",">

										<div class="invalid-feedback">
											Campo obrigatório, preencha o período, Ex.: <?php echo date("Y") ?>/2
										</div>
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Sentar-e-Alcançar <span class="text-danger">*</span></label>
										<input id="sentar_alcacar" title="1" autocomplete="off" name="sentar_alcacar" class="form-control decimal" type="text" value="<?php echo isset($row->sentar_alcacar) ? $row->sentar_alcacar : "" ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=",">

										<div class="invalid-feedback">
											Campo obrigatório, preencha o período, Ex.: <?php echo date("Y") ?>/2
										</div>
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Abdominal <span class="text-danger">*</span></label>
										<input id="abdominal" title="1" autocomplete="off" name="abdominal" class="form-control decimal" type="text" value="<?php echo isset($row->abdominal) ? $row->abdominal : "" ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=",">

										<div class="invalid-feedback">
											Campo obrigatório, preencha o período, Ex.: <?php echo date("Y") ?>/2
										</div>
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Salto Horizontal <span class="text-danger">*</span></label>
										<input id="salto_distancia" title="1" autocomplete="off" name="salto_distancia" class="form-control decimal" type="text" value="<?php echo isset($row->salto_distancia) ? $row->salto_distancia : "" ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=",">

										<div class="invalid-feedback">
											Campo obrigatório, preencha o período, Ex.: <?php echo date("Y") ?>/2
										</div>
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Corrida de 20 metros <span class="text-danger">*</span></label>
										<input id="corrida_metros" title="1" autocomplete="off" name="corrida_metros" class="form-control decimal" type="text" value="<?php echo isset($row->corrida_metros) ? $row->corrida_metros : "" ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=",">

										<div class="invalid-feedback">
											Campo obrigatório, preencha o período, Ex.: <?php echo date("Y") ?>/2
										</div>
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">6 Minutos <span class="text-danger">*</span></label>
										<input id="seis_minutos" title="1" autocomplete="off" name="seis_minutos" class="form-control decimal" type="text" value="<?php echo isset($row->seis_minutos) ? $row->seis_minutos : "" ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=",">

										<div class="invalid-feedback">
											Campo obrigatório, preencha o período, Ex.: <?php echo date("Y") ?>/2
										</div>
									</div>

									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Arremesso Medicineball <span class="text-danger">*</span></label>
										<input id="arremesso_medicineball" title="1" autocomplete="off" name="arremesso_medicineball" class="form-control decimal" type="text" value="<?php echo isset($row->arremesso_medicineball) ? $row->arremesso_medicineball : "" ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=",">

										<div class="invalid-feedback">
											Campo obrigatório, preencha o período, Ex.: <?php echo date("Y") ?>/2
										</div>
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Quadradado <span class="text-danger">*</span></label>
										<input id="quadrado" title="1" autocomplete="off" name="quadrado" class="form-control decimal" type="text" value="<?php echo isset($row->quadrado) ? $row->quadrado : "" ?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=",">

										<div class="invalid-feedback">
											Campo obrigatório, preencha o período, Ex.: <?php echo date("Y") ?>/2
										</div>
									</div>

									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Observação: <span class="text-danger">*</span></label>
										<textarea id="observacao" class="form-control" name="observacao"><?php echo isset($row->observacao) ? $row->observacao : "" ?></textarea>
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


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js"></script>
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

		$(function() {
			$(".decimal").maskMoney({
				decimal: '.'
			});
		});


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
		$("#titulos_em_aberto_resultado").load("modulos/colegio/ficha_avaliacao/ajax/buscar_usuario.php?id_usuario=" + data.id);

	});
</script>