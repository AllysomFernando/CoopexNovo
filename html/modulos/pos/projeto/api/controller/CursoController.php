<?php

require_once __DIR__ . "/../factory/RepositoryFactory.php";
require_once __DIR__ . "/../usecases/CreateCursoUseCase.php";
require_once __DIR__ . "/../usecases/UpdateCursoUseCase.php";
require_once __DIR__ . "/../usecases/CreateMensalidadeUseCase.php";
require_once __DIR__ . "/../usecases/UpdateMensalidadeUseCase.php";
require_once __DIR__ . "/../usecases/CreateParecerUseCase.php";
require_once __DIR__ . "/../models/Curso.php";

class CursoController
{

  public $repositories;
  public $create_curso_use_case;
  public $update_curso_use_case;
  public $create_mensalidade_use_case;
  public $update_mensalidade_use_case;
  public $create_parecer_use_case;

  public function __construct()
  {
    $this->repositories = new RepositoryFactory();
    $this->create_curso_use_case = new CreateCursoUseCase();
    $this->update_curso_use_case = new UpdateCursoUseCase();
    $this->create_mensalidade_use_case = new CreateMensalidadeUseCase();
    $this->update_mensalidade_use_case = new UpdateMensalidadeUseCase();
    $this->create_parecer_use_case = new CreateParecerUseCase();
  }

  public function createCurso($body)
  {
    $inserted = $this->create_curso_use_case->execute($body);

    return $inserted;
  }

  public function getCursoById($id)
  {
    $exists = $this->repositories->curso->existsById($id);

    if (!$exists) {
      throw new Exception("Curso não encontrado");
    }

    $response = new stdClass();
    $response->curso = $this->repositories->curso->getById($id);
    $response->coordenador = $this->repositories->coordenador->getByCursoId($id);
    $response->descricao = $this->repositories->descricao_curso->getByCursoId($id);
    $response->justificativa = $this->repositories->justificativa->getByCursoId($id);
    $response->objetivos = $this->repositories->objetivos->getByCursoId($id);
    $response->enviado_aprovacao = $this->repositories->parecer_curso->isEnviadoParaAprovacao($id);
    $response->historico_parecer = $this->repositories->parecer_curso->getHistoricoByCursoId($id);
    $response->parecer_coordenacao = $this->repositories->parecer_curso->getParecerCoordenacaoByCursoId($id);
    $response->parecer_reitoria = $this->repositories->parecer_curso->getParecerReitoriaByCursoId($id);
    $response->proponentes = $this->repositories->proponentes->getByCursoId($id);
    $response->realizacao = $this->repositories->realizacao->getByCursoId($id);
    $response->disciplina = $this->repositories->disciplina->getDisciplinasByCursoId($id);
    $response->docentes = $this->repositories->docente->getDocentesByCursoId($id);
    $response->valores = $this->repositories->curso_valor->getByCursoId($id);
    $response->parceiros = $this->repositories->parceiros->getByCursoId($id);
    $response->mensalidade = $this->repositories->mensalidade->getByCursoId($id);

    return $response;
  }

  public function updateCurso($body)
  {
    $updated = $this->update_curso_use_case->execute($body);

    return $updated;
  }

  public function deleteCurso($id)
  {
    try {
      $this->repositories->curso->deleteById($id);
      return true;
    } catch (\Throwable $th) {
      return false;
    }
  }

  public function setMensalidade($body)
  {
    $mensalidade = $this->create_mensalidade_use_case->execute($body);

    return $mensalidade;
  }

  public function updateMensalidade($body)
  {
    $mensalidade = $this->update_mensalidade_use_case->execute($body);

    return $mensalidade;
  }

  public function createParecer($body)
  {
    $parecer = $this->create_parecer_use_case->execute($body);
    return $parecer;
  }
}
