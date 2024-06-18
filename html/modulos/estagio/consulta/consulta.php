<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . "/../php/repository/UserRepository.php";

$repository = new UserRepository($coopex);

$id_menu = 115;
$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];
$session_tipo_usuario = $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'];

$relatorios = $repository->getEstagiosByUserAccessLevel($session_tipo_usuario, $id_pessoa);

?>

<iframe class="d-none" name="dados" src=""></iframe>
<form id="form_excluir_registro" method="post" target="dados" action="modulos/estagio/cadastro/cadastro_dados.php">
	<input type="hidden" id="excluir_registro" name="excluir_registro">
</form>
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Relatório de estágio</a></li>
		<li class="breadcrumb-item active">Consulta</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title col-6">
			<i class='subheader-icon fal fa-paperclip'></i> Relatório de estágio
			<small>
				Gerenciamento de relatório de estágio
			</small>
		</h1>

		<div class="subheader-title col-6 text-right">
			<a href="estagio/consulta/cadastro">
				<button type="button" class="btn btn-lg btn-primary waves-effect waves-themed">
					<span class="ni ni-plus mr-3"></span>
					Cadastrar relatório
				</button>
			</a>
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
									<?php
									if ($session_tipo_usuario != 6) { ?>
										<th>Aluno</th>
									<?php }
									?>
									<th>Empresa/Projeto</th>
									<th>Data de cadastro</th>
									<th class="text-center">Ações</th>
								</tr>
							</thead>
							<tbody>
								<?php

								foreach ($relatorios as $row) {
								?>
									<tr>
										<?php
										if ($session_tipo_usuario != 6) { ?>
											<th class="pointer"><a href="estagio/consulta/cadastro/<?php echo $row->id_estagio ?>"><?php echo $row->nome ?></a></th>
										<?php }
										?>
										<td class="pointer"><a href="estagio/consulta/cadastro/<?php echo $row->id_estagio ?>"><?php echo $row->empresa ?></a>
										</td>
										<td class="pointer"><?php echo $row->data_cadastro ?></a>
										</td>
										<td style="width: 70px">
											<?php
											if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][3])) {
											?>
												<a href="estagio/consulta/cadastro/<?php echo $row->id_estagio ?>" class="btn btn-sm btn-icon btn-outline-success rounded-circle mr-2" title="Editar Registro">
													<i class="fal fa-pencil-alt"></i>
												</a>
											<?php
											}
											?>

											<?php
											if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][4])) {
											?>
												<a href="javascript:excluir_registro('estagio.estagio', 'id_estagio', <?php echo $row->id_estagio ?>)" class="btn btn-sm btn-icon btn-outline-danger rounded-circle" title="Excluir Registro">
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
			responsive: true,
			order: [
				[0, 'asc']
			]
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