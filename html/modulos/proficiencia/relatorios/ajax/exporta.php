
<?php session_start();
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');


require_once("conecta.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// $id_reoferta = $_GET['reoferta'];

//selecionas os boletos das matrículas
$evento = $_POST['id'];

$sql = "SELECT * FROM evento_valores where id_evento = :evento";
$stm = $conexao->prepare($sql);
$stm->bindValue(':evento', $evento);
$stm->execute();
$dados = $stm->fetchAll(PDO::FETCH_OBJ);

foreach ($dados as $i => $value) {

  if ($i == 0) {

    $select = "SELECT
  	evento_pessoa.id_usuario,
  	evento_pessoa.nome,
  	evento_pessoa.data_nascimento,
  	evento_inscricao.id_inscricao,
  	usuario.email,
  	evento_pessoa.email as email1,
  	evento_pessoa.endereco_cidade,
  	evento_pessoa.telefone1,
  	evento_pessoa.telefone2,
  	evento_inscricao.pago,
  	evento_pessoa.cpf,
  	evento_inscricao.id_valor,
  	evento_inscricao.data_inscricao
  FROM
  	evento_inscricao
  LEFT JOIN evento_pessoa ON evento_pessoa.id_pessoa = evento_inscricao.id_pessoa
  LEFT JOIN usuario ON usuario.id_usuario = evento_pessoa.id_usuario
  WHERE
  	evento_inscricao.id_evento = :evento and evento_inscricao.id_valor = :valor
  ORDER BY evento_inscricao.data_inscricao";
    $stm1 = $conexao->prepare($select);
    $stm1->bindValue(':evento', $evento);
    $stm1->bindValue(':valor', $value->id_valor);
    $stm1->execute();
    $dados1 = $stm1->fetchAll(PDO::FETCH_OBJ);

    $fp = fopen("inscritos_ingles.csv", "w+"); // o "a" indica que o arquivo será sobrescrito sempre que esta função for executada.
    $escreve = fwrite($fp, "USUARIO;NOME;NASCIMENTO;INSCRICAO;EMAIL;EMAIL1;CIDADE;TELEFONE1;TELEFONE2;PAGO;CPF;ID_VALOR;DATA_INSCRICAO");
    foreach ($dados1 as $registro) {
      $escreve = fwrite($fp, "\n$registro->id_usuario;$registro->nome;$registro->data_nascimento;$registro->id_inscricao;$registro->email;$registro->email1;$registro->endereco_cidade;$registro->telefone1;$registro->telefone2;$registro->pago;$registro->cpf;$registro->id_valor;$registro->data_inscricao");
    }
    fclose($fp);

    include("PHPExcel/Classes/PHPExcel/IOFactory.php");
    $objReader = PHPExcel_IOFactory::createReader('CSV');
    $objReader->setDelimiter(";"); // define que a separação dos dados é feita por ponto e vírgula
    $objReader->setInputEncoding('UTF-8'); // habilita os caracteres latinos.
    $objPHPExcel = $objReader->load('inscritos_ingles.csv'); //indica qual o arquivo CSV que será convertido
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('inscritos_ingles.xls'); // Resultado da conversão; um arquivo do EXCEL   

  } else {
    $select = "SELECT
  	evento_pessoa.id_usuario,
  	evento_pessoa.nome,
  	evento_pessoa.data_nascimento,
  	evento_inscricao.id_inscricao,
  	usuario.email,
  	evento_pessoa.email as email1,
  	evento_pessoa.endereco_cidade,
  	evento_pessoa.telefone1,
  	evento_pessoa.telefone2,
  	evento_inscricao.pago,
  	evento_pessoa.cpf,
  	evento_inscricao.id_valor,
  	evento_inscricao.data_inscricao
  FROM
  	evento_inscricao
  LEFT JOIN evento_pessoa ON evento_pessoa.id_pessoa = evento_inscricao.id_pessoa
  LEFT JOIN usuario ON usuario.id_usuario = evento_pessoa.id_usuario
  WHERE
  	evento_inscricao.id_evento = :evento and evento_inscricao.id_valor = :valor
  ORDER BY evento_inscricao.data_inscricao";
    $stm1 = $conexao->prepare($select);
    $stm1->bindValue(':evento', $evento);
    $stm1->bindValue(':valor', $value->id_valor);
    $stm1->execute();
    $dados1 = $stm1->fetchAll(PDO::FETCH_OBJ);

    $fp = fopen("inscritos_espanhol.csv", "w+"); // o "a" indica que o arquivo será sobrescrito sempre que esta função for executada.
    $escreve = fwrite($fp, "USUARIO;NOME;NASCIMENTO;INSCRICAO;EMAIL;EMAIL1;CIDADE;TELEFONE1;TELEFONE2;PAGO;CPF;ID_VALOR;DATA_INSCRICAO");
    foreach ($dados1 as $registro) {
      $escreve = fwrite($fp, "\n$registro->id_usuario;$registro->nome;$registro->data_nascimento;$registro->id_inscricao;$registro->email;$registro->email1;$registro->endereco_cidade;$registro->telefone1;$registro->telefone2;$registro->pago;$registro->cpf;$registro->id_valor;$registro->data_inscricao");
    }
    fclose($fp);


    $objReader = PHPExcel_IOFactory::createReader('CSV');
    $objReader->setDelimiter(";"); // define que a separação dos dados é feita por ponto e vírgula
    $objReader->setInputEncoding('UTF-8'); // habilita os caracteres latinos.
    $objPHPExcel = $objReader->load('inscritos_espanhol.csv'); //indica qual o arquivo CSV que será convertido
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('inscritos_espanhol.xls'); // Resultado da conversão; um arquivo do EXCEL   

  }
}
$retorno = array('status' => 'ok');
echo json_encode($retorno);


?>