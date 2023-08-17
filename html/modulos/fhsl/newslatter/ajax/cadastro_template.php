
<?
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once('conecta.php');

if (isset($_POST)) {
    extract($_POST);
    if (isset($idTemplate) and $idTemplate > 0) {
        echo $sql = 'update coopex_fhsl.template set nomeTemplate ="' . $nome . '", assunto = "' . $assunto . '", mensagem= :mensagem, ativo = ' . $ativo . ' where idTemplate = ' . $idTemplate;
        $stm = $coopex->prepare($sql);
        $stm->bindValue(':mensagem',$mensagem);
    } else {
        $sql = "insert into coopex_fhsl.template (nomeTemplate, assunto, mensagem, ativo) values('" . $nome . "'    ,'" . $assunto . "' ,:mensagem ," . $ativo . " )";
        $stm = $coopex->prepare($sql);
        $dtm->bindValue(':mensagem',$mensagem);
    }

    $stm->execute();

    header('Location: https://coopex.fag.edu.br/fhsl/newslatter/templates/list');
}
?>