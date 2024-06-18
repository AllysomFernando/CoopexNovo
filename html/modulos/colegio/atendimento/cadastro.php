<?php
$id_menu = 23;
$chave	 = "id_protocolo";

if (isset($_GET['id'])) {
	$$chave = $_GET['id'];

	$id_protocolo = $_GET['id'];

	$sql = "SELECT
				*,
				DATE_FORMAT( data_cadastro, '%d/%m/%Y' ) AS data_cadastro,
				DATE_FORMAT( DATE_ADD(data_cadastro, INTERVAL 24 HOUR), '%Y-%m-%dT%H:%i:%s' ) AS data_contagem,
				DATE_FORMAT( data_cadastro, '%d/%m - %H:%i' ) AS data_curta,
				TIMESTAMPDIFF(
					HOUR,
					data_cadastro,
				NOW()) AS horas_passadas,
				DATE_FORMAT( DATE_ADD(data_cadastro, INTERVAL 24 HOUR), '%d/%m - %H:%i' ) AS data_final
			FROM
				colegio.atd_protocolo a
				INNER JOIN colegio.atd_status USING ( id_status )
				INNER JOIN coopex.pessoa p ON p.id_pessoa = a.id_reclamante 
			WHERE
				id_protocolo = $id_protocolo 
			ORDER BY
				data_cadastro DESC";

	$res = $coopex->query($sql);
	$dados = $res->fetch(PDO::FETCH_OBJ);
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
		<li class="breadcrumb-item"><a href="javascript:void(0);">Protocolo de Atendimento</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu ?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-calendar'></i> Protocolo de Atendimento
			<small>
				Cadastro de Protocolos de Atendimento
			</small>
		</h1>
	</div>
	<?
	if (isset($_GET['id'])) {
	?>
		<div class="row">
			<div class="col-sm-6 col-xl-4" title="Total Geral de Inscritos">
				<div class="p-3 bg-primary-300 rounded overflow-hidden position-relative text-white mb-g">
					<div class="">
						<h3 class="display-4 d-block l-h-n m-0 fw-500">
							<?= $dados->data_curta ?>
							<small class="m-0 l-h-n">Início do Atendimento</small>
						</h3>
					</div>
					<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
				</div>
			</div>
			<div class="col-sm-6 col-xl-4" title="Total Geral de Inscritos">
				<div class="p-3 bg-success-600 rounded overflow-hidden position-relative text-white mb-g">
					<div class="">
						<h3 class="display-4 d-block l-h-n m-0 fw-500">
							<?= $dados->data_final ?>
							<small class="m-0 l-h-n">Prazo para término</small>
						</h3>
					</div>
					<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
				</div>
			</div>
			<div class="col-sm-6 col-xl-4" title="Total Geral de Inscritos">
				<div id="card_tempo_restante" class="p-3  rounded overflow-hidden position-relative text-white mb-g">
					<div class="">
						<h3 class="display-4 d-block l-h-n m-0 fw-500">
							<span id="countdown"></span>
							<small id="tempo_restante" class="m-0 l-h-n"></small>
						</h3>
					</div>
					<i class="fal fa-user position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
				</div>
			</div>
		</div>

		<script>
			// Defina a data-alvo da contagem regressiva
			const targetDate = new Date('<?= $dados->data_contagem ?>').getTime();

			// Atualize o contador a cada segundo
			const countdownInterval = setInterval(() => {
				// Obtenha a data e hora atual
				const now = new Date().getTime();

				// Calcule a diferença entre a data atual e a data-alvo
				const difference = targetDate - now;

				// Se a diferença for negativa, a data-alvo já passou
				if (difference < 0) {
					// Calcule o tempo de atraso
					const lateTime = Math.abs(difference);

					// Calcule horas, minutos e segundos de atraso
					const lateHours = Math.floor(lateTime / (1000 * 60 * 60));
					const lateMinutes = Math.floor((lateTime % (1000 * 60 * 60)) / (1000 * 60));
					const lateSeconds = Math.floor((lateTime % (1000 * 60)) / 1000);

					$("#card_tempo_restante").addClass('bg-danger-600');

					document.getElementById('tempo_restante').innerHTML = 'Atrasado';
					// Exiba o tempo de atraso
					document.getElementById('countdown').innerHTML = `
                    ${lateHours}:${lateMinutes}:${lateSeconds}
                `;
				} else {
					// Caso contrário, calcule horas, minutos e segundos restantes
					const hours = Math.floor(difference / (1000 * 60 * 60));
					const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
					const seconds = Math.floor((difference % (1000 * 60)) / 1000);
					document.getElementById('tempo_restante').innerHTML = 'Tempo Restante';
					// Exiba a contagem regressiva
					$("#card_tempo_restante").addClass('bg-warning-600');
					document.getElementById('countdown').innerHTML = `
                    ${hours}:${minutes}:${seconds}
                `;
				}

			}, 1000); // Atualize a cada segundo
		</script>
	<?
	}
	?>
	<iframe class="d-none" name="dados" src=""></iframe>
	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/colegio/atendimento/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave ?>" value="<?php echo $$chave ? $$chave : 0 ?>">

		<div class="row">
			<div class="col-xl-12">
				<div id="panel-2" class="panel">
					<div class="panel-hdr">
						<h2>
							Protocolo
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Status<span class="text-danger">*</span></label>
										<?php
										$sql = "SELECT
													* 
												FROM
													colegio.atd_status 
												ORDER BY
													ordem";

										$periodo = $coopex->query($sql);
										?>
										<select id="id_status" name="id_status" class="select2 form-control" required="">
											<?php
											while ($row2 = $periodo->fetch(PDO::FETCH_OBJ)) {
												$selecionado = '';
												if (isset($dados->id_status)) {
													if ($dados->id_status == $row2->id_status) {
														$selecionado = 'selected=""';
													}
												} else {
													$selecionado = '';
												}
											?>
												<option <?= isset($dados->id_status) ? $selecionado : "" ?> value="<?= $row2->id_status ?>"><?= utf8_encode($row2->status) ?>
												</option>
											<?php
											}
											?>
										</select>
										<div class="invalid-feedback">
											Selecione o período da reoferta
										</div>
									</div>
								</div>
								<div class="form-row">
									<div class="col-md-12 mb-3">
										<div class="form-group">
											<div class="form-group">
												<label class="form-label" for="select2-ajax">
													Aluno <span class="text-danger">*</span>
												</label>
												<?
												if (!isset($_GET['id'])) {
												?>
													<select name="id_reclamante" onChange="" data-placeholder="Nome do aluno..." class="js-consultar-usuario form-control" id="select2-ajax"></select>
												<?
												} else {
												?>
													<input type="text" class="form-control" value="<?php echo isset($dados->id_pessoa) ? utf8_encode($dados->nome) : "" ?>" readonly>
												<?
												}
												?>
											</div>
										</div>
										<div class="form-row">
											<div class="col-md-12 mb-3">
												<label class="form-label" for="validationCustom03">Reclamação <span class="text-danger">*</span></label>
												<textarea name="reclamacao" rows="10" class="form-control col-md-12"><?php echo isset($dados->id_pessoa) ? utf8_encode($dados->reclamacao) : "" ?></textarea>

											</div>
										</div>
									</div>



								</div>
								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Tempo de Resposta<span class="text-danger"> *</span></label>

										<select <?= isset($_GET['id']) ? "disabled" : "" ?> id="tempo_resposta" name="tempo_resposta" class="select2 form-control" required="">
											<option <?= isset($_GET['id']) && $dados->tempo_resposta == 24 ? "selected " : "" ?> value="24">24</option>
											<option <?= isset($_GET['id']) && $dados->tempo_resposta == 36 ? "selected " : "" ?>value="36">36</option>
											<option <?= isset($_GET['id']) && $dados->tempo_resposta == 72 ? "selected " : "" ?>value="72">72</option>
										</select>
										<div class="invalid-feedback">
											Selecione o período da reoferta
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
		</div>
	</form>

	<?
	if (isset($_GET['id'])) {

		$sql = "SELECT
					*,
					DATE_FORMAT( data_cadastro, '%d/%m/%Y' ) AS data_cadastro,
					DATE_FORMAT( data_cadastro, '%H:%i:%s' ) AS hora 
				FROM
					colegio.atd_atendimento 
				INNER JOIN coopex.pessoa USING ( id_pessoa )	
				WHERE
					id_protocolo = " . $_GET['id'];
		$res = $coopex->query($sql);
		if ($res->rowCount()) {

	?>
			<div class="tab-content">
				<div class="tab-pane show active" id="tab-all" role="tabpanel" aria-labelledby="tab-all">
					<div class="subheader mb-2">
						<h1 class="subheader-title">
							<i class='subheader-icon fal fa-calendar'></i> Histórico de Antedimento
						</h1>
					</div>
					<div class="card">

						<ul class="list-group list-group-flush">
							<?
							while ($row = $res->fetch(PDO::FETCH_OBJ)) {
							?>

								<li class="list-group-item py-4 px-4">
									<span class="fs-lg fw-500"><?= $row->data_cadastro; ?> - <?= $row->hora; ?></span>
									<div class="fs-lg mt-1 text-success">
										<?= $row->nome ?>
									</div>
									<div class="mt-2">
										<?= utf8_encode($row->resposta); ?>
									</div>

								</li>


							<?
							}
							?>
						</ul>
					</div>
				</div>
			</div>
		<?
		}
		?>

		<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/colegio/atendimento/resposta_dados.php">
			<input type="hidden" name="id_atencimento" value="0">
			<input type="hidden" name="id_protocolo" value="<?= $_GET['id'] ?>">

			<div class="row mt-5">
				<div class="col-xl-12">
					<div id="panel-2" class="panel">
						<div class="panel-hdr">
							<h2>
								Antendimento
							</h2>
							<div class="panel-toolbar">
								<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
								<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
							</div>
						</div>
						<div class="panel-container show">
							<div class="panel-content">
								<div class="form-row">

									<div class="col-md-12 mb-3">
										<div class="form-row">
											<div class="col-md-12 mb-3">
												<label class="form-label" for="validationCustom03">Resposta <span class="text-danger">*</span></label>
												<textarea name="resposta" rows="10" class="form-control col-md-12"></textarea>
											</div>
										</div>
									</div>

								</div>
							</div>

							<div class="panel-container show">
								<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
									<button class="btn btn-primary ml-auto" type="submit">Cadastrar</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	<?
	}
	?>




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
				document.location.reload(true);
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
				//window.history.back();
			}
		});
	}

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
</script>