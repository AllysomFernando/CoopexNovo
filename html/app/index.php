<?
require_once("inc/config.php");
//require_once("inc/mysql.php");

//require_once("class/index.php");

if (!isset($_SESSION['app']['tipo_login'])) {
    header("Location: /app/login");
}

$debug = 0;

if ($debug) {
    echo "<pre>";
    print_r($_SESSION['app']);
}

?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <base href="<?= $url ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?= $titulo ?></title>
    <link rel="stylesheet" type="text/css" href="styles/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="styles/style.css?<?= rand() ?>">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900,900i|Source+Sans+Pro:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="fonts/css/fontawesome-all.min.css">
    <link rel="manifest" href="_manifest.json?1234" data-pwa-version="set_in_manifest_and_pwa_js">
    <link rel="apple-touch-icon" sizes="180x180" href="app/icons/icon-192x192.png">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="theme-light" data-highlight="highlight-red" data-gradient="body-default">

    <div id="preloader">
        <div class="spinner-border color-highlight" role="status"></div>
    </div>

    <div id="page">

        <div class="header header-fixed header-logo-center mb-4 bg-dark-dark">
            <a href="/app/home" class="header-logo"><img class="logo" src="images/logo_full.svg"></a>
            <button data-menu="menu-sidebar-left-6" class="header-icon header-icon-1 color-white ms-2"><i class="fas fa-bars"></i></button>


            <!-- <a href="#" class="header-icon header-icon-3"><i class="fas fa-heart color-red-light"></i></a> -->
            <button data-menu="menu-team" href="#"><img src="images/pictures/faces/<?= $_SESSION['app']['id_pessoa'] ?>.jpg" width="20" class="header-icon header-icon-4 rounded-circle shadow-xl me-3 shadow-xl" style="margin-top: -.5rem !important; border:solid white 3px"></button>
        </div>

        <div id="footer-bar" class="footer-bar-1 d-flex justify-content-center px-2">
            <a href="/app/agenda" class="w20"><i class="fa fa-book"></i><span class="font-14">Agenda</span><em class="badge bg-orange-dark">1</em></a>
            <a href="/app/chat" class="w20"><i class="fa fa-comment"></i><span class="font-14">Chat</span><em class="badge bg-orange-dark">3</em></a>
            <a href="loja" class="p-0 shadows-lg w20 mt-1">
                <img class="rounded-xl ratio ratio-1x1 mt-0" style="width: 55px;" src="images/logo.jpg">
            </a>
            <a href="boletim" class="w20"><i class="fa fa-table"></i><span class="font-14">Boletim</span></a>
            <a href="carteirinha" class="w20"><i class="fa fa-user-circle"></i><span class="font-14">Carteirinha</span></a>
        </div>

        <style>
            #footer-bar a,
            button {
                /* width: 20% !important; */
            }
        </style>

        <!-- <div id="footer-bar" class="footer-bar-2 justify-content-center">
            <a href="#"><i class="fa fa-list"></i><span>Menu</span></a>
            <a href="#"><i class="fa fa-comment"></i><span>Chat</span><em class="badge bg-orange-dark">3</em></a>
            <a href="#" class="active-nav"><span></span><strong><img style="height: 65%;" class="mt-2" src="images/logo_2.svg"></strong></a>
            <a href="#" data-menu="menu-settings"><i class="fa fa-cog"></i><span>Config</span></a>
            <a href="#"><i class="fa fa-user-circle"></i><span>Alessandro</span></a>
            
        </div> -->

        <? include isset($_GET['p']) ? "pages/" . $_GET['p'] . ".php" : "pages/home.php"; ?>


        <div id="menu-sidebar-left-6" class="bg-white menu menu-box-left" data-menu-width="320" data-menu-effect="menu-parallax">
            <div class="d-flex">
                <a href="https://www.facebook.com/colegiofag/" target="_blank" class="flex-fill icon icon-m text-center color-facebook border-right"><i class="fab font-12 fa-facebook-f"></i></a>

                <a href="https://www.instagram.com/colegiofag" target="_blank" class="flex-fill icon icon-m text-center color-instagram border-right"><i class="fab font-12 fa-instagram"></i></a>

                <a href="tel:+554533213973" class="flex-fill icon icon-m text-center color-twitter border-right"><i class="fa font-12 fa-phone"></i></a>

                <a href="#" class="close-menu flex-fill icon icon-m text-center color-red-dark"><i class="fa font-12 fa-times"></i></a>
            </div>
            <div class="divider mb-3"></div>
            <div class="ps-3 pe-3 pt-2 mb-4">
                <div class="d-flex">
                    <div class="me-2 align-self-center">
                        <img src="images/pictures/faces/<?= $_SESSION['app']['id_pessoa'] ?>.jpg" class="gradient-red rounded-sm" width="43">
                    </div>
                    <div class="flex-grow-1 align-self-center ps-2">
                        <h1 class="font-22 font-700 mb-0">
                            <?= primeiro_nome($_SESSION['app']['aluno'][$_SESSION['app']['pessoa_ativa']]->nome) ?>
                        </h1>
                        <p class="mt-n2 mb-0 font-16 font-400">
                            <?= utf8_e($_SESSION['app']['aluno'][$_SESSION['app']['pessoa_ativa']]->turma) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="divider divider-margins mb-0"></div>

            <div class="me-3 ms-3">
                <div class="list-group list-custom-small">
                    <a href="protocolo">
                        <i class="fa font-14 fa-star rounded-s bg-yellow-dark"></i>
                        <span class="font-16">Protocolo de Atendimento</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <a href="boletim" class="border-0">
                        <i class="fa font-14 fa-table rounded-s bg-instagram"></i>
                        <span class="font-16">Boletim</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <a href="horarios" class="border-0">
                        <i class="fa font-14 fa-clock rounded-s bg-dark-dark"></i>
                        <span class="font-16">Horários de Aula</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <a href="avaliacao">
                        <i class="fa font-14 fa-heartbeat rounded-s bg-blue-dark"></i>
                        <span class="font-16">Avaliação Física</span>

                        <i class="fa fa-angle-right"></i>
                    </a>

                </div>
            </div>

            <div class="me-3 ms-3 mt-4">
                <span class="text-uppercase font-900 font-11 opacity-30">ATIVIDADES</span>
                <div class="list-group list-custom-small">
                    <a href="esportes">
                        <i class="fa font-14 rounded-s bg-brown-dark"></i>
                        <span class="font-16">Escola de Esportes</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <a href="bigjump">
                        <i class="fas fa-socks font-14 rounded-s bg-green-dark"></i>
                        <span class="font-16">Big Jump</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <a href="tarefa" class="border-0">
                        <i class="fa font-14  rounded-s bg-teal-dark"></i>
                        <span class="font-16">Clube da Tarefa</span>
                        <span class="badge bg-red-dark">NOVO</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <a href="acamp">
                        <i class="font-14 fas fa-campground rounded-s bg-twitter"></i>
                        <span class="font-16">Acamp FAG</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <a href="materiais">
                        <i class="fab font-14 fa-fw select-all fas rounded-s bg-facebook"></i>
                        <span class="font-16">Lista de Materiais</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                </div>
            </div>

            <div class="me-3 ms-3 mt-4 pt-2">
                <span class="text-uppercase font-900 font-11 opacity-30">Configurações</span>
                <div class="list-group list-custom-small">
                    <a href="#" data-toggle-theme data-trigger-switch="switch-dark3-mode">
                        <i class="fa font-12 fa-moon bg-gray-dark rounded-s"></i>
                        <span class="font-16">Modo Escuro</span>
                        <div class="custom-control small-switch ios-switch">
                            <input data-toggle-theme type="checkbox" class="ios-input" id="switch-dark3-mode">
                            <label class="custom-control-label" for="switch-dark3-mode"></label>
                        </div>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <!-- <a data-trigger-switch="sidebar-311-switch-2" href="#">
                        <i class="fa font-14 fa-circle rounded-s bg-green-dark"></i>
                        <span>Active Mode</span>
                        <div class="custom-control small-switch ios-switch">
                            <input type="checkbox" class="ios-input" id="sidebar-311-switch-2" checked>
                            <label class="custom-control-label" for="sidebar-311-switch-2"></label>
                        </div>
                        <i class="fa fa-angle-right"></i>
                    </a> -->
                    <!-- <a data-trigger-switch="sidebar-31-switch-3" href="#" class="border-0">
                        <i class="fa font-14 fa-bell rounded-s bg-blue-dark"></i>
                        <span>Notifications</span>
                        <div class="custom-control small-switch ios-switch">
                            <input type="checkbox" class="ios-input" id="sidebar-31-switch-3" checked>
                            <label class="custom-control-label" for="sidebar-31-switch-3"></label>
                        </div>
                        <i class="fa fa-angle-right"></i>
                    </a> -->
                </div>
            </div>
        </div>
        <!-- End of Page Content-->
        <!-- All Menus, Action Sheets, Modals, Notifications, Toasts, Snackbars get Placed outside the <div class="page-content"> -->
        <div id="menu-settings" class="menu menu-box-bottom menu-box-detached">
            <div class="menu-title mt-0 pt-0">
                <h1>Settings</h1>
                <p class="color-highlight">Flexible and Easy to Use</p><a href="#" class="close-menu"><i class="fa fa-times"></i></a>
            </div>
            <div class="divider divider-margins mb-n2"></div>
            <div class="content">
                <div class="list-group list-custom-small">
                    <a href="#" data-toggle-theme data-trigger-switch="switch-dark-mode" class="pb-2 ms-n1">
                        <i class="fa font-12 fa-moon rounded-s bg-highlight color-white me-3"></i>
                        <span>Modo Escuro</span>
                        <div class="custom-control scale-switch ios-switch">
                            <input data-toggle-theme type="checkbox" class="ios-input" id="switch-dark-mode">
                            <label class="custom-control-label" for="switch-dark-mode"></label>
                        </div>
                        <i class="fa fa-angle-right"></i>
                    </a>
                </div>
                <div class="list-group list-custom-large">
                    <a data-menu="menu-highlights" href="#">
                        <i class="fa font-14 fa-tint bg-green-dark rounded-s"></i>
                        <span>Page Highlight</span>
                        <strong>16 Colors Highlights Included</strong>
                        <span class="badge bg-highlight color-white">HOT</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <a data-menu="menu-backgrounds" href="#" class="border-0">
                        <i class="fa font-14 fa-cog bg-blue-dark rounded-s"></i>
                        <span>Background Color</span>
                        <strong>10 Page Gradients Included</strong>
                        <span class="badge bg-highlight color-white">NEW</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- Menu Settings Highlights-->
        <div id="menu-highlights" class="menu menu-box-bottom menu-box-detached">
            <div class="menu-title">
                <h1>Highlights</h1>
                <p class="color-highlight">Any Element can have a Highlight Color</p><a href="#" class="close-menu"><i class="fa fa-times"></i></a>
            </div>
            <div class="divider divider-margins mb-n2"></div>
            <div class="content">
                <div class="highlight-changer">
                    <a href="#" data-change-highlight="blue"><i class="fa fa-circle color-blue-dark"></i><span class="color-blue-light">Default</span></a>
                    <a href="#" data-change-highlight="red"><i class="fa fa-circle color-red-dark"></i><span class="color-red-light">Red</span></a>
                    <a href="#" data-change-highlight="orange"><i class="fa fa-circle color-orange-dark"></i><span class="color-orange-light">Orange</span></a>
                    <a href="#" data-change-highlight="pink2"><i class="fa fa-circle color-pink2-dark"></i><span class="color-pink-dark">Pink</span></a>
                    <a href="#" data-change-highlight="magenta"><i class="fa fa-circle color-magenta-dark"></i><span class="color-magenta-light">Purple</span></a>
                    <a href="#" data-change-highlight="aqua"><i class="fa fa-circle color-aqua-dark"></i><span class="color-aqua-light">Aqua</span></a>
                    <a href="#" data-change-highlight="teal"><i class="fa fa-circle color-teal-dark"></i><span class="color-teal-light">Teal</span></a>
                    <a href="#" data-change-highlight="mint"><i class="fa fa-circle color-mint-dark"></i><span class="color-mint-light">Mint</span></a>
                    <a href="#" data-change-highlight="green"><i class="fa fa-circle color-green-light"></i><span class="color-green-light">Green</span></a>
                    <a href="#" data-change-highlight="grass"><i class="fa fa-circle color-green-dark"></i><span class="color-green-dark">Grass</span></a>
                    <a href="#" data-change-highlight="sunny"><i class="fa fa-circle color-yellow-light"></i><span class="color-yellow-light">Sunny</span></a>
                    <a href="#" data-change-highlight="yellow"><i class="fa fa-circle color-yellow-dark"></i><span class="color-yellow-light">Goldish</span></a>
                    <a href="#" data-change-highlight="brown"><i class="fa fa-circle color-brown-dark"></i><span class="color-brown-light">Wood</span></a>
                    <a href="#" data-change-highlight="night"><i class="fa fa-circle color-dark-dark"></i><span class="color-dark-light">Night</span></a>
                    <a href="#" data-change-highlight="dark"><i class="fa fa-circle color-dark-light"></i><span class="color-dark-light">Dark</span></a>
                    <div class="clearfix"></div>
                </div>
                <a href="#" data-menu="menu-settings" class="mb-3 btn btn-full btn-m rounded-sm bg-highlight shadow-xl text-uppercase font-900 mt-4">Back to Settings</a>
            </div>
        </div>
        <!-- Menu Settings Backgrounds-->
        <div id="menu-backgrounds" class="menu menu-box-bottom menu-box-detached">
            <div class="menu-title">
                <h1>Backgrounds</h1>
                <p class="color-highlight">Change Page Color Behind Content Boxes</p><a href="#" class="close-menu"><i class="fa fa-times"></i></a>
            </div>
            <div class="divider divider-margins mb-n2"></div>
            <div class="content">
                <div class="background-changer">
                    <a href="#" data-change-background="default"><i class="bg-theme"></i><span class="color-dark-dark">Default</span></a>
                    <a href="#" data-change-background="plum"><i class="body-plum"></i><span class="color-plum-dark">Plum</span></a>
                    <a href="#" data-change-background="magenta"><i class="body-magenta"></i><span class="color-dark-dark">Magenta</span></a>
                    <a href="#" data-change-background="dark"><i class="body-dark"></i><span class="color-dark-dark">Dark</span></a>
                    <a href="#" data-change-background="violet"><i class="body-violet"></i><span class="color-violet-dark">Violet</span></a>
                    <a href="#" data-change-background="red"><i class="body-red"></i><span class="color-red-dark">Red</span></a>
                    <a href="#" data-change-background="green"><i class="body-green"></i><span class="color-green-dark">Green</span></a>
                    <a href="#" data-change-background="sky"><i class="body-sky"></i><span class="color-sky-dark">Sky</span></a>
                    <a href="#" data-change-background="orange"><i class="body-orange"></i><span class="color-orange-dark">Orange</span></a>
                    <a href="#" data-change-background="yellow"><i class="body-yellow"></i><span class="color-yellow-dark">Yellow</span></a>
                    <div class="clearfix"></div>
                </div>
                <a href="#" data-menu="menu-settings" class="mb-3 btn btn-full btn-m rounded-sm bg-highlight shadow-xl text-uppercase font-900 mt-4">Back to Settings</a>
            </div>
        </div>
        <!-- Menu Share -->
        <div id="menu-share" class="menu menu-box-bottom menu-box-detached">
            <div class="menu-title mt-n1">
                <h1>Share the Love</h1>
                <p class="color-highlight">Just Tap the Social Icon. We'll add the Link</p><a href="#" class="close-menu"><i class="fa fa-times"></i></a>
            </div>
            <div class="content mb-0">
                <div class="divider mb-0"></div>
                <div class="list-group list-custom-small list-icon-0">
                    <a href="auto_generated" class="shareToFacebook external-link">
                        <i class="font-18 fab fa-facebook-square color-facebook"></i>
                        <span class="font-13">Facebook</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                    <a href="auto_generated" class="shareToTwitter external-link">
                        <i class="font-18 fab fa-twitter-square color-twitter"></i>
                        <span class="font-13">Twitter</span>
                        <i class="fa fa-tel"></i>
                    </a>
                    <a href="auto_generated" class="shareToMail external-link border-0">
                        <i class="font-18 fa fa-envelope-square color-mail"></i>
                        <span class="font-13">Email</span>
                        <i class="fa fa-angle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Be sure this is on your main visiting page, for example, the index.html page-->
        <!-- Install Prompt for Android -->
        <div id="menu-install-pwa-android" class="menu menu-box-bottom menu-box-detached rounded-l">
            <div class="boxed-text-l mt-4 pb-3">
                <img class="rounded-l mb-3" src="images/logo.png" alt="img" width="90">
                <h4 class="mt-3">Adicione</h4>
                <p>
                    Install Sticky on your home screen, and access it just like a regular app. It really is that simple!
                </p>
                <a href="#" class="pwa-install btn btn-s rounded-s shadow-l text-uppercase font-900 bg-highlight mb-2">Adicione o App Colégio FAG na sua tela inicial</a><br>
                <a href="#" class="pwa-dismiss close-menu color-gray-dark text-uppercase font-900 opacity-60 font-10 pt-2">Depois</a>
                <div class="clear"></div>
            </div>
        </div>

        <!-- Install instructions for iOS -->
        <div id="menu-install-pwa-ios" class="menu menu-box-bottom menu-box-detached rounded-l">
            <div class="boxed-text-xl mt-4 pb-3">
                <img class="rounded-l mb-3" src="app/icons/icon-128x128.png" alt="img" width="90">
                <h4 class="mt-3">Adicione o App Colégio FAG na sua tela inicial</h4>
                <p class="mb-0 pb-0">
                    Abra o menu do Safari e toque em "Adicionar à Tela de Início";
                </p>
                <div class="clearfix pt-3"></div>
                <a href="#" class="pwa-dismiss close-menu color-highlight text-uppercase font-700">Depois</a>
            </div>
        </div>

        <div id="menu-team" class="menu menu-box-top menu-box-detached rounded-m pb-4" data-menu-height="auto" data-menu-effect="menu-over" style="display: block; ">
            <div class="menu-title">
                <h1>Usuários </h1>
                <p class="color-theme font-12 opacity-80">Clique para ativar o usuário desejado</p><a href="#" class="close-menu"><i class="fa fa-times"></i></a>
            </div>
            <div class="divider divider-margins mb-n2"></div>

            <div class="content mt-2">
                <div class="list-group list-custom-small">
                    <?
                    //monta o responsável
                    if ($_SESSION['app']['tipo_login'] == 1) {
                    ?>
                        <button class="d-flex py-2">
                            <div class="align-self-center">
                                <img src="images/pictures/faces/<?= $_SESSION['app']['responsavel']->id_pessoa ?>.jpg" width="55" class="rounded-xl me-3 shadow-xl" alt="img">
                            </div>
                            <div class="align-self-center text-start">
                                <p class="font-16 font-600 color-theme mb-0 line-height-s">
                                    <?= $_SESSION['app']['responsavel']->nome ?>
                                </p>
                                <div class="font-14 mb-0 line-height-s">RESPONSÁVEL</div>
                            </div>
                            <div class="position-absolute end-0 pe-3">
                                <i class="fab font-22 fa-fw select-all fas rounded-s opacity-40"></i>
                            </div>
                        </button>

                    <?
                    }
                    ?>

                    <?
                    //monta a lista de usuários
                    $i = 0;
                    foreach ($_SESSION['app']['aluno'] as $aluno) {
                    ?>
                        <button onclick="trocar_usuario(<?= $aluno->id_pessoa ?>, <?= $i ?>)" class="d-flex py-2">
                            <div class="align-self-center">
                                <img src="images/pictures/faces/<?= $aluno->id_pessoa ?>.jpg" width="55" class="rounded-xl me-3 shadow-xl" alt="img">
                            </div>
                            <div class="align-self-center">
                                <p class="font-16 font-600 color-theme mb-0 line-height-s">
                                    <?= primeiro_nome($aluno->nome) ?>
                                </p>
                                <p class="font-14 mb-0 line-height-s"><?= utf8_e($aluno->turma) ?></p>
                            </div>
                            <div class="position-absolute end-0 pe-3">
                                <?
                                if ($aluno->id_pessoa == $_SESSION['app']['id_pessoa']) {
                                ?>
                                    <strong class="badge bg-yellow-dark">ATIVO</strong>
                                <?
                                } else {
                                ?>
                                    <i class="fab color-blue-dark font-22 fa-fw select-all fas rounded-s"></i>
                                <?
                                }
                                ?>

                            </div>
                        </button>

                    <?
                        $i++;
                    }
                    ?>


                </div>
                <button onclick="logout()" class="btn btn-m float-end btn-full mb-0 mt-3 rounded-xl text-uppercase font-900 shadow-xl bg-red-dark btn-icon text-start">
                    <i class="fa fa-power-off font-15 rounded-xl text-center"></i>
                    SAIR
                </button>


            </div>
        </div>

    </div>

    <input type="hidden" id="ra" value="<?= $_SESSION['app']['aluno'][$_SESSION['app']['pessoa_ativa']]->ra ?>">


    <script type="text/javascript" src="scripts/bootstrap.min.js"></script>
    <script type="text/javascript" src="scripts/custom.js?<?= rand() ?>"></script>
    <script type="text/javascript" src="scripts/index.js?<?= rand() ?>"></script>
</body>