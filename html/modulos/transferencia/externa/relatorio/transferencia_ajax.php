<?php
if (!strstr($_SERVER['HTTP_REFERER'], "https://coopex.fag.edu.br/")) {
	exit;
}

require_once("../../../../php/config.php");
require_once("../../../../php/mysql.php");
require_once("../../../../php/utils.php");


if (isset($_GET['tipo'])) {

	extract($_GET);
	$sql = "SELECT
					* 
				FROM
					transferencia.transferencia_externa
				INNER JOIN coopex.departamento using (id_departamento)
				INNER JOIN transferencia.matriculado using (id_matriculado)
				INNER JOIN transferencia.egresso using (id_egresso)
				INNER JOIN transferencia.ingresso using (id_ingresso)";
	$res = $coopex->query($sql);
	$geral = $res->fetch(PDO::FETCH_OBJ);
?>
	<div class="row">
		<div class="col-xl-12">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<div class="panel-toolbar">
						<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
						<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
					</div>
				</div>
				<?php
				$tipo = $_GET['tipo'];
				if ($tipo == 1) {
					$sql = "SELECT
					* 
				FROM
					transferencia.transferencia_externa a
					INNER JOIN coopex.departamento using (id_departamento)
					INNER JOIN transferencia.matriculado using (id_matriculado)
					INNER JOIN transferencia.egresso using (id_egresso)
					INNER JOIN transferencia.ingresso using (id_ingresso)
				GROUP BY
					academico";

					$reoferta = $coopex->query($sql);
				?>
					<div class="panel-container show">
						<div class="panel-content">
							<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
								<thead class="bg-primary-600">
									<tr>
										<th>Registro</th>
									
										<th>Curso</th>
										<th>Acadêmico</th>
										<th>Instituição de Origem</th>

										<th>Matriculado no Curso FAG/DB/IAG</th>
										<th>Aluno</th>

										<th>Ingressante através de</th>
									</tr>
								</thead>
								<tbody>
									<?php
									while ($row = $reoferta->fetch(PDO::FETCH_OBJ)) {
									?>
										<tr>
											<td><?php echo utf8_encode($row->id_transferencia_externa) ?></td>
											<td><?php echo utf8_encode($row->departamento) ?></td>
											<td><?php echo utf8_encode($row->academico) ?></td>

											
											<td><?php echo utf8_encode($row->instituicao_origem) ?></td>
											<td><?php echo utf8_encode($row->tipo_matriculado) ?></td>
											<td><?php echo utf8_encode($row->tipo_egresso) ?></td>
											<td><?php echo utf8_encode($row->tipo_ingresso) ?></td>
										</tr>
									<?php
									}
									?>
								</tbody>
							</table>
						<?php
					} else if ($tipo == 2) {
						$sql1 = "SELECT count(*) AS total, departamento, tipo_matriculado, tipo_ingresso, tipo_egresso, instituicao_origem
						FROM transferencia.transferencia_externa
						INNER JOIN coopex.departamento using (id_departamento)				
						INNER JOIN transferencia.matriculado using (id_matriculado)
						INNER JOIN transferencia.egresso using (id_egresso)
						INNER JOIN transferencia.ingresso using (id_ingresso)
						GROUP BY departamento";
						$all = $coopex->query($sql1);
						?>
							<div class="panel-container show">
								<div class="panel-content">
									<!-- datatable start -->
									<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
										<thead class="bg-primary-600">
											<tr>
												<th>Curso</th>
												<th>Instituição de Origem</th>

												<th>Matriculado no Curso FAG/DB/IAG </th>
												<th>É aluno </th>

												<th>Ingressou na FAG através de</th>
												<th>Quantidade</th>
											</tr>
										</thead>
										<tbody>
											<?php
											while ($row1 = $all->fetch(PDO::FETCH_OBJ)) {
											?>
												<tr>

													<td><?php echo utf8_encode($row1->departamento) ?></td>
													<td><?php echo utf8_encode($row1->instituicao_origem) ?></td>
													<td><?php echo utf8_encode($row1->tipo_matriculado) ?></td>
													<td><?php echo utf8_encode($row1->tipo_egresso) ?></td>
													<td><?php echo utf8_encode($row1->tipo_ingresso) ?></td>
													<td><?php echo utf8_encode($row1->total) ?></td>
												</tr>
											<?php
											}
											?>
										</tbody>
									</table>
								<?php
							} else if ($tipo == 3) {
								$sql2 = "SELECT instituicao_origem, departamento
						FROM transferencia.transferencia_externa
						INNER JOIN coopex.departamento using (id_departamento)	
						GROUP BY instituicao_origem
						ORDER BY instituicao_origem";
								$all2 = $coopex->query($sql2);
								?>
									<div class="panel-container show">
										<div class="panel-content">
											<!-- datatable start -->
											<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
												<thead class="bg-primary-600">
													<tr>
														<th>Instituição de Origem</th>
														<th>Para o curso da FAG</th>
													</tr>
												</thead>
												<tbody>
													<?php
													while ($row2 = $all2->fetch(PDO::FETCH_OBJ)) {
													?>
														<tr>
															<td><?php echo utf8_encode($row2->instituicao_origem) ?></td>
															<td><?php echo utf8_encode($row2->departamento) ?></td>
														</tr>
													<?php
													}
													?>
												</tbody>
											</table>
										<?php
									}
										?>
										<?php

										if (isset($_GET['tipo'])) {
											extract($_GET);
											$id_periodo = $_GET['id_periodo'];
											if (preg_match('/^\d{4}-[1-2]$/', $id_periodo)) {

												list($ano, $semestre) = explode('-', $id_periodo);

												$sql = "SELECT COUNT(*) AS total, departamento, tipo_matriculado, tipo_ingresso, tipo_egresso, instituicao_origem
									FROM transferencia.transferencia_externa
									INNER JOIN coopex.departamento USING (id_departamento)
									INNER JOIN transferencia.matriculado USING (id_matriculado)
									INNER JOIN transferencia.egresso USING (id_egresso)
									INNER JOIN transferencia.ingresso USING (id_ingresso)
									WHERE YEAR(data_cadastro) = :ano AND IF(MONTH(data_cadastro) < 7, 1, 2) = :semestre
									GROUP BY departamento";
												$consulta = $coopex->prepare($sql);

												$consulta->bindValue(':ano', $ano, PDO::PARAM_INT);
												$consulta->bindValue(':semestre', $semestre, PDO::PARAM_INT);

												$consulta->execute();

												while ($row = $consulta->fetch(PDO::FETCH_OBJ)) {
										?>
													<div class="panel-container show">
														<div class="panel-content">
															<!-- datatable start -->
															<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
																<thead class="bg-primary-600">
																	<tr>
																		<th>Curso</th>
																		<th>Instituição de Origem</th>
																		<th>Matriculado no Curso FAG/DB/IAG</th>
																		<th>É aluno</th>
																		<th>Ingressou na FAG através de</th>
																		<th>Quantidade</th>
																	</tr>
																</thead>
																<tbody>
																	<?php
																	while ($row1 = $consulta->fetch(PDO::FETCH_OBJ)) { // Use $consulta aqui em vez de $all
																	?>
																		<tr>
																			<td><?php echo utf8_encode($row1->departamento) ?></td>
																			<td><?php echo utf8_encode($row1->instituicao_origem) ?></td>
																			<td><?php echo utf8_encode($row1->tipo_matriculado) ?></td>
																			<td><?php echo utf8_encode($row1->tipo_egresso) ?></td>
																			<td><?php echo utf8_encode($row1->tipo_ingresso) ?></td>
																			<td><?php echo utf8_encode($row1->total) ?></td>
																		</tr>
																	<?php
																	}
																	?>
																</tbody>
															</table>
												<?php
												}
											} else {
												// O formato do parâmetro id_periodo não é válido
												// Faça o tratamento apropriado aqui
											}
										}
												?>
														</div>



													</div>
										</div>
									</div>
								</div>
							</div>
							<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
							<script src="js/datagrid/datatables/datatables.bundle.js"></script>
							<script src="js/datagrid/datatables/datatables.export.js"></script>
							<script>
								
							
								$(document).ready(function() {

									$('.select2').select2();

									$('#dt-basic-example').dataTable({
										responsive: true,
										pageLength: 15,
										order: [
											[1, 'asc']
										],
										rowGroup: {
											dataSrc: 1
										},
										
									});
								});
							</script>
						<?php
					}
						?>