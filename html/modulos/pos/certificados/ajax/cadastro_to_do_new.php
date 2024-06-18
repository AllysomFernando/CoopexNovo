<?php
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// mb_internal_encoding('UTF-8');
require_once('conecta.php');
require_once 'SagresRepository.php';

$sagres = new SagresRepository();

#VERFIFICA SE O FORMULÁRIO FOI ENVIADO
if ($_FILES['fileUpload']['name'] != NULL) {

  $arquivo_tmp = $_FILES['fileUpload']['tmp_name'];
  $dados = file($arquivo_tmp);

  foreach ($dados as $linha) {
    // $linha = trim($linha);
    $valor = explode(';', $linha);
    // print_r($valor);

    if ($valor[2] <> 'CPF') {

      if (strlen(trim($valor[2])) < 11) {
        $cpf_composto = trim($valor[2]);
        for ($i = 0; $i < (11 - strlen(trim($valor[2]))); $i++) {

          $cpf_composto = '0' . $cpf_composto;
        }
      } else {
        $cpf_composto = trim($valor[2]);
      }


      $cpf_antes = $valor[2];
      $cpf = $cpf_composto;
      $nome = $valor[0];
      $curso = trim($valor[1]);

      $cpf = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);

      $disciplinas = $sagres->getDisciplinasByIdCurso($valor[4]);
      $isPosToledo = isPosFromToledo($disciplinas[0]->crs_id_tp_curso);

      $certificado_texto = "";

      if ($isPosToledo) {
        $certificado_texto = 'A Faculdade Assis Gurgacz, tendo em vista a conclusão do curso "Lato Sensu" de <strong> ' . utf8_encode($curso) . ' </strong>do Programa de Pós-Graduação, confere o título de especialista a <br><br><strong style="font-size:30px"> ' . utf8_encode($valor[0]) . '</strong><br><br>e outorga-lhe o presente certificado para que possa dispor de seus direitos e prerrogativas legais.';
      } else {
        $certificado_texto = 'O Centro Universitário Assis Gurgacz, tendo em vista a conclusão do curso "Lato Sensu" de <strong> ' . utf8_encode($curso) . ' </strong>do Programa de Pós-Graduação, confere o título de especialista a <br><br><strong style="font-size:30px"> ' . utf8_encode($valor[0]) . '</strong><br><br>e outorga-lhe o presente certificado para que possa dispor de seus direitos e prerrogativas legais.';
      }

      //      print_r($texto);
      $insert = 'insert into coopex_usuario.certificados(texto,tipo,cpf,cadastro_data,titulo,portaria,curso) values (:texto,9,:cpf,:cadastro_date,:titulo,:portaria,:curso)';
      $stm = $conexao->prepare($insert);
      $stm->bindValue(':texto', $texto);
      $stm->bindValue(':cpf', $cpf);
      $stm->bindValue(':cadastro_date', date("Y-m-d H:i:s"));
      $stm->bindValue(':titulo', utf8_encode($curso));
      $stm->bindValue(':portaria', utf8_encode($valor[3]));
      $stm->bindValue(':curso', utf8_encode($valor[4] + ' - ' + $curso));

      $stm->execute();
    }
  }
  if ($conexao->lastInsertId()) {
    header('Location: /pos/certificados/gerador/1');
  } else {
    header('Location: /pos/certificados/gerador/2');
  }
}

function isPosFromToledo($id_disciplina)
  {
    $id_campus_toledo = [1100000001, 1100000002, 2100000003];
    $id_campus_cascavel = [1000000001, 1000000002, 1000000003];

    if (in_array($id_disciplina, $id_campus_toledo)) {
      return true;
    }

    return false;
  }