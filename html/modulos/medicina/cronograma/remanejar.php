<?php

	$id_menu = 41;

	$id_horario 	= $_GET['id'];
	$id_cronograma 	= $_GET['p1'];

	$coopex->query("SET lc_time_names = 'pt_BR';");

	$sql = "SELECT
				especialidade,
				DATE_FORMAT(data_disponivel, '%d/%b') AS data_disponivel2,
				DATE_FORMAT(data_disponivel, '%W') AS id_dia2,
				DATE_FORMAT(data_disponivel, '%u') AS semana,
				e.id_dia,
				horario_inicio,
				horario_termino,
				`local`,
				id_grupo_aluno,
				cor,
				count(*) AS agrupamento,
				id_horario_data,
				id_docente,
				qtd_alunos
			FROM
				medicina.horario_data a
			INNER JOIN medicina.horario b USING (id_horario)
			INNER JOIN medicina.especialidade c USING (id_especialidade)
			INNER JOIN medicina.`local` d USING (id_local)
			INNER JOIN medicina.horario_dia e ON e.id_horario = b.id_horario
			WHERE
				b.id_horario = $id_horario
			AND id_horario_data NOT IN (
				SELECT
					id_horario_data
				FROM
					medicina.cronograma
			)
			GROUP BY
				id_horario_data";

	$cronograma = $coopex->query($sql);

?>

<iframe class="d-none" name="dados" src=""></iframe>
<form id="form_excluir_registro" method="post" target="dados" action="modulos/pos/projeto/cadastro_dados.php">
	<input type="hidden" id="excluir_registro" name="excluir_registro">
</form>	
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Remanejamento</a></li>
		<li class="breadcrumb-item active">Consulta</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title col-6">
			<i class='subheader-icon fal fa-calendar'></i> Remanejamento
			<small>
				Rodízio de Práticas
			</small>
		</h1>

	</div>

	<div class="row">
		<div class="col-xl-12">
			<div id="panel-1" class="panel">
				<div class="panel-container show">
					<div class="panel-content">
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th>id</th>
									<th>Especialidade</th>
									<th class="text-center">Data</th>
									<th class="text-center">Dia Semana</th>
									<th class="text-center">Horário</th>
									<th>Local</th>
									<th class="text-center">Professor</th>
									<th class="text-center">Situação</th>
									<th class="text-center">Ações</th>
								</tr>
							</thead>
							<tbody>
								<?php
									while($row = $cronograma->fetch(PDO::FETCH_OBJ)){
								?>

								<tr style="background-color: <?=$row->cor?>">
									<td style="vertical-align: middle;" class="pointer"><?= texto($row->id_horario_data)?></td>
									<td style="vertical-align: middle;" class="pointer"><?= texto($row->especialidade)?></td>
									<td style="vertical-align: middle;" align="center"><?= ($row->data_disponivel2)?></td>
									<td style="vertical-align: middle;" align="center"><?= texto($row->id_dia2)?></td>
									<td style="vertical-align: middle;" align="center"><?= substr($row->horario_inicio, 0,5) ."<br>".substr($row->horario_termino, 0,5) ?></td>
									<td style="vertical-align: middle;"><?= texto($row->local)?></td>
									<td style="vertical-align: middle;"><?= texto(nome_sagres($row->id_docente))?></td>
									<td style="vertical-align: middle;" align="center">
										<!-- <span class="badge badge-<?php echo $row->parecer_pos ? "success" : "warning"?> badge-pill"><?php echo $row->parecer_pos ? "Ativo" : "Inativo"?></span> -->
									</td>
									<td align="center" style="vertical-align: middle;">
										<?php
											if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][3])){
										?>
										<button onclick="remanejar(<?= $row->id_horario_data?>, <?=$id_cronograma?>)" href="medicina/cronograma/remanejar/<?php echo $row->id_cronograma?>" class="btn  btn-icon" title="Alocar nesta data">
											<i class="fal fa-calendar-plus fa-2x mt-2"></i>
										</button>
										<?php
											}
										?>
									</td>
								</tr>
								<?php
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script>
	$(document).ready(function(){
		$('#dt-basic-example').dataTable({
			responsive: true,
			"aaSorting": [],
			"pageLength": 99999999
		});
	});

	function remanejar(id_horario_data, id_cronograma){
		$.ajax({
			type: "POST",
			url: "modulos/medicina/cronograma/ajax/remanejar.php",
			data: { id_horario_data: id_horario_data, id_cronograma: id_cronograma },
			success: function(response) {
				window.history.back();
			}
		});
	}

	function exclusaoOK(){
		Swal.fire({
			type: "success",
			title: "Registro excluido com sucesso!",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				document.location.reload(true)
			}
		});
	}

	function exclusaoFalha(){
		Swal.fire({
			type: "error",
			title: "Falha ao excluir registro",
			showConfirmButton: true
		});
	}
</script>
