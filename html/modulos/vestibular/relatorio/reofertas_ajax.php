<?php session_start();

	require_once("../../../php/mysql.php");
	require_once("../../../php/utils.php");


	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	if(isset($_GET['periodo'])){

		$aux = explode(" - ", $_GET['periodo']);
		if(count($aux)){
			$data_inicio = converterData($aux[0]);
			$data_fim = converterData($aux[1]);
		} else {
			$data_inicio = converterData($aux[0]);
			$data_fim = converterData($aux[0]);
		}
?>
	<div class="row">
		<div class="col-xl-12">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>Período selecionado: <span class="fw-300"><i><?php echo $_GET['periodo']?></i></span></h2>
					<div class="panel-toolbar">
						<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
						<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
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
									evento_inscricao_boleto_pagamento.data_pagamento BETWEEN '$data_inicio' AND '$data_fim'
								ORDER BY
									evento_inscricao_boleto_pagamento.data_pagamento";
					$aux_boleto = $coopex->prepare($sql);
					$aux_boleto->execute();
					$boleto_total = 0;
				?>
				<div class="panel-container show">
					<div class="panel-content">
						<!-- datatable start -->
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead class="bg-primary-600">
								<tr>
									<th>Tipo</th>
									<th>ID</th>
									<th>Nome</th>
									<th>Boleto</th>
									<th>Pagamento</th>
									<th>Valor</th>
								</tr>
							</thead>
							<tbody>
							<?
								while($nap_boleto = $aux_boleto->fetch(PDO::FETCH_OBJ)){
								$boleto_total +=$nap_boleto->valor_pago; 
								
								if($nap_boleto->evento == "EVTB"){
									$tipo = "Evento";
								} else if($nap_boleto->evento == "REMB"){
									$tipo = "Reoferta Matrícula";
								} else if($nap_boleto->evento == "REPB"){
									$tipo = "Reoferta Pré-matrícula";
								}
									
							?>
								<tr>
									<td><?php echo $tipo?></td>
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
							<tfoot>
								<tr>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th>Total</th>
									<th class="text-right"><strong><?php echo isset($boleto_total) ? number_format($boleto_total, 2, ',', '.') : "0,00"?></strong></th>
								</tr>
							</tfoot>

						</table>
						<!-- datatable end -->
					</div>
				</div>
			</div>
		</div>
	</div>
	<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
	<script src="js/datagrid/datatables/datatables.bundle.js"></script>
	<script src="js/datagrid/datatables/datatables.export.js"></script>
	<script>
	// initialize datatable
		$('#dt-basic-example').dataTable({
			responsive: true,
			lengthChange: true,
			"pageLength": 50,
			dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
			buttons: [
				{
					extend: 'pdfHtml5',
					text: 'PDF',
					titleAttr: 'Gerar PDF',
					className: 'btn-outline-danger btn-sm mr-1 ml-3',
					title: 'BOLETOS PAGOS BRADESCO',
					messageTop: 'Período: <?php echo $_GET['periodo']?>',
					filename: 'asdfasdf'
				},
				{
					extend: 'excelHtml5',
					text: 'Excel',
					titleAttr: 'Gerar arquivo Excel',
					className: 'btn-outline-success btn-sm mr-1'
				},
				{
					extend: 'print',
					text: 'Imprimir',
					titleAttr: 'Imprimir Tabela',
					className: 'btn-outline-primary btn-sm',
					title: 'BOLETOS PAGOS BRADESCO',
					messageTop: 'Período: <?php echo $_GET['periodo']?>',
				}
			]
		});
	</script>
	<?
		}
	?>