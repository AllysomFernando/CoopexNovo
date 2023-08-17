
<?
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once('conecta.php');
include __DIR__ . "/PHPMailer-master/PHPMailerAutoload.php";
// echo __DIR__;
extract($_POST);

$sql = 'select * from coopex_fhsl.template where idTemplate =' . $idTemplate;
$stm = $coopex->prepare($sql);
$stm->execute();
$dados = $stm->fetchAll(PDO::FETCH_OBJ);

if ($filtro == '1') {

    $sqlT = "select nome, email from coopex_fhsl.catalogo";
    $stmT = $coopex->prepare($sqlT);
    $stmT->execute();
    $dadosT = $stmT->fetchAll(PDO::FETCH_OBJ);

    foreach ($dadosT as $row) {
        $result  = email($row->email, $row->nome, $dados[0]->assunto, $dados[0]->mensagem);
        if ($result == '2') {
            echo ('erro');
        }
    }
} else if ($filtro == '2') {
    $sqlF = "select nome, email from coopex_fhsl.catalogo where funcao = " . $funcao;
    $stmF = $coopex->prepare($sqlF);
    $stmF->execute();
    $dadosF = $stmF->fetchAll(PDO::FETCH_OBJ);
    foreach ($dadosF as $row) {
        $result  = email($row->email, $row->nome, $dados[0]->assunto, $dados[0]->mensagem);
        if ($result == '2') {
            echo ('erro');
        }
    }
    header('Location:/fhsl/newslatter/templates/list');
} else if ($filtro == '3') {
    $sqlS = "select nome, email from coopex_fhsl.catalogo where setor = " . $setor;
    $stmS = $coopex->prepare($sqlS);
    $stmS->execute();
    $dadosS = $stmS->fetchAll(PDO::FETCH_OBJ);
    foreach ($dadosS as $row) {
        $result  = email($row->email, $row->nome, $dados[0]->assunto, $dados[0]->mensagem);
        if ($result == '2') {
            echo ('erro');
        }
    }
}



function email($destino, $nome, $assunto, $corpo)
{
    $mail = new PHPMailer();

    $mail->IsSMTP();

    // Enviar por SMTP 
    $mail->Host = "localhost";

    // Você pode alterar este parametro para o endereço de SMTP do seu provedor 
    $mail->Port = 25;

    // Usar autenticação SMTP (obrigatório) 
    $mail->SMTPAuth = false;

    // Usuário do servidor SMTP (endereço de email) 
    // obs: Use a mesma senha da sua conta de email 
    // $mail->Username = '6c80ec3bae979d'; 
    // $mail->Password = '4ec0979d724ac8'; 

    // Configurações de compatibilidade para autenticação em TLS 
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // Você pode habilitar esta opção caso tenha problemas. Assim pode identificar mensagens de erro. 
    // $mail->SMTPDebug = 2; 

    // Define o remetente 
    // Seu e-mail 
    $mail->From = 'contato@fag.edu.br';

    // Seu nome 
    $mail->FromName = 'newsletter';

    // Define o(s) destinatário(s) 
    //$mail->AddAddress('vagnerkuntz@fag.edu.br', 'EAD - Contato');
    $mail->AddAddress($destino, $nome);

    // Opcional: mais de um destinatário
    // $mail->AddAddress('fernando@email.com'); 

    // Opcionais: CC e BCC
    // $mail->AddCC('fernando@fag.edu.br', 'teste site');
    // $mail->AddBCC('roberto@gmail.com', 'Roberto'); 

    // Definir se o e-mail é em formato HTML ou texto plano 
    // Formato HTML . Use "false" para enviar em formato texto simples ou "true" para HTML.
    $mail->IsHTML(true);

    // Charset (opcional) 
    $mail->CharSet = 'UTF-8';

    // Assunto da mensagem 
    $mail->Subject = $assunto;

    $body = '
        <style>
            body{ font-family:Tahoma, Geneva, sans-serif; font-size:12px; margin:0; margin-top:10px; }
            a:link, a:visited, a:active { color: #000000; text-decoration: none; }
            a:hover { text-decoration:underline; color: #000000; }
            p{ margin-left:20px; line-height:17px; }
        </style>
        <div style="max-width:60%; margin:0 auto">
            <a href="http://fhsl.org.br">
                <div style="max-width:100%">
                    <img style="width:100%" src="https://coopex.fag.edu.br/modulos/fhsl/newslatter/img/top.png">
                </div>
            </a>
            <br><br>
            <div style="width:90%; margin: 0 auto">			
                ' . $corpo . '			
            </div>
            
            <br><br><br>
            <div style="max-width:100%">
            <img style="width:100%" src="https://coopex.fag.edu.br/modulos/fhsl/newslatter/img/footer.png">
            </div>
        </div>       
        <div style="font-weigh:300;font-size:11px;color:#555555">
            Este email foi gerado automaticamente.
        </div>';


    // Corpo do email 
    $mail->Body = $body;

    if ($mail->Send()) {
        header('Location: https://coopex.fag.edu.br/fhsl/newslatter/templates/list');
    }
}

?>