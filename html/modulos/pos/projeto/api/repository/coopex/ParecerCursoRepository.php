<?php

require_once __DIR__ . "/../../../../../../php/repository/IAbstractRepository.php";
require_once __DIR__ . "/PosDatabase.php";
require_once __DIR__ . "/../../models/ParecerCurso.php";

class ParecerCursoRepository extends PosDatabase implements IAbstractRepository
{

  function __construct($connection)
  {
    if ($connection) {
      $this->db = $connection;
    } else {
      parent::__construct();
    }

    $this->table = "parecer_curso";
  }

  public function getAll()
  {

    $sql = "SELECT * FROM {$this->table}";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'ParecerCurso');
    $res = $stmt->fetchAll();



    return $res;
  }

  public function getById($id)
  {
    $exists = $this->existsById($id);

    if (!$exists) {
      throw new RuntimeException("Registro não existente: [tabela]{$this->table} - [id]" . $id);
    }



    $sql = "SELECT * FROM {$this->table} WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'ParecerCurso');
    $res = $stmt->fetch();



    return $res;
  }

  public function getByCursoId($id)
  {

    $sql = "SELECT * FROM {$this->table} WHERE id_projeto = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'ParecerCurso');
    $res = $stmt->fetch();



    return $res;
  }

  public function getHistoricoByCursoId($id)
  {



    $sql = "SELECT pc.*, p.usuario, e.descricao
    FROM {$this->table} pc
             INNER JOIN coopex.pessoa p ON p.id_pessoa = pc.id_pessoa
            INNER JOIN parecer_etapa e ON e.id = pc.etapa
    WHERE id_projeto = :id
    ORDER BY pc.data";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    // $stmt->setFetchMode(PDO::FETCH_CLASS, 'ParecerCurso');
    $res = $stmt->fetchAll(PDO::FETCH_OBJ);



    return $res;
  }

  public function getParecerCoordenacaoByCursoId($id)
  {

    $sql = "SELECT * FROM {$this->table} WHERE id_projeto = :id AND tipo_usuario = 'COORDENACAO' ORDER BY data DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'ParecerCurso');
    $res = $stmt->fetch();


    return $res;
  }

  public function getParecerReitoriaByCursoId($id)
  {

    $sql = "SELECT * FROM {$this->table} WHERE id_projeto = :id AND tipo_usuario = 'REITORIA' ORDER BY data DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'ParecerCurso');
    $res = $stmt->fetch();



    return $res;
  }

  public function isEnviadoParaAprovacao($id_curso)
  {

    $sql = "SELECT * FROM {$this->table} WHERE id_projeto = :id AND tipo_usuario = 'PROPONENTE' AND id_parecer = :id_parecer AND etapa = :etapa ORDER BY data DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id_curso);
    $stmt->bindValue(":id_parecer", 1);
    $stmt->bindValue(":etapa", 1);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'ParecerCurso');
    $res = $stmt->fetch();



    if ($res == null || $res->id == null) {
      return false;
    }

    return true;
  }

  /**
   * Insere um registro na tabela `parecer_curso` no banco de dados
   *
   * Recebe uma instância da classe ParecerCurso e insere no banco de dados
   *
   * @param ParecerCurso $data
   * @return ParecerCurso
   **/
  public function create($data)
  {



    try {
      $sql = "INSERT INTO {$this->table} (id_projeto, etapa, data, tipo_usuario, id_parecer, id_pessoa, observacao) VALUES (:id_projeto, :etapa, :data, :tipo_usuario, :id_parecer, :id_pessoa, :observacao)";

      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(":id_projeto", $data->id_projeto);
      $stmt->bindParam(":etapa", $data->etapa);
      $stmt->bindParam(":data", $data->data);
      $stmt->bindParam(":tipo_usuario", $data->tipo_usuario);
      $stmt->bindParam(":id_parecer", $data->id_parecer);
      $stmt->bindParam(":id_pessoa", $data->id_pessoa);
      $stmt->bindParam(":observacao", $data->observacao);
      $stmt->execute();

      $id = $this->db->lastInsertId();


      $inserted = $this->getById($id);


      return $inserted;
    } catch (\Throwable $th) {


      throw new Exception($th->getMessage());
    }
  }

  public function existsById($id)
  {

    $sql = "SELECT id FROM {$this->table} WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $exists = $stmt->fetch(PDO::FETCH_OBJ);



    return $exists == null ? false : true;
  }

  public function updateByCursoId($id, $data)
  {



    $sql = "UPDATE {$this->table} SET parecer_coordenacao=:parecer_coordenacao, data_parecer_coordenacao=:data_parecer_coordenacao, parecer_pro_reitoria=:parecer_pro_reitoria, data_parecer_pro_reitoria=:data_parecer_pro_reitoria WHERE id_projeto = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->bindParam(":parecer_coordenacao", $data->parecer_coordenacao);
    $stmt->bindParam(":data_parecer_coordenacao", $data->data_parecer_coordenacao);
    $stmt->bindParam(":parecer_pro_reitoria", $data->parecer_pro_reitoria);
    $stmt->bindParam(":data_parecer_pro_reitoria", $data->data_parecer_pro_reitoria);
    $stmt->execute();


    $inserted = $this->getById($id);



    return $inserted;
  }


  public function updateById($id, $data)
  {
    throw new Exception("Method not implemented");
  }

  public function deleteById($id)
  {
    throw new Exception("Method not implemented");
  }
}
