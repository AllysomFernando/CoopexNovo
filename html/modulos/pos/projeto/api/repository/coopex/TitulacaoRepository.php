<?php

require_once __DIR__ . "/../../../../../../php/repository/IAbstractRepository.php";
require_once __DIR__ . "/PosDatabase.php";
require_once __DIR__ . "/../../models/Titulacao.php";

class TitulacaoRepository extends PosDatabase implements IAbstractRepository
{

  function __construct($connection)
  {
    if ($connection) {
      $this->db = $connection;
    } else {
      parent::__construct();
    }

    $this->table = "titulacao";
  }

  public function getAll()
  {

    $sql = "SELECT * FROM {$this->table}";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Titulacao');
    $res = $stmt->fetchAll();


    return $res;
  }

  public function getById($id)
  {

    $sql = "SELECT * FROM {$this->table} WHERE id_titulacao = $id";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Titulacao');
    $res = $stmt->fetch();


    return $res;
  }

  public function create($data)
  {
    throw new Exception("Method not implemented");
  }

  public function existsById($id)
  {
    throw new Exception("Method not implemented");
  }

  public function updateById($id, $data)
  {
    throw new Exception("Method not implemented");
  }

  public function deleteById($id)
  {
    throw new Exception("Method not implemented");
  }
}
