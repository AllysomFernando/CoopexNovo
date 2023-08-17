<?php
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once('conecta.php');

#VERFIFICA SE O FORMULÁRIO FOI ENVIADO


$cpf = $_POST['cpf'];
$email = $_POST['email'];
$idioma = $_POST['idioma'];
// $senha = $_POST['senha'];
$pago = $_POST['pago'];
$inscricao = $_POST['inscricao'];
$evento = $_POST['evento'];

$sql = "SELECT * FROM evento_valores where id_evento = :evento and tipo = :tipo";
$stm = $conexao->prepare($sql);
$stm->bindValue(':evento', $evento);
$stm->bindValue(':tipo', $idioma);
$stm->execute();
$dados = $stm->fetchAll(PDO::FETCH_OBJ);

$update = "UPDATE evento_inscricao SET id_valor = :idioma,pago = :pago where id_inscricao = :inscricao";
$stm1 = $conexao->prepare($update);
$stm1->bindValue(':idioma', $dados[0]->id_valor);
$stm1->bindValue(':pago', $pago);
$stm1->bindValue(':inscricao', $inscricao);
$stm1->execute();

$update2 = "UPDATE evento_pessoa SET cpf = :cpf,email = :email where id_pessoa = :pessoa";
$stm2 = $conexao->prepare($update2);
$stm2->bindValue(':cpf', $cpf);
$stm2->bindValue(':email', $email);
$stm2->bindValue(':pessoa', $_POST['pessoa']);
$stm2->execute();

header('Location: /proficiencia/relatorios/inscritos');
