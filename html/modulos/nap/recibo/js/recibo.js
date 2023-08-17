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
				"<div class='select2-result-repository__avatar mr-2'><img src='https://coopex.fag.edu.br/img/demo/avatars/avatar-"+repo.sexo+".png' class='width-2 height-2 mt-1 rounded-circle' /></div>" +
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
	$("#titulos_em_aberto_resultado").load("modulos/tesouraria/receber/ajax/titulos_em_aberto.php?id_usuario="+data.id);
	
});
