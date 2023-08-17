
<?
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once('conecta.php');

if (isset($_POST)) {

    if (isset($_POST['idSetor']) and $_POST['idSetor'] > 0) {
        echo $sql = "update coopex_fhsl.setor set nomeSetor ='" . $_POST['nome'] . "' where idSetor = " . $_POST['idSetor'];
        $stm = $coopex->prepare($sql);

    } else {
        echo $sql = "insert into coopex_fhsl.setor (nomeSetor) values('" . $_POST['nome'] . "')";
        $stm = $coopex->prepare($sql);
    }

    $stm->execute();
    if ($coopex->lastInsertId() or $stm->rowCount() > 0) {
        header('Location: https://coopex.fag.edu.br/fhsl/newslatter/setor/list');
    }
}
?>