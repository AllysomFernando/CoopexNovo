<?php
require_once("php/sqlsrv.php");
require_once("ajax/GraduacaoController.php");

$id_menu = 112;
$chave = "graduacao_id";

$controller = new GraduacaoController($google);

if (isset($_GET['id'])) {
	$$chave = $_GET['id'];
	$campus = "CASCAVEL";

	if ($$chave > 2000) {
		$$chave -= 2000;
		$campus = "TOLEDO";
	}

	$dados = $controller->getGraduacaoById($campus, $$chave);
} else {
	$$chave = 0;
}
// echo $campus;
// print_r($dados);

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css?<php echo time() ?>">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/summernote/summernote.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/cropperjs/cropper.css">
<link rel="stylesheet" media="screen, print" href="css/fa-solid.css">
<script src="js/core.js?<?php echo time() ?>"></script>

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
		<li class="breadcrumb-item"><a href="https://coopex.fag.edu.br/coopex/graduacao/consulta">Cursos de Graduação</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID.
				<?php echo $id_menu ?>c
			</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-repeat'></i> Cursos de Graduação
			<small>
				Cadastro de Cursos de Graduação
			</small>
		</h1>
	</div>

	<iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 30%; background-color: #fff; top: 0; left: 0; height: 270px"></iframe>

	<form class="needs-validation" novalidate target="dados" method="post" action="modulos/coopex/graduacao/cadastro_dados.php">
		<!-- <form class="needs-validation" method="post"
		action="modulos/coopex/graduacao/cadastro_dados.php"> -->
		<input type="hidden" name="<?php echo $chave ?>" value="<?php echo $$chave ? $$chave : 0 ?>" />

		<div class="row">
			<div class="col-xl-12">
				<div id="panel-2" class="panel">
					<div class="panel-hdr">
						<h2>
							1. Institucional
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">

						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="form-row">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom02">Nome da Graduação<span class="text-danger">
												*</span></label>
										<input type="text" class="form-control" name="graduacao_nome" placeholder="Administração" value="<?php echo isset($dados->graduacao_nome) ? $dados->graduacao_nome : "" ?>" required>
									</div>
								</div>

								<div class="form-row form-group">

									<div class="col-md-4 mb-3">
										<label class="form-label" for="validationCustom02">Campus<span class="text-danger"> *</span></label>
										<select name="graduacao_campus" class="form-control" required>
											<option value="cascavel" <?php echo isset($campus) && $campus == "CASCAVEL" ? "selected" : "" ?>>
												CASCAVEL
											</option>
											<option value="toledo" <?php echo isset($campus) && $campus == "TOLEDO" ? "selected" : "" ?>>
												TOLEDO
											</option>
										</select>
									</div>

									<div class="col-md-5 ">
										<label class="form-label" for="validationCustom02">Valor da mensalidade<span class="text-danger">
												*</span></label>
										<input type="text" class="form-control decimal" data-thousands="." data-decimal="," name="graduacao_valor" id="graduacao_valor" placeholder="R$ 600,00" value="<?php echo isset($dados->graduacao_valor) ? $dados->graduacao_valor : "" ?>" required>
									</div>

									<div class="col-md-3">
										<label class="form-label" for="validationCustom02">Conceito Mec <span class="text-danger"></span></label>
										<input type="number" class="form-control" name="graduacao_conceito_mec" placeholder="5" value="<?php echo isset($dados->graduacao_conceito_mec) ? $dados->graduacao_conceito_mec : "" ?>">
									</div>
								</div>

								<div class="form-row form-group">
									<div class="col-md-4 mb-4">
										<label class="form-label" for="validationCustom02">Tipo da graduação 1<span class="text-danger"></span></label>
										<select name="graduacao_tipo_1" class="form-control">
											<option value="">
												-
											</option>
											<option value="Bacharelado" <?php echo isset($dados->graduacao_tipo_1) && $dados->graduacao_tipo_1 == "Bacharelado" ? "selected" : "" ?>>
												Bacharelado
											</option>
											<option value="Tecnologia" <?php echo isset($dados->graduacao_tipo_1) && $dados->graduacao_tipo_1 == "Tecnologia" ? "selected" : "" ?>>
												Tecnologia
											</option>
											<option value="Licenciatura" <?php echo isset($dados->graduacao_tipo_1) && $dados->graduacao_tipo_1 == "Licenciatura" ? "selected" : "" ?>>
												Licenciatura
											</option>
										</select>
									</div>

									<div class="col-md-4 mb-4">
										<label class="form-label" for="validationCustom02">Tipo da graduação 2<span class="text-danger"></span></label>
										<select name="graduacao_tipo_2" class="form-control">
											<option value="">
												-
											</option>
											<option value="Bacharelado" <?php echo isset($dados->graduacao_tipo_2) && $dados->graduacao_tipo_2 == "Bacharelado" ? "selected" : "" ?>>
												Bacharelado
											</option>
											<option value="Tecnologia" <?php echo isset($dados->graduacao_tipo_2) && $dados->graduacao_tipo_2 == "Tecnologia" ? "selected" : "" ?>>
												Tecnologia
											</option>
											<option value="Licenciatura" <?php echo isset($dados->graduacao_tipo_2) && $dados->graduacao_tipo_2 == "Licenciatura" ? "selected" : "" ?>>
												Licenciatura
											</option>
										</select>
									</div>

									<div class="col-md-4 mb-4">
										<label class="form-label" for="validationCustom02">Tipo 3<span class="text-danger"></span></label>
										<select name="graduacao_tipo_3" class="form-control">
											<option value="">
												-
											</option>
											<option value="Bacharelado" <?php echo isset($dados->graduacao_tipo_3) && $dados->graduacao_tipo_3 == "Bacharelado" ? "selected" : "" ?>>
												Bacharelado
											</option>
											<option value="Tecnologia" <?php echo isset($dados->graduacao_tipo_3) && $dados->graduacao_tipo_3 == "Tecnologia" ? "selected" : "" ?>>
												Tecnologia
											</option>
											<option value="Licenciatura" <?php echo isset($dados->graduacao_tipo_3) && $dados->graduacao_tipo_3 == "Licenciatura" ? "selected" : "" ?>>
												Licenciatura
											</option>
										</select>
									</div>
								</div>

								<div class="form-row form-group">
									<div class="col-md-12">
										<label class="form-label" for="graduacao_estrutura_lab">
											Estrutura dos Laboratórios
										</label>
										<input type="text" class="form-control" name="graduacao_estrutura_lab" id="graduacao_estrutura_lab" value="<?php echo isset($dados->graduacao_estrutura_lab) ? $dados->graduacao_estrutura_lab : "" ?>" placeholder="Salas Pro Active - Laboratórios de Informática">
									</div>
								</div>

								<div class="form-row form-group">
									<div class="col-md-12 mb-12">
										<label class="form-label" for="validationCustom02">Sobre o curso<span class="text-danger">
												*</span></label>
										<textarea class="js-summernote" id="summernote" name="graduacao_texto" required><?php echo isset($dados->graduacao_texto) ? $dados->graduacao_texto : "" ?></textarea>
									</div>
								</div>

								<div class="form-row form-group">
									<div class="col-md-12 mb-12">
										<label class="form-label" for="validationCustom02">Matriz Curricular</label>
										<textarea class="js-summernote" id="summernote" name="graduacao_matriz_curricular"><?php echo isset($dados->graduacao_matriz_curricular) ? $dados->graduacao_matriz_curricular : "" ?></textarea>
									</div>
								</div>

								<h1>Carga Horária</h1>

								<div class="form-row form-group">
									<div class="col-md-4">
										<label class="form-label" for="validationCustom02">Duração<span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_duracao" placeholder="2 anos" value="<?php echo isset($dados->graduacao_duracao) ? $dados->graduacao_duracao : "" ?>">
									</div>

									<div class="col-md-4">
										<label class="form-label" for="validationCustom02">Carga Horária<span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_carga_horaria" placeholder="1.600 horas" value="<?php echo isset($dados->graduacao_carga_horaria) ? $dados->graduacao_carga_horaria : "" ?>">
									</div>

									<div class="col-md-4">
										<label class="form-label" for="validationCustom02">Período<span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_periodo" placeholder="Noturno" value="<?php echo isset($dados->graduacao_periodo) ? $dados->graduacao_periodo : "" ?>">
									</div>
								</div>

								<h1>Coordenação</h1>

								<div class="form-row">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="validationCustom02">Coordenador<span class="text-danger">
												*</span></label>
										<input type="text" class="form-control" name="graduacao_coordenador" placeholder="Prof. Fernando Incerti" value="<?php echo isset($dados->graduacao_coordenador) ? $dados->graduacao_coordenador : "" ?>" required>
									</div>
								</div>

								<div class="form-row form-group">
									<div class="col-md-6 mb-3">
										<label class="form-label" for="validationCustom02">Email<span class="text-danger"> *</span></label>
										<input type="email" class="form-control" name="graduacao_email" placeholder="fernando@fag.edu.br" value="<?php echo isset($dados->graduacao_email) ? $dados->graduacao_email : "" ?>" required>
									</div>

									<div class="col-md-6 mb-3">
										<label class="form-label" for="validationCustom02">Telefone<span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_telefone" placeholder="(45) 3321-3884 | (45) 3321-3887" value="<?php echo isset($dados->graduacao_telefone) ? $dados->graduacao_telefone : "" ?>">
									</div>
								</div>

								<div class="form-row form-group">
									<h2>Atendimento</h2>
									<div class="col-md-12 mb-12">
										<label class="form-label" for="validationCustom02">Descrição</label>
										<textarea class="js-summernote" name="graduacao_atendimento"><?php echo isset($dados->graduacao_atendimento) ? $dados->graduacao_atendimento : "" ?></textarea>
									</div>
								</div>

								<h1>Semana Acadêmica</h1>

								<div class="form-row form-group">
									<div class="col-md-6 ">
										<label class="form-label" for="validationCustom02">Semana Acadêmica Ano<span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_semana_academica_ano" placeholder="" value="<?php echo isset($dados->graduacao_semana_academica_ano) ? $dados->graduacao_semana_academica_ano : "" ?>">
									</div>
									<div class="col-md-6 ">
										<label class="form-label" for="validationCustom02">Semana Acadêmica Período<span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_semana_academica_periodo" placeholder="" value="<?php echo isset($dados->graduacao_semana_academica_periodo) ? $dados->graduacao_semana_academica_periodo : "" ?>">
									</div>
								</div>

								<div class="form-row form-group">
									<div class="col-md-6">
										<label class="form-label" for="graduacao_semana_academica_linha1">Semana Acadêmica Linha 1<span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_semana_academica_linha1" placeholder="" value="<?php echo isset($dados->graduacao_semana_academica_linha1) ? $dados->graduacao_semana_academica_linha1 : "" ?>">
									</div>
									<div class="col-md-6">
										<label class="form-label" for="validationCustom02">Semana Acadêmica Linha 2<span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_semana_academica_linha2" placeholder="" value="<?php echo isset($dados->graduacao_semana_academica_linha2) ? $dados->graduacao_semana_academica_linha2 : "" ?>">
									</div>
								</div>

								<h1>Descrição</h1>

								<div class="form-row form-group">
									<div class="col-md-12">
										<label class="form-label" for="validationCustom02">Título extra 1 <span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_titulo_extra_1" placeholder="" value="<?php echo isset($dados->graduacao_titulo_extra_1) ? $dados->graduacao_titulo_extra_1 : "" ?>">
									</div>
									<br />
									<div class="col-md-12 mb-12">
										<label class="form-label" for="validationCustom02">Descrição extra 1</label>
										<textarea class="js-summernote" name="graduacao_descricao_extra_1"><?php echo isset($dados->graduacao_descricao_extra_1) ? $dados->graduacao_descricao_extra_1 : "" ?></textarea>
									</div>
								</div>

								<div class="form-row form-group">
									<div class="col-md-12">
										<label class="form-label" for="validationCustom02">Título extra 2 <span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_titulo_extra_2" placeholder="" value="<?php echo isset($dados->graduacao_titulo_extra_2) ? $dados->graduacao_titulo_extra_2 : "" ?>">
									</div>
									<br />
									<div class="col-md-12 mb-12">
										<label class="form-label" for="validationCustom02">Descrição extra 2</label>
										<textarea class="js-summernote" name="graduacao_descricao_extra_2"><?php echo isset($dados->graduacao_descricao_extra_2) ? $dados->graduacao_descricao_extra_2 : "" ?></textarea>
									</div>
								</div>

								<h1>Egressos</h1>

								<div class="form-row">
									<div class="col-md-12 mb-12">
										<label class="form-label" for="validationCustom02">Perfil Egresso</label>
										<textarea class="js-summernote" id="summernote" name="graduacao_perfil_egresso"><?php echo isset($dados->graduacao_perfil_egresso) ? $dados->graduacao_perfil_egresso : "" ?></textarea>
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
				<div id="panel-2" class="panel">
					<div class="panel-hdr">
						<h2>
							2. Mídias e redes sociais
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">

						<div class="panel-content p-0">
							<div class="panel-content">

								<div class="form-row form-group">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Youtube Id<span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_youtube" placeholder="LA9lv7EL1ho" value="<?php echo isset($dados->graduacao_youtube) ? $dados->graduacao_youtube : "" ?>">
									</div>

									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Instagram<span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_instagram" placeholder="admfag" value="<?php echo isset($dados->graduacao_instagram) ? $dados->graduacao_instagram : "" ?>">
									</div>

									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Facebook<span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_facebook" placeholder="admfag" value="<?php echo isset($dados->graduacao_facebook) ? $dados->graduacao_facebook : "" ?>">
									</div>

									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Guia<span class="text-danger"></span></label>
										<input type="number" class="form-control" name="graduacao_guia" placeholder="" value="<?php echo isset($dados->graduacao_guia) ? $dados->graduacao_guia : "0" ?>">
									</div>
								</div>

								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Podcast<span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_podcast" placeholder="PODCast-FAG-Temporada-1---Administrao-el9la4/a-a3jbp8l" value="<?php echo isset($dados->graduacao_podcast) ? $dados->graduacao_podcast : "" ?>">
									</div>

									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Spotify<span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_spotify" placeholder="https://open.spotify.com/show/4sy3AxMqia5ZeIj8bNX2yG?si=ktAJCLhASuOPiou0M-kPSw" value="<?php echo isset($dados->graduacao_spotify) ? $dados->graduacao_spotify : "" ?>">
									</div>

									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Título Blog<span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_blog" placeholder="" value="<?php echo isset($dados->graduacao_blog) ? $dados->graduacao_blog : "" ?>">
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Blog URL<span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_blog_url" placeholder="http://www2.fag.edu.br/blog-arquitetura/" value="<?php echo isset($dados->graduacao_blog_url) ? $dados->graduacao_blog_url : "" ?>">
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
				<div id="panel-2" class="panel">
					<div class="panel-hdr">
						<h2>
							3. Site e servidor
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">

						<div class="panel-content p-0">
							<div class="panel-content">

								<div class="form-row form-group">
									<div class="col-md-3 ">
										<label class="form-label" for="validationCustom02">Cor <span class="text-danger"> *</span></label>
										<input type="color" class="form-control" name="graduacao_cor" placeholder="#FFFFFF" value="<?php echo isset($dados->graduacao_cor) ? "#" . $dados->graduacao_cor : "#000000" ?>" required>
									</div>
									<div class="col-md-3 ">
										<label class="form-label" for="validationCustom02">Ativo <span class="text-danger"></span></label>
										<select name="graduacao_ativo" class="form-control" required>
											<option value="01" <?php echo isset($dados->graduacao_ativo) && $dados->graduacao_ativo == "01" ? "selected" : "" ?>>
												SIM
											</option>
											<option value="00" <?php echo isset($dados->graduacao_ativo) && $dados->graduacao_ativo == "00" ? "selected" : "" ?>>
												NÃO
											</option>
										</select>
									</div>
									<div class="col-md-3 ">
										<label class="form-label" for="validationCustom02">Slug <span class="text-danger"> *</span></label>
										<input type="text" class="form-control" name="graduacao_slug" placeholder="administracao" value="<?php echo isset($dados->graduacao_slug) ? $dados->graduacao_slug : "" ?>" required>
									</div>

									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Diretorio<span class="text-danger"></span></label>
										<input type="text" class="form-control" name="graduacao_diretorio" placeholder="toledo/administracao" value="<?php echo isset($dados->graduacao_diretorio) ? $dados->graduacao_diretorio : "" ?>">
									</div>
								</div>

								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Código<span class="text-danger"> *</span></label>
										<input type="number" class="form-control" name="graduacao_codigo" placeholder="" value="<?php echo isset($dados->graduacao_codigo) ? $dados->graduacao_codigo : "" ?>" required>
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Id Admin Área<span class="text-danger">
												*</span></label>
										<input type="number" class="form-control" name="graduacao_id_admin_area" placeholder="" value="<?php echo isset($dados->graduacao_id_admin_area) ? $dados->graduacao_id_admin_area : "" ?>" required>
									</div>

									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Chave<span class="text-danger"> *</span></label>
										<input type="string" class="form-control" name="graduacao_chave" placeholder="" value="<?php echo isset($dados->graduacao_chave) ? $dados->graduacao_chave : "" ?>" required>
									</div>
								</div>

								<div class="form-row form-group">
									<div class="col-md-3">
										<input type="checkbox" name="graduacao_selos_melhores_universidades" id="graduacao_selos_melhores_universidades" value="1">
										<label for="graduacao_selos_melhores_universidades">
											Selo melhores universidades
										</label>
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
				<div class="form-row form-group">
					<div class="col-md-12 mb-3 d-flex justify-content-end">
						<button class="btn btn-primary ml-3" type="submit">
							Cadastrar Nova Graduação
						</button>
					</div>
				</div>
			</div>

		</div>
	</form>

