<?php

/**
 * Entidade Justificativa
 *
 * Representa a tabela `justificativa` no banco de dados
 */
class Justificativa
{
  public $id;
  public $id_projeto;
  public $descricao;
  public $contribuicao;

  /**
   * Instância uma Justificativa
   *
   * Cria uma instância de uma Justificativa
   *
   * @param int $id Id da justificativa
   * @param int $id_projeto Id do curso - Toda justificativa deve estar relacionada a um curso
   * @param string $descricao Descrição da justificativa - Desenvolva aqui a justificativa para a proposição deste projeto (mudanças e avanços no mercado, falta de profissionais capacitados, grande demanda...)
   * * @param string $contribuicao Texto descrevendo a contribuição do curso - Como o curso proposto contribuirá para o desenvolvimento profissional e acadêmico dos alunos
   * @return Justificativa
   **/
  public function build($id, $id_projeto, $descricao, $contribuicao)
  {
    $this->id = $id;
    $this->id_projeto = $id_projeto;
    $this->descricao = $descricao;
    $this->contribuicao = $contribuicao;
    return $this;
  }
}
