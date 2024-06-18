<?php

class PosDatabase
{
  public $db;
  public $table;
  function __construct()
  {
    // $this->connect();
  }

  /**
   * Inicia uma conexão com o banco de dados
   *
   * Instância um PDO com a conexão do banco e atribui a conexão para a variável `db` na classe
   */
  public function connect()
  {
    global $coopex;
    $this->db = new PDO("mysql:dbname=pos;host=10.0.0.41", 'fernando', 'indioveio', array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    ));
  }

  /**
   * Encerra a conexão com o banco de dados
   *
   * Encerra a conexão com o banco de dados definindo a varíavel de conexão como `null`
   */
  public function disconnect()
  {
    $this->db = null;
  }

  /**
   * Insere um registro na tabela `coopex.log`
   *
   * Registra um log da query que foi executada no banco.
   * Para fins de debug e análise de problemas é sempre necessário chamar este método após uma manipulação dos dados
   * através de métodos `INSERT`, `DELETE`, `UPDATE`
   *
   * @param string $tabela Tabela onde a query está sendo executada
   * @param string|int $id_registro Id do registro que está sendo inserido, deletado ou atualizado
   * @param string $operacao Nome da operação que está sendo executada na query
   * @param string $query A query que foi executada
   * @param string $dados Os dados utilizados na query
   * @param string $erro `opcional` Caso a query lance uma exception é necessário capturar a mensagem da exception e passar de parâmetro no método
   * @return void
   **/
  public function log($tabela, $id_registro, $operacao, $query, $dados, $erro = '')
  {
    $id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];

    $sql = "INSERT INTO coopex.log (id_pessoa, tabela, id_registro, operacao, data_log, comando, dados, erro) VALUES (:id_pessoa, :tabela, :id_registro, :operacao, now(), :query, :dados, :erro)";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(":id_pessoa", $id_pessoa);
    $stmt->bindValue(":tabela", $tabela);
    $stmt->bindValue(":id_registro", $id_registro);
    $stmt->bindValue(":operacao", $operacao);
    $stmt->bindValue(":query", $query);
    $stmt->bindValue(":dados", $dados);
    $stmt->bindValue(":erro", $erro);

    $stmt->execute();
  }
}
