<?php

include_once __DIR__ . "/../../../../../php/mysql.php";
include_once __DIR__ . "/../repository/coopex/PosDatabase.php";
require_once __DIR__ . '/../repository/coopex/AreaRepository.php';
require_once __DIR__ . '/../repository/coopex/CoordenadorRepository.php';
require_once __DIR__ . '/../repository/coopex/CursoRepository.php';
require_once __DIR__ . '/../repository/coopex/DescricaoCursoRepository.php';
require_once __DIR__ . '/../repository/coopex/DisciplinaRepository.php';
require_once __DIR__ . '/../repository/coopex/DocenteRepository.php';
require_once __DIR__ . '/../repository/coopex/JustificativaRepository.php';
require_once __DIR__ . '/../repository/coopex/MensalidadeRepository.php';
require_once __DIR__ . '/../repository/coopex/ObjetivosRepository.php';
require_once __DIR__ . '/../repository/coopex/ParecerCursoRepository.php';
require_once __DIR__ . '/../repository/coopex/ProponentesRepository.php';
require_once __DIR__ . '/../repository/coopex/RealizacaoRepository.php';
require_once __DIR__ . '/../repository/coopex/CursoValorRepository.php';
require_once __DIR__ . '/../repository/coopex/TitulacaoRepository.php';
require_once __DIR__ . '/../repository/coopex/ParceirosRepository.php';

class RepositoryFactory
{
  public $area;
  public $coordenador;
  public $curso;
  public $descricao_curso;
  public $disciplina;
  public $docente;
  public $justificativa;
  public $mensalidade;
  public $curso_valor;
  public $objetivos;
  public $parecer_curso;
  public $proponentes;
  public $realizacao;
  public $titulacao;
  public $parceiros;

  public $active_connection;

  public function __construct($connection = null)
  {
    if (!$connection) {
      $client = new PosDatabase();
      $client->connect();
      $connection = $client->db;
      $this->active_connection = $connection;
    }
    $this->area = new AreaRepository($connection);
    $this->coordenador = new CoordenadorRepository($connection);
    $this->curso = new CursoRepository($connection);
    $this->descricao_curso = new DescricaoCursoRepository($connection);
    $this->disciplina = new DisciplinaRepository($connection);
    $this->docente = new DocenteRepository($connection);
    $this->justificativa = new JustificativaRepository($connection);
    $this->mensalidade = new MensalidadeRepository($connection);
    $this->objetivos = new ObjetivosRepository($connection);
    $this->parecer_curso = new ParecerCursoRepository($connection);
    $this->proponentes = new ProponentesRepository($connection);
    $this->realizacao = new RealizacaoRepository($connection);
    $this->curso_valor = new CursoValorRepository($connection);
    $this->titulacao = new TitulacaoRepository($connection);
    $this->parceiros = new ParceirosRepository($connection);
  }
}
