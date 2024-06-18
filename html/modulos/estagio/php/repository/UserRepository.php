<?php

class UserRepository
{
  private $db;

  function __construct($conn)
  {
    $this->db = $conn;
  }

  /**
   * Busca relatórios de estágio baseado na pessoa que está logada no sistema
   *
   * Cada nível de acesso possui uma query diferente que deve ser feita
   * o aluno só pode ver seus próprios estágios, o professor vê todos os estágios que ele é docente
   * e o coordenador e admnistrador vêem todos os relatórios dos seus devidos departamentos
   *
   * @param number $userAccessLevel Id do nivel de acesso do usuario
   * @param number $id_pessoa Id da pessoa que está logada na sessão
   * @return array Retorna um array com os estágio encontrados
   **/
  public function getEstagiosByUserAccessLevel($userAccessLevel, $id_pessoa)
  {
    $this->db->exec("set names utf8");
    $pre_sql = "SELECT
    a.nome,
    est.id_estagio,
    est.id_curso,
    est.id_disciplina,
    est.id_periodo,
    est.id_docente,
    est.carga_horaria,
    est.empresa,
    est.funcao,
    DATE_FORMAT(est.data_cadastro, '%d/%m/%Y') as data_cadastro,
    est.excluido ";

    $sql = "";

    switch ($userAccessLevel) {
      case 6: // Aluno
        $sql = "FROM estagio.estagio est INNER JOIN coopex.pessoa a ON est.id_pessoa = a.id_pessoa WHERE est.id_pessoa = :id_pessoa AND est.excluido = 0";
        break;

      case 10: // Coordenador
        $sql = "FROM coopex.pessoa p
        INNER JOIN coopex.departamento_pessoa dp ON p.id_pessoa = dp.id_pessoa
        INNER JOIN estagio.estagio est ON est.id_curso = dp.id_departamento
        INNER JOIN coopex.pessoa a ON est.id_pessoa = a.id_pessoa
        WHERE dp.id_pessoa = :id_pessoa AND est.excluido = 0";
        break;

      case 5: // Professor
        $sql = "FROM coopex.pessoa p 
        INNER JOIN estagio.estagio est ON p.id_pessoa = est.id_docente 
        INNER JOIN coopex.pessoa a ON est.id_pessoa = a.id_pessoa 
        WHERE p.id_pessoa = :id_pessoa AND est.excluido = 0";
        break;

      default:
      $sql = "FROM coopex.pessoa p
      INNER JOIN coopex.departamento_pessoa dp ON p.id_pessoa = dp.id_pessoa
      INNER JOIN estagio.estagio est ON est.id_curso = dp.id_departamento
      INNER JOIN coopex.pessoa a ON est.id_pessoa = a.id_pessoa WHERE est.excluido = 0 GROUP BY est.id_estagio";
        
    }

    $final_sql = $pre_sql . $sql;

    $stmt = $this->db->prepare($final_sql);
    $stmt->bindParam(':id_pessoa', $id_pessoa);
    $stmt->execute();

    $res = $stmt->fetchAll(PDO::FETCH_OBJ);

    return $res;
  }
}
