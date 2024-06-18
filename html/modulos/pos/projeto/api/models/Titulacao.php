<?php

class Titulacao {
  public $id_titulacao;
  public $titulacao;
  public $valor_padrao;

  public function build($id_titulacao, $titulacao, $valor) {
    $this->id_titulacao = $id_titulacao;
    $this->titulacao = $titulacao;
    $this->valor_padrao = $valor;

    return $this;
  }

  public function isSelected($id)
  {
    return isset($this->id_titulacao) && $id != '' && $this->id_titulacao == $id;
  }
}