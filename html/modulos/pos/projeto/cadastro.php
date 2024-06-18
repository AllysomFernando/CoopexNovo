<?php
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 0);

// header('Content-Type: text/html; charset=utf-8');

require_once("php/sqlsrv.php");
require_once("ajax/DocenteController.php");
require_once("ajax/DisciplinaController.php");
require_once("api/controller/CursoController.php");
require_once("api/repository/coopex/AreaRepository.php");
require_once("api/factory/RepositoryFactory.php");
require_once("api/models/Area.php");

$id_menu = 41;
$chave = "id_projeto";

$tipo_usuario = trim($_SESSION["coopex"]["usuario"]["sistema"]["id_tipo_usuario"]);
$possuiPermissao = isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][1]);
$isAdmin = $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == "1";
$factory = new RepositoryFactory();

$docente_controller = new DocenteController($coopex);
$disciplina_controller = new DisciplinaController($coopex);
$curso_controller = new CursoController();
$enviado_aprovacao = false;

$titulacoes = $factory->titulacao->getAll();
$areas = $factory->area->getAll();

// echo $_SESSION['coopex']['usuario']['id_pessoa'];

if (isset($_GET['id'])) {
	$$chave = $_GET['id'];

	$dados = $curso_controller->getCursoById($$chave);
	$enviado_aprovacao = isset($dados->enviado_aprovacao) && $dados->enviado_aprovacao == true;
	$isProponente = $dados->curso->id_pessoa == $_SESSION['coopex']['usuario']['id_pessoa'];
} else {
	$$chave = 0;
}

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css?<php echo time() ?>">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/summernote/summernote.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/cropperjs/cropper.css">
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" media="screen, print" href="css/fa-solid.css">
<script src="js/core.js?<?php echo time() ?>"></script>

