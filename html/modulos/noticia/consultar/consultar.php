<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
<script src="js/core.js"></script>

<main id="js-page-content" role="main" class="page-content">
<ol class="breadcrumb page-breadcrumb">
	<li class="breadcrumb-item"><a href="javascript:void(0);">Notícias</a></li>
	<li class="breadcrumb-item">Consultar</li>
	<li class="breadcrumb-item active">Consultar</li>
	<li class="position-absolute pos-top pos-right d-none d-sm-block"><code>ID: 01</code></li>
</ol>
<div class="subheader">
	<h1 class="subheader-title">
		<i class='subheader-icon fal fa-barcode-read'></i> Consultar
		<small>
			Consulta de notícias
		</small>
	</h1>
</div>
<div class="row">
	<div class="col-xl-12">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>
					Nontícia
				</h2>
				<div class="panel-toolbar">
					<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
					<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
					<button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
				</div>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<div class="form-group">
						<label class="form-label" for="select2-ajax">
							Informe o código ou título da notícia
						</label>
						<select onChange="" data-placeholder="" class="js-consultar-usuario form-control" id="select2-ajax"></select>
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
				url: "modulos/_core/buscar_noticia.php",
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
					// console.log(data);
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
			templateResult: formatoUsuario,
			templateSelection: formatoTextoUsuario
		});

		function formatoUsuario(repo)
		{
			if (repo.loading)
			{
				return repo.text;
			}

			console.log(repo);

			var markup = "<div class='select2-result-repository clearfix d-flex'>" +
				"<div class='select2-result-repository__meta'>" +
				"<div><span class='select2-result-repository__title fs-lg fw-500'>" + repo.text + "</span>"+ " (" + repo.id + ")</div>";

			
			markup += "<div class='select2-result-repository__description fs-xs opacity-80 mb-1'>" + repo.editoria + "</div>";

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
	//$("#titulos_em_aberto_resultado").load("modulos/tesouraria/receber/ajax/titulos_em_aberto.php?id_usuario="+data.id);

	window.location = "http://cms.catve.terra.com.br/admin/adm/noticia/cadastro/cadastro/"+data.id;

	
});

</script>