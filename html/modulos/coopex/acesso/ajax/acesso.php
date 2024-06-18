<?php session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../../../../php/mysql.php");
require_once("../../../../php/utils.php");

$id_pessoa = $_GET['id_usuario'];
?>

<h1>Acessos Possuidos</h1>
<ul class="list-group">
	<?php
	$query = "SELECT m.menu, pt.menu_permissao_tipo
FROM menu_permissao_usuario pu
    INNER JOIN menu_permissao mp USING (id_menu_permissao)
    INNER JOIN menu m USING (id_menu)
    INNER JOIN menu_permissao_tipo pt USING (id_menu_permissao_tipo)
WHERE id_pessoa = $id_pessoa";

	$permissao = "SELECT m.menu, tu.tipo_usuario, mpt.menu_permissao_tipo
FROM pessoa p
    INNER JOIN tipo_usuario tu USING(id_tipo_usuario)
    INNER JOIN menu_permissao_tipo_usuario mptu USING (id_tipo_usuario)
    INNER JOIN menu_permissao USING (id_menu_permissao)
    INNER JOIN menu_permissao_tipo mpt USING (id_menu_permissao_tipo)
    INNER JOIN menu m USING (id_menu)
WHERE id_pessoa = $id_pessoa";
	$result = $coopex->query($permissao);
	$result1 = $coopex->query($query);

	if ($result1->rowCount() == 0) {
		echo "Nenhum acesso liberado";
	} ?>

	<div class="row">
		<div class="col-xl-12">
			<div id="panel-1" class="panel">
				<div class="panel-container show">
					<div class="panel-content">
						<table id="dt-basic-example1"
							class="  table table-bordered table-hover table-striped w-100 dataTable dtr-inline mt-5">
							<thead class="bg-primary-600">
								<th>Menu</th>
								<th>Tipo</th>
								</tr>
							</thead>
							<tbody>
								<?php
								while ($row1 = $result->fetch(PDO::FETCH_OBJ)) {
									?>
									<tr>
										<td>
											<b><?php echo utf8_encode($row1->menu) ?></b>
										</td>
										<td class="text-center">
											<?php echo utf8_encode($row1->menu_permissao_tipo) ?>
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
	</div>

	<h1>Liberar Acesso</h1>
	<div class="row">
		<div class="col-xl-12">
			<div id="panel-1" class="panel">
				<div class="panel-container show">
					<div class="panel-content">
						<table id="dt-basic-example"
							class="  table table-bordered table-hover table-striped w-100 dataTable dtr-inline mt-5">
							<thead class="bg-primary-600">
								<?php
								$sql = "SELECT m.menu, pt.menu_permissao_tipo, mp.id_menu_permissao	
								FROM menu m
										 INNER JOIN menu_permissao mp USING (id_menu)
										 INNER JOIN menu_permissao_tipo pt USING (id_menu_permissao_tipo)";
								$res = $coopex->query($sql);
								?>
								<tr>
									<th>Menu</th>
									<th>Tipo</th>
									<th>Liberar</th>
									<th>Retirar</th>
								</tr>
							</thead>
							<tbody>
								<?php
								while ($row = $res->fetch(PDO::FETCH_OBJ)) {
									?>
									<tr>
										<td>
											<b><?php echo utf8_encode($row->menu) ?></b>
										</td>
										<td class="text-center">
											<?php echo utf8_encode($row->menu_permissao_tipo) ?>
										</td>
										<td>
											<button class="btn btn-primary"
												onclick="libararAcesso(<?php echo $id_pessoa ?>, <?php echo $row->id_menu_permissao ?>)">
												<i class="fal fa-check"></i>
											</button>
										</td>
										<td>
											<button class="btn btn-danger"
												onclick="tirarAcesso(<?php echo $id_pessoa ?>, <?php echo $row->id_menu_permissao ?>)">
												<i class="fal fa-times"></i>
											</button>
										</td>
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
	</div>

	<style>
		table tr td {
			vertical-align: middle !important;
		}
	</style>
	<script src="js/datagrid/datatables/datatables.bundle.js"></script>
	<script>
		$(document).ready(function () {
			$('#dt-basic-example').dataTable({
				responsive: true,
				order: [
					[0, 'asc']
				],
				rowGroup: {
					dataSrc: 0
				},
				columnDefs: [{
					"targets": [0],
					"visible": false
				}]
			});
		});
		$(document).ready(function () {
			$('#dt-basic-example1').dataTable({
				responsive: true,
				order: [
					[0, 'asc']
				],
				rowGroup: {
					dataSrc: 0
				},
				columnDefs: [{
					"targets": [0],
					"visible": false
				}]
			});
		});

		function libararAcesso(id_pessoa, id_menu_permissao) {
			$.ajax({
				url: "modulos/coopex/acesso/ajax/liberar_acesso.php",
				type: "POST",
				data: {
					id_pessoa: id_pessoa,
					id_menu_permissao: id_menu_permissao
				},
				success: function (data) {
					if (data == 1) {
						liberacaoOK();
					} else {
						liberacaoFalha();
					}
				}
			});
		}

		function tirarAcesso(id_pessoa, id_menu_permissao) {
			$.ajax({
				url: "modulos/coopex/acesso/ajax/tirar_acesso.php",
				type: "POST",
				data: {
					id_pessoa: id_pessoa,
					id_menu_permissao: id_menu_permissao
				},
				success: function (data) {
					if (data == 1) {
						tiradaOk();
					} else {
						tiradaFalha();
					}
				}
			});
		}

		function liberacaoOK() {
			Swal.fire({
				type: "success",
				title: "Acesso liberado com sucesso!",
				showConfirmButton: false,
				timer: 1500,
				onClose: () => {
					document.location.reload(true)
				}
			});
		}

		function liberacaoFalha() {
			Swal.fire({
				type: "error",
				title: "Falha ao liberar acesso",
				showConfirmButton: true
			});
		}

		function tiradaOk() {
			Swal.fire({
				type: "success",
				title: "Acesso retirado com sucesso!",
				showConfirmButton: false,
				timer: 1500,
				onClose: () => {
					document.location.reload(true)
				}
			});
		}
		function tiradaFalha() {
			Swal.fire({
				type: "error",
				title: "Falha ao retirar acesso",
				showConfirmButton: true
			});

		}
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