<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once('conecta.php');

#VERFIFICA SE O FORMULÁRIO FOI ENVIADO
if ($_POST) {
  if (isset($_POST['id']) && $_POST['id'] <> 0) {

    $sql = "UPDATE vagas SET titulo = :titulo, descricao = :descricao, ativo = :ativo, `local` = :cidade where id_vaga = :id";
    $stm = $coopex->prepare($sql);
    $stm->bindValue(':titulo', $_POST['titulo']);
    $stm->bindValue(':descricao', $_POST['descricao']);
    $stm->bindValue(':ativo', $_POST['ativo']);
    $stm->bindValue(':id', $_POST['id']);
    $stm->bindValue(':cidade', $_POST['cidade']);
    $stm->execute();

    header('Location: /rh/vagas');
  } else if (isset($_POST['id'])) {
    $sql = "INSERT INTO vagas (titulo,descricao,ativo,`local`) values(:titulo,:descricao,:ativo,:local)";
    $stm = $coopex->prepare($sql);
    $stm->bindValue(':titulo', $_POST['titulo']);
    $stm->bindValue(':descricao', $_POST['descricao']);
    $stm->bindValue(':ativo', $_POST['ativo']);
    $stm->bindValue(':local', $_POST['local']);
    $stm->execute();
    header('Location: /rh/vagas');
  }
}

if (isset($_POST['acao']) && $_POST['acao'] == 'inativa') {
  $sql = "UPDATE vagas SET ativo = :ativo where id_vaga = :id";
  $stm = $coopex->prepare($sql);
  $stm->bindValue(':ativo', 0);
  $stm->bindValue(':id', $_POST['id_vaga']);
  $stm->execute();
  $retorno = array('status' => '1');
  echo json_encode($retorno);
}
if (isset($_POST['acao']) && $_POST['acao'] == 'apto') {
  $sql = "UPDATE candidato_vaga  SET classificacao = :clas where id_candidato = :id";
  $stm = $coopex->prepare($sql);
  $stm->bindValue(':clas', 2);
  $stm->bindValue(':id', $_POST['id_candidato']);
  $stm->execute();
  $retorno = array('status' => '1');
  echo json_encode($retorno);
}
if (isset($_POST['acao']) && $_POST['acao'] == 'inapto') {
  $sql = "UPDATE candidato_vaga  SET classificacao = :clas where id_candidato = :id";
  $stm = $coopex->prepare($sql);
  $stm->bindValue(':clas', 0);
  $stm->bindValue(':id', $_POST['id_candidato']);
  $stm->execute();
  $retorno = array('status' => '1');
  echo json_encode($retorno);
}

  