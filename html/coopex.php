<?php 

	require_once("php/config.php");


	if(!isset($_SESSION['coopex'])){
        $_SESSION['destino'] = "https://coopex.fag.edu.br".$_SERVER['REQUEST_URI'];
		header("Location: $_url/");
        exit;
	}
	require_once("php/mysql.php");
	require_once("php/utils.php");
?>
<!DOCTYPE html>
<html lang="pt_br">
    <head>
        <meta charset="utf-8">
        <title>
            Sistema Coopex
        </title>
		<base href="<?php echo $_url?>">
        <meta name="description" content="Sistema Coopex">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
        <!-- Call App Mode on ios devices -->
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <!-- Remove Tap Highlight on Windows Phone IE -->
        <meta name="msapplication-tap-highlight" content="no">
        <!-- base css -->
        <link rel="stylesheet" media="screen, print" href="css/vendors.bundle.css">
        <link rel="stylesheet" media="screen, print" href="css/app.bundle.css">
        <!-- Place favicon.ico in the root directory -->
        <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
        <link rel="mask-icon" href="img/favicon/safari-pinned-tab.svg" color="#5bbad5">
		<script src="js/vendors.bundle.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.compatibility.js"></script>
		<script src="js/jquery.scrollTo.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
		
    </head>
    <body class="mod-bg-1 ">
        <!-- DOC: script to save and load page settings -->
        <script>
            /**
             *	This script should be placed right after the body tag for fast execution 
             *	Note: the script is written in pure javascript and does not depend on thirdparty library
             **/
            'use strict';

            var classHolder = document.getElementsByTagName("BODY")[0],
                /** 
                 * Load from localstorage
                 **/
                themeSettings = (localStorage.getItem('themeSettings')) ? JSON.parse(localStorage.getItem('themeSettings')) :
                {},
                themeURL = themeSettings.themeURL || '',
                themeOptions = themeSettings.themeOptions || '';
            /** 
             * Load theme options
             **/
            if (themeSettings.themeOptions){
                classHolder.className = themeSettings.themeOptions;
                console.log("%c✔ Theme settings loaded", "color: #148f32");
            } else {
                console.log("Heads up! Theme settings is empty or does not exist, loading default settings...");
            }
            if (themeSettings.themeURL && !document.getElementById('mytheme')){
                var cssfile = document.createElement('link');
                cssfile.id = 'mytheme';
                cssfile.rel = 'stylesheet';
                cssfile.href = themeURL;
                document.getElementsByTagName('head')[0].appendChild(cssfile);
            }
            /** 
             * Save to localstorage 
             **/
            var saveSettings = function(){
                themeSettings.themeOptions = String(classHolder.className).split(/[^\w-]+/).filter(function(item){
                    return /^(nav|header|mod|display)-/i.test(item);
                }).join(' ');
                if (document.getElementById('mytheme')){
                    themeSettings.themeURL = document.getElementById('mytheme').getAttribute("href");
                };
                localStorage.setItem('themeSettings', JSON.stringify(themeSettings));
            }
            /** 
             * Reset settings
             **/
            var resetSettings = function(){
                localStorage.setItem("themeSettings", "");
            }

        </script>

		<div class="page-wrapper">
            <div class="page-inner">
                <?php include_once("modulos/_core/estrutura/menu.php")?>
                <div class="page-content-wrapper">
                    <?php include_once("modulos/_core/estrutura/header.php")?>

                    <?php
						if(isset($_GET['diretorio3'])){
                            include_once("modulos/".$_GET['modulo']."/".$_GET['diretorio']."/".$_GET['diretorio2']."/".$_GET['diretorio3']."/".$_GET['arquivo'].".php");
                        } else if(isset($_GET['diretorio2'])){
							
							include_once("modulos/".$_GET['modulo']."/".$_GET['diretorio']."/".$_GET['diretorio2']."/".$_GET['arquivo'].".php");
						} else if(isset($_GET['diretorio'])){
							//echo "modulos/".$_GET['modulo']."/".$_GET['arquivo'].".php";
							include_once("modulos/".$_GET['modulo']."/".$_GET['diretorio']."/".$_GET['arquivo'].".php");
						} else if(isset($_GET['arquivo'])){
							//echo "modulos/".$_GET['modulo']."/".$_GET['arquivo'].".php";
							include_once("modulos/".$_GET['modulo']."/".$_GET['arquivo']."/".$_GET['arquivo'].".php");
						} else {
                            if($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == 6){
                                include_once("modulos/dashboard/academico/academico.php");
                            } else {
                                include_once("modulos/_core/estrutura/main.php");
                            }
						}
					?>
                    <div class="page-content-overlay" data-action="toggle" data-class="mobile-nav-on"></div> <!-- END Page Content -->
                    <?php
						include_once("modulos/_core/estrutura/footer.php");
                    	include_once("modulos/_core/estrutura/atalho.php");
					?>
                </div>
            </div>
        </div>
        <?php
			include_once("modulos/_core/estrutura/menu_rapido.php");
        	include_once("modulos/_core/estrutura/mensageiro.php");
        	include_once("modulos/_core/estrutura/configuracao_layout.php");
		?>
		<script src="js/app.bundle.js"></script>
		<?php
			/*if(isset($_GET['arquivo'])){
				$plugins = "modulos/".$_GET['modulo']."/".$_GET['arquivo']."/plugins/".$_GET['arquivo'].".php";
				include_once($plugins);
			}

			if(isset($_GET['arquivo'])){
				$js = "modulos/".$_GET['modulo']."/".$_GET['arquivo']."/js/".$_GET['arquivo'].".js";
				echo "<script src='$js'></script>";
			}*/
		?>
    </body>
</html>
