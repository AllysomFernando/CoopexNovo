<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once('conecta.php');


if ($_POST) {
  if (isset($_POST['id']) && $_POST['id'] <> 0) {
    // print_r('chegamos aqui');
    $old = "SELECT * FROM candidato_vaga WHERE id_candidato = :id_candidato";
    $stm2 = $coopex->prepare($old);
    $stm2->bindValue(':id_candidato',$_POST['candidato']);
    $stm2->execute();
    $dados = $stm2->fetchAll(PDO::FETCH_OBJ);
     
    $historico= '';
    $campo = '';
    if ($dados[0]->classificacao != $_POST['classificacao']){
      $historico =$historico.' Alterado Classificação de  ' . $dados[0]->classificacao .' - para '.$_POST['classificacao'];
      $campo =$campo.'  classificacao  -';
      // print_r('chegamos aqui');
    }
    if ($_POST['vaga_old'] != $_POST['vaga']){

      $historico =$historico.'  Alterado Vaga de ' . $_POST['vaga_old'] .' - para '.$_POST['vaga'];

      $campo =$campo.'  Vaga -  ';
    }
    
    $log = 'INSERT INTO historico_candidato (historico, dia,id_candidato,campo_alterado) VALUES(:historico,:data_, :candidato,:campo)';
    $stm1 = $coopex->prepare($log);
    $stm1->bindValue(':historico',$historico);
    $stm1->bindValue(':data_',date('Y/m/d H:i:s'));
    $stm1->bindValue(':campo',$campo);
    $stm1->bindValue(':candidato',$_POST['candidato']);
    $stm1->execute();

    if($dados[0]->classificacao <> $_POST['classificacao']){
      $sql = "UPDATE candidato_vaga SET classificacao = :class where id_candidato = :id";
      $stm = $coopex->prepare($sql);
      $stm->bindValue(':class', $_POST['classificacao']);
      $stm->bindValue(':id', $_POST['candidato']);
      $stm->execute();  
    }
    $sql = "UPDATE painel_vaga SET id_vaga = :vaga  where id = :id";
    $stm = $coopex->prepare($sql);
    $stm->bindValue(':vaga', $_POST['vaga']);
    $stm->bindValue(':id', $_POST['id']);
    $stm->execute();
    header('Location: /rh/vagas');
  }
}