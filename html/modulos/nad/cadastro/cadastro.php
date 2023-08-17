<?php
	require_once("php/sqlsrv.php");

	$id_menu = 22;
	$chave	 = "id_pratica_docente";

	if(isset($_GET['id'])){
		$$chave = $_GET['id'];

		//CARREGA DADOS DA REOFERTA
		$sql = "SELECT
					*
				FROM
					coopex_nad.pratica_docente
				WHERE
					coopex_nad.pratica_docente.excluido = 0
				AND	
					id_pratica_docente = ".$_GET['id'];
		$res = $coopex->query($sql);
		$dados = $res->fetch(PDO::FETCH_OBJ);

		//CARREGA DADOS DAS DISCIPLINAS EQUIVALENTE
		$sql = "SELECT
					id_disciplina
				FROM
					coopex_reoferta.disciplina_equivalente 
				WHERE
					id_reoferta = ".$_GET['id'];

		$res = $coopex->query($sql);

		$array = array();
		while($row = $res->fetch(PDO::FETCH_OBJ)){
			array_push($array, $row->id_disciplina);
		}
		$array_disciplina_equivalente = implode("','", $array);
		$array_disciplina_equivalente_select = implode("','", $array);
		

		//CARREGA DADOS DOS ACADÊMICOS AUTORIZADOS
		$sql = "SELECT
					id_usuario
				FROM
					coopex_reoferta.academico_autorizado 
				WHERE
					id_reoferta = ".$_GET['id'];

		$res = $coopex->query($sql);

		$array = array();
		while($row = $res->fetch(PDO::FETCH_OBJ)){
			array_push($array, $row->id_usuario);
		}
		$array_academico_autorizado = implode(",", $array);
		$array_academico_autorizado_select = implode("','", $array);

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
			<i class='subheader-icon fal fa-repeat'></i> Publicações Docentes
			<small>
				Cadastro de Práticas Docentes
			</small>
		</h1>
		<?php
			if(isset($_GET['id'])){
		?>
		<div class="subheader-title col-6 text-right">
			<a href="reoferta/inscritos/<?php echo $_GET['id']?>">
				<button type="button" class="btn btn-lg btn-primary waves-effect waves-themed">
					<span class="ni ni-users mr-3"></span>
					Verificar Inscritos
				</button>
			</a>
		</div>
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
	<iframe class="d-none" name="dados" src="" style="border-bottom: solid red 1px; position: fixed; z-index: 999999999999; width: 30%; background-color: #fff; top: 0; left: 0; height: 300px"></iframe>

	<form class="needs-validation" novalidate="" enctype="multipart/form-data" method="post" target="dados" action="modulos/nad/cadastro/cadastro_dados.php">
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
								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Período<span class="text-danger">*</span></label>
										<?php
											$sql = "SELECT
														id_periodo,
														periodo,
														ativo
													FROM
														coopex_nad.periodo 
													ORDER BY
														id_periodo DESC";

											$periodo = $coopex->query($sql);
										?>
										<select <?php echo $desabilitar_edicao ? 'disabled=""' : ""?> id="id_periodo" name="id_periodo" class="select2 form-control" required="">
											<option value="">Selecione o Período</option>
										<?php
											while($row = $periodo->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if($dados->id_periodo == $row->id_periodo){
													$selecionado = 'selected=""';
												}
										?>	
											<option <?php echo isset($dados->id_periodo) ? $selecionado : ""?> <?php echo !$row->ativo ? "disabled" : ""?> value="<?php echo $row->id_periodo?>"><?php echo $row->periodo?> <?php echo !$row->ativo ? "(Inativo)" : "(Atual)"?></option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											Selecione o período da reoferta
										</div>
									</div>
									<div class="col-md-9 mb-3">
										<label class="form-label" for="validationCustom03">Temática<span class="text-danger">*</span></label>
										<?php
											$sql = "SELECT
														* 
													FROM
														coopex_nad.tema 
													ORDER BY
														tema DESC";

											$tema = $coopex->query($sql);
										?>
										<select id="id_tema" name="id_tema" class="select2 form-control" required="">
											<option value="">Selecione a Temática</option>
										<?php
											while($row = $tema->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if($dados->id_tema == $row->id_tema){
													$selecionado = 'selected=""';
												}
										?>	
											<option <?php echo isset($dados->id_tema) ? $selecionado : ""?> <?php echo !$row->ativo ? "disabled" : ""?> value="<?php echo $row->id_tema?>"><?php echo texto($row->tema)?> <?php echo !$row->ativo ? "(Inativo)" : "(Atual)"?></option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											Selecione o período da reoferta
										</div>
									</div>



									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom03">Curso <span class="text-danger">*</span></label>
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
												
													GROUP BY
														id_departamento 
													ORDER BY
														departamento";

											$curso = $coopex->query($sql);
										?>
										<select <?php echo $desabilitar_edicao_carga_horaria || $desabilitar_edicao  ? 'disabled=""' : ""?> id="id_curso" name="id_departamento" class="select2 form-control" required="">
											<option value="">Selecione o Curso</option>
										<?php
											while($row = $curso->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if($dados->id_departamento == $row->id_departamento){
													$selecionado = 'selected=""';
												}
										?>
											<option <?php echo isset($dados->id_departamento) ? $selecionado : ""?> value="<?php echo $row->id_departamento?>"><?php echo utf8_encode($row->departamento)." - ".utf8_encode($row->campus)?></option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											Selecione o curso
										</div>
									</div>


								</div>	
								<div class="custom-control custom-checkbox">
									<input <?php echo isset($dados->fag) ? "checked" : ""?> type="checkbox" class="custom-control-input" id="invalidCheck" value="1" name="fag">
									<label class="custom-control-label" for="invalidCheck">Centro Universitário FAG</label>
								</div><br>
								<div class="custom-control custom-checkbox">
									<input <?php echo isset($dados->fag_toledo) ? "checked" : ""?> type="checkbox" class="custom-control-input" id="invalidCheck2" value="2" name="fag_toledo">
									<label class="custom-control-label" for="invalidCheck2">Faculdade Assis Gurgacz - Toledo</label>
								</div><br>
								<div class="custom-control custom-checkbox">
									<input <?php echo isset($dados->dom_bosco) ? "checked" : ""?> type="checkbox" class="custom-control-input" id="invalidCheck3" value="3" name="dom_bosco">
									<label class="custom-control-label" for="invalidCheck3">Faculdade Dom Bosco</label>
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
							2. Formato
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="frame-wrap">
                                    <div class="demo">

										<script type="text/javascript">
											function mostrar_campo(campo){
                                    			$(".campo_formato").hide();
                                    			$("#"+campo).show();
                                    		}
                                    	</script>

                                    	<?php
											$sql = "SELECT
														* 
													FROM
														coopex_nad.formato WHERE ativo = 1 order by formato";

											$formato = $coopex->query($sql);
								
											while($row = $formato->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if(isset($dados)){
													if($dados->id_formato == $row->id_formato){
														$selecionado = 'checked';
													}
												}
										?>	
											<div class="custom-control custom-radio">
	                                            <input <?php echo $selecionado?> value="<?php echo $row->id_formato?>" type="radio" class="custom-control-input" id="defaultUncheckedRadio<?php echo texto($row->id_formato)?>" name="id_formato">
	                                            <label onclick="mostrar_campo('<?php echo $row->tipo?>')" class="custom-control-label" for="defaultUncheckedRadio<?php echo texto($row->id_formato)?>"><?php echo texto($row->formato)?></label>
	                                        </div>
										<?php
											}
										?>

                                        <div class="form-row campo_formato" id="arquivo">
											<div class="col-md-12 mb-3">
												<div class="form-group">
		                                            <label class="form-label" for="example-fileinput">Selecione o arquivo</label>
		                                            <input <?php echo isset($dados->arquivo) ? $dados->arquivo : ""?> name="arquivo" type="file" id="example-fileinput" class="form-control-file">
		                                        </div>
											</div>
										</div>

		                                <div class="form-row campo_formato" id="video">
											<div class="col-md-12 mb-3">
												<label class="form-label" for="validationCustom03">Link do vídeo (YouTube, Vimeo...) <span class="text-danger">*</span></label>
												<input value="<?php echo isset($dados->video) ? $dados->video : ""?>" name="video" type="text" class="form-control" id="pre_inscricao_data_inicio_fixo" placeholder="" value="">
												<div class="invalid-feedback">
													Please provide a valid state.
												</div>
											</div>
										</div>
                                    </div>
                                </div>
							</div>
						</div>
					</div>
					<?php
						if(!isset($dados)){
					?>
					<div class="panel-container show">
						<div class="panel-content ">
							<div class="alert alert-primary">
	                            <div class="d-flex flex-start w-100">
	                                <div class="mr-2 hidden-md-down">
	                                    <span class="icon-stack icon-stack-lg">
	                                        <i class="base base-6 icon-stack-3x opacity-100 color-primary-500"></i>
	                                        <i class="base base-10 icon-stack-2x opacity-100 color-primary-300 fa-flip-vertical"></i>
	                                        <i class="fal fa-info icon-stack-1x opacity-100 color-white"></i>
	                                    </span>
	                                </div>
	                               
	                                <div class="d-flex flex-fill">
	                                    <div class="flex-fill">
	                                        <span class="h5"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Declaração de Autoria</font></font></span>
	                                        <p><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">
	                                        Declaro a autoria dos conteúdos contidos nesta publicação e a sua conformidade com as diretrizes previstas no respectivo edital. Estou ciente de que as produções textuais e/ou audiovisuais poderão integrar uma publicação em formato digital, a ser organizada pelo Centro Universitário Assis Gurgacz, FAG Toledo e Faculdade Dom Bosco.</font></font></p>
	                                        <div class="custom-control custom-checkbox">
												<input required="" type="checkbox" class="custom-control-input" id="invalidCheck8" value="1" name="termo">
												<label class="custom-control-label" for="invalidCheck8">Aceito os termos</label>
											</div><br>
	                                    </div>
	                                </div>
	                                
	                            </div>
	                        </div>
						</div>
					</div>
					<?php
						}
					?>
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

	function aprovacao_indeferido(){
		$("#aprovacao_motivo").prop("disabled", false);
		$("#aprovacao_motivo").prop("required", true);
		$("#aprovacao_observacao").show();
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