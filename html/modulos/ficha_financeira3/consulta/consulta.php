<?php

$id_menu = 22;

$campus = $_SESSION['coopex']['usuario']['pessoa']->id_campus ? " and departamento.id_campus = " . $_SESSION['coopex']['usuario']['pessoa']->id_campus : "";

#VERIFICA SE O TIPO DE USUÁRIO POSSUI PERMISSÃO PARA ACESSAR TODOS OS REGISTROS
if (in_array($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'], array(1, 2, 3, 8, 9, 11, 13))) {
	$where  = " AND 1=1 ";
} else {
	$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];
	$where  = "AND (ficha_financeira.ficha_financeira.id_pessoa = $id_pessoa
					OR id_departamento IN (SELECT id_departamento FROM coopex.departamento_pessoa WHERE id_pessoa = $id_pessoa)) ";
}

if ($_SESSION['coopex']['usuario']['id_pessoa'] == 5000208750) {
	$where .= " and departamento.id_campus = 1100000002";
}

if ($_SESSION['coopex']['usuario']['id_pessoa'] == 5000216706) {
	/*$where .= " and id_etapa = 11";*/
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (isset($_GET['id'])) {
	$id_semestre = $_GET['id'];
} else {
	$sql = "SELECT
				*
			FROM
				ficha_financeira.semestre_letivo
			ORDER BY
				id_semestre_letivo DESC
			LIMIT 1";
	$res = $coopex->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);
	$id_semestre = $row->id_semestre;
}


$sql = "SELECT
				* 
			FROM
				ficha_financeira.ficha_financeira
				
			WHERE
				id_etapa is null
			";
$reoferta = $coopex->query($sql);

while ($row = $reoferta->fetch(PDO::FETCH_OBJ)) {

	$sql2 = "SELECT
		id_etapa 
	FROM
	ficha_financeira.ficha_financeira_etapa 
	WHERE
		id_ficha_financeira = $row->id_ficha_financeira 
	ORDER BY
		id_etapa DESC 
		LIMIT 1";

	$etapa = $coopex->query($sql2);
	$row2 = $etapa->fetch(PDO::FETCH_OBJ);

	if ($row2->id_etapa) {
		$sql3 = "UPDATE `ficha_financeira`.`ficha_financeira` 
			SET `id_etapa` = $row2->id_etapa 
			WHERE
				`id_ficha_financeira` = $row->id_ficha_financeira";
		$coopex->query($sql3);
	}
}

?>

<iframe class="d-none" name="dados" src=""></iframe>
<form id="form_excluir_registro" method="post" target="dados" action="modulos/ficha_financeira/cadastro/cadastro_dados.php">
	<input type="hidden" id="excluir_registro" name="excluir_registro">
</form>
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Ficha Financeira</a></li>
		<li class="breadcrumb-item active">Consulta</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title col-6">
			<i class='subheader-icon fal fa-repeat'></i> Ficha Financeira
			<small>
				Gerenciamento de Ficha Financeira
			</small>
		</h1>
		<?php
		if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][2])) {
		?>
			<div class="subheader-title col-6 text-right">
				<a href="ficha_financeira/cadastro">
					<button type="button" class="btn btn-lg btn-primary waves-effect waves-themed">
						<span class="ni ni-plus mr-3"></span>
						Cadastrar Ficha Financeira
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
						Semestre
					</h2>
					<div class="panel-toolbar">
						<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
						<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
					</div>
				</div>

				<script type="text/javascript">
					function selecionar_semestre() {
						var semestre = $("#id_semestre_letivo").val();
						window.location.href = "https://coopex.fag.edu.br/ficha_financeira/consulta/" + semestre;
					}
				</script>

				<div class="panel-container show">

					<div class="panel-content p-0">
						<div class="panel-content">
							<div class="form-row">
								<div class="col-md-3 mb-3">
									<label class="form-label" for="validationCustom03">Selecione o Semestre</label>
									<?php
									$sql = "SELECT
														* 
													FROM
														ficha_financeira.semestre_letivo 
													WHERE
														id_semestre is not null	
													ORDER BY
														id_semestre_letivo DESC";

									$periodo = $coopex->query($sql);
									?>
									<select onchange="selecionar_semestre()" id="id_semestre_letivo" class="select2 form-control" required="">
										<?php
										while ($row = $periodo->fetch(PDO::FETCH_OBJ)) {
											$selecionado = '';
											if ($id_semestre == $row->id_semestre) {
												$selecionado = 'selected=""';
											}
										?>
											<option <?= $selecionado ?> value="<?php echo $row->id_semestre ?>"><?php echo $row->id_semestre_letivo ?> </option>
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
									<th>Disciplina</th>
									<th>Curso</th>
									<th>Período</th>
									<th>Cadastro</th>
									<th>Situação</th>
									<th class="text-center">Ações</th>
								</tr>
							</thead>
							<tbody>
								<?php
								while ($row = $reoferta->fetch(PDO::FETCH_OBJ)) {

								?>
									<tr>
										<td class="pointer"><?php echo texto($row->nome) ?></td>
										<td><?php echo texto($row->departamento) ?></td>

										<td><?php echo texto($row->periodo) ?></td>
										<td><?php echo ($row->data_cadastro) ?></td>
										<td><span class="badge badge-<?php echo $row->cor; ?> badge-pill"><?php echo texto($row->etapa); ?></span></td>
										<td style="width: 100px" class="text-center">
											<?php
											if (
												isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][3]) ||
												isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][5]) ||
												$_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 2 ||
												isset(
													$_SESSION['coopex']['usuario']['permissao'][$id_menu][6]
												)
											) {
											?>
												<a href="ficha_financeira/cadastro/<?php echo $row->id_ficha_financeira ?>" class="btn btn-success btn-icon rounded-circle waves-effect waves-themed mr-2"><i class="fal fa-pencil"></i></a>
											<?php
											}
											?>

											<?php
											if ((isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][4]) ||  $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 2) && $row->id_etapa < 3) {
											?>
												<a href="javascript:excluir_registro('ficha_financeira.ficha_financeira', 'id_ficha_financeira', <?php echo $row->id_ficha_financeira ?>)" class="btn btn-danger btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-times"></i></a>

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
<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script>
	function base(x) {
		return function produto(y) {
			return x * y;
			console.log("asdf" + x);
			console.log(y);
		}
	}

	var f = base(2);
	var g = base(-1);


	$(document).ready(function() {

		$('.select2').select2();


		$('#dt-basic-example').dataTable({
			responsive: true,
			pageLength: 15,
			stateSave: true,
			order: [
				[1, 'asc']
			],
			rowGroup: {
				dataSrc: 1
			},
			columnDefs: [{
				"targets": [1],
				"visible": false
			}]
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