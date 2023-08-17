<?php
define('__ROOT__', dirname(dirname(__FILE__)));
$user = $_SESSION['coopex']['usuario']['id_pessoa'];
require(__ROOT__ . '/ajax/conecta.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);


if (isset($_GET['id'])) {
    $sql = "SELECT * FROM coopex_fhsl.catalogo where idEmail = :id";
    $stm = $coopex->prepare($sql);
    $stm->bindValue(':id', $_GET['id']);
    $stm->execute();
    $dados = $stm->fetchAll(PDO::FETCH_OBJ);

    $sqlFuncao = "SELECT * FROM coopex_fhsl.funcao";
    $stmFuncao = $coopex->prepare($sqlFuncao);
    $stmFuncao->execute();
    $dadosF = $stmFuncao->fetchAll(PDO::FETCH_OBJ);

    $sqlSetor = "SELECT * FROM coopex_fhsl.setor";
    $stmSetor = $coopex->prepare($sqlSetor);
    $stmSetor->execute();
    $dadosS = $stmSetor->fetchAll(PDO::FETCH_OBJ);
} else {
    // $sql = "SELECT * FROM coopex_fhsl.catalogo";
    // $stm = $coopex->prepare($sql);
    // $stm->execute();
    // $dados = $stm->fetchAll(PDO::FETCH_OBJ);

    $sqlFuncao = "SELECT * FROM coopex_fhsl.funcao";
    $stmFuncao = $coopex->prepare($sqlFuncao);
    $stmFuncao->execute();
    $dadosF = $stmFuncao->fetchAll(PDO::FETCH_OBJ);

    $sqlSetor = "SELECT * FROM coopex_fhsl.setor";
    $stmSetor = $coopex->prepare($sqlSetor);
    $stmSetor->execute();
    $dadosS = $stmSetor->fetchAll(PDO::FETCH_OBJ);
}


?>
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
                Cadastro E-mails
            </small>
        </h1>
        <div class="subheader-title col-6 text-right" id="btn_cadastro">
            <a href="/fhsl/newslatter/emails/list">
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
                        <form class="needs-validation" novalidate="" method="post" action="modulos/fhsl/newslatter/ajax/cadastro_email.php">
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="validationCustom02">Nome: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nome" placeholder="" value="<?php echo isset($dados[0]->nome) ? $dados[0]->nome : "" ?>" required>
                                    <input type="text" class="form-control" name="idEmail" placeholder="" value="<?php echo isset($dados[0]->idEmail) ? $dados[0]->idEmail : "" ?>" hidden>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="validationCustom02">E-mail: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="email" placeholder="" value="<?php echo isset($dados[0]->email) ? $dados[0]->email : "" ?>" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="validationCustom02">Setor: <span class="text-danger">*</span></label>
                                    <select class="form-control" name="setor">
                                    <option value="" ></option>
                                        <?php
                                        foreach ($dadosS as $row) { ?>
                                            <option value="<? echo $row->idSetor ?>" <? echo isset($dados[0]->setor) == $row->idSetor ? 'selected' : '' ?>><? echo $row->nomeSetor ?></option>

                                        <?php   }

                                        ?>
                                    </select>

                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="validationCustom02">Função: <span class="text-danger">*</span></label>
                                    <select class="form-control" name="funcao">
                                    <option value="" ></option>
                                        <?php
                                        foreach ($dadosF as $row) { ?>
                                            <option value="<? echo $row->idFuncao ?>" <? echo isset($dados[0]->funcao) == $row->idFuncao ? 'selected' : '' ?>><? echo $row->nomeFuncao ?></option>

                                        <?php   }

                                        ?>
                                    </select>

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