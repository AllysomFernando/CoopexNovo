<?php

	$id_menu = 69;

	if(isset($_GET['id'])){
		$ano = $_GET['id'];
	} else {
		$sql = "SELECT
					ano 
				FROM
					tesouraria.material 
				GROUP BY
					ano 
				ORDER BY
					ano DESC
				LIMIT 1";
		$res = $coopex->query($sql);
		$row = $res->fetch(PDO::FETCH_OBJ);
		$ano = $row->ano;
	}

	$sql = "SELECT
				*, date(data_devolucao) AS data
			FROM
				tesouraria.devolucao
			INNER JOIN tesouraria.material USING (id_material)
			WHERE
				ano = $ano
			ORDER BY
				data_devolucao DESC";

	$reoferta = $coopex->query($sql);

?>

<iframe class="d-none" name="dados" src=""></iframe>
<form id="form_excluir_registro" method="post" target="dados" action="modulos/tesouraria/devolucao/cadastro_dados.php">
	<input type="hidden" id="excluir_registro" name="excluir_registro">
</form>	
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Devolução</a></li>
		<li class="breadcrumb-item active">Consulta</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title col-6">
			<i class='subheader-icon fal fa-repeat'></i> Devolução
			<small>
				Gerenciamento de Devoluções
			</small>
		</h1>
		<?php
			if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][2])){
		?>
		<div class="subheader-title col-6 text-right">
			<a href="tesouraria/colegio/devolucao/cadastro">
				<button type="button" class="btn btn-lg btn-primary waves-effect waves-themed">
					<span class="ni ni-plus mr-3"></span>
					Cadastrar Devolução
				</button>
			</a>
		</div>
		<?php
			}
		?>
	</div>
	<div class="row">
			<div class="col-xl-12">
			  	<div id="panel-2" class="panel">
				  	<div class="panel-hdr">
						<h2>
							Ano
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>

					<script type="text/javascript">
						function selecionar_semestre(){
							var ano = $("#ano").val();
							window.location.href = "https://coopex.fag.edu.br/tesouraria/colegio/devolucao/consulta/"+ano;
						}
					</script>

					<div class="panel-container show">

						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Selecione o Ano</label>
										<?php
											$sql = "SELECT
														ano 
													FROM
														tesouraria.material 
													GROUP BY
														ano 
													ORDER BY
														ano DESC";
											$periodo = $coopex->query($sql);
										?>
										<select onchange="selecionar_semestre()" id="ano" class="select2 form-control" required="">
										<?php
											while($row = $periodo->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if($ano == $row->ano){
													$selecionado = 'selected=""';
												}
										?>	
											<option <?= $selecionado?>  value="<?php echo $row->ano?>"><?php echo $row->ano?> </option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											Selecione o período da reoferta
										</div>
									</div>
								</div>	
									
								
							</div>
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
									<th>Devolução</th>
									<th>Data</th>
									<th class="text-center">Ações</th>
								</tr>
							</thead>
							<tbody>
								<?php
									while($row = $reoferta->fetch(PDO::FETCH_OBJ)){
								?>
								<tr>
									<td class="pointer"><a href="tesouraria/colegio/material/cadastro/<?php echo $row->id_material?>"><?php echo texto($row->material)?></a></td>
									<td class="pointer"><?php echo converterData($row->data)?></td>
									<td style="width: 70px">
										<?php
											if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][3])){
										?>
										<a href="tesouraria/colegio/material/devolucao/<?php echo $row->id_material?>" class="btn btn-sm btn-icon btn-outline-success rounded-circle mr-2" title="Editar Registro">
											<i class="fal fa-pencil-alt"></i>
										</a>
										<?php
											}
										?>
										
										<?php
											if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][4])){
										?>
										<a href="javascript:excluir_registro('tesouraria.devolucao', 'id_devolucao', <?php echo $row->id_material?>)" class="btn btn-sm btn-icon btn-outline-danger rounded-circle" title="Excluir Registro">
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
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
		<script src="js/formplugins/select2/select2.bundle.js"></script>
        <script src="js/datagrid/datatables/datatables.bundle.js"></script>
        <script>
            $(document).ready(function(){
				$('.select2').select2();
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
