<?php

class Mensalidade {
  public $id;
  public $id_projeto;
  public $mensalidade;
  public $numero_parcelas;
  public $data_vigente;

  public function build($id, $id_projeto, $mensalidade, $numero_parcelas, $data_vigente) {
    $this->id = $id;
    $this->id_projeto = $id_projeto;
    $this->mensalidade = $mensalidade;
    $this->numero_parcelas = $numero_parcelas;
    $this->data_vigente = $data_vigente;
    return $this;
  }
}