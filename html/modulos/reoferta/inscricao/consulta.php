<?php

	$id_menu = 22;


	$sql = "SELECT
		id_periodo 
	FROM
		coopex_reoferta.periodo 
	WHERE
		ativo = 1 order by id_periodo desc";

	$periodo = $coopex->query($sql);
	$periodo = $periodo->fetch(PDO::FETCH_OBJ);

	$periodo = $periodo->id_periodo;

	$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];

	$sql = "SELECT
				id_reoferta,
				disciplina,
				departamento,
				periodo,
				id_pre_matricula,
				id_matricula 
			FROM
				coopex_reoferta.reoferta a
				INNER JOIN coopex_reoferta.periodo USING ( id_periodo )
				INNER JOIN coopex.departamento USING ( id_departamento )
				INNER JOIN coopex_reoferta.carga_horaria USING ( id_carga_horaria )
				LEFT JOIN coopex_reoferta.pre_matricula USING ( id_reoferta )
				LEFT JOIN coopex_reoferta.matricula USING ( id_reoferta ) 
			WHERE
				id_parecer = 2 
				and id_periodo = $periodo
				or DATE (
	now()) BETWEEN a.pre_inscricao_data_inicial 
	AND a.inscricao_data_final 
				AND a.excluido = 0
				AND id_reoferta NOT IN ( SELECT id_reoferta FROM coopex_reoferta.academico_autorizado ) 
				OR id_reoferta IN ( SELECT id_reoferta FROM coopex_reoferta.academico_autorizado
									INNER JOIN coopex_reoferta.reoferta USING ( id_reoferta )  where id_usuario = $id_pessoa AND id_parecer = 2 AND a.excluido = 0 )
			GROUP BY
				id_reoferta	
			ORDER BY
				departamento,
				disciplina DESC";

	$reoferta = $coopex->query($sql);

?>

<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Reoferta</a></li>
		<li class="breadcrumb-item active">Consulta</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title col-6">
			<i class='subheader-icon fal fa-repeat'></i> Reofertas
			<small>
				Gerenciamento de Reofertas
			</small>
		</h1>
		<?php
			if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][2])){
		?>
		<div class="subheader-title col-6 text-right">
			<a href="reoferta/cadastro">
				<button type="button" class="btn btn-lg btn-primary waves-effect waves-themed">
					<span class="ni ni-plus mr-3"></span>
					Cadastrar Reoferta
				</button>
			</a>
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
									<th>Disciplina</th>
									<th>Curso</th>
									<th>Período</th>
									<th class="text-center">Pré-matricula</th>
									<th class="text-center">Matricula</th>
									<th class="text-center">Ações</th>
								</tr>
							</thead>
							<tbody>
								<?php
									while($row = $reoferta->fetch(PDO::FETCH_OBJ)){
										$sql = "SELECT
													* 
												FROM
													coopex_reoferta.pre_matricula 
												WHERE
													id_reoferta = $row->id_reoferta 
													AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
										$pre_confirmada = $coopex->query($sql);

										$sql = "SELECT
													* 
												FROM
													coopex_reoferta.matricula 
												WHERE
													id_reoferta = $row->id_reoferta 
													AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
										$matricula_confirmada = $coopex->query($sql);

								?>
								<tr>
									<td class="pointer">
										<a href="reoferta/inscricao/inscricao/<?php echo $row->id_reoferta?>" class="btn btn-sm  mr-2" title="Editar Registro"><?php echo texto($row->disciplina)?></a>
									</td>
									<td><?php echo texto($row->departamento)?></td>
									<td>
										<a href="reoferta/inscricao/inscricao/<?php echo $row->id_reoferta?>" class="btn btn-sm  mr-2" title="Editar Registro"><?php echo texto($row->periodo)?></a>
									</td>
									<td class="text-center">
										<?php
											if($pre_confirmada->rowCount()){
												echo '<a href="reoferta/inscricao/inscricao/'.$row->id_reoferta.'" class="btn btn-success btn-icon waves-effect waves-themed"><i class="fal fa-check"></i></a>';
											} else {
												echo '<a href="reoferta/inscricao/inscricao/'.$row->id_reoferta.'" class="btn btn-outline-danger btn-icon waves-effect waves-themed"><i class="fal fa-times"></i></a>';	
											}
										?>
									</td>
									<td class="text-center">
										<?php
											if($matricula_confirmada->rowCount()){
												echo '<a href="reoferta/inscricao/inscricao/'.$row->id_reoferta.'" class="btn btn-success btn-icon waves-effect waves-themed"><i class="fal fa-check"></i></a>';
											} else {
												echo '<a href="reoferta/inscricao/inscricao/'.$row->id_reoferta.'" class="btn btn-outline-danger btn-icon waves-effect waves-themed"><i class="fal fa-times"></i></a>';	
											}
										?>
									</td>
									<td style="width: 70px" class="text-center">
										<a href="reoferta/inscricao/inscricao/<?php echo $row->id_reoferta?>" class="btn btn-primary btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-plus"></i></a>
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

            $(document).ready(function()
            {
                $('#dt-basic-example').dataTable(
                {
                    responsive: true,
                    pageLength: 30,
                    order: [
                        [2, 'desc']
                    ],
                    rowGroup:
                    {
                        dataSrc: 1
                    },
                    columnDefs: [
			            {
			                "targets": [ 1 ],
			                "visible": false
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
    </body>
</html>
