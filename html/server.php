<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
date_default_timezone_set('America/Sao_Paulo');
// Função para obter mensagens do banco de dados
require_once 'php/mysql.php'; // Arquivo de configuração do banco de dados
function getMessages($id){
  global $coopex;
  $sql = "SELECT * FROM colegio_app.chat where id_chat = $id";
  $stmt = $coopex->query($sql);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para enviar mensagens para o cliente
function sendMessage($data){
  //$data = date("h:i:s");
  echo "data: $data\n\n";
  ob_flush();
  flush();
}

// Loop para enviar mensagens para os clientes quando houver novas mensagens no banco de dados
while (true) {
  $messages = getMessages($_GET['id']);
  if (!empty($messages)) {
      foreach ($messages as $message) {
          sendMessage(json_encode($message));
      }
  }
  sleep(2); // Aguarda 2 segundos antes de verificar novamente
}

