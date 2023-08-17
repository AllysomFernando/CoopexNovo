<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once('conecta.php');

#VERFIFICA SE O FORMULÁRIO FOI ENVIADO
if ($_POST) {
  $data = date("Y-m-d H:i:s");
  if ($_POST['id'] == 0) {
    $iserirVaga = 'INSERT INTO vagas (titulo,descricao,local) VALUES ("' . $_POST['titulo'] . '","' . $_POST['descricao'] . '",' . $_POST['cidade'] . ')';
    echo $inserirHistorico = 'INSERT INTO historico_vaga (abertura,expectativa,id_vaga,_status,area,perfil,recrutador) VALUES("' . $data . '","' . $_POST['expectativa'] . '",' . run($iserirVaga, 'insert') . ',1,"' . $_POST['area'] . '","' . $_POST['perfil'] . '","' . $_POST['recrutador'] . '")';
    if (run($inserirHistorico, 'insert') > 0) {
      header('Location: /rh/vagas');
    }
  } else if ($_POST['id'] <> 0) {
    $select = 'SELECT a.*, b.* FROM historico_vaga AS a INNER JOIN vagas AS b ON a.id_vaga = b.id_vaga  WHERE a.id = ' . $_POST['id'] . ' ORDER BY a.id DESC LIMIT 1';
    $result = run($select, 'select');

    if ($result[0]->_status <> $_POST['status']) {
      if ($_POST['status'] <> 1) {
        echo $updateVaga = "update historico_vaga set encerramento = '" . $data . "', _status = " . $_POST['status'] . " where id = " . $_POST['id'];
        if (run($updateVaga, 'update') == 1) {
          header('Location: /rh/vagas');
         
        }
      } else {
        echo $inserirHistorico = 'INSERT INTO historico_vaga (abertura,expectativa,id_vaga,_status,area,perfil,recrutador) VALUES ("' . $data . '","' . $_POST['expectativa'] . '",' . $_POST['id_vaga'] . ',' . $_POST['status'] . ',"' . $_POST['area'] . '","' . $_POST['perfil'] . '","' . $_POST['recrutador'] . '")';
        if (run($inserirHistorico, 'insert') > 0) {
          header('Location: /rh/vagas');
        }
      }
    } else {
      echo $updateVaga = "UPDATE vagas set titulo = '" . $_POST['titulo'] . "', descricao = '" . $_POST['descricao'] . "', local = " . $_POST['cidade'] ." where id_vaga = " . $_POST['id_vaga'];
      echo $historico = "UPDATE historico_vaga set area = '" . $_POST['area'] . "',perfil = '" . $_POST['perfil'] . "',recrutador='" . $_POST['recrutador']."' where id = " . $_POST['id'];
      run($updateVaga, 'update');
      run($historico, 'update');
      header('Location: /rh/vagas');
    }
  }
}
function run($sql, $op)
{
  global $coopex;
  $stm = $coopex->prepare($sql);
  $stm->execute();
  if ($op == 'insert') {
    return $coopex->lastInsertId();
  } else if ($op == 'update') {
    if ($stm->rowCount() > 0) {
      return 1;
    }
  } else if ($op == 'select') {
    $dados = '';
    return $stm->fetchAll(PDO::FETCH_OBJ);
  }
}
