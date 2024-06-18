<?php

require_once __DIR__ . "/../../../../../../php/repository/IAbstractRepository.php";
require_once __DIR__ . "/PosDatabase.php";
require_once __DIR__ . "/../../models/Proponentes.php";

class ProponentesRepository extends PosDatabase implements IAbstractRepository
{

  function __construct($connection)
  {
    if ($connection) {
      $this->db = $connection;
    } else {
      parent::__construct();
    }

    $this->table = "proponentes";
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
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Proponentes');
    $res = $stmt->fetch();



    return $res;
  }

  public function getByCursoId($id)
  {

    $this->db->exec("set names utf8");
    $sql = "SELECT * FROM {$this->table} WHERE id_projeto = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Proponentes');

    $res = $stmt->fetch();



    return $res;
  }

  /**
   * Insere um registro na tabela `proponentes` no banco de dados
   *
   * Recebe uma instância da classe Proponentes e insere no banco de dados
   *
   * @param Proponentes $data
   * @return Proponentes
   **/
  public function create($data)
  {



    try {
      $sql = "INSERT INTO {$this->table} (id_projeto, instituicao, coordenacao_institucional, id_coordenador) VALUES (:id_projeto, :instituicao, :coordenacao_institucional, :id_coordenador)";

      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(":id_projeto", $data->id_projeto);
      $stmt->bindParam(":instituicao", $data->instituicao);
      $stmt->bindParam(":coordenacao_institucional", $data->coordenacao_institucional);
      $stmt->bindParam(":id_coordenador", $data->id_coordenador);
      $stmt->execute();

      $id = $this->db->lastInsertId();


      $inserted = $this->getById($id);


      return $inserted;
    } catch (\PDOException $th) {


      throw new RuntimeException($th->getMessage());
    }
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



    $sql = "UPDATE {$this->table} SET instituicao=:instituicao, coordenacao_institucional=:coordenacao_institucional, id_coordenador=:id_coordenador WHERE id_projeto = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":instituicao", $data->instituicao);
    $stmt->bindParam(":coordenacao_institucional", $data->coordenacao_institucional);
    $stmt->bindParam(":id_coordenador", $data->id_coordenador);
    $stmt->execute();


    $inserted = $this->getByCursoId($id);


    return $inserted;
  }


  public function deleteById($id)
  {
    throw new Exception("Method not implemented");
  }
}
