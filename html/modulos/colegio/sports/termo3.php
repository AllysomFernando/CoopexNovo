<?php



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("php/sqlsrv.php");

$id_menu = 93;
$chave	 = "id_sports";
$valor_unitario = 20;

$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];



#VERIFICA SE O ALUNO ESTÁ AUTORIZADO
$sql = "SELECT
			* 
		FROM
			colegio.sports 
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
		<li class="breadcrumb-item active">Termo de Autorização</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID.
				<?php echo $id_menu ?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-volleyball-ball'></i> Sports School
			<small>
				Termo de Ciência de Pré-matrícula
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
					<span class="h5">TERMO DE CIÊNCIA</span><br>
					Declaro, para os devidos fins. Estar ciente e concordar com as disposições elencadas abaixo, as
					quais são correlatadas à realização de pré-matrícula para a Escola de Esportes Colégio FAG:<br><br>
					<ol>
						<li>Para a realização da pré-matrícula será cobrada uma taxa no valor de R$120,00 (cento e vinte
							reais), a qual incluirá um kit de 2 (duas) camisetas.</li>
						<li>Em caso de desistência, não haverá a devolução do valor pago, salvo na hipótese de o esporte
							escolhido não atingir o número mínimo de inscritos, podendo o (a) responsável legal, se
							assim desejar, optar por outra modalidade de esporte com disponibilidade de vagas, ou pela
							devolução integral da taxa de pré-matrícula. </li>
						<li>A pré-matrícula garante a vaga. Todavia, fica condicionada à abertura de turma e também na finalização do procedimento de matrícula, com atendimento de todos os
							prazos e solicitações pelo COLÉGIO, bem como pela apresentação de atestado médico de aptidão
							física para a realização do esporte desejado.</li>
						<li>Poderá ocorrer, a critério do COLÉGIO, alteração dos horários e/ou cronograma
							disponibilizado, bem como outras medidas que se tornem necessárias por razões de ordem
							pedagógica e/ou administrativa.</li>
						<li>Os prazos, documentos e demais procedimentos relativos à matrícula serão indicados
							posteriormente pelo COLÉGIO, através do WhatsApp.</li>



						<hr>
					</ol>
					<h3>MODALIDADES</h3>
					<div class="row mt-4">
						<div class="col-3 border p-4">
							<h5>Modalidades Educação Infantil</h5>
							<ul>
								<li>Futsal</li>
								<li>Capoeira</li>
								<li>Handebol</li>
								<li>Hip Hop</li>
								<li>Ginástica Rítimica</li>
								<li>Futebol de Campo</li>
								<li>Xadrez</li>
							</ul>
						</div>

						<div class="col-3 border p-4">
							<h5>Modalidades Ensino Fundamental</h5>
							<ul>
								<li>Futsal</li>
								<li>Basquete</li>
								<li>Handebol</li>
								<li>Voleibol</li>
								<li>Hip Hop</li>
								<li>Ginástica Rítimica</li>
								<li>Futebol de Campo</li>
								<li>Xadrez</li>
							</ul>
						</div>

						<div class="col-3 border p-4">
							<h5>Modalidades Ensino Médio</h5>
							<ul>
								<li>Futsal</li>
								<li>Basquete</li>
								<li>Handebol</li>
								<li>Voleibol</li>
								<li>Hip Hop</li>
								<li>Futebol de Campo</li>
								<li>Xadrez</li>
							</ul>
						</div>
					</div>

					<h3 class="mt-5">CRONOGRAMA</h3>
				

					<!-- <h4 class="mt-5">Alunos do Matutino</h4>
					<li>5º ano à 3ª série do Ensino Médio: segundas e sextas-feiras, das 13h30 às 17h;</li>
					<li>Infantil 4 ao 4º ano: terças e quintas-feiras, das 13h30 às 17h;</li>

					<h4 class="mt-5">Alunos do Vespertino</h4>
					<li>1º ano ao 7º ano: de segunda a sexta-feira, das 17h30 às 19h;</li>
					<li>Infantil 4 e 5: uma vez por semana, das 17h30 às 19h, e uma vez por semana, no período matutino, com horário a definir.;</li> -->

					<table class="table mt-5">
						<thead>
							<tr>
								<th>Turma</th>
								<th>Período</th>
								<th>Dias</th>
								<th>Horário</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>5º ano à 3ª série do Ensino Médio</td>
								<td>Matutino</td>
								<td>Segundas e sextas-feiras</td>
								<td>13h30 às 17h</td>
							</tr>
							<tr>
								<td>Infantil 4 ao 4º ano</td>
								<td>Matutino</td>
								<td>Terças e quintas-feiras</td>
								<td>13h30 às 17h</td>
							</tr>
							<tr>
								<td>1º ano ao 7º ano</td>
								<td>Vespertino</td>
								<td>Segunda a sexta-feira</td>
								<td>17h30 às 19h</td>
							</tr>
							<tr>
								<td>Infantil 4 e 5</td>
								<td>Vespertino</td>
								<td>Segunda a sexta-feira</td>
								<td>17h30 às 19h</td>
							</tr>
							<tr>
								<td>Infantil 4 e 5</td>
								<td>Matutino</td>
								<td>1 vez por semana</td>
								<td>Horário a definir</td>
							</tr>
						</tbody>
					</table>
					<!-- <li>
						<h4><a target="_blank" href="https://www.fag.edu.br/novo/arquivos/cronograma_sports.pdf"><strong>VEJA O CRONOGRAMA COMPLETO</strong></a></h4>
					</li> -->
				

					<h3 class="mt-5">VALORES</h3>
					<ul>
						<li>Uma modalidade: R$ 100,00 mensais (Fevereiro a Novembro)</li>
						<li>Duas ou mais modalidades: R$ 85,00 mensais cada modalidade (Fevereiro a Novembro).</li>
					</ul>

					<h4 class="mt-5">Modalidade Futebol de Campo</h4>
					<ul>
						<li>R$ 120,00 mensais (Fevereiro a Novembro), não tem desconto de segunda modalidade.</li>
						<li>Público externo: R$ 240,00 mensais (A participação de público externo fica condicionada à indicação de um aluno do Colégio FAG.).</li>
					</ul>
					<hr>

					<div class="custom-control custom-checkbox mt-4">
						<input <?php echo $autorizado ? 'checked="" disabled' : '' ?> type="checkbox" class="custom-control-input" id="termo_de_aceite" value="1" name="termo_de_aceite">
						<label class="custom-control-label" for="termo_de_aceite">Li e concordo com os termos de
							ciência</label>
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
						1. Pré-matrícula
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
											regras para atividade denominada "SPORTS SCHOOL",
											<strong>AUTORIZANDO</strong> o menor abaixo qualificado a praticar tal
											atividade, me responsabilizando pelo mesmo. Para habilitar a pré-matrícula é
											necessário clicam em <strong>"Li e concordo com os termos de
												ciência"</strong> na seção acima.
										</div>
									<?php
									} else {
									?>
										<div class="panel-tag">
											Autorização realizada em:
											<strong><?php echo converterDataHora($row->data_cadastro); ?></strong><br>
										</div>
									<?php
									}
									?>
								</div>
								
								<form class="col-12 p-2" method="post" target="dados" action="modulos/colegio/sports/pre_matricular.php">
									<div class="row">
										<div class="col-xl-5 mb-3">
											<label class="form-label" for="responsavel">Nome do Responsável</label>
											<input style="text-transform: uppercase" required 
												type="text" id="responsavel"
												name="responsavel" class="form-control form-control-lg required" disabled
												value="<?= isset($row->responsavel) ? $row->responsavel : "" ?>"  />
										</div>
										<div class="col-xl-3 mb-3">
											<label class="form-label" for="cpf">CPF do Responsável</label>
											<input required  data-inputmask="'mask': '999.999.999-99'"  type="text" id="cpf" name="cpf" class="form-control form-control-lg" value="<?= isset($row->cpf) ? $row->cpf : "" ?>" disabled />
										</div>

									</div>
									<h3 class="mt-4">CAMISETA</h3>
									<div class="row">
										<div class="col-xl-3 mb-4">
											<label class="form-label">Valor do Kit</label>
											<input required  type="text" class="form-control form-control-lg" value="R$ 120,00" disabled />
										</div>

										<div class="col-xl-2 mb-3">
											<label class="form-label" for="responsavel">Tamanho</label>
											<?
											$sql2 = "SELECT
															* 
														FROM
															colegio.camiseta_tamanho";

											$res2 = $coopex->query($sql2);
											?>
											<select required id="id_camiseta_tamanho" name="id_camiseta_tamanho" class="form-control form-control-lg required" <?= $autorizado ? "" : "disabled" ?>  disabled />
												<option value="">Selecione</option>
												<?
												while ($row2 = $res2->fetch(PDO::FETCH_OBJ)) {
												?>
													<option <?= isset($row->id_camiseta_tamanho) && $row2->id_camiseta_tamanho == $row->id_camiseta_tamanho ? "selected" : "" ?> value="<?= $row2->id_camiseta_tamanho ?>"><?= $row2->tamanho ?></option>
												<?
												}
												?>
											</select>
										</div>

										<div class="col-xl-3 mb-3">
											<label class="form-label" for="nome_camiseta">Nome estampado na camiseta</label>
											<input required style="text-transform: uppercase" type="text" id="nome_camiseta" name="nome_camiseta" class="form-control form-control-lg required" value="<?= isset($row->nome_camiseta) ? $row->nome_camiseta : "" ?>" disabled />
										</div>
									</div>	
									<div class="row">
										<img class="col-xl-3 col-md-6" src="modulos/colegio/sports/images/testes.jpg">	
										<img class="col-xl-3 col-md-6" src="modulos/colegio/sports/images/branca.jpg">
										
									</div>
									<div>*Imagens ilustrativas</div>
									
								
									<div class="row">
										<div class="col-4 mb-3">
											<label class="form-label" for="autorizar">&nbsp</label>
											<br>
											<?php if (!$autorizado) { ?>
												<button id="botao_pre_matricula" type="submit" class="btn btn-lg btn-primary waves-effect waves-themed" disabled>
													<span class="fal fa-lock mr-1"></span>
													Pré-matricular
												</button>
											<?php } else { ?>
												<div>
													<label class="form-label">&nbsp</label><br>
													<a target="_blank" href="https://coopex.fag.edu.br/boleto/sports/<?= $id_pessoa ?>" class="btn btn-lg btn-primary btn-lg waves-effect waves-themed" <?= $autorizado ? "" : "disabled" ?>>
														<span class="fal fa-<?= $autorizado ? "check" : "lock" ?> mr-1"></span>Gerar
														o Boleto de Pagamento
													</a>
												</div>
											<?
											}
											?>
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
		var msg = "Não foi possível realizar Autorização";
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
		var msg = "Pré-matricula realizada com sucesso";
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
		var msg = "Não foi possível realizar a Pré-matricula";
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

		$(":input").inputmask();

		$("#termo_de_aceite").change(function() {

			if ($("#termo_de_aceite").prop('checked')) {
				$("#botao_pre_matricula").attr("disabled", false);
				$("#responsavel").attr("disabled", false);
				$("#cpf").attr("disabled", false);

				$("#id_camiseta_tamanho").attr("disabled", false);
				$("#nome_camiseta").attr("disabled", false);
				$("#cpf").attr("disabled", false);

				$("#botao_pre_matricula span").removeClass("fa-lock");
				$("#botao_pre_matricula span").addClass("fa-check");

			} else {
				$("#botao_pre_matricula").attr("disabled", true);
				$("#responsavel").attr("disabled", true);
				$("#cpf").attr("disabled", true);

				$("#id_camiseta_tamanho").attr("disabled", true);
				$("#nome_camiseta").attr("disabled", true);

				$("#botao_pre_matricula span").removeClass("fa-check");
				$("#botao_pre_matricula span").addClass("fa-lock");
			}
		});

	});
</script>