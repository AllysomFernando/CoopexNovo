<?php
	require_once("php/sqlsrv.php");

	$id_menu = 41;
	$chave	 = "id_projeto";

	if(isset($_GET['id'])){
		$$chave = $_GET['id'];

		$sql = "SELECT
					*
				FROM
					pos.projeto
				WHERE
					pos.projeto.excluido = 0
				AND	
					id_projeto = ".$_GET['id'];
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
		<li class="breadcrumb-item"><a href="javascript:void(0);">Projeto de Pós-Graduação</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-repeat'></i> Projeto de Pós-Graduação
			<small>
				Cadastro de Projeto de Pós-Graduação
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
	?>
	<div class="alert alert-primary">
		<div class="d-flex flex-start w-100">
			<div class="mr-2 hidden-md-down">
				<span class="icon-stack icon-stack-lg">
					<i class="base base-2 icon-stack-3x opacity-100 color-primary-500"></i>
					<i class="base base-2 icon-stack-2x opacity-100 color-primary-300"></i>
					<i class="fal fa-info icon-stack-1x opacity-100 color-white"></i>
				</span>
			</div>
			<div class="d-flex flex-fill">
				<div class="flex-fill">
					<span class="h5">Status de aprovação do projeto</span>
					<!-- <div class="progress">
						<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
					</div> -->
					<br><br>
					<ol>
						<?php
							echo '<li><b>'.converterDataHora($dados->data_cadastro).'</b> - Projeto cadastrado</li>';
							
							if($dados->enviado_aprovacao == 1){
								echo '<li><b>'.converterDataHora($dados->data_envio_aprovacao).'</b> - Projeto enviado para aprovação</li>';

								if($dados->parecer_pos == 0){
									echo '<li><span class="badge badge-warning">Aguardando aprovação do departamento de Pós-graduação</span></li>';
								} else if($dados->parecer_pos == 1){
									echo '<li><b>'.converterDataHora($dados->parecer_pos_data).'</b><span class="badge badge-warning">Projeto aprovado pelo departamento de Pós-graduação</span></li>';
								} else if($dados->parecer_pos == 2){
									echo '<li><span class="badge badge-danger">Projeto não aprovado pelo departamento de Pós-graduação</span></li>';
								}

								/*if($dados->parecer_pos == 1 && $dados->parecer_direcao == 0){
									echo '<li><b>'.$dados->parecer_pos_data.'</b><h4><span class="badge badge-danger">Aguardando aprovação da direção</span></h4><b>Motivo:</b> '.utf8_encode($dados->parecer_observacao).'</li>';
								} else if($dados->parecer_pos == 1){
									echo '<li><b>'.$dados->parecer_data.'</b> - Projeto aprovado pelo departamento de Pós-graduação</li>';
									$desabilitar_edicao = true;
								}
								if($dados->parecer_pos == 1 && $dados->parecer_direcao == 0){
									echo '<li><b>'.$dados->parecer_data.'</b><h4><span class="badge badge-danger">Reoferta não autorizada</span></h4><b>Motivo:</b> '.utf8_encode($dados->parecer_observacao).'</li>';
								}	*/
							} else {
								echo '<li><h4><span class="badge badge-warning">Projeto não enviado para aprovação final</span></h4></li>';
							}
							
						?>
                    	
					</ol>
				</div>
			</div>
		</div>
	</div>
	<?php
		}
	?>

	<iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 30%; background-color: #fff; top: 0; left: 0; height: 270px"></iframe>

	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/pos/projeto/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave?>" value="<?php echo $$chave ? $$chave : 0?>">
		<div class="row">
			<div class="col-xl-12">
			  	<div id="panel-2" class="panel">
				  	<div class="panel-hdr">
						<h2>
							1. Dados do Curso
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
									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom02">Nome do Curso<span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="curso" placeholder="" value="<?php echo isset($dados->curso) ? utf8_encode($dados->curso) : ""?>" required>
									</div>
								</div>
								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Área<span class="text-danger">*</span></label>
										<?php
											$sql = "SELECT
														*
													FROM
														pos.area
													ORDER BY
														area DESC";

											$area = $coopex->query($sql);
										?>
										<select id="id_area" name="id_area" class="select2 form-control" required="">
											<option value="">Selecione a Área</option>
										<?php
											while($row = $area->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if($dados->id_area == $row->id_area){
													$selecionado = 'selected=""';
												}
										?>	
											<option <?php echo isset($dados->id_area) ? $selecionado : ""?> value="<?php echo $row->id_area?>"><?php echo texto($row->area)?></option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											Selecione a área do curso
										</div>
									</div>
								</div>	
								<div class="form-row">	
									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom02">Coordenador Pedagógico <span class="text-danger"></span></label>
										<input type="hidden" name="id_pessoa" class="form-control" id="id_pessoa" placeholder="" value="<?php echo isset($dados->nome) ? $dados->nome : $_SESSION['coopex']['usuario']['id_pessoa']?>" >
										<input readonly="" type="text" class="form-control" placeholder="" value="<?php echo isset($dados->nome) ? $dados->nome : $_SESSION['coopex']['usuario']['nome']?>" >
									</div>
								</div>
								<hr>
								<div class="form-row form-group">
									<div class="col-md-6">
										<div class="custom-control custom-switch">
											<input type="hidden" id="select_valor_diferente_hidden" name="valor_diferente" value="<?php echo isset($dados->valor_diferente) && $dados->valor_diferente ? "true" : "false"?>">
											<input onchange="$('#select_valor_diferente_hidden').val(this.checked)" <?php echo isset($dados->valor_diferente) && $dados->valor_diferente ? "checked" : ""?> contenteditable="" type="checkbox" class="custom-control-input is-invalid" id="select_valor_diferente">

											<label class="custom-control-label" for="select_valor_diferente">Definir valores diferentes dos valores padrão</label>
										</div>
									</div>
								</div>	
								<div class="form-row form-group">	
									<div class="col-md-2 ">
										<label class="form-label" for="validationCustom02">Especialista <span class="text-danger">*</span></label>
										<input data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0'" <?php echo isset($dados->valor_diferente) && $dados->valor_diferente == 1 ? "" : "disabled"?> type="text" class="form-control valor_diferente" name="valor_especialista" placeholder="" value="<?php echo isset($dados->valor_diferente) && $dados->valor_diferente == 1 ? ($dados->valor_especialista) : "80"?>">
									</div>
									<div class="col-md-2 ">
										<label class="form-label" for="validationCustom02">Mestre <span class="text-danger">*</span></label>
										<input data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0'" <?php echo isset($dados->valor_diferente) && $dados->valor_diferente == 1 ? "" : "disabled"?> type="text" class="form-control valor_diferente" name="valor_mestre" placeholder="" value="<?php echo isset($dados->valor_diferente) && $dados->valor_diferente == 1 ? ($dados->valor_mestre) : "90"?>">
									</div>
									<div class="col-md-2 ">
										<label class="form-label" for="validationCustom02">Doutor <span class="text-danger">*</span></label>
										<input data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0'" <?php echo isset($dados->valor_diferente) && $dados->valor_diferente == 1 ? "" : "disabled"?> type="text" class="form-control valor_diferente" name="valor_doutor" placeholder="" value="<?php echo isset($dados->valor_diferente) && $dados->valor_diferente == 1 ? ($dados->valor_doutor) : "100"?>">
									</div>

								</div>
								<hr>
								<div class="form-row">
									<div class="col-md-2 mb-3">
										<label class="form-label" for="validationCustom02">Carga Horária<span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="carga_horaria" placeholder="" value="<?php echo isset($dados->carga_horaria) ? $dados->carga_horaria : ""?>" required>
									</div>
									<div class="col-md-2 mb-3">
										<label class="form-label" for="validationCustom02">Número de Vagas<span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="vagas" placeholder="" value="<?php echo isset($dados->vagas) ? $dados->vagas : ""?>" required>
									</div>
									<div class="col-md-8 mb-3">
										<label class="form-label" for="validationCustom02">Período de Realização<span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="periodo" placeholder="" value="<?php echo isset($dados->periodo) ? $dados->periodo : ""?>" required>
									</div>
								</div>

								<div class="form-row">
									<div class="col-md-4 mb-3">
										<label class="form-label" for="validationCustom02">Dias da Semana<span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="dias" placeholder="" value="<?php echo isset($dados->dias) ? utf8_encode($dados->dias) : ""?>" required>
									</div>
									<div class="col-md-4 mb-3">
										<label class="form-label" for="validationCustom02">Horário<span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="horario" placeholder="" value="<?php echo isset($dados->horario) ? utf8_encode($dados->horario) : ""?>" required>
									</div>
									<div class="col-md-4 mb-3">
										<label class="form-label" for="validationCustom02">Local<span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="local" placeholder="" value="<?php echo isset($dados->local) ? utf8_encode($dados->local) : "Centro Universitário da Fundação Assis Gurgacz - FAG"?>" required>
									</div>
								</div>

								<div class="form-row">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom02">Público Alvo<span class="text-danger">*</span></label>
										<textarea type="text" class="form-control" name="publico_alvo" placeholder="" required><?php echo isset($dados->publico_alvo) ? utf8_encode($dados->publico_alvo) : ""?></textarea>
									</div>
									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom02">Justificativa de Oferta do Curso<span class="text-danger">*</span></label>
										<textarea type="text" class="form-control" name="justificativa" placeholder="" required><?php echo isset($dados->justificativa) ? utf8_encode($dados->justificativa) : ""?></textarea>
									</div>
									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom02">Objetivos<span class="text-danger">*</span></label>
										<textarea type="text" class="form-control" name="objetivos" placeholder="" required><?php echo isset($dados->objetivos) ? utf8_encode($dados->objetivos) : ""?></textarea>
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
							4. Estrutura Curricular
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

						<div class="custom-control custom-checkbox" id="aprovacao_check">
							<input type="checkbox" class="custom-control-input" id="invalidCheck2" value="1" name="enviar_aprovacao">
							<label class="custom-control-label" for="invalidCheck2">Enviar para aprovação</label>
						</div>

						<button class="btn btn-primary ml-auto" type="submit"><?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar"?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<textarea class="d-none" name="cronograma" id="cronograma" rows="10" cols="100"></textarea>
	</form>

	<?php
		if(isset($_GET['id'])){
			if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][6])){
	?>
	<iframe class="d-none" name="aprovacao_dados" src="" style="position: fixed; z-index: 999999999999; width: 30%; background-color: #fff; top: 0; left: 0; height: 300px"></iframe>
	<form class="needs-validation" novalidate="" method="post" target="aprovacao_dados" action="modulos/reoferta/cadastro/aprovacao_dados.php">
		<input type="hidden" name="<?php echo $chave?>" value="<?php echo $$chave ? $$chave : 0?>">
		<input type="hidden" name="parecer_data_reducao" value="1">
		<input type="hidden" name="id_reoferta" id="disciplina" value="<?php echo isset($dados->id_reoferta) ? utf8_encode($dados->id_reoferta) : ""?>">
		<div id="panel-5" class="panel">
			<div class="panel-hdr">
				<h2>
					Redução de Carga Horária
				</h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<div class="mb-g text-center">
						<h5>Redução de <b><?php echo round(100 - ($dados->carga_horaria *100 / $dados->carga_horaria_disciplina), 2)?>%</b> da Carga Horária</h5>
						<div class="js-toggle-skin btn-group btn-group-toggle" data-toggle="buttons">
							<label class="btn btn-default <?php echo isset($dados->id_parecer_reducao) && $dados->id_parecer_reducao == 1 ? "active" : ""?>">
								<input type="radio" name="id_parecer_reducao" value="1" onchange="aprovacao_deferido()">
								<span class="hidden-sm-down">Aguardando</span><span class="hidden-sm-up">Opt 1</span>
							</label>
							<label class="btn btn-default <?php echo isset($dados->id_parecer_reducao) && $dados->id_parecer_reducao == 2 ? "active" : ""?>">
								<input type="radio" name="id_parecer_reducao" value="2" onchange="aprovacao_deferido()">
								<span class="hidden-sm-down">Deferido</span><span class="hidden-sm-up">Opt 2</span>
							</label>
							<label class="btn btn-default <?php echo isset($dados->id_parecer_reducao) && $dados->id_parecer_reducao == 3 ? "active" : ""?>">
								<input type="radio" name="id_parecer_reducao" value="3" onchange="aprovacao_indeferido()">
								<span class="hidden-sm-down">Indeferido</span><span class="hidden-sm-up">Opt 3</span>
							</label>
						</div>
						<br><br>
						<div class="form-group" id="aprovacao_observacao_reducao" style="display: none;">
							<label class="form-label" for="example-textarea">Motivo do Indeferimento</label>
							<textarea id="aprovacao_motivo_reducao" name="parecer_observacao_reducao" disabled="" class="form-control" rows="2"><?php echo isset($dados->parecer_observacao_reducao) ? $dados->parecer_observacao_reducao : ""?></textarea>
						</div>
						<button class="btn btn-primary ml-auto" type="submit">Salvar</button>
					</div>
				</div>
			</div>
		</div>
	</form>
	<?php
			}
		}
	?>

	<?php
		if(isset($_GET['id'])){
			if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][5])){

	?>
	<iframe class="d-none" name="aprovacao_dados" src="" style="position: fixed; z-index: 999999999999; width: 30%; background-color: #fff; top: 0; left: 0; height: 300px"></iframe>
	<form class="needs-validation" novalidate="" method="post" target="aprovacao_dados" action="modulos/reoferta/cadastro/aprovacao_dados.php">
		<input type="hidden" name="<?php echo $chave?>" value="<?php echo $$chave ? $$chave : 0?>">
		<input type="hidden" name="parecer_data" value="1">
		<input type="hidden" name="id_reoferta" id="disciplina" value="<?php echo isset($dados->id_reoferta) ? utf8_encode($dados->id_reoferta) : ""?>">
		<div id="panel-5" class="panel">
			<div class="panel-hdr">
				<h2>
					Aprovação da Reoferta
				</h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<?php
						if($dados->enviado_aprovacao == 1){
					?>
					<div class="mb-g text-center">
						<h5>Situação da Reoferta</h5>
						<div class="js-toggle-skin btn-group btn-group-toggle" data-toggle="buttons">
							<label class="btn btn-default <?php echo isset($dados->id_parecer) && $dados->id_parecer == 1 ? "active" : ""?>">
								<input type="radio" name="id_parecer" value="1" onchange="aprovacao_deferido()">
								<span class="hidden-sm-down">Aguardando</span><span class="hidden-sm-up">Opt 1</span>
							</label>
							<label class="btn btn-default <?php echo isset($dados->id_parecer) && $dados->id_parecer == 2 ? "active" : ""?>">
								<input type="radio" name="id_parecer" value="2" onchange="aprovacao_deferido()">
								<span class="hidden-sm-down">Deferido</span><span class="hidden-sm-up">Opt 2</span>
							</label>
							<label class="btn btn-default <?php echo isset($dados->id_parecer) && $dados->id_parecer == 3 ? "active" : ""?>">
								<input type="radio" name="id_parecer" value="3" onchange="aprovacao_indeferido()">
								<span class="hidden-sm-down">Indeferido</span><span class="hidden-sm-up">Opt 3</span>
							</label>
						</div>
						<br><br>
						<div class="form-group" id="aprovacao_observacao" style="display: none;">
							<label class="form-label" for="example-textarea">Motivo do Indeferimento</label>
							<textarea id="aprovacao_motivo" name="parecer_observacao" disabled="" class="form-control" rows="2"><?php echo isset($dados->parecer_observacao) ? utf8_encode($dados->parecer_observacao) : ""?></textarea>
						</div>
						<button class="btn btn-primary ml-auto" type="submit">Salvar</button>
					</div>
					<?php
						} else {
					?>
						<h5>Reoferta não enviada para aprovação</h5>
					<?php
						}
					?>
				</div>
			</div>
		</div>
	</form>
	<?php
			}
		}
	?>


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

	//CARREGA OS PERÍODOS DE REOFERTAS
	function carrega_periodo(id_periodo = ''){
		$("#carga_horaria_disciplina").val('');

		if($("#id_periodo").val()){
			$("#select_valor_diferente").attr("disabled", false);
			valor_diferente();
			
			$.getJSON("modulos/reoferta/cadastro/ajax/carrega_periodo.php", {id_periodo: $("#id_periodo").val()})
			.done(function(json){
				$("#pre_inscricao_data_inicial_fixo").val(json.pre_inscricao_data_inicial);
				$("#pre_inscricao_data_final_fixo").val(json.pre_inscricao_data_final);
				$("#inscricao_data_inicial_fixo").val(json.inscricao_data_inicial);
				$("#inscricao_data_final_fixo").val(json.inscricao_data_final);
				
			})
			.fail(function(jqxhr, textStatus, error) {
				var err = textStatus + ", " + error;
				console.log( "Request Failed: " + err );
			});

		} else {
			$("#select_valor_diferente").attr("disabled", true);
			$(".valor_diferente").attr("disabled", true);

			$("#pre_inscricao_data_inicial_fixo").val("");
			$("#pre_inscricao_data_final_fixo").val("");
			$("#inscricao_data_inicial_fixo").val("");
			$("#inscricao_data_final_fixo").val("");
		}
	}

	//HABILITA OS CAMPOS REFERENTES AO PERÍODO DIFERENTE DO PRE-DEFINIDO
	function valor_diferente(){
		if($("#select_valor_diferente").prop('checked')){
			$(".valor_diferente").attr("disabled", false);
			$(".valor_diferente").attr("required", true);
		} else {
			$(".valor_diferente").attr("disabled", true);
			$(".valor_diferente").attr("required", false);
			$("#pre_inscricao_data_inicial").focus();
		}
	}

	function recalculo(){
		$('#cronograma_tabela').DataTable().draw(false);
	}


	$(document).ready(function(){


		//CARREGA OS DADOS DOS SELECTS DEPENDENTES QUANDO EDITAR O REGISTRO
		<?php
			if(isset($_GET['id'])){
				if(isset($dados->id_periodo)){
					echo "carrega_periodo(".$dados->id_periodo.");";
				}
			}
		?>

		$(":input").inputmask();
		$('.select2').select2();

		$("#select_valor_diferente").change(function() {
			valor_diferente();
		});


		$("#id_periodo").change(function() {
			carrega_periodo();
		});



		//TOTALIZADOR DE HORAS NO RODAPÉ DA TABELA	
		$("#cronograma_tabela").append('<tfoot><tr role="row" class="odd"><td align="right" class="sorting_1" colspan="6"></td><td class="sorting_1" id="tempoTotal"><strong></strong></td></tr></tfoot>');

		// Column Definitions
		var columnSet = [
		{
			title: "ID",
			id: "id_disciplina",
			data: "id_disciplina",
			placeholderMsg: "Gerado automáticamente",
			"visible": false,
			"searchable": false,
			type: "readonly"
		},
		{
			title: "Disciplina",
			id: "disciplina",
			data: "disciplina",
			type: "text"
		},
		{
			title: "Docente",
			id: "docente",
			data: "docente",
			type: "text"
		},
		{
			title: "Titulação",
			id: "titulacao",
			data: "titulacao",
			type: "select",
			"options": [
				"Especialista",
				"Mestre",
				"Doutor"
			]
		},
		{
			title: "IES",
			id: "ies",
			data: "ies",
			type: "text"
		},
		{
			title: "Ementa",
			id: "ementa",
			data: "ementa",
			type: "textarea"
		},
		{
			title: "CH",
			id: "carga_horaria",
			data: "carga_horaria"
		}
		]

		/* start data table */
		var myTable = $('#cronograma_tabela').dataTable(
		{
			/* check datatable buttons page for more info on how this DOM structure works */
			dom: "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
			ajax: "modulos/pos/projeto/ajax/estrutura_curricular.php?id_projeto=<?php echo $id_projeto?>",

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

			"footerCallback": function ( row, data, start, end, display ) {
				var api = this.api(), data;
				var tempo_total = 0;
				console.log(data);
				for(i=0; i<data.length; i++){
					tempo_total += parseInt(data[i].carga_horaria);
				}

				$( api.column( 5 ).footer() ).html('<strong>CH TOTAL</strong>');
				$( api.column( 6 ).footer() ).html('<strong>'+tempo_total+'</strong>');
			},


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