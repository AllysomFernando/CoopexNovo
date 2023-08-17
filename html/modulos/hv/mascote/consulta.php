<?
	//CONFIG
	$id_menu = 20;
?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">

<main id="js-page-content" role="main" class="page-content">
<ol class="breadcrumb page-breadcrumb">
	<li class="breadcrumb-item"><a href="javascript:void(0);">Hospital Veterinário</a></li>
	<li class="breadcrumb-item active">Clube do Mascote</li>
	<li class="position-absolute pos-top pos-right d-none d-sm-block"><code>ID: <?=$id_menu?></code></li>
</ol>
<div class="subheader">
	<h1 class="subheader-title">
		<i class='subheader-icon fal fa-first-aid'></i> Clube do Mascote
		<small>
			Consultar animais no Clube do Mascote
		</small>
	</h1>
</div>
<div class="row">
	<div class="col-xl-12">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>
					Acadêmico/Colaborador
				</h2>
				<div class="panel-toolbar">
					<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Minimizar"></button>
					<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Tela Cheia"></button>
				</div>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					
					<div class="form-group">
						<label class="form-label" for="select2-ajax">
							Selecione o usuário do Sagres
						</label>
						<select onChange="" data-placeholder="Select a state..." class="js-consultar-usuario form-control" id="select2-ajax"></select>
					</div>
					
					<div id="titulos_em_aberto_resultado">
					</div>

				</div>
			</div>
		</div>
		
	</div>
	
</div>

</main>

<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>

<script>
$(document).ready(function()
{
	
	
	$(function()
	{
		$('.select2').select2();

		$(".js-consultar-usuario").select2(
		{
			ajax:
			{
				url: "modulos/_core/buscar_usuario.php",
				dataType: 'json',
				delay: 250,
				data: function(params)
				{
					return {
						q: params.term, // search term
						page: params.page
					};
				},
				processResults: function(data, params)
				{
					// parse the results into the format expected by Select2
					// since we are using custom formatting functions we do not need to
					// alter the remote JSON data, except to indicate that infinite
					// scrolling can be used
					console.log(data);
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
			escapeMarkup: function(markup)
			{
				return markup;
			}, // let our custom formatter work
			minimumInputLength: 3,
			templateResult: formatRepo,
			templateSelection: formatRepoSelection
		});

		function formatRepo(repo)
		{
			if (repo.loading)
			{
				return repo.text;
			}

			var markup = "<div class='select2-result-repository clearfix d-flex'>" +
				"<div class='select2-result-repository__avatar mr-2'><img src='https://www2.fag.edu.br/coopex3/img/demo/avatars/avatar-"+repo.sexo+".png' class='width-2 height-2 mt-1 rounded-circle' /></div>" +
				"<div class='select2-result-repository__meta'>" +
				"<div><span class='select2-result-repository__title fs-lg fw-500'>" + repo.nome + "</span>"+ " (" + repo.usuario + ")</div>";

			
			markup += "<div class='select2-result-repository__description fs-xs opacity-80 mb-1'>" + repo.tipo_descricao + "</div>";

			markup += "</div></div>";

			return markup;
		}

		function formatRepoSelection(repo)
		{
			return repo.nome || '';
		}

	});
});

$('#select2-ajax').on('select2:select', function (e) {
	var data = e.params.data;
	console.log(data.id);
	$("#titulos_em_aberto_resultado").load("modulos/hv/mascote/ajax/mascote.php?id_usuario="+data.id);
	
});

</script>