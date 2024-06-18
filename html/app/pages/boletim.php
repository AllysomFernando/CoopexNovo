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
                        <?= primeiro_nome($_SESSION['app']['aluno'][$_SESSION['app']['pessoa_ativa']]->nome) ?>
                    </h1>
                    <p class="mb-0 pb-1 pe-3">
                        <b><?= utf8_e($_SESSION['app']['aluno'][$_SESSION['app']['pessoa_ativa']]->curso) ?></b><br>
                        <?= utf8_e($_SESSION['app']['aluno'][$_SESSION['app']['pessoa_ativa']]->turma) ?>
                    </p>
                </div>
            </div>
            <div>
                <img src="images/empty.png" data-src="images/pictures/faces/<?= $_SESSION['app']['id_pessoa'] ?>.jpg" width="87" class="rounded-circle shadow-xl preload-img">
            </div>
        </div>
    </div>


    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            text-align: center;
            padding: 8px;
            border: 1px solid #dddddd;
        }

        th:first-child,
        td:first-child {
            text-align: left;
        }
    </style>
    <div class="card card-style">
        <div class="content">
            <h4>Boletim</h4>
            <p>
                Confira as notas e faltas de cada disciplina
            </p>
        </div>
        <div class="content mb-0">
            <table class="table table-striped">
                <tr>
                    <th>Disciplina</th>
                    <th>B1</th>
                    <th>B2</th>
                    <th>B3</th>
                    <th>B4</th>
                    <th>M</th>
                </tr>
                <tr>
                    <td>Matemática</td>
                    <td>6,0<span class="float-end ms-n2 mt-n1 pt-1 badge rounded-pill bg-blue-dark font-9 font-400 scale-switch">5</span></td>
                    <td>7,0<span class="float-end ms-n2 mt-n1 pt-1 badge rounded-pill bg-blue-dark font-9 font-400 scale-switch">2</span></td>
                    <td>-</td>
                    <td>-</td>
                    <td>6,5<span class="float-end ms-n2 mt-n1 pt-1 badge rounded-pill bg-blue-dark font-9 font-400 scale-switch">7</span></td>
                </tr>
                <tr>
                    <td>Portugês</td>
                    <td><strong class="font-600 color-red-dark">5.0</strong><span class="float-end ms-n2 mt-n1 pt-1 badge rounded-pill bg-blue-dark font-9 font-400 scale-switch">2</span></td>
                    <td>8,0</td>
                    <td>-</td>
                    <td>-</td>
                    <td>6,5<span class="float-end ms-n2 mt-n1 pt-1 badge rounded-pill bg-blue-dark font-9 font-400 scale-switch">2</span></td>
                </tr>
                <tr>
                    <td>Ciências</td>
                    <td>8,0</td>
                    <td>10</td>
                    <td>-</td>
                    <td>-</td>
                    <td>9,0</td>
                </tr>
                <tr>
                    <td>Geofrafia</td>
                    <td>7.5</td>
                    <td>8,0</td>
                    <td>-</td>
                    <td>-</td>
                    <td>7,75</td>
                </tr>
                <tr>
                    <td>História</td>
                    <td>6.5</td>
                    <td><strong class="font-600 color-red-dark">5,0<span class="float-end ms-n2 mt-n1 pt-1 badge rounded-pill bg-red-dark font-9 font-400 scale-switch">9</span></strong></td>
                    <td>-</td>
                    <td>-</td>
                    <td><strong class="font-600 color-red-dark">5,75<span class="float-end ms-n2 mt-n1 pt-1 badge rounded-pill bg-red-dark font-9 font-400 scale-switch">9</span></strong></td>
                </tr>
                <tr>
                    <td>Artes</td>
                    <td>8,0</td>
                    <td>10</td>
                    <td>-</td>
                    <td>-</td>
                    <td>9,0</td>
                </tr>
                <tr>
                    <td>Inglês</td>
                    <td>7.5<span class="float-end ms-n2 mt-n1 pt-1 badge rounded-pill bg-blue-dark font-9 font-400 scale-switch">1</span></td>
                    <td>8,0</td>
                    <td>-</td>
                    <td>-</td>
                    <td>7,75<span class="float-end ms-n2 mt-n1 pt-1 badge rounded-pill bg-blue-dark font-9 font-400 scale-switch">1</span></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card card-style mb-0">
        <div class="content mb-0">
            <div class="row mb-2 mt-n2">
                <div class="col-6 text-start">
                    <h4 class="font-700 text-uppercase font-12 opacity-50">Professores</h4>
                </div>
            </div>
            <div class="divider mb-3"></div>


            <a href="page-chat-bubbles-2.html" class="d-flex pb-3">
                <div class="align-self-center">
                    <img src="images/coordenacao/vanessa.jpg" width="50" class="rounded-xl me-3 border border-s" alt="img">
                </div>
                <div class="align-self-center">
                    <h5>Vanessa</h5>
                    <p class="font-500 opacity-70 mt-n2">Matemática</p>
                </div>

            </a>
            <div class="divider mb-3"></div>

            <a href="page-chat-bubbles-2.html" class="d-flex pb-3">
                <div class="align-self-center">
                    <img src="images/coordenacao/kelly.jpg" width="50" class="rounded-xl me-3 border border-s" alt="img">
                </div>
                <div class="align-self-center">
                    <h5>Kelly</h5>
                    <p class="font-500 opacity-70 mt-n2">Portugês</p>
                </div>

            </a>
            <div class="divider mb-3"></div>

            <a href="conversa" class="d-flex pb-3">
                <div class="align-self-center">
                    <img src="images/coordenacao/gil.jpg?a" width="50" class="rounded-xl me-3 border border-s" alt="img">
                </div>
                <div class="align-self-center">
                    <h5>Gil</h5>
                    <p class="font-500 opacity-70 mt-n2">Ciências</p>
                </div>

            </a>
            <div class="divider mb-3"></div>

            <a href="page-chat-bubbles-2.html" class="d-flex pb-3">
                <div class="align-self-center">
                    <img src="images/coordenacao/luiz.jpg" width="50" class="rounded-xl me-3 border border-s" alt="img">
                </div>
                <div class="align-self-center">
                    <h5>Luiz</h5>
                    <p class="font-500 opacity-70 mt-n2">Geofrafia</p>
                </div>

            </a>
            <div class="divider mb-3"></div>


            <a href="page-chat-bubbles-2.html" class="d-flex pb-3">
                <div class="align-self-center">
                    <img src="images/coordenacao/fernanda.jpg" width="50" class="rounded-xl me-3 border border-s" alt="img">
                </div>
                <div class="align-self-center">
                    <h5>Fernanda</h5>
                    <p class="font-500 opacity-70 mt-n2">Artes e Inglês</p>
                </div>

            </a>
            <div class="divider mb-3"></div>

            <a href="page-chat-bubbles-2.html" class="d-flex pb-3">
                <div class="align-self-center">
                    <img src="images/coordenacao/welvis.jpg" width="50" class="rounded-xl me-3 border border-s" alt="img">
                </div>
                <div class="align-self-center">
                    <h5>
                        <p class="font-14 font-600 color-theme mb-0 line-height-s">Welvis</p>
                    </h5>
                    <p class="font-500 opacity-70 mt-n2">História</p>
                </div>
            </a>


        </div>
    </div>



</div>