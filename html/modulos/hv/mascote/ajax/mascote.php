<?php session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");
	
	//selecionas os boletos das matrículas
	$sql = "SELECT
				* 
			FROM
				coopex_hv.clube_mascote 
			WHERE
				id_pessoa = ?";
	$mascote = $coopex_antigo->prepare($sql);
	$mascote->bindParam(1, $_GET['id_usuario']);
	$mascote->execute();

	//echo $mascote->rowCount();

	if($mascote->rowCount() == 1){
		$colunas_xl = 12;
	} else if($mascote->rowCount() == 2){
		$colunas_xl = 6;
	} else if($mascote->rowCount() == 3){
		$colunas_xl = 4;
	}

	if($mascote->rowCount() > 0){
?>
<style>
	table tr td{
		vertical-align: middle !important;
	}
</style>
<div class="row">
	<?
		while($row = $mascote->fetch(PDO::FETCH_OBJ)){
	?>
	<div class="col-lg-6 col-xl-<?php echo $colunas_xl?> order-lg-1 order-xl-1">
		<div class="card mb-g rounded-top">
			<div class="row no-gutters row-grid">
				<div class="col-12">
					<div class="d-flex flex-column align-items-center justify-content-center p-4">
						<div class="rounded-circle shadow-2 img-thumbnail">
							<div class="rounded-circle shadow-2 img-thumbnail" style="width: 130px; height: 130px; background: url(<?php echo $row->imagem ? "https://www2.fag.edu.br/hospitalveterinario/clubedomascote/fotos/".$row->imagem : 'https://www2.fag.edu.br/hospitalveterinario/clubedomascote/img/foto.png'?>) center; background-size: cover"></div>
						</div>
						
						<h5 class="mb-0 fw-700 text-center mt-3">
							<?php echo utf8_encode($row->nome)?>
							<small class="text-muted mb-0"><?php echo utf8_encode($row->especie)?>, <?php echo utf8_encode($row->raca)?></small>
						</h5>
					</div>
				</div>
				<div class="col-6">
					<div class="text-center py-3">
						<h5 class="mb-0 fw-700">
							<small class="text-muted mb-0">Idade</small>
							<?php echo utf8_encode($row->idade)?>
						</h5>
					</div>
				</div>
				<div class="col-6">
					<div class="text-center py-3">
						<h5 class="mb-0 fw-700">
							<small class="text-muted mb-0">Gênero</small>
							<?php echo $row->genero == "M" ? "Macho" : "Fêmea"?>
						</h5>
					</div>
				</div>
				<div class="col-12">
					<div class="p-3 text-center">
						<a href="javascript:void(0);" class="btn-link font-weight-bold">Excluir</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?
		}
	?>
</div>	
<div class="modal fade" id="pagamentos_modal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document" id="pagamentos_modal_conteudo">
	</div>
</div>
       
<script>
$(document).ready(function(){
	$('#dt-basic-example').dataTable(	{
		responsive: true,
		pageLength: 15,
		order: [[2, 'desc']],
		rowGroup:{dataSrc: 0},
		"columnDefs": [{ "visible": false, "targets": 0 }]
	});
	
	$(".botao_receber_matricula").click(function() {
		pagamentos($(this).attr('value'), 'reoferta_matricula');
	});
	$(".botao_receber_prematricula").click(function() {
		pagamentos($(this).attr('value'), 'reoferta_pre_inscricao');
	});
	
	function pagamentos(id, tabela){
		$("#pagamentos_modal_conteudo").load("modulos/tesouraria/receber/ajax/titulos_em_aberto_valores.php?id_registro="+id+"&tabela="+tabela);
	}
	
	$(":input").inputmask();
	
	$('.data').datepicker({
		todayHighlight: true,
		orientation: "bottom left",
		locale: "pt-BR"
	});
});
</script>
<?
	} else {
		echo utf8_decode('<div class="alert alert-danger" role="alert"><strong>Ops!</strong> Nenhum mascote encontrado.</div>');
	}
?>