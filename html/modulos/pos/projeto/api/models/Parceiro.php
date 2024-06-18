<?php 

class Parceiro {
  public $id;
  public $id_projeto;
  public $nome;
  public $cpf;

  public function build($id, $id_projeto, $nome, $cpf) {
    $this->id = $id;
    $this->id_projeto = $id_projeto;
    $this->nome = $nome;
    $this->cpf = $cpf;

    return $this;
  }
}