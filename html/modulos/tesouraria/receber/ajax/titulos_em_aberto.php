<?php session_start();

	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	$id_pessoa = $_GET['id_usuario'];
	
	//selecionas os boletos das matrículas
?>

<?	
	$sql = "SELECT
				* 
			FROM
				coopex_reoferta.matricula_boleto
				INNER JOIN coopex_reoferta.matricula m USING ( id_matricula )
				INNER JOIN coopex_reoferta.reoferta USING ( id_reoferta ) 
			WHERE
				m.id_pessoa = $id_pessoa";
	$matricula = $coopex->query($sql);

	$sql = "SELECT
				* 
			FROM
				coopex_reoferta.pre_matricula_boleto
				INNER JOIN coopex_reoferta.pre_matricula m USING ( id_pre_matricula )
				INNER JOIN coopex_reoferta.reoferta USING ( id_reoferta ) 
			WHERE
				m.id_pessoa = $id_pessoa";
	$prematricula = $coopex->query($sql);
		


	if($matricula->rowCount() > 0 || $prematricula->rowCount() > 0){
?>

<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
	<thead class="bg-primary-600">
		<tr>
			<th>Name</th>
			<th>Disciplina</th>
			<th>Vencimento</th>
			<th>Valor</th>
			<th>Situa&ccedil;&atilde;o</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
			while($row = $matricula->fetch(PDO::FETCH_OBJ)){
		?>
		<tr>
			<td><strong>Matr&iacute;cula</strong></td>
			<td><strong><?php echo utf8_encode($row->disciplina)?></strong></td>
			<td><?php echo converterData($row->data_vencimento)?></td>
			<td align="right"><?php echo number_format($row->valor, 2, ',', '.');?></td>
			<td><span class="badge badge-<?php echo $row->pago == 2 ? 'success' : 'danger'?> badge-pill"><?php echo $row->pago ? 'Pago' : 'Em aberto'?></span></td>
			<td align="center"><button value="<?php echo $row->id_matricula?>" type="button" class="btn btn-primary btn-icon rounded-circle hover-effect-dot waves-effect waves-themed botao_receber_matricula" data-toggle="modal" data-target="#pagamentos_modal"><i class="fal fa-plus"></i></button></td>
		</tr>
		<?php
			}
		?>
		
		<?php
			while($row = $prematricula->fetch(PDO::FETCH_OBJ)){
		?>
		<tr>
			<td><strong>Pr&eacute;-Matr&iacute;cula</strong></td>
			<td><strong><?php echo utf8_encode($row->disciplina)?></strong></td>
			<td><?php echo converterData($row->data_vencimento)?></td>
			<td align="right"><?php echo number_format($row->valor, 2, ',', '.');?></td>
			<td><span class="badge badge-<?php echo $row->boleto_pago == 2 ? 'success' : 'danger'?> badge-pill"><?php echo $row->boleto_pago ? 'Pago' : 'Em aberto'?></span></td>
			<td align="center"><button value="<?php echo $row->id_pre_matricula?>" type="button" class="btn btn-primary btn-icon rounded-circle hover-effect-dot waves-effect waves-themed botao_receber_prematricula" data-toggle="modal" data-target="#pagamentos_modal"><i class="fal fa-plus"></i></button></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>
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
<?php
	} else {
		echo utf8_decode('<div class="alert alert-danger" role="alert"><strong>Ops!</strong> Nenhum título encontrado.</div>');
	}
?>