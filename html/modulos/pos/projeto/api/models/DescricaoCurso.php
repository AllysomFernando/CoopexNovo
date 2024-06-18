<?php

class DescricaoCurso {
  public $id;
  public $id_projeto;
  public $publico_alvo;
  public $perfil_aluno;
  public $pilares_curso;
  public $processo_selecao;

  public function build($id, $id_projeto, $publico_alvo, $perfil_aluno, $pilares_curso, $processo_selecao) {
    $this->id = $id;
    $this->id_projeto = $id_projeto;
    $this->publico_alvo = $publico_alvo;
    $this->perfil_aluno = $perfil_aluno;
    $this->pilares_curso = $pilares_curso;
    $this->processo_selecao = $processo_selecao;
  }
}