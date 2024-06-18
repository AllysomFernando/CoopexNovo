<?php

require_once __DIR__ . "/../../../../../../php/repository/IAbstractRepository.php";
require_once __DIR__ . "/PosDatabase.php";
require_once __DIR__ . "/../../models/Objetivos.php";

class ObjetivosRepository extends PosDatabase implements IAbstractRepository
{

  function __construct($connection)
  {
    if ($connection) {
      $this->db = $connection;
    } else {
      parent::__construct();
    }

    $this->table = "objetivos";
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
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Objetivos');
    $res = $stmt->fetch();

    return $res;
  }

  public function getByCursoId($id)
  {
    $sql = "SELECT * FROM {$this->table} WHERE id_projeto = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Objetivos');
    $res = $stmt->fetch();

    return $res;
  }

  /**
   * Insere um registro na tabela `objetivos` no banco de dados
   *
   * Recebe uma instância da classe Objetivos e insere no banco de dados
   *
   * @param Objetivos $data
   * @return Objetivos
   **/
  public function create($data)
  {
    $sql = "INSERT INTO {$this->table} (id_projeto, geral, especifico) VALUES (:id_projeto, :geral, :especifico)";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id_projeto", $data->id_projeto);
    $stmt->bindParam(":geral", $data->geral);
    $stmt->bindParam(":especifico", $data->especifico);
    $stmt->execute();

    $id = $this->db->lastInsertId();

    $inserted = $this->getById($id);
    return $inserted;
  }

  public function existsById($id)
  {
    $sql = "SELECT id FROM pos.objetivos WHERE id = :id";

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
    $sql = "UPDATE {$this->table} SET geral=:geral, especifico=:especifico WHERE id_projeto = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":geral", $data->geral);
    $stmt->bindParam(":especifico", $data->especifico);
    $stmt->execute();

    $inserted = $this->getByCursoId($id);

    return $inserted;
  }


  public function deleteById($id)
  {
    throw new Exception("Method not implemented");
  }
}
