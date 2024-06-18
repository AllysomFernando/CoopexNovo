<?php

/**
 * Entidade ParecerCurso
 * 
 * Representa a tabela `parecer_curso` no banco de dados
 */
class ParecerCurso
{
  public $id;
  public $id_projeto;
  public $id_parecer;
  public $etapa;
  public $tipo_usuario;
  public $data;
  public $id_pessoa;
  public $observacao;

  public function build($id, $id_projeto, $id_parecer, $tipo_usuario, $data, $id_pessoa, $observacao)
  {
    $this->id = $id;
    $this->id_projeto = $id_projeto;
    $this->id_parecer = $id_parecer;
    $this->data = $data;
    $this->id_pessoa = $id_pessoa;
    $this->tipo_usuario = $tipo_usuario;
    $this->observacao = $observacao;

    $this->etapa = $this->setEtapa($this->id_parecer, $this->tipo_usuario);
  }

  public function isSelected($id)
  {
    return isset($this->id_parecer) && $id != '' && $this->id_parecer == $id;
  }

  private function setEtapa($id_parecer, $tipo_usuario)
  {
    switch ($id_parecer) {
      case 1:
        if ($tipo_usuario == 'COORDENACAO') {
          return 2;
        } elseif ($tipo_usuario == 'REITORIA') {
          return 3;
        } elseif ($tipo_usuario == 'PROPONENTE') {
          return 1;
        }
        break;
      case 2:
        if ($tipo_usuario == 'COORDENACAO') {
          return 4;
        } elseif ($tipo_usuario == 'REITORIA') {
          return 5;
        }
        break;
      default:
        return 1;
    }
  }
}
