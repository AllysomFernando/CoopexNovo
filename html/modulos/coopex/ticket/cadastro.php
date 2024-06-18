<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("api/repository/TicketRepository.php");
require_once("api/controllers/TicketController.php");

$id_menu = 119;

$repository = new TicketRepository($coopex);

$tickets = $repository->getAll();

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css?<?php echo time() ?>">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/cropperjs/cropper.css">
<link rel="stylesheet" media="screen, print" href="css/fa-solid.css">
<script src="js/core.js?<?php echo time() ?>"></script>

<main id="js-page-content" role="main" class="page-content">

	<!-- <?php
	if (!isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][1])) {
	?>
		<div class="alert border-danger bg-transparent text-secondary fade show" role="alert">
			<div class="d-flex align-items-center">
				<div class="alert-icon">
					<span class="icon-stack icon-stack-md">
						<i class="base-7 icon-stack-3x color-danger-900"></i>
						<i class="fal fa-ticket-alt"></i>
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
	?> -->

	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="https://coopex.fag.edu.br/coopex/ticket/consulta">Ticket</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID.
				<?php echo $id_menu ?>c
			</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-ticket-alt'></i> Ticket
			<small>
				Enviar um novo ticket
			</small>
		</h1>
	</div>

	<iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 30%; background-color: #fff; top: 0; left: 0; height: 270px"></iframe>

	<form method="post" action="modulos/coopex/ticket/api/routes/ticket.php" id="main-form" novalidate class="needs-validation" target="dados">
		<input type="text" class="form-control" name="id_usuario" placeholder="Usuário" id="id_usuario" value="<?php echo $_SESSION['coopex']['usuario']['id_pessoa'] ?>" hidden required>
		<div class="row">
			<div class="col-xl-12">
				<div id="panel-1" class="panel">
					<div class="panel-hdr">
						<h2>
							1. Detalhes
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content">
							<div class="form-group">
								<label class="form-label" for="titulo">Título<span class="text-danger">
										*</span></label>
								<input type="text" class="form-control" name="titulo" placeholder="Título do ticket" id="titulo" required>
								<div class="invalid-feedback">O título do ticket é obrigatório.</div>
							</div>

							<div class="form-group">
								<label class="form-label" for="url">URL</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text" id="basic-addon3">
											<i class='fal fa-link'></i>
										</span>
									</div>
									<input type="text" class="form-control" name="url" placeholder="Url da página que ocorreu o problema" id="url">
								</div>
								<span class="help-block">Esse problema aconteceu em alguma página? Informe aqui a url</span>
							</div>


							<div class="form-group">
								<label class="form-label" for="descricao">Descrição do problema<span class="text-danger">*</span></label>
								<textarea class="form-control" id="descricao" name="descricao" placeholder="Descreva seu problema com a maior quantidade de detalhes possíveis" rows="6" required></textarea>
								<div class="invalid-feedback">A descrição do ticket é obrigatória.</div>
							</div>
						</div>
						<div class="panel-content">
							<div class="col-md-12 d-flex justify-content-end">
								<button class="btn btn-primary" type="submit">
									Enviar ticket
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>

</main>

<script src="js/formplugins/select2/select2.bundle.js?<?php echo time() ?>"></script>
<script src="js/moment-with-locales.js?<?php echo time() ?>"></script>
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js?<?php echo time() ?>"></script>

<script>
	function cadastroOK(operacao) {
		var msg = "Ticket cadastrado com sucesso";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				window.history.back();
			}
		});
	}

	function cadastroFalha(operacao) {
		var msg = operacao == 1 ? "Falha ao cadastrar dados" : "Falha ao alterar dados";
		Swal.fire({
			type: "error",
			title: msg,
			showConfirmButton: false,
			timer: 1500
		});
	}


	$(document).ready(async function() {

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
	})
</script>

<script>
	$("#main-form").submit(async function(e) {

		e.preventDefault();

		var form = $(this);

		if ($.trim($("#titulo").val()) === "" || $.trim($("#descricao").val()) === "") {
        alert('Alguns campos obrigatórios estão vazios');
        return false;
    }

		$.ajax({
			type: "POST",
			url: "modulos/coopex/ticket/api/routes/ticket.php",
			data: form.serialize(),
			success: function(data) {
				cadastroOK(1)
			},
			error: function() {
				cadastroFalha(1)
			}
		});
	});
</script>