<main id="js-page-content" role="main" class="page-content">

	<?php if (!$possuiPermissao) { ?>
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

	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Projeto de Pós-Graduação</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID.
				<?php echo $id_menu ?>c
			</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-repeat'></i> Projeto de Pós-Graduação
			<small>
				Cadastro de Projeto de Pós-Graduação
			</small>
		</h1>
	</div>

	<?php if (isset($_GET['id'])) {  ?>
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
						<span class="h5">Status de aprovação do projeto</span>
						<br><br>
						<ol>
							<?php
							foreach ($dados->historico_parecer as $parecer) { ?>
								<li><b><?php echo $parecer->data ?></b> - <?php echo $parecer->descricao ?> <strong>(<?php echo $parecer->usuario ?>)</strong></li>
							<? } ?>
						</ol>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
	?>

	<?php if ($$chave == 0) { ?>
		<div class="row">
			<div class="col-xl-12">
				<div id="panel-termos" class="panel">
					<div class="panel-hdr">
						<h2>
							Termos e condições
						</h2>
					</div>
					<div class="panel-container show">
						<div class="panel-content">
							<div class="panel-tag mb-0">
								<p>
									A Pós-Graduação do Centro Universitário Assis Gurgacz e da Faculdade Assis Gurgacz agradece seu interesse em propor um projeto de especialização lato sensu. Para garantir a conformidade com nossos padrões, solicitamos que leia cuidadosamente o edital antes de prosseguir com o preenchimento e a submissão do projeto. É fundamental que esteja plenamente ciente das normas estabelecidas.
								</p>
							</div>
							<div class="panel-content">
								<a href="https://coopex.fag.edu.br/arquivos/pos/Edital_chamada_de_projetos_PPGFAG_2024.pdf">Clique aqui para ter acesso ao edital</a>
							</div>
							<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="termos_condicoes" value="">
									<label class="custom-control-label" for="termos_condicoes">Li e concordo com os termos</label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>

	<iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 50%; background-color: #fff; top: 0; left: 0; height: 370px"></iframe>

	<form method="post" id="main-form" class="needs-validation" target="dados" action="modulos/pos/projeto/api/routes/create-curso.php" style="display: <?php echo $$chave ? 'block' : 'none' ?>;">
		<input type="hidden" name="<?php echo $chave ?>" value="<?php echo $$chave ? $$chave : 0 ?>" />
		<input type="hidden" name="id_pessoa" class="form-control" id="id_pessoa" placeholder="" value="<?php echo isset($dados->curso->id_pessoa) ? $dados->curso->id_pessoa : $_SESSION['coopex']['usuario']['id_pessoa'] ?>">
		<input type="hidden" name="id_projeto" class="form-control" id="id_projeto" placeholder="" value="<?php echo isset($dados->curso->id) ? $dados->curso->id : "" ?>">

		<div class="row">
			<div class="col-xl-12">
				<div id="panel-2" class="panel">
					<div class="panel-hdr">
						<h2>
							1. Dados do Curso
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" type="button" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" type="button" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="form-row">
									<div class="col-md-8 mb-3">
										<label class="form-label" for="curso_nome">Nome do Curso <span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="curso_nome" id="curso_nome" placeholder="Digite aqui o nome do curso" value="<?php echo isset($dados->curso->nome) ? $dados->curso->nome : "" ?>" required>
										<div class="invalid-feedback">
											O nome não pode estar vazio
										</div>
									</div>
									<div class="col-md-4 mb-3">
										<label class="form-label" for="curso_id_area">Área <span class="text-danger">*</span></label>
										<select id="curso_id_area" name="curso_id_area" class="custom-select2-field form-control" required>
											<option value="">Selecione a Área</option>
											<?php foreach ($areas as $area) { ?>
												<option <?php echo isset($dados) && $area->isSelected($dados->curso->id_area) ? "selected" : "" ?> value="<?php echo $area->id_area ?>">
													<?php echo $area->area ?>
												</option>
											<?php
											}
											?>
										</select>
										<div class="invalid-feedback">
											Selecione a área do curso
										</div>
									</div>
									<div class="col-md-4 mb-3">
										<label class="form-label" for="campus_id_campus">Campus <span class="text-danger">*</span></label>
										<select id="campus_id_campus" name="campus_id_campus" class="form-control" required>
											<option <?php echo isset($dados) && $dados->curso->isCampusSelected(1) ? "selected" : "" ?> value="1">Cascavel</option>
											<option <?php echo isset($dados) && $dados->curso->isCampusSelected(2) ? "selected" : "" ?> value="2">Toledo</option>
										</select>
										<div class="invalid-feedback">
											Selecione o campus do curso
										</div>
									</div>
									<div class="col-md-8 mb-3">
										<label class="form-label" for="realizacao_local">Local <span class="text-danger">*</span></label>
										<input type="text" class="form-control" id="realizacao_local" name="realizacao_local" placeholder="" value="<?php echo isset($dados->local) ? $dados->local : "Centro Universitário da Fundação Assis Gurgacz - FAG" ?>" readonly required>
										<div class="invalid-feedback">
											O local não pode estar vazio
										</div>
									</div>
								</div>

								<hr>

								<h2>Proponentes</h2>
								<div class="form-row">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="proponente_instituicao">Instituição <span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="proponente_instituicao" id="proponente_instituicao" placeholder="Centro Universitário Fundação Assis Gurgacz - FAG" value="<?php echo isset($dados->proponentes->instituicao) ? $dados->proponentes->instituicao : "Centro Universitário Fundação Assis Gurgacz - FAG" ?>" readonly>
									</div>
									<div class="col-md-12 mb-3">
										<label class="form-label" for="proponente_coordenacao">Coordenação Institucional <span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="proponente_coordenacao" id="proponente_coordenacao" placeholder="Coordenação de Pós-Graduação da FAG - CPG" value="<?php echo isset($dados->proponentes->coordenacao_institucional) ? $dados->proponentes->coordenacao_institucional : "Coordenação de Pós-Graduação da FAG - CPG" ?>" readonly>
									</div>
								</div>

								<hr>
								<h2>Coordenador</h2>
								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="custom-select2-field-ajax">
											Cpf do coordenador <span class="text-danger">*</span>
										</label>
										<input type="text" class="form-control mr-2 cpf" name="coordenador_cpf" id="coordenador_cpf" placeholder="Cpf do coordenador" value="<?php echo isset($dados->coordenador->cpf) ? $dados->coordenador->cpf : "" ?>" required />
									</div>
									<div class="col-md-6 mb-3">
										<label class="form-label" for="custom-select2-field-ajax">
											Nome do coordenador <span class="text-danger">*</span>
										</label>
										<input type="text" class="form-control" name="coordenador_nome" id="coordenador_nome" placeholder="Nome do coordenador" value="<?php echo isset($dados->coordenador->nome) ? $dados->coordenador->nome : "" ?>" required />
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="coordenador_titulacao">Titulação <span class="text-danger">*</span></label>
										<select id="coordenador_titulacao" name="coordenador_titulacao" class="custom-select2-field form-control" required>
											<option value="">Selecione a titulação do coordenador</option>
											<?php foreach ($titulacoes as $titulacao) { ?>
												<option <?php echo isset($dados->coordenador) && $dados->coordenador && $titulacao->isSelected($dados->coordenador->titulacao) ? "selected" : "" ?> value="<?php echo $titulacao->id_titulacao ?>">
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

								<hr>

								<h2>Parceiros</h2>
								<div class="form-row">
									<div class="col-xl-12">
										<table id="parceiros_table" class="table table-bordered table-hover table-striped w-100"></table>

										<textarea class="d-none" name="parceiros" id="parceiros" rows="10" cols="100">[]</textarea>
									</div>
								</div>

								<hr>

								<h2>Operacionaliação</h2>
								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="curso_carga_horaria">Carga Horária do curso (hr) <span class="text-danger">*</span></label>
										<input type="number" class="form-control" name="curso_carga_horaria" id="curso_carga_horaria" min="0" placeholder="10h" value="<?php echo isset($dados->curso->carga_horaria) ? $dados->curso->carga_horaria : "" ?>" required>
										<span class="help-block">
											Carga horária total do curso em horas
										</span>
										<div class="invalid-feedback">
											A carga horária está inválida
										</div>
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="curso_numero_vagas">Número de Vagas <span class="text-danger">*</span></label>
										<input type="number" class="form-control" id="curso_numero_vagas" name="curso_numero_vagas" min="0" placeholder="10" value="<?php echo isset($dados->curso->numero_vagas) ? $dados->curso->numero_vagas : "" ?>" required>
										<div class="invalid-feedback">
											O número de vagas precisa ser maior que zero
										</div>
									</div>
									<div class="col-md-6 mb-3">
										<label class="form-label" for="realizacao_periodo">Período das aulas <span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="realizacao_periodo" id="realizacao_periodo" placeholder="18 meses" value="<?php echo isset($dados->realizacao->periodo) ? $dados->realizacao->periodo : "" ?>" required>
										<span class="help-block">
											Período de aulas do curso. EX.: 18 meses
										</span>
									</div>
								</div>
								<div class="form-row">
									<div class="col-md-4 mb-3">
										<label class="form-label" for="realizacao_dias_semana">Dias da Semana <span class="text-danger">*</span></label>
										<input type="text" class="form-control" id="realizacao_dias_semana" name="realizacao_dias_semana" placeholder="Segunda e sexta" value="<?php echo isset($dados->realizacao->dias_semana) ? $dados->realizacao->dias_semana : "" ?>" required>
										<span class="help-block">
											Dias em que ocorrerão as aulas
										</span>
										<div class="invalid-feedback">
											Este campo é obrigatório
										</div>
									</div>
									<div class="col-md-4 mb-3">
										<label class="form-label" for="realizacao_horario">Horário <span class="text-danger">*</span></label>
										<input type="text" class="form-control" name="realizacao_horario" id="realizacao_horario" placeholder="Segunda às 18 e sexta às 09" value="<?php echo isset($dados->realizacao->horario) ? $dados->realizacao->horario : "" ?>" required>
										<span class="help-block">
											Horários que ocorrerão as aulas
										</span>
										<div class="invalid-feedback">
											Este campo é obrigatório
										</div>
									</div>
								</div>

								<div class="form-row form-group">
									<div class="col-md-6">
										<div class="custom-control custom-switch">
											<input type="hidden" id="select_valor_diferente_hidden" name="curso_valor_customizado" value="<?php echo isset($dados->valores->valor_customizado) && $dados->valores->valor_customizado == '1' ? "true" : "false" ?>">
											<input onchange="$('#select_valor_diferente_hidden').val(this.checked)" <?php echo isset($dados->valores->valor_customizado) ? "checked" : "" ?> contenteditable="" type="checkbox" class="custom-control-input is-invalid" id="select_valor_diferente">

											<label class="custom-control-label" for="select_valor_diferente">Definir valores customizados para os docentes</label>
										</div>
									</div>
								</div>
								<div class="form-row form-group">
									<div class="col-md-2 ">
										<label class="form-label" for="curso_valor_especialista">Especialista <span class="text-danger">*</span></label>
										<input <?php echo isset($dados->valores->valor_customizado) && $dados->valores->valor_customizado == 1 ? "" : "disabled" ?> type="text" class="form-control valor_diferente" name="curso_valor_especialista" id="curso_valor_especialista" placeholder="" value="<?php echo isset($dados->valores->valor_customizado) && $dados->valores->valor_customizado == 1 ? ($dados->valores->especialista) : "80" ?>">
									</div>
									<div class="col-md-2 ">
										<label class="form-label" for="curso_valor_mestre">Mestre <span class="text-danger">*</span></label>
										<input <?php echo isset($dados->valores->valor_customizado) && $dados->valores->valor_customizado == 1 ? "" : "disabled" ?> type="text" class="form-control valor_diferente" name="curso_valor_mestre" id="curso_valor_mestre" placeholder="" value="<?php echo isset($dados->valores->valor_customizado) && $dados->valores->valor_customizado == 1 ? ($dados->valores->mestre) : "90" ?>">
									</div>
									<div class="col-md-2 ">
										<label class="form-label" for="curso_valor_doutor">Doutor <span class="text-danger">*</span></label>
										<input <?php echo isset($dados->valores->valor_customizado) && $dados->valores->valor_customizado == 1 ? "" : "disabled" ?> type="text" class="form-control valor_diferente" name="curso_valor_doutor" id="curso_valor_doutor" placeholder="" value="<?php echo isset($dados->valores->valor_customizado) && $dados->valores->valor_customizado == 1 ? ($dados->valores->doutor) : "110" ?>">
									</div>
								</div>

								<div class="form-row">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="descricao_projeto_publico_alvo">Público Alvo <span class="text-danger">*</span></label>
										<textarea type="text" style="min-height: 100px;" class="form-control" name="descricao_projeto_publico_alvo" id="descricao_projeto_publico_alvo" placeholder="" required><?php echo isset($dados->descricao->publico_alvo) ? $dados->descricao->publico_alvo : "" ?></textarea>
									</div>
									<div class="col-md-12 mb-3">
										<label class="form-label" for="descricao_projeto_perfil_aluno">Perfil do aluno <span class="text-danger">*</span></label>
										<textarea type="text" class="form-control" style="min-height: 100px;" name="descricao_projeto_perfil_aluno" id="descricao_projeto_perfil_aluno" placeholder="" required><?php echo isset($dados->descricao->perfil_aluno) ? $dados->descricao->perfil_aluno : "" ?></textarea>
									</div>
									<div class="col-md-12 mb-3">
										<label class="form-label" for="descricao_projeto_pilares_curso">Pilares do Curso <span class="text-danger">*</span></label>
										<textarea type="text" class="form-control" style="min-height: 100px;" name="descricao_projeto_pilares_curso" id="descricao_projeto_pilares_curso" placeholder="" required><?php echo isset($dados->descricao->pilares_curso) ? $dados->descricao->pilares_curso : "" ?></textarea>
									</div>
									<div class="col-md-12 mb-3">
										<label class="form-label" for="descricao_projeto_processo_selecao">Processo de seleção <span class="text-danger">*</span></label>
										<textarea type="text" class="form-control" style="min-height: 100px;" name="descricao_projeto_processo_selecao" id="descricao_projeto_processo_selecao" placeholder="" required><?php echo isset($dados->descricao->processo_selecao) ? $dados->descricao->processo_selecao : "" ?></textarea>
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
							2. Justificativa de Oferta do Curso
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" type="button" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" type="button" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="form-row">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="justificativa_descricao">Descrição <span class="text-danger">*</span></label>
										<textarea type="text" class="form-control" style="min-height: 100px;" name="justificativa_descricao" id="justificativa_descricao" placeholder="" required><?php echo isset($dados->justificativa->descricao) ? $dados->justificativa->descricao : "" ?></textarea>
									</div>
									<div class="col-md-12 mb-3">
										<label class="form-label" for="justificativa_contribuicao">Contribuição do Curso <span class="text-danger">*</span></label>
										<textarea type="text" class="form-control" style="min-height: 100px;" name="justificativa_contribuicao" id="justificativa_contribuicao" placeholder="" required><?php echo isset($dados->justificativa->contribuicao) ? $dados->justificativa->contribuicao : "" ?></textarea>
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
							3. Objetivos
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" type="button" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" type="button" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="form-row">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="objetivos_geral">Gerais <span class="text-danger">*</span></label>
										<textarea type="text" class="form-control" style="min-height: 100px;" name="objetivos_geral" id="objetivos_geral" placeholder="" required><?php echo isset($dados->objetivos->geral) ? $dados->objetivos->geral : "" ?></textarea>
									</div>
									<div class="col-md-12 mb-3">
										<label class="form-label" for="objetivos_especifico">Específicos <span class="text-danger">*</span></label>
										<textarea type="text" class="form-control" style="min-height: 100px;" name="objetivos_especifico" id="objetivos_especifico" placeholder="" required><?php echo isset($dados->objetivos->especifico) ? $dados->objetivos->especifico : "" ?></textarea>
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
				<div id="panel-4" class="panel">
					<div class="panel-hdr">
						<h2>
							4. Estrutura Curricular
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" type="button" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" type="button" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>

					<div class="panel-container show">
						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="panel-tag">
									<span>Mestres e doutores devem constituir ao menos 30% dos docentes do curso</span>
									<h4 class="mt-3">Atualmente constituem: <span id="porcentagem-docente"></span></h4>
								</div>

								<div class="w-100 d-flex justify-content-center align-items-end flex-nowrap mb-3">
									<div class="w-100 mr-2">
										<div class="invalid-feedback">
											Selecione a disciplina do curso
										</div>
										<label class="form-label" for="validationCustom03">Disciplinas<span class="text-danger">*</span></label>
										<select id="disciplinas_select" class="custom-select2-field form-control mr-3">
										</select>
									</div>
									<div class="btn btn-primary mr-2" id="adicionarAoCurso" data-toggle="tooltip" title="Adicionar esta disciplina ao curso"><i class="fal fa-indent"></i></div>
									<div class="btn btn-success" data-toggle="modal" data-target="#nova_disciplina_modal" title="Cadastrar uma nova disciplina"><i class="fal fa-plus"></i></div>
								</div>

								<div class="form-row">
									<div class="col-md-12">
										<ul class="list-group" id="lista_disciplinas">
											<?php if (isset($dados->disciplina)) {
												$i = 1;
												foreach ($dados->disciplina as $disciplina) { ?>
													<li class="list-group-item d-flex justify-content-between align-items-center" data-id_titulacao="<?php echo $disciplina->id_titulacao ?>" data-disciplina="<?php echo $disciplina->id ?>" data-list="<?php echo $i++ ?>" data-id="<?php echo $disciplina->id ?>">
														<div class="w-25">
															<?php echo $disciplina->nome ?>
														</div>
														<span class="badge badge-primary badge-pill">
															<?php echo $disciplina->carga_horaria ?>h
														</span>
														<button type="button" class="btn btn-danger removerDoCurso"><i class="fal fa-trash-alt"></i></button>
													</li>
											<?php }
											} ?>
										</ul>
										<textarea class="d-none" name="disciplinas" id="disciplinas_log" rows="10" cols="100">
										<?php if (isset($dados->disciplina)) {
											$json_arr = [];
											$i = 1;
											foreach ($dados->disciplina as $disciplina) {
												$json = $disciplina;
												$json->listIndex = $i++;
												$json->acao = "INSERT";
												array_push($json_arr, $json);
											}
											echo json_encode($json_arr);
										} else { ?>
										[]
										<?php } ?>
										</textarea>
									</div>
								</div>

							</div>
						</div>
					</div>

					<div class="panel-container show">
						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="form-row">
								</div>
								<div class="form-row form-group">
								</div>
							</div>
						</div>
						<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center justify-content-between col-xl-12 w-100">
							<?php if (!$enviado_aprovacao) { ?>
								<div class="custom-control custom-checkbox" id="aprovacao_check">
									<input type="checkbox" class="custom-control-input" id="invalidCheck2" value="1" name="enviar_aprovacao">
									<label class="custom-control-label" for="invalidCheck2">Enviar para aprovação</label>
								</div>
								<div class="d-flex justify-content-end align-items-end flex-nowrap col-md-4 g-3">
									<button class="btn btn-outline-primary" type="button" id="save-local-storage">
										Salvar para depois
									</button>
									<button class="btn btn-primary ml-2" type="submit">
										<?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar" ?>
									</button>
								</div>
							<?php } else { ?>
								<span>O projeto foi enviado para aprovação e não pode mais ser editado</span>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>

	<?php require 'partials/modal_nova_disciplina.php'; ?>

	<?php require 'partials/modal_novo_docente.php'; ?>

</main>

<script src="js/formplugins/select2/select2.bundle.js?<?php echo time() ?>"></script>
<script src="js/jquery.maskMoney.min.js?<?php echo time() ?>" type="text/javascript"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js?<?php echo time() ?>"></script>
<script src="js/moment-with-locales.js?<?php echo time() ?>"></script>
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js?<?php echo time() ?>"></script>
<script src="js/formplugins/summernote/summernote.js?<?php echo time() ?>"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/datagrid/datatables/dataTables.editor.min.js"></script>

<script src="js/modulos/pos/projeto/list-handler.js?<?php echo time() ?>"></script>
<script src="js/modulos/pos/projeto/data-table-parceiros.js?<?php echo time() ?>"></script>
<script src="js/modulos/pos/projeto/toggle-campus.js?<?php echo time() ?>"></script>
<script src="js/modulos/pos/projeto/termos.js?<?php echo time() ?>"></script>
<script src="js/modulos/pos/projeto/cache-form.js?<?php echo time() ?>"></script>

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
				$location = "https://coopex.fag.edu.br/pos/projeto/consulta";
				if (!isset($_GET['id'])) {
					echo "document.location.href = '{$location}'";
				} else {
					echo "document.location.reload(true);";
				}
				?>
			}
		});
	}

	//MENSAGEM DE FALHA NO CADASTRO
	function cadastroFalha(message) {
		var msg = message ? message : "Ocorreu um erro inesperado";
		Swal.fire({
			type: "error",
			title: msg,
			showConfirmButton: false,
			timer: 1500
		});
	}

	//HABILITA OS CAMPOS REFERENTES AO PERÍODO DIFERENTE DO PRE-DEFINIDO
	function valor_diferente() {
		if ($("#select_valor_diferente").prop('checked')) {
			$(".valor_diferente").attr("disabled", false);
			$(".valor_diferente").attr("required", true);
		} else {
			$(".valor_diferente").attr("disabled", true);
			$(".valor_diferente").attr("required", false);
			$("#pre_inscricao_data_inicial").focus();
		}
	}


	$(document).ready(async function() {

		await loadFormFromCache();
		checkTermos();

		$('.custom-select2-field').select2();

		$('#novo_disciplina_id_docente').select2({
			dropdownParent: $("#nova_disciplina_modal")
		});

		$('#docentes_select').select2({
			dropdownParent: $("#nova_disciplina_modal")
		});

		$('#docente_titulacao').select2({
			dropdownParent: $("#novo_docente_modal")
		});

		$(":input").inputmask();
		$(".cpf").inputmask("999.999.999-99");

		$(".js-consultar-usuario").select2({
			ajax: {
				url: "modulos/estagio/cadastro/ajax/buscar_professor.php",
				dataType: 'json',
				delay: 250,
				data: function(params) {
					return {
						q: params.term, // search term
						page: params.page
					};
				},
				processResults: function(data, params) {
					params.page = params.page || 1;

					return {
						results: data.items,
						pagination: {
							more: (params.page * 30) < data.total_count
						}
					};
				},
				cache: true
			},
			placeholder: 'Buscar no banco de dados',
			escapeMarkup: function(markup) {
				return markup;
			},
			minimumInputLength: 3,
			templateResult: formatoUsuario,
			templateSelection: formatoTextoUsuario
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

		await getDisciplinasOptions();
		await getDocentesOptions();
		createDataTableParceiros(true)

		$(".valor_diferente").inputmask({
			alias: 'numeric',
			groupSeparator: ',',
			digits: 2,
			digitsOptional: false,
			prefix: '',
			placeholder: '0'
		});

		$('.data-money').maskMoney({
			prefix: 'R$ ',
			allowNegative: false,
			thousands: '.',
			decimal: ',',
			affixesStay: false
		});

		$("#select_valor_diferente").change(function() {
			valor_diferente();
		});

		calculatePorcentagemDocente();

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
	const listHandlerDocente = new ListHandler({
		htmlListElementId: "docentes_list",
		htmlLogElementId: "docentes_log",
		deleteIdentifierClassName: "removerDaDisciplina",
	});

	const listHandlerDisciplina = new ListHandler({
		htmlListElementId: "lista_disciplinas",
		htmlLogElementId: "disciplinas_log",
		deleteIdentifierClassName: "removerDoCurso",
	});

	document.querySelectorAll('.removerDoCurso').forEach(element => {
		listHandlerDisciplina.deleteListener(element)
	})

	document.querySelector("#adicionarAoCurso").addEventListener("click", async (e) => {
		const select = document.querySelector("#disciplinas_select")
		const selectedOption = select.options[select.selectedIndex]

		const logObject = {
			id: selectedOption.value,
			carga_horaria: selectedOption.getAttribute('data-carga_horaria'),
			nome: selectedOption.getAttribute('data-nome'),
			id_titulacao: selectedOption.getAttribute('data-id_titulacao'),
		}

		const listItemData = {
			badge: logObject.carga_horaria + "h",
			inputValue: logObject.id,
			displayName: logObject.nome,
			attributes: [
				['id', logObject.id],
				['id_titulacao', logObject.id_titulacao],
				['disciplina', logObject.id],
			]
		}

		let itemList = listHandlerDisciplina.createItem(listItemData)
		if (itemList) {
			listHandlerDisciplina.appendItem(itemList)
			listHandlerDisciplina.appendToLog(logObject)
		}

		calculatePorcentagemDocente();
	})

	document.querySelector("#adicionarAdisciplina").addEventListener("click", async (e) => {
		const select = document.querySelector("#docentes_select")
		const selectedOption = select.options[select.selectedIndex]

		const listItemData = {
			badge: selectedOption.getAttribute('data-titulacao'),
			inputValue: selectedOption.value,
			displayName: selectedOption.getAttribute('data-nome'),
			attributes: [
				['id', selectedOption.value,
					'docente', selectedOption.value
				],
			]
		}

		const logObject = {
			id: selectedOption.value,
			id_titulacao: selectedOption.getAttribute('data-id_titulacao'),
			nome: selectedOption.getAttribute('data-nome')
		}

		let itemList = listHandlerDocente.createItem(listItemData)
		if (itemList) {
			listHandlerDocente.appendItem(itemList)
			listHandlerDocente.appendToLog(logObject)
		}
	})

	$("#form-disciplina").submit(async function(e) {

		e.preventDefault();
		const hasItems = listHandlerDocente.hasItems();

		if (!hasItems) {
			alert('A disciplina precisa ter pelo menos um docente')
			return
		}

		var form = $(this);
		var actionUrl = form.attr('action');

		$.ajax({
			type: "POST",
			url: actionUrl,
			data: form.serialize(),
			success: async function(data) {
				alert("Disciplina cadastrada com sucesso!");
				$("#nova_disciplina_modal").modal('hide');
				await getDisciplinasOptions();
			},
			error: function() {
				alert("Não foi possível cadastrar sua disciplina")
			}
		});

		await getDisciplinasOptions();
	});

	$("#form-docente").submit(async function(e) {

		e.preventDefault();

		var form = new FormData(this);

		$.ajax({
			type: "POST",
			url: "modulos/pos/projeto/api/routes/novo-docente.php",
			data: form,
			cache: false,
			contentType: false,
			processData: false,
			success: async function(data) {
				alert("Docente cadastrado com sucesso!");
				$("#novo_docente_modal").modal('hide');
			},
			error: function(xhr) {
				alert(xhr.responseJSON.error)
			}
		});

		await getDocentesOptions();
	});

	$("#main-form").submit(async function(e) {

		e.preventDefault();

		const disciplinas = document.querySelector("#lista_disciplinas")
		const porcentagemDocente = document.querySelector("#porcentagem-docente").innerHTML.replace("%", "")

		if (Number(porcentagemDocente) < 30) {
			alert("Mestres e doutores devem constituir ao menos 30% dos docentes do curso")
			return;
		}

		if (disciplinas.children.length < 1) {
			alert("É necessário ter ao menos uma disciplina anexada");
			return;
		}

		var form = $(this);
		var actionUrl = form.attr('action');

		// console.log(form)
		// return;

		$.ajax({
			type: "POST",
			url: actionUrl,
			data: form.serialize(),
			success: async function(data) {
				deleteFormFromCache()
				await deleteFormFromDatabase()
				localStorage.removeItem('disciplinas')
				// console.log(data)
				cadastroOK(1)
			},
			error: function(xhr) {
				const xhrMessage = JSON.parse(xhr.responseText)
				const mensagem = xhrMessage.error ? xhrMessage.error : xhrMessage.message
				cadastroFalha(mensagem)
			}
		});

		await getDisciplinasOptions();
	});

	$("#mensalidade-form").submit(async function(e) {

		e.preventDefault();

		var form = $(this);
		var actionUrl = form.attr('action');

		$.ajax({
			type: "POST",
			url: actionUrl,
			data: form.serialize(),
			success: function(data) {
				console.log(data);
				cadastroOK(1)
			},
			error: function() {
				cadastroFalha(1)
			}
		});
	});

	$("#parecer-coordenacao-form").submit(async function(e) {

		e.preventDefault();

		var form = $(this);
		var actionUrl = form.attr('action');

		$.ajax({
			type: "POST",
			url: actionUrl,
			data: form.serialize(),
			success: function(data) {
				console.log(data);
				cadastroOK(1)
			},
			error: function() {
				cadastroFalha(1)
			}
		});
	});

	$("#parecer-reitoria-form").submit(async function(e) {

		e.preventDefault();

		var form = $(this);
		var actionUrl = form.attr('action');

		$.ajax({
			type: "POST",
			url: actionUrl,
			data: form.serialize(),
			success: function(data) {
				console.log(data);
				cadastroOK(1)
			},
			error: function() {
				cadastroFalha(1)
			}
		});
	});
</script>