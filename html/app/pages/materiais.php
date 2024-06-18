<?
require_once("class/materiais.php");

$material = listar_material();
$mochila = listar_mochila();


?>

<div class="page-content header-clear-large">

    <div class="card card-style">
        <div class="d-flex content">
            <div class="flex-grow-1">
                <div>
                    <h1 class="font-700 mb-1">
                    <?=primeiro_nome($_SESSION['app']['aluno'][$_SESSION['app']['pessoa_ativa']]->nome)?>
                    </h1>
                    <p class="mb-0 pb-1 pe-3">
                        <b><?=utf8_e($_SESSION['app']['aluno'][$_SESSION['app']['pessoa_ativa']]->curso)?></b><br>
                        <?=utf8_e($_SESSION['app']['aluno'][$_SESSION['app']['pessoa_ativa']]->turma)?>
                    </p>
                </div>
            </div>
            <div>
                <img src="images/empty.png" data-src="images/pictures/faces/<?=$_SESSION['app']['id_pessoa']?>.jpg" width="87" class="rounded-circle shadow-xl preload-img">
            </div>
        </div>
    </div>

    <style>

    </style>

    <div class="card card-style">
        <div class="content  mb-0">
            <h2>Lista de materiais</h2>
            <p class="mb-4">
                Utilize esta ferramenta como uma lista de verificação para garantir que você não se perca durante a compra do material.
            </p>
            <div class="row p-2">
                <table class="table">
                    <?
                    foreach ($material as $row) {
                    ?>
                        <tr>
                            <td class="text-center font-16 opacity-60" width="40"><b><?= $row->qtd ?></b></td>
                            <td><?= utf8_e($row->material) ?></td>
                            <td>
                                <div class="ios-switch m-0">
                                    <input type="checkbox" class="ios-input" id="switch-material-<?= $row->id_material ?>">
                                    <label class="custom-control-label" for="switch-material-<?= $row->id_material ?>"></label>
                                </div>
                            </td>
                        </tr>
                    <?
                    }
                    ?>
                </table>
            </div>

        </div>
    </div>

    <div class="card card-style">
        <div class="content mb-0">
            <h2>Manter na mochia </h2>
            <p>
                Não esquecer de enviar diariamente na mochila
            </p>
            <div class="row px-2">
                <table class="table">
                    <?
                    foreach ($mochila as $row) {
                    ?>
                        <tr>
                            <td class="text-center font-16 opacity-60" width="40"><b><?= $row->qtd ?></b></td>
                            <td><?= utf8_e($row->material) ?></td>

                        </tr>
                    <?
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>

    <div class="card card-style">
        <div class="content mb-0">
            <div class="d-flex">
                <div class="align-self-start">
                    <h2 class="mb-3">Observações</h2>
                </div>
                <div class="align-self-start ms-auto ps-3">
                    <span class="icon icon-xxs rounded-xl bg-white color-brown-dark">
                        <i class="fa fa-exclamation-circle color-red-dark font-22"></i>
                    </span>
                </div>
            </div>
            <div class="row px-2">
                <table class="table">
                    <tr>
                        <td class="text-center font-16 opacity-60" width="40"><span class="font-14"><i class="fa fa-tag font-16 pe-1"></i></td>
                        <td>Todo material deverá ser etiquetado com o nome do(a) aluno(a)</td>
                    </tr>
                    <tr>
                        <td class="text-center font-16 opacity-60" width="40"><span class="font-14"><i class="fa fa-tag font-16 pe-1"></i></td>
                        <td>Identificar também o UNIFORME</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>