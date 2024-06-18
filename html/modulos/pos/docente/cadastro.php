<?php
require_once("php/sqlsrv.php");
require_once("ajax/DocenteController.php");

$id_menu = 109;
$chave = "id_docente";

$docente_controller = new DocenteController($coopex);
$isAdmin = $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == "1";

if (isset($_GET['id'])) {
	$$chave = $_GET['id'];

	$sql = "SELECT
					*
				FROM
					pos.docente
				WHERE
					id_docente = " . $_GET['id'];
	$res = $coopex->query($sql);
	$dados = $res->fetch(PDO::FETCH_OBJ);
} else {
	$$chave = 0;
}

//print_r($dados);

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/summernote/summernote.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/cropperjs/cropper.css">
<link rel="stylesheet" media="screen, print" href="css/fa-solid.css">

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

	<?php
	if (!isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][1])) {
	?>
		<div class="alert border-danger bg-transparent text-secondary fade show" role="alert">
			<div class="d-flex align-items-center">
				<div class="alert-icon">
					<span class="icon-stack icon-stack-md">
						<i class="base-7 icon-stack-3x color-danger-900"></i>
						<i class="fal fa-times icon-stack-1x text-white"></i>
					</span>
				</div>
				<div class="flex-1">
					<span class="h5 color-danger-900">Seu usuário não possui permissão para acessar esta tela</span>
				</div>
				<a href="javascript:solicitarPermissao()" class="btn btn-outline-danger btn-sm btn-w-m">Solicitar acesso</a>
			</div>
		</div>
	<?php
		exit;
	}
	?>

	<iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 30%; background-color: #fff; top: 0; left: 0; height: 270px"></iframe>

	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/pos/docente/ajax/cadastro_dados.php" enctype="multipart/form-data">
		<input type="hidden" name="<?php echo $chave ?>" value="<?php echo $$chave ? $$chave : 0 ?>">

		<div class="row">
			<div class="col-xl-12">
				<div id="panel-2" class="panel">
					<div class="panel-hdr">
						<h2>
							1. Cadastrar Docente
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content p-0">
							<div class="panel-content">

								<div class="form-row mb-3">
									<div class="col-xl mb-3">
										<label class="form-label" for="docente_nome">Nome<span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="docente_nome" id="docente_nome" placeholder="Nome do docente" value="<?php echo $dados->nome ? $dados->nome : '' ?>" required>
										<div class="invalid-feedback">
											Este campo não pode estar vazio
										</div>
									</div>
									<div class="col-xl mb-3">
										<label class="form-label" for="docente_descricao">Descrição <span class="text-danger">*</span></label>
										<input value="<?php echo $dados->descricao ? $dados->descricao : '' ?>" maxlength="200" type="text" class="form-control" name="docente_descricao" id="docente_descricao" placeholder="Escreva uma descrição do docente (até 200 caracteres)" value="" required>
									</div>
									<div class="col-xl mb-3">
										<label class="form-label" for="docente_ies">IES <span class="text-danger">*</span></label>
										<input value="<?php echo $dados->ies ? $dados->ies : '' ?>" maxlength="200" type="text" class="form-control" name="docente_ies" id="docente_ies" placeholder="A instituição de ensino em que o docente se formou" value="" required>
									</div>
								</div>

								<div class="form-row mb-3">
									<div class="col-md-6">
										<label class="form-label" for="docente_cidade">Cidade <span class="text-danger">*</span></label>
										<input maxlength="200" type="text" class="form-control" name="docente_cidade" id="docente_cidade" placeholder="Cidade onde mora o docente" value="" required>
									</div>
									<div class="col-md-6">
										<label class="form-label" for="nacionalidade">Nacionalidade <span class="text-danger">*</span></label>
										<select id="lista_docentes" name="nacionalidade" class="form-control" required data-container="body">
											<option value="BR" <?php echo isset($dados->nacionalidade) && $dados->nacionalidade == "BR" ? "selected" : "" ?>>BR - Brasil</option>
											<option value="US" <?php echo isset($dados->nacionalidade) && $dados->nacionalidade == "US" ? "selected" : "" ?>>US - Estados Unidos</option>
											<option value="CA" <?php echo isset($dados->nacionalidade) && $dados->nacionalidade == "CA" ? "selected" : "" ?>>CA - Canadá</option>
											<option value="PT" <?php echo isset($dados->nacionalidade) && $dados->nacionalidade == "PT" ? "selected" : "" ?>>PT - Portugal</option>
											<option value="UK" <?php echo isset($dados->nacionalidade) && $dados->nacionalidade == "UK" ? "selected" : "" ?>>UK - Reino Unido</option>
										</select>
									</div>
								</div>

								<div class="form-row mb-3">
									<div class="col-xl form-row d-flex align-items-end justify-content-center flex-nowrap">
										<div class="col-xl mb-3">
											<label class="form-label" for="docente_cpf">CPF <span class="text-danger">*</span></label>
											<input type="text" class="form-control cpf" name="docente_cpf" id="docente_cpf" placeholder="Cpf do docente" required>
											<div class="invalid-feedback">
												Este campo não pode estar vazio
											</div>
										</div>
										<div class="col-xl mb-3">
											<label class="form-label" for="docente_titulacao">Titulação <span class="text-danger">*</span></label>
											<select id="docente_titulacao" name="docente_titulacao" class="select2 form-control" required>
												<option value="">Selecione a titulação do docente</option>
												<?php foreach ($titulacoes as $titulacao) { ?>
													<option <?php echo isset($dados) && $titulacao->isSelected($dados->coordenador->titulacao) ? "selected" : "" ?> value="<?php echo $titulacao->id_titulacao ?>">
														<?php echo $titulacao->titulacao ?>
													</option>
												<?php
												}
												?>
											</select>
											<div class="invalid-feedback">
												Selecione a área do curso
											</div>
										</div>
									</div>
								</div>

								<div class="form-row mb-3">
									<div class="col-xl mb-3">
										<label class="form-label">Foto de perfil <span class="text-danger">*</span></label>
										<div class="">
											<input type="file" class="form-control upload_file_field" id="docente_foto" accept=".jpg,.jpeg,.png" name="docente_foto" required>
										</div>
									</div>
									<div class="col-xl mb-3">
										<label class="form-label">Certificado <span class="text-danger">*</span></label>
										<div class="custom-file">
											<input type="file" class="form-control upload_file_field" id="docente_certificado" accept=".pdf" name="docente_certificado" required>
											<span class="help-block">
												Anexe uma certificação do docente
											</span>
										</div>
									</div>
									<div class="col-xl mb-3">
										<label class="form-label">Termo de aceite <span class="text-danger">*</span></label>
										<div class="custom-file">
											<input type="file" class="form-control upload_file_field" id="docente_aceite" accept=".pdf" name="docente_aceite" required>
											<span class="help-block">
												Anexe o termo de aceite assinado pelo docente
											</span>
										</div>
									</div>
									<div class="col-xl mb-3">
										<label class="form-label">Termo de autorização do uso de imagem <span class="text-danger">*</span></label>
										<div class="custom-file">
											<input type="file" class="form-control" id="docente_uso_imagem" accept=".pdf" name="docente_uso_imagem" required>
											<span class="help-block">
												Anexe o termo de autorização do uso de imagem assinado pelo docente
											</span>
										</div>
									</div>
								</div>

								<div class="form-row mb-3">
									<div class="col-xl">
										<label class="form-label" for="docente_curriculo">Currículo</label>
										<textarea class="js-summernote" id="docente_curriculo" name="docente_curriculo"></textarea>
									</div>
								</div>

								<div class="form-row">
									<div class="col-md-12 mb-3 d-flex justify-content-end">
										<button class="btn btn-primary ml-3" type="submit">
											Cadastrar Novo Docente
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</form>


