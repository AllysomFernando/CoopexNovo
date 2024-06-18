<?php

/**
 * Entidade Realizacao
 * Armazena informações relacionadas ao período e local de realização do projeto de pós graduação
 */
class Realizacao {
  public $id;
  public $id_projeto;
  public $periodo;
  public $dias_semana;
  public $horario;
  public $local;

  /**
   * Instância uma Realizacao
   * 
   * Cria uma instância de uma Realizacao
   *
   * @param int $id Id
   * @param int $id_projeto Id do curso
   * @param string $periodo Periodo de realização do projeto - 6 meses
   * @param string $dias_semana Dias da semana que ocorrerão os encontros - Segunda à Sexta
   * @param string $horario Em quais horários ocorrerão os encontros - Segunda as 18:00 e Sexta as 22:00 
   * @param string $local Local onde ocorrerão os encontros - `Centro Universitário Fundação Assis Gurgacz - FAG` 
   * @return Realizacao
   **/
  public function __construct($id, $id_projeto, $periodo, $dias_semana, $horario, $local) {
    $this->id = $id;
    $this->id_projeto = $id_projeto;
    $this->periodo = $periodo;
    $this->dias_semana = $dias_semana;
    $this->horario = $horario;
    $this->local = $local;
  }
}