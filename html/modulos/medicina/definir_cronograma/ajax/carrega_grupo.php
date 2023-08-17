<?php session_start();
	
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	$id_periodo = $_GET['id_periodo'];

	$sql = "SELECT
				*
			FROM
				medicina.grupo
			INNER JOIN medicina.grupo_periodo USING (id_grupo_periodo)
			WHERE
				id_periodo = $id_periodo";

	$periodo = $coopex->query($sql);
	while($row = $periodo->fetch(PDO::FETCH_OBJ)){

?>

<div class="card">
<div id="js_list_accordion<?=$row->id_grupo?>" class="accordion accordion-hover accordion-clean">
    <div class="card border-top-left-radius-0 border-top-right-radius-0">
        <div class="card-header">
            <a href="javascript:void(0);" class="card-title collapsed" data-toggle="collapse" data-target="#js_list_accordion-<?=$row->id_grupo?>" aria-expanded="true" data-filter-tags="settings">
                <i class="fal fa-users width-2 fs-xl"></i>
                Grupo <?= $row->grupo?>
                <span class="ml-auto">
                    <span class="collapsed-reveal">
                        <i class="fal fa-chevron-up fs-xl"></i>
                    </span>
                    <span class="collapsed-hidden">
                        <i class="fal fa-chevron-down fs-xl"></i>
                    </span>
                </span>
            </a>
        </div>
        <div id="js_list_accordion-<?=$row->id_grupo?>" class="collapse" data-parent="#js_list_accordion<?=$row->id_grupo?>" style="">
            <div class="card-body">
                <select id="id_pessoa_<?=$row->id_grupo?>" name="id_pessoa_<?=$row->id_grupo?>" data-placeholder="Nome" class="js-consultar-usuario form-control" >
				</select>

				<table id="tabela<?=$row->id_grupo?>" class="table table-striped table-hover mt-4">
					<tbody>
					<?
						$sql2 = "SELECT
									id_pessoa,
									nome
								FROM
									medicina.grupo_pessoa
								INNER JOIN coopex.pessoa USING (id_pessoa)
								WHERE
								id_grupo = $row->id_grupo";

						$res2 = $coopex->query($sql2);
						while($row2 = $res2->fetch(PDO::FETCH_OBJ)){
							echo '<tr id="'.$row->id_grupo.$row2->id_pessoa.'"><td>'.utf8_encode($row2->nome).'</td><td class="text-right"><button onclick="excluir_pessoa_grupo('.$row->id_grupo.', '.$row2->id_pessoa.')" class="btn btn-danger btn-xs btn-icon"><i class="fal fa-times"></i></button></td></tr>';
						}
					?>					
					</tbody>
				</table>
            </div>
        </div>

    </div>

</div>
<span class="filter-message js-filter-message"></span>
</div>
<script type="text/javascript">
	$("#id_pessoa_<?=$row->id_grupo?>").change(function() {
		var grupo = <?=$row->id_grupo?>;
		var data = $("#id_pessoa_<?=$row->id_grupo?>").select2('data');
		console.log(data[0].id);
		adicionar_pessoa_grupo(grupo, data[0].id);
		$("#tabela<?=$row->id_grupo?>").append(`<tr id="${grupo}${data[0].id}"><td>${data[0].nome}</td><td class="text-right"><button onclick="excluir_pessoa_grupo(${grupo}, ${data[0].id})" class="btn btn-danger btn-xs btn-icon"><i class="fal fa-times"></i></button></td></tr>`);
	});
</script>
<?
	}
?>


<script type="text/javascript">
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
</script>