<?php
	require_once("php/sqlsrv.php");
	
	$id_menu = 49;
	$chave	 = "id_grupo_periodo";

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
		<li class="breadcrumb-item"><a href="javascript:void(0);">Grupos</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block">
			<span class="">ID. <?php echo $id_menu?>c</span>
		</li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-repeat'></i> Grupos
			<small>
				Cadastro de Grupos
			</small>
		</h1>
	</div>

	<iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 100%; background-color: #fff; top: 0; left: 0; height: 200px"></iframe>

	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/medicina/grupos/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave?>" value="<?php echo $$chave ? $$chave : 0?>">
		<div class="row">
			<div class="col-xl-12">
			  	<div id="panel-2" class="panel">
				  	<div class="panel-hdr">
						<h2>
							1. Grupo
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
									<div class="col-md-4 mb-3">
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
									<div class="col-md-2 mb-3">
										<label class="form-label" for="validationCustom02">
											Alunos na turma<span class="text-danger">*</span>
										</label>
										<input onkeyup="ajusta_tabela()" type="text" class="form-control" id="alunos_turma" name="alunos_turma" placeholder="" value="<?php echo isset($dados->qtd_alunos) ? $dados->qtd_alunos : ""?>" required>
									</div>
									<div class="col-md-2 mb-3">
										<label class="form-label" for="validationCustom02">
											Alunos por grupo<span class="text-danger">*</span>
										</label>
										<input onkeyup="ajusta_tabela()" type="text" class="form-control" id="alunos_grupo" name="alunos_grupo" placeholder="" value="<?php echo isset($dados->qtd_alunos) ? $dados->qtd_alunos : ""?>" required>
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
							2. Divisão
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content p-0">

							<div class="panel-content">

								<table id="tabela" class="table table-sm">
									<thead>
										<tr>
											<th>Grupo</th>
											<th>Alunos</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>

							</div>
						</div>
					</div>

					<script type="text/javascript">
						function ajusta_tabela(){
							var alunos_turma = $("#alunos_turma").val();
							var alunos_grupo = $("#alunos_grupo").val();
							var grupos = alunos_turma / alunos_grupo;
	
							$("#tabela > tbody").empty();
							var soma = 0;
							var json = "";
							if(grupos != "Infinity"){
								for(i=1; i<grupos; i++){
									p = parseInt(alunos_grupo);
									soma += p;
									$("#tabela > tbody").append(`<tr><td>${i}</td><td>${p}</td></tr>`);
									json += `;i{"grupo":"${i}","alunos_grupo":"${p}"}`
								}
								$("#tabela > tbody").append(`<tr><td>${i}</td><td>${alunos_turma - soma}</td></tr>`);
								json += `;i{"grupo":"${i}","alunos_grupo":"${alunos_turma - soma}"}`
								$("#cronograma").val(json);
							}
						}
					</script>

					<div class="panel-container show">
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