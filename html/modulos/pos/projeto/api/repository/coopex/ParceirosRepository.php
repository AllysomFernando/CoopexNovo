<?php

require_once __DIR__ . "/../../../../../../php/repository/IAbstractRepository.php";
require_once __DIR__ . "/PosDatabase.php";
require_once __DIR__ . "/../../models/Parceiro.php";

class ParceirosRepository extends PosDatabase implements IAbstractRepository
{

  function __construct($connection)
  {
    if ($connection) {
      $this->db = $connection;
    } else {
      parent::__construct();
    }

    $this->table = "parceiros";
  }

  public function getAll()
  {

    $sql = "SELECT * FROM {$this->table}";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Parceiro');
    $res = $stmt->fetchAll();



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
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Titulacao');
    $res = $stmt->fetch();



    return $res;
  }

  public function getByCursoId($id)
  {



    $sql = "SELECT * FROM {$this->table} WHERE id_projeto = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Parceiro');
    $res = $stmt->fetchAll();



    return $res;
  }

  public function create($data)
  {



    try {
      $sql = "INSERT INTO {$this->table} (id_projeto, nome) VALUES (:id_projeto, :nome)";

      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(":id_projeto", $data->id_projeto);
      $stmt->bindParam(":nome", $data->nome);
      $stmt->execute();

      $id = $this->db->lastInsertId();


      $inserted = $this->getById($id);




      return $inserted;
    } catch (\Throwable $th) {


      throw new Exception($th->getMessage());
    }
  }
  public function updateById($id, $data)
  {



    $sql = "UPDATE {$this->table} SET nome=:nome, cpf=:cpf WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":nome", $data->nome);
    $stmt->bindParam(":cpf", $data->cpf);
    $stmt->execute();


    $inserted = $this->getById($id);



    return $inserted;
  }

  public function updateByCursoId($id, $data)
  {



    $sql = "UPDATE {$this->table} SET id_pessoa=:id_pessoa, titulacao=:titulacao, pilares_curso=:pilares_curso, processo_selecao=:processo_selecao WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":titulacao", $data->titulacao);
    $stmt->bindParam(":id_projeto", $data->id_projeto);
    $stmt->bindParam(":id_pessoa", $data->id_pessoa);
    $stmt->bindParam(":pilares_curso", $data->pilares_curso);
    $stmt->bindParam(":processo_selecao", $data->processo_selecao);
    $stmt->execute();


    $inserted = $this->getById($id);




    return $inserted;
  }
  public function deleteById($id)
  {



    try {
      $sql = "DELETE FROM {$this->table} WHERE id = :id";

      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(":id", $id);
      $stmt->execute();




      return true;
    } catch (\PDOException $e) {


      return false;
    }
  }

  public function existsById($id)
  {

    $sql = "SELECT c.id FROM {$this->table} c WHERE c.id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);



    return $res == null || $res->id == null ? false : true;
  }
}
