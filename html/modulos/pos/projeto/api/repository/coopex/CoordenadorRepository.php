<?php

require_once __DIR__ . "/../../../../../../php/repository/IAbstractRepository.php";
require_once __DIR__ . "/PosDatabase.php";
require_once __DIR__ . "/../../models/Coordenador.php";

class CoordenadorRepository extends PosDatabase implements IAbstractRepository
{

  function __construct($connection)
  {
    if ($connection) {
      $this->db = $connection;
    } else {
      parent::__construct();
    }

    $this->table = "coordenador";
  }

  public function getAll()
  {
    $sql = "SELECT c.id, c.titulacao, p.nome, p.usuario, p.cpf FROM {$this->table} c INNER JOIN coopex.pessoa p ON p.id_pessoa = c.id_pessoa";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    $res = $stmt->fetchAll(PDO::FETCH_OBJ);

    $arr = [];

    for ($i = 0; $i < count($res); $i++) {
      $data = new Coordenador();
      $data->build($res[$i]->id, $res[$i]->id_pessoa, $res[$i]->titulacao, $res[$i]->nome, $res[$i]->usuario, $res[$i]->cpf);
      array_push($arr, $data);
    }

    return $arr;
  }

  public function getById($id)
  {
    $exists = $this->existsById($id);

    if (!$exists) {
      throw new RuntimeException("Registro não existente: [tabela]{$this->table} - [id]" . $id);
    }

    $sql = "SELECT c.id, p.id_pessoa, c.titulacao, c.nome, p.usuario, c.cpf FROM {$this->table} c LEFT JOIN coopex.pessoa p ON p.id_pessoa = c.id_pessoa WHERE c.id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);

    $data = new Coordenador();
    $data->build($res->id, $res->id_pessoa, $res->titulacao, $res->nome, $res->usuario, $res->cpf);

    return $data;
  }

  public function getByCursoId($id)
  {
    $sql = "SELECT c.id, c.id_pessoa, c.titulacao, c.nome, c.cpf
    FROM {$this->table} c
    INNER JOIN proponentes pr ON pr.id_coordenador = c.id
    WHERE pr.id_projeto = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Coordenador');
    $res = $stmt->fetch();

    return $res;
  }

  public function create($data)
  {
    $sql = "INSERT INTO {$this->table} (nome, cpf, titulacao) VALUES (:nome, :cpf, :titulacao)";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":nome", $data->nome);
    $stmt->bindParam(":cpf", $data->cpf);
    $stmt->bindParam(":titulacao", $data->titulacao);
    $stmt->execute();

    $id = $this->db->lastInsertId();
    $inserted = $this->getById($id);

    return $inserted;
  }

  public function updateById($id, $data)
  {
    $sql = "UPDATE {$this->table} SET id_pessoa=:id_pessoa, titulacao=:titulacao WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":id_pessoa", $data->id_pessoa);
    $stmt->bindParam(":titulacao", $data->titulacao);
    $stmt->execute();

    $inserted = $this->getById($id);

    return $inserted;
  }

  public function updateByCursoId($id, $data)
  {
    $coordenador = $this->getByCursoId($id);
    $sql = "UPDATE {$this->table} SET nome=:nome, cpf=:cpf WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $coordenador->id);
    $stmt->bindParam(":nome", $data->nome);
    $stmt->bindParam(":cpf", $data->cpf);
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
    $sql = "SELECT c.id FROM {$this->table} c WHERE c.id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);

    return $res->id == null ? false : true;
  }
}
