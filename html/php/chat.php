<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

require_once 'mysql.php'; // Arquivo de configuração do banco de dados

// Função para obter mensagens do banco de dados
function getMessages() {
    global $conn;
    $sql = "SELECT * FROM chat_messages ORDER BY id DESC LIMIT 10";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Função para enviar mensagens para o cliente
function sendMessage($data) {
    echo "data: $data\n\n";
    ob_flush();
    flush();
}

// Envie as mensagens existentes para o cliente
$messages = getMessages();
foreach ($messages as $message) {
    sendMessage(json_encode($message));
}

// Aguarde 2 segundos antes de enviar mensagens novamente
sleep(2);
