<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../../../php/class.phpmailer.php';


$titulo = "Lorem ipsum dolor sit amet";
$body = "Lorem ipsum dolor sit amet, consectetur adip incididunt et inter lobortis just sed diam euismod erat vol governing et dolore magna aliqu fugiat nulla pariatur";

function loadTemplate($titulo, $body)
{
    ob_start();

    $email_data['titulo_email'] = 'Atendimento Ticket 60';
    $email_data['titulo_ticket'] = 'Não consigo acessar meu ticket';
    $email_data['descricao_ticket'] = 'Alterei o projeto, e para mim só aparece como cadastrado a matéria de: Estágio em UTI cardiológica Acredito ser algum erro do sistema, peço que confirme se meu escopo ficou salvo corretamente!';
    $email_data['remetente'] = 'Guilherme perinotti';
    $email_data['data_envio'] = '14/06/2024';
    $email_data['id_ticket'] = 60;

    include 'template/ticket/ticket.php';
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function sendEmail($remetente, $destino, $assunto, $corpo)
{
    $template = loadTemplate($assunto, $corpo);

    $nome = 'COOPEX';

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Host = "localhost";
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Username = 'guilherme.ricardo@fag.edu.br';
    $mail->Password = 'Blonded@2016';
    $mail->Port = 25;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->isHTML(true);
    $mail->From = $remetente;
    $mail->FromName = $nome;
    $mail->Subject = utf8_decode($assunto);
    $mail->Body = $template;
    $mail->AddAddress($destino, $destino);

    $response = $mail->Send();

    return $response;
}

?>
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
    <?php

    $response = sendEmail("guilherme.ricardo@fag.edu.br", "guilherme.ricardo@fag.edu.br", "Contato de verificação", "Coopex");

    var_dump($response);

    ?>
</main>
</body>

</html>