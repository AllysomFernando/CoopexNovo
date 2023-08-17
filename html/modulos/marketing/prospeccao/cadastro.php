<?php
	require_once("php/sqlsrv.php");

	$id_menu = 42;
	$chave	 = "id_prospect";

	if(isset($_GET['id'])){
		$$chave = $_GET['id'];

		//CARREGA DADOS DA REOFERTA
		$sql = "SELECT
					*
				FROM
					marketing.prospect
				WHERE
					marketing.prospect.excluido = 0
				AND	
					id_prospect = ".$_GET['id'];
		$res = $coopex->query($sql);
		$dados = $res->fetch(PDO::FETCH_OBJ);

	} else {
		$$chave = 0;
	}

	//print_r($dados);
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
		<li class="breadcrumb-item"><a href="javascript:void(0);">Reoferta</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-repeat'></i> Prospecção
			<small>
				Cadastro de Prospects
			</small>
		</h1>
		<?php
			if(isset($_GET['id'])){
		?>
		<!-- <div class="subheader-title col-6 text-right">
			<a href="reoferta/inscritos/<?php echo $_GET['id']?>">
				<button type="button" class="btn btn-lg btn-primary waves-effect waves-themed">
					<span class="ni ni-users mr-3"></span>
					Verificar Inscritos
				</button>
			</a>
		</div> -->
		<?php
			}
		?>
	</div>

	<?php

		$desabilitar_edicao_carga_horaria = false;
		$desabilitar_edicao = false;
		if(isset($_GET['id'])){
		}
	?>
	<iframe class="d-none" name="dados" src="" style="border-bottom: solid red 1px; position: fixed; z-index: 999999999999; width: 90%; background-color: #fff; top: 0; left: 0; height: 300px"></iframe>

	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/marketing/prospeccao/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave?>" value="<?php echo $$chave ? $$chave : 0?>">
		<div class="row">
			<div class="col-xl-12">
			  	<div id="panel-2" class="panel">
				  	<div class="panel-hdr">
						<h2>
							1. Período/Colegiado
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">

						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="custom-control custom-checkbox">
									<input onclick="mostrar_curso('curso_graduacao')" <?php echo isset($dados->tipo) && $dados->tipo == 1 ? "checked" : ""?> type="radio" class="custom-control-input" id="invalidCheck" value="1" name="tipo">
									<label class="custom-control-label" for="invalidCheck">Graduação</label>
								</div><br>
								<div class="custom-control custom-checkbox">
									<input onclick="mostrar_curso('curso_pos')" <?php echo isset($dados->tipo)  && $dados->tipo == 2 ? "checked" : ""?> type="radio" class="custom-control-input" id="invalidCheck2" value="2" name="tipo">
									<label class="custom-control-label" for="invalidCheck2">Pós-Graduação</label>
								</div><br>
								<div class="form-row">
								
									<div class="col-md-12 mb-3" id="curso_graduacao" style="display: <?php echo isset($dados->tipo) && $dados->tipo == 1 ? "block" : "none"?>;">
										<label class="form-label" for="validationCustom03">Curso de Graduação<span class="text-danger">*</span></label>
										<?php

											if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][5]) || isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][6])){
												$where = " WHERE graduacao = 1 ";
											} else {
												$where = " WHERE graduacao = 1 AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
											}

											$sql = "SELECT
														id_departamento,
														departamento,
														campus.campus
													FROM
														coopex.departamento
														INNER JOIN coopex.departamento_pessoa USING ( id_departamento ) 
														INNER JOIN coopex.campus USING ( id_campus ) 
													WHERE
														id_campus = 1000000002
													AND
														graduacao = 1
													GROUP BY
														id_departamento 
													ORDER BY
														departamento";

											$curso = $coopex->query($sql);
										?>
										<select id="id_curso" name="id_curso" class="select2 form-control">
											<option value="">Selecione o Curso</option>
										<?php
											while($row = $curso->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if($dados->id_curso == $row->id_departamento){
													$selecionado = 'selected=""';
												}
										?>
											<option <?php echo isset($dados->id_curso) ? $selecionado : ""?> value="<?php echo $row->id_departamento?>"><?php echo utf8_encode(mb_strtoupper($row->departamento))?></option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											Selecione o curso
										</div>
									</div>

									<div class="col-md-12 mb-3" id="curso_pos" style="display: <?php echo isset($dados->tipo) && $dados->tipo == 2 ? "block" : "none"?>;">
										<label class="form-label" for="validationCustom03">Curso de Pós-Graduação<span class="text-danger">*</span></label>
										<?php

											if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][5]) || isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][6])){
												$where = " WHERE graduacao = 1 ";
											} else {
												$where = " WHERE graduacao = 1 AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
											}

											$sql = "SELECT
														id_pos_curso,
														curso
													FROM
														coopex.pos_curso
													WHERE
														ativo = 1
															ORDER BY
														curso";

											$curso = $coopex->query($sql);
										?>
										<select id="id_pos_curso" name="id_pos_curso" class="select2 form-control">
											<option value="">Selecione o Curso</option>
										<?php
											while($row = $curso->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if($dados->id_curso == $row->id_pos_curso){
													$selecionado = 'selected=""';
												}
										?>
											<option <?php echo isset($dados->id_curso) ? $selecionado : ""?> value="<?php echo $row->id_pos_curso?>"><?php echo utf8_encode($row->curso)?></option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											Selecione o curso
										</div>
									</div>
									
									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom03">Nome<span class="text-danger">*</span></label>
										<input value="<?php echo isset($dados->nome) ? $dados->nome : ""?>" name="nome" type="text" class="form-control" id="nome" placeholder="" value="">
										<div class="invalid-feedback">
											Please provide a valid state.
										</div>
									</div>

									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom03">CPF<span class="text-danger">*</span></label>
										<input data-inputmask="'mask': '999.999.999-99'" value="<?php echo isset($dados->cpf) ? $dados->cpf : ""?>" name="cpf" type="text" class="form-control" id="cpf" placeholder="" value="">
										<div class="invalid-feedback">
											Please provide a valid state.
										</div>
									</div>
																	
									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom03">WhatsApp<span class="text-danger">*</span></label>
										<input data-inputmask="'mask': '(99) 99999-9999'" value="<?php echo isset($dados->telefone) ? $dados->telefone : ""?>" name="telefone" type="text" class="form-control" id="telefone" placeholder="" value="">
										<div class="invalid-feedback">
											Please provide a valid state.
										</div>
									</div>
								
								</div>	
								

								<div class="panel-container show">
						<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
							<button class="btn btn-primary ml-auto" type="submit"><?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar"?></button>
						</div>
					</div>
				
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	
		

	
		<textarea class="d-none" name="cronograma" id="cronograma" rows="10" cos="100"></textarea>
	</form>

	


</main>

<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/datagrid/datatables/dataTables.editor.min.js"></script>
<script src="js/moment-with-locales.js"></script>
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<script>

	function mostrar_curso(div){
		$("#curso_graduacao").hide();
		$("#curso_pos").hide();
		$("#"+div).show();
		console.log(div)
	}
	function aprovacao_deferido(){
		$("#aprovacao_motivo").prop("disabled", true);
		$("#aprovacao_motivo").prop("required", false);
		$("#aprovacao_observacao").hide();
	}
	<?php echo isset($dados->id_parecer) && $dados->id_parecer == 3 ? 'aprovacao_indeferido()' : ""; ?>

		function reducao_aprovacao_indeferido(){
		$("#aprovacao_motivo_reducao").prop("disabled", false);
		$("#aprovacao_motivo_reducao").prop("required", true);
		$("#aprovacao_observacao_reducao").show();
	}
	function reducao_aprovacao_indeferido(){
		$("#aprovacao_motivo").prop("disabled", true);
		$("#aprovacao_motivo").prop("required", false);
		$("#aprovacao_observacao_reducao").hide();
	}
	<?php echo isset($dados->id_parecer_reducao) && $dados->id_parecer_reducao == 3 ? 'reducao_aprovacao_indeferido()' : ""; ?>

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

		
	
	//CARREGAS AS DISCIPLINAS EQUIVALENTE MEDIANTE PESQUISA DO USUÁRIO
	function carrega_disciplina_equivalente(){
		$.getJSON("modulos/reoferta/cadastro/ajax/carrega_disciplina_equivalente.php", {id_curso: $("#id_curso").val()})
		.done(function(json){
			$("#id_equivalente").empty();
			$("#id_equivalente").append("<option value=''>Seleciona a Disciplina</option>");
			$.each( json, function( i, item ) {
				//console.log(item);
				$("#id_equivalente").append('<option value="'+item.atc_id_atividade+'">'+item.atc_nm_atividade+'</option>');
				$("#id_equivalente").attr("disabled", false);
			});
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}



	$(document).ready(function(){

		//CARREGA OS DADOS DOS SELECTS DEPENDENTES QUANDO EDITAR O REGISTRO
		<?php
			if(isset($_GET['id'])){
				if(isset($dados->id_disciplina)){
					echo "carrega_disciplina(".$dados->id_disciplina.");";
				}
		
				
			}
		?>

		$(":input").inputmask();
		$('.select2').select2();


	

		//SELECT DISCIPLINA	
		$(".js-consultar-disciplina").select2({
			ajax:{
				url: "modulos/reoferta/cadastro/ajax/carrega_disciplina_equivalente.php",
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
						pagination:{
							more: (params.page * 30) < data.total_count
						}
					};
				},
				cache: true
			},
			escapeMarkup: function(markup){
				return markup;
			},
			minimumInputLength: 3,
			templateResult: formatoDisciplina,
			templateSelection: formatoTextoDisciplina
		});

		var total_global = 0;
		//SELECT USUÁRIO
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

		//TOTALIZADOR DE HORAS NO RODAPÉ DA TABELA	
		
		
	});

	// Example starter JavaScript for disabling form submissions if there are invalid fields
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