<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Multiusuário</title>
</head>
<body>
    <h1>Chat Multiusuário</h1>
    <div id="chat"></div>
    <input type="text" id="messageInput" placeholder="Digite sua mensagem">
    <button onclick="sendMessage()">Enviar</button>

    <script>
        const eventSource = new EventSource('server.php?id=1');

        eventSource.onmessage = function(event) {
            const chatDiv = document.getElementById('chat');
            const message = JSON.parse(event.data);
            chatDiv.innerHTML += `<p>${message.user}: ${message.message}</p>`;
        };

        eventSource.onerror = function(event) {
            console.error('Erro no EventSource:', event);
            eventSource.close();
        };

        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value;
            const user = 'User'; // Substitua por lógica de autenticação ou obtenção do nome do usuário
            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `message=${encodeURIComponent(message)}&user=${encodeURIComponent(user)}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao enviar mensagem');
                }
                messageInput.value = '';
            })
            .catch(error => console.error('Erro ao enviar mensagem:', error));
        }
    </script>
</body>
</html>
