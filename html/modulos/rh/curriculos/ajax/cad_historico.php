<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once('conecta.php');

#VERFIFICA SE O FORMULÁRIO FOI ENVIADO
if ($_POST) {
  if (isset($_POST['id_painel']) && $_POST['id_painel'] <> 0) {
    $sql = "UPDATE candidato_vaga SET obs = :obs  where id_candidato = :id";
    $stm = $coopex->prepare($sql);
    $stm->bindValue(':obs', $_POST['historico']);
    $stm->bindValue(':id', $_POST['id_painel']);
    $stm->execute();
    $dados = $stm->fetchAll(PDO::FETCH_OBJ);
    $retorno = array('status' => 'ok');
    echo json_encode($retorno);
    exit();
  }
}
