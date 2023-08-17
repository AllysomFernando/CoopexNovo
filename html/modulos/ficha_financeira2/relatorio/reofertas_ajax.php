<?php 
	if(!strstr($_SERVER['HTTP_REFERER'], "https://coopex.fag.edu.br/")){
		exit;
	}
		
	require_once("../../../php/config.php");
	require_once("../../../php/mysql.php");
	require_once("../../../php/utils.php");


	

	if(isset($_GET['id_curso'])){

		extract($_GET);

		$sql = "SELECT
					departamento 
				FROM
					coopex.departamento 
				WHERE
					id_departamento = ".$_GET['id_curso'];
		$res = $coopex->query($sql);
		$curso = $res->fetch(PDO::FETCH_OBJ);

		$sql = "SELECT
					periodo 
				FROM
					coopex_reoferta.periodo 
				WHERE
					id_periodo = ".$_GET['id_periodo'];
		$res = $coopex->query($sql);
		$periodo = $res->fetch(PDO::FETCH_OBJ);

?>
	<div class="row">
		<div class="col-xl-12">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>Curso: <span class="fw-300"><i><?php echo utf8_encode($curso->departamento)?></i></span></h2>
					<div class="panel-toolbar">
						<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
						<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
					</div>
				</div>
				<?php
					$sql = "SELECT
								* 
							FROM
								coopex_reoferta.reoferta a
								INNER JOIN coopex.pessoa b ON a.id_docente = b.id_pessoa
								INNER JOIN coopex_reoferta.cronograma c USING ( id_reoferta ) 
							WHERE
								id_departamento = $id_curso 
								AND id_periodo = $id_periodo
								AND id_parecer = 2 
							GROUP BY
								id_reoferta 
							ORDER BY
								c.data_reoferta ASC";
					$reoferta = $coopex->query($sql);
				?>
				<div class="panel-container show">
					<div class="panel-content">
						<!-- datatable start -->
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead class="bg-primary-600">
								<tr>
									<th>Disciplina</th>
									<th>Professor</th>
									
									<th>Local</th>
									<th>Início</th>
								</tr>
							</thead>
							<tbody>
							<?php
								while($row = $reoferta->fetch(PDO::FETCH_OBJ)){
							?>
								<tr>
									<td><?php echo utf8_encode($row->disciplina)?></td>
									<td><?php echo utf8_encode($row->nome)?></td>
									
									<td><?php echo $row->local?></td>
									<td><?php echo converterData($row->data_reoferta)?></td>
								</tr>
							<?php
							}
							?>
								
								
							</tbody>


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
					title: 'RELATÓRIO DE REOFERTAS POR PERÍODO',
					messageTop: 'Curso: <?php echo $curso->departamento?>\nPeríodo: <?php echo $periodo->periodo?>',
					filename: '<?php echo uniqid(time())?>'
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
					title: 'RELATÓRIO DE REOFERTAS POR PERÍODO',
					messageTop: 'Curso: <?php echo $curso->departamento?><br>Período: <?php echo $periodo->periodo?>',
				}
			]
		});
	</script>
	<?php
		}
	?>