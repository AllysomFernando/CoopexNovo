<?php

require_once __DIR__ . "/../../../../../php/utils.php";
require_once __DIR__ . "/../../../../../php/repository/IAbstractRepository.php";

class CoopexAntigoRepository implements IAbstractRepository
{

  public $db;

  function __construct($conexao)
  {
    $this->db = $conexao;
  }

  public function getAll()
  {
    $sql = "SELECT * FROM ticket.ticket t ORDER BY id";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    $res = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $res;
  }
  public function getById($id)
  {
    $sql = "SELECT * FROM ticket.ticket t WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);
    return $res;
  }
  public function create($data)
  {
    $this->db->beginTransaction();

    $sql = "INSERT INTO ticket.ticket (id_usuario, data_envio, titulo, descricao, url, status) VALUES (:id_usuario, NOW(), :titulo, :descricao, :url, 4)";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id_usuario", $data->id_usuario);
    $stmt->bindParam(":titulo", $data->titulo);
    $stmt->bindParam(":descricao", $data->descricao);
    $stmt->bindValue(":url", $data->url ? $data->url : null);
    $stmt->execute();

    $inserted_id = $this->db->lastInsertId();
    // $serialized_data = serialize(array("id_usuario" => $data->id_usuario, "titulo" => $data->titulo, "descricao" => $data->descricao, "url" => $data->url));

    if (!$inserted_id) {
      // gravarLog("ticket.ticket", 0, 1, $sql, $serialized_data);
      $this->db->rollback();
      die("Erro ao cadastrar ticket");
    }

    // gravarLog("ticket.ticket", $inserted_id, 1, $sql, $serialized_data, "erro");

    $this->db->commit();

    $res = $this->getById($inserted_id);
    return $res;
  }
  public function updateById($id, $data)
  {
  }
  public function deleteById($id)
  {
  }
  public function existsById($id)
  {
    $sql = "SELECT t.id FROM ticket.ticket t WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);

    if ($res == null || $res == "") return false;

    return true;
  }

  public function getUserInfoByUserId($id)
  {
    $sql = "SELECT p.id_pessoa,
    u.id_usuario,
    u.ra,
    u.nome,
    u.cpf,
    u.email,
    c.curso,
    t.tipo,
    u.usuario
    FROM coopex_usuario.usuario u
    LEFT JOIN coopex_usuario.evento_pessoa p ON p.id_usuario = u.id_usuario
    INNER JOIN coopex_usuario.usuario_tipo t ON u.tipo = t.id_tipo
    LEFT JOIN coopex_usuario.curso c ON u.curso = c.id_curso
    WHERE u.id_usuario = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);
    return $res;
  }
}
