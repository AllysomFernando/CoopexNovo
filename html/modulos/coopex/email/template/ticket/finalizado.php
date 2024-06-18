<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Sistema Coopex</title>
</head>

<body>
  <!DOCTYPE html>
  <html>

  <head>
    <meta charset="UTF-8">
    <title><?php echo $email_data['titulo_email'] ?></title>
  </head>

  <body style="font-family: Arial, sans-serif; margin: 0; padding: 0;">
    <div style="background-color: #f2f2f2; padding: 20px;">
      <table style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
        <tr>
          <td>
            <img src="https://coopex.fag.edu.br/img/coopex_icone_192.png" style="max-width: 100%; height: 40px;" />
            <h1 style="color: #8762a4; font-size: 28px; margin-bottom: 4px;"><?php echo $email_data['titulo_email'] ?></h1>
          </td>
        </tr>

        <tr>
          <td>
            <p style="color: #666666; font-size: 16px; line-height: 20px;">
              <strong><?php echo $email_data['finalizado_por'] ?></strong> finalizou seu ticket e definiu o status como:
              <br />
              <br />
              <mark style="background-color: #8762a4; color: white; padding: 4px 10px; font-weight: 600; border-radius: 5px;"><?php echo $email_data['ticket_status'] ?></mark>
            </p>
            <br />
            <p style="color: #666666; font-size: 16px; line-height: 20px;">Caso seu problema não tenha sido solucionado corretamente <a href="https://coopex.fag.edu.br/coopex/ticket/cadastro">clique aqui</a> para abrir um novo ticket</p>
          </td>
        </tr>

        <tr>
          <td>
            <p style="color: #666666; font-size: 16px; line-height: 20px;"> <i>Esta é uma mensagem automática, por favor não responda este e-mail</i></p>
          </td>
        </tr>

        <br>
        <tr>
          <td>
            <div style="width: 100%; display: flex; align-items: center; justify-content: center;">
              <a href="https://coopex.fag.edu.br/coopex/ticket/atendimento/<?php echo $email_data['id_ticket'] ?>" style="padding: 16px 20px; color: #fff; background-color: #8762a4; text-decoration: none; border-radius: 5px; width: 100%; text-align: center; font-weight: bold; font-size: 16px;">Acessar Ticket</a>
            </div>
          </td>
        </tr>
      </table>
    </div>
  </body>

  </html>
</body>

</html>