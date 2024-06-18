<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 0);

// header('Content-Type: text/html; charset=utf-8');

require_once("php/sqlsrv.php");
require_once("ajax/DocenteController.php");
require_once("ajax/DisciplinaController.php");
require_once("api/controller/CursoController.php");
require_once("api/factory/RepositoryFactory.php");
require_once __DIR__ . '/../../../php/repository/CoopexPessoaRepository.php';

$id_menu = 41;
$chave = "id_projeto";

$tipo_usuario = trim($_SESSION["coopex"]["usuario"]["sistema"]["id_tipo_usuario"]);
$possuiPermissao = isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][1]);
$isAdmin = $_SESSION['coopex']['usuario']['sistema']['tipo_usuario'] == "ADMINISTRADOR";

$factory = new RepositoryFactory();
$coopex_pessoa = new CoopexPessoaRepository();

$curso_controller = new CursoController();
$enviado_aprovacao = false;

$titulacoes = $factory->titulacao->getAll();
$areas = $factory->area->getAll();

try {
  $$chave = $_GET['id'];

  $dados = $curso_controller->getCursoById($$chave);
  $pessoa = $coopex_pessoa->getByIdPessoa($dados->curso->id_pessoa);
  $enviado_aprovacao = isset($dados->enviado_aprovacao) && $dados->enviado_aprovacao == true;
  $isProponente = $dados->curso->id_pessoa == $_SESSION['coopex']['usuario']['id_pessoa'];
  $canAccess = ($tipo_usuario == "17" || $tipo_usuario == "21" || $tipo_usuario == "1") && $possuiPermissao;
} catch (Exception $e) {
  $invalid_url = true;
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

  <?php if (!$canAccess) { ?>
    <div class="alert border-danger bg-transparent text-secondary fade show" role="alert">
      <div class="d-flex align-items-center">
        <div class="alert-icon">
          <span class="icon-stack icon-stack-md">
            <i class="base-7 icon-stack-3x color-danger-900"></i>
            <i class="fal fa-times icon-stack-1x text-white"></i>
          </span>
        </div>
        <div class="flex-1">
          <span class="h5 color-danger-900">Você não tem permissão para acessar este painel</span>
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
        Aprovação de Projeto de Pós-Graduação
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

  <iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 50%; background-color: #fff; top: 0; left: 0; height: 370px"></iframe>

  <form method="post" id="main-form" class="needs-validation" target="dados" action="modulos/pos/projeto/api/routes/create-curso.php">
    <input type="hidden" name="<?php echo $chave ?>" value="<?php echo $$chave ? $$chave : 0 ?>" />
    <input type="hidden" name="id_pessoa" class="form-control" id="id_pessoa" placeholder="" value="<?php echo isset($dados->curso->id_pessoa) ? $dados->curso->id_pessoa : $_SESSION['coopex']['usuario']['id_pessoa'] ?>">
    <input type="hidden" name="id_projeto" class="form-control" id="id_projeto" placeholder="" value="<?php echo isset($dados->curso->id) ? $dados->curso->id : "" ?>">

    <div class="row">
      <div class="col-xl-12">
        <div id="panel-proponent" class="panel">
          <div class="panel-hdr">
            <h2>
              Enviado por
            </h2>
          </div>
          <div class="panel-container show">
            <div class="panel-content p-0">
              <div class="panel-content">
                <ul class="list-group">
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="badge badge-primary badge-pill">Nome</span>
                    <?php echo $pessoa->nome ?>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="badge badge-primary badge-pill">CPF</span>
                    <?php echo $pessoa->cpf ? $pessoa->cpf : "..."; ?>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="badge badge-primary badge-pill">Email</span>
                    <?php echo $pessoa->email ? $pessoa->email : "..."; ?>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="badge badge-primary badge-pill">Usuario de login</span>
                    <?php echo $pessoa->usuario ?>
                  </li>
                </ul>
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
                    <input type="text" class="form-control" name="curso_nome" id="curso_nome" placeholder="Digite aqui o nome do curso" value="<?php echo isset($dados->curso->nome) ? $dados->curso->nome : "" ?>" required readonly>
                    <div class="invalid-feedback">
                      O nome não pode estar vazio
                    </div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label" for="curso_id_area">Área <span class="text-danger">*</span></label>
                    <select id="curso_id_area" name="curso_id_area" class="custom-select2-field form-control" required disabled readonly>
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
                    <select id="campus_id_campus" name="campus_id_campus" class="form-control" required disabled readonly>
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

                <h2>Coordenador</h2>
                <div class="form-row">
                  <div class="col-md-3 mb-3">
                    <label class="form-label" for="custom-select2-field-ajax">
                      Cpf do coordenador <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control mr-2 cpf" name="coordenador_cpf" id="coordenador_cpf" placeholder="Cpf do coordenador" value="<?php echo isset($dados->coordenador->cpf) ? $dados->coordenador->cpf : "" ?>" required readonly />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="custom-select2-field-ajax">
                      Nome do coordenador <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" name="coordenador_nome" id="coordenador_nome" placeholder="Nome do coordenador" value="<?php echo isset($dados->coordenador->nome) ? $dados->coordenador->nome : "" ?>" required readonly />
                  </div>
                  <div class="col-md-3 mb-3">
                    <label class="form-label" for="coordenador_titulacao">Titulação <span class="text-danger">*</span></label>
                    <select id="coordenador_titulacao" name="coordenador_titulacao" class="custom-select2-field form-control" required readonly>
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


                <div class="form-row">
                  <div class="col-xl-12">
                    <label class="form-label" for="parceiros_table">Parceiros</label>
                    <table id="parceiros_table" class="table table-bordered table-hover table-striped w-100"></table>
                    <textarea class="d-none" name="parceiros" id="parceiros" rows="10" cols="100">[]</textarea>
                  </div>
                </div>

                <hr>

                <h2>Operacionaliação</h2>

                <div class="form-row">
                  <div class="col-md-3 mb-3">
                    <label class="form-label" for="curso_carga_horaria">Carga Horária do curso (hr) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="curso_carga_horaria" id="curso_carga_horaria" min="0" placeholder="10h" value="<?php echo isset($dados->curso->carga_horaria) ? $dados->curso->carga_horaria : "" ?>" required readonly disabled>
                    <span class="help-block">
                      Carga horária total do curso em horas
                    </span>
                    <div class="invalid-feedback">
                      A carga horária está inválida
                    </div>
                  </div>
                  <div class="col-md-3 mb-3">
                    <label class="form-label" for="curso_numero_vagas">Número de Vagas <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="curso_numero_vagas" name="curso_numero_vagas" min="0" placeholder="10" value="<?php echo isset($dados->curso->numero_vagas) ? $dados->curso->numero_vagas : "" ?>" required readonly disabled>
                    <div class="invalid-feedback">
                      O número de vagas precisa ser maior que zero
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label" for="realizacao_periodo">Período das aulas <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="realizacao_periodo" id="realizacao_periodo" placeholder="18 meses" value="<?php echo isset($dados->realizacao->periodo) ? $dados->realizacao->periodo : "" ?>" required readonly disabled>
                    <span class="help-block">
                      Período de aulas do curso. EX.: 18 meses
                    </span>
                  </div>
                </div>
                <div class="form-row">
                  <div class="col-md-4 mb-3">
                    <label class="form-label" for="realizacao_dias_semana">Dias da Semana <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="realizacao_dias_semana" name="realizacao_dias_semana" placeholder="Segunda e sexta" value="<?php echo isset($dados->realizacao->dias_semana) ? $dados->realizacao->dias_semana : "" ?>" required readonly disabled>
                    <span class="help-block">
                      Dias em que ocorrerão as aulas
                    </span>
                    <div class="invalid-feedback">
                      Este campo é obrigatório
                    </div>
                  </div>
                  <div class="col-md-4 mb-3">
                    <label class="form-label" for="realizacao_horario">Horário <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="realizacao_horario" id="realizacao_horario" placeholder="Segunda às 18 e sexta às 09" value="<?php echo isset($dados->realizacao->horario) ? $dados->realizacao->horario : "" ?>" required readonly disabled>
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
                      <input type="hidden" id="select_valor_diferente_hidden" name="curso_valor_customizado" value="<?php echo isset($dados->valores->valor_customizado) && $dados->valores->valor_customizado == '1' ? "true" : "false" ?>" readonly disabled>
                      <input onchange="$('#select_valor_diferente_hidden').val(this.checked)" <?php echo isset($dados->valores->valor_customizado) ? "checked" : "" ?> contenteditable="" type="checkbox" class="custom-control-input is-invalid" id="select_valor_diferente" readonly disabled>

                      <label class="custom-control-label" for="select_valor_diferente">Definir valores customizados para os docentes</label>
                    </div>
                  </div>
                </div>
                <div class="form-row form-group">
                  <div class="col-md-2 ">
                    <label class="form-label" for="curso_valor_especialista">Especialista <span class="text-danger">*</span></label>
                    <input <?php echo isset($dados->valores->valor_customizado) && $dados->valores->valor_customizado == 1 ? "" : "disabled" ?> type="text" class="form-control valor_diferente" name="curso_valor_especialista" id="curso_valor_especialista" placeholder="" value="<?php echo isset($dados->valores->valor_customizado) && $dados->valores->valor_customizado == 1 ? ($dados->valores->especialista) : "80" ?>" readonly disabled>
                  </div>
                  <div class="col-md-2 ">
                    <label class="form-label" for="curso_valor_mestre">Mestre <span class="text-danger">*</span></label>
                    <input <?php echo isset($dados->valores->valor_customizado) && $dados->valores->valor_customizado == 1 ? "" : "disabled" ?> type="text" class="form-control valor_diferente" name="curso_valor_mestre" id="curso_valor_mestre" placeholder="" value="<?php echo isset($dados->valores->valor_customizado) && $dados->valores->valor_customizado == 1 ? ($dados->valores->mestre) : "90" ?>" readonly disabled>
                  </div>
                  <div class="col-md-2 ">
                    <label class="form-label" for="curso_valor_doutor">Doutor <span class="text-danger">*</span></label>
                    <input <?php echo isset($dados->valores->valor_customizado) && $dados->valores->valor_customizado == 1 ? "" : "disabled" ?> type="text" class="form-control valor_diferente" name="curso_valor_doutor" id="curso_valor_doutor" placeholder="" value="<?php echo isset($dados->valores->valor_customizado) && $dados->valores->valor_customizado == 1 ? ($dados->valores->doutor) : "110" ?>" readonly disabled>
                  </div>
                </div>

                <div class="form-row">
                  <div class="col-md-12 mb-3">
                    <label class="form-label" for="descricao_projeto_publico_alvo">Público Alvo<span class="text-danger">*</span></label>
                    <textarea type="text" class="form-control" name="descricao_projeto_publico_alvo" id="descricao_projeto_publico_alvo" placeholder="" required readonly disabled><?php echo isset($dados->descricao->publico_alvo) ? $dados->descricao->publico_alvo : "" ?></textarea>
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label" for="descricao_projeto_perfil_aluno">Perfil do aluno<span class="text-danger">*</span></label>
                    <textarea type="text" class="form-control" name="descricao_projeto_perfil_aluno" id="descricao_projeto_perfil_aluno" placeholder="" required readonly disabled><?php echo isset($dados->descricao->perfil_aluno) ? $dados->descricao->perfil_aluno : "" ?></textarea>
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label" for="descricao_projeto_pilares_curso">Pilares do Curso<span class="text-danger">*</span></label>
                    <textarea type="text" class="form-control" name="descricao_projeto_pilares_curso" id="descricao_projeto_pilares_curso" placeholder="" required readonly disabled><?php echo isset($dados->descricao->pilares_curso) ? $dados->descricao->pilares_curso : "" ?></textarea>
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label" for="descricao_projeto_processo_selecao">Processo de seleção<span class="text-danger">*</span></label>
                    <textarea type="text" class="form-control" name="descricao_projeto_processo_selecao" id="descricao_projeto_processo_selecao" placeholder="" required readonly disabled><?php echo isset($dados->descricao->processo_selecao) ? $dados->descricao->processo_selecao : "" ?></textarea>
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
                    <textarea type="text" class="form-control" name="justificativa_descricao" id="justificativa_descricao" placeholder="" required readonly disabled><?php echo isset($dados->justificativa->descricao) ? $dados->justificativa->descricao : "" ?></textarea>
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label" for="justificativa_contribuicao">Contribuição do Curso <span class="text-danger">*</span></label>
                    <textarea type="text" class="form-control" name="justificativa_contribuicao" id="justificativa_contribuicao" placeholder="" required readonly disabled><?php echo isset($dados->justificativa->contribuicao) ? $dados->justificativa->contribuicao : "" ?></textarea>
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
                    <textarea type="text" class="form-control" name="objetivos_geral" id="objetivos_geral" placeholder="" required readonly disabled><?php echo isset($dados->objetivos->geral) ? $dados->objetivos->geral : "" ?></textarea>
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label" for="objetivos_especifico">Específicos <span class="text-danger">*</span></label>
                    <textarea type="text" class="form-control" name="objetivos_especifico" id="objetivos_especifico" placeholder="" required readonly disabled><?php echo isset($dados->objetivos->especifico) ? $dados->objetivos->especifico : "" ?></textarea>
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
              4. Disciplinas
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

                <div class="form-row">
                  <div class="col-md-12">
                    <ul class="list-group" id="lista_disciplinas">
                      <?php if (isset($dados->disciplina)) {
                        $i = 1;
                        $total_horas = 0;
                        foreach ($dados->disciplina as $disciplina) {
                          $total_horas += $disciplina->carga_horaria;
                      ?>
                          <li class="list-group-item border-0 d-flex flex-column justify-content-between align-items-center p-0 mb-3" data-id_titulacao="<?php echo $disciplina->id_titulacao ?>" data-disciplina="<?php echo $disciplina->id ?>" data-list="<?php echo $i++ ?>" data-id="<?php echo $disciplina->id ?>">
                            <ul class="list-group w-100">
                              <li class="list-group-item w-100 d-flex justify-content-between align-items-center">
                                <div><strong>Nome: </strong> <?php echo $disciplina->nome ?></div>
                                <span class="badge badge-primary badge-pill">
                                  <?php echo $disciplina->carga_horaria ?>h
                                </span>
                              </li>
                              <li class="list-group-item">
                                <strong>Ementa: </strong> <?php echo $disciplina->ementa ?>
                              </li>
                              <li class="list-group-item">
                                <strong>Docente: </strong> <?php echo $disciplina->nome_docente ?> - <?php echo $disciplina->titulacao_docente ?>
                              </li>
                            </ul>
                          </li>
                      <?php }
                      } ?>
                    </ul>
                    <h4>Carga Horária Total</h4>
                    <div class="panel-tag">
                      <h4><?php echo $total_horas . 'h'?></h4>
                    </div>
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
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-xl-12">
        <div id="panel-5" class="panel">
          <div class="panel-hdr">
            <h2>
              5. Docentes
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
                  <div class="col-md-12">
                    <ul class="list-group" id="lista_disciplinas">
                      <?php if (isset($dados->docentes)) {
                        foreach ($dados->docentes as $docente) {
                      ?>
                          <li class="list-group-item border-0 d-flex flex-column justify-content-between align-items-center p-0 mb-3">
                            <ul class="list-group w-100">
                              <li class="list-group-item w-100 d-flex justify-content-between align-items-center">
                                <?php echo $docente->nome ?>
                                <span class="badge badge-primary badge-pill">
                                  <?php echo $docente->titulacao_docente ?>
                                </span>
                              </li>
                              <li class="list-group-item">
                                <strong>Anexos: </strong>
                                <?php if (isset($docente->foto) && $docente->foto != null) { ?>
                                  <a target="_blank" href="https://coopex.fag.edu.br/images/pos/professores/foto/<?php echo $docente->foto ?>" class="ml-3">
                                    <button type="button" class="btn btn-sm btn-primary waves-effect waves-themed">
                                      <span class="fal fa-download mr-1"></span>
                                      Foto de perfil
                                    </button>
                                  </a>
                                <? } ?>

                                <?php if (isset($docente->certificado) && $docente->certificado != null) { ?>
                                  <a target="_blank" href="https://coopex.fag.edu.br/arquivos/pos/professores/certificados/<?php echo $docente->certificado ?>" class="ml-3">
                                    <button type="button" class="btn btn-sm btn-primary waves-effect waves-themed">
                                      <span class="fal fa-download mr-1"></span>
                                      Certificado
                                    </button>
                                  </a>
                                <? } ?>

                                <?php if (isset($docente->termo_aceite)) { ?>
                                  <a target="_blank" href="https://coopex.fag.edu.br/arquivos/pos/professores/termo-aceite/<?php echo $docente->termo_aceite ?>" class="ml-3">
                                    <button type="button" class="btn btn-sm btn-primary waves-effect waves-themed">
                                      <span class="fal fa-download mr-1"></span>
                                      Termo aceite
                                    </button>
                                  </a>
                                <? } ?>

                                <?php if (isset($docente->termo_uso_imagem)) { ?>
                                  <a target="_blank" href="https://coopex.fag.edu.br/arquivos/pos/professores/termo-uso-imagem/<?php echo $docente->termo_uso_imagem ?>" class="ml-3">
                                    <button type="button" class="btn btn-sm btn-primary waves-effect waves-themed">
                                      <span class="fal fa-download mr-1"></span>
                                      Termo uso de imagem
                                    </button>
                                  </a>
                                <? } ?>
                              </li>
                            </ul>
                          </li>
                      <?php }
                      } ?>
                    </ul>
                  </div>
                </div>

              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </form>

  <?php if (false && isset($dados) && $dados->curso->id != 0) { ?>

    <form id="mensalidade-form" class="needs-validation" action="modulos/pos/projeto/api/routes/create-mensalidade.php" method="post">
      <input type="hidden" name="id_pessoa" class="form-control" id="id_pessoa" placeholder="" value="<?php echo isset($dados->curso->id) ? $dados->curso->id_pessoa : $_SESSION['coopex']['usuario']['id_pessoa'] ?>">
      <input type="hidden" name="id_projeto" class="form-control" id="id_projeto" placeholder="" value="<?php echo isset($dados->curso->id) ? $dados->curso->id : "" ?>">
      <input type="hidden" name="id_mensalidade" class="form-control" id="id_mensalidade" placeholder="" value="<?php echo isset($dados->mensalidade->id) ? $dados->mensalidade->id : "0" ?>">
      <div class="row">
        <div class="col-xl-12">
          <div id="panel-2" class="panel">
            <div class="panel-hdr">
              <h2>
                6. Mensalidade
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
                    <div class="col-md-3 mb-3">
                      <label class="form-label" for="mensalidade_valor">Mensalidade <span class="text-danger">*</span></label>
                      <input type="text" class="form-control data-money" name="mensalidade_valor" id="mensalidade_valor" min="0" placeholder="300.00" value="<?php echo isset($dados->mensalidade->mensalidade) ? $dados->mensalidade->mensalidade : "" ?>" required>
                      <span class="help-block">
                        Valor da mensalidade em reais (R$)
                      </span>
                      <div class="invalid-feedback">
                        A carga horária está inválida
                      </div>
                    </div>
                    <div class="col-md-3 mb-3">
                      <label class="form-label" for="mensalidade_parcelas">Número de Parcelas <span class="text-danger">*</span></label>
                      <input type="number" class="form-control" id="mensalidade_parcelas" name="mensalidade_parcelas" min="0" placeholder="10" value="<?php echo isset($dados->mensalidade->numero_parcelas) ? $dados->mensalidade->numero_parcelas : "" ?>" required>
                      <div class="invalid-feedback">
                        O número de parcelas precisa ser maior que zero
                      </div>
                    </div>
                  </div>
                </div>
                <div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
                  <button class="btn btn-primary ml-auto" type="submit">
                    Definir mensalidade
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>

  <?php } ?>

  <?php if (false && isset($dados) && $dados->curso->id != 0 && ($tipo_usuario == "17" || $tipo_usuario == "1")) { ?>
    <form id="parecer-coordenacao-form" class="needs-validation" action="modulos/pos/projeto/api/routes/create-parecer.php" method="post">
      <input type="hidden" name="id_pessoa" class="form-control" id="id_pessoa" placeholder="" value="<?php echo isset($dados->curso->id) ? $dados->curso->id_pessoa : $_SESSION['coopex']['usuario']['id_pessoa'] ?>">
      <input type="hidden" name="id_projeto" class="form-control" id="id_projeto" placeholder="" value="<?php echo isset($dados->curso->id) ? $dados->curso->id : "" ?>">
      <input type="hidden" name="parecer_tipo" class="form-control" id="parecer_tipo" placeholder="" value="COORDENACAO" hidden>
      <div class="row">
        <div class="col-xl-12">
          <div id="panel-7" class="panel">
            <div class="panel-hdr">
              <h2>
                7. Parecer Coordenação
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
                      <label class="form-label" for="parecer_parecer">Parecer <span class="text-danger">*</span></label>
                      <select class="form-control" name="parecer_parecer" id="parecer_parecer" required>
                        <option value>- Selecione o parecer</option>
                        <option value="1" <?php echo ($dados->parecer_coordenacao != null) && $dados->parecer_coordenacao->isSelected(1) ? 'selected' : '' ?>>Aprovado</option>
                        <option value="2" <?php echo ($dados->parecer_coordenacao != null) && $dados->parecer_coordenacao->isSelected(2) ? 'selected' : '' ?>>Recusado</option>
                        <option value="3" <?php echo ($dados->parecer_coordenacao != null) && $dados->parecer_coordenacao->isSelected(3) ? 'selected' : '' ?>>Reavaliação</option>
                      </select>
                      <div class="invalid-feedback">
                        Escolha um parecer para este curso
                      </div>
                    </div>
                    <div class="col-md-12">
                      <label class="form-label" for="parecer_observacao">Observação </label>
                      <textarea name="parecer_observacao" id="parecer_observacao" class="form-control"><?php echo $dados->parecer_coordenacao ? $dados->parecer_coordenacao->observacao : '' ?></textarea>
                    </div>
                  </div>
                </div>
                <div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
                  <button class="btn btn-primary ml-auto" type="submit">
                    Definir parecer
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  <?php } ?>

  <?php if (false && isset($dados) && $dados->curso->id !=  0 && ($tipo_usuario == "21" || $tipo_usuario == "1")) { ?>
    <form id="parecer-reitoria-form" class="needs-validation" action="modulos/pos/projeto/api/routes/create-parecer.php" method="post">
      <input type="hidden" name="id_pessoa" class="form-control" id="id_pessoa" placeholder="" value="<?php echo isset($dados->curso->id) ? $dados->curso->id_pessoa : $_SESSION['coopex']['usuario']['id_pessoa'] ?>">
      <input type="hidden" name="id_projeto" class="form-control" id="id_projeto" placeholder="" value="<?php echo isset($dados->curso->id) ? $dados->curso->id : "" ?>">
      <input type="hidden" name="parecer_tipo" class="form-control" id="parecer_tipo" placeholder="" value="REITORIA" hidden>
      <div class="row">
        <div class="col-xl-12">
          <div id="panel-8" class="panel">
            <div class="panel-hdr">
              <h2>
                8. Parecer Pró-reitoria
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
                      <label class="form-label" for="parecer_parecer">Parecer <span class="text-danger">*</span></label>
                      <select class="form-control" name="parecer_parecer" id="parecer_parecer" required>
                        <option value>- Selecione o parecer</option>
                        <option value="1" <?php echo ($dados->parecer_reitoria != null) && $dados->parecer_reitoria->isSelected(1) ? 'selected' : '' ?>>Aprovado</option>
                        <option value="2" <?php echo ($dados->parecer_reitoria != null) && $dados->parecer_reitoria->isSelected(2) ? 'selected' : '' ?>>Recusado</option>
                        <option value="3" <?php echo ($dados->parecer_reitoria != null) && $dados->parecer_reitoria->isSelected(3) ? 'selected' : '' ?>>Reavaliação</option>
                      </select>
                      <div class="invalid-feedback">
                        Escolha um parecer para este curso
                      </div>
                    </div>
                    <div class="col-md-12">
                      <label class="form-label" for="parecer_observacao">Observação </label>
                      <textarea name="parecer_observacao" id="parecer_observacao" class="form-control"><?php echo $dados->parecer_reitoria ? $dados->parecer_reitoria->observacao : '' ?></textarea>
                    </div>
                  </div>
                </div>
                <div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
                  <button class="btn btn-primary ml-auto" type="submit">
                    Definir parecer
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  <?php } ?>

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
<script src="js/modulos/pos/projeto/data-table-disciplinas.js?<?php echo time() ?>"></script>
<script src="js/modulos/pos/projeto/toggle-campus.js?<?php echo time() ?>"></script>

<script>
  function aprovacao_indeferido() {
    $("#aprovacao_motivo").prop("disabled", false);
    $("#aprovacao_motivo").prop("required", true);
    $("#aprovacao_observacao").show();
  }

  function aprovacao_deferido() {
    $("#aprovacao_motivo").prop("disabled", true);
    $("#aprovacao_motivo").prop("required", false);
    $("#aprovacao_observacao").hide();
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

    $('.custom-select2-field').select2();

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

    calculatePorcentagemDocente();
    createDataTableParceiros(false)

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