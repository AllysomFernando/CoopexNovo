<?php

class CursoValor {
  public $id;
  public $id_projeto;
  public $valor_customizado;
  public $especialista;
  public $mestre;
  public $doutor;

  public function build($id, $id_projeto, $valor_customizado, $especialista, $mestre, $doutor) {
    $this->id = $id;
    $this->id_projeto = $id_projeto;
    $this->valor_customizado = $valor_customizado;
    $this->especialista = $especialista;
    $this->mestre = $mestre;
    $this->doutor = $doutor;
  }
}