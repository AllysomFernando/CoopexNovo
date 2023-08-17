<?php
	$id_menu = 23;

	if(isset($_GET['id_reoferta'])){
		$id_reoferta = $_GET['id_reoferta'];
	} else {
		$id_reoferta = 0;
	}

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Período de Reofertas</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?=$id_menu?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-calendar'></i> Período de Reofertas
			<small>
				Cadastro de Períodos de Reofertas
			</small>
		</h1>
	</div>
	<iframe src="cadastro_dados.php"></iframe>
	<form class="needs-validation" novalidate>
		<div class="row">
			<div class="col-xl-12">
			  <div id="panel-2" class="panel">
				  <div class="panel-hdr">
						<h2>
							Período de Reofertas
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
										<label class="form-label" for="validationCustom03">Período <span class="text-danger">*</span></label>
										<input type="text" class="form-control" id="pre_inscricao_data_inicio_fixo" placeholder="" value="" required>
										<div class="invalid-feedback">
											Please provide a valid state.
										</div>
									</div>
									<div class="col-md-2 mb-3">
										<label class="form-label" for="validationCustom02">Inicio da Pré-Matrícula <span class="text-danger">*</span></label>
										<input data-inputmask="'mask': '99/99/9999'" type="text" class="form-control periodo_diferente" id="pre_inscricao_data_inicio" placeholder="" value="" required>
										<div class="valid-feedback">
											Looks good!
										</div>
									</div>
									<div class="col-md-2 mb-3">
										<label class="form-label" for="validationCustom02">Fim da Pré-Matrícula <span class="text-danger">*</span></label>
										<input data-inputmask="'mask': '99/99/9999'" type="text" class="form-control periodo_diferente" id="pre_inscricao_data_final" placeholder="" value="" required>
										<div class="valid-feedback">
											Looks good!
										</div>
									</div>
									<div class="col-md-2 mb-3">
										<label class="form-label" for="validationCustom02">Inicio da Matrícula <span class="text-danger">*</span></label>
										<input data-inputmask="'mask': '99/99/9999'" type="text" class="form-control periodo_diferente" id="inscricao_data_inicial" placeholder="" value="" required>
										<div class="valid-feedback">
											Looks good!
										</div>
									</div>
									<div class="col-md-2 mb-3">
										<label class="form-label" for="validationCustom02">Fim da Matrícula <span class="text-danger">*</span></label>
										<input data-inputmask="'mask': '99/99/9999'" type="text" class="form-control periodo_diferente" id="inscricao_data_final" placeholder="" value="" required>
										<div class="valid-feedback">
											Looks good!
										</div>
									</div>
								</div>
								<div class="form-row form-group">
									<div class="col-md-4 mb-3">
										<label class="form-label">Período Ativo</label>
										<div class="custom-control custom-switch">
											<input contenteditable="" type="checkbox" class="custom-control-input is-invalid" id="select_periodo_diferente" required="">
											<label class="custom-control-label" for="select_periodo_diferente">Permitir cadastro de novas reofertas neste período</label>
										</div>
									</div>
									
								</div>
							</div>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
							<button class="btn btn-primary ml-auto" type="submit">Cadastrar</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</main>
