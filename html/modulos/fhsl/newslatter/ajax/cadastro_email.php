
<?
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once('conecta.php');

if (isset($_POST)) {

    if (isset($_POST['idEmail']) and $_POST['idEmail'] > 0) {
        echo $sql = "update coopex_fhsl.catalogo set nome ='" . $_POST['nome'] . "', email = '" . $_POST['email'] . "', setor= '" . $_POST['setor'] . "', funcao = '" . $_POST['funcao'] . "' where idEmail = " . $_POST['idEmail'];
        $stm = $coopex->prepare($sql);

    } else {
        echo $sql = "insert into coopex_fhsl.catalogo (nome, email, setor, funcao) values('" . $_POST['nome'] . "'    ,'" . $_POST['email'] . "' ,'" . $_POST['setor'] . "' ,'" . $_POST['funcao'] . "' )";
        $stm = $coopex->prepare($sql);
    }

    $stm->execute();
    if ($coopex->lastInsertId() or $stm->rowCount() > 0) {
        header('Location: https://coopex.fag.edu.br/fhsl/newslatter/emails/list');
    }
}
?>