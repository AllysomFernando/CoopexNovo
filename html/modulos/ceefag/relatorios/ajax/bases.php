
<?php session_start();
$user = $_SESSION['coopex']['usuario']['id_pessoa'];
require_once("../../../../php/mysql.php");
require_once("conecta.php");

// ini_set('error_reporting', E_ALL); // mesmo resultado de: error_reporting(E_ALL);
// ini_set('display_errors', 1);

if ($_POST['grafico'] == 'recrutador') {
  // Listando nomes dos recrutadores do local do usuario
  $usuarios = "select a.recrutador from historico_vaga as a 
inner join vagas as b on a.id_vaga = b.id_vaga
INNER JOIN pessoa_local as c on c.id_local = b.`local` 
where a.recrutador is not null and c.id_pessoa = " . $user . "
group by a.recrutador";
  $stmUsuarios = $coopex->prepare($usuarios);
  $stmUsuarios->execute();
  $resultadoUsuarios = $stmUsuarios->fetchAll(PDO::FETCH_OBJ);
  $totais = array();
  $recrutadores = array();

  foreach ($resultadoUsuarios as $users) {
    $cont = "SELECT COUNT(*) AS TOTAL  FROM  historico_vaga where recrutador = '" . $users->recrutador . "' AND _status = 1";
    $stmCont = $coopex->prepare($cont);
    $stmCont->execute();
    $resultadoCont = $stmCont->fetchAll(PDO::FETCH_OBJ);

    $vagasPublicadas = $vagasPublicadas + $resultadoCont[0]->TOTAL;
    array_push($totais, $resultadoCont[0]->TOTAL);
    array_push($recrutadores, $users->recrutador);
  }
  array_push($totais, $vagasPublicadas);
  array_push($recrutadores, 'total');
  $retorno = array($recrutadores, $totais);
  echo json_encode($retorno);
}
if ($_POST['grafico'] == 'farol') {
  $totais = array();
  $label = array();

  //Vagas Congeladas
  $farol = "select count(*) as total from historico_vaga where _status = 2";
  $smtCongelada = $coopex->prepare($farol);
  $smtCongelada->execute();
  $resultCongelada = $smtCongelada->fetchAll(PDO::FETCH_OBJ);
  array_push($totais, $resultCongelada[0]->total);
  array_push($label, 'Congeladas');
  //Vagas que fecharam no prazo
  $prazo = "select count(*) as total from historico_vaga where encerramento = expectativa and _status <> 2 ";
  $smtPrazo = $coopex->prepare($prazo);
  $smtPrazo->execute();
  $resultPrazo = $smtPrazo->fetchAll(PDO::FETCH_OBJ);
  array_push($totais, $resultPrazo[0]->total);
  array_push($label, 'Fechou no prazo');
  //Vagas que estão no prazo seguro
  $seguro = "select count(*) as total from historico_vaga where (encerramento <= expectativa or encerramento is null)  and _status <>2";
  $smtSeguro = $coopex->prepare($seguro);
  $smtSeguro->execute();
  $resultSeguro = $smtSeguro->fetchAll(PDO::FETCH_OBJ);
  array_push($totais, $resultSeguro[0]->total);
  array_push($label, 'Prazo Seguro');

  // //totais de vagas
  // $total = "select count(*) as total from historico_vaga";
  // $smtTotal = $coopex->prepare($total);
  // $smtTotal->execute();
  // $smtTotal1 = $smtTotal->fetchAll(PDO::FETCH_OBJ);
  // array_push($totais, $smtTotal1[0]->total);
  // array_push($label, 'Total');



  $retorno = array($label, $totais);
  echo json_encode($retorno);
}
if ($_POST['grafico'] == 'areas') {
  // Listando nomes dos recrutadores do local do usuario
  $usuarios = "select a.area from historico_vaga as a 
inner join vagas as b on a.id_vaga = b.id_vaga
INNER JOIN pessoa_local as c on c.id_local = b.`local` 
where a.recrutador is not null and c.id_pessoa = " . $user . "
group by a.area";
  $stmUsuarios = $coopex->prepare($usuarios);
  $stmUsuarios->execute();
  $resultadoUsuarios = $stmUsuarios->fetchAll(PDO::FETCH_OBJ);
  $totais = array();
  $label = array();

  foreach ($resultadoUsuarios as $users) {
    $cont = "SELECT COUNT(*) AS TOTAL  FROM  historico_vaga where area = '" . $users->area . "' AND _status = 1";
    $stmCont = $coopex->prepare($cont);
    $stmCont->execute();
    $resultadoCont = $stmCont->fetchAll(PDO::FETCH_OBJ);

    $vagasPublicadas = $vagasPublicadas + $resultadoCont[0]->TOTAL;
    array_push($totais, $resultadoCont[0]->TOTAL);
    array_push($label, $users->area);
  }
  array_push($totais, $vagasPublicadas);
  array_push($label, 'total');
  $retorno = array($label, $totais);
  echo json_encode($retorno);
}
if ($_POST['grafico'] == 'perfil') {
  // Listando nomes dos recrutadores do local do usuario
  $usuarios = "select a.perfil from historico_vaga as a 
inner join vagas as b on a.id_vaga = b.id_vaga
INNER JOIN pessoa_local as c on c.id_local = b.`local` 
where a.recrutador is not null and c.id_pessoa = " . $user . "
group by a.perfil";
  $stmUsuarios = $coopex->prepare($usuarios);
  $stmUsuarios->execute();
  $resultadoUsuarios = $stmUsuarios->fetchAll(PDO::FETCH_OBJ);
  $totais = array();
  $label = array();

  foreach ($resultadoUsuarios as $users) {
    $cont = "SELECT COUNT(*) AS TOTAL  FROM  historico_vaga where perfil = '" . $users->perfil . "' AND _status = 1";
    $stmCont = $coopex->prepare($cont);
    $stmCont->execute();
    $resultadoCont = $stmCont->fetchAll(PDO::FETCH_OBJ);

    $vagasPublicadas = $vagasPublicadas + $resultadoCont[0]->TOTAL;
    array_push($totais, $resultadoCont[0]->TOTAL);
    array_push($label, $users->perfil);
  }
  array_push($totais, $vagasPublicadas);
  array_push($label, 'total');
  $retorno = array($label, $totais);
  echo json_encode($retorno);
}

if ($_POST['grafico'] == 'status') {
  $totais = array();
  $label = ['Encerradas','Publicadas','Congeladas'];
  for($i = 0; $i <= 2; $i++) {
    $cont = "SELECT COUNT(*) AS TOTAL  FROM  historico_vaga where _status = " . $i;
    $stmCont = $coopex->prepare($cont);
    $stmCont->execute();
    $resultadoCont = $stmCont->fetchAll(PDO::FETCH_OBJ);

    $vagasPublicadas = $vagasPublicadas + $resultadoCont[0]->TOTAL;
    array_push($totais, $resultadoCont[0]->TOTAL);

  }
  
  array_push($totais, $vagasPublicadas);
  array_push($label, 'Total');
  $retorno = array($label, $totais);
  echo json_encode($retorno);
}
?>