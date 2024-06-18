<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_menu = 41;

// vmrosa = coordenacao
// aline gurgacz = pro reitoria
$usuario = trim($_SESSION["coopex"]["usuario"]["usuario"]);
$tipo_usuario = trim($_SESSION["coopex"]["usuario"]["sistema"]["id_tipo_usuario"]);
$id_pessoa = $_SESSION["coopex"]["usuario"]["id_pessoa"];
$isCoordenacao = $tipo_usuario == "1" || $tipo_usuario == "17" || $tipo_usuario == "21";

// echo "<pre>";
// var_dump($_SESSION["coopex"]);
// echo "</pre>";

if ($tipo_usuario == "17") {
	$sql = "SELECT DISTINCT
					c.id as id_curso,
					c.nome as curso_nome,
					a.area,
					p.nome,
					pc.id_parecer,
					pc.tipo_usuario,
					DATE_FORMAT(c.data_cadastro, '%d/%m/%Y') AS data_cadastro_projeto
				FROM pos.curso c
						INNER JOIN pos.area a USING (id_area)
						INNER JOIN coopex.pessoa p USING (id_pessoa)
					LEFT JOIN pos.parecer_curso pc ON pc.id_projeto = c.id
				WHERE
				excluido = 0
				AND
				pc.tipo_usuario = 'PROPONENTE'
				AND pc.id_parecer = 1
				ORDER BY data_cadastro DESC";
} else if ($tipo_usuario == "21") {
	$sql = "SELECT DISTINCT
					c.id as id_curso,
					c.nome as curso_nome,
					a.area,
					p.nome,
					pc.id_parecer,
					pc.tipo_usuario,
					DATE_FORMAT(c.data_cadastro, '%d/%m/%Y') AS data_cadastro_projeto
				FROM pos.curso c
						INNER JOIN pos.area a USING (id_area)
						INNER JOIN coopex.pessoa p USING (id_pessoa)
					LEFT JOIN pos.parecer_curso pc ON pc.id_projeto = c.id
				WHERE
				excluido = 0
				AND
				pc.tipo_usuario = 'COORDENACAO'
				AND pc.id_parecer = 1
				ORDER BY data_cadastro DESC";
} else if ($tipo_usuario == "1") {
	$sql = "SELECT DISTINCT
					c.id as id_curso,
					c.nome as curso_nome,
					a.area,
					p.nome,
					pc.id_parecer,
					pc.tipo_usuario,
					DATE_FORMAT(c.data_cadastro, '%d/%m/%Y') AS data_cadastro_projeto
				FROM pos.curso c
						INNER JOIN pos.area a USING (id_area)
						INNER JOIN coopex.pessoa p USING (id_pessoa)
					LEFT JOIN pos.parecer_curso pc ON pc.id_projeto = c.id
				WHERE
				excluido = 0
				ORDER BY data_cadastro DESC";
} else {
	$sql = "SELECT DISTINCT c.id as id_curso,
						c.nome as curso_nome,
						a.area,
						p.nome,
						pc.id_parecer,
						pc.tipo_usuario,
						DATE_FORMAT(c.data_cadastro, '%d/%m/%Y') AS data_cadastro_projeto
					FROM pos.curso c
							INNER JOIN pos.area a USING (id_area)
							INNER JOIN pessoa p USING (id_pessoa)
							LEFT JOIN pos.parecer_curso pc ON pc.id_projeto = c.id
					WHERE excluido = 0
					AND c.id_pessoa = $id_pessoa
					GROUP BY c.id
					ORDER BY c.data_cadastro DESC";
}

$reoferta = $coopex->query($sql);

?>

<iframe class="d-none" name="dados" src=""></iframe>
<form id="form_excluir_registro" method="post" target="dados" action="modulos/pos/projeto/api/routes/delete-curso.php">
	<input type="hidden" id="excluir_registro" name="excluir_registro">
</form>
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Projetos de Pós-Graduação</a></li>
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
		<?php if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][2])) { ?>
			<div class="subheader-title col-6 text-right">
				<a href="pos/projeto/cadastro">
					<button type="button" class="btn btn-lg btn-primary waves-effect waves-themed" id="button-cadastro">
						<span class="ni ni-plus mr-3"></span>
						Cadastrar Projeto
					</button>
				</a>
			</div>
		<?php } ?>
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
									<th>Curso</th>
									<th>Área</th>
									<th>Data Cadastro</th>
									<th>Proponente</th>
									<!-- <th>Situação</th> -->
									<th class="text-center">Ações</th>
								</tr>
							</thead>
							<tbody>
								<?php while ($row = $reoferta->fetch(PDO::FETCH_OBJ)) { ?>
									<tr>
										<td class="pointer"><?= texto($row->curso_nome) ?></td>
										<td><?= texto($row->area) ?></td>
										<td><?= ($row->data_cadastro_projeto) ?></td>
										<td><?= ($row->nome) ?></td>
										<!-- <td><span class="badge badge-<?php echo $row->parecer_pos ? "success" : "warning" ?> badge-pill"><?php echo $row->parecer_pos ? "Ativo" : "Inativo" ?></span></td> -->
										<td style="width: 70px">
											<?php
											if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][3])) {
												$base_url = "pos/projeto/cadastro/";
												if ($isCoordenacao) $base_url = "pos/projeto/aprovacao/"
											?>
												<a href="<?php echo $base_url . $row->id_curso ?>" class="btn btn-sm btn-icon btn-outline-success rounded-circle mr-2" title="Editar Registro">
													<i class="fal fa-pencil-alt"></i>
												</a>
											<?php
											}
											?>

											<?php
											if (isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][4])) {
											?>
												<a href="javascript:excluir_registro('pos.curso', 'id', <?php echo $row->id_curso ?>)" class="btn btn-sm btn-icon btn-outline-danger rounded-circle" title="Excluir Registro">
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

		const hasFormOnCache = JSON.parse(localStorage.getItem("cache-form"));

		if (hasFormOnCache) {
			document.querySelector('#button-cadastro').innerHTML = "Continuar cadastro de projeto"
		}

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

	$("#form_excluir_registro").submit(async function(e) {

		e.preventDefault();

		var form = $(this);

		var actionUrl = form.attr('action');

		$.ajax({
			type: "POST",
			url: actionUrl,
			data: form.serialize(),
			success: function(data) {
				console.log(data)
				exclusaoOK(1);
			},
			error: function() {
				exclusaoFalha(1);
			}
		});

		await getDisciplinas();
	});
</script>
</body>

</html>