</main>

<script src="js/formplugins/select2/select2.bundle.js?<?php echo time() ?>"></script>
<script src="js/jquery.maskMoney.min.js?a" type="text/javascript"></script>
<script src="js/moment-with-locales.js?<?php echo time() ?>"></script>
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js?<?php echo time() ?>"></script>
<script src="js/modulos/pos/projeto/list-handler.js?<?php echo time() ?>"></script>
<script src="js/formplugins/summernote/summernote.js?<?php echo time() ?>"></script>

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


	$(document).ready(async function() {

		$('.select2').select2();

		$('.decimal').maskMoney({
			prefix: 'R$ ',
			allowNegative: true,
			thousands: '.',
			decimal: ',',
			affixesStay: false
		});

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

		$("#id_periodo").change(function() {
			carrega_periodo();
		});

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
	document.querySelector("#adicionarAoCurso").addEventListener("click", async (e) => {
		const id = document.querySelector("#disciplinas_select").value

		if (id != null && id != "") {
			let disciplina = null;
			await $.get("modulos/pos/projeto/ajax/api/disciplina.php?id=" + id, (data) => {
				disciplina = JSON.parse(data);
			})

			let itemList = listCreateItem(disciplina)
			document.querySelector("#lista_disciplinas").appendChild(itemList)
		}

		removeListItem();
	})

	$("#form-disciplina").submit(async function(e) {

		e.preventDefault();

		var form = $(this);
		var actionUrl = form.attr('action');

		$.ajax({
			type: "POST",
			url: actionUrl,
			data: form.serialize(),
			success: function(data) {
				alert("Disciplina cadastrada com sucesso!");
			},
			error: function() {
				alert("Não foi possível cadastrar sua disciplina")
			}
		});

		await getDisciplinas();
	});
</script>