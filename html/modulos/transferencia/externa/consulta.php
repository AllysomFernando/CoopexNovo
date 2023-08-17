<?php

$id_menu = 89;

$sql = "SELECT * FROM transferencia.transferencia_externa  WHERE id_transferencia_externa = id_transferencia_externa AND excluido = 0 ORDER BY academico DESC";


$row = $coopex->query($sql);

?>

<iframe class="d-none" name="dados" src=""></iframe>
<form id="form_excluir_registro" method="post" target="dados" action="modulos/transferencia/externa/cadastro_dados.php">
	<input type="hidden" id="excluir_registro" name="excluir_registro">
</form>
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Ficha Transferência</a></li>
		<li class="breadcrumb-item active">Consulta</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title col-6">
			<i class='subheader-icon fal fa-repeat'></i> Ficha Transferência
			<small>
				Gerenciamento de Ficha Transferência
			</small>
		</h1>
		<?php
		if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][2])) {
		?>
			<div class="subheader-title col-6 text-right">
				<a href="transferencia/externa/cadastro">
					<button type="button" class="btn btn-lg btn-primary waves-effect waves-themed">
						<span class="ni ni-plus mr-3"></span>
						Cadastrar Ficha Transferência
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
									<th>Nome</th>
									<th>Data Cadastro</th>
									<th class="text-center">Ações</th>
								</tr>
							</thead>
							<tbody>
								<?php
								while ($res = $row->fetch(PDO::FETCH_OBJ)) {
								?>
									<tr>
										<td class="pointer"><a href="transferencia/externa/relatorio/geral/<?php echo $res->id_transferencia_externa ?>"><?php echo strtoupper(utf8_encode(($res->academico))) ?></a></td>
										<td class="pointer"><?php echo $res->data_cadastro ?></td>
										<td style="width: 130px">
											<?php
											if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][3])) {
											?>
												<a href="transferencia/externa/cadastro/<?php echo $res->id_transferencia_externa ?>" class="btn btn-sm btn-icon btn-outline-success rounded-circle mr-2" title="Editar Registro">
													<i class="fal fa-pencil-alt"></i>
												</a>
											<?php
											}
											?>
											<?php
											if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][4])) {
											?>
												<a href="javascript:excluir_registro('transferencia.transferencia_externa', 'id_transferencia_externa', <?php echo $res->id_transferencia_externa ?>)" class="btn btn-sm btn-icon btn-outline-danger rounded-circle" title="Excluir Registro">
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
	$(document).ready(function() {
		$('#dt-basic-example').dataTable({
			
		});
	});

	function exclusaoOK() {
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

	function exclusaoFalha() {
		Swal.fire({
			type: "error",
			title: "Falha ao excluir registro",
			showConfirmButton: true
		});
	}
</script>
</body>

</html>