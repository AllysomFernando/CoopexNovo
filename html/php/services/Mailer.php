<?php

require __DIR__ . '/../class.phpmailer.php';

class Mailer
{
  public $client;
  public $remetente;
  public $assunto;
  public $destinatarios;
  public $body;
  public $wasSent;

  public function __construct()
  {
    $this->makePhpMailerInstance();
    $this->destinatarios = [];
  }

  private function makePhpMailerInstance()
  {
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Port = 25;
    $mail->Host = "localhost";
    $mail->SMTPAuth = false;
    $mail->SMTPOptions = array(
      'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
      )
    );
    $mail->FromName = 'Sistema Coopex';
    $mail->Mailer = "smtp";
    $mail->isHTML(true);

    $this->client = $mail;
  }

  public function setRemetente($remetente)
  {
    $this->remetente = $remetente;
  }

  public function setBody($body)
  {
    $this->body = $body;
  }

  public function addDestinatario($array)
  {
    foreach ($array as $item) {
      array_push($this->destinatarios, $item);
    }
  }

  public function setAssunto($str)
  {
    $this->assunto = $str;
  }

  public function getAssunto() {
    return $this->assunto;
  }

  public function send()
  {
    try {
      $this->client->From = $this->remetente;
      $this->client->Subject = $this->assunto;
      $this->client->Body = $this->body;

      foreach ($this->destinatarios as $destinatario) {
        $this->client->AddAddress($destinatario['email'], $destinatario['nome']);
      }

      $response = $this->client->Send();
      $this->wasSent = $response;
    } catch (\Throwable $th) {
      throw new RuntimeException($th->getMessage());
    }
  }

  public function getTemplate($email_data, $template_url) {
    ob_start();

    include __DIR__ .  '/../../modulos/coopex/email/template/' . $template_url;
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
  }
}
