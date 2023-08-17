<?php

	$id_menu = 41;

	$periodo = explode("/", $_SERVER['REQUEST_URI']);
	$periodo = end($periodo);

	$coopex->query("SET lc_time_names = 'pt_BR';");

	$sql = "SELECT
				especialidade,
				DATE_FORMAT( data_disponivel, '%d/%b' ) AS data_disponivel2 ,
				DATE_FORMAT( data_disponivel, '%W' ) AS id_dia2 ,
				DATE_FORMAT( data_disponivel, '%u' ) AS semana ,
				f.id_dia,
				b.horario_inicio,
				b.horario_termino,
				`local`,
				nome,
				c.alunos,
				id_grupo_aluno,
				grupo_aluno,
				cor,
				count(*) AS agrupamento,
				id_horario_data,
				id_sub_grupo,
				id_docente,
				qtd_alunos,
				id_cronograma,
				d.id_horario,
				situacao
			FROM
				medicina.cronograma a
			INNER JOIN medicina.horario_data b USING (id_horario_data)
			INNER JOIN medicina.sub_grupo c USING (id_sub_grupo)
			INNER JOIN medicina.horario d ON d.id_horario = b.id_horario
			INNER JOIN medicina.especialidade e USING (id_especialidade)
			INNER JOIN medicina.`local` USING (id_local)
			INNER JOIN medicina.horario_dia f ON f.id_horario = b.id_horario
			INNER JOIN medicina.grupo_aluno USING (id_grupo_aluno)
			WHERE id_periodo = $periodo
			GROUP BY
				id_horario_data
			ORDER BY
				data_disponivel, id_dia, b.horario_inicio";

	$reoferta = $coopex->query($sql);

	$sql = "SELECT
				especialidade,
				DATE_FORMAT( data_disponivel, '%d/%b' ) AS data_disponivel2 ,
				DATE_FORMAT( data_disponivel, '%W' ) AS id_dia2 ,
				DATE_FORMAT( data_disponivel, '%u' ) AS semana ,
				f.id_dia,
				b.horario_inicio,
				b.horario_termino,
				`local`,
				nome,
				c.alunos,
				id_grupo_aluno,
				grupo_aluno,
				situacao
			FROM
				medicina.cronograma a
			INNER JOIN medicina.horario_data b USING (id_horario_data)
			INNER JOIN medicina.sub_grupo c USING (id_sub_grupo)
			INNER JOIN medicina.horario d ON d.id_horario = b.id_horario
			INNER JOIN medicina.especialidade e USING (id_especialidade)
			INNER JOIN medicina.`local` USING (id_local)
			INNER JOIN medicina.horario_dia f ON f.id_horario = b.id_horario
			INNER JOIN medicina.grupo_aluno USING (id_grupo_aluno)
			WHERE id_periodo = $periodo
			GROUP BY
				semana
			ORDER BY
				data_disponivel, id_dia, horario_inicio";

	$res_semana = $coopex->query($sql);


	$sql = "SELECT DATE_FORMAT(now(), '%u') AS atual";
	$res_atual = $coopex->query($sql);
	$row_atual = $res_atual->fetch(PDO::FETCH_OBJ);
	$semana_atual = $row_atual->atual;


	$sql = "SELECT
				*
			FROM
				medicina.periodo
			WHERE
				id_periodo = $periodo";

	$res_per = $coopex->query($sql);
	$row_per = $res_per->fetch(PDO::FETCH_OBJ);
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
			<i class='subheader-icon fal fa-calendar'></i> Cronograma do <strong class="fw-bold"><?=texto($row_per->periodo)?></strong> período
			<small>
				Rodízio de Práticas
			</small>
		</h1>
		<?php
			if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][2])){
		?>
		<div class="subheader-title col-6 text-right">
			<h3 class="display-3 m-0"><strong style="font-weight: bold;"><?=texto($row_per->periodo)?></strong> período</h3>

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
						<div class="subheader">
							<h1 class="subheader-title col-6">
								<i class='subheader-icon fal fa-calendar'></i> Semanas de Aula
							</h1>

						</div>
						<?
							$i = 1;
							while($row_semana = $res_semana->fetch(PDO::FETCH_OBJ)){
								$cor = $semana_atual == $row_semana->semana ? "primary" : "secondary";
						?>		
								
							<a href="<?=$_SERVER['REQUEST_URI']?>#semana_<?=$i?>" type='button' class='btn btn-small btn-<?=$cor?> waves-effect waves-themed mr-2 mb-2'><?=$i?></a>
								
						<?		
								$i++;
							}
						?>
					</div>
				</div>
			</div>
		</div>			
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
									<th>id</th>
									<th>Especialidade</th>
									<th class="text-center">Data</th>
									<th class="text-center">Dia Semana</th>
									<th class="text-center">Horário</th>
									<th>Local</th>
									<th class="text-center">Grupo</th>
									<th class="text-center">Alunos</th>
									<th class="text-center">Professor</th>
									<th class="text-center">Situação</th>
									<th class="text-center">Ações</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$semana_anterior = 0;
								$semana = 1;
								while($row = $reoferta->fetch(PDO::FETCH_OBJ)){
									$semana_atual = $row->semana;
									if($semana_anterior != $semana_atual ){
										$semana_anterior = $semana_atual
							?>
									<tr id="semana_<?=$semana?>" style="background-color: black; color: white;">
										<th>id</th>
										<th><b><?=$semana?>º SEMANA DE AULA</b></th>
										<th class="text-center">Data</th>
										<th class="text-center">Dia Semana</th>
										<th class="text-center">Horário</th>
										<th>Local</th>
										<th class="text-center">Grupo</th>
										<th class="text-center">Alunos</th>
										<th class="text-center">Professor</th>
										<th class="text-center">Situação</th>
										<th class="text-center">Remanejar</th>
									</tr>
							<?	
										$semana++;
									}
							
									if($row->agrupamento > 1){

										$sql = "SELECT
													nome
												FROM
													medicina.cronograma
												INNER JOIN medicina.sub_grupo USING (id_sub_grupo)
												WHERE
													id_horario_data = $row->id_horario_data
												ORDER BY
													nome";
										$res_agrupamento = $coopex->query($sql);			

										unset($arr);
										while($row_agrupamento = $res_agrupamento->fetch(PDO::FETCH_OBJ)){

											$aux = explode("/", $row_agrupamento->nome);

											$arr[] = $aux[0];

										}

										if($row->id_grupo_aluno == 1){
											$grupo = $row->grupo_aluno;
										} else {
											$qtd_alunos = $row->alunos * count($arr);
											$grupo = implode(" e ", $arr);
										}

									} else {

										if($row->id_grupo_aluno == 1){
											$grupo = $row->grupo_aluno;
										} else {
											$aux = explode("/", $row->nome);
											$grupo = $aux[0];
										}

										$qtd_alunos = $row->alunos;
									}


							?>
									<tr style="background-color: <?=$row->cor?>">
										<td style="vertical-align: middle;" class="pointer"><?= texto($row->id_horario)?></td>
										<td style="vertical-align: middle;" class="pointer"><?= texto($row->especialidade)?></td>
										<td style="vertical-align: middle;" align="center"><?= ($row->data_disponivel2)?></td>
										<td style="vertical-align: middle;" align="center"><?= texto($row->id_dia2)?></td>
										<td style="vertical-align: middle;" align="center"><?= substr($row->horario_inicio, 0,5) ."<br>".substr($row->horario_termino, 0,5) ?></td>
										<td style="vertical-align: middle;"><?= texto($row->local)?></td>
										<td style="vertical-align: middle;" align="center"><?= ($grupo)?></td>
										<td style="vertical-align: middle;" align="center"><?= texto($qtd_alunos)?></td>
										<td style="vertical-align: middle;"><?= texto(nome_sagres($row->id_docente))?></td>
										<td style="vertical-align: middle;" align="center">
											<span class="badge badge-<?= $row->situacao ? "warning" : "secondary"?> badge-pill"><?php echo $row->situacao ? "Remanejado" : "-"?></span>
										</td>
										<td align="center" style="vertical-align: middle;">
											<?php
												if(!$row->situacao){
											?>
											<a href="medicina/cronograma/remanejar/<?= $row->id_horario?>/<?php echo $row->id_cronograma?>" class="btn  btn-icon" title="Remanejar Horário">
												<i class="fal fa-calendar-edit fa-2x mt-2"></i>
											</a>
											<?php
												}
											?>
											
											<?php
												if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][4])){
											?>
											<!-- <a href="javascript:excluir_registro('pos.projeto', 'id_projeto', <?php echo $row->id_projeto?>)" class="btn btn-sm btn-icon btn-outline-danger rounded-circle" title="Excluir Registro">
												<i class="fal fa-times"></i>
											</a> -->
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
<script src="js/datagrid/datatables/datatables.export.js"></script>
<script>
	$(document).ready(function(){
		$('#dt-basic-example').dataTable({
			responsive: true,
			"aaSorting": [],
			"pageLength": 99999999,
			dom:
                        /*	--- Layout Structure 
                        	--- Options
                        	l	-	length changing input control
                        	f	-	filtering input
                        	t	-	The table!
                        	i	-	Table information summary
                        	p	-	pagination control
                        	r	-	processing display element
                        	B	-	buttons
                        	R	-	ColReorder
                        	S	-	Select

                        	--- Markup
                        	< and >				- div element
                        	<"class" and >		- div with a class
                        	<"#id" and >		- div with an ID
                        	<"#id.class" and >	- div with an ID and a class

                        	--- Further reading
                        	https://datatables.net/reference/option/dom
                        	--------------------------------------
                         */
                        "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    buttons: [
                        /*{
                        	extend:    'colvis',
                        	text:      'Column Visibility',
                        	titleAttr: 'Col visibility',
                        	className: 'mr-sm-3'
                        },*/
                        {
                            extend: 'pdfHtml5',
                            text: 'PDF',
                            titleAttr: 'Generate PDF',
                            className: 'btn-outline-danger btn-sm mr-1'
                        },
                        {
                            extend: 'excelHtml5',
                            text: 'Excel',
                            titleAttr: 'Generate Excel',
                            className: 'btn-outline-success btn-sm mr-1'
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'CSV',
                            titleAttr: 'Generate CSV',
                            className: 'btn-outline-primary btn-sm mr-1'
                        },
                        {
                            extend: 'copyHtml5',
                            text: 'Copiar',
                            titleAttr: 'Copy to clipboard',
                            className: 'btn-outline-primary btn-sm mr-1'
                        },
                        {
                            extend: 'print',
                            text: 'Imprimir',
                            titleAttr: 'Print Table',
                            className: 'btn-outline-primary btn-sm'
                        }
                    ]
		});
	});

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
