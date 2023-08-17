<?php

	require_once("php/sqlsrv.php");
	
	$id_menu = 41;
	$chave	 = "id_horario";

	if(isset($_GET['id'])){
		$$chave = $_GET['id'];

		$sql = "SELECT
					*
				FROM
					medicina.horario
				WHERE
					id_horario = ".$_GET['id'];
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

	<?php
		if(!isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][1])){
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
		<li class="breadcrumb-item"><a href="javascript:void(0);">Horários</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block">
			<span class="">ID. <?php echo $id_menu?>c</span>
		</li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-repeat'></i> Horários
			<small>
				Cadastro de Horários
			</small>
		</h1>
	</div>

	<iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 100%; background-color: #fff; top: 0; left: 0; height: 200px"></iframe>

	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/medicina/horarios/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave?>" value="<?php echo $$chave ? $$chave : 0?>">
		<div class="row">
			<div class="col-xl-12">
			  	<div id="panel-2" class="panel">
				  	<div class="panel-hdr">
						<h2>
							1. Informações
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
									<div class="col-md34 mb-3">
										<label class="form-label" for="validationCustom03">Período<span class="text-danger">*</span></label>
										<?php
											$sql = "SELECT
														*
													FROM
														medicina.periodo
													ORDER BY
														periodo";

											$periodo = $coopex->query($sql);
										?>
										<select id="id_periodo" name="id_periodo" class="select2 form-control" required="">
											<option value="">Selecione o Período</option>
										<?php
											while($row = $periodo->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if($dados->id_periodo == $row->id_periodo){
													$selecionado = 'selected=""';
												}
										?>	
											<option <?php echo isset($dados->id_periodo) ? $selecionado : ""?> value="<?php echo $row->id_periodo?>"><?php echo texto($row->periodo)?></option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											O período é obrigatório
										</div>
									</div>
								</div>
								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Especialidade<span class="text-danger">*</span></label>
										<?php
											$sql = "SELECT
														*
													FROM
														medicina.especialidade
													ORDER BY
														especialidade";

											$especialidade = $coopex->query($sql);
										?>
										<select id="id_especialidade" name="id_especialidade" class="select2 form-control" required="">
											<option value="">Selecione a Especialidade</option>
										<?php
											while($row = $especialidade->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if($dados->id_especialidade == $row->id_especialidade){
													$selecionado = 'selected=""';
												}
										?>	
											<option <?php echo isset($dados->id_especialidade) ? $selecionado : ""?> value="<?php echo $row->id_especialidade?>"><?php echo texto($row->especialidade)?></option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											Selecione a Especialidade
										</div>
									</div>
								</div>
								
								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Local<span class="text-danger">*</span></label>
										<?php
											$sql = "SELECT
														*
													FROM
														medicina.local
													ORDER BY
														local";

											$local = $coopex->query($sql);
										?>
										<select id="id_local" name="id_local" class="select2 form-control" required="">
											<option value="">Selecione o Local</option>
										<?php
											while($row = $local->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if($dados->id_local == $row->id_local){
													$selecionado = 'selected=""';
												}
										?>	
											<option <?php echo isset($dados->id_local) ? $selecionado : ""?> value="<?php echo $row->id_local?>"><?php echo texto($row->local)?></option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											Selecione o Local
										</div>
									</div>
								</div>	
								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Quantidade de Alunos<span class="text-danger">*</span></label>
										<?php
											$sql = "SELECT
														*
													FROM
														medicina.grupo_aluno
													ORDER BY
														id_grupo_aluno";

											$grupo_aluno = $coopex->query($sql);
										?>
										<select onchange="habilita_numero_alunos()" id="id_grupo_aluno" name="id_grupo_aluno" class="select2 form-control" required="">
											<option value="">Selecione a Quantidade de Alunos</option>
										<?php
											while($row = $grupo_aluno->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if($dados->id_grupo_aluno == $row->id_grupo_aluno){
													$selecionado = 'selected=""';
												}
										?>	
											<option <?php echo isset($dados->id_grupo_aluno) ? $selecionado : ""?> value="<?php echo $row->id_grupo_aluno?>"><?php echo texto($row->grupo_aluno)?></option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											Selecione a Quantidade de Alunos
										</div>
									</div>
									<div class="col-md-2 mb-3">
										<label class="form-label" for="validationCustom02">Número de Alunos<span class="text-danger">*</span></label>
										<input disabled type="text" class="form-control" id="qtd_alunos" name="qtd_alunos" placeholder="" value="<?php echo isset($dados->qtd_alunos) ? $dados->qtd_alunos : ""?>" required>
									</div>

									<script type="text/javascript">
										function habilita_numero_alunos(){
											if($("#id_grupo_aluno").val() == 5){
												$("#qtd_alunos").removeAttr("disabled");
												$("#qtd_alunos").focus();
											} else {
												$("#qtd_alunos").attr("disabled", "disabled");
											}
										}
									</script>
								</div>

								<div class="form-row">
									<div class="col-md-6 mb-3">
										<div class="form-group">
											<label class="form-label" for="select2-ajax">
												Professor
											</label>
											
											<select name="id_docente" data-placeholder="Selecione o docente da disciplina" class="js-consultar-usuario form-control" >
												<?php
													if(isset($dados->id_docente)){
														$id_docente = $dados->id_docente;
														$sql = "SELECT DISTINCT
																	id_pessoa,
																	nome
																FROM
																	integracao..view_integracao_usuario 
																WHERE
																	id_pessoa IN ($id_docente)";
														$res = mssql_query($sql);

													 	while($row = mssql_fetch_assoc($res)){
												?>
														<option  value="<?php echo $row['id_pessoa']?>"><?php echo trim(utf8_encode($row['nome']))?></option>
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

								<div class="form-row">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom02">Observação<span class="text-danger">*</span></label>
										<textarea type="text" class="form-control" name="obs" placeholder="" required><?php echo isset($dados->obs) ? utf8_encode($dados->obs) : ""?></textarea>
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
							2. Horários
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


						<button class="btn btn-primary ml-auto" type="submit"><?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar"?></button>
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
	function cadastroOK(operacao){ 
		var msg = operacao == 1 ? "Registro cadastrado com sucesso" : "Registro alterado com sucesso";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				<?php
					if(!isset($_GET['id'])){
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
	function cadastroFalha(operacao){ 
		var msg = operacao == 1 ? "Falha ao cadastrar dados" : "Falha ao alterar dados";
		Swal.fire({
			type: "error",
			title: msg,
			showConfirmButton: false,
			timer: 1500
		});
	}


	$(document).ready(function(){

		$(":input").inputmask();
		$('.select2').select2();

		$(".js-consultar-usuario").select2({
			ajax:{
				url: "modulos/_core/buscar_usuario.php",
				dataType: 'json',
				delay: 250,
				data: function(params){
					return {
						q: params.term, // search term
						page: params.page
					};
				},
				processResults: function(data, params){
					params.page = params.page || 1;

					return {
						results: data.items,
						pagination:
						{
							more: (params.page * 30) < data.total_count
						}
					};
				},
				cache: true
			},
			placeholder: 'Buscar no banco de dados',
			escapeMarkup: function(markup){
				return markup;
			}, // let our custom formatter work
			minimumInputLength: 3,
			templateResult: formatoUsuario,
			templateSelection: formatoTextoUsuario
		});


		// Column Definitions
		var columnSet = [
			{
				title: "ID",
				id: "id_horario_dia",
				data: "id_horario_dia",
				placeholderMsg: "Gerado automáticamente",
				"visible": false,
				"searchable": false,
				type: "readonly"
			},
			{
				title: "Dia da semana",
				id: "id_dia",
				data: "id_dia",
				type: "select",
				"options": [
					"Segunda",
					"Terça",
					"Quarta",
					"Quinta",
					"Sexta"
				]
			},
			{
				title: "Horário de Início",
				id: "horario_inicio",
				data: "horario_inicio",
				type: "time"
			},
			{
				title: "Horário de Término",
				id: "horario_termino",
				data: "horario_termino",
				type: "time"
			},
		]

		/* start data table */
		var myTable = $('#cronograma_tabela').dataTable(
		{
			/* check datatable buttons page for more info on how this DOM structure works */
			dom: "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
			ajax: "modulos/medicina/horarios/ajax/carrega_horarios.php?id_horario=<?php echo $id_horario?>",

			columns: columnSet,
			paging: false,
			/* selecting multiple rows will not work */
			select: 'single',
			/* altEditor at work */
			altEditor: true,
			responsive: true,
			/* buttons uses classes from bootstrap, see buttons page for more details */
			buttons: [
				{
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
			columnDefs: [
				
			],




			/* default callback for insertion: mock webservice, always success */
			onAddRow: function(dt, rowdata, success, error){
				success(rowdata);
				$("#cronograma").append(";i"+JSON.stringify(rowdata, null, 5));
			},
			onEditRow: function(dt, rowdata, success, error){
				success(rowdata);
				$("#cronograma").append(";u"+JSON.stringify(rowdata, null, 5));
			},
			onDeleteRow: function(dt, rowdata, success, error){
				success(rowdata);
				$("#cronograma").append(";d"+JSON.stringify(rowdata, null, 5));
			},
		});
	});

	(function() {
		'use strict';
		window.addEventListener('load', function(){
			// Fetch all the forms we want to apply custom Bootstrap validation styles to
			var forms = document.getElementsByClassName('needs-validation');
			// Loop over them and prevent submission
			var validation = Array.prototype.filter.call(forms, function(form){
				form.addEventListener('submit', function(event){
					if (form.checkValidity() === false){
						event.preventDefault();
						event.stopPropagation();
					}
					form.classList.add('was-validated');
				}, false);
			});
		}, false);
	})();

</script>