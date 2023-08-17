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
			WHERE id_grupo = $row_grupo_pessoa->id_grupo
			GROUP BY
				id_horario_data
			ORDER BY
				data_disponivel, id_dia, horario_inicio";

	$reoferta = $coopex->query($sql);

	$sql_semana = "SELECT
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
			GROUP BY
				semana
			ORDER BY
				data_disponivel, id_dia, horario_inicio";

	$res_semana = $coopex->query($sql_semana);


	$sql = "SELECT DATE_FORMAT(now(), '%u') AS atual";
	$res_atual = $coopex->query($sql);
	$row_atual = $res_atual->fetch(PDO::FETCH_OBJ);
	$semana_atual = $row_atual->atual;

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
									<th class="text-center">Vagas</th>
									<th class="text-center">Ações</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$semana = 1;

								$res_semana = $coopex->query($sql_semana);
								while($row_semana = $res_semana->fetch(PDO::FETCH_OBJ)){


									$sql_horario = "SELECT
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
											WHERE id_grupo = $row_grupo_pessoa->id_grupo
											AND DATE_FORMAT(data_disponivel, '%u') = $row_semana->semana
											GROUP BY
												id_horario_data
											ORDER BY
												data_disponivel, id_dia, horario_inicio";

									$horario = $coopex->query($sql_horario);

							?>
									<tr id="semana_<?=$semana?>" style="background-color: black; color: white;">
										<!-- <th>id</th> -->
										<th><b><?=$semana?>º SEMANA</b></th>
										<th class="text-center">Data</th>
										<th class="text-center">Dia</th>
										<th class="text-center">Horário</th>
										<th>Local</th>
										<!-- <th class="text-center">Grupo</th> -->
										<!-- <th class="text-center">Alunos</th> -->
										<th class="text-center">Professor</th>
										<th class="text-center">Situação</th>
										<th class="text-center">Vagas</th>
										<th class="text-center">Confirmar</th>
									</tr>

							<?

								
								if($horario->rowCount()){
									while($row = $horario->fetch(PDO::FETCH_OBJ)){
								
					
									
							
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
											$arr[] = $row_agrupamento->nome;

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
											$grupo = $row->nome;
											$aux = explode("/", $row->nome);
											$grupo = $aux[0];
										}

										$qtd_alunos = $row->alunos;
									}
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
										
										<td align="center" style="vertical-align: middle;">
											<?
												if($row->id_grupo_aluno == 1){
											?>
											<?		
												} else {

													//echo $sql_horario;
													$sql = "SELECT
																*
															FROM
																medicina.sub_grupo_pessoa
																INNER JOIN pessoa USING (id_pessoa)
															WHERE
																id_horario = $row->id_horario
															AND id_sub_grupo = $row->id_sub_grupo
															AND id_horario_data = $row->id_horario_data";
													$res_c = $coopex->query($sql);
													$vagas = $row->alunos - $res_c->rowCount();
													$nomes = [];
													while($row_c = $res_c->fetch(PDO::FETCH_OBJ)){
														$nomes[] = $row_c->nome;
													}
													$nomes = implode("\n", $nomes);
											?>
                                                <button type="button" class="btn btn-primary waves-effect waves-themed" data-toggle="popover" data-trigger="focus" data-placement="top" title="" data-content="<?=$nomes?>" data-template="<div class=&quot;popover bg-primary-500 border-primary&quot; role=&quot;tooltip&quot;><div class=&quot;arrow&quot;></div><h3 class=&quot;popover-header bg-transparent&quot;></h3><div class=&quot;popover-body text-white&quot;></div></div>"><?=$vagas?></button>
											<?		
												}
											?>

										</td>

										<td align="center" style="vertical-align: middle;">
											<?
												if($row->id_grupo_aluno == 1){
											?>
												<div class="custom-control custom-switch">
													<input type="radio" class="custom-control-input" id="<?=$row->id_sub_grupo?>" checked disabled  name="<?=rand()?>">
													<label class="custom-control-label" for="<?=$row->id_sub_grupo?>"></label>
												</div>
											<?		
												} else {
													if($vagas){
														$sql = "SELECT
																	*
																FROM
																	medicina.sub_grupo_pessoa
																WHERE
																	id_horario = $row->id_horario
																AND id_sub_grupo = $row->id_sub_grupo
																AND id_horario_data = $row->id_horario_data
																AND id_pessoa = $id_pessoa";
														$row_c = $coopex->query($sql);
														//echo $row_c->rowCount();
											?>
				
														<div class="custom-control custom-switch">
															<input <?=$row_c->rowCount() ? "checked" : ""?>
																onclick="reservar(<?=$row->id_horario?>, <?=$row->id_horario_data?>, <?=$row->id_sub_grupo?>, <?=$id_pessoa?>)"
																type="radio" class="custom-control-input" 
																id="h_<?=$row->id_cronograma?>"
																name="<?=$row->id_horario?>">
															<label class="custom-control-label" for="h_<?=$row->id_cronograma?>"></label>
														</div>
											<?		
													}
												}
											?>
										</td>
									</tr>
							<?php
									} 
								} else {
							?>
								<tr id="semana_<?=$semana?>" style="background-color: white;">
									<th>SEM ATIVIDADES</th>
									<th>PARA</th>
									<th>O GRUPO</th>
									<th>NESTA</th>
									<th>SEMANA SEMANA</th>
									<th></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							<?		
								}
								$semana++;
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

	function reservar(id_horario, id_horario_data, id_sub_grupo, id_pessoa){
		$.ajax({
			type: "POST",
			url: "modulos/medicina/cronograma/ajax/reservar.php",
			data: { id_horario: id_horario, id_horario_data: id_horario_data, id_sub_grupo: id_sub_grupo, id_pessoa: id_pessoa },
			success: function(response) {
				/*window.history.back();*/
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
