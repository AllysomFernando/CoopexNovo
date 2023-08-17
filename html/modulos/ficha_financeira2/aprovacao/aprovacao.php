<?php session_start();
	require_once("php/sqlsrv.php");

	require_once("modulos/ficha_financeira/funcoes_sagres.php");


	$_SESSION['ficha_financeira']['carga_horaria'] = 0;

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	$id_menu = 30;
	$chave	 = "id_ficha_financeira";

	unset($_SESSION['ficha_financeira']);

	$_SESSION['ficha_financeira']['carga_horaria'] 								= 0;
	$_SESSION['ficha_financeira']['carga_horaria_pacote'] 						= 0;
	$_SESSION['ficha_financeira']['carga_horaria_disciplinas_pacote'] 			= 0;
	$_SESSION['ficha_financeira']['carga_horaria_real_disciplinas_pacote'] 		= 0;
	$_SESSION['ficha_financeira']['carga_horaria_disciplinas_fora_pacote'] 		= 0;
	$_SESSION['ficha_financeira']['carga_horaria_real_disciplinas_fora_pacote'] = 0;
	$_SESSION['ficha_financeira']['desconto_dp'] 								= 0;


	if(isset($_GET['id'])){
		$$chave = $_GET['id'];
		

		//CARREGA DADOS DA FICHA FINANCEIRA
		$sql = "SELECT
					*,
					DATE_FORMAT( data_cadastro, '%d/%m/%Y - %H:%i:%s' ) AS data_cadastro 
				FROM
					ficha_financeira.ficha_financeira
					INNER JOIN coopex.pessoa USING ( id_pessoa ) 
				WHERE
					ficha_financeira.ficha_financeira.excluido = 0 
					AND id_ficha_financeira = ".$_GET['id'];
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

<style type="text/css">
	.table th, .table td {
		vertical-align: middle !important;
	}
</style>

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
		<li class="breadcrumb-item"><a href="javascript:void(0);">Ficha Financeira</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-repeat'></i> Ficha Financeira
			<small>
				Cadastro de Ficha Financeira
			</small>
		</h1>

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
					<span class="h5">Status de aprovação da ficha financeira</span>
					<!-- <div class="progress">
						<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
					</div> -->
					<br><br>
					<ol>
						<?php
							$sql2 = "SELECT
										*,
										DATE_FORMAT( data_cadastro, '%d/%m/%Y - %H:%i:%s' ) AS data_cadastro 
									FROM
										ficha_financeira.ficha_financeira_etapa
										INNER JOIN ficha_financeira.etapa USING ( id_etapa ) 
									WHERE
										id_ficha_financeira = ".$_GET['id'];
							$res2 = $coopex->query($sql2);
							while($etapa = $res2->fetch(PDO::FETCH_OBJ)){
								$etapa->etapa = utf8_encode($etapa->etapa);
								echo "<li><b>$etapa->data_cadastro</b> - $etapa->etapa</li>";
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

		<input type="hidden" name="<?php echo $chave?>" value="<?php echo $$chave ? $$chave : 0?>">
		<div class="row">
			<div class="col-xl-12">
			  	<div id="panel-2" class="panel">
				  	<div class="panel-hdr">
						<h2>
							1. Ficha Financeira
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
										<label class="form-label" for="validationCustom03">Curso <span class="text-danger">*</span></label>
										<?php
											$where = "";

											$id_faculdade = $_SESSION['coopex']['usuario']['id_faculdade'] ? $_SESSION['coopex']['usuario']['id_faculdade'] : "1000000002";

											if($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 1 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 13 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 8){
												$where .= " AND graduacao = 1 and id_campus = $id_faculdade";
											} else {
												$where .= " AND graduacao = 1 AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
											}

											if(isset($_GET['id'])){
												$sql = "SELECT
														id_departamento,
														departamento,
														campus.campus
													FROM
														coopex.departamento
														INNER JOIN coopex.departamento_pessoa USING ( id_departamento )
														INNER JOIN coopex.campus USING ( id_campus )
														WHERE 
														
															id_departamento = $dados->id_curso
													GROUP BY
														id_departamento
													ORDER BY
														departamento";
										
											} else {
												$sql = "SELECT
														id_departamento,
														departamento,
														campus.campus
													FROM
														coopex.departamento
														INNER JOIN coopex.departamento_pessoa USING ( id_departamento )
														INNER JOIN coopex.campus USING ( id_campus )
														WHERE 1=1
														$where
													GROUP BY
														id_departamento
													ORDER BY
														departamento";
											}

											$curso = $coopex->query($sql);
										?>

										<?php
											if(isset($_GET['id'])){
											$row = $curso->fetch(PDO::FETCH_OBJ)	
										?>
											<input type="text" class="form-control" value="<?php echo isset($row->id_departamento) ? utf8_encode($row->departamento) : ""?>">
										<?	} else {?>
											<select id="id_curso" name="id_curso" class="select2 form-control" required="">
												<option value="">Selecione o Curso</option>
										<?php
												while($row = $curso->fetch(PDO::FETCH_OBJ)){
										?>
												<option  value="<?php echo $row->id_departamento?>"><?php echo utf8_encode($row->departamento)?></option>
										<?php
												}
										?>	
											</select>
										<?php
											}
										?>

										<div class="invalid-feedback">
											Selecione o curso
										</div>
									</div>

									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Grades do Curso <span class="text-danger">*</span></label>
										<?php
											if(isset($_GET['id'])){
												$sql = "SELECT
															crr_id_curriculo AS id_curriculo,
															pel_ds_compacta AS grade 
														FROM
															academico..CRR_curriculo
															INNER JOIN academico..PEL_periodo_letivo ON crr_id_periodo_letivo_inicio = pel_id_periodo_letivo 
														WHERE
															crr_id_curriculo = ".$dados->id_grade;	
												$res = mssql_query($sql);
												$row = mssql_fetch_object($res);
										?>
											<input type="text" class="form-control" value="<?php echo isset($row->id_curriculo) ? $row->grade : ""?>">
										<?php
											} else {
										?>
										<select <?php echo $desabilitar_edicao_carga_horaria || $desabilitar_edicao  ? 'disabled=""' : ""?> id="id_grade" name="id_grade" onchange="$('#disciplina').val($(this).select2('data')[0].text);" disabled="" class="select2 form-control" required="">
											<option value="">Selecione a Grade</option>
										</select>
										<!-- <input type="hidden" name="disciplina" id="disciplina" value="<?php echo isset($dados->disciplina) ? utf8_encode($dados->disciplina) : ""?>"> -->
										<div class="invalid-feedback">
											Selecione a Grade
										</div>
										<?php
											}
										?>
									</div>


									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Semestre Letivo <span class="text-danger">*</span></label>
										<?php
											if(isset($_GET['id'])){
												$sql = "SELECT
															pel_id_periodo_letivo as id_semestre,
															pel_ds_historico as semestre
														FROM
															academico..PEL_periodo_letivo 
														WHERE
															pel_id_periodo_letivo = ".$dados->id_semestre;	
												$res = mssql_query($sql);
												$row = mssql_fetch_object($res);
										?>
											<input type="text" class="form-control" id="id_semestre_letivo" value="<?php echo isset($row->id_semestre) ? $row->semestre : ""?>">
										<?php
											} else {
										?>
										<select <?php echo $desabilitar_edicao_carga_horaria || $desabilitar_edicao  ? 'disabled=""' : ""?> id="id_semestre" name="id_semestre"  disabled="" class="select2 form-control" required="">
											<option value="">Selecione o Semestre</option>
										</select>
										<!-- <input type="hidden" name="semestre" id="semestre" value="<?php echo isset($dados->disciplina) ? utf8_encode($dados->disciplina) : ""?>"> -->
										<div class="invalid-feedback">
											Selecione o Semestre
										</div>
										<?php
											}
										?>
									</div>

								</div>
									
								<div class="form-row">
									<div class="col-md-12 mb-3">
										<div class="form-group">
											<label class="form-label" for="select2-ajax">
												Acadêmico
											</label>
											<?php
												if(isset($_GET['id'])){
													get_aluno($dados->id_pessoa, $dados->id_semestre, $dados->id_curso);
											?>
												<input type="text" id="nome_academico_input" class="form-control" value="<?php echo isset($dados->id_pessoa) ? utf8_encode($dados->nome) : ""?>">
											<?php
												} else {
											?>
											<select id="id_pessoa"  disabled="" name="id_pessoa" data-placeholder="Acadêmico" class="js-consultar-usuario form-control"></select>
											<?php
												}
											?>
										</div>	
									</div>
								</div>

								<div class="form-row">
									<div class="col-md-12 mb-3">
										<table class="table table-bordered table-hover table-striped w-100">
											<thead>
												<tr>
													<td class="text-center"><strong>RA</strong></td>
													<td class="text-center"><strong>Turno</strong></td>
													<td class="text-center"><strong>Turma</strong></td>
													<td class="text-center"><strong>Valor Hora</strong></td>
													<td class="text-center"><strong>Valor Mensalidade</strong></td>
													<td class="text-center"><strong>Valor Semestre</strong></td>
												</tr>
											</thead>
											<tfoot>
												<tr>
													<td class="text-center"><strong id="ra">
														<?=isset($_GET['id']) ? $_SESSION['ficha_financeira']['ra'] : "-"?></strong>
													</td>
													<td class="text-center"><strong id="turno">
														<?=isset($_GET['id']) ? $_SESSION['ficha_financeira']['turno'] : "-"?></strong></td>
													<td class="text-center">
														<strong id="turma"><?=isset($_GET['id']) ? utf8_encode($_SESSION['ficha_financeira']['link_de_turma']) : "-"?></strong>
													</td>
													
													<td class="text-center">
														<strong id="valor_hora">R$ <?=isset($_GET['id']) ? number_format($_SESSION['ficha_financeira']['valor_hora'], 2, ',', '.') : "0,00"?></strong>
													</td>
													<td class="text-center">
														<strong id="valor_mensalidade">R$ <?=isset($_GET['id']) ? number_format($_SESSION['ficha_financeira']['valor_semestre'] / 6, 2, ',', '.') : "0,00"?></strong>
													</td>
													<td class="text-center">
														<strong id="valor_semestre">R$ <?=isset($_GET['id']) ? number_format($_SESSION['ficha_financeira']['valor_semestre'], 2, ',', '.') : "0,00"?></strong>
													</td>
												</tr>
											</tfoot>
										</table>
									</div>

									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom03">Observações</label>
										<textarea name="observacao" class="form-control col-md-12"><?php echo isset($dados->id_pessoa) ? utf8_encode($dados->observacao) : ""?></textarea>	
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
							Disciplinas da Ficha Financeira
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content p-0">

							<div class="panel-content">
								


								<div class="form-row" id="cronograma_container" style="display:<?php echo $dados->id_parecer_reducao == "1" ? "none" : ""?>;">
									
									<div class="col-xl-12">
									<!-- datatable start -->
										<table id="diciplinas_ficha" class="table table-bordered table-hover table-striped w-100">
											<thead>
												<tr>
													<td><strong>Código</strong></td>
													<td><strong>Disciplina</strong></td>
													<td><strong>Equivalência</strong></td>
													<td class="text-center"><strong>Horas</strong></td>
				
												</tr>
											</thead>

											<tbody>
											<?
												if(isset($_GET['id'])){
													$sql = "SELECT
																*
															FROM
																ficha_financeira.ficha_financeira_disciplinas 
																INNER JOIN ficha_financeira.ficha_financeira USING ( id_ficha_financeira ) 
															WHERE
																excluido = 0 
																AND
																	id_ficha_financeira = ".$_GET['id'];

													$curso = $coopex->query($sql);
													while($row = $curso->fetch(PDO::FETCH_OBJ)){
														
														$sql = "SELECT
																	atc_id_atividade,
																	atc_cd_atividade,
																	atc_nm_atividade
																FROM
																	academico..ATC_atividade_curricular
																WHERE
																	atc_id_atividade = ".$row->id_disciplina;	
														$res = mssql_query($sql);

													 	$disciplina = mssql_fetch_object($res);

													 	$_SESSION['ficha_financeira']['carga_horaria'] += $row->carga_horaria;
														//$_SESSION['ficha_financeira']['carga_horaria_real_disciplinas_pacote'] += trim($row2['atc_qt_horas'] / $divisao);
														$_SESSION['ficha_financeira']['carga_horaria_disciplinas_pacote'] += $row->carga_horaria;

														$_SESSION['ficha_financeira']['disciplinas'][$row->id_disciplina]['id_disciplina'] = $row->id_disciplina;
														$_SESSION['ficha_financeira']['disciplinas'][$row->id_disciplina]['carga_horaria'] = $row->carga_horaria;
														$_SESSION['ficha_financeira']['disciplinas'][$row->id_disciplina]['fora_pacote']  = $row->fora_do_pacote;
														$_SESSION['ficha_financeira']['disciplinas'][$row->id_disciplina]['dp']  = $row->dp;
														$_SESSION['ficha_financeira']['disciplinas'][$row->id_disciplina]['equivalencia']  = $row->id_equivalencia;

														
														if($row->fora_do_pacote > 0){
															if($row->dp){
																$equivalencia = "Dependência";
															} else {
																if($row->id_equivalencia){
																	$sql3 = "SELECT
																				atc_id_atividade,
																				atc_cd_atividade,
																				atc_nm_atividade
																			FROM
																				academico..ATC_atividade_curricular
																			WHERE
																				atc_id_atividade = ".$row->id_equivalencia;	
																	$res3 = mssql_query($sql3);
																 	$row3 = mssql_fetch_object($res3);
																 	$equivalencia = $row3->atc_cd_atividade." - ".utf8_encode($row3->atc_nm_atividade);
														
																} else {
																	$equivalencia = "";
																}
															}
														} else {
															$equivalencia = "Disciplina do pacote";
														}
														
											?>
												<tr id="grade_montada_<?=$disciplina->atc_id_atividade?>">
													<td><strong><?=$disciplina->atc_cd_atividade?></strong></td>
													<td><strong><?=utf8_encode($disciplina->atc_nm_atividade)?></strong></td>
													<td><strong><?=$equivalencia?></strong></td>
													<td class="text-center"><strong><?=$row->carga_horaria?></strong></td>
						
												</tr>

											<?
													}
											?>
											<script type="text/javascript">
												$(document).ready(function(){
													//carrega_valor_ficha();
												})
											</script>
											<?		
												}
											?>

												
											</tbody>
												
											<tfoot>
												<tr>
													<td><strong></strong></td>
													<td><strong></strong></td>
													<td class="text-right"><strong>CARGA HORÁRIA DA FICHA</strong></td>
													<td class="text-center"><strong id="ch_total_ficha">0</strong></td>
													<td class="text-center"></td>
												</tr>
												<tr style="display: none;">
													<td><strong></strong></td>
													<td><strong></strong></td>
													<td class="text-right"><strong>VALOR DISCIPLINAS DO PACOTE</strong></td>
													<td class="text-center"><strong><span id="valor_pacote">0,00</span></strong></td>
													<td class="text-center"></td>
												</tr>
												<tr style="display: none;">
													<td><strong></strong></td>
													<td><strong></strong></td>
													<td class="text-right"><strong>VALOR DISCIPLINAS FORA DO PACOTE</strong></td>
													<td class="text-center"><strong><span id="valor_fora_pacote">0,00</span></strong></td>
													<td class="text-center"></td>
												</tr>
												<tr style="display: none;">
													<td><strong></strong></td>
													<td><strong></strong></td>
													<td class="text-right"><strong>VALOR DESCONTO DP</strong></td>
													<td class="text-center"><strong><span id="valor_desconto_dp">0,00</span></strong></td>
													<td class="text-center"></td>
												</tr>
												<tr>
													<td><strong></strong></td>
													<td><strong></strong></td>
													<td class="text-right"><strong>VALOR TOTAL</strong></td>
													<td class="text-center"><strong><span id="valor_total_semestre">0,00</span><input value="" id="valor_total_semestre_input" type="hidden" name=""></strong></td>
													<td class="text-center"></td>
												</tr>
												<tr>
													<td><strong></strong></td>
													<td><strong></strong></td>
													<td class="text-right"><strong>PREVISÃO DA MENSALIDADE</strong></td>
													<td class="text-center"><strong><span id="valor_previsao_mensalidade">0,00</span><input value="" id="valor_total_semestre_input" type="hidden" name=""></strong></td>
													<td class="text-center"></td>
												</tr>
											</tfoot>
										</table>
									<!-- datatable end -->
									</div>
								</div>
								<?
									if($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 1 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 3 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 2){
								?>
								<div class="form-row" id="cronograma_container" style="display:<?php echo $dados->id_parecer_reducao == "1" ? "none" : ""?>;">
									<div class="col-xl-12">
									<!-- datatable start -->
										<label class="form-label" for="select2-ajax">
											Pagamentos
										</label>
										<table id="" class="table table-bordered table-hover table-striped w-100">
											<thead>
												<tr>
													<td class="text-center"><strong>1º</strong></td>
													<td class="text-center"><strong>2º</strong></td>
													<td class="text-center"><strong>3º</strong></td>
													<td class="text-center"><strong>4º</strong></td>
													<td class="text-center"><strong>5º</strong></td>
													<td class="text-center"><strong>6º</strong></td>
												</tr>
											</thead>

											<tbody>
												<tr>
													<td><input id="pagamento1" title="1" autocomplete="off" name="valores_valor[]" class="form-control moeda" type="text" value="<?=isset($_GET['id']) ? $dados->parcela_1 : ''?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
													<td><input id="pagamento2" title="2" autocomplete="off" name="valores_valor[]" class="form-control moeda" type="text" value="<?=isset($_GET['id']) ? $dados->parcela_2 : ''?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
													<td><input id="pagamento3" title="3" autocomplete="off" name="valores_valor[]" class="form-control moeda" type="text" value="<?=isset($_GET['id']) ? $dados->parcela_3 : ''?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
													<td><input id="pagamento4" title="4" autocomplete="off" name="valores_valor[]" class="form-control moeda" type="text" value="<?=isset($_GET['id']) ? $dados->parcela_4 : ''?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
													<td><input id="pagamento5" title="5" autocomplete="off" name="valores_valor[]" class="form-control moeda" type="text" value="<?=isset($_GET['id']) ? $dados->parcela_5 : ''?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
													<td><input id="pagamento6" title="6" autocomplete="off" name="valores_valor[]" class="form-control moeda" type="text" value="<?=isset($_GET['id']) ? $dados->parcela_6 : ''?>" style="text-align: right; font-weight: bold;" data-thousands="." data-decimal=","></td>
												</tr>
											</tbody>
												
											
										</table>

										<table id="" class="table table-bordered table-hover table-striped w-100">
											<thead>
												<tr>
													<td><strong id="descricao_diferenca">Reembolso</strong></td>
												</tr>
											</thead>
											<thead>
												<tr>
													<td><strong>Reembolso</strong></td>
													<input id="valor_diferenca" name="reembolso" type="hidden" value="">
												</tr>
											</thead>

											<tbody>
												<tr>
													<td><strong id="reembolso"></strong></td>
													<input id="valor_reembolso" name="reembolso" type="hidden" value="">
												</tr>
											</tbody>
												
										</table>
										<label class="form-label" for="select2-ajax">
											Obervações Tesouraria
										</label>
										<textarea class="form-control" name="tesouraria"></textarea>
									<!-- datatable end -->
									</div>
								</div>
								<?
									}
								?>
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
						


					</div>
				</div>
			</div>
		</div>

		<?
			$sql = "SELECT
						* 
					FROM
						ficha_financeira.ficha_financeira_etapa 
					WHERE
						( id_etapa = 5 ) 
						AND id_ficha_financeira = ".$_GET['id'];
			$res = $coopex->query($sql);
			if(!$res->rowCount()){
		?>

		<div class="row">
			<div class="col-xl-12">
			  <div id="panel-2" class="panel">
				<div class="panel-hdr">
						<h2>
							Aprovação
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
										<div class="col-md-12">
											<form class="needs-validation" novalidate="" method="post" target="aprovacao_dados" action="modulos/ficha_financeira/aprovacao/aprovacao_dados.php">
												<input type="hidden" value="<?=$_GET['id']?>" name="id_ficha_financeira">
												<input type="hidden" value="3" name="id_etapa">
												<div class="alert border-faded bg-transparent text-secondary fade show" role="alert">
		                                            <div class="d-flex align-items-center">
		                                                <div class="alert-icon">
		                                                    <span class="icon-stack icon-stack-md">
		                                                        <i class="base-7 icon-stack-3x color-success-600"></i>
		                                                        <i class="fal fa-check icon-stack-1x text-white"></i>
		                                                    </span>
		                                                </div>
		                                                <div class="flex-1">
		                                                    <span class="h5 color-success-600">Aprovar ficha financeira</span>
		                                                    <br>
		                                                    O valor aqui apresentado é apenas uma previsão da mensalidade, não sendo necessariamente o valor real da mensalidade.
		                                                    <br>
		                                                    <br>
		                                                    <button type="submit" class="btn btn-outline-success btn-sm btn-w-m waves-effect waves-themed">APROVAR FICHA FINANCEIRA</button>
		                                                </div>
		                                                
		                                            </div>
		                                        </div>
	                                        </form>

										</div>	

										<div class="col-md-12 mb-3">
											<form class="needs-validation" novalidate="" method="post" target="aprovacao_dados" action="modulos/ficha_financeira/aprovacao/aprovacao_dados.php">
												<input type="hidden" value="<?=$_GET['id']?>" name="id_ficha_financeira">
												<input type="hidden" value="4" name="id_etapa">
												<div class="alert border-danger bg-transparent text-secondary fade show" role="alert">
		                                            <div class="d-flex align-items-center">
		                                                <div class="alert-icon">
		                                                    <span class="icon-stack icon-stack-md">
		                                                        <i class="base-7 icon-stack-3x color-danger-900"></i>
		                                                        <i class="fal fa-times icon-stack-1x text-white"></i>
		                                                    </span>
		                                                </div>
		                                                <div class="flex-1">
		                                                    <span class="h5 color-danger-900">Não aprovar ficha financeira</span>
		                                                    <br>
		                                                    Caso não esteja de acordo com a ficha financeira preencha o campo abaixo informando os motivos.<br><br>
		                                                    <textarea name="obs" id="aprovacao_observacao" class="form-control"></textarea><br>
		                                                    <button type="submit" class="btn btn-outline-danger btn-sm btn-w-m waves-effect waves-themed">NÃO APROVAR FICHA FINANCEIRA</button>
		                                                </div>
		                                                
		                                            </div>
		                                        </div>
	                                        </form>
										</div>
									</div>		
								</div>
							</div>
						</div>			
					
				</div>
			</div>
		</div>

		<?
			}
		?>
		
		<!-- <textarea class="d-none" name="cronograma" id="cronograma" rows="10" cols="100"></textarea> -->



	<iframe class="d-none" name="aprovacao_dados" src="" style="position: fixed; z-index: 999999999999; width: 30%; background-color: #fff; top: 0; left: 0; height: 300px"></iframe>

	

<?
	if(isset($_GET['id'])){
		$id_registro = $_GET['id'];
		$sql = "SELECT
					* 
				FROM
					ficha_financeira.ficha_financeira_etapa 
				WHERE
					id_ficha_financeira = $id_registro 
					AND forma_contato = 1";
		$res = $coopex->query($sql);
		if($res->rowCount()){
			$whats = $res->fetch(PDO::FETCH_OBJ);
			$whats = $whats->contato;
		} else {
			$whats = $_SESSION['ficha_financeira']['whatsapp'] ;
		}

		$sql = "SELECT
					* 
				FROM
					ficha_financeira.ficha_financeira_etapa 
				WHERE
					id_ficha_financeira = $id_registro 
					AND forma_contato = 2";
		$res = $coopex->query($sql);
		if($res->rowCount()){
			$email = $res->fetch(PDO::FETCH_OBJ);
			$email = $email->contato;
		} else {
			$email = $_SESSION['ficha_financeira']['email'];
		}			
	}
?>	

<input type="hidden" id="numero_whatsapp" value="<?=$whats?>">
<input type="hidden" id="numero_whatsapp" value="<?=$email?>">
	
<link rel="stylesheet" media="screen, print" href="css/fa-brands.css">
<link rel="stylesheet" media="screen, print" href="css/fa-regular.css">

<div class="modal fade" id="default-example-modal-lg-center" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notificar Acadêmico</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
            	<div class="form-group">
                    <label class="form-label">Notificar por Whatsapp</label>
                    <div class="input-group input-group-lg bg-white shadow-inset-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-transparent border-right-0 py-1 px-3 text-success">
                                <i class="fab fa-whatsapp" style="font-size: 24px"></i>
                            </span>
                        </div>
                        <input type="text" onkeyup="alterar_link_whatsapp()" class="form-control border-left-0 bg-transparent pl-0" id="whatsapp" value="">
                        <div class="input-group-append">
                            <a id="link_whatsapp" href="" target="_blank" onclick="<?=$_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 3 ? "notificar_aluno_tesouraria()" : "notificar_aluno_coordenacao()"?>" class="btn btn-default waves-effect waves-themed" type="button">Enviar</a>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Notificar por e-mail</label>
                    <div class="input-group input-group-lg bg-white shadow-inset-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-transparent border-right-0 py-1 px-3 text-success">
                                <i class="fal fa-at" style="font-size: 24px"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control border-left-0 bg-transparent pl-0" id="email" value="">
                        <div class="input-group-append">
                            <button onclick="<?=$_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 3 ? "notificar_aluno_tesouraria_email()" : "notificar_aluno_coordenacao_email()"?>" class="btn btn-default waves-effect waves-themed" type="button">Enviar</button>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="nome_academico">


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

</main>

<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/datagrid/datatables/dataTables.editor.min.js"></script>
<script src="js/moment-with-locales.js"></script>
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<script src="https://www2.fag.edu.br/coopex/js/jquery.maskMoney.min.js" type="text/javascript"></script>
<script>

	function alterar_link_whatsapp(id_registro = ''){
		<?
			if(isset($_GET['id'])){
				echo "id_registro = ".$_GET['id'].";";
			}
		?>
		var texto = "<?= urlencode('Olá ');?>";
		texto += $("#nome_academico").val();
		texto += "<?= urlencode(", sua *ficha financeira* foi gerada e precisa de aprovação, para aprovar acesse o link: \n");?>"
		texto += "https://coopex.fag.edu.br/ficha_financeira/aprovacao/"+id_registro;

		var link = "https://api.whatsapp.com/send?phone=55"+$("#whatsapp").val()+"&text="+texto;
		console.log(link);
		$("#link_whatsapp").attr("href", link);
	}

	function moeda(valor){
		valor = valor.replace('R$ ', '');
		valor = valor.replace('.', '');
		valor = valor.replace(',', '.');
		return parseFloat(valor);
	}


	function numero(valor) {
		//console.log(valor);
		/* valor = valor.replace('R$ ', '');
		return parseInt(valor);*/
		return valor;
	}
	 


	function calculo_pagamento(id, valor){


		valor = moeda(valor);
		total = moeda($("#valor_total_semestre_input").val());

		var soma = 0;
		var parcela = 0;
		for(var i=1; i<=id; i++){
			soma += moeda($("#pagamento"+i).val());
			parcela = i;
		}

		
		saldo = total - soma;
		valor_parcela = saldo/(6-parcela);

		//console.log("soma " + soma);
		//console.log("total " + total);
		valor_reembolso = soma - total;

		for(var i=(parseInt(id)+1); i<=6; i++){
			var valor = numero(valor_parcela.toFixed(2).replace(".",","));

			if(valor_parcela <= 0){
				$("#pagamento"+i).val("0");
				$("#valor_reembolso").val(valor_reembolso.toFixed(2).replace(".",","));
				$("#reembolso").html("R$ " + valor_reembolso.toFixed(2).replace(".",","));
			} else {
				$("#pagamento"+i).val(valor);
				$("#valor_reembolso").val("");
				$("#reembolso").html("");
			}

		}

	}

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
	function cadastroOK(operacao, id_registro){ 
		Swal.fire({
			type: "success",
			title: "Aprovação definida com sucesso",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				document.location.reload(true);
			}
		});
	}

	//MENSAGEM DE FALHA NO CADASTRO
	function cadastroFalha(operacao){ 
		Swal.fire({
			type: "success",
			title: "Definido como não aprovado",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				document.location.reload(true);
			}
		});
	}



	//CARREGAS AS GRADES DO CURSO
	function carrega_grade(id_grade = ''){

		$("#id_grade").attr("disabled", true);
		
		$.getJSON("modulos/ficha_financeira/cadastro/ajax/carrega_grade.php", {id_curso: $("#id_curso").val()})
		.done(function(json){
			$("#id_grade").empty();
			$("#id_grade").append("<option value=''>Seleciona a Grade</option>");
			$.each( json, function( i, item ) {
				$("#id_grade").append('<option value="'+item.id_curriculo+'">'+item.grade+'</option>');
			});
			if(id_grade){
				$('#id_grade option[value='+id_grade+']').attr('selected','selected');
				<?php
					if(isset($_GET['id'])){
						if(isset($dados->id_semestre)){
							echo "carrega_semestre(".$dados->id_semestre.");";
						}
					}
				?>
			}
			$("#id_grade").attr("disabled", false);
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}


	//CARREGAS AS GRADES DO CURSO
	function carrega_turma(id_turma){

		$.getJSON("modulos/ficha_financeira/cadastro/ajax/carrega_turma.php", {id_semestre: $("#id_semestre").val(), id_curso: $("#id_curso").val()})
		.done(function(json){
			//$("#id_turma").empty();
			$.each( json, function( i, item ) {
				selecionado = item.id_pacote == id_turma ? 'selected=""' : "";
				$("#id_turma").append('<option '+selecionado+' value="'+item.id_pacote+'">'+item.pacote+'</option>');
			});
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}

	//CARREGAS OS SEMESTRE LETIVOS DO CURSO
	function carrega_semestre(id_semestre = ''){

		var id_grade = $('#id_grade').select2('data')
		id_grade = id_grade[0].text;

		
		$.getJSON("modulos/ficha_financeira/cadastro/ajax/carrega_semestre.php", {id_grade: id_grade})
		.done(function(json){
			$("#id_semestre").empty();
			$("#id_semestre").append("<option value=''>Seleciona o Semestre</option>");
			$.each( json, function( i, item ) {
				$("#id_semestre").append('<option value="'+item.id_periodo_letivo+'">'+item.periodo_letivo+'</option>');
			});
			if(id_semestre){
				$('#id_semestre option[value='+id_semestre+']').attr('selected','selected');
			}
			$("#id_semestre").attr("disabled", false);
			habilita_academico();
			
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

		}
	}

	//CARREGAS AS DISCIPLINAS EQUIVALENTE MEDIANTE PESQUISA DO USUÁRIO
	function carrega_disciplina_equivalente(){
		$.getJSON("modulos/ficha_financeira/cadastro/ajax/carrega_disciplina_geral.php", {id_curso: $("#id_curso").val()})
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
		$('#diciplinas_pacote').DataTable().draw(false);
	}


	function ativar_select2(){
		//SELECT DISCIPLINA	
		$(".js-consultar-disciplina-equivalente").select2({
			ajax:{
				url: "modulos/ficha_financeira/cadastro/ajax/carrega_disciplina_equivalente.php",
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
			minimumInputLength: 2,
			templateResult: formatoDisciplina,
			templateSelection: formatoTextoDisciplina
		});

		$(".js-consultar-disciplina-equivalente").change(function() {
			definir_equivalencia(this.title, this.value);
		});
	}

	function definir_equivalencia($id_disciplina, $id_equivalente){
		
		$.getJSON("modulos/ficha_financeira/cadastro/ajax/definir_equivalencia.php", {id_disciplina: $id_disciplina, id_equivalente: $id_equivalente})
		.done(function(json){
			
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}

	function definir_desconto_dp($id_disciplina){

		$valor_desconto = ($("#desconto_dp"+$id_disciplina).val());
		
		$.getJSON("modulos/ficha_financeira/cadastro/ajax/definir_desconto_dp.php", {id_disciplina: $id_disciplina, valor_desconto: $valor_desconto})
		.done(function(json){

			carrega_valor_ficha();
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}

	function habilita_academico(){
		$("#id_pessoa").attr("disabled", false);
		$("#id_disciplina").attr("disabled", false);
	}

	$(document).ready(function(){

		$.ajaxSetup({
		    async: false
		});

		$(".moeda").keyup(function() {
			calculo_pagamento(this.title, this.value);
		});

		//setInterval(recalculo, 1000);

		$('.moeda').maskMoney({prefix:'R$ '});

		//CARREGA OS DADOS DOS SELECTS DEPENDENTES QUANDO EDITAR O REGISTRO
		<?php
			if(isset($_GET['id'])){
				/*if(isset($dados->id_disciplina)){
					echo "carrega_disciplina(".$dados->id_disciplina.");";
				}
				if(isset($dados->id_grade)){
					echo "carrega_grade(".$dados->id_grade.");";
				}*/
				if(isset($dados->id_semestre)){

					echo "carrega_pacote($dados->id_turma);";
					//echo "carrega_dados();";
					//echo "carrega_dp();";
				}

		
			}
		?>

		$(":input").inputmask();
		$('.select2').select2();

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
			carrega_grade();
		});

		$("#id_turma").change(function() {
			id_turma = $("#id_turma").val();
			console.log(id_turma);
			carrega_pacote(id_turma);
		});

		$("#id_pessoa").change(function() {
			carrega_pacote();
			//carrega_dp();
		});

		$("#id_grade").change(function() {
			carrega_semestre();
		});

		$("#id_semestre").change(function() {
			habilita_academico();
		});



		$("#id_disciplina").change(function() {
			id_disciplina = $("#id_disciplina").val()
			incluir_disciplina_fora(id_disciplina);
		});

		

		//SELECT DISCIPLINA	
		$(".js-consultar-disciplina").select2({
			ajax:{
				url: "modulos/ficha_financeira/cadastro/ajax/carrega_disciplina_geral.php",
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
			minimumInputLength: 2,
			templateResult: formatoDisciplina,
			templateSelection: formatoTextoDisciplina
		});

		var total_global = 0;
		//SELECT USUÁRIO
		$(".js-consultar-usuario").select2({
			ajax:{
				url: "modulos/_core/buscar_usuario_matriculado.php",
				dataType: 'json',
				delay: 250,
				data: function(params){
					return {
						q: params.term, // search term
						page: params.page,
						id_periodo_letivo: $("#id_semestre").val(),
						id_curso: $("#id_curso").val()
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

	function carrega_pacote(id_turma = ''){

		<?
			if(isset($_GET['id'])){
		?>
			id_ficha_financeira = <?=$_GET['id']?>;
			id_semestre = <?=$dados->id_semestre?>;
			id_pessoa 	= <?=$dados->id_pessoa?>;
			id_turma 	= id_turma;

			var id_periodo = $('#id_semestre_letivo').val();
			
			var ch_total = 0;
			var json_disciplinas;

			id_curso = <?=$dados->id_curso?>;

		<?
			} else {
		?>
			id_semestre = $("#id_semestre").val();
			id_pessoa 	= $("#id_pessoa").val();

			var id_periodo = $('#id_semestre').select2('data')
			id_periodo = id_periodo[0].text;
			id_turma = 0;

			var ch_total = 0;
			var json_disciplinas;

			id_curso = $("#id_curso").val();
		<?		
			}
		?>
		
		$.getJSON("modulos/ficha_financeira/cadastro/ajax/disciplina_pacote.php", {id_semestre: id_semestre, id_pessoa: id_pessoa, id_periodo: id_periodo, id_curso: id_curso, id_turma: id_turma, id_ficha_financeira: id_ficha_financeira})
		.done(function(json){
			$("#diciplinas_pacote tbody").empty();
			
			$.each( json, function( i, item ) {
				ch_total += parseInt(item.atc_qt_horas);

				$("#diciplinas_pacote").append('<tr id="grade_original_'+item.atc_cd_atividade+'"><td><strong>'+item.atc_cd_atividade+'</strong></td><td>'+item.atc_nm_atividade+'</td><td class="text-center">'+item.atc_qt_horas+'</td><td class="text-center"><a style="display:none" id="bt_incluido'+item.atc_id_atividade+'" href="javascript:void(0);" class="btn btn-default btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-check"></i></a><a onclick=incluir_disciplina("'+item.atc_id_atividade+'",0) id="bt_incluir'+item.atc_id_atividade+'" href="javascript:void(0);" class="btn btn-primary  btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-plus"></i></a></div></td></tr>');
		
				if($("#bt_remover"+item.atc_id_atividade).length){
					$("#bt_incluido"+item.atc_id_atividade).show();
					$("#bt_incluir"+item.atc_id_atividade).hide();
				}
			});

			carrega_valor_ficha();

			$("#total_diciplinas_pacote").html(ch_total);
			
			<?
				if(!isset($_GET['id'])){
			?>
				carrega_dados();
			<?
				}
			?>	

			

		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}


	function carrega_dp(){

		<?
			if(isset($_GET['id'])){
		?>
			id_semestre = <?=$dados->id_semestre?>;
			id_pessoa 	= <?=$dados->id_pessoa?>;
			id_periodo = $('#id_semestre_letivo').val();
			id_curso = <?=$dados->id_curso?>;
		<?
			} else {
		?>
			id_semestre = $("#id_semestre").val();
			id_pessoa 	= $("#id_pessoa").val();
			id_periodo = $('#id_semestre').select2('data');
			id_periodo = id_periodo[0].text;
			id_curso = $("#id_curso").val();
		<?
			}
		?>
		
		var ch_total = 0;
		var json_disciplinas;


		$.getJSON("modulos/ficha_financeira/cadastro/ajax/disciplina_dp.php", {id_semestre: id_semestre, id_pessoa: id_pessoa, id_periodo: id_periodo, id_curso: id_curso})
		.done(function(json){
			$("#diciplinas_dp tbody").empty();
			
			$.each( json, function( i, item ) {
				ch_total += parseInt(item.atc_qt_horas);

				$("#diciplinas_dp").append('<tr id="grade_original_'+item.atc_cd_atividade+'"><td><strong>'+item.atc_cd_atividade+'</strong></td><td>'+item.atc_nm_atividade+'</td><td class="text-center">'+item.atc_qt_horas+'</td></tr>');
			});

			$("#total_diciplinas_dp").html(ch_total);
			carrega_dados();

		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}


	function incluir_disciplina(id){

		$("#bt_incluir"+id).hide();
		$("#bt_incluido"+id).show();

		<?
			if(isset($_GET['id'])){
		?>
			id_semestre = <?=$dados->id_semestre?>;
			id_pessoa 	= <?=$dados->id_pessoa?>;
			id_periodo = $('#id_semestre_letivo').val();
			id_curso = <?=$dados->id_curso?>;
			//id_turma 	= <?=isset($dados->id_turma) ? $dados->id_turma : 0?>;
			id_turma 	= $("#id_turma").val();
		<?
			} else {
		?>
			id_semestre = $("#id_semestre").val();
			id_pessoa 	= $("#id_pessoa").val();
			id_periodo = $('#id_semestre').select2('data');
			id_periodo = id_periodo[0].text;
			id_curso = $("#id_curso").val();
			id_turma 	= $("#id_turma").val();
		<?
			}
		?>
		
		$.getJSON("modulos/ficha_financeira/cadastro/ajax/disciplina_pacote_inclusao.php", {id_semestre: id_semestre, id_pessoa: id_pessoa, id_periodo: id_periodo, id_disciplina: id, id_curso: id_curso, id_turma: id_turma})
		.done(function(json){

			$.each( json, function( i, item ) {

				$.each( item.classe, function( j, classe ) {
					var aux = classe;
					str = aux.split(":")
					console.log(str[0]);
				})

				origem = '<select class="form-control"></select>';
				

				$("#diciplinas_ficha tbody").append('<tr id="grade_montada_'+item.atc_id_atividade+'"><td>'+item.atc_cd_atividade+'</td><td>'+item.atc_nm_atividade+'</td><td>Disciplina do Pacote</td><td class="text-center">'+item.atc_qt_horas+'</td><td class="text-center">'+item.dp+'</td><td class="text-center"><a onclick=remover_disciplina_pacote("'+item.atc_id_atividade+'") id="bt_remover'+item.atc_id_atividade+'" href="javascript:void(0);" class="btn btn-danger btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-times"></i></a></div></td></tr>');
			});

			carrega_valor_ficha();

		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}

	function incluir_disciplina_fora(id){


		<?
			if(isset($_GET['id'])){
		?>
			id_semestre = <?=$dados->id_semestre?>;
			id_pessoa 	= <?=$dados->id_pessoa?>;
			id_periodo = $('#id_semestre_letivo').val();
			id_curso = <?=$dados->id_curso?>;
		<?
			} else {
		?>
			id_semestre = $("#id_semestre").val();
			id_pessoa 	= $("#id_pessoa").val();
			id_periodo = $('#id_semestre').select2('data');
			id_periodo = id_periodo[0].text;
			id_curso = $("#id_curso").val();
		<?
			}
		?>

		$("#bt_incluir"+id).hide();
		$("#bt_incluido"+id).show();
		
		

		

		$.getJSON("modulos/ficha_financeira/cadastro/ajax/disciplina_fora_pacote_inclusao.php", {id_disciplina: id, id_pessoa: id_pessoa, id_curso: id_curso})
		.done(function(json){

			$.each( json, function( i, item ) {

				origem = '<select title="'+item.atc_id_atividade+'" data-placeholder="Selecione a disciplina..." class="js-consultar-disciplina-equivalente form-control">';

				dp = '<input onchange="definir_desconto_dp('+item.atc_id_atividade+')" id="desconto_dp'+item.atc_id_atividade+'" title="'+item.atc_id_atividade+'"  class="form-control">';

				$("#diciplinas_ficha tbody").append('<tr id="grade_montada_'+item.atc_id_atividade+'"><td>'+item.atc_cd_atividade+'</td><td>'+item.atc_nm_atividade+'</td><td>'+origem+'</td><td class="text-center" id="carga_horaria_equivalente'+item.atc_id_atividade+'">'+item.atc_qt_horas+'</td><td class="text-center" id="dp'+item.atc_id_atividade+'">'+dp+'</td><td class="text-center"><a onclick=remover_disciplina_fora_pacote("'+item.atc_id_atividade+'") id="bt_remover'+item.atc_id_atividade+'" href="javascript:void(0);" class="btn btn-danger btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-times"></i></a></div></td></tr>');
			});

			carrega_valor_ficha();
			ativar_select2();

		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}


	function remover_disciplina_pacote(id){

		$("#bt_incluir"+id).show();
		$("#bt_incluido"+id).hide();
		$("#grade_montada_"+id).remove();

		<?
			if(isset($_GET['id'])){
		?>
			id_semestre = <?=$dados->id_semestre?>;
			id_pessoa 	= <?=$dados->id_pessoa?>;
			id_periodo = $('#id_semestre_letivo').val();
			id_curso = <?=$dados->id_curso?>;
			id_turma 	= <?=isset($dados->id_turma) ? $dados->id_turma : 0?>;
		<?
			} else {
		?>
			id_semestre = $("#id_semestre").val();
			id_pessoa 	= $("#id_pessoa").val();
			id_periodo = $('#id_semestre').select2('data');
			id_periodo = id_periodo[0].text;
			id_curso = $("#id_curso").val();
			id_turma = $("#id_turma").val();
		<?
			}
		?>


		$.getJSON("modulos/ficha_financeira/cadastro/ajax/disciplina_pacote_remocao.php", {id_semestre: id_semestre, id_pessoa: id_pessoa, id_periodo: id_periodo, id_disciplina: id, id_curso: id_curso, id_turma: id_turma})
		.done(function(json){

			$.each( json, function( i, item ) {
				carrega_valor_ficha();
			});
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}

	function remover_disciplina_fora_pacote(id){

		$("#bt_incluir"+id).show();
		$("#bt_incluido"+id).hide();
		$("#grade_montada_"+id).remove();

		
		id_semestre = $("#id_semestre").val();
		id_pessoa 	= $("#id_pessoa").val();

		var id_periodo = $('#id_semestre').select2('data')
		id_periodo = id_periodo[0].text;
		id_curso = $("#id_curso").val();

		$.getJSON("modulos/ficha_financeira/cadastro/ajax/disciplina_fora_pacote_remocao.php", {id_semestre: id_semestre, id_pessoa: id_pessoa, id_periodo: id_periodo, id_disciplina: id, id_curso: id_curso})
		.done(function(json){
			$.each( json, function( i, item ) {
				console.log(item);
				carrega_valor_ficha();
			});
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}


	function carrega_dados(){
		
		$.getJSON("modulos/ficha_financeira/cadastro/ajax/carrega_dados.php", {})
		.done(function(json){
			$.each( json, function( i, item ) {
				$("#valor_semestre").html("R$ " + item.valor_semestre);
				$("#valor_hora").html("R$ " + item.valor_hora);
				$("#valor_mensalidade").html("R$ " + item.valor_mensalidade);
				$("#ra").html(item.ra);
				$("#turno").html(item.turno);
				$("#turma").html(item.turma);

				$("#whatsapp").val(item.whatsapp);
				$("#email").val(item.email);
				$("#nome_academico").val(item.nome_academico);

				carrega_turma(item.id_turma);

				//$('#id_turma option[value='+item.id_turma+']').attr('selected','selected');
				//console.log($('#id_turma option[value='+item.id_turma+']').attr('selected'));

				alterar_link_whatsapp();
				
			});
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}

	function carrega_valor_ficha(){
		$.getJSON("modulos/ficha_financeira/cadastro/ajax/carrega_valor_ficha.php", {})
		.done(function(json){
			$.each( json, function( i, item ) {
				$("#ch_total_ficha").html(item.carga_horaria);
				$("#valor_pacote").html("R$ " + item.valor_pacote);
				$("#valor_fora_pacote").html("R$ " + item.valor_fora_pacote);
				$("#valor_total_semestre").html("R$ " + item.valor_total_semestre);
				$("#valor_desconto_dp").html("R$ " + item.valor_desconto_dp);
				$("#valor_total_semestre_input").val(item.valor_total_semestre);
				$("#valor_previsao_mensalidade").html("R$ " + item.valor_previsao_mensalidade);
			});
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}

	function enviar_para_tesouraria(){
		var nome = $("#nome_academico_input").val();
		$.getJSON("modulos/ficha_financeira/cadastro/ajax/enviar_para_tesouraria.php", {id_ficha_financeira: <?=isset($_GET['id']) ? $_GET['id'] : 0?>, nome: nome})
			.done(function(json){
				Swal.fire({
					type: "success",
					title: "Notificação enviada para a Tesouraria",
					showConfirmButton: false,
					timer: 1500,
						onClose: () => {
							window.history.back();
						}
				});
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}

	function notificar_aluno_tesouraria(){
		var whats = $("#whatsapp").val();
		$.getJSON("modulos/ficha_financeira/cadastro/ajax/notificar_aluno_tesouraria.php", {id_ficha_financeira: <?=isset($_GET['id']) ? $_GET['id'] : 0?>, whats:whats})
		.done(function(json){
			/*window.history.back();*/
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}

	function notificar_aluno_coordenacao(){
		var whats = $("#whatsapp").val();
		$.getJSON("modulos/ficha_financeira/cadastro/ajax/notificar_aluno_coordenacao.php", {id_ficha_financeira: <?=isset($_GET['id']) ? $_GET['id'] : 0?>, whats:whats})
		.done(function(json){
			window.history.back();
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}

	function notificar_aluno_tesouraria_email(){
		var email = $("#email").val();
		$.getJSON("modulos/ficha_financeira/cadastro/ajax/notificar_aluno_tesouraria_email.php", {id_ficha_financeira: <?=isset($_GET['id']) ? $_GET['id'] : 0?>, email: email})
			.done(function(json){
				$('#default-example-modal-lg-center').modal('toggle');
				Swal.fire({
					type: "success",
					title: "Notificação enviada com sucesso",
					showConfirmButton: false,
					timer: 1500,
						onClose: () => {
							/*window.history.back();*/
						}
				});
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}

	function finalizar_ficha_financeira(){
		var email = $("#email").val();
		$.getJSON("modulos/ficha_financeira/cadastro/ajax/finalizar_ficha_financeira.php", {id_ficha_financeira: <?=isset($_GET['id']) ? $_GET['id'] : 0?>})
			.done(function(json){
				Swal.fire({
					type: "success",
					title: "Ficha finalizada com sucesso",
					showConfirmButton: false,
					timer: 1500,
						onClose: () => {
							/*window.history.back();*/
						}
				});
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}



	function notificar_aluno_coordenacao_email(){
		var email = $("#email").val();
		$.getJSON("modulos/ficha_financeira/cadastro/ajax/notificar_aluno_coordenacao_email.php", {id_ficha_financeira: <?=isset($_GET['id']) ? $_GET['id'] : 0?>, email: email})
			.done(function(json){
				$('#default-example-modal-lg-center').modal('toggle');
				Swal.fire({
					type: "success",
					title: "Notificação enviada com sucesso",
					showConfirmButton: false,
					timer: 1500,
						onClose: () => {
							window.history.back();
						}
				});
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}

</script>