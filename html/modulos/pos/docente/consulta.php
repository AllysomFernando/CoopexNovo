<?php

$id_menu = 109;
$isAdmin = $_SESSION['coopex']['usuario']['sistema']['tipo_usuario'] == "ADMINISTRADOR";

$sql = "SELECT
				*
			FROM
				pos.docente
			INNER JOIN pos.titulacao USING (id_titulacao)
			WHERE
			excluido = 0
			ORDER BY
				nome";

$res = $coopex->query($sql);
$docentes = $res->fetchAll(PDO::FETCH_OBJ);

?>

<iframe class="d-none" name="dados" src=""></iframe>
<form id="form_excluir_registro" method="post" target="dados" action="modulos/pos/docente/ajax/cadastro_dados.php">
	<input type="hidden" id="excluir_registro" name="excluir_registro">
</form>
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<?php if (!$isAdmin) { ?>
		<div class="alert border-danger bg-transparent text-secondary fade show" role="alert">
			<div class="d-flex align-items-center">
				<div class="alert-icon">
					<span class="icon-stack icon-stack-md">
						<i class="base-7 icon-stack-3x color-danger-900"></i>
						<i class="fal fa-times icon-stack-1x text-white"></i>
					</span>
				</div>
				<div class="flex-1">
					<span class="h5 color-danger-900">Este painel está em manutenção e será reativado em breve</span>
				</div>
			</div>
		</div>
	<?php
		exit;
	} ?>

	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Pós-Graduação</a></li>
		<li class="breadcrumb-item active">Docente</li>
		<li class="breadcrumb-item active">Consulta</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title col-6">
			<i class='subheader-icon fal fa-repeat'></i> Docentes de Pós-Graduação
			<small>
				Gerenciamento de Docentes de Pós-Graduação
			</small>
		</h1>
		<?php
		if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][2])) {
		?>
			<div class="subheader-title col-6 text-right">
				<a href="pos/docente/cadastro">
					<button type="button" class="btn btn-lg btn-primary waves-effect waves-themed">
						<span class="ni ni-plus mr-3"></span>
						Cadastrar Docente
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
									<th>Titulação</th>
									<th>Nacionalidade</th>
									<th class="text-center">Ações</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($docentes as $docente) {
								?>
									<tr>
										<td>
											<?php echo texto($docente->nome) ?>
										</td>
										<td>
											<?php echo $docente->titulacao ?>
										</td>
										<td>
											<?php echo $docente->nacionalidade ?>
										</td>
										<td style="width: 70px">
											<?php
											if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][3])) {
											?>
												<a href="pos/docente/cadastro/<?php echo $docente->id_docente ?>" class="btn btn-sm btn-icon btn-outline-success rounded-circle mr-2" title="Editar Registro">
													<i class="fal fa-pencil-alt"></i>
												</a>
											<?php
											}
											?>

											<?php
											if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][4]) && $isAdmin) {
											?>
												<a href="javascript:excluir_registro('pos.docente', 'id_docente', <?php echo $docente->id_docente ?>)" class="btn btn-sm btn-icon btn-outline-danger rounded-circle" title="Excluir Registro">
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
			"aaSorting": []
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