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

  public function buscarDisciplinasSemId()
  {
    $this->db->exec("set names utf8");
    $sql = "SELECT * FROM pos.disciplina WHERE id_projeto IS NULL ORDER BY id";

    $res = $this->db->query($sql);
    return $res->fetchAll(PDO::FETCH_OBJ);
  }

  public function buscarDisciplinaPorId($id)
  {
    try {
      $this->db->exec("set names utf8");
      $sql = "SELECT di.*,
       MAX(do.id_titulacao) as id_titulacao
      FROM pos.disciplina di
          LEFT JOIN pos.docente_disciplina dd ON dd.id_disciplina = di.id
              LEFT JOIN pos.docente do ON do.id_docente = dd.id_docente
              LEFT JOIN pos.titulacao t ON do.id_titulacao = t.id_titulacao
      WHERE di.id = $id
      GROUP BY di.id";

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

  public function buscarDisciplinaPorCadastrador($id_pessoa)
  {
    try {
      $this->db->exec("set names utf8");
      $sql = "SELECT di.*,
       MAX(do.id_titulacao) as id_titulacao
      FROM pos.disciplina di
          LEFT JOIN pos.docente_disciplina dd ON dd.id_disciplina = di.id
              LEFT JOIN pos.docente do ON do.id_docente = dd.id_docente
              LEFT JOIN pos.titulacao t ON do.id_titulacao = t.id_titulacao
      WHERE di.cadastrado_por = $id_pessoa
      GROUP BY di.id";

      $res = $this->db->query($sql);
      return $res->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
      return $e;
    }
  }

  public function adicionarDisciplina($nome, $docente, $carga_horaria, $ementa, $cadastrado_por)
  {
    try {
      $this->db->exec("set names utf8");
      $sql = "INSERT INTO pos.disciplina (nome, id_docente, carga_horaria, ementa, cadastrado_por) VALUES (:disciplina, :id_docente, :carga_horaria, :ementa, :cadastrado_por)";

      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(":disciplina", $nome);
      $stmt->bindParam(":id_docente", $docente);
      $stmt->bindParam(":carga_horaria", $carga_horaria);
      $stmt->bindParam(":ementa", $ementa);
      $stmt->bindParam(":cadastrado_por", $cadastrado_por);
      $stmt->execute();

      $id = $this->db->lastInsertId();

      $disciplina = $this->buscarDisciplinaPorId($id);

      return $disciplina;
    } catch (PDOException $e) {
      return $e;
    }
  }

  public function adicionarEstruturaCurricular($id_disciplina, $id_docente)
  {
    try {
      $this->db->exec("set names utf8");
      $sql = "INSERT INTO pos.docente_disciplina (id_disciplina, id_docente) VALUES (:disciplina, :id_docente)";

      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(":disciplina", $id_disciplina);
      $stmt->bindParam(":id_docente", $id_docente);
      $stmt->execute();

      $id = $this->db->lastInsertId();

      $disciplina = $this->buscarDisciplinaPorId($id);

      return $disciplina;
    } catch (PDOException $e) {
      throw new RuntimeException($e->getMessage());
    }
  }
}
