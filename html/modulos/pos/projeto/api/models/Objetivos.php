<?php

/**
 * Entidade Objetivos
 *
 * Representa a tabela `objetivos` no banco de dados.
 */
class Objetivos {
  public $id;
  public $id_projeto;
  public $geral;
  public $especifico;

  public function build($id, $id_projeto, $geral, $especifico) {
    $this->id = $id;
    $this->id_projeto = $id_projeto;
    $this->geral = $geral;
    $this->especifico = $especifico;
    return $this;
  }
}