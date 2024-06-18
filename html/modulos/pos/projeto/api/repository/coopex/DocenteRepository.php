<?php

require_once __DIR__ . "/../../../../../../php/repository/IAbstractRepository.php";
require_once __DIR__ . "/PosDatabase.php";
require_once __DIR__ . "/../../models/Docente.php";

class DocenteRepository extends PosDatabase implements IAbstractRepository
{

  function __construct($connection)
  {
    if ($connection) {
      $this->db = $connection;
    } else {
      parent::__construct();
    }

    $this->table = "docente";
  }

  public function getAll()
  {
    $sql = "SELECT DISTINCT d.*, t.titulacao FROM {$this->table} d INNER JOIN titulacao t ON t.id_titulacao = d.id_titulacao GROUP BY d.nome ORDER BY nome ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Docente');
    $res = $stmt->fetchAll();

    return $res;
  }

  public function getById($id)
  {
    $sql = "SELECT * FROM {$this->table} WHERE id_docente = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Docente');
    $res = $stmt->fetch();

    return $res;
  }

  public function getByCpf($cpf)
  {
    $sql = "SELECT * FROM {$this->table} WHERE cpf = :cpf";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":cpf", $cpf);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Docente');
    $res = $stmt->fetch();

    return $res;
  }

  public function getDocentesByCursoId($id)
  {
    $sql = "SELECT docente.*, titulacao.titulacao as titulacao_docente FROM disciplina INNER JOIN docente ON docente.id_docente = disciplina.id_docente
      INNER JOIN titulacao ON docente.id_titulacao = titulacao.id_titulacao WHERE id_projeto = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_OBJ);

    return $res;
  }

  public function updateById($id_docente, $data)
  {
    $sql = "UPDATE {$this->table} SET nome = :nome, id_titulacao = :id_titulacao, descricao = :descricao, curriculo = :curriculo, nacionalidade = :nacionalidade, foto = :foto WHERE id_docente = :id_docente";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":nome", $data->nome);
    $stmt->bindParam(":id_titulacao", $data->id_titulacao);
    $stmt->bindParam(":descricao", $data->descricao);
    $stmt->bindParam(":curriculo", $data->curriculo);
    $stmt->bindParam(":nacionalidade", $data->nacionalidade);
    $stmt->bindParam(":foto", $data->foto);
    $stmt->bindParam(":id_docente", $data->id_docente);
    $res = $stmt->execute();

    $id = $this->db->lastInsertId();

    $inserted = $this->getById($id);

    return $inserted;
  }

  /**
   * Cadastrar docente
   *
   * Insere um registro na tabela `docente` no banco de dados
   *
   * @param Docente $docente Instância da classe docente
   * @return Docente
   **/
  public function create($docente)
  {
    $sql = "INSERT INTO {$this->table} (id_titulacao, nome, cpf, ies, cidade, descricao, curriculo, foto, certificado, nacionalidade, termo_aceite, termo_uso_imagem) VALUES (:id_titulacao, :nome, :cpf, :ies, :cidade, :descricao, :curriculo, :foto, :certificado, :nacionalidade, :termo_aceite, :termo_uso_imagem)";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id_titulacao", $docente->id_titulacao);
    $stmt->bindParam(":nome", $docente->nome);
    $stmt->bindParam(":cpf", $docente->cpf);
    $stmt->bindParam(":ies", $docente->ies);
    $stmt->bindParam(":cidade", $docente->cidade);
    $stmt->bindParam(":descricao", $docente->descricao);
    $stmt->bindParam(":curriculo", $docente->curriculo);
    $stmt->bindParam(":foto", $docente->foto);
    $stmt->bindParam(":certificado", $docente->certificado);
    $stmt->bindParam(":nacionalidade", $docente->nacionalidade);
    $stmt->bindParam(":termo_aceite", $docente->termo_aceite);
    $stmt->bindParam(":termo_uso_imagem", $docente->termo_uso_imagem);
    $stmt->execute();

    $id = $this->db->lastInsertId();

    $inserted = $this->getById($id);

    return $inserted;
  }

  public function existsById($id)
  {
    throw new Exception("Method not implemented");
  }

  public function deleteById($id)
  {
    throw new Exception("Method not implemented");
  }
}
