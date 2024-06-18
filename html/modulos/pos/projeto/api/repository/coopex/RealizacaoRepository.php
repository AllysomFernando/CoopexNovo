<?php

require_once __DIR__ . "/../../../../../../php/repository/IAbstractRepository.php";
require_once __DIR__ . "/PosDatabase.php";
require_once __DIR__ . "/../../models/Realizacao.php";

class RealizacaoRepository extends PosDatabase implements IAbstractRepository
{

  function __construct($connection)
  {
    if ($connection) {
      $this->db = $connection;
    } else {
      parent::__construct();
    }

    $this->table = "realizacao";
  }

  public function getAll()
  {

    $this->db->exec("set names utf8");
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



    $this->db->exec("set names utf8");
    $sql = "SELECT * FROM {$this->table} WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);

    $data = new Realizacao($res->id, $res->id_projeto, $res->periodo, $res->dias_semana, $res->horario, $res->local);



    return $data;
  }

  public function getByCursoId($id)
  {


    $sql = "SELECT * FROM {$this->table} WHERE id_projeto = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);

    $data = new Realizacao($res->id, $res->id_projeto, $res->periodo, $res->dias_semana, $res->horario, $res->local);


    return $data;
  }

  /**
   * Insere um registro na tabela `realizacao` no banco de dados
   *
   * Recebe uma instância da classe Realizacao e insere no banco de dados
   *
   * @param Realizacao $data
   * @return Realizacao
   **/
  public function create($data)
  {



    try {
      $sql = "INSERT INTO {$this->table} (id_projeto, periodo, dias_semana, horario, local) VALUES (:id_projeto, :periodo, :dias_semana, :horario, :local)";

      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(":id_projeto", $data->id_projeto);
      $stmt->bindParam(":periodo", $data->periodo);
      $stmt->bindParam(":dias_semana", $data->dias_semana);
      $stmt->bindParam(":horario", $data->horario);
      $stmt->bindParam(":local", $data->local);
      $stmt->execute();

      $id = $this->db->lastInsertId();


      $inserted = $this->getById($id);


      return $inserted;
    } catch (\Throwable $th) {


      throw new Exception($th->getMessage());
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

  public function updateByCursoId($id, $data)
  {



    $sql = "UPDATE {$this->table} SET periodo=:periodo, dias_semana=:dias_semana, horario=:horario, local=:local WHERE id_projeto = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":periodo", $data->periodo);
    $stmt->bindParam(":dias_semana", $data->dias_semana);
    $stmt->bindParam(":horario", $data->horario);
    $stmt->bindParam(":local", $data->local);
    $stmt->execute();


    $inserted = $this->getByCursoId($id);


    return $inserted;
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
