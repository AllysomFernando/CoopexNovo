<?php

require_once 'CoopexDatabase.php';

class CoopexPessoaRepository extends CoopexDatabase
{

  public function __construct()
  {
    parent::__construct();
    $this->table = 'pessoa';
  }

  public function getById($id)
  {
    $this->connect();

    $sql = "SELECT * FROM {$this->table} WHERE id_pessoa = :id";
    $stmt = $this->client->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $response = $stmt->fetch(PDO::FETCH_OBJ);

    return $response;
  }

  public function getByIdPessoa($id)
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
