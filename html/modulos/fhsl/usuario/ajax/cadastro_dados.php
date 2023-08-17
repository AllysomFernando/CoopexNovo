<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once('conecta.php');

#VERFIFICA SE O FORMULÁRIO FOI ENVIADO
if ($_POST) {
  if ($_POST['id'] <> 0) {
    $sql = "UPDATE coopex_fhsl.users SET user = :user, passwd = :passwd where id = :id";
    $stm = $coopex->prepare($sql);
    $stm->bindValue(':user', $_POST['usuario']);
    $stm->bindValue(':passwd', base64_encode($_POST['passwd']));
    $stm->bindValue(':id', $_POST['id']);
    $stm->execute();
    header('Location: /fhsl/usuario');
  } else {
    $sql = "insert into coopex_fhsl.users (user,passwd) values (:user,:passwd)";
    $stm = $coopex->prepare($sql);
    $stm->bindValue(':user', $_POST['usuario']);
    $stm->bindValue(':passwd', base64_encode($_POST['passwd']));
    $stm->execute();
    header('Location: /fhsl/usuario');
  }
}
