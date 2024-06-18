<?php

class GraduacaoController
{
  public $db;

  function __construct($conexao)
  {
    $this->db = $conexao;
  }

  public function getAllGraduacao()
  {

    $sql = "SELECT
    'CASCAVEL' as campus,
    gc.graduacao_id as id,
    gc.graduacao_nome as nome,
    gc.graduacao_cor as cor,
    gc.graduacao_email as email
FROM graduacao gc
UNION ALL
SELECT
    'TOLEDO' as campus,
    gt.graduacao_id + 2000 as id,
    gt.graduacao_nome as nome,
    gt.graduacao_cor as cor,
    gt.graduacao_email as email
FROM graduacao_toledo gt
ORDER BY nome";


    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    $res = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $res;
  }

  public function getGraduacaoBySlug($campus, $slug)
  {
    $table = $this->campusTable($campus);

    $this->db->exec("set names utf8");
    $sql = "SELECT
    graduacao_id as id,
    graduacao_nome as nome,
    graduacao_cor as cor,
    graduacao_email as email
FROM $table
WHERE graduacao_slug = :slug";


    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":slug", $slug);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);
    return $res;
  }

  public function getGraduacaoById($campus, $id)
  {
    $table = $this->campusTable($campus);

    $this->db->exec("set names utf8");
    $sql = "SELECT * FROM $table WHERE graduacao_id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $res = $stmt->fetch(PDO::FETCH_OBJ);
    return $res;
  }

  private function campusTable($campus) {
    $campus_table = $campus == "TOLEDO" ? "graduacao_toledo" : "graduacao";
    return $campus_table;
  }

}
