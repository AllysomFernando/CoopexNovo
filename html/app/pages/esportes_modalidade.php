<?
require_once("class/esportes.php");
$id = $_GET['id'];
$planejamento = listar_planejamento($id);
?>
<div class="page-content pb-5">

    <div class="card position-fixed w-100 rounded-0" data-card-height="300" style="background: url(https://coopex.fag.edu.br/app/images/esportes/modalidade/<?= $id ?>.jpg); background-size: cover;">
        <div class="card-top notch-clear">
            <a href="#" data-back-button class="icon icon-xl color-white font-15 font-700"><i class="fa fa-arrow-left color-white pt-2 font-14 me-n2"></i> Back</a>
            <a href="#" data-menu="menu-share" class="icon icon-xl float-end"><i class="fa fa-share-alt color-white pt-1"></i></a>
        </div>
        <div class="card-bottom mb-4 pb-2 px-3 mt-2">
            <span class="badge no-click bg-green-dark py-2 px-3 mb-2 text-uppercase rounded-s">MODALIDADE</span>
            <h1 class="font-30 pb-3 color-white">
                Voleibol
            </h1>

        </div>
        <div class="card-overlay bg-gradient opacity-90"></div>
    </div>
    <div class="card card-style bg-transparent shadow-0 rounded-0 no-click" data-card-height="230"></div>

    <div class="card card-style">


        <div class="accordion" id="accordion-1">
            <div class="mb-">
                <button class="btn accordion-btn no-effect collapsed" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false">
                    <i class="fa fa-info-circle color-blue-dark me-2"></i>
                    Resumo
                    <i class="fa fa-chevron-up font-10 accordion-icon fa-rotate-180"></i>
                </button>
                <div id="collapse1" class="collapse" data-bs-parent="#accordion-1" style="">
                    <div class="pt-1 pb-2 ps-3 pe-3">
                        <p><?= nl2br(utf8_e($planejamento->resumo)) ?></p>
                    </div>
                </div>
            </div>
            <div class="mb-0">
                <button class="btn accordion-btn no-effect collapsed" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false">
                    <i class="fa fa-star color-blue-dark me-2"></i>
                    Objetivo
                    <i class="fa fa-chevron-up font-10 accordion-icon fa-rotate-180"></i>
                </button>
                <div id="collapse2" class="collapse" data-bs-parent="#accordion-1" style="">
                    <div class="pt-1 pb-2 ps-3 pe-3">
                        <p><?= nl2br(utf8_e($planejamento->objetivo)) ?></p>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <button class="btn accordion-btn no-effect collapsed" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false">
                    <i class="fa fa-book color-blue-dark me-2"></i>
                    Conteúdo
                    <i class="fa fa-chevron-up font-10 accordion-icon fa-rotate-180"></i>
                </button>
                <div id="collapse3" class="collapse" data-bs-parent="#accordion-1" style="">
                    <div class="pt-1 pb-2 ps-3 pe-3">
                        <p><?= nl2br(utf8_e($planejamento->conteudo)) ?></p>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <button class="btn accordion-btn no-effect collapsed" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false">
                    <i class="fa fa-line-chart color-blue-dark me-2"></i>
                    Avaliação
                    <i class="fa fa-chevron-up font-10 accordion-icon fa-rotate-180"></i>
                </button>
                <div id="collapse4" class="collapse" data-bs-parent="#accordion-1" style="">
                    <div class="pt-1 pb-2 ps-3 pe-3">
                        <p><?= nl2br(utf8_e($planejamento->avaliacao)) ?></p>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <button class="btn accordion-btn no-effect collapsed" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false">
                    <i class="fa fa-calendar color-blue-dark me-2"></i>
                    Projetos/Eventos internos e externos
                    <i class="fa fa-chevron-up font-10 accordion-icon fa-rotate-180"></i>
                </button>
                <div id="collapse5" class="collapse" data-bs-parent="#accordion-1" style="">
                    <div class="pt-1 pb-2 ps-3 pe-3">
                        <p><?= nl2br(utf8_e($planejamento->projeto_evento)) ?></p>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <div class="card card-style">
        <div class="content">
            <div class="d-flex">
                <div class="align-self-center">
                    <h2 class="mb-0">Planejamento</h2>
                </div>
                <!-- <div class="align-self-center ms-auto">
                        <h6 class="mb-0 opacity-30 font-13">2hr, 25min</h6>
                    </div> -->
            </div>

            <div class="divider mt-3 mb-3"></div>

            <?
            for ($i = 2; $i <= 12; $i++) {
                $mes = "mes_" . $i;
                $cor = $i <= date('m') ? "color-green-dark opacity-80" : "opacity-30";
            ?>
                <a href="#chapter-1" class="d-flex mb-3">
                    <div class="align-self-center">
                        <h1 class="pe-3 font-40 <?= $cor ?> font-900 "><?= str_pad($i, 2, "0", STR_PAD_LEFT); ?></h1>
                        <p class="font-12 opacity-50 ms-2">MÊS</p>
                    </div>
                    <div class="align-self-center">
                        <p class="mb-n1 font-12"><?= utf8_e($planejamento->$mes) ?></p>
                    </div>
                </a>
                <hr>
            <?
            }
            ?>
        </div>
    </div>

</div>