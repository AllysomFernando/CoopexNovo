<?php

require_once("php/sqlsrv.php");

$id_menu = 91;
$chave	 = "id_reoferta";
$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];

if (isset($_GET['id'])) {
	$$chave = $_GET['id'];
	//CARREGA DADOS DA REOFERTA
	$sql = "SELECT * FROM proex.proex
				WHERE
					id_reoferta = " . $_GET['id'];
	$res = $coopex->query($sql);
	$dados = $res->fetch(PDO::FETCH_OBJ);

	//CARREGA DADOS DOS ACADÊMICOS AUTORIZADOS
	$sql = "SELECT
					id_usuario
				FROM
				proex.academico_autorizado
				WHERE
					id_reoferta = " . $_GET['id'];

	$res = $coopex->query($sql);

	$array = array();
	while ($row = $res->fetch(PDO::FETCH_OBJ)) {
		array_push($array, $row->id_usuario);
	}
	$array_academico_autorizado = implode(",", $array);
	$array_academico_autorizado_select = implode("','", $array);

	//VERIFICA SE O ACADEMICO PODE ACESSAR O PROEX
	$sql = "SELECT
				id_usuario,
				id_reoferta 
			FROM
				proex.academico_autorizado
				INNER JOIN proex.proex USING ( id_reoferta ) 
			WHERE
				(
					id_pessoa = $id_pessoa 
					OR id_docente = $id_pessoa 
				OR id_reoferta IN ( SELECT id_reoferta FROM proex.academico_autorizado WHERE id_usuario = $id_pessoa )) 
				AND id_reoferta = ".$_GET['id'];

	$res = $coopex->query($sql);

	if ($res->rowCount() == 0) { ?>
		<div class="alert border-danger bg-transparent text-secondary fade show" role="alert">
			<div class="d-flex align-items-center">
				<div class="alert-icon">
					<span class="icon-stack icon-stack-md">
						<i class="base-7 icon-stack-3x color-danger-900"></i>
						<i class="fal fa-times icon-stack-1x text-white"></i>
					</span>
				</div>
				<div class="flex-1">
					<span class="h5 color-danger-900">Seu usuário não possui permissão para acessar esta tela</span>
				</div>
				<a href="javascript:solicitarPermissao()" class="btn btn-outline-danger btn-sm btn-w-m">Solicitar acesso</a>
			</div>
		</div>
<?php exit;
	}
} else {
	$$chave = 0;
}

//print_r($dados);

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/core.js?<?= rand() ?>"></script>

