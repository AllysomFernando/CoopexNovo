<?php

	$id_pessoa = ($_SESSION['coopex']['usuario']['id_pessoa']);

	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	$id_menu = 52;

	//$periodo = explode("/", $_SERVER['REQUEST_URI']);
	//$periodo = end($periodo);

	$sql = "SELECT
				*
			FROM
				medicina.grupo_pessoa a
			INNER JOIN medicina.grupo USING (id_grupo)
			INNER JOIN medicina.grupo_periodo USING (id_grupo_periodo)
			INNER JOIN medicina.periodo USING (id_periodo)
			WHERE
				a.id_pessoa = $id_pessoa";

	$res = $coopex->query($sql);
	$row_grupo_pessoa = $res->fetch(PDO::FETCH_OBJ);

	$coopex->query("SET lc_time_names = 'pt_BR';");


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
		<li class="breadcrumb-item"><a href="javascript:void(0);">Cronograma</a></li>
		<li class="breadcrumb-item active">Consulta</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title col-6">
			<i class='subheader-icon fal fa-calendar'></i> Cronograma do <strong class="fw-bold"><?=texto($row_grupo_pessoa->periodo)?></strong> período
			<small>
				Rodízio de Práticas
			</small>
		</h1>
		<?php
			if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][2])){
		?>
		<div class="subheader-title col-6 text-right">
			<h3 class="display-3 m-0"><strong style="font-weight: bold;"><?=texto($row_grupo_pessoa->periodo)?></strong> período</h3>

		</div> 
		<?php
			}
		?>
	</div>


	<div class="row">
		<div class="col-xl-12">
			<div id="panel-1" class="panel">
				<div class="panel-container show">
					<div class="panel-content">
						<!-- datatable start -->
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<!-- <th>id</th> -->
									<th>Especialidade</th>
									<th class="text-center">Data</th>
									<th class="text-center">Dia</th>
									<th class="text-center">Horário</th>
									<th>Local</th>
									<!-- <th class="text-center">Grupo</th> -->
									<!-- <th class="text-center">Alunos</th> -->
									<th class="text-center">Professor</th>
									<th class="text-center">Situação</th>
									<!-- <th class="text-center">Vagas</th> -->
									<th class="text-center">Ações</th>
								</tr>
							</thead>
							<tbody>
							<?php
		


								$sql_horario = "SELECT
										especialidade,
										DATE_FORMAT(data_disponivel, '%d/%b') AS data_disponivel2,
										DATE_FORMAT(data_disponivel, '%W') AS id_dia2,
										DATE_FORMAT(data_disponivel, '%u') AS semana,
										b.horario_inicio,
										b.horario_termino,
										`local`,
										situacao,
										id_docente,
										a.id_horario,
										id_horario_data,
										a.id_sub_grupo
									FROM
										medicina.sub_grupo_pessoa a
									INNER JOIN medicina.horario_data b USING (id_horario_data)
									INNER JOIN medicina.horario c ON a.id_horario = c.id_horario
									INNER JOIN medicina.especialidade e USING (id_especialidade)
									INNER JOIN medicina.`local` USING (id_local)
									INNER JOIN medicina.cronograma USING (id_horario_data)
									WHERE
										a.id_pessoa = $id_pessoa
									ORDER BY
										data_disponivel, id_dia, horario_inicio";

								$horario = $coopex->query($sql_horario);


								
								if($horario->rowCount()){
									while($row = $horario->fetch(PDO::FETCH_OBJ)){
							?>
									<tr style="background-color: <?=$row->cor?>">
										<!-- <td style="vertical-align: middle;" class="pointer"><?= texto($row->id_horario_data)?></td> -->
										<td style="vertical-align: middle;" class="pointer"><?= texto($row->especialidade)?></td>
										<td style="vertical-align: middle;" align="center"><?= ($row->data_disponivel2)?></td>
										<td style="vertical-align: middle;" align="center"><?= texto($row->id_dia2)?></td>
										<td style="vertical-align: middle;" align="center"><?= substr($row->horario_inicio, 0,5) ."<br>".substr($row->horario_termino, 0,5) ?></td>
										<td style="vertical-align: middle;"><?= texto($row->local)?></td>
										<!-- <td style="vertical-align: middle;" align="center"><?= ($grupo)?></td> -->
										<!-- <td style="vertical-align: middle;" align="center"><?= $row->alunos?></td> -->
										<td style="vertical-align: middle;"><?= texto(nome_sagres($row->id_docente))?></td>
										<td style="vertical-align: middle;" align="center">
											<span class="badge badge-<?= $row->situacao ? "warning" : "success"?> badge-pill"><?php echo $row->situacao ? "Remanejado" : "Confirmada"?></span>
										</td>
										<td style="vertical-align: middle;" align="center">
											<a href="javascript:cancelar(<?=$row->id_horario?>, <?=$row->id_horario_data?>, <?=$row->id_sub_grupo?>, <?=$id_pessoa?>)" class="btn btn-danger btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-times"></i></a>
										</td>
										


									</tr>
							<?php
									} 
								} else {
							?>
								<tr id="semana_<?=$semana?>" style="background-color: white;">
									<th colspan="20">SEM ATIVIDADES SELECIONADAS</th>
								</tr>
							<?		
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

	function cancelar(id_horario, id_horario_data, id_sub_grupo, id_pessoa){
		$.ajax({
			type: "POST",
			url: "modulos/medicina/cronograma/ajax/cancelar.php",
			data: { id_horario: id_horario, id_horario_data: id_horario_data, id_sub_grupo: id_sub_grupo, id_pessoa: id_pessoa },
			success: function(response) {
				document.location.reload(true);
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
