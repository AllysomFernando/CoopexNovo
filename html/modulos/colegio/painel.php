<?php



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("php/sqlsrv.php");

$id_menu = 93;
$chave	 = "id_big_jump";
$valor_unitario = 20;

$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];



#VERIFICA SE O ALUNO ESTÁ AUTORIZADO
$sql = "SELECT
			* 
		FROM
			colegio.big_jump 
		WHERE
			id_pessoa = " . $_SESSION['coopex']['usuario']['id_pessoa'];

$res = $coopex->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);
$autorizado = $res->rowCount() ? true : false;

#VERIFICA SE O ADQUIRIU A MEIA
$sql = "SELECT
			* 
		FROM
			colegio.big_jump_meia 
		WHERE
			id_pessoa = " . $_SESSION['coopex']['usuario']['id_pessoa'];
$res = $coopex->query($sql);
$row_pedido = $res->fetch(PDO::FETCH_OBJ);
$pedido = $res->rowCount() ? true : false;

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/core.js"></script>

<style>
	.form-control-lg {
		padding: 0.5rem 0.875rem;
	}
</style>

<main id="js-page-content" role="main" class="page-content">

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

	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Colégio FAG</a></li>
		<li class="breadcrumb-item active">Painel</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID.
				<?php echo $id_menu ?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-cubes'></i> Colégio FAG

		</h1>
	</div>

	<div class="row">
		<div class="card mb-g rounded-top col-md-3 mx-2">
			<div class="row no-gutters row-grid">
				<div class="col-12">
					<div class="d-flex flex-column align-items-center justify-content-center p-4">
						<img src="modulos/colegio/assets/images/big_jump.png" class="img-thumbnail border-0" alt="">
						<h5 class="mb-0 fw-700 text-center mt-3">
							Big Jump
							<small class="text-muted mb-0">Termo de Autorização</small>
						</h5>
					</div>
				</div>
				<div class="col-12">
					<div class="text-center py-3">
						<a href="/colegio/big_jump/termo" class="btn btn-primary">Acessar</a>
					</div>
				</div>

			</div>
		</div>


		<div class="card mb-g rounded-top col-md-3 mx-2">
			<div class="row no-gutters row-grid">
				<div class="col-12">
					<div class="d-flex flex-column align-items-center justify-content-center p-4">
						<img src="modulos/colegio/assets/images/sports_school.png" class="img-thumbnail border-0" alt="">
						<h5 class="mb-0 fw-700 text-center mt-3">
							Sports School
							<small class="text-muted mb-0">Pré-matrícula</small>
						</h5>
					</div>
				</div>
				<div class="col-12">
					<div class="text-center py-3">
						<a href="/colegio/sports/termo" class="btn btn-primary">Acessar</a>
					</div>
				</div>

			</div>
		</div>

		<div class="card mb-g rounded-top col-md-3 mx-2">
			<div class="row no-gutters row-grid">
				<div class="col-12">
					<div class="d-flex flex-column align-items-center justify-content-center p-4">
						<img src="modulos/colegio/assets/images/clube_da_tarefa.jpg" class="img-thumbnail border-0" alt="">
						<h5 class="mb-0 fw-700 text-center mt-3">
						Clube da Tarefa
							<small class="text-muted mb-0">Matrícula</small>
						</h5>
					</div>
				</div>
				<div class="col-12">
					<div class="text-center py-3">
						<a target="_blank" href="https://coopex.fag.edu.br/colegio/clube_da_tarefa/matricula" class="btn btn-primary">Acessar</a>
					</div>
				</div>

			</div>
		</div>

		<!-- <div class="card mb-g rounded-top col-md-3 mx-2">
			<div class="row no-gutters row-grid">
				<div class="col-12">
					<div class="d-flex flex-column align-items-center justify-content-center p-4">
						<img src="modulos/colegio/assets/images/acamp.png" class="img-thumbnail border-0" alt="">
						<h5 class="mb-0 fw-700 text-center mt-3">
						Acamp FAG
							<small class="text-muted mb-0">Inscrição</small>
						</h5>
					</div>
				</div>
				<div class="col-12">
					<div class="text-center py-3">
						<a target="_blank" href="https://www2.fag.edu.br/coopex/inscricao/nap/5210" class="btn btn-primary">Acessar</a>
					</div>
				</div>

			</div>
		</div> -->


		
	</div>



</main>

<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>

<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/datagrid/datatables/dataTables.editor.min.js"></script>
<script src="js/moment-with-locales.js"></script>
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<script>
	//MENSAGEM DE CADASTRO OK
	function prematriculaOK(operacao) {
		var msg = "Autorização realizada sucesso";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				location.reload();
			}
		});
	}

	//MENSAGEM DE FALHA NO CADASTRO
	function prematriculaFalha(operacao) {
		var msg = "Não foi possível realizar a Pré-matrícula";
		Swal.fire({
			type: "error",
			title: msg,
			showConfirmButton: false,
			timer: 1500
		});
	}

	function alterar_quantidade() {
		$("#valor_total").val("R$ " + <?= $valor_unitario ?> * $("#quantidade").val() + ",00");
	}

	//MENSAGEM DE CADASTRO OK
	function compraOK(operacao) {
		var msg = "Pré-matrícula realizada sucesso";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				location.reload();
			}
		});
	}

	//MENSAGEM DE FALHA NO CADASTRO
	function matriculaFalha(operacao) {
		var msg = "Não foi possível realizar a Pré-matrícula";
		Swal.fire({
			type: "error",
			title: msg,
			showConfirmButton: false,
			timer: 1500
		});
	}

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


	$(document).ready(function() {
		$("#termo_de_aceite").change(function() {

			if ($("#termo_de_aceite").prop('checked')) {
				$("#botao_pre_matricula").attr("disabled", false);
				$("#responsavel").attr("disabled", false);
				$("#cpf").attr("disabled", false);
				$("#botao_pre_matricula span").removeClass("fa-lock");
				$("#botao_pre_matricula span").addClass("fa-check");

			} else {
				$("#botao_pre_matricula").attr("disabled", true);
				$("#responsavel").attr("disabled", true);
				$("#cpf").attr("disabled", true);
				$("#botao_pre_matricula span").removeClass("fa-check");
				$("#botao_pre_matricula span").addClass("fa-lock");
			}
		});

	});
</script>