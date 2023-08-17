asdfasdf
<?php
exit;
	require_once("php/sqlsrv.php");

	$id_menu = 41;
	$chave	 = "id_reoferta";

	if(isset($_GET['id'])){
		$$chave = $_GET['id'];

		//CARREGA DADOS DA REOFERTA
		$sql = "SELECT
					*,
					DATE_FORMAT( data_envio_aprovacao, '%d/%m/%Y - %H:%i:%s' ) AS data_envio_aprovacao,
					DATE_FORMAT( data_envio_aprovacao_reducao, '%d/%m/%Y - %H:%i:%s' ) AS data_envio_aprovacao_reducao,
					DATE_FORMAT( data_cadastro, '%d/%m/%Y - %H:%i:%s' ) AS data_cadastro,
					DATE_FORMAT( parecer_data, '%d/%m/%Y - %H:%i:%s' ) AS parecer_data, 
					DATE_FORMAT( parecer_data_reducao, '%d/%m/%Y - %H:%i:%s' ) AS parecer_data_reducao, 
					carga_horaria_disciplina > carga_horaria as reducao
				FROM
					coopex_reoferta.reoferta
					INNER JOIN coopex_reoferta.carga_horaria USING ( id_carga_horaria ) 
				WHERE
					coopex_reoferta.reoferta.excluido = 0
				AND	
					id_reoferta = ".$_GET['id'];
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
			<i class='subheader-icon fal fa-repeat'></i> Reofertas
			<small>
				Cadastro de Reofertas
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
					<span class="h5">Status de aprovação da reoferta</span>
					<!-- <div class="progress">
						<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
					</div> -->
					<br><br>
					<ol>
						<?php
							echo '<li><b>'.$dados->data_cadastro.'</b> - Reoferta cadastrada</li>';
							if($dados->reducao){ //SE EXISTIR REDUÇÃO DE CARGA HORÁRIA
								if($dados->enviado_aprovacao_reducao){ //SE TIVER SIDO ENVIADO PARA APROVAÇÃO
									echo '<li><b>'.$dados->data_envio_aprovacao_reducao.'</b> - Enviada para aprovação de redução de carga horária</li>';
									if($dados->id_parecer_reducao == 1){ //SE ESTIVER AGUARDANDO APROVAÇÃO DA REDUÇÃO
										echo '<li><h4><span class="badge badge-warning">Aguardando aprovação de redução de carga horária</span></h4></li>';
									} else {
										if($dados->id_parecer_reducao == 2){ //SE A REDUÇÃO ESTIVER APROVADA
											echo '<li><b>'.$dados->parecer_data_reducao.'</b> - Redução de carga horária autorizada</li>';
											$desabilitar_edicao_carga_horaria = true;
											if($dados->enviado_aprovacao == 1){ //SE TIVER SIDO ENVIADO PARA APROVAÇÃO	
												echo '<li><b>'.$dados->data_cadastro.'</b> - Enviada para aprovação final</li>';
												if($dados->id_parecer == 1){ //SE ESTIVER AGUARDANDO APROVAÇÃO
													echo '<li><h4><span class="badge badge-warning">Aguardando aprovação final da reoferta</span></h4></li>';
												} else if($dados->id_parecer == 2){ //SE FOR APROVADO
													echo '<li><b>'.$dados->parecer_data.'</b> - Reoferta autorizada</li>';
													$desabilitar_edicao = true;
												} else if($dados->id_parecer == 3){ //SE NÃO FOR APROVADO
													echo '<li><b>'.$dados->data_cadastro.'</b><h4><span class="badge badge-danger">Reoferta não autorizada</span></h4><b>Motivo:</b> '.utf8_encode($dados->parecer_observacao_reducao).'</li>';
												}	
											} else { //SE NÃO TIVER SIDO ENVIADA PARA APROVAÇÃO
												echo '<li><h4><span class="badge badge-warning">Reoferta não enviada para aprovação final</span></h4></li>';
											}
										} else { //SE NÃO FOR AUTORIZADA A REDUÇÃO DE CARGA HORÁRIA
											echo '<li><b>'.$dados->data_cadastro.'</b><h4><span class="badge badge-danger">Redução de carga horária não autorizada</span></h4><b>Motivo:</b> '.utf8_encode($dados->parecer_observacao_reducao).'</li>';
										}
									}
								} else {
									echo '<li><h4><span class="badge badge-warning">Reoferta não enviada para aprovação de redução de carga horária</span></h4></li>';
								}	
							} else {
								if($dados->enviado_aprovacao == 1){
									echo '<li><b>'.$dados->data_envio_aprovacao.'</b> - Enviada para aprovação final</li>';
									if($dados->id_parecer == 1){
										echo '<li><h4><span class="badge badge-warning">Aguardando aprovação final da reoferta</span></h4></li>';
									} else if($dados->id_parecer == 2){
										echo '<li><b>'.$dados->parecer_data.'</b> - Reoferta autorizada</li>';
										$desabilitar_edicao = true;
									} else if($dados->id_parecer == 3){
										echo '<li><b>'.$dados->parecer_data.'</b><h4><span class="badge badge-danger">Reoferta não autorizada</span></h4><b>Motivo:</b> '.utf8_encode($dados->parecer_observacao).'</li>';
									}	
								} else {
									echo '<li><h4><span class="badge badge-warning">Reoferta não enviada para aprovação final</span></h4></li>';
								}
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

	<iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 30%; background-color: #fff; top: 0; left: 0; height: 300px"></iframe>

	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/reoferta/cadastro/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave?>" value="<?php echo $$chave ? $$chave : 0?>">
		<div class="row">
			<div class="col-xl-12">
			  	<div id="panel-2" class="panel">
				  	<div class="panel-hdr">
						<h2>
							1. Período
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
										<label class="form-label" for="validationCustom03">Período da Reoferta<span class="text-danger">*</span></label>
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
														coopex_reoferta.periodo 
													ORDER BY
														id_periodo DESC";

											$periodo = $coopex->query($sql);
										?>
										<select <?php echo $desabilitar_edicao  ? 'disabled=""' : ""?> id="id_periodo" name="id_periodo" class="select2 form-control" required="">
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
								</div>	
								<div class="form-row">	
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Inicio da Pré-Matrícula <span class="text-danger"></span></label>
										<input readonly="" type="text" name="pre_inscricao_data_inicial_fixo" class="form-control" id="pre_inscricao_data_inicial_fixo" placeholder="" value="" >
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Fim da Pré-Matrícula <span class="text-danger"></span></label>
										<input readonly="" type="text" class="form-control" name="pre_inscricao_data_final_fixo" id="pre_inscricao_data_final_fixo" placeholder="" value="" >
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Inicio da Matrícula <span class="text-danger"></span></label>
										<input readonly="" type="text" class="form-control" name="inscricao_data_inicial_fixo" id="inscricao_data_inicial_fixo" placeholder="" value="" >
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Fim da Matrícula <span class="text-danger"></span></label>
										<input readonly="" type="text" class="form-control" name="inscricao_data_final_fixo" id="inscricao_data_final_fixo" placeholder="" value="">
									</div>
								</div>
								<hr>
								<div class="form-row form-group">
									<div class="col-md-6 mb-3">
										<div class="custom-control custom-switch">
											<input type="hidden" id="select_periodo_diferente_hidden" name="periodo_diferente" value="<?php echo isset($dados->periodo_diferente) && $dados->periodo_diferente ? "true" : "false"?>">
											<input disabled="" onchange="$('#select_periodo_diferente_hidden').val(this.checked)" <?php echo isset($dados->periodo_diferente) && $dados->periodo_diferente ? "checked" : ""?> contenteditable="" type="checkbox" class="custom-control-input is-invalid" id="select_periodo_diferente">

											<label class="custom-control-label" for="select_periodo_diferente">Utilizar período diferente do pré-definido</label>
										</div>
									</div>
								</div>	
								<div class="form-row form-group">	
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Inicio da Pré-Matrícula <span class="text-danger">*</span></label>
										<input data-inputmask="'mask': '99/99/9999'" disabled type="text" class="form-control periodo_diferente" name="pre_inscricao_data_inicial" placeholder="" value="<?php echo isset($dados->periodo_diferente) && $dados->periodo_diferente == 1 ? converterData($dados->pre_inscricao_data_inicial) : ""?>">
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Fim da Pré-Matrícula <span class="text-danger">*</span></label>
										<input data-inputmask="'mask': '99/99/9999'" disabled type="text" class="form-control periodo_diferente" name="pre_inscricao_data_final" placeholder="" value="<?php echo isset($dados->periodo_diferente) && $dados->periodo_diferente == 1 ? converterData($dados->pre_inscricao_data_final) : ""?>">
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Inicio da Matrícula <span class="text-danger">*</span></label>
										<input data-inputmask="'mask': '99/99/9999'" disabled type="text" class="form-control periodo_diferente" name="inscricao_data_inicial" placeholder="" value="<?php echo isset($dados->periodo_diferente) && $dados->periodo_diferente == 1 ? converterData($dados->inscricao_data_inicial) : ""?>">
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Fim da Matrícula <span class="text-danger">*</span></label>
										<input data-inputmask="'mask': '99/99/9999'" disabled type="text" class="form-control periodo_diferente" name="inscricao_data_final" placeholder="" value="<?php echo isset($dados->periodo_diferente) && $dados->periodo_diferente == 1 ? converterData($dados->inscricao_data_final) : ""?>" required>
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
							2. Disciplina
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
										<label class="form-label" for="validationCustom03">Curso <span class="text-danger">*</span></label>
										<?php

											if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][5]) || isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][6])){
												$where = " WHERE graduacao = 1 ";
											} else {
												$where = " WHERE graduacao = 1 AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
											}

											$sql = "SELECT
														id_departamento,
														departamento 
													FROM
														coopex.departamento
														INNER JOIN coopex.departamento_pessoa USING ( id_departamento ) 
														$where 
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
											<option <?php echo isset($dados->id_departamento) ? $selecionado : ""?> value="<?php echo $row->id_departamento?>"><?php echo utf8_encode($row->departamento)?></option>
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
									<div class="col-md-4 mb-3">
										<label class="form-label" for="validationCustom03">Disciplina <span class="text-danger">*</span></label>
										<select <?php echo $desabilitar_edicao_carga_horaria || $desabilitar_edicao  ? 'disabled=""' : ""?> id="id_disciplina" name="id_disciplina" onchange="$('#disciplina').val($(this).select2('data')[0].text);" disabled="" class="select2 form-control" required="">
											<option value="">Selecione a Disciplina</option>
										</select>
										<input type="hidden" name="disciplina" id="disciplina" value="<?php echo isset($dados->disciplina) ? utf8_encode($dados->disciplina) : ""?>">
										<div class="invalid-feedback">
											Selecione a disciplina
										</div>
									</div>
									<div class="col-md-4 mb-3">
										<label class="form-label" for="validationCustom02">Carga Horária Original da Disciplina</label>
										<input readonly="" type="text" class="form-control" id="carga_horaria_disciplina" name="carga_horaria_disciplina" placeholder="" value="" required>
										<div class="valid-feedback">
											OK!
										</div>
									</div>
									<div class="col-md-4 mb-3">
										<label class="form-label" for="validationCustom03">Carga Horária da Reoferta <span class="text-danger">*</span></label>
										<?php
											$sql = "SELECT
														id_carga_horaria,
														carga_horaria 
													FROM
														coopex_reoferta.carga_horaria 

													ORDER BY
														carga_horaria";

											$valor = $coopex->query($sql);
										?>
										<select <?php echo $desabilitar_edicao_carga_horaria || $desabilitar_edicao ? 'readonly="readonly"' : ""?> id="id_carga_horaria_reoferta" name="id_carga_horaria" class="select2 form-control" required="">
											<option value="">Selecione a Carga Horária</option>
										<?php
											/*while($row = $valor->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if($dados->id_carga_horaria == $row->id_carga_horaria){
													$selecionado = 'selected=""';
												}*/
										?>	
											<option <?php echo isset($dados->id_carga_horaria) ? $selecionado : ""?> value="<?php echo $row->id_carga_horaria?>"><?php echo utf8_encode($row->carga_horaria)?></option>
										<?php
											//}
										?>	
										</select>
										<div class="invalid-feedback">
											Selecione a carga horária da reoferta
										</div>
									</div>
								</div>
								
								<div class="form-row">
									<div class="col-md-12 mb-3">
										<div class="form-group">
											<label class="form-label" for="select2-ajax">
												Disciplinas equivalentes
											</label>
											<select name="id_disciplina_equivalente[]" multiple="multiple" data-placeholder="Selecione as disciplinas equivalentes..." class="js-consultar-disciplina form-control">
												<?php
													if(isset($array_disciplina_equivalente)){
														$sql = "SELECT
																	atc_id_atividade,
																	atc_cd_atividade,
																	atc_nm_atividade
																FROM
																	academico..ATC_atividade_curricular
																	INNER JOIN academico..GCR_grade_curricular ON gcr_id_atividade = atc_id_atividade
																	INNER JOIN academico..CRR_curriculo ON gcr_id_curriculo = crr_id_curriculo
																	INNER JOIN academico..CRS_curso ON crr_id_curso = crs_id_curso 
																WHERE
																	atc_cd_atividade IN ('$array_disciplina_equivalente')";	
														$res = mssql_query($sql);

													 	while($row = mssql_fetch_assoc($res)){
												?>
														<option value="<?php echo trim($row['atc_cd_atividade'])?>"><?php echo $row['atc_cd_atividade']." - ".trim(utf8_encode($row['atc_nm_atividade']))?></option>
												<?php		
												 		}
												?>
												 	<script>$('.js-consultar-disciplina').val(['<?php echo $array_disciplina_equivalente_select?>']).trigger('change');</script>
												<?php		
												 	}
												?>	
											</select>
							
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
							3. Reoferta
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
									<div class="col-md-6 mb-3">
										<div class="form-group">
											<label class="form-label" for="select2-ajax">
												Docente da reoferta
											</label>
											

											<select <?php echo $desabilitar_edicao  ? 'disabled=""' : ""?> name="id_docente" data-placeholder="Selecione o docente da disciplina" class="js-consultar-usuario form-control" >
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
										<div class="form-group">
											<label class="form-label" for="select2-ajax">
												Oferta especial para acadêmicos específicos
											</label>
											<select multiple="multiple" id="id_academico_autorizado" name="id_academico_autorizado[]" data-placeholder="Selecione os acadêmicos autorizados..." class="js-consultar-usuario form-control">
												<?php
													if(isset($array_academico_autorizado)){
														$sql = "SELECT DISTINCT
																	id_pessoa,
																	nome 
																FROM
																	integracao..view_integracao_usuario 
																WHERE
																	id_pessoa IN ($array_academico_autorizado)";	
														$res = mssql_query($sql);

													 	while($row = mssql_fetch_assoc($res)){
												?>
														<option value="<?php echo $row['id_pessoa']?>"><?php echo $row['id_pessoa']." - ".trim(utf8_encode($row['nome']))?></option>
												<?php		
												 		}
												?>
													<script>$('#id_academico_autorizado').val(['<?php echo $array_academico_autorizado_select?>']).trigger('change');</script>
												<?php	
												 	}
												?>	
											</select>
											
										</div>	
									</div>
								</div>
								<div class="form-row">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom02">Local da Reoferta <span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="local" placeholder="" value="<?php echo isset($dados->local) ? $dados->local : ""?>" required>
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
							4. Cronograma
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content p-0">

							<div class="panel-content">
								<div id="cronograma_aguardando" class="alert alert-success alert-dismissible" style="display:<?php echo isset($_GET['id']) && $dados->id_parecer_reducao == "1" ? "" : "none"?>;" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true"><i class="fal fa-times"></i></span>
									</button>
									<div class="d-flex align-items-center">
										<div class="alert-icon width-1">
											<i class="fal fa-sync fs-xl fa-spin"></i>
										</div>
										<div class="flex-1">
											<span class="h6 m-0 fw-700">Reoferta com redução de carga horária</span>
											<p>Só é possível lançar o cronograma após a aprovação da redução de carga carga horária. Aguarde a aprovação!</p>
										</div>
									</div>
								</div>

								<div class="form-row" id="cronograma_container" style="display:<?php echo $dados->id_parecer_reducao == "1" ? "none" : ""?>;">
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
						<?php 
							if(isset($_GET['id'])){
								if($dados->reducao && !$dados->enviado_aprovacao_reducao){
						?>	
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="invalidCheck" value="1" name="enviar_aprovacao_reducao">
								<label class="custom-control-label" for="invalidCheck">Enviar para aprovação de redução de carga horária</label>
							</div>
						<?php
								} else if((!$dados->enviado_aprovacao && $dados->id_parecer_reducao == 2) || !$dados->enviado_aprovacao){
						?>
							<div class="custom-control custom-checkbox" id="aprovacao_check">
								<input type="checkbox" class="custom-control-input" id="invalidCheck2" value="1" name="enviar_aprovacao">
								<label class="custom-control-label" for="invalidCheck2">Enviar para aprovação</label>
							</div>
						<?php			
								}
							} else {
						?>
							<div class="custom-control custom-checkbox" id="reducao_check" style="display: none;">
								<input type="checkbox" class="custom-control-input" id="invalidCheck" value="1" name="enviar_aprovacao_reducao">
								<label class="custom-control-label" for="invalidCheck">Enviar para aprovação de redução de carga horária</label>
							</div>
							<div class="custom-control custom-checkbox" id="aprovacao_check">
								<input type="checkbox" class="custom-control-input" id="invalidCheck2" value="1" name="enviar_aprovacao">
								<label class="custom-control-label" for="invalidCheck2">Enviar para aprovação</label>
							</div>
						<?php		
							}
						?>
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

	function reducao_carga_horaria(){
		if(parseInt($("#carga_horaria_disciplina").val()) > parseInt($("#id_carga_horaria_reoferta option:selected").text())){
			$("#cronograma_container").hide();
			$("#cronograma_aguardando").show();
			$("#enviar_aprovacao_reducao").prop("disabled", false);
			$("#reducao_check").show();
			$("#aprovacao_check").hide();
		} else {
			$("#cronograma_container").show();
			$("#cronograma_aguardando").hide();
			$("#enviar_aprovacao_reducao").prop("disabled", true);
			$("#reducao_check").hide();
			$("#aprovacao_check").show();
		}
	}

	//CARREGA A CARGA HORÁRIA DA DISCIPLINA SELECIONADA
	function carrega_carga_horaria_disciplina(id_disciplina = ''){
		if(!id_disciplina){
			id_disciplina = $("#id_disciplina").val()
		}

		$.getJSON("modulos/reoferta/cadastro/ajax/carrega_carga_horaria_disciplina.php", {id_disciplina: id_disciplina})
		.done(function(json){
			$("#carga_horaria_disciplina").val(json);
			<?php 
				if(isset($_GET['id'])){
					if(!$dados->reducao){
			?>
				reducao_carga_horaria();
			<?php
					}
				}	
			?>
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}

	//CARREGAS AS DISCIPLINAS REFERENTES AO CURSO SELECIONADO
	function carrega_disciplina(id_disciplina = ''){
		$("#carga_horaria_disciplina").val('');

		$("#id_disciplina").attr("disabled", true);
		
		$.getJSON("modulos/reoferta/cadastro/ajax/carrega_disciplina.php", {id_curso: $("#id_curso").val()})
		.done(function(json){
			$("#id_disciplina").empty();
			$("#id_disciplina").append("<option value=''>Seleciona a Disciplina</option>");
			$.each( json, function( i, item ) {
				$("#id_disciplina").append('<option value="'+item.atc_id_atividade+'">'+item.atc_nm_atividade+'</option>');
				<?php 
					if(isset($_GET['id'])){
						if($dados->id_parecer_reducao > 1){
				?>
						$("#id_disciplina").attr("disabled", true);
				<?php
						} else if($dados->id_parecer > 1){
				?>
						$("#id_disciplina").attr("disabled", true);
				<?php			
						}
					} else {
				?>
						$("#id_disciplina").attr("disabled", false);
				<?php		
					}
				?>
			});
			if(id_disciplina){
				$('#id_disciplina option[value='+id_disciplina+']').attr('selected','selected');

			}
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}


	//CARREGAS CARGA HORÁRIA
	function carrega_carga_horaria_reoferta(id_disciplina = '', id_carga_horaria = ''){
		$("#id_carga_horaria_reoferta").val('');

		//$("#id_carga_horaria_reoferta").attr("disabled", true);
		
		$.getJSON("modulos/reoferta/cadastro/ajax/carrega_carga_horaria_reoferta.php", {id_curso: $("#id_curso").val()})
		.done(function(json){
			$("#id_carga_horaria_reoferta").empty();
			$("#id_carga_horaria_reoferta").append("<option value=''>Seleciona a Carga Horária</option>");
			$.each( json, function( i, item ) {
				$("#id_carga_horaria_reoferta").append('<option value="'+item.id_carga_horaria+'">'+item.carga_horaria+'</option>');
				
			});
			//$("#id_carga_horaria_reoferta").attr("disabled", false);
			if(id_carga_horaria){
				$('#id_carga_horaria_reoferta option[value='+id_carga_horaria+']').attr('selected','selected');

			}
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}

	//CARREGA OS PERÍODOS DE REOFERTAS
	function carrega_periodo(id_periodo = ''){
		$("#carga_horaria_disciplina").val('');

		if($("#id_periodo").val()){
			$("#select_periodo_diferente").attr("disabled", false);
			periodo_diferente();
			
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
			$("#select_periodo_diferente").attr("disabled", true);
			$(".periodo_diferente").attr("disabled", true);

			$("#pre_inscricao_data_inicial_fixo").val("");
			$("#pre_inscricao_data_final_fixo").val("");
			$("#inscricao_data_inicial_fixo").val("");
			$("#inscricao_data_final_fixo").val("");
		}
	}

	//HABILITA OS CAMPOS REFERENTES AO PERÍODO DIFERENTE DO PRE-DEFINIDO
	function periodo_diferente(){
		if($("#select_periodo_diferente").prop('checked')){
			$(".periodo_diferente").attr("disabled", false);
			$(".periodo_diferente").attr("required", true);
		} else {
			$(".periodo_diferente").attr("disabled", true);
			$(".periodo_diferente").attr("required", false);
			$("#pre_inscricao_data_inicial").focus();
		}
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

	function recalculo(){
		$('#cronograma_tabela').DataTable().draw(false);
	}


	$(document).ready(function(){

		//setInterval(recalculo, 1000);

		

		//CARREGA OS DADOS DOS SELECTS DEPENDENTES QUANDO EDITAR O REGISTRO
		<?php
			if(isset($_GET['id'])){
				if(isset($dados->id_disciplina)){
					echo "carrega_disciplina(".$dados->id_disciplina.");";
					echo "carrega_carga_horaria_disciplina(".$dados->id_disciplina.");";
					echo "carrega_carga_horaria_reoferta(".$dados->id_disciplina.",".$dados->id_carga_horaria.");";
				}
				if(isset($dados->id_periodo)){
					echo "carrega_periodo(".$dados->id_periodo.");";
				}
			}
		?>

		$(":input").inputmask();
		$('.select2').select2();

		$("#select_periodo_diferente").change(function() {
			periodo_diferente();
		});

		$("#id_carga_horaria_reoferta").change(function() {
			reducao_carga_horaria();
		});

		$("#select_disciplina_equivalente").change(function() {
			carrega_disciplina_equivalente();
		});

		$("#id_periodo").change(function() {
			carrega_periodo();
		});

		$("#id_curso").change(function() {
			carrega_disciplina();
			carrega_carga_horaria_reoferta();
		});

		$("#id_disciplina").change(function() {
			carrega_carga_horaria_disciplina();
		});

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
		$("#cronograma_tabela").append('<tfoot><tr role="row" class="odd"><td class="sorting_1" tabindex="0"></td><td class="sorting_1" tabindex="0"></td><td></td><td><strong></strong></td><td id="tempoTotal"><strong>04:48</strong></td></tr></tfoot>');

		// Column Definitions
		var columnSet = [
		{
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
			title: "Horas",
			id: "horas",
			data: "horas",
			type: "readonly",
			placeholderMsg: "-",
			defaultValue: "0"
		}]

		/* start data table */
		var myTable = $('#cronograma_tabela').dataTable(
		{
			/* check datatable buttons page for more info on how this DOM structure works */
			dom: "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
			ajax: "modulos/reoferta/cadastro/ajax/cronograma.php?id_reoferta=<?php echo $id_reoferta?>",

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
			}],
			columnDefs: [
				{
					targets: 4,
					render: function(data, type, full, meta){
						//console.log(full);
						return subtraiHora(full.horario_inicio, full.horario_termino);
					}
				}, 
				{
					targets: 1,
					render: function(data, type, full, meta){
						return moment(data).format('DD/MM/YYYY');
					},
					editorOnChange : function(event, altEditor) {

						console.log(event, altEditor);
						} 
				},
			],

			"footerCallback": function ( row, data, start, end, display ) {
				var api = this.api(), data;
				var tempo_total = 0;
				console.log(data);
				for(i=0; i<data.length; i++){
					temp = subtraiHora(data[i].horario_inicio, data[i].horario_termino);
					tempo_total += moment.duration(temp).asMinutes();
				}
					
				var dur = moment.duration(tempo_total, 'minutes');
				var hours = Math.floor(dur.asHours());
				var mins  = Math.floor(dur.asMinutes()) - hours * 60;
				var result = ((hours > 9) ? hours : ("0"+hours)) + ":" + ((mins > 9) ? mins : ("0"+mins));

				$( api.column( 3 ).footer() ).html('<strong>TOTAL</strong>');
				$( api.column( 4 ).footer() ).html('<strong>'+result+'</strong>');
			},
			columnDefs: [
				{
					targets: 4,
					render: function(data, type, full, meta){
						return subtraiHora(full.horario_inicio, full.horario_termino);
					}
				}, 
				{
					targets: 1,
					render: function(data, type, full, meta){
						return moment(data).format('DD/MM/YYYY');
					},
					editorOnChange : function(event, altEditor) {
						//console.log(event, altEditor);
					} 
				},
			],

			/* default callback for insertion: mock webservice, always success */
			onAddRow: function(dt, rowdata, success, error){
				success(rowdata);
				$("#cronograma").append(";i"+JSON.stringify(rowdata, null, 4));
			},
			onEditRow: function(dt, rowdata, success, error){
				success(rowdata);
				$("#cronograma").append(";u"+JSON.stringify(rowdata, null, 4));
			},
			onDeleteRow: function(dt, rowdata, success, error){
				success(rowdata);
				$("#cronograma").append(";d"+JSON.stringify(rowdata, null, 4));
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