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
		<li class="breadcrumb-item"><a href="javascript:void(0);">Sports School</a></li>
		<li class="breadcrumb-item active">Inscrição</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID.
				<?php echo $id_menu ?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-repeat'></i> Sports School
			<small>
				Inscrição
			</small>
		</h1>
	</div>

	<img src="https://www.colegiofag.com.br/assets/images/banners/banner-desktop-esportes-colegio.jpg">


	<div class="alert alert-primary">
		<div class="d-flex flex-start w-100">
			<div class="mr-2 hidden-md-down">
				<span class="icon-stack icon-stack-lg">
					<i class="base base-2 icon-stack-3x opacity-100 color-primary-500"></i>
					<i class="base base-2 icon-stack-2x opacity-100 color-primary-300"></i>
					<i class="fal fa-info icon-stack-1x opacity-100 color-white"></i>
				</span>
			</div>
			<div class="d-flex flex-fill">
				<div class="flex-fill">
					<span class="h5">TERMO DE AUTORIZAÇÃO</span><br>
					Declaro que estou ciente:<br><br>
					<h3>NORMAS – COLÉGIO FAG</h3>
					<ol>
						<li>Antes de pular, todos os usuários precisam estar com o termo de autorização oficial do Big
							Jump Colégio FAG, assinado pelos pais e/ou responsáveis;</li>
						<li>Ao pular, respeite seus limites;</li>
						<li>Não é permitido tocar, escalar, apoiar-se ou pendurar-se nas proteções de mola ou redes
							dentro da área de trampolins, nem sentar ou deitar nos trampolins;</li>
						<li>Sempre que pular, aterrisse nos dois pés. Nunca pule ou aterrisse nas proteções e nunca
							aterrisse de cabeça;</li>
						<li>Não é permitido dar cambalhotas;</li>
						<li>Não é permitido passar por cima da proteção para outro trampolim;</li>
						<li>Mantenha o controle do seu corpo a todo o momento;</li>
						<li>Sempre obedeça às regras e orientações dos monitores e professores do Colégio.</li>


						<hr>
					</ol>
					<h3>EQUIPAMENTOS, ACESSÓRIOS E OBJETOS</h3>
					<ol>
						<li>Não são permitidos o uso de cintos com fivelas, joias, celular, chaveiros ou objetos
							pontiagudos durante o uso dos trampolins, bem como objetos nos bolsos enquanto estiver
							pulando;</li>
						<li>Comidas e bebidas não são permitidas nos trampolins;</li>
						<li>Não é permitido mascar chicletes, balas, etc., enquanto estiver pulando;</li>
						<li>É Obrigatório o uso de meias apropriadas para o brinquedo. </li>
					</ol>

					<div class="custom-control custom-checkbox">
						<input <?php echo $autorizado ? 'checked="" disabled' : '' ?> type="checkbox" class="custom-control-input" id="termo_de_aceite" value="1" name="termo_de_aceite">
						<label class="custom-control-label" for="termo_de_aceite">Li e concordo com os termos de
							autorização</label>
					</div>

					<br>
				</div>
			</div>
		</div>
	</div>


	<iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 100%; background-color: #fff; top: 0; left: 0; height: 300px"></iframe>

	<div class="row">
		<div class="col-xl-12">
			<div id="panel-2" class="panel">
				<div class="panel-hdr">
					<h2>
						1. Inscrição
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content p-0">
						<div class="panel-content">
							<div class="form-row form-group">
								<div class="col-md-12 mb-3">
									<?php
									if (!$autorizado) {
									?>
										<div class="panel-tag">
											Na condição de responsável legal pelo(a) aluno(a):
											<strong><?= $_SESSION['coopex']['usuario']['nome'] ?></strong>, expressamente
											<strong>DECLARO</strong> estar ciente de todas as informações/orientações e
											regras para a correta prática da atividade denominada "SPORTS SCHOOL",
											<strong>AUTORIZANDO</strong> o menor abaixo qualificado a praticar tal
											atividade, me responsabilizando pelo mesmo.
										</div>
									<?php
									} else {
									?>
										<div class="panel-tag">
											Autorização realizada em:
											<strong><?php echo $row->data_cadastro; ?></strong><br>
										</div>
									<?php
									}
									?>
								</div>

								<form class="col-12 p-2" method="post" target="dados" action="modulos/colegio/big_jump/autorizar.php">
									
									<div class="row">
										
										<div class="col-2 mb-3">
											<label class="form-label" for="responsavel2">Valor da inscrição</label>
											<input require type="text" id="responsavel2" name="responsavel" class="form-control form-control-lg required" value="120,00" disabled>
										</div>
										<div class="col-4 mb-3">
											<label class="form-label" for="cpf">Nome das Camisas</label>
											<input require type="text" id="cpf" name="cpf" class="form-control form-control-lg required" value="" disabled>
										</div>
										<div class="col-2 mb-3">
											<label class="form-label" for="responsavel">Tamanho das Camisas</label>
											<select require type="text" id="responsavel" name="responsavel" class="form-control form-control-lg required" value="120,00" disabled>
												<option>PP</option>
												<option>P</option>
												<option>M</option>
												<option>G</option>
												<option>GG</option>
												<option>-------------</option>
												<option>2</option>
												<option>4</option>
												<option>6</option>
												<option>8</option>
												<option>10</option>
												<option>12</option>
												<option>14</option>
												<option>16</option>
												<option>18</option>
											</select>
										</div>
										<div class="col-3 mb-3">
											<label class="form-label" for="autorizar">&nbsp</label>
											<br>
											<?php if (!$autorizado) : ?>
												<button id="botao_pre_matricula" type="submit" class="btn btn-lg btn-primary waves-effect waves-themed" disabled>
													<span class="fal fa-lock mr-1"></span>
													Inscrever
												</button>
											<?php endif; ?>
										</div>
									</div>
								</form>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row" style="display: none;">
		<div class="col-xl-12">
			<div id="panel-2" class="panel">
				<div class="panel-hdr">
					<h2>
						2. Meias
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content p-0">
						<div class="panel-content">

							<div class="panel-tag">
								O valor do par de meias apropriadas para o Big Jump é de <strong>R$ 20,00</strong>.<br>
								<?
								if (!$autorizado) {
								?>
									Para que seja possível adquirir as meias é necessário aceitar o <strong>TERMO DE
										AUTORIZAÇÃO</strong> na seção acima.
								<?
								}
								?>
							</div>
							<div class="form-row form-group">
								<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/colegio/big_jump/comprar.php">

									<div class="row">
										<div class="col-2 mb-3">
											<label class="form-label">Quantidade </label>
											<input type="number" onchange="alterar_quantidade()" name="quantidade" id="quantidade" class="form-control form-control-lg" value="<?= $pedido ? $row_pedido->quantidade : 1 ?>" <?= $autorizado ? "" : "disabled" ?> <?= $pedido ? "disabled" : "" ?>>
										</div>
										<div class="col-3 mb-4">
											<label class="form-label">Valor Unitário </label>
											<input type="text" class="form-control form-control-lg" value="R$ <?= $valor_unitario ?>,00" disabled>
										</div>
										<div class="col-3 mb-4">
											<label class="form-label">Valor Total </label>
											<input id="valor_total" type="text" class="form-control form-control-lg" value="R$ <?= $pedido ? $row_pedido->quantidade * $valor_unitario : $valor_unitario ?>,00" disabled>
										</div>
										<?
										if (!$pedido) {
										?>
											<div class="col-3 mb-3">
												<label class="form-label">&nbsp</label><br>
												<button type="submit" class="btn btn-lg btn-primary btn-lg waves-effect waves-themed" <?= $autorizado ? "" : "disabled" ?>>
													<span class="fal fa-<?= $autorizado ? "check" : "lock" ?> mr-1"></span>Comprar
												</button>
											</div>
										<?
										} else {
										?>
											<div class="col-3 mb-3">
												<label class="form-label">&nbsp</label><br>
												<a href="https://coopex.fag.edu.br/boleto/big_jump/<?= $id_pessoa ?>" class="btn btn-lg btn-primary btn-lg waves-effect waves-themed" <?= $autorizado ? "" : "disabled" ?>>
													<span class="fal fa-<?= $autorizado ? "check" : "lock" ?> mr-1"></span>Gerar o Boleto de Pagamento
												</a>
											</div>
										<?
										}
										?>
									</div>
								</form>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
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