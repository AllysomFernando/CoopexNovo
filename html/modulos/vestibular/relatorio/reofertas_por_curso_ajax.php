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
								departamento as titulo,
								sum( valor ) as valor_pago,
								count( * ) AS pagamentos  
							FROM
								coopex.departamento
								INNER JOIN coopex_reoferta.reoferta USING ( id_departamento )
								INNER JOIN coopex_reoferta.matricula USING ( id_reoferta ) 
							WHERE
								data_pagamento BETWEEN '$data_inicio' 
								AND '$data_fim' 
								AND pagamento_tesouraria = 0 
								AND pago = 1 
							GROUP BY
								id_departamento 
							ORDER BY
								departamento";
					$aux_boleto = $coopex->prepare($sql);
					$aux_boleto->execute();
					$boleto_total = 0;
					$total_nap = 0;
					$total_evento = 0;
					$total_reoferta = 0;
				?>
				<div class="panel-container show">
					<div class="panel-content">
						<!-- datatable start -->
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead class="bg-primary-600">
								<tr>
									<th>Descrição</th>
									<!-- <th>Data</th> -->
									<th>Pagamentos</th>
									<th>Valor</th>
								</tr>
							</thead>
							<tbody>
							<?php

								while($row = $aux_boleto->fetch(PDO::FETCH_OBJ)){
									$boleto_total += $row->valor_pago; 
								
								/*if($row->evento == "EVTB"){
									$tipo = "Evento";
								} else if($row->evento == "REMB"){
									$tipo = "Reoferta Matrícula";
								} else if($row->evento == "REPB"){
									$tipo = "Reoferta Pré-matrícula";
								}*/

							?>
								<tr>
									<td><?php echo utf8_encode($row->titulo)?></td>
									<!-- <td><?php echo isset($row->data_pagamento) ? converterData($row->data_pagamento) : ""?></td> -->
									<td><?php echo $row->pagamentos?></td>
									<td align="right"><?php echo number_format($row->valor_pago, 2, ',', '.')?></td>
								</tr>
							<?php
									
									}
							?>
								
								
							</tbody>
							<tfoot>
								<tr>
									<th></th>
									<th class="bg-primary-600">TOTAL</th>
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
	<?php
		}
	?>