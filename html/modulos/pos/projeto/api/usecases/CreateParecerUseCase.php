<?php

require_once __DIR__ . "/../factory/RepositoryFactory.php";
require_once __DIR__ . "/../models/ParecerCurso.php";
require_once __DIR__ . "/../models/Curso.php";

class CreateParecerUseCase
{

  public $repositories;

  public function __construct()
  {
    $this->repositories = new RepositoryFactory();
  }
  public function execute($body)
  {
    $this->validateFields($body, ['id_projeto', 'id_pessoa', 'parecer_tipo', 'parecer_parecer']);

    $novo_parecer = new ParecerCurso();
    $novo_parecer->build(null, $body['id_projeto'], $body['parecer_parecer'], $body['parecer_tipo'], date("Y-m-d H:i:s"), $body['id_pessoa'], $body['parecer_observacao']);

    $inserted = $this->repositories->parecer_curso->create($novo_parecer);

    return $inserted;
  }

  private function validateFields($body, $fields)
  {
    foreach ($fields as $field) {
      if (!isset($body[$field])) {
        throw new RuntimeException("Campo obrigatório faltando: {$field}");
      }
    }
  }

}
