<?
require_once 'conecta.php';

extract($_POST);

$sql = "SELECT * FROM certificados WHERE id_certificado = :id_certificado";
$stm = $conexao->prepare($sql);
$stm->bindValue(':id_certificado', $id_certificado);
$stm->execute();

$rs = $stm->fetchAll(PDO::FETCH_OBJ);

$texto = str_replace($nome_old, $nome_new, $rs[0]->texto);

$texto = str_replace($idioma_old, $idioma_new, $texto);

$titulo = str_replace($idioma_old, $idioma_new, $rs[0]->titulo);


$UPDATE = "update certificados set texto = :texto, titulo = :titulo where id_certificado = :id_certificado";
$stm1 = $conexao->prepare($UPDATE);
$stm1->bindValue(':texto', $texto);
$stm1->bindValue(':titulo', $titulo);
$stm1->bindValue(':id_certificado', $id_certificado);
$stm1->execute();

$cont = $stm1->rowCount();

if ($cont > 0) {
    header('Location: /proficiencia/certificados/gerador');
}