<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script>

	//CARREGA A CARGA HORÁRIA DA DISCIPLINA SELECIONADA
	function carrega_carga_horaria_disciplina(){
		$.getJSON("modulos/reoferta/cadastro/ajax/carrega_carga_horaria_disciplina.php", {id_disciplina: $("#id_disciplina").val()})
		.done(function(json){
			$("#carga_horaria_disciplina").val(json);
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}

	//CARREGAS AS DISCIPLINAS REFERENTES AO CURSO SELECIONADO
	function carrega_disciplina(){
		$("#carga_horaria_disciplina").val('');

		$("#id_disciplina").attr("disabled", true);

		$.getJSON("modulos/reoferta/cadastro/ajax/carrega_disciplina.php", {id_curso: $("#id_curso").val()})
		.done(function(json){
			$("#id_disciplina").empty();
			$("#id_disciplina").append("<option value=''>Seleciona a Disciplina</option>");
			$.each( json, function( i, item ) {
				console.log(item);
				$("#id_disciplina").append('<option value="'+item.atc_id_atividade+'">'+item.atc_nm_atividade+'</option>');
				$("#id_disciplina").attr("disabled", false);
			});
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
	}

	//CARREGA OS PERÍODOS DE REOFERTAS
	function carrega_periodo(){
	$("#carga_horaria_disciplina").val('');

		if($("#id_periodo").val()){
			$("#select_periodo_diferente").attr("disabled", false);
			periodo_diferente();
			
			$.getJSON("modulos/reoferta/cadastro/ajax/carrega_periodo.php", {id_periodo: $("#id_periodo").val()})
			.done(function(json){
				$("#pre_inscricao_data_inicio_fixo").val(json.pre_inscricao_data_inicio);
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

			$("#pre_inscricao_data_inicio_fixo").val("");
			$("#pre_inscricao_data_final_fixo").val("");
			$("#inscricao_data_inicial_fixo").val("");
			$("#inscricao_data_final_fixo").val("");
		}
	}

	//HABILITA OS CAMPOS REFERENTES AO PERÍODO DIFERENTE DO PRE-DEFINIDO
	function periodo_diferente(){
		if($("#select_periodo_diferente").prop('checked')){
			$(".periodo_diferente").attr("disabled", false);
		} else {
			$(".periodo_diferente").attr("disabled", true);
			$("#pre_inscricao_data_inicio").focus();
		}
	}


	// function disciplina_equivalente(){
	// 	if($("#select_disciplina_equivalente").prop('checked')){
	// 		$("#id_equivalente").attr("disabled", false);
	// 	} else {
	// 		$("#id_equivalente").attr("disabled", true);
	// 		$("#pre_inscricao_data_inicio").focus();
	// 	}
	// }

	//CARREGAS AS DISCIPLINAS EQUIVALENTE MEDIANTE PESQUISA DO USUÁRIO
	function carrega_disciplina_equivalente(){
		$.getJSON("modulos/reoferta/cadastro/ajax/carrega_disciplina_equivalente.php", {id_curso: $("#id_curso").val()})
		.done(function(json){
			$("#id_equivalente").empty();
			$("#id_equivalente").append("<option value=''>Seleciona a Disciplina</option>");
			$.each( json, function( i, item ) {
				console.log(item);
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

		$(":input").inputmask();
		$('.select2').select2();


		$("#select_periodo_diferente").change(function() {
			periodo_diferente();
		});

		$("#select_disciplina_equivalente").change(function() {
			carrega_disciplina_equivalente();
		});

		$("#id_periodo").change(function() {
			carrega_periodo();
		});

		$("#id_curso").change(function() {
			carrega_disciplina();
		});

		$("#id_disciplina").change(function() {
			carrega_carga_horaria_disciplina();
		});


		//SELECT DISCIPLINA--------------------------------------------------------------------------		
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
			placeholder: 'Buscando...',
			escapeMarkup: function(markup){
				return markup;
			},
			minimumInputLength: 3,
			templateResult: formatoDisciplina,
			templateSelection: formatoTextoDisciplina
		});

		function formatoDisciplina(repo){
			if (repo.loading){
				return repo.text;
			}

			var markup = "<div class='select2-result-repository clearfix d-flex'>" +
				"<div class='select2-result-repository__avatar mr-2'><img src='https://www2.fag.edu.br/coopex3/img/departamentos/"+repo.crs_id_curso+".png' class='width-2 height-2 mt-1 rounded-circle' /></div>" +
				"<div class='select2-result-repository__meta'>" +
				"<div><span class='select2-result-repository__title fs-lg fw-500'>" + repo.disciplina + "</span>"+ " (" + repo.atc_qt_horas + " - " +  repo.crs_id_curso +  " horas)</div>";
			markup += "<div class='select2-result-repository__description fs-xs opacity-80 mb-1'>" + repo.curso + "</div>";
			markup += "</div></div>";
			return markup;
		}

		function formatoTextoDisciplina(repo){
			return repo.disciplina || '';
		}
		//FIM SELECT DISCIPLINA--------------------------------------------------------------------------


		//SELECT USUÁRIO---------------------------------------------------------------------------------
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
			placeholder: 'Search for a repository',
			escapeMarkup: function(markup){
				return markup;
			}, // let our custom formatter work
			minimumInputLength: 3,
			templateResult: formatoUsuario,
			templateSelection: formatoTextoUsuario
		});

		function formatoUsuario(repo){
			if (repo.loading){
				return repo.text;
			}

			var markup = "<div class='select2-result-repository clearfix d-flex'>" +
				"<div class='select2-result-repository__avatar mr-2'><img src='https://www2.fag.edu.br/coopex3/img/avatars/avatar-"+repo.sexo+".png' class='width-2 height-2 mt-1 rounded-circle' /></div>" +
				"<div class='select2-result-repository__meta'>" +
				"<div><span class='select2-result-repository__title fs-lg fw-500'>" + repo.nome + "</span>"+ " (" + repo.usuario + ")</div>";

			
			markup += "<div class='select2-result-repository__description fs-xs opacity-80 mb-1'>" + repo.tipo_descricao + "</div>";

			markup += "</div></div>";

			return markup;
		}

		function formatoTextoUsuario(repo){
			return repo.nome || '';
		}
		//SELECT USUÁRIO---------------------------------------------------------------------------------


		

	
	})	

	// Example starter JavaScript for disabling form submissions if there are invalid fields
	// (function()
	// {
	// 	'use strict';
	// 	window.addEventListener('load', function()
	// 	{
	// 		// Fetch all the forms we want to apply custom Bootstrap validation styles to
	// 		var forms = document.getElementsByClassName('needs-validation');
	// 		// Loop over them and prevent submission
	// 		var validation = Array.prototype.filter.call(forms, function(form)
	// 		{
	// 			form.addEventListener('submit', function(event)
	// 			{
	// 				if (form.checkValidity() === false)
	// 				{
	// 					event.preventDefault();
	// 					event.stopPropagation();
	// 				}
	// 				form.classList.add('was-validated');
	// 			}, false);
	// 		});
	// 	}, false);
	// })();


            $(document).ready(function()
            {

                /* 
                NOTES:
                	
                	Column id
                	---------------------------------------------------
                	Please always keep in mind that DataTable framework allows two different kinds of "rows": Arrays and Objects. In first case columns are indexed through integers; in second case columns are indexed by their attribute name. Usually JSON's use the Object approach, but we cannot be sure.


                	Row key
                	---------------------------------------------------
                	There is no default key in the table. Inside your callback functions, probably you will need a row key to build URL's, in that case you can get them from the rowdata parameter.


                COLUMN DEFINITIONS:

                	title = "string" - title name on table header th and on form labels
                	---------------------------------------------------
                	id = "string" - id assigned to imput element when editing/adding in modal
                	---------------------------------------------------
                	data = "string"  - data name from the dataset
                	---------------------------------------------------
                	type = "text" | "select" | "hidden" | "readonly"  - Type of HTML input to be shown.
                	---------------------------------------------------
                	hoverMsg = "some msg" - The message will appear as a tooltip over the input field.
                	---------------------------------------------------
                	pattern = r.e.  - If type is "input", the typed text will be matched against given regular expression, before submit.
                	---------------------------------------------------
                	msg = "some string" - An error message that is displayed in case pattern is not matched. Set HTML "data-errorMsg" attribute.
                	---------------------------------------------------
                	maxLength = integer - If type is "input", set HTML "maxlength" attribute.
                	---------------------------------------------------
                	options = ["a", "b", "c"] - If type is "select", the options that shall be presented.
                	---------------------------------------------------
                	select2 = {} - If type is "select", enable a select2 component. Select2 jQuery plugin must be linked. More select2 configuration options may be passed within the array.
                	---------------------------------------------------
                	datepicker = {} - If type is "text", enable a datepicker component. jQuery-UI plugin must be linked. More datepicker configuration options may be passed within the array.
                	---------------------------------------------------
                	multiple = true | false - Set HTML "multiple" attribute (for use with select2).
                	---------------------------------------------------
                	unique = true | false - Ensure that no two rows have the same value. The check is performed client side, not server side. Set HTML "data-unique" attribute. (Probably there's some issue with this).
                	---------------------------------------------------
                	uniqueMsg = "some string" - An error message that is displayed when the unique constraint is not respected. Set HTML "data-uniqueMsg" attribute.
                	---------------------------------------------------
                	special = "any string" - Set HTML "data-special" attribute (don't know what's that needed for).
                	---------------------------------------------------
                	defaultValue = "any string" - Adds a default value when adding a row
                	---------------------------------------------------
                */


                // Event Lot
                var events = $("#app-eventlog");

                // Column Definitions
                var columnSet = [
                {
                    title: "Data",
                    id: "adate",
                    data: "adate",
                    type: "date",
                    pattern: "((?:19|20)\d\d)-(0?[1-9]|1[012])-([12][0-9]|3[01]|0?[1-9])",
                    placeholderMsg: "dd-mm-yyyy",
                    errorMsg: "*Invalid date format. Format must be yyyy-mm-dd"
                },
                {
                    title: "Horário de Início",
                    id: "adate",
                    data: "adate",
                    type: "time",
                    pattern: "((?:19|20)\d\d)-(0?[1-9]|1[012])-([12][0-9]|3[01]|0?[1-9])",
                    placeholderMsg: "yyyy-mm-dd",
                    errorMsg: "*Invalid date format. Format must be yyyy-mm-dd"
                },
                {
                    title: "Horário de Término",
                    id: "adate",
                    data: "adate",
                    type: "time",
                    pattern: "((?:19|20)\d\d)-(0?[1-9]|1[012])-([12][0-9]|3[01]|0?[1-9])",
                    placeholderMsg: "yyyy-mm-dd",
                    errorMsg: "*Invalid date format. Format must be yyyy-mm-dd"
                },
                {
                    title: "Horas Aula",
                    id: "balance",
                    data: "balance",
                    type: "readonly",
                    placeholderMsg: "Amount due",
                    defaultValue: "0"
                }]

                /* start data table */
                var myTable = $('#dt-basic-example').dataTable(
                {
                    /* check datatable buttons page for more info on how this DOM structure works */
                    dom: "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    ajax: "modulos/reoferta/cadastro/ajax/cronograma.php?id_reoferta="+<?php echo $id_reoferta?>,
                    columns: columnSet,
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
                        targets: 1,
                        render: function(data, type, full, meta)
                        {
                            var badge = {
                                "active":
                                {
                                    'title': 'Active',
                                    'class': 'badge-success'
                                },
                                "inactive":
                                {
                                    'title': 'Inactive',
                                    'class': 'badge-warning'
                                },
                                "disabled":
                                {
                                    'title': 'Disabled',
                                    'class': 'badge-danger'
                                },
                                "partial":
                                {
                                    'title': 'Partial',
                                    'class': 'bg-danger-100 text-white'
                                }
                            };
                            if (typeof badge[data] === 'undefined')
                            {
                                return data;
                            }
                            return '<span class="badge ' + badge[data].class + ' badge-pill">' + badge[data].title + '</span>';
                        },
                    }, ],

                    /* default callback for insertion: mock webservice, always success */
                    onAddRow: function(dt, rowdata, success, error)
                    {
                        console.log("Missing AJAX configuration for INSERT");
                        success(rowdata);

                        // demo only below:
                        events.prepend('<p class="text-success fw-500">' + JSON.stringify(rowdata, null, 4) + '</p>');
                    },
                    onEditRow: function(dt, rowdata, success, error)
                    {
                        console.log("Missing AJAX configuration for UPDATE");
                        success(rowdata);

                        // demo only below:
                        events.prepend('<p class="text-info fw-500">' + JSON.stringify(rowdata, null, 4) + '</p>');
                    },
                    onDeleteRow: function(dt, rowdata, success, error)
                    {
                        console.log("Missing AJAX configuration for DELETE");
                        success(rowdata);

                        // demo only below:
                        events.prepend('<p class="text-danger fw-500">' + JSON.stringify(rowdata, null, 4) + '</p>');
                    },
                });

            });

        </script>