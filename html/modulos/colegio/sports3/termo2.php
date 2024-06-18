<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("php/sqlsrv.php");

$id_menu = 93;
$chave = "id_sports";
$valor_unitario = 20;

$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];

#VERIFICA SE O ALUNO ESTÁ AUTORIZADO
$sql = "SELECT
			* 
		FROM
			colegio.matricula
		INNER JOIN colegio.atestado USING ( id_pessoa )  
		WHERE
			id_pessoa = " . $_SESSION['coopex']['usuario']['id_pessoa'];

$res = $coopex->query($sql);
$atestado = false;
if ($res->rowCount()) {
	$row = $res->fetch(PDO::FETCH_OBJ);
	$atestado = $row->id_situacao_atestado == 1 ? true : false;
} else {
	$atestado = false;
}

#VERIFICA SE O ALUNO ESTÁ AUTORIZADO
$sql = "SELECT
			* 
		FROM
			colegio.sports 
		WHERE
			id_pessoa = " . $_SESSION['coopex']['usuario']['id_pessoa'];

$res = $coopex->query($sql);
$autorizado = false;
if ($res->rowCount()) {
	$row = $res->fetch(PDO::FETCH_OBJ);
	$autorizado = $res->rowCount() ? true : false;
}

$sql2 = "SELECT
			crs_id_curso AS id_curso,
		CASE
			tcu_ch_matutino 
			WHEN 'S' THEN
			'1' ELSE '2' 
		END id_turno,
			ser_id_serie AS id_serie 
		FROM
			academico..HIS_historico_ingresso_saida a
			INNER JOIN academico..FMI_forma_ingresso b ON a.his_id_forma_ingresso = fmi_id_forma_ingresso
			INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_registro_curso = a.his_id_registro_curso
			INNER JOIN academico..CRS_curso ON rca_id_curso = crs_id_curso
			INNER JOIN academico..COL_colegiado ON crs_id_unidade = col_id_colegiado
			INNER JOIN academico..FAC_faculdade ON fac_id_faculdade = col_id_faculdade
			INNER JOIN academico..SAP_situacao_aluno_periodo_letivo_view SAP0 ON rca_id_registro_curso = SAP0.sap_id_registro_curso
			INNER JOIN academico..PEL_periodo_letivo PEL0 ON PEL0.pel_id_periodo_letivo = SAP0.sap_id_periodo_letivo
			INNER JOIN academico..IAP_informacoes_aluno_periodo_view ON iap_id_registro_curso = rca_id_registro_curso
			INNER JOIN academico..SER_serie ON iap_id_serie = ser_id_serie
			INNER JOIN registro..PES_pessoa ON PES_pessoa.pes_id_pessoa = rca_id_aluno
			INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
			INNER JOIN academico..TCU_turmas_curso ON tcu_id_turma_curso = rca_id_turma_curso 
		WHERE
			pel_ds_compacta = '20240' 
			AND fac_id_faculdade = 1000000006 
			AND iap_id_periodo_letivo = SAP0.sap_id_periodo_letivo 
			AND PES_ID_PESSOA = $id_pessoa";

$res2 = mssql_query($sql2);
$row_aluno = mssql_fetch_object($res2);

