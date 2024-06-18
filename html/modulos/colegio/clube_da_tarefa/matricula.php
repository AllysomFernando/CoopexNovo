<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("php/sqlsrv.php");

$id_menu = 93;
$chave	 = "id_sports";
$valor_unitario = 20;

$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];

$sql3 = "SELECT
			* 
		FROM
			academico..AUE_aluno_unidade_ensino a
			INNER JOIN integracao..view_integracao_usuario b ON a.aue_id_aluno= b.id_pessoa 
		WHERE
			aue_id_responsavel IN ( SELECT aue_id_responsavel FROM academico..AUE_aluno_unidade_ensino WHERE aue_id_aluno = $id_pessoa )";

$res3 = mssql_query($sql3);
$pessoas = Array();
while ($row3 = mssql_fetch_object($res3)) {
	$pessoas[] = $row3->id_pessoa;
}

$pessoas = implode(",",$pessoas);


#VERIFICA SE O ALUNO ESTÁ AUTORIZADO
$sql = "SELECT
			* 
		FROM
			colegio.cdt_aluno_matricula
		WHERE
			id_pessoa in ($pessoas)";

$res = $coopex->query($sql);

if ($res->rowCount()) {
	$row = $res->fetch(PDO::FETCH_OBJ);
	$matricula = true;
} else {
	$matricula = false;
}

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
		<li class="breadcrumb-item"><a href="javascript:void(0);">Clube da Tarefa</a></li>
		<li class="breadcrumb-item active">Matrícula</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID.
				<?php echo $id_menu ?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-pen'></i> Clube da Tarefa
		</h1>
	</div>

	<img src="https://coopex.fag.edu.br/arquivos/colegio/clube_da_tarefa/banner-clube-da-tarefa.jpg">

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
					Declaro, para os devidos fins. Estar ciente e concordar com as disposições elencadas abaixo, as quais são correlatadas à realização de matrícula para O Clube da Tarefa Colégio FAG:<br><br>
					<ol>
						<li>O CLUBE DA TAREFA irá acontecer de segunda a sexta-feira, das 12h15 às 13h15, sessenta minutos de duração, respeitando o calendário escolar do Colégio FAG;</li>
						<li>O custo será de R$ 70,00 mensais por aluno, em caso de dois filhos ou mais matriculados no Clube de Tarefas, o valor será de R$ 50,00 por aluno;</li>
						<li>Estará disponível para os alunos devidamente matriculados no Colégio FAG, a partir do 1° ano do Ensino Fundamental I até o 9° ano do Ensino Fundamental II;</li>
						<li>O aluno poderá participar todos os dias ou quantos dias da semana quiser, sem alteração de valores;</li>
						<li>Em caso de falta, não será possível reposição;</li>
						<li>É de total responsabilidade do aluno / responsável, trazer o material necessário para realização das tarefas proposta;</li>
						<li>Fotos e filmagens das atividades do Clube de tarefas serão utilizadas pelo Colégio, para fins de divulgação;</li>
						<li>Após a efetivação da inscrição no CLUBE DA TAREFA, o valor pago não será reembolsado;</li>
						<li>O Clube da Tarefa é um projeto que tem como objetivo promover a autonomia dos nossos alunos, durante a realização das tarefas escolares. Os alunos serão monitorados e orientados, mas o desenvolvimento e a realização das atividades são de responsabilidade do estudante.</li>
						<li><b>Nesse projeto as atividades do Online Practice e English Hub, não serão realizadas.</b></li>
						<hr>
					</ol>
					

					<!-- <h3 class="mt-5">VALORES</h3>
					<ul>
						<li>Um aluno: R$ 70,00 mensais (Maio a Novembro)</li>						<li>Dois alunos ou mais: R$ 50,00 mensais cada aluno (Maio a Novembro).</li>
					</ul> -->

					<hr>


					<h3><a target="_blank" href="https://coopex.fag.edu.br/arquivos/colegio/clube_da_tarefa/contrato.pdf"><strong>LEIA O CONTRATO DO CLUBE DA TAREFA</strong></a></h3>

					<div class="custom-control custom-checkbox mt-4">
						<input <?php echo $matricula ? 'checked="" disabled' : '' ?> type="checkbox" class="custom-control-input" id="termo_de_aceite" value="1" name="termo_de_aceite">
						<label class="custom-control-label" for="termo_de_aceite">Li e concordo com os termos de ciência</label>
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
						Matrícula
					</h2>
				</div>

				<div class="panel-container show">
					<div class="panel-content p-0">
						<div class="panel-content">
							<div class="form-row form-group">

								<form class="col-12 p-2" method="post" target="dados" action="modulos/colegio/clube_da_tarefa/inscricao_dados_matricula.php">
									<h3 class="mt-0">MATRÍCULA</h3>
									<label class="form-label">De acordo com o constante na Cláusula Quinta do Contrato de Prestação de Serviços Desportivos, o(a) representante legal do(a) aluno(a) poderá, a qualquer momento, rescindir o contrato unilateralmente, ficando obrigado(a) ao pagamento das mensalidades em atraso e a do mês em vigência, bem como da multa rescisória correspondente à 01 mensalidade.</label>
									<table class="table mt-4">
										<tr>
											<th>Aluno</th>
											<!-- <th class="text-center">Vagas</th> -->
											<th class="text-right">Valor</th>
											<th>Matrícula</th>
										</tr>
										<?
										$sql3 = "SELECT
															DISTINCT id_pessoa, nome
														FROM
															academico..AUE_aluno_unidade_ensino a
															INNER JOIN integracao..view_integracao_usuario b ON a.aue_id_aluno= b.id_pessoa 
														WHERE
															aue_id_responsavel IN ( SELECT aue_id_responsavel FROM academico..AUE_aluno_unidade_ensino WHERE aue_id_aluno = $id_pessoa )";

										$res3 = mssql_query($sql3);
										
										while ($row3 = mssql_fetch_object($res3)) {

											$sql2 = "SELECT
														* 
													FROM
														colegio.cdt_aluno_matricula
													where id_pessoa = $row3->id_pessoa";

											$res2 = $coopex->query($sql2);
											$row2 = $res2->fetch(PDO::FETCH_OBJ);

											$arr_pessoa[] = $row3->id_pessoa;
										?>
											<tr>
												<td>
													<?= utf8_encode($row3->nome) ?>
												</td>
												<td align="right">R$ 70,00</td>
												<td>
													<div class="custom-control custom-checkbox">
														<input onclick="carrega_valor()" name="id_pessoa_matricula[]" value="<?= $row3->id_pessoa ?>" type="checkbox" <?= $res2->rowCount() ? "checked" : "" ?> class="custom-control-input check_modalidade" id="id_pessoa_matricula<?= $row3->id_pessoa ?>" disabled>
														<label class="custom-control-label" 
														
														for="id_pessoa_matricula<?= $row3->id_pessoa ?>">Matricular</label>
													</div>
												</td>
											</tr>
										<?
										}



										?>
										<tr style="background-color: #DCDCDC;">
											<td colspan="2" align="right">SUBTOTAL</td>
											<td align=""><strong>R$ <span id="subtotal"><?= number_format($subtotal, 2, ',', '.') ?></span></strong></td>
											<td></td>
										</tr>
										<tr style="background-color: #D3D3D3;">
											<td colspan="2" align="right">DESCONTO PARA MAIS DE UM ALUNO</td>
											<td align=""><strong>R$ <span id="desconto"><?= number_format($desconto, 2, ',', '.') ?></span></strong></td>
											<td></td>
										</tr>
										<tr style="background-color: #C0C0C0;">
											<td colspan="2" align="right">TOTAL MENSAL</td>
											<td align=""><strong>R$ <span id="mensal"><?= number_format($total, 2, ',', '.') ?></span></strong></td>
											<td></td>
										</tr>

									</table>



									<div class="row">
										<div class="col-12 mb-3">
											<label class="form-label" for="autorizar">&nbsp</label>
											<br>
											<?
											if (!$matricula) {

											?>
												<button id="botao_matricula" type="submit" class="btn btn-lg btn-primary waves-effect waves-themed">
													<span class="fal mr-1"></span>
													Finalizar Matrícula
												</button>

											<?
											} else {
											?>
																								

												<table class="table">
													<tr>
														<td>Parcela</td>
														<td>Valor</td>
														<td>Vencimento</td>
														<td>Boleto</td>
													</tr>

													<?
													$id_pessoas = implode(",",$arr_pessoa);

													$sql = "SELECT
																* 
															FROM
																colegio.cdt_matricula_boleto 
															WHERE
																id_pessoa in ($id_pessoas)
															AND
																ativo = 1
															ORDER BY parcela";
													$res = $coopex->query($sql);
													while ($row = $res->fetch(PDO::FETCH_OBJ)) {
													?>
														<tr>
															<td><?= $row->parcela ?></td>
															<td>R$ <?= number_format($row->valor, 2, ',', '.') ?></td>
															<td><?= converterData($row->data_vencimento) ?></td>
															<td>
																<?
																if ($row->pago) {
																?>
																	<button class="btn btn-lg btn-primary waves-effect waves-themed" <?= $row->pago || 1 == 1 ? "disabled" : "" ?>>
																		<span class="fal <?= $row->pago ? "fa-check" : "fa-print" ?> mr-1"></span>
																		<?= $row->pago ? "Pago" : "Gerar Boleto" ?>
																	</button>
																<?
																} else {
																?>
																	<a target="_blank" href="https://coopex.fag.edu.br/boleto/clube_da_tarefa/matricula/<?= $row->id_matricula_boleto ?>" class="btn btn-lg btn-primary waves-effect waves-themed" <?= $row->pago  == 0 ? "disabled" : "" ?>>
																		<span class="fal <?= $row->pago ? "fa-check" : "fa-print" ?> mr-1"></span>
																		<?= $row->pago ? "Pago" : "Gerar Boleto" ?>
																	</a>
																<?
																}
																?>

															</td>
														</tr>
													<?
													}
													?>
												</table>
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
	function matriculaOK(id_matricula) {
		var msg = "Matricula realizada sucesso";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				$.getJSON("php/registro_bradesco/clube_da_tarefa_matricula_parcelamento.php", {
						id: id_matricula
					})
					.done(function(json) {
						location.reload();
					})
					.fail(function(jqxhr, textStatus, error) {
						location.reload();
						/*var err = textStatus + ", " + error;
						console.log("Request Failed: " + err);*/
					});

			}
		});
	}

	//MENSAGEM DE FALHA NO CADASTRO
	function matriculaFalha(operacao) {
		var msg = "Não foi possível realizar Autorização";
		Swal.fire({
			type: "error",
			title: msg,
			showConfirmButton: false,
			timer: 1500
		});
	}

	function carrega_valor() {

		var ids = $('input[name="id_pessoa_matricula[]"]:checked').map(function() {
			return $(this).val();
		}).get();


		if (ids.length) {
			$.getJSON("modulos/colegio/clube_da_tarefa/ajax/carrega_valor.php", {
					id_pessoa_matricula: ids
				})
				.done(function(json) {
					res = json[0];

					$("#subtotal").html(res.subtotal);
					$("#desconto").html(res.desconto);
					$("#mensal").html(res.total);

					$("#botao_matricula").attr("disabled", false);
					$("#botao_matricula span").removeClass("fa-lock");
					$("#botao_matricula span").addClass("fa-check");
				})
				.fail(function(jqxhr, textStatus, error) {

					var err = textStatus + ", " + error;
					console.log("Request Failed: " + err);
				});
		} else {
			$("#subtotal").html("0,00");

			$("#subtotal").html("0,00");
			$("#desconto").html("0,00");
			$("#mensal").html("0,00");

			$("#botao_matricula").attr("disabled", true);
			$("#botao_matricula span").removeClass("fa-check");
			$("#botao_matricula span").addClass("fa-lock");
		}
	}

	function alterar_quantidade() {
		$("#valor_total").val("R$ " + <?= $valor_unitario ?> * $("#quantidade").val() + ",00");
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

		carrega_valor();

		//$(":input").inputmask();

		$("#termo_de_aceite").change(function() {

			if ($("#termo_de_aceite").prop('checked')) {
				$("#botao_pre_matricula").attr("disabled", false);
				$("#responsavel").attr("disabled", false);
				$("#cpf").attr("disabled", false);

				$("#id_camiseta_tamanho").attr("disabled", false);
				$("#nome_camiseta").attr("disabled", false);
				$("#cpf").attr("disabled", false);
				$(".check_modalidade").attr("disabled", false);

				$("#botao_pre_matricula span").removeClass("fa-lock");
				$("#botao_pre_matricula span").addClass("fa-check");
			} else {
				$("#botao_pre_matricula").attr("disabled", true);
				$("#responsavel").attr("disabled", true);
				$("#cpf").attr("disabled", true);

				$("#id_camiseta_tamanho").attr("disabled", true);
				$("#nome_camiseta").attr("disabled", true);
				$(".check_modalidade").attr("disabled", true);

				$("#botao_pre_matricula span").removeClass("fa-check");
				$("#botao_pre_matricula span").addClass("fa-lock");
			}
		});

	});
</script>