</main>

<script src="js/core.js"></script>
<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/datagrid/datatables/dataTables.editor.min.js"></script>
<script src="js/moment-with-locales.js"></script>
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<script src="js/formplugins/summernote/summernote.js"></script>

<script>
	//MENSAGEM DE CADASTRO OK
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

				//document.location.reload(true)
			}
		});
	}

	//MENSAGEM DE FALHA NO CADASTRO
	function cadastroFalha(operacao) {
		var msg = operacao == 1 ? "Falha ao cadastrar dados" : "Falha ao alterar dados";
		Swal.fire({
			type: "error",
			title: msg,
			showConfirmButton: false,
			timer: 1500
		});
	}

	const uploadField = document.querySelector(".upload_file_field");

	const fileSizeInMB = 3 * 1048576;

	uploadField.onchange = function() {
		if (this.files[0].size > fileSizeInMB) {
			alert("Arquivo muito grande (máximo 3MB)");
			this.value = "";
		}
	}

	$(document).ready(function() {

		$(":input").inputmask();
		$('.select2').select2();

		$('.js-summernote').summernote({
			height: 400,
			tabsize: 2,
			dialogsFade: true,
			toolbar: [
				['font', ['strikethrough', 'superscript', 'subscript']],
				['font', ['bold', 'italic', 'underline', 'clear']],
				['para', ['ul', 'ol']]
			],
			cleaner: {
				action: 'paste',
				keepHtml: false,
				keepClasses: false,
				limitChars: false,
				limitDisplay: 'both',
				limitStop: false
			}
		});

		const checkboxImagemProfessor = document.querySelector("#toggle_imagem_professor");

		checkboxImagemProfessor?.addEventListener("click", (e) => {
			const checked = e.target;
			const inputImage = document.querySelector('#inputImage');

			if (checked.hasAttribute('checked')) {
				inputImage.style.display = 'none';
				checked.removeAttribute('checked');
			} else {
				checked.setAttribute('checked', 'checked');
				inputImage.style.display = 'block';

			}
		})

		/* start data table */
	});

	// Example starter JavaScript for disabling form submissions if there are invalid fields
	(function() {
		'use strict';
		window.addEventListener('load', function() {
			// Fetch all the forms we want to apply custom Bootstrap validation styles to
			var forms = document.getElementsByClassName('needs-validation');
			// Loop over them and prevent submission
			var validation = Array.prototype.filter.call(forms, function(form) {
				form.addEventListener('submit', function(event) {
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