//print_r($row_aluno);

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
		<li class="breadcrumb-item active">Matrícula</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID.
				<?php echo $id_menu ?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-volleyball-ball'></i> Sports School
			<small>
				Matrícula
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
							devolução integral da taxa de pré-matrícula.</li>
						<li>A pré-matrícula garante a vaga. Todavia, fica condicionada à abertura de turma e também na
							finalização do procedimento de matrícula, com atendimento de todos os prazos e solicitações
							pelo COLÉGIO, bem como pela apresentação de atestado médico de aptidão física para a
							realização do esporte desejado.</li>
						<li>Poderá ocorrer, a critério do COLÉGIO, alteração dos horários e/ou cronograma
							disponibilizado, bem como outras medidas que se tornem necessárias por razões de ordem
							pedagógica e/ou administrativa.</li>
						<li>Se a modalidade escolhida pelo aluno(a) já estiver com o número máximo de alunos, ele(a)
							terá a opção de escolher outra modalidade ou entrar na lista de espera.</li>
						<li>As aulas serão realizadas de acordo com o calendário escolar do Colégio FAG.</li>
						<hr>
					</ol>
					<h3>MODALIDADES</h3>
					<div class="row mt-4">
						<div class="col-md-3 border p-4 m-2">
							<h5>Modalidades Educação Infantil</h5>
							<ul>
								<li>Capoeira</li>
								<li>Futebol de Campo</li>
								<li>Futsal</li>
								<li>Ginástica Rítimica</li>
								<li>Handebol</li>
								<li>Hip Hop</li>
								<li>Taekwondo</li>
							</ul>
						</div>

						<div class="col-md-3 border p-4 m-2">
							<h5>Modalidades Ensino Fundamental</h5>
							<ul>
								<li>Basquete</li>
								<li>Futebol de Campo</li>
								<li>Futsal</li>
								<li>Ginástica Rítimica</li>
								<li>Handebol</li>
								<li>Hip Hop</li>
								<li>Taekwondo</li>
								<li>Voleibol</li>
							</ul>
						</div>

						<div class="col-md-3 border p-4 m-2">
							<h5>Modalidades Ensino Médio</h5>
							<ul>
								<li>Basquete</li>
								<li>Futebol de Campo</li>
								<li>Futsal</li>
								<li>Handebol</li>
								<li>Hip Hop</li>
								<li>Voleibol</li>
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


					<h4><a target="_blank"
							href="https://coopex.fag.edu.br/arquivos/colegio/sports/horario2.pdf"><strong>VEJA O
								CRONOGRAMA COMPLETO</strong></a></h4>



					<h3 class="mt-5">VALORES</h3>
					<ul>
						<li>Uma modalidade: R$ 100,00 mensais (Fevereiro a Novembro)</li>
						<li>Duas ou mais modalidades: R$ 85,00 mensais cada modalidade (Fevereiro a Novembro).</li>
					</ul>

					<h4 class="mt-5">Modalidade Futebol de Campo</h4>
					<ul>
						<li>R$ 120,00 mensais (Fevereiro a Novembro), não tem desconto de segunda modalidade.</li>
						<li>Público externo: R$ 240,00 mensais (A participação de público externo fica condicionada à
							indicação de um aluno do Colégio FAG.).</li>
					</ul>
					<hr>


					<h3><a target="_blank"
							href="https://coopex.fag.edu.br/arquivos/colegio/sports/contrato.pdf"><strong>LEIA O
								CONTRATO DA ESCOLA DE ESPORTES</strong></a></h3>

					<div class="custom-control custom-checkbox mt-4">
						<input <?php echo $autorizado ? 'checked="" disabled' : '' ?> type="checkbox"
							class="custom-control-input" id="termo_de_aceite" value="1" name="termo_de_aceite">
						<label class="custom-control-label" for="termo_de_aceite">Li e concordo com os termos de
							ciência</label>
					</div>

					<br>
				</div>
			</div>
		</div>
	</div>


	<iframe class="d-nones" name="dados" src=""
		style="position: fixed; z-index: 999999999999; width: 100%; background-color: #fff; top: 0; left: 0; height: 300px"></iframe>

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
											<strong><?= utf8_encode($_SESSION['coopex']['usuario']['nome']) ?></strong>,
											expressamente
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
											Termo de ciência aceito em:
											<strong><?php echo converterDataHora($row->data_cadastro); ?></strong><br>
										</div>
										<?php
									}
									?>
								</div>

								<form class="col-12 p-2" method="post" target="dados"
									action="modulos/colegio/sports/pre_matricular.php">

									<input type="hidden" name="id_curso" value="<?= $row_aluno->id_curso ?>">
									<input type="hidden" name="id_turno" value="<?= $row_aluno->id_turno ?>">
									<input type="hidden" name="id_serie" value="<?= $row_aluno->id_serie ?>">

									<div class="row">
										<div class="col-xl-5 mb-3">
											<label class="form-label" for="responsavel">Nome do Responsável</label>
											<input style="text-transform: uppercase" required type="text"
												id="responsavel" name="responsavel"
												class="form-control form-control-lg required" disabled
												value="<?= isset($row->responsavel) ? utf8_encode($row->responsavel) : "" ?>" />
										</div>
										<div class="col-xl-3 mb-3">
											<label class="form-label" for="cpf">CPF do Responsável</label>
											<input required data-inputmask="'mask': '999.999.999-99'" type="text"
												id="cpf" name="cpf" class="form-control form-control-lg"
												value="<?= isset($row->cpf) ? $row->cpf : "" ?>" disabled />
										</div>
									</div>
									<h3 class="mt-4">CAMISETA</h3>
									<div class="row">
										<div class="col-xl-3 mb-4">
											<label class="form-label">Valor do Kit</label>
											<input type="text" class="form-control form-control-lg" value="R$ 120,00"
												disabled />
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
											<select required id="id_camiseta_tamanho" name="id_camiseta_tamanho"
												class="form-control form-control-lg required" <?= $autorizado ? "" : "disabled" ?> disabled />
											<option value="">Selecione</option>
											<?
											while ($row2 = $res2->fetch(PDO::FETCH_OBJ)) {
												?>
												<option <?= isset($row->id_camiseta_tamanho) && $row2->id_camiseta_tamanho == $row->id_camiseta_tamanho ? "selected" : "" ?> value="<?= $row2->id_camiseta_tamanho ?>"><?= $row2->tamanho ?>
												</option>
												<?
											}
											?>
											</select>
											<div class="mt-1"><a target="_blank"
													href="https://coopex.fag.edu.br/arquivos/colegio/sports/camiseta.pdf">Ver
													grade de tamanhos</a> </div>

										</div>

										<div class="col-xl-3 mb-3">
											<label class="form-label" for="nome_camiseta">Nome estampado na
												camiseta</label>
											<input required style="text-transform: uppercase" type="text"
												id="nome_camiseta" name="nome_camiseta"
												class="form-control form-control-lg required"
												value="<?= isset($row->nome_camiseta) ? utf8_encode($row->nome_camiseta) : "" ?>"
												disabled />
										</div>
									</div>
									<div class="row">
										<a class="col-xl-6 col-md-6"
											href="modulos/colegio/sports/images/camiseta_cinza.jpg" target="_blank"><img
												width="100%" src="modulos/colegio/sports/images/camiseta_cinza.jpg"></a>
										<a class="col-xl-6 col-md-6"
											href="modulos/colegio/sports/images/camiseta_azul.jpg" target="_blank"><img
												width="100%" src="modulos/colegio/sports/images/camiseta_azul.jpg"></a>
									</div>
									<div>*Imagens ilustrativas</div>

									<h3 class="mt-4">MODALIDADE DE INTERESSE</h3>
									<!-- <label class="form-label" for="nome_camiseta">Esta informação serve somente para fins de levantamento do número de interessados em cada modalidade, a modalidade definitiva que aluno irá participar será informada no ato da matrícula em Dezembro de 2023.</label> -->

									<div class="">
										<table class="table">
											<tr>
												<th>Modalidade</th>
												<!-- <th class="text-center">Vagas</th> -->
												<th>Vaga</th>
											</tr>

											<?
											/*$sql3 = "SELECT
																																														id_modalidade,
																																														modalidade,
																																														count(*) AS total,
																																														vagas,
																																														vagas - count(*) AS vasgas_restantes 
																																													FROM
																																														colegio.sports
																																														INNER JOIN colegio.modalidade_aluno USING ( id_sports )
																																														INNER JOIN colegio.modalidade USING ( id_modalidade )
																																														INNER JOIN colegio.modalidade_vaga USING ( id_modalidade ) 
																																													WHERE
																																														sports.id_curso = $row_aluno->id_curso 
																																														AND modalidade_vaga.id_curso = $row_aluno->id_curso 
																																														AND id_turno = $row_aluno->id_turno 
																																													GROUP BY
																																														id_modalidade";*/
											$sql3 = "SELECT
														* 
													FROM
														colegio.modalidade
														INNER JOIN colegio.modalidade_vaga USING ( id_modalidade ) 
													WHERE
														curso LIKE '%$row_aluno->id_curso%' 
														AND id_curso = $row_aluno->id_curso 
													ORDER BY
														modalidade";

											$res3 = $coopex->query($sql3);
											while ($row3 = $res3->fetch(PDO::FETCH_OBJ)) {
												if (isset($row->id_sports)) {
													$sql2 = "SELECT
															* 
														FROM
															colegio.modalidade_aluno
														where id_modalidade = $row3->id_modalidade
														and id_sports = $row->id_sports";
													$res2 = $coopex->query($sql2);
												}
												$sql_grupo = "SELECT
																	id_turno 
																FROM
																	colegio.grupo
																	INNER JOIN colegio.grupo_serie USING ( id_grupo ) 
																WHERE
																	id_serie = $row_aluno->id_serie 
																	AND id_modalidade = $row3->id_modalidade";
												$res_grupo = $coopex->query($sql_grupo);
												$row_grupo = $res_grupo->fetch(PDO::FETCH_OBJ);

												//se turno = 3, manhã e tarde fazem juntos
												if (isset($row_grupo->id_turno)) {
													$condicao_turno = $row_grupo->id_turno == 3 ? "" : "AND id_turno = $row_aluno->id_turno";
												}

												/*$sql_vaga = "SELECT
																																																				id_modalidade,
																																																				modalidade,
																																																				count(*) AS total,
																																																				vagas,
																																																				vagas - count(*) AS vasgas_restantes 
																																																			FROM
																																																				colegio.modalidade_aluno
																																																				INNER JOIN colegio.sports USING ( id_sports )
																																																				INNER JOIN colegio.grupo_serie USING ( id_serie )
																																																				INNER JOIN colegio.modalidade USING ( id_modalidade )
																																																				INNER JOIN colegio.modalidade_vaga USING ( id_modalidade )  
																																																			WHERE
																																																				id_modalidade = $row3->id_modalidade 
																																																				AND id_grupo = ( SELECT id_grupo FROM colegio.grupo INNER JOIN colegio.grupo_serie USING ( id_grupo ) WHERE id_serie = $row_aluno->id_serie $condicao_turno AND id_modalidade = $row3->id_modalidade GROUP BY id_modalidade) 
																																																				$condicao_turno
																																																				GROUP BY id_pessoa";*/
												$sql_vaga = "SELECT
																count(*) AS total,
																vagas,
																vagas - count(*) AS vagas_restantes 
															FROM
																colegio.modalidade_aluno
																INNER JOIN colegio.sports USING ( id_sports )
																INNER JOIN colegio.grupo_serie USING ( id_serie )
																INNER JOIN colegio.grupo USING ( id_grupo ) 
															WHERE
																modalidade_aluno.id_modalidade = $row3->id_modalidade 
																AND id_grupo = ( SELECT id_grupo FROM colegio.grupo INNER JOIN colegio.grupo_serie USING ( id_grupo ) WHERE id_serie = $row_aluno->id_serie AND id_modalidade = $row3->id_modalidade GROUP BY id_modalidade ) 
															GROUP BY
																id_grupo";


												$res_vaga = $coopex->query($sql_vaga);
												$row_vaga = $res_vaga->fetch(PDO::FETCH_OBJ);
												$vagas_restantes = $row_vaga->vagas_restantes;

												if (isset($row->id_sports)) {
													$check = "disabled";
												} else if ($vagas_restantes < 1) {
													$check = "disabled";
												} else {
													$check = "";
												}

												?>
												<tr>
													<th><?= utf8_encode($row3->modalidade) ?></th>
													<!-- <th class="text-center"><?= $vagas_restantes > 0 ? $vagas_restantes : 0; ?></th> -->
													<th>
														<div class="custom-control custom-checkbox">
															<input <?= (isset($row->id_sports) && $res2->rowCount()) ? "checked" : "" ?> 	<?= $check ?> name="id_modalidade[]"
																value="<?= $row3->id_modalidade ?>,<?= $vagas_restantes ?>"
																type="checkbox"
																class="custom-control-input check_modalidade"
																id="modalidade<?= $row3->id_modalidade ?>">
															<label class="custom-control-label"
																for="modalidade<?= $row3->id_modalidade ?>">
																<?= $vagas_restantes > 0 ? "Reservar vaga" : "Incluir na fila de espera"; ?></label>
														</div>
													</th>
												</tr>
												<?
											}
											?>
										</table>
									</div>

									<div class="row">
										<div class="col-4">
											<label class="form-label" for="autorizar">&nbsp</label>
											<br>
											<?php if (!$autorizado) { ?>
												<button id="botao_pre_matricula" type="submit"
													class="btn btn-lg btn-primary waves-effect waves-themed" disabled>
													<span class="fal fa-lock mr-1"></span>
													Pré-matricular
												</button>
											<?php } else {

												if ($row->pagamento) {
													?>
													<div class="panel-tag">
														Pagamento realizado em:
														<strong><?php echo converterData($row->data_pagamento); ?></strong><br>
													</div>
													<?
												} else {


													?>
													<div>
														<label class="form-label">&nbsp</label><br>
														<a target="_blank"
															href="https://coopex.fag.edu.br/boleto/sports/<?= $id_pessoa ?>"
															class="btn btn-lg btn-primary btn-lg waves-effect waves-themed"
															<?= $autorizado ? "" : "disabled" ?>>
															<span
																class="fal fa-<?= $autorizado ? "check" : "lock" ?> mr-1"></span>Gerar
															o Boleto de Pagamento
														</a>
													</div>
													<?
												}
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

	<?
	if (isset($row->pagamento) && $row->pagamento) {
		$sql = "SELECT
					pes_id_pessoa,
					rtrim(alu_nu_matricula) AS ra,
					rtrim(pes_nm_pessoa) AS nome,
					rtrim(crs_nm_resumido) AS curso,
					ser_ds_serie AS serie,
					sap_ds_situacao AS situacao,
					rca_id_registro_curso,
					ser_id_serie,
					pel_id_periodo_letivo,
					rca_id_registro_curso
				FROM
					registro..PES_pessoa
				INNER JOIN academico..ALU_aluno ON alu_id_pessoa = pes_id_pessoa
				INNER JOIN academico..RCA_registro_curso_aluno ON rca_id_aluno = pes_id_pessoa
				INNER JOIN academico..CRS_curso ON crs_id_curso = rca_id_curso
				INNER JOIN academico..SAP_situacao_aluno_periodo_letivo_view ON rca_id_registro_curso = sap_id_registro_curso
				INNER JOIN academico..PEL_periodo_letivo ON sap_id_periodo_letivo = pel_id_periodo_letivo
				INNER JOIN academico..IAP_informacoes_aluno_periodo_view ON iap_id_registro_curso = rca_id_registro_curso
				INNER JOIN academico..SER_serie ON iap_id_serie = ser_id_serie
				WHERE
					iap_id_periodo_letivo = 5000000244
				AND pes_id_pessoa = $id_pessoa
				AND EXISTS (
					SELECT
						1
					FROM
						financeiro..cta_contrato_academico,
						financeiro..ctr_contrato,
						financeiro..CPL_contrato_periodo_letivo,
						financeiro..prc_parcela,
						financeiro..ttf_titulo_financeiro
					WHERE
						cta_id_contrato = ctr_id_contrato
					AND ctr_id_cliente = rca_id_aluno
					AND cpl_id_periodo_letivo = pel_id_periodo_letivo
					AND cpl_id_contrato = cta_id_contrato
					AND prc_id_contrato = cta_id_contrato
					AND ttf_id_parcela = prc_id_parcela
					AND ttf_st_situacao IN ('P', 'L', 'G', 'R', 'S') 
				)
			
				ORDER BY
					crs_nm_resumido,
					ser_ds_serie,
					pes_nm_pessoa";
		$res = mssql_query($sql);
		if (mssql_num_rows($res)) {
			?>
			<div class="row">
				<div class="col-xl-12">
					<div id="panel-2" class="panel">
						<div class="panel-hdr">
							<h2>
								3. Atestado
							</h2>
						</div>

						<?
						$sql = "SELECT
								* 
							FROM
								colegio.matricula 
							WHERE
								id_pessoa = " . $_SESSION['coopex']['usuario']['id_pessoa'];

						$res = $coopex->query($sql);
						$matricula = $res->rowCount();
						$row_matricula = $res->fetch(PDO::FETCH_OBJ);
						?>
						<div class="panel-container show">
							<div class="panel-content p-0">
								<div class="panel-content">
									<div class="form-row form-group">

										<form data-ajax="false" id="form_atestado" class="col-12 p-2" method="post"
											target="dados" action="modulos/colegio/sports/inscricao_dados_atestado.php"
											enctype="multipart/form-data">

											<h3 id="atestado_aptidao">ATESTADO DE APTIDÃO FÍSICA</h3>
											<label class="form-label">De acordo com o constante no Item 2.1.1. do Contrato de
												Prestação de Serviços Desportivos, é exigida, para a efetivação da matrícula e
												participação nas atividades desportivas, a apresentação de atestado médico
												comprovando a aptidão física do(a) aluno(a), <strong>o qual deve ser anexado no
													presente momento (abaixo)</strong>. O atestado médico em questão será
												submetido à apreciação do Coordenador da Escola de Esportes em até 05 dias úteis
												e, em sendo aprovado, restará autorizado o prosseguimento do processo de
												matrícula.</label>


											<?
											$sql = "SELECT
													* 
												FROM
													colegio.atestado 
												WHERE
													id_pessoa = " . $_SESSION['coopex']['usuario']['id_pessoa'];

											$res = $coopex->query($sql);
											$atestado = $res->rowCount();
											$row_atestado = $res->fetch(PDO::FETCH_OBJ);

											if ($atestado) {
												?>
												<h3 class="mt-4"><a target="_blank"
														href="https://coopex.fag.edu.br/arquivos/colegio/sports/atestado/<?= $row_atestado->id_atestado . "." . $row_atestado->extensao ?>">Visualizar
														atestado anexado</a></h3>
												<div class="panel-tag mt-4">
													Atestado enviado em:
													<strong><?php echo converterDataHora($row_atestado->data_atestado); ?></strong><br>
												</div>
												<?
											} else {
												?>
												<input type="file" id="atestado" name="atestado">
												<br>
												<button onclick="validarFormulario()" id="botao_atestado" type="button"
													class="btn btn-lg btn-primary waves-effect waves-themed mt-4">
													<span class="fal mr-1"></span>
													Enviar Atestado
												</button>
												<?
											}
											?>
										</form>

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?
			if ($atestado) {
				?>
				<div class="row">
					<div class="col-xl-12">
						<div id="panel-2" class="panel">
							<div class="panel-hdr">
								<h2>
									4. Matrícula
								</h2>
							</div>

							<?
							$sql = "SELECT
								* 
							FROM
								colegio.matricula 
							WHERE
								id_pessoa = " . $_SESSION['coopex']['usuario']['id_pessoa'];

							$res = $coopex->query($sql);
							$matricula = $res->rowCount();
							$row_matricula = $res->fetch(PDO::FETCH_OBJ);
							?>
							<div class="panel-container show">
								<div class="panel-content p-0">
									<div class="panel-content">
										<div class="form-row form-group">

											<form class="col-12 p-2" method="post" target="dados"
												action="modulos/colegio/sports/inscricao_dados_matricula.php">
												<h3 class="mt-4">MODALIDADES</h3>
												<label class="form-label">De acordo com o constante na Cláusula Quinta do Contrato
													de Prestação de Serviços Desportivos, o(a) representante legal do(a) aluno(a)
													poderá, a qualquer momento, rescindir o contrato unilateralmente, ficando
													obrigado(a) ao pagamento das mensalidades em atraso e a do mês em vigência, bem
													como da multa rescisória correspondente à 01 mensalidade da modalidade
													escolhida. Ademais, na eventualidade de a turma da modalidade esportiva
													selecionada não alcançar o número mínimo de alunos estabelecido contratualmente,
													o contrato restará automaticamente rescindido.</label>
												<table class="table">
													<tr>
														<th>Modalidade</th>
														<!-- <th class="text-center">Vagas</th> -->
														<th class="text-right">Valor</th>
														<th>Matrícula</th>
													</tr>
													<?
													$sql3 = "SELECT
															* 
														FROM
															colegio.modalidade
															INNER JOIN colegio.modalidade_vaga USING ( id_modalidade ) 
														WHERE
															curso LIKE '%$row_aluno->id_curso%' 
															AND id_curso = $row_aluno->id_curso 
														ORDER BY
															modalidade";

													$res3 = $coopex->query($sql3);
													$total = 0;
													while ($row3 = $res3->fetch(PDO::FETCH_OBJ)) {
														if (isset($row->id_sports)) {
															if ($matricula) {
																$sql2 = "SELECT
																* 
															FROM
																colegio.modalidade_aluno_matricula
															where id_modalidade = $row3->id_modalidade
															and id_matricula = $row_matricula->id_matricula";
															} else {
																$sql2 = "SELECT
																* 
															FROM
																colegio.modalidade_aluno
															where id_modalidade = $row3->id_modalidade
															and id_sports = $row->id_sports";
															}

															//echo $sql2;
										
															$res2 = $coopex->query($sql2);
															$row2 = $res2->fetch(PDO::FETCH_OBJ);

															if ($res2->rowCount()) {
																$id_mod[] = $row2->id_modalidade;
															}
														}


														$sql_grupo = "SELECT
																	id_turno 
																FROM
																	colegio.grupo
																	INNER JOIN colegio.grupo_serie USING ( id_grupo ) 
																WHERE
																	id_serie = $row_aluno->id_serie 
																	AND id_modalidade = $row3->id_modalidade";
														$res_grupo = $coopex->query($sql_grupo);
														$row_grupo = $res_grupo->fetch(PDO::FETCH_OBJ);

														//se turno = 3, manhã e tarde fazem juntos
														if (isset($row_grupo->id_turno)) {
															$condicao_turno = $row_grupo->id_turno == 3 ? "" : "AND id_turno = $row_aluno->id_turno";
														}

														$sql_vaga = "SELECT
																id_modalidade,
																modalidade,
																count(*) AS total,
																vagas,
																vagas - count(*) AS vasgas_restantes 
															FROM
																colegio.modalidade_aluno
																INNER JOIN colegio.sports USING ( id_sports )
																INNER JOIN colegio.grupo_serie USING ( id_serie )
																INNER JOIN colegio.modalidade USING ( id_modalidade )
																INNER JOIN colegio.modalidade_vaga USING ( id_modalidade )  
															WHERE
																id_modalidade = $row3->id_modalidade 
																AND id_grupo = ( SELECT id_grupo FROM colegio.grupo INNER JOIN colegio.grupo_serie USING ( id_grupo ) WHERE id_serie = $row_aluno->id_serie $condicao_turno AND id_modalidade = $row3->id_modalidade GROUP BY id_modalidade) 
																$condicao_turno
																GROUP BY id_pessoa";
														$res_vaga = $coopex->query($sql_vaga);
														$vagas_restantes = $row3->vagas - $res_vaga->rowCount();

														$sql_vaga = "SELECT
																		count(*) AS total,
																		vagas,
																		vagas - count(*) AS vagas_restantes 
																	FROM
																		colegio.modalidade_aluno
																		INNER JOIN colegio.sports USING ( id_sports )
																		INNER JOIN colegio.grupo_serie USING ( id_serie )
																		INNER JOIN colegio.grupo USING ( id_grupo ) 
																	WHERE
																		modalidade_aluno.id_modalidade = $row3->id_modalidade 
																		AND id_grupo = ( SELECT id_grupo FROM colegio.grupo INNER JOIN colegio.grupo_serie USING ( id_grupo ) WHERE id_serie = $row_aluno->id_serie AND id_modalidade = $row3->id_modalidade GROUP BY id_modalidade ) 
																	GROUP BY
																		id_grupo";


														$res_vaga = $coopex->query($sql_vaga);
														$row_vaga = $res_vaga->fetch(PDO::FETCH_OBJ);
														$vagas_restantes = $row_vaga->vagas_restantes ? $row_vaga->vagas - $row_vaga->total : $row3->vagas;


														?>
														<tr>
															<td>
																<?= utf8_encode($row3->modalidade) ?>
															</td>
															<!-- <td align="center"><?= $vagas_restantes > 1 ? $vagas_restantes : 0 ?></td> -->
															<td align="right">R$ <?= number_format($row3->valor, 2, ',', '.') ?></td>
															<td>
																<div class="custom-control custom-checkbox">
																	<input <?= $vagas_restantes <= 0 ? "disabled" : "" ?>
																		onclick="carrega_valor()" <?= isset($row->id_sports) && $res2->rowCount() && $vagas_restantes > 0 ? "checked" : "" ?>
																		name="id_modalidade_matricula[]"
																		value="<?= $row3->id_modalidade ?>" type="checkbox"
																		class="custom-control-input check_modalidade"
																		id="id_modalidade_matricula<?= $row3->id_modalidade ?>"
																		<?= $matricula ? 'disabled' : '' ?>>
																	<label class="custom-control-label"
																		for="id_modalidade_matricula<?= $row3->id_modalidade ?>">Matricular</label>
																</div>
															</td>
														</tr>
														<?
													}


													$id_modalidade = implode(",", $id_mod);

													$sql = "SELECT
													SUM( valor ) AS total ,
													SUM( desconto ) AS desconto
												FROM
													colegio.modalidade 
												WHERE
													id_modalidade IN ($id_modalidade)";
													$res5 = $coopex->query($sql);
													$row5 = $res5->fetch(PDO::FETCH_OBJ);

													$subtotal = $row5->total;
													$desconto = $row5->total - $row5->desconto;
													$total = $row5->total - $desconto;

													?>
													<tr style="background-color: #DCDCDC;">
														<td colspan="2" align="right">SUBTOTAL</td>
														<td align="right"><strong>R$ <span
																	id="subtotal"><?= number_format($subtotal, 2, ',', '.') ?></span></strong>
														</td>
														<td></td>
													</tr>
													<tr style="background-color: #D3D3D3;">
														<td colspan="2" align="right">DESCONTO DE SEGUNDA MODALIDADE</td>
														<td align="right"><strong>R$ <span
																	id="desconto"><?= number_format($desconto, 2, ',', '.') ?></span></strong>
														</td>
														<td></td>
													</tr>
													<tr style="background-color: #C0C0C0;">
														<td colspan="2" align="right">TOTAL MENSAL</td>
														<td align="right"><strong>R$ <span
																	id="mensal"><?= number_format($total, 2, ',', '.') ?></span></strong>
														</td>
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
															<button id="botao_matricula" type="submit"
																class="btn btn-lg btn-primary waves-effect waves-themed"
																<?= count($id_mod) ? "" : "disabled" ?>>
																<span
																	class="fal <?= count($id_mod) ? "fa-check" : "fa-lock" ?> mr-1"></span>
																Matricular
															</button>

															<?
														} else {
															?>
															<hr>
															<label class="form-label" for="nome_camiseta">A emissão e o respectivo
																fornecimento dos boletos bancários para pagamento das mensalidades
																dar-se-ão tão somente após a devida inscrição do(a) aluno e o
																cumprimento da totalidade dos requisitos exigidos no Contrato de
																Prestação de Serviços Desportivos. A matrícula do(a) aluno(a) restará
																formalizada através do pagamento da mensalidade, de acordo com a
																modalidade e a quantidade de esportes optada.</label>

															<table class="table">
																<tr>
																	<td>Parcela</td>
																	<td>Valor</td>
																	<td>Vencimento</td>
																	<td>Boleto</td>
																</tr>

																<?
																$sql = "SELECT
																* 
															FROM
																colegio.matricula_boleto 
															WHERE
																id_pessoa = $id_pessoa";
																$res = $coopex->query($sql);
																while ($row = $res->fetch(PDO::FETCH_OBJ)) {
																	?>
																	<tr>
																		<td><?= $row->parcela ?></td>
																		<td>R$ <?= number_format($row->valor, 2, ',', '.') ?></td>
																		<td><?= converterData($row->data_vencimento) ?></td>
																		<td>
																			<?
																			if ($row->pago || $row_atestado->id_situacao_atestado == 0) {
																				?>
																				<button class="btn btn-lg btn-primary waves-effect waves-themed"
																					<?= $row->pago || 1 == 1 ? "" : "" ?>>
																					<span
																						class="fal <?= $row->pago ? "fa-check" : "fa-print" ?> mr-1"></span>
																					<?= $row->pago ? "Pago" : "Gerar Boleto" ?>
																				</button>
																				<?
																			} else {
																				?>
																				<a target="_blank"
																					href="https://coopex.fag.edu.br/boleto/sports/matricula/<?= $row->id_matricula_boleto ?>"
																					class="btn btn-lg btn-primary waves-effect waves-themed"
																					<?= $row->pago || $atestado == 0 ? "" : "" ?>>
																					<span
																						class="fal <?= $row->pago ? "fa-check" : "fa-print" ?> mr-1"></span>
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
				<?
			}
			?>
			<?
		} else {
			echo "sem pagamento da primeira mensalidade";
		}
		?>

		<?
	}
	?>


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
	function validarFormulario() {
		var inputFile = document.getElementById("atestado");

		// Verificar se um arquivo foi selecionado
		if (inputFile.files.length === 0) {
			alert("É obrigatório anexar o Atestado de Aptidão Física!");
		} else {
			$("#form_atestado").submit();
		}

		// Restante da lógica de validação, se necessário
		return true; // Permite o envio do formulário
	}


	//MENSAGEM DE CADASTRO OK
	function atestadoOK(id_matricula) {
		var msg = "Atestado enviado com sucesso, selecione a modalidade esportiva abaixo!";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 3000,
			onClose: () => {
				location.reload();
			}
		});
	}


	//MENSAGEM DE CADASTRO OK
	function matriculaOK(id_matricula) {
		var msg = "Matricula realizada sucesso";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				$.getJSON("php/registro_bradesco/sports_matricula_parcelamento.php", {
					id: id_matricula
				})
					.done(function (json) {
						location.reload();
					})
					.fail(function (jqxhr, textStatus, error) {
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

		var ids = $('input[name="id_modalidade_matricula[]"]:checked').map(function () {
			return $(this).val();
		}).get();

		// Exibe os IDs no console (pode ser alterado conforme sua necessidade)
		//console.log(ids);

		//console.log($('input[name="id_modalidade"]').is(':checked'));


		if (ids.length) {
			$.getJSON("modulos/colegio/sports/ajax/carrega_valor.php", {
				id_modalidade: ids
			})
				.done(function (json) {
					res = json[0];

					$("#subtotal").html(res.subtotal);
					$("#desconto").html(res.desconto);
					$("#total").html(res.anual);
					$("#mensal").html(res.total);

					$("#total_anual").html(res.total_anual);
					$("#total_semestral").html(res.total_semestral);
					$("#total_mensal").html(res.total_mensal);

					$("#parcela_anual").html(res.parcela_anual);
					$("#parcela_semestral").html(res.parcela_semestral);
					$("#parcela_mensal").html(res.parcela_mensal);

					$("#botao_matricula").attr("disabled", false);
					$("#botao_matricula span").removeClass("fa-lock");
					$("#botao_matricula span").addClass("fa-check");
				})
				.fail(function (jqxhr, textStatus, error) {

					var err = textStatus + ", " + error;
					console.log("Request Failed: " + err);
				});
		} else {
			$("#subtotal").html("0,00");

			$("#subtotal").html("0,00");
			$("#desconto").html("0,00");
			$("#total").html("0,00");
			$("#mensal").html("0,00");

			$("#total_anual").html("0,00");
			$("#total_semestral").html("0,00");
			$("#total_mensal").html("0,00");

			$("#parcela_anual").html("0,00");
			$("#parcela_semestral").html("0,00");
			$("#parcela_mensal").html("0,00");

			$("#botao_matricula").attr("disabled", true);
			$("#botao_matricula span").removeClass("fa-check");
			$("#botao_matricula span").addClass("fa-lock");
		}
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


	$(document).ready(function () {

		carrega_valor();

		$(":input").inputmask();

		$("#termo_de_aceite").change(function () {

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