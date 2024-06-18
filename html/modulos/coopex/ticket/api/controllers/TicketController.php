<?php

require_once __DIR__ . "/../errors/Erro.php";

class TicketController
{
  private $repository;
  public $mailer;

  public function __construct($repository, $mailer)
  {
    $this->repository = $repository;
    $this->mailer = $mailer;
  }

  public function createTicket($post)
  {
    if ($post->descricao == "" || $post->titulo == "") {
      return new Erro("Campos ausentes: descricao ou titulo");
    }

    $response = $this->repository->create($post);

    return $response;
  }

  public function prepareEmail($post, $id_ticket)
  {

    $this->mailer->setRemetente("fernando@fag.edu.br");
    $this->mailer->setAssunto("Atendimento Ticket {$id_ticket} - " . $post->titulo);
    $this->mailer->setBody($post->descricao);

    $admins = [
      $this->setAdminForEmail('Guilherme Perinotti', 'guilherme.ricardo@fag.edu.br'),
      $this->setAdminForEmail('Allysom Fernando', 'afluis@fag.edu.br'),
      $this->setAdminForEmail('Fernando Incerti', 'fernando@fag.edu.br')
    ];

    $this->mailer->addDestinatario($admins);

    return $this->mailer;
  }

  public function getAllTickets()
  {
    return $this->repository->getAll();
  }

  public function getAllTicketsByUserId($id)
  {
    return $this->repository->getAllByUserId($id);
  }

  public function getTicketById($id)
  {

    $exists = $this->repository->existsById($id);

    if ($exists) return $this->repository->getById($id);

    return new Erro("Ticket não encontrado");
  }

  public function updateById($id, $ticket)
  {
  }

  public function setFinalizado($id)
  {
    $ticket = $this->getTicketById($id);
    $ticket->status = 1;

    $update = $this->repository->updateById($id, $ticket);

    return $update;
  }

  public function setCancelado($id)
  {
    $ticket = $this->getTicketById($id);
    $ticket->status = 2;

    $update = $this->repository->updateById($id, $ticket);

    return $update;
  }

  public function setAdminForEmail($nome, $email)
  {
    return array('nome' => $nome, 'email' => $email);
  }
}
