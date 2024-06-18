<?php

class Coordenador {
  public $id;
  public $id_pessoa;
  public $titulacao;
  public $nome;
  public $usuario;
  public $cpf;

  public function build($id, $id_pessoa, $titulacao, $nome, $usuario, $cpf) {
    $this->id = $id;
    $this->id_pessoa = $id_pessoa;
    $this->titulacao = $titulacao;
    $this->nome = trim(strtoupper($nome));
    $this->usuario = $usuario;
    $this->cpf = str_replace(array(".", "-", " ", "_"), "", $cpf);
    return $this;
  }
}