
<?
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once('conecta.php');

if (isset($_POST)) {

    if (isset($_POST['idFuncao']) and $_POST['idFuncao'] > 0) {
        echo $sql = "update coopex_fhsl.funcao set nomeFuncao ='" . $_POST['nome'] . "' where idFuncao = " . $_POST['idFuncao'];
        $stm = $coopex->prepare($sql);
    } else {
        echo $sql = "insert into coopex_fhsl.funcao (nomeFuncao) values('" . $_POST['nome'] . "')";
        $stm = $coopex->prepare($sql);
    }

    $stm->execute();
    if ($coopex->lastInsertId() or $stm->rowCount() > 0) {
        header('Location: https://coopex.fag.edu.br/fhsl/newslatter/funcao/list');
    }
}
?>