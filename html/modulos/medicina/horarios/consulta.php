<?php

	$id_menu = 48;

	

	$sql = "SELECT
				*
			FROM
				medicina.horario h
			INNER JOIN medicina.especialidade e USING (id_especialidade)
			INNER JOIN medicina.`local` USING (id_local)
			INNER JOIN medicina.periodo USING (id_periodo)
			WHERE
				h.excluido = 0";			

				$reoferta = $coopex->query($sql);

?>

<iframe class="d-none" name="dados" src=""></iframe>
<form id="form_excluir_registro" method="post" target="dados" action="modulos/medicina/horarios/cadastro_dados.php">
	<input type="hidden" id="excluir_registro" name="excluir_registro">
</form>	
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Período de Reofertas</a></li>
		<li class="breadcrumb-item active">Consulta</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title col-6">
			<i class='subheader-icon fal fa-repeat'></i> Projetos de Pós-Graduação
			<small>
				Gerenciamento de Projetos de Pós-Graduação
			</small>
		</h1>
		<?php
			//if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][2])){
		?>
		<div class="subheader-title col-6 text-right">
			<a href="medicina/horarios/cadastro">
				<button type="button" class="btn btn-lg btn-primary waves-effect waves-themed">
					<span class="ni ni-plus mr-3"></span>
					Cadastrar Horário
				</button>
			</a>
		</div>
		<?php
			//}
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
									<th>Período</th>
									<th>Especialidade</th>
									<th>Local</th>
									<th>Professor</th>
									<th class="text-center">Ações</th>
								</tr>
							</thead>
							<tbody>
								<?php
									while($row = $reoferta->fetch(PDO::FETCH_OBJ)){
								?>
								<tr>
									<td class="pointer"><?= texto($row->periodo)?></td>
									<td><?= texto ($row->especialidade)?></td>
									<td><?= texto($row->local)?></td>
									<td><?= texto(nome_sagres($row->id_docente))?></td>

									<td style="width: 70px">
										<?php
											if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][3])){
										?>
										<a href="medicina/horarios/cadastro/<?php echo $row->id_horario?>" class="btn btn-sm btn-icon btn-outline-success rounded-circle mr-2" title="Editar Registro">
											<i class="fal fa-pencil-alt"></i>
										</a>
										<?php
											}
										?>
										
										<?php
											if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][4])){
										?>
										<a href="javascript:excluir_registro('medicina.horario', 'id_horario', <?php echo $row->id_horario?>)" class="btn btn-sm btn-icon btn-outline-danger rounded-circle" title="Excluir Registro">
											<i class="fal fa-times"></i>
										</a>
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
						<!-- datatable end -->
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
					"aaSorting": []
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
    </body>
</html>
