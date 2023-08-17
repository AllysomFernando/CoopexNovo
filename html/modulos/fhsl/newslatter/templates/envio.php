<?php
define('__ROOT__', dirname(dirname(__FILE__)));
$user = $_SESSION['coopex']['usuario']['id_pessoa'];
require(__ROOT__ . '/ajax/conecta.php');


if (isset($_GET['id'])) {
    $sql = "SELECT * FROM coopex_fhsl.template where idTemplate = :id";
    $stm = $coopex->prepare($sql);
    $stm->bindValue(':id', $_GET['id']);
    $stm->execute();
    $dados = $stm->fetchAll(PDO::FETCH_OBJ);


    $sqlSetor = "select * from coopex_fhsl.setor";
    $stmS = $coopex->prepare($sqlSetor);
    $stmS->execute();
    $dadosS = $stmS->fetchAll(PDO::FETCH_OBJ);

    $sqlFuncao = 'select * from coopex_fhsl.funcao';
    $stmF = $coopex->prepare($sqlFuncao);
    $stmF->execute();
    $dadosF = $stmF->fetchAll(PDO::FETCH_OBJ);
}
$status = isset($dados[0]->_status) ? $dados[0]->_status : 0;

?>
<script src="https://cdn.tiny.cloud/1/bcgvi6e011xw466lirf3h7pss4s14dtn6wzwv5a5emywxwrw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<main id="js-page-content" role="main" class="page-content">
    <ol class="breadcrumb page-breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">FHSL</a></li>
        <li class="breadcrumb-item active">Newslatter</li>

        <li class="position-absolute pos-top pos-right d-none d-sm-block"><code>ID: 01</code></li>
    </ol>
    <div class="subheader">
        <h1 class="subheader-title">
            <i class='subheader-icon fal fa-barcode-read'></i> FHSL
            <small>
                Envio em massa
            </small>
        </h1>
        <div class="subheader-title col-6 text-right" id="btn_cadastro">
            <a href="/fhsl/newslatter/templates/list">
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
                        <form class="needs-validation" novalidate="" method="post" action="modulos/fhsl/newslatter/ajax/envio_email.php">
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="validationCustom02">Template: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nome" placeholder="" value="<?php echo isset($_GET['id']) ? $_GET['id'] : "" ?>" disabled>
                                    <input type="text" class="form-control" name="idTemplate" placeholder="" value="<?php echo isset($dados[0]->idTemplate) ? $dados[0]->idTemplate : "" ?>" hidden>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="validationCustom02">Tipo de filtro: <span class="text-danger">*</span></label>
                                    <select class="form-control" name="filtro" id="filtro" onchange="carregaSelect()">
                                        <option value=""></option>
                                        <option value="1">Todos</option>
                                        <option value="2">Função</option>
                                        <option value="3">Setor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row" id="funcao" style="display: none">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="validationCustom02">Selecione a função que receberá os email: <span class="text-danger">*</span></label>
                                    <select class="form-control" name="funcao">
                                        <option value=""></option>
                                        <?
                                        foreach ($dadosF as $row) { ?>
                                            <option value="<?php echo $row->idFuncao ?>"><? echo $row->nomeFuncao ?></option>
                                        <?    }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row" id="setor" style="display: none">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="validationCustom02">Selecione o setor que receberá os email: <span class="text-danger">*</span></label>
                                    <select class="form-control" name="setor">
                                        <option value=""></option>
                                        <?
                                        foreach ($dadosS as $row) { ?>
                                            <option value="<?php echo $row->idSetor ?>"><? echo $row->nomeSetor ?></option>
                                        <?    }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <button class="btn btn-primary ml-auto" type="submit">Enviar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

</main>
<script>
    function carregaSelect() {
        var valor = $('#filtro').val();
        $('#funcao').hide();
        $('#setor').hide();
        console.log(valor)
        if(valor === '2'){
            $('#funcao').show();
        }else if(valor === '3'){
            $('#setor').show();
        }
    }
</script>