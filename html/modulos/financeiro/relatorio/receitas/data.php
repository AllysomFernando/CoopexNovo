<link rel="stylesheet" media="screen, print" href="css/formplugins/bootstrap-daterangepicker/bootstrap-daterangepicker.css">
<main id="js-page-content" role="main" class="page-content naoimprimir">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Financeiro</a></li>
		<li class="breadcrumb-item">Relatórios</li>
		<li class="breadcrumb-item active">Boletos Pagos Bradesco</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-credit-card-front'></i> BOLETOS PAGOS BRADESCO<sup class='badge badge-primary fw-500'>NOVO</sup>
			<small>
				Relatório baixas do arquivo de retorno do Bradesco
			</small>
		</h1>
	</div>
	
	<div class="row">
		<div class="col-xl-12">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Relatório <span class="fw-300"><i>NAP</i></span>
					</h2>
					<div class="panel-toolbar">
						<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
						<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						<button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<div class="panel-tag">
							Clique na data inicial e na data final para selecionar o período, clique sobre a data duas vezes para selecionar uma única data
						</div>

						<form class="form-group row" method="post" action="" >
							<div class="col-6">
								<label class="col-form-label form-label">Selecione o período</label>
								<div class="input-group">
									<input name="intervalo" type="text" class="form-control" placeholder="Select date" id="datepicker-2">
									<div class="input-group-append">
										<span class="input-group-text fs-xl">
											<i class="fal fa-calendar"></i>
										</span>
									</div>
								</div>
								<button type="submit" class="btn btn-primary waves-effect waves-themed mt-5">Gerar Relatório</button>
								<button onClick="window.print();" id="bt_imprimir" type="button" class="btn btn-info mt-3"><span class="fal fa-print mr-1"></span>Imprimir Relatório</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	
</main>


<?php
	//include "nap_relatorio.php";
?>


<script src="js/dependency/moment/moment.js"></script>
<script src="js/formplugins/bootstrap-daterangepicker/bootstrap-daterangepicker.js"></script>
<script>
	$(document).ready(function(){
		$('#datepicker-2, #datepicker-modal-3').daterangepicker({
			startDate: moment().startOf('hour'),
			endDate: moment().startOf('hour').add(32, 'hour'),
			locale:
			{
				format: 'DD/MM/YYYY'
			}
		});

		$(function(){

			var start = moment().subtract(29, 'days');
			var end = moment();

			function cb(start, end)
			{
				$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
			}

			$('#datepicker-4').daterangepicker(
			{
				startDate: start,
				endDate: end,
				ranges:
				{
					'Today': [moment(), moment()],
					'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
					'Last 7 Days': [moment().subtract(6, 'days'), moment()],
					'Last 30 Days': [moment().subtract(29, 'days'), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
				}
			}, cb);

			cb(start, end);

		});
	});

</script>

<?php 
	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	$aux = explode(" - ", $_POST['intervalo']);
	if(count($aux)){
		$data_inicio = converterData($aux[0]);
		$data_fim = converterData($aux[1]);
	} else {
		$data_inicio = converterData($aux[0]);
		$data_fim = converterData($aux[0]);
	}
	
?>
<link rel="stylesheet" media="screen, print" href="css/page-invoice.css">
<main id="js-page-content" role="main" class="page-content">

	<div class="container">
		<div data-size="A4">
			<div class="row">
				<div class="col-sm-12">
					<div class="d-flex align-items-center mb-5">
						<h2 class="keep-print-font fw-500 mb-0 text-primary flex-1 position-relative">
							Sistema Coopex
							<small class="text-muted mb-0 fs-xs">
								Relatório de Receitas NAP
							</small>
							<!-- barcode demo only -->
						</h2>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6 ">
					<div class="table-responsive">
						<table class="table table-clean table-sm align-self-end">
							<tbody>
								
								<tr>
									<td colspan="2"><strong>GERADO EM:</strong><?php echo date("d/m/Y H:i:s")?></td>
								</tr>
								<tr>
									<td>
										<strong>POR:</strong> <?php echo $_SESSION['coopex']['usuario']['nome']?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<?php


					$sql = "SELECT
									evento,
									nosso_numero,
									data_pagamento,
									valor_pago,
									nome,
									id_inscricao  
								FROM
									coopex_usuario.evento_projeto
									INNER JOIN coopex_usuario.evento_inscricao USING ( id_evento )
									INNER JOIN coopex_usuario.evento_pessoa USING ( id_pessoa )
									INNER JOIN coopex_usuario.evento_inscricao_boleto_pagamento USING ( id_inscricao ) 
								WHERE
									evento_inscricao_boleto_pagamento.data_pagamento BETWEEN '$data_inicio' AND '$data_fim'";
					$aux_boleto = $coopex->prepare($sql);
					$aux_boleto->execute();
					$boleto_total = 0;
			?>
			<div class="mt-3">
				<h1>RECEITAS</h1>
			</div>
			
			<div class="row">
				<div class="col-sm-12">
					<div class="table-responsive">
						<table  class="table">
							<tbody>
								<thead>
									<tr>
										<th>ID</td>
										<th>Nome</td>
										<th>Boleto</td>
										<th>Pagamento</td>
										<th class="text-right">Valor</td>
									</tr>
								</thead>
							<?
								while($nap_boleto = $aux_boleto->fetch(PDO::FETCH_OBJ)){
								$boleto_total +=$nap_boleto->valor_pago; 
							?>
								<tr>
									<td><?php echo $nap_boleto->evento."-".$nap_boleto->id_inscricao?></td>
									<td><?php echo utf8_encode($nap_boleto->nome)?></td>
									<td><?php echo $nap_boleto->nosso_numero?></td>
									<td><?php echo converterData($nap_boleto->data_pagamento)?></td>
									<td align="right"><?php echo number_format($nap_boleto->valor_pago, 2, ',', '.')?></td>
								</tr>
							<?
							}
							?>
							</tbody>
						</table>
	
						<table class="table">
							<thead>
								<tr>
									<th class="border-top-0 table-scale-border-bottom fw-700">Forma de Pagamento</th>
									<th class="text-right border-top-0 table-scale-border-bottom fw-700">Valor</th>
								</tr>
								</thead>
							<tbody>
								<tr>
									<td class="text-left strong"><strong>Boleto</strong></td>
									<td class="text-right"><strong><?php echo isset($boleto_total) ? number_format($boleto_total, 2, ',', '.') : "0,00"?></strong></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>