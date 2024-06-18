<?php

/**
 * Representa a entidade `proponentes` do banco de dados
 *
 * Armazena informações relacionadas a entidades que estão proponto o curso de pós graduação
 * São estas: Instituição, Coordenção institucional, Coordenador Pedagógico e Parceiros (se houver)
 */
class Proponentes {
  public $id;
  public $id_projeto;
  public $instituicao;
  public $coordenacao_institucional;
  public $id_coordenador;

  /**
   * Instância um Proponente
   *
   * @param int $id Id do proponete
   * @param int $id_projeto Id do curso
   * @param string $instituicao Nome da instituição que está propondo o curso - `Cento Universitário Fundação Assis Gurgacz - FAG`
   * @param string $coordenacao_institucional Nome da coordenação da instituição que está propondo o curso - `Coordenação de Pós-Graduação da FAG - CPG`
   * @param string $id_coordenador Id do coordenador do curso - Referência o Id da tabela coordenador
   * @return Proponentes
   **/
  public function build($id, $id_projeto, $instituicao, $coordenacao_institucional, $id_coordenador) {
    $this->id = $id;
    $this->id_projeto = $id_projeto;
    $this->instituicao = $instituicao;
    $this->coordenacao_institucional = $coordenacao_institucional;
    $this->id_coordenador = $id_coordenador;

    return $this;
  }
}