<?php

require_once __DIR__ . "/../../../../../../php/repository/IAbstractRepository.php";
require_once __DIR__ . "/PosDatabase.php";
require_once __DIR__ . "/../../models/DescricaoCurso.php";

class DescricaoCursoRepository extends PosDatabase implements IAbstractRepository
{

  function __construct($connection)
  {
    if ($connection) {
      $this->db = $connection;
    } else {
      parent::__construct();
    }

    $this->table = "descricao_projeto";
  }

  public function getAll()
  {
    throw new Exception("Method not implemented");
  }

  public function getById($id)
  {
    $exists = $this->existsById($id);

    if (!$exists) {
      throw new RuntimeException("Registro não existente: [tabela]{$this->table} - [id]" . $id);
    }

    $sql = "SELECT * FROM {$this->table} WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);

    $data = new DescricaoCurso();
    $data->build($res->id, $res->id_projeto, $res->publico_alvo, $res->perfil_aluno, $res->pilares_curso, $res->processo_selecao);

    return $data;
  }

  public function getByCursoId($id)
  {
    $sql = "SELECT * FROM {$this->table} WHERE id_projeto = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'DescricaoCurso');

    $res = $stmt->fetch();

    return $res;
  }

  /**
   * Insere um registro na tabela `descricao_projeto`
   *
   * Insere um registro na tabela `descricao_projeto`
   *
   * @param DescricaoCurso $data Instância da classe DescricaoCurso
   * @return DescricaoCurso
   **/
  public function create($data)
  {
    $sql = "INSERT INTO {$this->table} (id_projeto, publico_alvo, perfil_aluno, pilares_curso, processo_selecao) VALUES (:id_projeto, :publico_alvo, :perfil_aluno, :pilares_curso, :processo_selecao)";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id_projeto', $data->id_projeto);
    $stmt->bindParam(':publico_alvo', $data->publico_alvo);
    $stmt->bindParam(':perfil_aluno', $data->perfil_aluno);
    $stmt->bindParam(':pilares_curso', $data->pilares_curso);
    $stmt->bindParam(':processo_selecao', $data->processo_selecao);
    $stmt->execute();

    $id = $this->db->lastInsertId();
    $inserted = $this->getById($id);

    return $inserted;
  }

  public function updateById($id, $data)
  {
    throw new Exception("Method not implemented");
  }

  public function updateByCursoId($id, $data)
  {
    $sql = "UPDATE {$this->table} SET publico_alvo=:publico_alvo, perfil_aluno=:perfil_aluno, pilares_curso=:pilares_curso, processo_selecao=:processo_selecao WHERE id_projeto = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":perfil_aluno", $data->perfil_aluno);
    $stmt->bindParam(":publico_alvo", $data->publico_alvo);
    $stmt->bindParam(":pilares_curso", $data->pilares_curso);
    $stmt->bindParam(":processo_selecao", $data->processo_selecao);
    $stmt->execute();

    $inserted = $this->getByCursoId($id);

    return $inserted;
  }

  public function deleteById($id)
  {
    throw new Exception("Method not implemented");
  }

  public function existsById($id)
  {
    $sql = "SELECT id FROM {$this->table} WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);

    return ($res == null) ? false : true;
  }
}
