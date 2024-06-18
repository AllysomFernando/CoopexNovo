<?php
$id_menu = 67;
$chave = "id_material";

if (isset($_GET['id'])) {
	$$chave = $_GET['id'];
	$sql = "SELECT
				*
			FROM
				tesouraria.material
			WHERE id_material = " . $_GET['id'];

	$res = $coopex->query($sql);
	$dados = $res->fetch(PDO::FETCH_OBJ);
} else {
	$$chave = 0;
}

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Material</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID.
				<?php echo $id_menu ?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-calendar'></i> Material
			<small>
				Cadastro de Materials
			</small>
		</h1>
	</div>
	<iframe class="d-none" name="dados" src=""></iframe>
	<form class="needs-validation" novalidate="" method="post" target="dados"
		action="modulos/tesouraria/colegio/material/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave ?>" value="<?php echo $$chave ? $$chave : 0 ?>">
		<input type="hidden" name="material" value="1">
		<div class="row">
			<div class="col-xl-12">
				<div id="panel-2" class="panel">
					<div class="panel-hdr">
						<h2>
							Material
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip"
								data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip"
								data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content p-0">
							<div class="panel-content">

								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Série<span
												class="text-danger">*</span></label>
										<?php
										$sql = "SELECT
														*
													FROM
														tesouraria.serie
													WHERE
														ativo = 1
													ORDER BY
														serie";

										$serie = $coopex->query($sql);
										?>
										<select id="id_serie" name="id_serie" class="select2 form-control" required>
											<option value="">Selecione a Série</option>
											<?php
											while ($row = $serie->fetch(PDO::FETCH_OBJ)) {
												$selecionado = '';
												if ($dados->id_serie == $row->id_serie) {
													$selecionado = 'selected=""';
												}
												?>
												<option <?php echo isset($dados->id_serie) ? $selecionado : "" ?>
													value="<?php echo $row->id_serie ?>">
													<?php echo utf8_encode($row->serie) ?></option>
												<?php
											}
											?>
										</select>
										<div class="invalid-feedback">
											Selecione a Série
										</div>
									</div>
								</div>

								<div class="form-row">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom03">Material <span
												class="text-danger">*</span></label>
										<input name="material"
											value="<?php echo isset($dados->material) ? texto($dados->material) : "" ?>"
											type="text" class="form-control" id="material" placeholder="" value=""
											required>
										<div class="invalid-feedback">
											Campo obrigatório, preencha o Material
										</div>
									</div>
								</div>

								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Ano<span
												class="text-danger">*</span></label>
										<?php
										$sql = "SELECT
											ano
										FROM
											tesouraria.material
										GROUP BY
											ano
										ORDER BY
											ano DESC
										LIMIT 1";

										$ano = $coopex->query($sql);
										?>
										<select id="ano" name="ano" class="select2 form-control" required>
											<option value="">Selecione o ano</option>
											<?php
											while ($row = $ano->fetch(PDO::FETCH_OBJ)) {
												$selecionado = '';
												if ($dados->ano == $row->ano) {
													$selecionado = 'selected=""'; /*TODO: Melhorar questão do ano, pegar por id antes de entrar nessa parte para o usuário não ficar escolhendo*/
												}
												?>
												<option <?php echo isset($dados->ano) ? $selecionado : "" ?>
													value="<?php echo $row->ano ?>">
													<?php echo utf8_encode($row->ano) ?></option>
												<?php
											}
											?>
										</select>
										<div class="invalid-feedback">
											Selecione a ano
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-container show">
						<div
							class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
							<button class="btn btn-primary ml-auto"
								type="submit"><?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar" ?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</main>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<script src="js/formplugins/select2/select2.bundle.js"></script>

<script>

	$(document).ready(function () {
		$('.select2').select2();
	});

	function cadastroOK(operacao) {
		var msg = operacao == 1 ? "Registro cadastrado com sucesso" : "Registro alterado com sucesso";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				<?php
				if (!isset($_GET['id'])) {
					echo "window.history.back();";
				} else {
					echo "document.location.reload(true);";
				}
				?>
			}
		});
	}
	function cadastroFalha(operacao) {
		var msg = operacao == 1 ? "Falha ao cadastrar dados" : "Falha ao alterar dados";
		Swal.fire({
			type: "error",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				window.history.back();
			}
		});
	}



	// Example starter JavaScript for disabling form submissions if there are invalid fields
	(function () {
		'use strict';
		window.addEventListener('load', function () {
			// Fetch all the forms we want to apply custom Bootstrap validation styles to
			var forms = document.getElementsByClassName('needs-validation');
			// Loop over them and prevent submission
			var validation = Array.prototype.filter.call(forms, function (form) {
				form.addEventListener('submit', function (event) {
					if (form.checkValidity() === false) {
						event.preventDefault();
						event.stopPropagation();
					}
					form.classList.add('was-validated');
				}, false);
			});
		}, false);
	})();

</script>