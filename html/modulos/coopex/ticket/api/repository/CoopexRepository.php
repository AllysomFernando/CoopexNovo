<?php

require_once __DIR__ . "/../../../../../php/utils.php";
require_once __DIR__ . "/../../../../../php/repository/IAbstractRepository.php";
require_once __DIR__ . "/../../../../../php/repository/CoopexDatabase.php";

class CoopexRepository extends CoopexDatabase implements IAbstractRepository
{

  function __construct()
  {
    parent::__construct();
  }

  public function getAll()
  {
    throw new RuntimeException("Not implemented");
  }
  public function getById($id)
  {
    throw new RuntimeException("Not implemented");
  }
  public function create($data)
  {
    throw new RuntimeException("Not implemented");
  }
  public function updateById($id, $data)
  {
    throw new RuntimeException("Not implemented");
  }
  public function deleteById($id)
  {
    throw new RuntimeException("Not implemented");
  }
  public function existsById($id)
  {
    throw new RuntimeException("Not implemented");
  }

  public function getUserInfoByUserId($id)
  {
    $this->connect();
    $sql = "SELECT u.id_pessoa,
    u.ra,
    u.nome,
    u.cpf,
    u.email,
    t.tipo_usuario,
    u.usuario
    FROM coopex.pessoa u
    INNER JOIN coopex.tipo_usuario t ON u.id_tipo_usuario = t.id_tipo_usuario
    WHERE u.id_pessoa = :id";

    $stmt = $this->client->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);
    
    $this->disconnect();
    return $res;
  }
}