<main id="js-page-content" role="main" class="page-content">

	<?php
	if (!isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][1])) {
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
					<span class="h5 color-danger-900">Seu usuário não possui permissão para acessar esta tela</span>
				</div>
				<a href="javascript:solicitarPermissao()" class="btn btn-outline-danger btn-sm btn-w-m">Solicitar acesso</a>
			</div>
		</div>
	<?php
		exit;
	}
	?>

	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">ProEX</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu ?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-repeat'></i> ProEX
			<small>
				Cadastro de ProEX
			</small>
		</h1>

	</div>

	<?php
	$desabilitar_edicao_carga_horaria = false;
	$desabilitar_edicao = false;
	if (isset($_GET['id'])) {
	?>

	<?php
	}
	?>

	<iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 100%; background-color: #fff; top: 0; left: 0; height: 200px"></iframe>

	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/proex/cadastro/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave ?>" value="<?php echo $$chave ? $$chave : 0 ?>">
		<div class="row">
			<div class="col-xl-12">
				<div id="panel-2" class="panel">
					<div class="panel-hdr">
						<h2>
							1. Projeto
						</h2>
					</div>
					<div class="panel-container show">

						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Período<span class="text-danger">*</span></label>
										<?php
										$sql = "SELECT
														id_periodo,
														periodo,
														ativo,
														DATE_FORMAT( pre_inscricao_data_inicial, '%d/%m/%Y' ) AS pre_inscricao_data_inicial,
														DATE_FORMAT( pre_inscricao_data_final, '%d/%m/%Y' ) AS pre_inscricao_data_final,
														DATE_FORMAT( inscricao_data_inicial, '%d/%m/%Y' ) AS inscricao_data_inicial,
														DATE_FORMAT( inscricao_data_final, '%d/%m/%Y' ) AS inscricao_data_final
													FROM
														proex.periodo
													ORDER BY
														id_periodo DESC";

										$periodo = $coopex->query($sql);
										?>
										<select <?php echo $desabilitar_edicao  ? 'disabled=""' : "" ?> id="id_periodo" name="id_periodo" class="select2 form-control" required="">
											<option value="">Selecione o Período</option>
											<?php
											while ($row = $periodo->fetch(PDO::FETCH_OBJ)) {
												$selecionado = '';
												if ($dados->id_periodo == $row->id_periodo) {
													$selecionado = 'selected=""';
												}
											?>
												<option <?php echo isset($dados->id_periodo) ? $selecionado : "" ?> <?php echo !$row->ativo ? "disabled" : "" ?> value="<?php echo $row->id_periodo ?>"><?php echo $row->periodo ?> <?php echo !$row->ativo ? "(Inativo)" : "(Atual)" ?></option>
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
										<select <?php echo $desabilitar_edicao_carga_horaria || $desabilitar_edicao  ? 'disabled=""' : "" ?> id="id_curso" name="id_departamento" class="select2 form-control" required="">
											<option value="">Selecione o Curso</option>
											<?php
											while ($row = $curso->fetch(PDO::FETCH_OBJ)) {
												$selecionado = '';
												if ($dados->id_departamento == $row->id_departamento) {
													$selecionado = 'selected=""';
												}
											?>
												<option <?php echo isset($dados->id_departamento) ? $selecionado : "" ?> value="<?php echo $row->id_departamento ?>"><?php echo utf8_encode($row->departamento) ?></option>
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
									<div class="col-md-8 mb-3">
										<label class="form-label" for="validationCustom03">Disciplina <span class="text-danger">*</span></label>
										<select <?php echo $desabilitar_edicao_carga_horaria || $desabilitar_edicao  ? 'disabled=""' : "" ?> id="id_disciplina" name="id_disciplina" onchange="$('#disciplina').val($(this).select2('data')[0].text);" disabled="" class="select2 form-control" required="">
											<option value="">Selecione a Disciplina</option>
										</select>
										<input type="hidden" name="disciplina" id="disciplina" value="<?php echo isset($dados->disciplina) ? utf8_encode($dados->disciplina) : "" ?>">
										<div class="invalid-feedback">
											Selecione a disciplina
										</div>
									</div>
									<div class="col-md-2 mb-3">
										<label class="form-label" for="validationCustom02">Carga Horária</label>
										<input readonly="" type="text" class="form-control" id="carga_horaria_disciplina" name="carga_horaria_disciplina" placeholder="" value="" required>
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


													<select <?php echo $desabilitar_edicao  ? 'disabled=""' : "" ?> name="id_docente" data-placeholder="Selecione o docente da disciplina" class="js-consultar-usuario form-control">
														<?php
														if (isset($dados->id_docente)) {
															$id_docente = $dados->id_docente;
															$sql = "SELECT DISTINCT
																			id_pessoa,
																			nome
																		FROM
																			integracao..view_integracao_usuario
																		WHERE
																			id_pessoa IN ($id_docente)";
															$res = mssql_query($sql);

															while ($row = mssql_fetch_assoc($res)) {
														?>
																<option value="<?php echo $row['id_pessoa'] ?>"><?php echo trim(utf8_encode($row['nome'])) ?></option>
															<?php
															}
															?>
														<?php
														}
														?>
													</select>
												</div>
											</div>
										</div>

										<div class="form-group">
											<label class="form-label" for="select2-ajax">
												Acadêmicos
											</label>
											<select multiple="multiple" id="id_academico_autorizado" name="id_academico_autorizado[]" data-placeholder="Selecione os acadêmicos participantes..." class="js-consultar-usuario form-control">
												<?php
												if (isset($array_academico_autorizado)) {
													$sql = "SELECT DISTINCT
																	id_pessoa,
																	nome
																FROM
																	integracao..view_integracao_usuario
																WHERE
																	id_pessoa IN ($array_academico_autorizado)";
													$res = mssql_query($sql);

													while ($row = mssql_fetch_assoc($res)) {
												?>
														<option value="<?php echo $row['id_pessoa'] ?>"><?php echo $row['id_pessoa'] . " - " . trim(utf8_encode($row['nome'])) ?></option>
													<?php
													}
													?>
													<script>
														$('#id_academico_autorizado').val(['<?php echo $array_academico_autorizado_select ?>']).trigger('change');
													</script>
												<?php
												}
												?>
											</select>

										</div>
									</div>
								</div>
								<hr>
								<div class="form-row">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom02">Descrição do Projeto<span class="text-danger">*</span></label>
										<textarea class="form-control" name="descricao" required><?php echo isset($dados->descricao) ? utf8_encode($dados->descricao) : "" ?></textarea>
									</div>
								</div>
								<div class="form-row">
									<div class="col-md-6 mb-3">
										<label class="form-label" for="validationCustom02">Público Alvo<span class="text-danger">*</span></label>
										<input value="<?php echo isset($dados->publico) ? utf8_encode($dados->publico) : "" ?>" type="text" class="form-control" name="publico" required>
									</div>
									<div class="col-md-6 mb-3">
										<label class="form-label" for="validationCustom02">Estimativa de Pessoas Beneficiadas<span class="text-danger">*</span></label>
										<input value="<?php echo isset($dados->estimativa) ? utf8_encode($dados->estimativa) : "" ?>" type="text" class="form-control" name="estimativa" required>
									</div>
								</div>
								<div class="form-row">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom02">Resultados Obtidos</label>
										<textarea class="form-control" name="resultado"><?php echo isset($dados->resultado) ? utf8_encode($dados->resultado) : "" ?></textarea>
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
							2. Atividades Realizadas
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
					</div>
				</div>
			</div>
		</div>

		<textarea class="d-none" name="cronograma" id="cronograma" rows="10" cols="100"></textarea>
	</form>



</main>

<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/datagrid/datatables/dataTables.editor.min.js"></script>
<script src="js/moment-with-locales.js"></script>
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
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


	//CARREGA A CARGA HORÁRIA DA DISCIPLINA SELECIONADA
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

		$.getJSON("modulos/proex/cadastro/ajax/carrega_disciplina.php", {
				id_curso: $("#id_curso").val()
			})
			.done(function(json) {
				$("#id_disciplina").empty();
				$("#id_disciplina").append("<option value=''>Seleciona a Disciplina</option>");
				$.each(json, function(i, item) {
					$("#id_disciplina").append('<option value="' + item.atc_id_atividade + '">' + item.atc_nm_atividade + '</option>');
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


	//CARREGAS CARGA HORÁRIA
	function carrega_carga_horaria_reoferta(id_disciplina = '', id_carga_horaria = '') {
		$("#id_carga_horaria_reoferta").val('');

		//$("#id_carga_horaria_reoferta").attr("disabled", true);

		$.getJSON("modulos/proex/cadastro/ajax/carrega_carga_horaria_reoferta.php", {
				id_curso: $("#id_curso").val()
			})
			.done(function(json) {
				$("#id_carga_horaria_reoferta").empty();
				$("#id_carga_horaria_reoferta").append("<option value=''>Seleciona a Carga Horária</option>");
				$.each(json, function(i, item) {
					$("#id_carga_horaria_reoferta").append('<option value="' + item.id_carga_horaria + '">' + item.carga_horaria + '</option>');

				});
				//$("#id_carga_horaria_reoferta").attr("disabled", false);
				if (id_carga_horaria) {
					$('#id_carga_horaria_reoferta option[value=' + id_carga_horaria + ']').attr('selected', 'selected');

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




		$("#select_disciplina_equivalente").change(function() {
			carrega_disciplina_equivalente();
		});



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

		//TOTALIZADOR DE HORAS NO RODAPÉ DA TABELA
		$("#cronograma_tabela").append('<tfoot><tr role="row" class="odd"><td class="sorting_1" tabindex="0"></td><td class="sorting_1" tabindex="0"></td><td></td><td><strong></strong></td><td id="tempoTotal"><strong>04:48</strong></td></tr></tfoot>');

		// Column Definitions
		var columnSet = [{
				title: "ID",
				id: "id_cronograma",
				data: "id_cronograma",
				placeholderMsg: "Gerado automáticamente",
				"visible": false,
				"searchable": false,
				type: "readonly"
			},
			{
				title: "Data",
				id: "data_reoferta",
				data: "data_reoferta",
				type: "date",
				pattern: "((?:19|20)\d\d)-(0?[1-9]|1[012])-([12][0-9]|3[01]|0?[1-9])",
				placeholderMsg: "dd-mm-yyyy",
				errorMsg: "*Invalid date format. Format must be yyyy-mm-dd"
			},
			{
				title: "Horário de Início",
				id: "horario_inicio",
				data: "horario_inicio",
				type: "time",
				pattern: "((?:19|20)\d\d)-(0?[1-9]|1[012])-([12][0-9]|3[01]|0?[1-9])",
				placeholderMsg: "yyyy-mm-dd",
				errorMsg: "*Invalid date format. Format must be yyyy-mm-dd"
			},
			{
				title: "Horário de Término",
				id: "horario_termino",
				data: "horario_termino",
				type: "time",
				pattern: "((?:19|20)\d\d)-(0?[1-9]|1[012])-([12][0-9]|3[01]|0?[1-9])",
				placeholderMsg: "yyyy-mm-dd",
				errorMsg: "*Invalid date format. Format must be yyyy-mm-dd"
			},
			{
				title: "Total de Horas",
				id: "descricao",
				data: "descricao",
				type: "textarea",
				placeholderMsg: "Descreva a atividade realizada"
			}
		]

		/* start data table */
		var myTable = $('#cronograma_tabela').dataTable({
			/* check datatable buttons page for more info on how this DOM structure works */
			dom: "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
			ajax: "modulos/proex/cadastro/ajax/cronograma.php?id_reoferta=<?php echo $id_reoferta ?>",

			columns: columnSet,
			paging: false,
			/* selecting multiple rows will not work */
			select: 'single',
			/* altEditor at work */
			altEditor: true,
			responsive: true,
			/* buttons uses classes from bootstrap, see buttons page for more details */
			buttons: [{
					extend: 'selected',
					text: '<i class="fal fa-times mr-1"></i> Excluir',
					name: 'delete',
					className: 'btn-primary btn-sm mr-1'
				},
				{
					extend: 'selected',
					text: '<i class="fal fa-edit mr-1"></i> Alterar',
					name: 'edit',
					className: 'btn-primary btn-sm mr-1'
				},
				{
					text: '<i class="fal fa-plus mr-1"></i> Inserir',
					name: 'add',
					className: 'btn-success btn-sm mr-1'
				}
			],
			columnDefs: [{
					targets: 4,
					render: function(data, type, full, meta) {
						//console.log(full);
						return subtraiHora(full.horario_inicio, full.horario_termino);
					}
				},
				{
					targets: 1,
					render: function(data, type, full, meta) {
						return moment(data).format('DD/MM/YYYY');
					},
					editorOnChange: function(event, altEditor) {

						console.log(event, altEditor);
					}
				},
			],

			"footerCallback": function(row, data, start, end, display) {
				var api = this.api(),
					data;
				var tempo_total = 0;
				console.log(data);
				for (i = 0; i < data.length; i++) {
					temp = subtraiHora(data[i].horario_inicio, data[i].horario_termino);
					tempo_total += moment.duration(temp).asMinutes();
				}

				var dur = moment.duration(tempo_total, 'minutes');
				var hours = Math.floor(dur.asHours());
				var mins = Math.floor(dur.asMinutes()) - hours * 60;
				var result = ((hours > 9) ? hours : ("0" + hours)) + ":" + ((mins > 9) ? mins : ("0" + mins));

				$(api.column(3).footer()).html('<strong>TOTAL</strong>');
				$(api.column(4).footer()).html('<strong>' + result + '</strong>');
			},
			columnDefs: [{
					targets: 4,
					render: function(data, type, full, meta) {
						return subtraiHora(full.horario_inicio, full.horario_termino);
					}
				},
				{
					targets: 1,
					render: function(data, type, full, meta) {
						return moment(data).format('DD/MM/YYYY');
					},
					editorOnChange: function(event, altEditor) {
						//console.log(event, altEditor);
					}
				},
			],

			/* default callback for insertion: mock webservice, always success */
			onAddRow: function(dt, rowdata, success, error) {
				success(rowdata);
				$("#cronograma").append(";i" + JSON.stringify(rowdata, null, 4));
			},
			onEditRow: function(dt, rowdata, success, error) {
				success(rowdata);
				$("#cronograma").append(";u" + JSON.stringify(rowdata, null, 4));
			},
			onDeleteRow: function(dt, rowdata, success, error) {
				success(rowdata);
				$("#cronograma").append(";d" + JSON.stringify(rowdata, null, 4));
			},
		});
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