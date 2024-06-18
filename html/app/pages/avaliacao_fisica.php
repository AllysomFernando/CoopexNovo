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

    </style>

    <div class="card card-style">
        <div class="content">
            <h4>Ficha de Avaliação Física</h4>
            <p>
                Confira abaixo a ficha de avaliação física da Felippa
            </p>
        </div>
        <div class="content mb-0">
            <div class="row mb-3">
                <div class="col-6">
                    <p class="font-12 mb-0 font-800 color-theme text-start">Parâmetro</p>
                </div>
                <div class="col-3">
                    <p class="font-12 mb-0 font-800 color-theme text-center">30/08/2023</p>
                </div>
                <div class="col-3">
                    <p class="font-12 mb-0 font-800 color-theme text-end">06/12/2023</p>
                </div>

                <div class="divider w-100 mb-2 mt-2"></div>
                <div class="col-6">
                    <p class="font-13 mb-0 font-500 color-theme text-start">Estimativa de Excesso de Peso</p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"> 0.00<br><span class="color-green-dark font-300">BOM</span></p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"> 0.00<br><span class="color-green-dark font-300">BOM</span></p>
                </div>

                <div class="divider w-100 mb-2 mt-2"></div>
                <div class="col-6">
                    <p class="font-13 mb-0 font-500 color-theme text-start">Estimativa de Excesso de Gordura Visceral</p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"> 0.18<br><span class="color-green-dark font-300">BOM</span></p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-arrow-up color-green-dark pe-1"></i> 0.47<br><span class="color-green-dark font-300">BOM</span></p>
                </div>

                <div class="divider w-100 mb-2 mt-2"></div>
                <div class="col-6">
                    <p class="font-13 mb-0 font-500 color-theme text-start">Aptidão Cardiorrespiratória</p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"> 954<br><span class="color-yellow-dark font-300">REGULAR</span></p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-arrow-down color-red-dark pe-1"></i> 876<br><span class="color-yellow-dark font-300">FRACO</span></p>
                </div>

                <div class="divider w-100 mb-2 mt-2"></div>
                <div class="col-6">
                    <p class="font-13 mb-0 font-500 color-theme text-start">Estimativa de Excesso de Peso</p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"> 0.00<br><span class="color-green-dark font-300">BOM</span></p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"> 0.00<br><span class="color-green-dark font-300">BOM</span></p>
                </div>

                <div class="divider w-100 mb-2 mt-2"></div>
                <div class="col-6">
                    <p class="font-13 mb-0 font-500 color-theme text-start">Flexibilidade</p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"> 0.18<br><span class="color-green-dark font-300">BOM</span></p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-arrow-up color-green-dark pe-1"></i> 0.47<br><span class="color-green-dark font-300">BOM</span></p>
                </div>

                <div class="divider w-100 mb-2 mt-2"></div>
                <div class="col-6">
                    <p class="font-13 mb-0 font-500 color-theme text-start">Resistência Muscular Localizada</p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"> 954<br><span class="color-yellow-dark font-300">REGULAR</span></p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-arrow-down color-red-dark pe-1"></i> 876<br><span class="color-yellow-dark font-300">FRACO</span></p>
                </div>

                <div class="divider w-100 mb-2 mt-2"></div>
                <div class="col-6">
                    <p class="font-13 mb-0 font-500 color-theme text-start">Agilidade</p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"> 954<br><span class="color-yellow-dark font-300">REGULAR</span></p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-arrow-down color-red-dark pe-1"></i> 876<br><span class="color-yellow-dark font-300">FRACO</span></p>
                </div>

                <div class="divider w-100 mb-2 mt-2"></div>
                <div class="col-6">
                    <p class="font-13 mb-0 font-500 color-theme text-start">Velocidade</p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"> 954<br><span class="color-yellow-dark font-300">REGULAR</span></p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-arrow-down color-red-dark pe-1"></i> 876<br><span class="color-yellow-dark font-300">FRACO</span></p>
                </div>

                
            </div>
        </div>
    </div>

    <div class="card card-style">
        <div class="content">
            <h4>Ficha de Avaliação Psicomotora</h4>
            <p>
                Confira abaixo a ficha de avaliação psicomotora da Felippa
            </p>
        </div>
        <div class="content mb-0">
            <div class="row mb-3">
                <div class="col-6">
                    <p class="font-12 mb-0 font-800 color-theme text-start">Parâmetro</p>
                </div>
                <div class="col-3">
                    <p class="font-12 mb-0 font-800 color-theme text-center">30/08/2023</p>
                </div>
                <div class="col-3">
                    <p class="font-12 mb-0 font-800 color-theme text-end">06/12/2023</p>
                </div>

                <div class="divider w-100 mb-2 mt-2"></div>
                <div class="col-6">
                    <p class="font-13 mb-0 font-500 color-theme text-start">Peso (Kg)</p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"> 20
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"> 25</p>
                </div>

                <div class="divider w-100 mb-2 mt-2"></div>
                <div class="col-6">
                    <p class="font-13 mb-0 font-500 color-theme text-start">Altura</p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"> 100</p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center">102<br></p>
                </div>

                <div class="divider w-100 mb-2 mt-2"></div>
                <div class="col-6">
                    <p class="font-13 mb-0 font-500 color-theme text-start">Coordenação Viso Manual</p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-circle color-green-dark pe-1"></i> M<br><span class="color-green-dark font-300">MÉDIA</span></p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-arrow-up color-blue-dark pe-1"></i> F<br><span class="color-blue-dark font-300">FÁCIL</span></p>
                </div>

                <div class="divider w-100 mb-2 mt-2"></div>
                <div class="col-6">
                    <p class="font-13 mb-0 font-500 color-theme text-start">Controle Postural</p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-arrow-up color-blue-dark pe-1"></i> F<br><span class="color-blue-dark font-300">FÁCIL</span></p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-circle color-green-dark pe-1"></i> M<br><span class="color-green-dark font-300">MÉDIA</span></p>
                </div>

                <div class="divider w-100 mb-2 mt-2"></div>
                <div class="col-6">
                    <p class="font-13 mb-0 font-500 color-theme text-start">Organização Perceptiva</p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-arrow-down color-red-dark pe-1"></i> F<br><span class="color-red-dark font-300">DIFÍCIL</span></p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-circle color-green-dark pe-1"></i> M<br><span class="color-green-dark font-300">MÉDIA</span></p>
                </div>

                <div class="divider w-100 mb-2 mt-2"></div>
                <div class="col-6">
                    <p class="font-13 mb-0 font-500 color-theme text-start">Organização Dinâmica</p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-arrow-down color-red-dark pe-1"></i> F<br><span class="color-red-dark font-300">DIFÍCIL</span></p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-circle color-green-dark pe-1"></i> M<br><span class="color-green-dark font-300">MÉDIA</span></p>
                </div>

                <div class="divider w-100 mb-2 mt-2"></div>
                <div class="col-6">
                    <p class="font-13 mb-0 font-500 color-theme text-start">Controle do Próprio Corpo</p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-arrow-up color-blue-dark pe-1"></i> F<br><span class="color-blue-dark font-300">FÁCIL</span></p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-circle color-green-dark pe-1"></i> M<br><span class="color-green-dark font-300">MÉDIA</span></p>
                </div>

                <div class="divider w-100 mb-2 mt-2"></div>
                <div class="col-6">
                    <p class="font-13 mb-0 font-500 color-theme text-start">Controle do Próprio Corpo</p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-circle color-green-dark pe-1"></i> M<br><span class="color-green-dark font-300">MÉDIA</span></p>
                </div>
                <div class="col-3">
                    <p class="font-13 mb-0 font-800 color-theme text-center"><i class="fa fa-circle color-green-dark pe-1"></i> M<br><span class="color-green-dark font-300">MÉDIA</span></p>
                </div>

                

                
            </div>
        </div>
    </div>



</div>