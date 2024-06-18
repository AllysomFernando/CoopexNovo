<?php

require_once __DIR__ . "/../../../../../php/utils.php";
require_once __DIR__ . "/../../../../../php/repository/IAbstractRepository.php";

class AtendimentoRepository implements IAbstractRepository
{

  public $db;

  function __construct($conexao)
  {
    $this->db = $conexao;
  }

  public function getAll()
  {
    $sql = "SELECT * FROM ticket.atendimento a ORDER BY id";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    $res = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $res;
  }
  public function getById($id)
  {
    $sql = "SELECT * FROM ticket.atendimento a WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);
    return $res;
  }

  public function getAtendimentoByTicketId($id)
  {
    $sql = "SELECT * FROM ticket.atendimento a WHERE id_ticket = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);
    return $res;
  }
  public function create($data)
  {
    $this->db->beginTransaction();

    $sql = "INSERT INTO ticket.atendimento (id_ticket, id_atendente, data_inicio) VALUES (:id_ticket, :id_atendente, NOW())";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id_ticket", $data->id_ticket);
    $stmt->bindParam(":id_atendente", $data->id_atendente);
    $stmt->execute();

    $inserted_id = $this->db->lastInsertId();

    if (!$inserted_id) {
      $this->db->rollback();
      die("Erro ao cadastrar Atendimento");
    }

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
    $sql = "SELECT a.id FROM ticket.atendimento a WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);

    if ($res == null || $res == "") return false;

    return true;
  }

  public function finalizarAtendimento($id)
  {

    $this->db->beginTransaction();

    $sql = "UPDATE ticket.atendimento a SET a.data_fim = now() WHERE a.id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);

    try {
      $stmt->execute();

      $this->db->commit();

      $res = $this->getById($id);
      return $res;
      
    } catch (\Throwable $th) {
      $this->db->rollback();
      die("Erro ao finalizar atendimento");
    }
  }
}
