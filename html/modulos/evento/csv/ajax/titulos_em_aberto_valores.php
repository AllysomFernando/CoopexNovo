<?php session_start();
	
	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	$campo = $_GET['tabela'] == "reoferta_matricula" ? "id_matricula" : "id_inscricao";
	$sql = "SELECT
				* 
			FROM
				reoferta
				INNER JOIN ".$_GET['tabela']." USING ( id_reoferta ) 
			WHERE
				$campo = ?";
	$reoferta = $coopex->prepare($sql);
	$reoferta->bindParam(1, $_GET['id_registro']);
	$reoferta->execute();
	$reoferta = $reoferta->fetch(PDO::FETCH_OBJ);

	//print_r($reoferta);
	//selecionas os boletos das matrículas
	$sql = "SELECT
					*
				FROM
					coopex_cascavel.reoferta_recebimento 
				WHERE
					id_registro = ? 
					AND tabela = ?";
	$pagamento = $coopex->prepare($sql);
	$pagamento->bindParam(1, $_GET['id_registro']);
	$pagamento->bindParam(2, $_GET['tabela']);
	$pagamento->execute();
	$total_registros = $pagamento->rowCount();
?>
<form id="form" onSubmit="validar(); return false" action="modulos/tesouraria/receber/ajax/titulos_em_aberto_valores_cadastro.php" class="modal-content" method="post" target="frame">
	<iframe id="frame" name="frame" style="display: none" ></iframe>

	<div class="modal-header panel-hdr">
		<h5 class="modal-title"><i class="subheader-icon ni ni-book-open"></i><?php echo $reoferta->disciplina_descricao?></h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true"><i class="fal fa-times"></i></span>
		</button>
	</div>

	<div class="modal-body">
		<div class="row">
			<div class="form-group mt-3 col-4">
				<div class="input-group">
					<div class="input-group-prepend">
						<label class="input-group-text" for="inputGroupSelect01">Parcelas</label>
					</div>
					<select class="custom-select" id="parcelas">
						<option <?php echo $total_registros == 1 ? "selected" : ""?> value="1">&Agrave; vista</option>
						<option <?php echo $total_registros == 2 ? "selected" : ""?> value="2">2</option>
						<option <?php echo $total_registros == 3 ? "selected" : ""?> value="3">3</option>
						<option <?php echo $total_registros == 4 ? "selected" : ""?> value="4">4</option>
						<option <?php echo $total_registros == 5 ? "selected" : ""?> value="5">5</option>
						<option <?php echo $total_registros == 6 ? "selected" : ""?> value="6">6</option>
					</select>
				</div>
			</div>
			<div class="form-group mt-3 col-8">
				<h1 class="subheader-title text-right"><small>Valor a receber</small></h1>
				<h1 class="subheader-title text-right">
					R$ <?php echo $_GET['tabela'] == "reoferta_matricula" ? number_format($reoferta->valor, 2, ',', '.') : "55,00"?>
				</h1>
			</div>
		</div>
		
		<div class="form-group">
			<h1 class="subheader-title">
				<small>
					Pagamentos
				</small>
			</h1>
			<?
				$total = 0;
				for($i=1; $i<=6; $i++){
					$row = $pagamento->fetch(PDO::FETCH_OBJ);
			?>
			<input type="hidden" name="editar[]" value="<?php echo isset($row->id_reoferta_recebimento) ? $row->id_reoferta_recebimento : ""?>">
			<input type="hidden" name="id_registro[]" value="<?php echo isset($row->id_registro) ? $row->id_registro : $_GET['id_registro']?>">
			<input type="hidden" name="id_pessoa[]" value="<?php echo isset($row->id_pessoa) ? $row->id_pessoa : $_SESSION['coopex']['usuario']['id_pessoa']?>">
			<input type="hidden" name="data_registro[]" value="<?php echo isset($row->data_registro) ? $row->data_registro : ""?>">
			<input type="hidden" name="data_recebimento[]" value="<?php echo isset($row->data_recebimento) ? $row->data_recebimento : ""?>">
			<input type="hidden" name="tabela[]" value="<?php echo isset($row->tabela) ? $row->tabela : $_GET['tabela']?>">
			<div class="input-group grupo_recebimento recebimento<?php echo $i?> mt-2">
				<div class="input-group-prepend">
					<span class="input-group-text text-success">
						<i class="fal fa-money-bill-alt fs-xl"></i>
					</span>
				</div>
				<select <?php echo $row->recebido ? "disabled" : ""?> name="id_reoferta_tipo_pagamento[]" class="custom-select"  aria-label="usertype">
					<option <?php echo isset($row->id_reoferta_tipo_pagamento) ? $row->id_reoferta_tipo_pagamento == 1 ? "selected" : "" : ""?> value="1">Dinheiro</option>
					<option <?php echo isset($row->id_reoferta_tipo_pagamento) ? $row->id_reoferta_tipo_pagamento == 2 ? "selected" : "" : ""?> value="2">Cheque</option>
					<option <?php echo isset($row->id_reoferta_tipo_pagamento) ? $row->id_reoferta_tipo_pagamento == 3 ? "selected" : "" : ""?> value="3">Boleto</option>
				</select>
				<div class="input-group-append input-group-prepend">
					<span class="input-group-text">
						<i class="fal fa-calendar-alt fs-xl"></i>
					</span>
				</div>
				<input <?php echo $row->recebido ? "disabled" : ""?> value="<?php echo isset($row->data_vencimento) ? converterData($row->data_vencimento) : ""?>" name="data_vencimento[]" type="text" class="form-control data" placeholder="Data">
				<div class="input-group-append input-group-prepend">
					<span class="input-group-text">
						<i class="fal fa-dollar-sign fs-xl"></i>
					</span>
				</div>
				<input <?php echo $row->recebido ? "disabled" : ""?> value="<?php echo isset($row->valor) ? number_format($row->valor, 2, ',', '.') : "0";?>" name="valor[]" type="text" class="form-control moeda" im-insert="true" style="text-align: right;">
				
				<div class="input-group-append">
					<div class="input-group-text">
						<div class="custom-control custom-checkbox">
							<input value="<?php echo isset($row->recebido) ? $row->recebido : "0"?>" name="recebido[]" type="hidden" id="recebido<?php echo $i?>">
							<input <?php echo $row->recebido ? "checked disabled" : ""?> value="<?php echo $i?>" type="checkbox" class="custom-control-input check_recebimento" id="checkmeout<?php echo $i?>">
							<label class="custom-control-label" for="checkmeout<?php echo $i?>">Recebido</label>
						</div>
					</div>
				</div>
			</div>
			<?
					$total += isset($row->valor) ? $row->valor : 0;
				}
			?>
		</div>
		<h1 id="total" class="text-right"><strong>R$ <?php echo number_format($total, 2, ',', '.'); ?></strong></h1>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
		<button id="bt_salvar" type="submit" class="btn btn-primary"><span class="fal fa-save mr-1"></span>Salvar Altera&ccedil;&otilde;es</button>
		<a href="tesouraria/receber/recibo/<?php echo $_GET['id_registro']?>"><button id="bt_imprimir" style="display: none" type="button" class="btn btn-info"><span class="fal fa-print mr-1"></span>Imprimir Comprovante</button></a>
	</div>
