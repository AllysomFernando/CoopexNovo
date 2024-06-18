<?php

require_once __DIR__ . "/../../../../../php/utils.php";
require_once __DIR__ . "/../../../../../php/repository/IAbstractRepository.php";

class MensagemRepository implements IAbstractRepository
{

  public $db;

  function __construct($conexao)
  {
    $this->db = $conexao;
  }

  public function getAll()
  {
    $sql = "SELECT * FROM ticket.mensagem m ORDER BY id";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    $res = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $res;
  }
  public function getById($id)
  {
    $sql = "SELECT * FROM ticket.mensagem m WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);
    return $res;
  }

  public function getTicketByMensagemId($id) {
    $sql = "SELECT t.* FROM ticket.ticket t INNER JOIN ticket.atendimento a ON a.id_ticket = t.id INNER JOIN ticket.mensagem m ON m.id_atendimento = a.id WHERE m.id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);
    return $res;
  }

  public function getTicketByAtendimentoId($id) {
    $sql = "SELECT t.* FROM ticket.ticket t INNER JOIN ticket.atendimento a ON a.id_ticket = t.id WHERE a.id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);
    return $res;
  }

  public function getAllMensagensFromAtendimentoById($id)
  {
    $sql = "SELECT
    m.*,
    p.nome as autor
FROM ticket.mensagem m
INNER JOIN
    coopex.pessoa p ON p.id_pessoa = m.remetente
WHERE
    m.id_atendimento = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $res;
  }
  public function create($data)
  {
    $this->db->exec("set names utf8");
    $this->db->beginTransaction();

    $sql = "INSERT INTO ticket.mensagem (id_atendimento, data_envio, conteudo, remetente, destinatario) VALUES (:id_atendimento, NOW(), :conteudo, :remetente, :destinatario)";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id_atendimento", $data->id);
    $stmt->bindParam(":conteudo", $data->conteudo);
    $stmt->bindParam(":remetente", $data->remetente);
    $stmt->bindValue(":destinatario", $data->destinatario);
    $stmt->execute();

    $inserted_id = $this->db->lastInsertId();

    if (!$inserted_id) {
      $this->db->rollback();
      die("Erro ao cadastrar mensagem");
    }

    $this->db->commit();

    $res = $this->getById($inserted_id);
    return $res;
  }
  public function updateById($id, $data)
  {
    throw new RuntimeException("Método não permitido");
  }
  public function deleteById($id)
  {
    throw new RuntimeException("Método não permitido");
  }
  public function existsById($id)
  {
    $sql = "SELECT m.id FROM ticket.mensagem m WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);

    if ($res == null || $res == "") return false;

    return true;
  }
}
