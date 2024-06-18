<?php

class DisciplinaController
{
  public $db;

  function __construct($conexao)
  {
    $this->db = $conexao;
  }

  public function buscarTodasDisciplinas()
  {
    $this->db->exec("set names utf8");
    $sql = "SELECT * FROM pos.disciplina ORDER BY id";

    $res = $this->db->query($sql);
    return $res->fetchAll(PDO::FETCH_OBJ);
  }

  public function buscarDisciplinaPorId($id)
  {
    try {
      $this->db->exec("set names utf8");
      $sql = "SELECT * FROM pos.disciplina WHERE id = $id";

      $res = $this->db->query($sql);
      return $res->fetch(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
      return $e;
    }
  }

  public function buscarDisciplinaPorCurso($id)
  {
    try {
      $this->db->exec("set names utf8");
      $sql = "SELECT e.id_disciplina, e.id_docente, e.disciplina, e.carga_horaria, e.ementa FROM pos.estrutura_curricular e WHERE e.id_projeto = $id";

      $res = $this->db->query($sql);
      return $res->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
      return $e;
    }
  }

  public function adicionarDisciplina($nome, $docente, $carga_horaria, $ementa) {
    try {
      $sql = "INSERT INTO pos.disciplina (nome, id_docente, carga_horaria, ementa) VALUES (:disciplina, :id_docente, :carga_horaria, :ementa)";

      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(":disciplina", $nome);
      $stmt->bindParam(":id_docente", $docente);
      $stmt->bindParam(":carga_horaria", $carga_horaria);
      $stmt->bindParam(":ementa", $ementa);
      $stmt->execute();
      return 200;
    } catch (PDOException $e) {
      return $e;
    }
  }

}
