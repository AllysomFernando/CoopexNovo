<?php
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mb_internal_encoding('UTF-8');


require_once('conecta.php');



$consulta = "select b.id_evento from coopex_cascavel.projeto as a 
              inner join coopex_usuario.evento_projeto as b on a.id_projeto = b.id_projeto
              where a.titulo like '%proficiência%' and  a.projeto_periodo_final < CURDATE() ORDER BY a.id_projeto desc limit 1";
$stm2 = $conexao->prepare($consulta);
$stm2->execute();
$result = $stm2->fetchAll(PDO::FETCH_OBJ);

#VERFIFICA SE O FORMULÁRIO FOI ENVIADO
if ($_FILES['fileUpload']['name'] != NULL) {

  $arquivo_tmp = $_FILES['fileUpload']['tmp_name'];
  $dados = file($arquivo_tmp);



  foreach ($dados as $linha) {
    // $linha = trim($linha);
    $valor = explode(',', $linha);

    print_r($valor);
    

    if ($valor[0] <> 'CPF') {
      $cpf_antes = $valor[0];
      $cpf = $valor[0];
      $nome = $valor[1];
      $linguagem = $valor[2];
      $nota = $valor[4];

      $cpf = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
      $check = 'select id_certificado from certificados where cpf=:cpf and cadastro_usuario=:evento';
      $stm1 = $conexao->prepare($check);
      $stm1->bindValue(':cpf', $cpf);
      $stm1->bindValue(':evento', $result[0]->id_evento);
      $stm1->execute();
      $dados = $stm1->fetchAll(PDO::FETCH_OBJ);

      // print_r($cpf);
      // print_r($result[0]->id_evento);
      if (!isset($dados[0]->id_certificado)) {

        $texto1 = "Certificamos, para fins de estudos de Mestrado e Doutorado, que <strong> " .  mb_strtoupper(($nome)) . "</strong> foi aprovado(a) no <strong>EXAME DE PROFICIÊNCIA EM LÍNGUA ESTRANGEIRA - " . ($linguagem) . " </strong>, no dia " . utf8_encode(strftime('%d de %B de %Y', strtotime($_POST['data'])))  . ", no Centro Universitário Assis Gurgacz, tendo atingido a nota <strong>" . $nota . "</strong>.";
        $texto2 = "EXAME DE PROFICIÊNCIA EM LÍNGUA ESTRANGEIRA -	" . utf8_encode($linguagem);
        $sql = "INSERT INTO certificados (texto,tipo,cpf,cadastro_data,titulo,cadastro_usuario) values (:texto1,:texto2,:cpf,:dia,:titulo,:cadastro_usuario)";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(':texto1', $texto1);
        $stm->bindValue(':texto2', '7');
        $stm->bindValue(':cpf', $cpf);
        $stm->bindValue(':dia', date("Y-m-d"));
        $stm->bindValue(':titulo', $texto2);
        $stm->bindValue(':cadastro_usuario', $result[0]->id_evento);
        $stm->execute();
      }

      // $select = " ";
      // $stm1 = $conexao->prepare($select);
      // $stm1->bindValue(':cpf', $cpf_antes);
      // $stm1->execute();
      // $dados = $stm1->fetchAll(PDO::FETCH_OBJ);
      // echo utf8_encode(strftime('%d de %B de %Y', strtotime($_POST['data'])));
      // if($check->id_certificado){
      //   echo 'certificado já lançado.';
      // }

    }
  }
  if ($conexao->lastInsertId()) {
    header('Location: /proficiencia/certificados/gerador/1');
  }else{
    header('Location: /proficiencia/certificados/gerador/2');
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
