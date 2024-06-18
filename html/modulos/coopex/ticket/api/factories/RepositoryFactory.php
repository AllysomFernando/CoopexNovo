<?php

include_once __DIR__ . "/../../../../../php/mysql.php";
require_once __DIR__ . "/../repository/CoopexAntigoRepository.php";
require_once __DIR__ . "/../repository/CoopexRepository.php";
require_once __DIR__ . "/../repository/MensagemRepository.php";
require_once __DIR__ . "/../repository/TicketRepository.php";
require_once __DIR__ . "/../repository/AtendimentoRepository.php";

class RepositoryFactory {

  public $coopex_antigo;
  public $coopex;
  public $ticket;
  public $mensagem;
  public $atendimento;

  public function __construct() {
    global $coopex_antigo;
    global $coopex;
    $this->coopex_antigo = new CoopexAntigoRepository($coopex_antigo);
    $this->ticket = new TicketRepository($coopex);
    $this->mensagem = new MensagemRepository($coopex);
    $this->atendimento = new AtendimentoRepository($coopex);
    $this->coopex = new CoopexRepository();
  }

}