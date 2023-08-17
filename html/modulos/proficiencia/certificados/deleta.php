<?
require_once 'ajax/conecta.php';

$del = "delete from certificados where id_certificado = :id_certificado";
$stm = $conexao->prepare($del);
$stm->bindValue(':id_certificado', $_POST['id_certificado']);
$stm->execute();

$cont = $stm->rowCount();

// echo json_encode(array('status'=>1));
// echo '<p id="teste" style="display:none">excluido</p>';
