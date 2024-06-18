<?php

require_once __DIR__ . "/../factory/RepositoryFactory.php";
require_once __DIR__ . "/../models/Curso.php";
require_once __DIR__ . "/../repository/coopex/PosDatabase.php";

class CreateCursoUseCase
{

  public $repositories;

  public function __construct()
  {
    $client = new PosDatabase();
    $client->connect();
    $coopex_pos = $client->db;
    $this->repositories = new RepositoryFactory($coopex_pos);
  }
  public function execute($body)
  {
    $this->validateFields($body, ['id_pessoa', 'curso_id_area', 'campus_id_campus', 'curso_nome', 'curso_carga_horaria', 'curso_numero_vagas']);
    // $this->repositories->active_connection->beginTransaction();

    $exists = $this->repositories->curso->existsByNomeAndIdPessoa($body['curso_nome'], $body['id_pessoa']);

    if ($exists) {
      throw new Exception("Este curso já esta cadastrado");
    }

    $curso = new Curso(null, $body['id_pessoa'], $body['curso_id_area'], $body['campus_id_campus'], $body['curso_nome'], $body['curso_carga_horaria'], $body['curso_numero_vagas'], date("Y-m-d"), 0);

    $inserted = $this->repositories->curso->create($curso);

    $inserted_coordenador = $this->createCoordenador($body);

    $descricao = $this->createDescricao($inserted->id, $body);
    $justificativa = $this->createJustificativa($inserted->id, $body);
    $objetivos = $this->createObjetivos($inserted->id, $body);
    if (isset($body['enviar_aprovacao'])) {
      $parecer_curso = $this->createParecer($inserted->id, $body);
    }
    $proponentes = $this->createProponentes($inserted->id, $inserted_coordenador->id, $body);
    $realizacao = $this->createRealizacao($inserted->id, $body);
    $disciplina = $this->createDisciplinas($inserted->id, $body);
    $curso_valor = $this->createValores($inserted->id, $body);
    $parceiros = $this->createParceiros($inserted->id, $body);

    return $inserted;
  }


  private function createCoordenador($body)
  {
    $this->validateFields($body, ['coordenador_cpf', 'coordenador_nome', 'coordenador_titulacao']);

    $coordenador = new Coordenador();
    $coordenador->build(null, $body['coordenador_id'], $body['coordenador_titulacao'], $body['coordenador_nome'], null, $body['coordenador_cpf']);

    $inserted_coordenador = $this->repositories->coordenador->create($coordenador);

    return $inserted_coordenador;
  }

  private function createDescricao($id_curso, $body)
  {

    $this->validateFields($body, ['descricao_projeto_publico_alvo', 'descricao_projeto_perfil_aluno', 'descricao_projeto_pilares_curso', 'descricao_projeto_processo_selecao']);

    $descricao = new DescricaoCurso();

    $descricao->build(null, $id_curso, $body['descricao_projeto_publico_alvo'], $body['descricao_projeto_perfil_aluno'], $body['descricao_projeto_pilares_curso'], $body['descricao_projeto_processo_selecao']);

    return $this->repositories->descricao_curso->create($descricao);
  }

  private function createJustificativa($id_curso, $body)
  {
    $justificativa = new Justificativa();
    $justificativa->build(null, $id_curso, $body['justificativa_descricao'], $body['justificativa_contribuicao']);

    return $this->repositories->justificativa->create($justificativa);
  }

  private function createObjetivos($id_curso, $body)
  {
    $this->validateFields($body, ['objetivos_geral', 'objetivos_especifico']);

    $objetivos = new Objetivos();
    $objetivos->build(null, $id_curso, $body['objetivos_geral'], $body['objetivos_especifico']);
    return $this->repositories->objetivos->create($objetivos);
  }

  private function createDisciplinas($id_curso, $body)
  {
    $arr = [];

    if (!isset($body['disciplinas']) || count($body['disciplinas']) < 1) {
      throw new RuntimeException('Nenhuma disciplina informada');
    }

    $json_disciplinas = json_decode($body['disciplinas']);

    foreach ($json_disciplinas as $disciplina) {

      if ($disciplina->acao == "INSERT" && $disciplina->id != "") {
        $nova_disciplina = $this->repositories->disciplina->getById($disciplina->id);
        $nova_disciplina->id_projeto = $id_curso;

        $inserted = $this->repositories->disciplina->updateById($nova_disciplina->id, $nova_disciplina);
        array_push($arr, $inserted);
      }
    }

    return $arr;
  }

  private function createParecer($id_curso, $body)
  {
    $parecer_curso = new ParecerCurso();
    $parecer_curso->build(null, $id_curso, 1, 'PROPONENTE', date('Y-m-d H:i:s'), $body['id_pessoa'], null);
    return $this->repositories->parecer_curso->create($parecer_curso);
  }

  private function createProponentes($id_curso, $id_coordenador, $body)
  {
    $this->validateFields($body, ['proponente_instituicao', 'proponente_coordenacao']);

    $proponentes = new Proponentes();
    $proponentes->build(null, $id_curso, $body['proponente_instituicao'], $body['proponente_coordenacao'], $id_coordenador);

    return $this->repositories->proponentes->create($proponentes);
  }

  private function createRealizacao($id_curso, $body)
  {
    $this->validateFields($body, ['realizacao_periodo', 'realizacao_dias_semana', 'realizacao_horario', 'realizacao_local']);

    $realizacao = new Realizacao(null, $id_curso, $body['realizacao_periodo'], $body['realizacao_dias_semana'], $body['realizacao_horario'], $body['realizacao_local']);

    return $this->repositories->realizacao->create($realizacao);
  }

  private function createValores($id_curso, $body)
  {

    $curso_valor = new CursoValor();

    if (isset($body['curso_valor_customizado']) && $body['curso_valor_customizado'] == "true") {
      $this->validateFields($body, ['curso_valor_especialista', 'curso_valor_mestre', 'curso_valor_doutor']);
      $curso_valor->build(null, $id_curso, 1, $body['curso_valor_especialista'], $body['curso_valor_mestre'], $body['curso_valor_doutor']);
    } else {
      $especialista = $this->repositories->titulacao->getById(1);
      $mestre = $this->repositories->titulacao->getById(2);
      $doutor = $this->repositories->titulacao->getById(3);
      $curso_valor->build(null, $id_curso, 0, $especialista->valor_padrao, $mestre->valor_padrao, $doutor->valor_padrao);
    }


    return $this->repositories->curso_valor->create($curso_valor);
  }

  private function createParceiros($id_curso, $body)
  {
    $arr = [];
    if (!isset($body['parceiros'])) {
      return null;
    }

    $parceiros = json_decode($body['parceiros']);

    foreach ($parceiros as $parceiro) {
      $novo_parceiro = new Parceiro();
      $novo_parceiro->build(null, $id_curso, $parceiro->nome, $parceiro->cpf);

      $inserted = $this->repositories->parceiros->create($novo_parceiro);
      array_push($arr, $inserted);
    }

    return $arr;
  }

  private function validateFields($body, $fields)
  {
    foreach ($fields as $field) {
      if (!isset($body[$field])) {
        throw new RuntimeException("Campo obrigatório faltando: {$field}");
      }
    }
  }
}
