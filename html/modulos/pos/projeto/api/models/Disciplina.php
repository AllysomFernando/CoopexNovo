<?php

/**
 * Entidade Disciplina
 */
class Disciplina {
  public $id;
  public $id_projeto;
  public $nome;
  public $id_docente;
  public $carga_horaria;
  public $ementa;
  public $cadastrado_por;

  /**
   * Instância uma Disciplina
   *
   * Esta classe é um modelo representativo da entidade disciplina no banco de dados.
   * Cria uma instância de uma Disciplina
   *
   * @param int $id Id da disciplina
   * @param number $id_projeto Id do curso que a disciplina está atrelada
   * @param string $nome Nome da disciplina
   * @param number $id_docente Id do docente da disciplina
   * @param number $carga_horaria Carga horaria da disciplina em horas
   * @param string $ementa Ementa da disciplina
   * @param string $cadastrado_por Ementa da disciplina
   * @return Disciplina
   **/
  public function build($id, $id_projeto, $nome, $id_docente, $carga_horaria, $ementa, $cadastrado_por) {
    $this->id = $id;
    $this->id_projeto = $id_projeto;
    $this->nome = $nome;
    $this->id_docente = $id_docente;
    $this->carga_horaria = $carga_horaria;
    $this->ementa = $ementa;
    $this->cadastrado_por = $cadastrado_por;
    return $this;
  }
}