</form>
<script src="js/formplugins/inputmask/jquery.maskMoney.min.js"></script>
<script>
	
var valor_pago = 0;
var valor_a_pagar = <?php echo $_GET['tabela'] == "reoferta_matricula" ? $reoferta->valor : 55?>;
function validar(){
	if(valor_pago != valor_a_pagar){
		alert("Valor recebido diferente do valor a receber");
	} else {
		$("#form").submit();
	}
}	
	
function sucesso(){
	$("#bt_salvar").hide();
	$("#bt_imprimir").show();
}
	
function formataDinheiro(n) {
	return "R$ " + n.toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+\,)/g, "$1.");
}
	
$(".moeda").keyup(function() {
	var aux = 0;
	var valor = 0;
	var str = ""
	$(".moeda").each(function(){
		str = $(this).val();
		//console.log(str);
		str = str.replace("R$", "");
		str = str.replace(".", "");
		str = str.replace(",", ".");
		valor = parseFloat(str);
		//console.log("valor "+valor);
		aux = aux + valor;
		//console.log(aux);
	});
	valor_pago = aux;
	$("#total").html(formataDinheiro(aux));
});

$(document).ready(function(){
	
	$(".grupo_recebimento").hide();
	var aux = <?php echo $total_registros ? $total_registros : "1"?>;
	for(var i=1; i<=aux; i++){
		console.log("#recebimento"+i);
		$(".recebimento"+i).show();
	}
	
	$(".moeda").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: false});

	$('.data').datepicker(
	{
		todayHighlight: true,
		orientation: "bottom left",
		format: 'dd/mm/yyyy'
	});
	
	
	
	$(".check_recebimento").click(function() {
		if($("#recebido"+$(this).val()).val() == 1){
			$("#recebido"+$(this).val()).val("0");
		} else {
			$("#recebido"+$(this).val()).val("1");
		}
	});
	
	


	
	$("#parcelas").change(function() {
		$(".grupo_recebimento").hide();
		
		var aux = $(this).val();
		for(var i=1; i<=aux; i++){
			console.log("#recebimento"+i);
			$(".recebimento"+i).show();
		}
		
		//pagamentos($(this).attr('value'), 'reoferta_matricula');
	});

	
});
</script>