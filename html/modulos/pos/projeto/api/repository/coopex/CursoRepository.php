<?php

require_once __DIR__ . "/../../../../../../php/repository/IAbstractRepository.php";
require_once __DIR__ . "/PosDatabase.php";
require_once __DIR__ . "/../../models/Curso.php";

class CursoRepository extends PosDatabase implements IAbstractRepository
{

  function __construct($connection)
  {
    if ($connection) {
      $this->db = $connection;
    } else {
      parent::__construct();
    }

    $this->table = "curso";
  }

  public function getAll()
  {
    $sql = "SELECT * FROM {$this->table}";
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

    $sql = "SELECT c.*, ca.id_campus as id_campus, ca.nome as campus_nome FROM {$this->table} c INNER JOIN campus ca ON ca.id_campus = c.id_campus WHERE c.id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);

    $data = new Curso($res->id, $res->id_pessoa, $res->id_area, $res->id_campus, $res->nome, $res->carga_horaria, $res->numero_vagas, $res->data_cadastro, $res->excluido);

    return $res;
  }

  /**
   * Insere um registro na tabela `curso` no banco de dados
   *
   * Recebe uma instância da classe Curso e insere no banco de dados
   *
   * @param Curso $data
   * @return Curso
   **/
  public function create($data)
  {
    $sql = "INSERT INTO {$this->table} (id_pessoa, id_area, id_campus, nome, carga_horaria, numero_vagas, data_cadastro) VALUES (:id_pessoa, :id_area, :id_campus, :nome, :carga_horaria, :numero_vagas, :data_cadastro)";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id_pessoa", $data->id_pessoa);
    $stmt->bindParam(":id_area", $data->id_area);
    $stmt->bindParam(":id_campus", $data->id_campus);
    $stmt->bindParam(":nome", $data->nome);
    $stmt->bindParam(":carga_horaria", $data->carga_horaria);
    $stmt->bindParam(":numero_vagas", $data->numero_vagas);
    $stmt->bindParam(":data_cadastro", $data->data_cadastro);
    $stmt->execute();

    $id = $this->db->lastInsertId();

    $inserted = $this->getById($id);

    return $inserted;
  }

  public function existsById($id)
  {

    $sql = "SELECT id FROM {$this->table} WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $exists = $stmt->fetch(PDO::FETCH_OBJ);

    return $exists == null ? false : true;
  }

  public function existsByNomeAndIdPessoa($nome, $id_pessoa)
  {

    $sql = "SELECT id FROM {$this->table} WHERE id_pessoa = :id_pessoa AND nome = :nome AND excluido = 0";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id_pessoa", $id_pessoa);
    $stmt->bindParam(":nome", $nome);
    $stmt->execute();

    $exists = $stmt->fetch(PDO::FETCH_OBJ);

    return $exists == null ? false : true;
  }

  public function updateById($id, $data)
  {
    $sql = "UPDATE {$this->table} SET id_area=:id_area, id_campus=:id_campus, nome=:nome, carga_horaria=:carga_horaria, numero_vagas=:numero_vagas WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":id_area", $data->id_area);
    $stmt->bindParam(":id_campus", $data->id_campus);
    $stmt->bindParam(":nome", $data->nome);
    $stmt->bindParam(":carga_horaria", $data->carga_horaria);
    $stmt->bindParam(":numero_vagas", $data->numero_vagas);
    $stmt->execute();

    $inserted = $this->getById($id);

    return $inserted;
  }

  public function deleteById($id)
  {
    $sql = "UPDATE {$this->table} SET excluido=:excluido WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->bindValue(":excluido", 1);
    $stmt->execute();

    $inserted = $this->getById($id);

    return $inserted;
  }
}
