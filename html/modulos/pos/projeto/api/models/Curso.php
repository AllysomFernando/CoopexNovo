<?php

/**
 * Entidade Curso
 * 
 * Representa a entidade Curso no banco de dados.
 */
class Curso {
  public $id;
  public $id_pessoa;
  public $id_area;
  public $id_campus;
  public $nome;
  public $carga_horaria;
  public $numero_vagas;
  public $data_cadastro;
  public $excluido;
  
  /**
   * Instância um Curso
   *
   * @param int $id Id do curso
   * @param int $id_pessoa Pessoa que cadastrou o projeto - Referência o campo id da tabela `pessoa`
   * @param int $id_campus Campus que está ofertando o curso - Referência o campo id da tabela `campus`
   * @param int $area_id Área do curso - Referência o campo id da tabela `area`
   * @param string $nome Nome do curso
   * @param number $carga_horaria Carga horária do curso em horas - Ex: 360
   * @param int $numero_vagas Número de vagas iniciais disponíveis para o curso
   * @param int $realizacao_id Referência o campo id da tabela `realizacao`
   * @param string $data_cadastro Data em que o projeto foi cadastrado
   * @param int $excluido Define se o curso está excluido ou não - 0: Não excluido, 1: Excluido 
   * @return Curso
   **/
  public function __construct($id, $id_pessoa, $area_id, $id_campus, $nome, $carga_horaria, $numero_vagas, $data_cadastro, $excluido) {
    $this->id = $id;
    $this->id_pessoa = $id_pessoa;
    $this->id_area = $area_id;
    $this->id_campus = $id_campus;
    $this->nome = $nome;
    $this->carga_horaria = $carga_horaria;
    $this->numero_vagas = $numero_vagas;
    $this->data_cadastro = $data_cadastro;
    $this->excluido = $excluido;
  }

  public function isCampusSelected($id) {
    return $id != '' && $this->id_campus == $id;
  }
}