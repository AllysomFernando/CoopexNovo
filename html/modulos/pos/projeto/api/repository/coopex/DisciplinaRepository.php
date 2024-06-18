<?php

require_once __DIR__ . "/../../../../../../php/repository/IAbstractRepository.php";
require_once __DIR__ . "/PosDatabase.php";
require_once __DIR__ . "/../../models/Disciplina.php";
class DisciplinaRepository extends PosDatabase implements IAbstractRepository
{

  function __construct($connection)
  {
    if ($connection) {
      $this->db = $connection;
    } else {
      parent::__construct();
    }

    $this->table = "disciplina";
  }

  public function getAll()
  {
    $sql = "SELECT DISTINCT e.id_disciplina, e.id_docente, e.disciplina, e.carga_horaria, e.ementa FROM {$this->table} e GROUP BY e.disciplina ORDER BY e.disciplina";
    $stmt = $this->db->query($sql);
    $res = $stmt->fetchAll(PDO::FETCH_OBJ);

    return $res;
  }

  public function getById($id)
  {
    $exists = $this->existsById($id);

    if (!$exists) {
      throw new RuntimeException("Registro não existente: [tabela]{$this->table} - [id]" . $id);
    }

    $sql = "SELECT * FROM {$this->table} WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Disciplina');
    $res = $stmt->fetch();

    return $res;
  }

  public function getDisciplinasByCursoId($id)
  {
    $sql = "SELECT di.*,
       MAX(do.id_titulacao) as id_titulacao,
       do.nome as nome_docente
      FROM {$this->table} di
          LEFT JOIN pos.docente_disciplina dd ON dd.id_disciplina = di.id
              LEFT JOIN pos.docente do ON do.id_docente = dd.id_docente
              LEFT JOIN pos.titulacao t ON do.id_titulacao = t.id_titulacao
              INNER JOIN pos.curso c ON c.id = di.id_projeto
      WHERE c.id = $id
      GROUP BY di.id";
    $stmt = $this->db->query($sql);
    $res = $stmt->fetchAll(PDO::FETCH_OBJ);

    return $res;
  }

  /**
   * Insere uma disciplina no banco de dados
   *
   * Recebe uma instância da classe Disciplina e insere no banco de dados
   *
   * @param Disciplina $disciplina
   * @return Disciplina
   **/
  public function create($disciplina)
  {
    $sql = "INSERT INTO {$this->table} (nome, id_projeto, id_docente, carga_horaria, ementa) VALUES (:nome, :id_projeto, :id_docente, :carga_horaria, :ementa)";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":nome", $disciplina->nome);
    $stmt->bindParam(":id_projeto", $disciplina->id_projeto);
    $stmt->bindParam(":id_docente", $disciplina->id_docente);
    $stmt->bindParam(":carga_horaria", $disciplina->carga_horaria);
    $stmt->bindParam(":ementa", $disciplina->ementa);
    $stmt->execute();

    $id = $this->db->lastInsertId();

    $inserted = $this->getById($id);

    return $inserted;
  }

  public function existsById($id)
  {
    $sql = "SELECT id FROM {$this->table} WHERE id = $id";

    $res = $this->db->query($sql);
    $exists = $res->fetch(PDO::FETCH_OBJ);

    return $exists == null ? false : true;
  }

  public function updateById($id, $data)
  {
    $sql = "UPDATE {$this->table} SET id_projeto=:id_projeto, nome=:nome, carga_horaria=:carga_horaria, ementa=:ementa WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":nome", $data->nome);
    $stmt->bindParam(":id_projeto", $data->id_projeto);
    $stmt->bindParam(":carga_horaria", $data->carga_horaria);
    $stmt->bindParam(":ementa", $data->ementa);
    $stmt->execute();

    $inserted = $this->getById($id);

    return $inserted;
  }

  public function updateByCursoId($id, $data)
  {
    $sql = "UPDATE {$this->table} SET descricao=:descricao, contribuicao=:contribuicao WHERE id_projeto = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":contribuicao", $data->contribuicao);
    $stmt->bindParam(":descricao", $data->descricao);
    $stmt->execute();

    $inserted = $this->getById($id);

    return $inserted;
  }

  public function deleteById($id)
  {
    $sql = "DELETE FROM {$this->table} WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    return true;
  }

  public function deleteFromRelationById($id)
  {
    $sql = "DELETE FROM docente_disciplina WHERE id_disciplina = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    return true;
  }

  public function deleteAllByCursoId($id)
  {
    $sql = "DELETE FROM {$this->table} WHERE id_projeto = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    return true;
  }
}
