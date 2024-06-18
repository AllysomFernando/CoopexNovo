<?php
require_once("php/sqlsrv.php");

$id_menu = 115;
$chave = "id_estagio";
$session_tipo_usuario = $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'];
$session_id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];

if (isset($_GET['id'])) {
	$$chave = $_GET['id'];

	$sql = "SELECT
	p.id_pessoa as coopex_pessoa_id,
	p.nome,
	e.*,
	c.*
FROM estagio.estagio e
INNER JOIN coopex.curso c on e.id_curso = c.id_curso
INNER JOIN coopex.pessoa p ON p.id_pessoa = e.id_pessoa
WHERE e.id_estagio = " . $_GET['id'];
	$res = $coopex->query($sql);
	$dados = $res->fetch(PDO::FETCH_OBJ);
} else {
	$$chave = 0;
}

$isAdmin = $_SESSION['coopex']['usuario']['sistema']['tipo_usuario'] == "ADMINISTRADOR";
$possuiPermissao = '';

?>

<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/core.js?<?= rand() ?>"></script>

<main id="js-page-content" role="main" class="page-content">

	<?php
	if (!isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][1]) || (isset($_GET['id']) && $session_id_pessoa != $dados->coopex_pessoa_id && !$isAdmin)) {
	?>
		<div class="alert border-danger bg-transparent text-secondary fade show" role="alert">
			<div class="d-flex align-items-center">
				<div class="alert-icon">
					<span class="icon-stack icon-stack-md">
						<i class="base-7 icon-stack-3x color-danger-900"></i>
						<i class="fal fa-times icon-stack-1x text-white"></i>
					</span>
				</div>
				<div class="flex-1">
					<span class="h5 color-danger-900">Você não tem acesso a este painel.</span>
				</div>

			</div>
		</div>
	<?php
		exit;
	}
	?>

	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="https://coopex.fag.edu.br/estagio/consulta">Relatório de estágio</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID.
				<?php echo $id_menu ?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-paperclip'></i> Relatório de estágio
			<small>
				Cadastro de relatório de estágio
			</small>
		</h1>

	</div>

	<?php
	$desabilitar_edicao_carga_horaria = false;
	$desabilitar_edicao = false;

	if (($session_tipo_usuario == 1 || $session_tipo_usuario == 5 || $session_tipo_usuario == 10) && isset($_GET['id'])) {
		$desabilitar_edicao_carga_horaria = true;
		$desabilitar_edicao = true;
	};

	?>

	<iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 100%; background-color: #fff; top: 0; left: 0; height: 200px"></iframe>

	<form class="needs-validation" method="post" novalidate="" target="dados" action="modulos/estagio/cadastro/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave ?>" value="<?php echo $$chave ? $$chave : 0 ?>" id="id_estagio">
		<div class="row">
			<div class="col-xl-12">
				<div id="panel-2" class="panel">
					<div class="panel-hdr">
						<h2>
							1. Estágio
						</h2>
					</div>
					<div class="panel-container show">

						<div class="panel-content p-0">
							<div class="panel-content">
								<input type="text" value="<?php echo $_SESSION['coopex']['usuario']['id_pessoa'] ?>" name="id_pessoa" hidden>
								<div class="form-row">
									<?php
									if ($session_tipo_usuario != 6) { ?>
										<div class="col-md-5 mb-3">
											<label class="form-label" for="validationCustom02">Aluno<span class="text-danger">*</span></label>
											<input type="text" class="form-control" value="<?php echo texto($dados->nome) ?>" required disabled>
										</div>
									<?php }
									?>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Período<span class="text-danger">*</span></label>
										<?php
										$sql = "SELECT
														id_periodo,
														periodo,
														ativo
													FROM
														estagio.periodo_atual
													ORDER BY
														id_periodo DESC";

										$periodo = $coopex->query($sql);
										?>
										<select <?php echo $desabilitar_edicao ? 'disabled=""' : "" ?> id="id_periodo" name="id_periodo" class="select2 form-control" required="">
											<?php

											// if()

											?>
											<option value="">Selecione o Período</option>
											<?php
											while ($row = $periodo->fetch(PDO::FETCH_OBJ)) {
												$selecionado = '';
												if ($dados->id_periodo == $row->id_periodo) {
													$selecionado = 'selected=""';
												}
											?>
												<option <?php echo isset($dados->id_periodo) ? $selecionado : "" ?> <?php echo !$row->ativo ? "disabled" : "" ?> value="<?php echo $row->id_periodo ?>"><?php echo $row->periodo ?>
													<?php echo !$row->ativo ? "(Inativo)" : "(Atual)" ?></option>
											<?php
											}
											?>
										</select>
										<div class="invalid-feedback">
											Selecione o período do estagio
										</div>
									</div>
									<div class="col-md-4 mb-3">
										<label class="form-label" for="validationCustom03">Curso <span class="text-danger">*</span></label>
										<?php

										if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][5]) || isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][6])) {
											$where = " WHERE graduacao = 1 ";
										} else {
											$where = " WHERE graduacao = 1 AND id_pessoa = " . $_SESSION['coopex']['usuario']['id_pessoa'];
										}

										$id_departamento = $_SESSION['coopex']['usuario']['id_curso'];

										$where = " WHERE id_departamento in ($id_departamento)";

										$sql = "SELECT
														id_departamento,
														departamento
													FROM
														coopex.departamento
														INNER JOIN coopex.departamento_pessoa USING ( id_departamento )
														$where
														AND campus = 1
													GROUP BY
														id_departamento
													ORDER BY
														departamento";

										$curso = $coopex->query($sql);
										?>

										<select <?php echo $desabilitar_edicao_carga_horaria || $desabilitar_edicao ? 'disabled=""' : "" ?> id="id_curso" name="id_curso" class="select2 form-control" required="">

											<?php

											if ($dados->id_curso) { ?>
												<option value="<?php echo $dados->id_curso ?>" selected><?php echo $dados->curso ?></option>
											<?php }

											?>

											<option value="">Selecione o Curso</option>
											<?php
											while ($row = $curso->fetch(PDO::FETCH_OBJ)) {
												$selecionado = '';
												if ($dados->id_curso == $row->id_departamento) {
													$selecionado = 'selected';
												}
											?>
												<option <?php echo isset($dados->id_curso) ? $selecionado : "" ?> value="<?php echo $row->id_departamento ?>">
													<?php echo utf8_encode($row->departamento) ?></option>
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

								</div>
								<div class="form-row">
									<div class="col-md-8 mb-3">
										<label class="form-label" for="validationCustom03">Disciplina <span class="text-danger">*</span></label>
										<select <?php echo $desabilitar_edicao_carga_horaria || $desabilitar_edicao ? 'disabled=""' : "" ?> id="id_disciplina" name="id_disciplina" onchange="$('#disciplina').val($(this).select2('data')[0].text);" disabled="" class="select2 form-control" required="">
											<option value="">Selecione a Disciplina</option>
										</select>
										<input type="hidden" name="disciplina" id="disciplina" value="<?php echo isset($dados->disciplina) ? utf8_encode($dados->disciplina) : "" ?>" disabled hidden>
										<div class="invalid-feedback">
											Selecione a disciplina
										</div>
									</div>
									<div class="col-md-2 mb-3">
										<label class="form-label" for="validationCustom02">Carga Horária</label>
										<input readonly="" type="text" class="form-control" id="carga_horaria_disciplina" name="carga_horaria" placeholder="" value="" required>
										<div class="valid-feedback">
											OK!
										</div>
									</div>

								</div>

								<div class="form-row">
									<div class="col-md-12 mb-3">
										<div class="form-row">
											<div class="col-md-8 mb-3">
												<div class="form-group">
													<label class="form-label" for="select2-ajax">
														Docente da disciplina
													</label>


													<select <?php echo $desabilitar_edicao ? 'disabled=""' : "" ?> name="id_docente" data-placeholder="Selecione o docente da disciplina" class="js-consultar-usuario form-control" required>
														<?php
														if (isset($dados->id_docente)) {
															$id_docente = $dados->id_docente;
															$sql = "SELECT DISTINCT
																			id_pessoa,
																			nome
																		FROM
																			integracao..view_integracao_usuario
																		WHERE
																			id_pessoa IN ($id_docente) AND tipo = 'PROFESSOR'";
															$res = mssql_query($sql);

															while ($row = mssql_fetch_assoc($res)) {
														?>
																<option value="<?php echo $row['id_pessoa'] ?>">
																	<?php echo trim(utf8_encode($row['nome'])) ?></option>
															<?php
															}
															?>
														<?php
														}
														?>
													</select>
													<div class="invalid-feedback">
														Selecione o docente da disciplina
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<hr>
								<div class="form-row">
									<div class="col-md-6 mb-3">
										<label class="form-label" for="validationCustom02">Empresa/Projeto<span class="text-danger">*</span></label>
										<input value="<?php echo isset($dados->empresa) ? utf8_encode($dados->empresa) : "" ?>" type="text" class="form-control" name="empresa" required>
										<div class="invalid-feedback">
											Informe a empresa ou projeto que você está estagiando
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<label class="form-label" for="validationCustom02">Função<span class="text-danger">*</span></label>
										<input value="<?php echo isset($dados->funcao) ? utf8_encode($dados->funcao) : "" ?>" type="text" class="form-control" name="funcao" required>
										<div class="invalid-feedback">
											Informe a função que você exerce nessa empresa ou projeto
										</div>
									</div>

								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xl-12">
				<div id="panel-2" class="panel">
					<div class="panel-hdr">
						<h2>
							2. Atividades realizadas
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content p-0">

							<div class="panel-content">


								<div class="form-row" id="cronograma_container">
									<div class="col-xl-12">
										<!-- datatable start -->
										<table id="cronograma_tabela" class="table table-bordered table-hover table-striped w-100"></table>
										<!-- datatable end -->
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-container show">

						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="form-row">

								</div>
								<div class="form-row form-group">

								</div>
							</div>
						</div>
						<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">

							<button class="btn btn-primary ml-auto" type="submit"><?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar" ?></button>
						</div>
						<textarea class="d-none" name="cronograma" id="cronograma" rows="10" cols="100">[]</textarea>
					</div>
				</div>
			</div>
		</div>

	</form>



</main>

<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/datagrid/datatables/dataTables.editor.min.js"></script>
<script src="js/moment-with-locales.js"></script>
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<script src="js/modulos/estagio/utils.js?<?php echo rand() ?>"></script>
<script src="js/modulos/estagio/data-table-cronograma.js?<?php echo rand() ?>"></script>
<script>
	//MENSAGEM DE CADASTRO OK
	function cadastroOK(operacao) {
		var msg = operacao == 1 ? "Registro cadastrado com sucesso" : "Registro alterado com sucesso";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				<?php
				if (!isset($_GET['id'])) {
					echo "window.history.back();";
				} else {
					echo "document.location.reload(true);";
				}
				?>

				//document.location.reload(true)
			}
		});
	}

	//MENSAGEM DE FALHA NO CADASTRO
	function cadastroFalha(operacao) {
		var msg = operacao == 1 ? "Falha ao cadastrar dados" : "Falha ao alterar dados";
		Swal.fire({
			type: "error",
			title: msg,
			showConfirmButton: false,
			timer: 1500
		});
	}

	function carrega_carga_horaria_disciplina(id_disciplina = '') {
		if (!id_disciplina) {
			id_disciplina = $("#id_disciplina").val()
		}

		$.getJSON("modulos/proex/cadastro/ajax/carrega_carga_horaria_disciplina.php", {
				id_disciplina: id_disciplina
			})
			.done(function(json) {
				$("#carga_horaria_disciplina").val(json);

			})
			.fail(function(jqxhr, textStatus, error) {
				var err = textStatus + ", " + error;
				console.log("Request Failed: " + err);
			});
	}


	//CARREGAS AS DISCIPLINAS REFERENTES AO CURSO SELECIONADO
	function carrega_disciplina(id_disciplina = '') {
		$("#carga_horaria_disciplina").val('');

		$("#id_disciplina").attr("disabled", true);

		$.getJSON("modulos/estagio/cadastro/ajax/carrega_disciplina.php", {
				id_curso: $("#id_curso").val()
			})
			.done(function(json) {
				$("#id_disciplina").empty();
				$("#id_disciplina").append("<option value=''>Seleciona a Disciplina</option>");
				$.each(json, function(i, item) {
					$("#id_disciplina").append('<option value="' + item.atc_id_atividade + '">' + item.atc_nm_atividade + '</option>');
					$("#carga_horaria_disciplina").val(item.atc_qt_horas);
					$("#id_disciplina").attr("disabled", false);
				});
				if (id_disciplina) {
					$('#id_disciplina option[value=' + id_disciplina + ']').attr('selected', 'selected');
				}
			})
			.fail(function(jqxhr, textStatus, error) {
				var err = textStatus + ", " + error;
				console.log("Request Failed: " + err);
			});
	}


	function recalculo() {
		$('#cronograma_tabela').DataTable().draw(false);
	}


	$(document).ready(function() {

		// let data = document.querySelector('#data');

		// data.value = new Date()

		//setInterval(recalculo, 1000);



		//CARREGA OS DADOS DOS SELECTS DEPENDENTES QUANDO EDITAR O REGISTRO
		<?php
		if (isset($_GET['id'])) {
			if (isset($dados->id_disciplina)) {
				echo "carrega_disciplina(" . $dados->id_disciplina . ");";
				echo "carrega_carga_horaria_disciplina(" . $dados->id_disciplina . ");";
			}
		}
		?>

		$(":input").inputmask();
		$('.select2').select2();


		$("#id_curso").change(function() {
			carrega_disciplina();
			//carrega_carga_horaria_reoferta();
		});

		$("#id_disciplina").change(function() {
			carrega_carga_horaria_disciplina();
		});

		var total_global = 0;
		//SELECT USUÁRIO
		$(".js-consultar-usuario").select2({
			ajax: {
				url: "modulos/estagio/cadastro/ajax/buscar_professor.php",
				dataType: 'json',
				delay: 250,
				data: function(params) {
					return {
						q: params.term, // search term
						page: params.page
					};
				},
				processResults: function(data, params) {
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
			placeholder: 'Buscar no banco de dados',
			escapeMarkup: function(markup) {
				return markup;
			}, // let our custom formatter work
			minimumInputLength: 3,
			templateResult: formatoUsuario,
			templateSelection: formatoTextoUsuario
		});

	});

	$('#data').on('change', function() {
		var value = $(this).val();
		if (!isValidDate(value)) {
			$(this).addClass('invalid');
		} else {
			$(this).removeClass('invalid');
		}
	});

	// Function to validate date format (YYYY-MM-DD)
	function isValidDate(dateString) {
		var regEx = /^\d{4}-\d{2}-\d{2}$/;
		return dateString.match(regEx) !== null;
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
</script>