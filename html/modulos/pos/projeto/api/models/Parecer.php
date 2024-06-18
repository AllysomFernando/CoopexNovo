<?php

class Parecer {
  public $id;
  public $descricao = 'APROVADO' | 'RECUSADO' | 'REAVALIACAO';

  /**
   * Instância um Parecer
   *
   * Instância um Parecer
   *
   * @param number $id id
   * @param string $descricao descricao - `APROVADO` | `RECUSADO` | `REAVALIACAO`
   * @return Parecer
   **/
  public function __construct($id, $descricao) {
    $this->id = $id;
    $this->descricao = $descricao;
  }
}