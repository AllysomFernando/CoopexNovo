<?php

require_once __DIR__ . '/../repository/coopex/CoordenadorRepository.php';
require_once __DIR__ . '/../repository/coopex/CursoRepository.php';
require_once __DIR__ . '/../repository/coopex/DescricaoCursoRepository.php';
require_once __DIR__ . '/../repository/coopex/DisciplinaRepository.php';
require_once __DIR__ . '/../repository/coopex/JustificativaRepository.php';
// require_once __DIR__ . '/../repository/coopex/MensalidadeRepository.php';
require_once __DIR__ . '/../repository/coopex/ObjetivosRepository.php';
require_once __DIR__ . '/../repository/coopex/ParecerRepository.php';
require_once __DIR__ . '/../repository/coopex/ParecerCursoRepository.php';
require_once __DIR__ . '/../repository/coopex/ProponentesRepository.php';
require_once __DIR__ . '/../repository/coopex/RealizacaoRepository.php';
require_once __DIR__ . '/../repository/coopex/TitulacaoRepository.php';

class CursoFactory {
  public $curso;
  public $coordenador;
  public $descricao;
  public $justificativa;
  public $mensalidade;
  public $objetivos;
  public $parecer;
  public $proponentes;
  public $realizacao;

  public function __construct($id) {
    $this->buildCurso($id);
    $this->buildCoordenador($id);
    $this->buildDescricao($id);
    $this->buildJustificativa($id);
    // $this->buildMensalidade($id);
    $this->buildObjetivos($id);
    $this->buildParecer($id);
    $this->buildProponentes($id);
    $this->buildRealizacao($id);
  }
  
  private function buildCurso($id) {
    $repo = new CursoRepository();

    $this->curso = $repo->getById($id);
  }

  private function buildCoordenador($id) {
    $repo = new CoordenadorRepository();

    $this->coordenador = $repo->getByCursoId($id);
  }
  private function buildDescricao($id) {
    $repo = new DescricaoCursoRepository();

    $this->descricao = $repo->getByCursoId($id);
  }
  private function buildJustificativa($id) {
    $repo = new JustificativaRepository();

    $this->justificativa = $repo->getByCursoId($id);
  }
  private function buildMensalidade($id) {
    $repo = new MensalidadeRepository();

    $this->mensalidade = $repo->getByCursoId($id);
  }
  private function buildObjetivos($id) {
    $repo = new ObjetivosRepository();

    $this->objetivos = $repo->getByCursoId($id);
  }
  private function buildParecer($id) {
    $repo = new ParecerCursoRepository();

    $this->parecer = $repo->getByCursoId($id);
  }
  private function buildProponentes($id) {
    $repo = new ProponentesRepository();

    $this->proponentes = $repo->getByCursoId($id);
  }
  private function buildRealizacao($id) {
    $repo = new RealizacaoRepository();

    $this->realizacao = $repo->getByCursoId($id);
  }

}