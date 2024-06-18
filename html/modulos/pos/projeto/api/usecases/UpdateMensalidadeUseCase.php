<?php

require_once __DIR__ . "/../factory/RepositoryFactory.php";
require_once __DIR__ . "/../models/Mensalidade.php";

class UpdateMensalidadeUseCase {

  public $repositories;

  public function __construct()
  {
    $this->repositories = new RepositoryFactory();
  }
  public function execute($body) {
    $this->validateFields($body, ['id_projeto', 'mensalidade_valor', 'mensalidade_parcelas']);

    $mensalidade = new Mensalidade();
    $mensalidade->build(null, $body['id_projeto'], $body['mensalidade_valor'], $body['mensalidade_parcelas'], date('Y-m-d'));

    $inserted = $this->repositories->mensalidade->updateByCursoId($body['id_projeto'], $mensalidade);

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