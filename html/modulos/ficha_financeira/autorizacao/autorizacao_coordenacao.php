<?php session_start();
	require_once("php/sqlsrv.php");
	require_once("php/mysql.php");


	require_once("modulos/ficha_financeira/funcoes_sagres.php");

	//print_r($_SESSION);

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	$id_menu = 34;
	$chave	 = "id_autorizacao";

	unset($_SESSION['ficha_financeira']);

	$_SESSION['ficha_financeira']['carga_horaria'] 								= 0;
	$_SESSION['ficha_financeira']['carga_horaria_pacote'] 						= 0;
	$_SESSION['ficha_financeira']['carga_horaria_disciplinas_pacote'] 			= 0;
	$_SESSION['ficha_financeira']['carga_horaria_real_disciplinas_pacote'] 		= 0;
	$_SESSION['ficha_financeira']['carga_horaria_disciplinas_fora_pacote'] 		= 0;
	$_SESSION['ficha_financeira']['carga_horaria_real_disciplinas_fora_pacote'] = 0;
	$_SESSION['ficha_financeira']['desconto_dp'] 								= 0;
	$_SESSION['ficha_financeira']['calculo']['ch_dp'] 							= 0;

	if(isset($_GET['id'])){
		$$chave = $_GET['id'];

		$_SESSION['ficha_financeira']['id_ficha_financeira'] = $_GET['id'];
		
		//CARREGA DADOS DA FICHA FINANCEIRA
		$sql = "SELECT
					*,
					DATE_FORMAT( data_cadastro, '%d/%m/%Y - %H:%i:%s' ) AS data_cadastro 
				FROM
					ficha_financeira.autorizacao
					INNER JOIN coopex.pessoa USING ( id_pessoa )
				WHERE
					id_autorizacao = ".$_GET['id'];
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
	.valor_destaque{
		background-color: #000;
		color: #fff;
	}
</style>

<script type="text/javascript">
	function alocar_horario(dia, hora_inicio, hora_termino, codigo, disciplina){
		//alert("#"+dia+"_"+hora_inicio+" - "+codigo+"<br>"+disciplina);

		if(hora_termino < 1300){
			$("#horario_manha").show();
		} else if(hora_inicio > 1300 && hora_inicio < 1900){
			$("#horario_tarde").show();
		} else if(hora_inicio >= 1930){
			$("#horario_noite").show();
		}

		if(hora_inicio == 1900 && hora_termino == 2040){
			$("#"+dia+"_"+hora_inicio).attr("rowspan", 2);
			$("#"+dia+"_"+1950).remove();
			
		} else if(hora_inicio == 2050 && hora_termino == 2230){
			$("#"+dia+"_"+hora_inicio).attr("rowspan", 2);
			$("#"+dia+"_"+2140).remove();
		}

		if($("#"+dia+"_"+hora_inicio).html()){
			$("#"+dia+"_"+hora_inicio).addClass('bg-danger-500');
		}

		
		$("#"+dia+"_"+hora_inicio).append("<p>"+codigo+"<br>"+disciplina+"</p>");
	}

	function alocar_horario_quinzenal(data, dia, hora_inicio, hora_termino, codigo, disciplina, local){
		//alert("#"+dia+"_"+hora_inicio+" - "+codigo+"<br>"+disciplina);
		$("#horario_quinzenal").show();
		$("#quadro_horario_quinzenal").append('<tr><td class="text-center"><strong>'+data+'</strong></td><td><strong>'+codigo+" - "+disciplina+'</strong></td><td class="text-center"><strong>'+dia+'</strong></td><td class="text-center"><strong>'+hora_inicio+'</strong></td><td class="text-center"><strong>'+hora_termino+'</strong></td><td class="text-center"><strong>'+local+'</strong></td></tr>');
	}

</script>

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


	<iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 30%; background-color: #fff; top: 0; left: 0; height: 300px"></iframe>

	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/ficha_financeira/autorizacao/cadastro_dados.php">
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
										<label class="form-label" for="validationCustom03">Curso</label>
										<?php
											$where = "";
											
											/*$id_faculdade = ($_SESSION['coopex']['usuario']['id_pessoa'] == '5000225543') ? "1100000002" : "1000000002";

											$campus = $_SESSION['coopex']['usuario']['pessoa']->id_campus ? " and departamento.id_campus = ".$_SESSION['coopex']['usuario']['pessoa']->id_campus : "";

											if($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 1 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 13 || $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 8){
												$where .= " AND graduacao = 1 and id_campus = $id_faculdade";
											} else {
												$where .= " AND graduacao = 1 AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
											}*/

												$campus = $_SESSION['coopex']['usuario']['pessoa']->id_campus ? " and graduacao = 1 and departamento.id_campus = ".$_SESSION['coopex']['usuario']['pessoa']->id_campus : "";

												#VERIFICA SE O TIPO DE USUÁRIO POSSUI PERMISSÃO PARA ACESSAR TODOS OS REGISTROS
												if(in_array($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'],array(1,2,3,8,9,11,13))){
													$where  = " AND 1=1 ";
												} else {
													$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];
													$where  = "AND (id_pessoa = $id_pessoa
																OR id_departamento IN (SELECT id_departamento FROM coopex.departamento_pessoa WHERE graduacao = 1 and id_pessoa = $id_pessoa)) ";
												}

												if($_SESSION['coopex']['usuario']['id_pessoa'] == 5000208750){
													$where .= " and departamento.id_campus = 1100000002";
												}

												if($_SESSION['coopex']['usuario']['id_pessoa'] == 5000216706){
													$where .= " and id_etapa = 11";
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
															and id_campus = 1000000002
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
														$where $campus
														and id_campus = 1000000002
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
								</div>

								<div class="form-row">
									
									<div class="col-md-8">
										<div class="form-group">
											<label class="form-label" for="select2-ajax">
												Acadêmico
											</label>
											<?php
												if(isset($_GET['id'])){
											?>
												<input type="text" id="nome_academico_input" class="form-control" value="<?php echo isset($dados->id_pessoa) ? utf8_encode($dados->nome) : ""?>">
												<input type="hidden" name="id_pessoa" value="<?php echo isset($dados->id_pessoa) ? utf8_encode($dados->id_pessoa) : ""?>">
											<?php
												} else {
											?>
												<select id="id_pessoa"   name="id_pessoa" data-placeholder="Acadêmico" class="js-consultar-usuario form-control"></select>
											<?php
												}
											?>
										</div>	
									</div>
									<div class="col-md-4">
										<label class="form-label" for="validationCustom03">Mês </label>
										
										<select disabled="" name="mes" class="select2 form-control" required="">
											<option value="">Selecione o Mês</option>
											<option <?=$dados->mes == 1 ? 'selected=""' : ''?> value="1">Janeiro</option>
											<option <?=$dados->mes == 2 ? 'selected=""' : ''?> value="2">Fevereiro</option>
											<option <?=$dados->mes == 3 ? 'selected=""' : ''?> value="3">Março</option>
											<option <?=$dados->mes == 4 ? 'selected=""' : ''?> value="4">Abril</option>
											<option <?=$dados->mes == 5 ? 'selected=""' : ''?> value="5">Maio</option>
											<option <?=$dados->mes == 6 ? 'selected=""' : ''?> value="6">Junho</option>
											<option <?=$dados->mes == 7 ? 'selected=""' : ''?> value="7">Julho</option>
											<option <?=$dados->mes == 8 ? 'selected=""' : ''?> value="8">Agosto</option>
											<option <?=$dados->mes == 9 ? 'selected=""' : ''?> value="9">Setembro</option>
											<option <?=$dados->mes == 10 ? 'selected=""' : ''?> value="10">Outubro</option>
											<option <?=$dados->mes == 11 ? 'selected=""' : ''?> value="11">Novembro</option>
											<option <?=$dados->mes == 12 ? 'selected=""' : ''?> value="12">Dezembro</option>
										</select>
										
										<div class="invalid-feedback">
											Selecione o Mês
										</div>
										
									</div>
								</div>	
								<div class="form-row mt-3">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom03">Observações</label>
										<textarea name="observacao" class="form-control col-md-12"><?php echo isset($dados->id_pessoa) ? utf8_encode($dados->observacao) : ""?></textarea>	
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
						


					</div>
				</div>

			</div>

		</div>

		

		


		
		
		
		<!-- <textarea class="d-none" name="cronograma" id="cronograma" rows="10" cols="100"></textarea> -->
	</form>

	



<input type="hidden" id="numero_whatsapp" value="<?=$whats?>">
<input type="hidden" id="contato_email" value="<?=$email?>">
	
<link rel="stylesheet" media="screen, print" href="css/fa-brands.css">
<link rel="stylesheet" media="screen, print" href="css/fa-regular.css">



</main>

<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/datagrid/datatables/dataTables.editor.min.js"></script>
<script src="js/moment-with-locales.js"></script>
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<script src="https://www2.fag.edu.br/coopex/js/jquery.maskMoney.min.js" type="text/javascript"></script>
<script>

	

	function moeda(valor){
		valor = valor.replace('R$ ', '');
		valor = valor.replace('.', '');
		valor = valor.replace(',', '.');
		return parseFloat(valor);
	}

	function moeda2(valor){
		//console.log(typeof(valor));
		if(typeof(valor) == 'string'){
			valor = valor.replace(',', '.');
			return parseFloat(valor);
		}
	}
 



	//MENSAGEM DE CADASTRO OK
	function cadastroOK(operacao, id_registro){ 
		var msg = operacao == 1 ? "Registro cadastrado com sucesso" : "Registro alterado com sucesso";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				alterar_link_whatsapp(id_registro);
				$('#default-example-modal-lg-center').modal();
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
			var aux = this.value;
			str = aux.split(":");
			
			definir_equivalencia(this.title, str[0], str[1], str[2]);
		});
	}







	$(document).ready(function(){

		$.ajaxSetup({
		    async: false
		});

		$(".moeda_calculo").keyup(function() {
			calculo_pagamento(this.title, this.value);
		});
		$(".moeda_dp").keyup(function() {
			calculo_pagamento_dp(this.title, this.value);
		});


		$('.moeda').maskMoney();

		//CARREGA OS DADOS DOS SELECTS DEPENDENTES QUANDO EDITAR O REGISTRO
		<?php
			if(isset($_GET['id'])){
				if(isset($dados->id_semestre)){
					echo "carrega_pacote($dados->id_turma);";
				}
			}
		?>

		$(":input").inputmask();
		$('.select2').select2();




		
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
				url: "modulos/_core/buscar_usuario.php",
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

	

</script>