<?php
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once('conecta.php');

#VERFIFICA SE O FORMULÁRIO FOI ENVIADO
if ($_FILES['fileUpload']['name'] != NULL) {

  $arquivo_tmp = $_FILES['fileUpload']['tmp_name'];
  $dados = file($arquivo_tmp);

  foreach ($dados as $linha) {
    // $linha = trim($linha);
    $valor = explode(';', $linha);

    if ($valor[0] <> 'CPF') {
      $cpf = $valor[0];
      $nome = $valor[1];
      $linguagem = $valor[2];
      $nota = $valor[4];

      $cpf = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);

      $texto1 = "Certificamos, para fins de estudos de Mestrado e Doutorado, que <strong> " .  $nome . "</strong> foi aprovado no <strong>EXAME DE PROFICIÊNCIA EM LÍNGUA ESTRANGEIRA - " . strtoupper($linguagem) . " </strong>, no dia " . strftime('%d de %B de %Y', strtotime($_POST['data']))  . ", no Centro Universitário Assis Gurgacz, tendo atingido a nota <strong>" . $nota . "</strong>.";
      $texto2 = "EXAME DE PROFICIÊNCIA EM LÍNGUA ESTRANGEIRA -	" . $linguagem;
      $sql = "INSERT INTO certificados (texto,tipo,cpf,cadastro_data,titulo) values (:texto1,:texto2,:cpf,:dia,:titulo)";
      $stm = $conexao->prepare($sql);
      $stm->bindValue(':texto1', $texto1);
      $stm->bindValue(':texto2', '7');
      $stm->bindValue(':cpf', $cpf);
      $stm->bindValue(':dia', date("Y-m-d"));
      $stm->bindValue(':titulo', $texto2);
      $stm->execute();
      if ($conexao->lastInsertId()) {
        header('Location: /proeficiencia/certificados/gerador');
      }
    }
  }

  // $extensao = explode('.', $_FILES['fileUpload']['name']);
  // //$extensao = strtolower(substr($_FILES['fileUpload']['name'], -4));
  // $novo_nome = md5(time()) . '.' . $extensao[1];
  // move_uploaded_file($_FILES['fileUpload']['tmp_name'], 'uploads/' . $novo_nome);

  // $query1 = "INSERT INTO coopex_usuario.evento_inscricao_equipe (id_inscricao, id_evento, id_curso, titulo, tipo, turno,apresentado, arquivo) VALUES (:inscricao,:id_evento,:id_curso,:titulo,:tipo,:turno,:funcao,:arquivo)";
  // $stm1 = $conexao->prepare($query1);
  // $stm1->bindValue(':inscricao', $inscricao);
  // $stm1->bindValue(':id_evento', $evento);
  // $stm1->bindValue(':id_curso', $curso);
  // $stm1->bindValue(':titulo', $titulo);
  // $stm1->bindValue(':tipo', $tipo);
  // $stm1->bindValue(':turno', $turno);
  // $stm1->bindValue(':arquivo', $novo_nome);
  // $stm1->bindValue(':funcao', 0);
  // $stm1->execute();
  // $equipe = $conexao->lastInsertId();
}
