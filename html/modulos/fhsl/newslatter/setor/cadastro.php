<?php
define('__ROOT__', dirname(dirname(__FILE__)));
$user = $_SESSION['coopex']['usuario']['id_pessoa'];
require(__ROOT__ . '/ajax/conecta.php');


if (isset($_GET['id'])) {
    $sql = "SELECT * FROM coopex_fhsl.setor where idSetor = :id";
    $stm = $coopex->prepare($sql);
    $stm->bindValue(':id', $_GET['id']);
    $stm->execute();
    $dados = $stm->fetchAll(PDO::FETCH_OBJ);
}
$status = isset($dados[0]->_status) ? $dados[0]->_status : 0;

?>
<script src="https://cdn.tiny.cloud/1/bcgvi6e011xw466lirf3h7pss4s14dtn6wzwv5a5emywxwrw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">FHSL</a></li>
        <li class="breadcrumb-item active">Newslatter</li>

        <li class="position-absolute pos-top pos-right d-none d-sm-block"><code>ID: 03</code></li>
    </ol>
    <div class="subheader">
        <h1 class="subheader-title">
            <i class='subheader-icon fal fa-barcode-read'></i> FHSL
            <small>
                Cadastro Setor
            </small>
        </h1>
        <div class="subheader-title col-6 text-right" id="btn_cadastro">
            <a href="/fhsl/newslatter/setor/list">
                <button type="button" class=" btn btn-lg btn-primary waves-effect waves-themed">

                    Voltar
                </button>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div id="panel-1" class="panel">
                <div class="panel-hdr">
                    <h2>
                        Cadastro
                    </h2>
                    <div class="panel-toolbar">
                        <button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                        <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                        <button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                    </div>
                </div>
                <div class="panel-container show">
                    <div class="panel-content">
                        <form class="needs-validation" novalidate="" method="post" action="modulos/fhsl/newslatter/ajax/cadastro_setor.php">
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="validationCustom02">Nome: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nome" placeholder="" value="<?php echo isset($dados[0]->nomeSetor) ? $dados[0]->nomeSetor : "" ?>" required>
                                    <input type="text" class="form-control" name="idSetor" placeholder="" value="<?php echo isset($dados[0]->idSetor) ? $dados[0]->idSetor : "" ?>" hidden>
                                </div>
                            </div>
                            


                            <button class="btn btn-primary ml-auto" type="submit"><?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar" ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

</main>
<script>
    tinymce.init({
        selector: 'textarea',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    });
</script>