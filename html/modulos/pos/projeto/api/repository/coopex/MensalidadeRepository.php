<?php

require_once __DIR__ . "/../../../../../../php/repository/IAbstractRepository.php";
require_once __DIR__ . "/PosDatabase.php";
require_once __DIR__ . "/../../models/Mensalidade.php";

class MensalidadeRepository extends PosDatabase implements IAbstractRepository
{

  function __construct($connection)
  {
    if ($connection) {
      $this->db = $connection;
    } else {
      parent::__construct();
    }

    $this->table = "mensalidade";
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

    $sql = "SELECT * FROM {$this->table} WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Mensalidade');

    $res = $stmt->fetch();

    return $res;
  }

  public function getByCursoId($id)
  {
    $sql = "SELECT * FROM {$this->table} WHERE id_projeto = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Mensalidade');

    $res = $stmt->fetch();

    return $res;
  }

  /**
   * Insere um registro na tabela `mensalidade` no banco de dados
   *
   * Recebe uma instância da classe Mensalidade e insere no banco de dados
   *
   * @param Mensalidade $data
   * @return Mensalidade
   **/
  public function create($data)
  {
    $sql = "INSERT INTO {$this->table} (id_projeto, mensalidade, numero_parcelas, data_vigente) VALUES (:id_projeto, :mensalidade, :numero_parcelas, :data_vigente)";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id_projeto", $data->id_projeto);
    $stmt->bindParam(":mensalidade", $data->mensalidade);
    $stmt->bindParam(":numero_parcelas", $data->numero_parcelas);
    $stmt->bindParam(":data_vigente", $data->data_vigente);
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

  public function updateById($id, $data)
  {
    throw new Exception("Method not implemented");
  }

  public function updateByCursoId($id, $data)
  {
    $sql = "UPDATE {$this->table} SET mensalidade=:mensalidade, numero_parcelas=:numero_parcelas WHERE id_projeto = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":mensalidade", $data->mensalidade);
    $stmt->bindParam(":numero_parcelas", $data->numero_parcelas);
    $stmt->execute();

    $inserted = $this->getByCursoId($id);
    return $inserted;
  }

  public function deleteById($id)
  {
    throw new Exception("Method not implemented");
  }
}
