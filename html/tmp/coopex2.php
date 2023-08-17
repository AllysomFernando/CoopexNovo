<pre>

<?php
	require_once("php/config.php");
	if(!isset($_SESSION['coopex'])){
		header("Location: $_base");
	}
	require_once("php/mysql.php");
	require_once("php/utils.php");

	unset($_SESSION['coopex']['menu']);

	$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];
	
	$sql = "SELECT
				id_menu_grupo,
				menu_grupo 
			FROM
				coopex.menu_grupo
				INNER JOIN coopex.menu USING ( id_menu_grupo )
				INNER JOIN coopex.menu_permissao USING ( id_menu )
				INNER JOIN coopex.menu_permissao_tipo_usuario USING ( id_menu_permissao )
				INNER JOIN coopex.usuario USING ( id_tipo_usuario ) 
			WHERE
				id_pessoa = $id_pessoa 
			GROUP BY
				id_menu_grupo UNION
			SELECT
				id_menu_grupo,
				menu_grupo 
			FROM
				coopex.menu_grupo
				INNER JOIN coopex.menu USING ( id_menu_grupo )
				INNER JOIN coopex.menu_permissao USING ( id_menu )
				INNER JOIN coopex.menu_permissao_usuario USING ( id_menu_permissao )
				INNER JOIN coopex.usuario USING ( id_usuario ) 
			WHERE
				id_pessoa = $id_pessoa 
				AND id_menu_grupo NOT IN (
				SELECT
					id_menu_grupo 
				FROM
					coopex.menu_grupo
					INNER JOIN coopex.menu USING ( id_menu_grupo )
					INNER JOIN coopex.menu_permissao USING ( id_menu )
					INNER JOIN coopex.menu_permissao_tipo_usuario USING ( id_menu_permissao )
					INNER JOIN coopex.usuario USING ( id_tipo_usuario ) 
				WHERE
					id_pessoa = $id_pessoa
				) 
			GROUP BY
				id_menu_grupo";
	$menu_grupo = $coopex->prepare($sql);
	$menu_grupo->execute();


	while($row = $menu_grupo->fetch(PDO::FETCH_OBJ)){
		
		$id_menu_grupo = $row->id_menu_grupo;
		
		$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['id'] = $id_menu_grupo;
		$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['nome'] = $row->menu_grupo;
		
		echo $row->menu_grupo.PHP_EOL;
		
		echo $sql = "SELECT
					* 
				FROM
					coopex.menu 
				WHERE
					id_menu_grupo = $id_menu_grupo
					AND url IS NOT NULL 
					OR id_menu IN (
					SELECT
						pai 
					FROM
						coopex.menu
						INNER JOIN coopex.menu_permissao USING ( id_menu )
						INNER JOIN coopex.menu_permissao_tipo_usuario USING ( id_menu_permissao )
						INNER JOIN coopex.usuario USING ( id_tipo_usuario ) 
					WHERE
						id_menu_grupo = $id_menu_grupo 
						AND id_pessoa = $id_pessoa 
						AND nivel = 2 
					GROUP BY
					pai 
					)";
		$menu_nivel1 = $coopex->prepare($sql);
		$menu_nivel1->execute();
		
		while($row_nivel1 = $menu_nivel1->fetch(PDO::FETCH_OBJ)){
			$id_menu_nivel1 = $row_nivel1->id_menu.PHP_EOL;
			echo "    1 - ".utf8_encode($row_nivel1->menu).PHP_EOL;
			
			$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['nivel1']['id'] = $id_menu_nivel1;
			$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['nivel1']['nome'] = $row_nivel1->menu;
			$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['nivel1']['icone'] = $row_nivel1->icone;
			
			$sql = "SELECT
						id_menu,
						menu 
					FROM
						coopex.menu
						INNER JOIN coopex.menu_permissao USING ( id_menu )
						INNER JOIN coopex.menu_permissao_tipo_usuario USING ( id_menu_permissao )
						INNER JOIN coopex.usuario USING ( id_tipo_usuario ) 
					WHERE
						pai = $id_menu_nivel1 
						AND id_pessoa = $id_pessoa
						AND nivel = 2
					GROUP BY
						id_menu UNION
					SELECT
						id_menu,
						menu 
					FROM
						coopex.menu
						INNER JOIN coopex.menu_permissao USING ( id_menu )
						INNER JOIN coopex.menu_permissao_usuario USING ( id_menu_permissao )
						INNER JOIN coopex.usuario USING ( id_usuario ) 
					WHERE
						pai = $id_menu_nivel1 
						AND id_pessoa = $id_pessoa 
						AND nivel = 2
					GROUP BY
						id_menu";
			$menu_nivel2 = $coopex->prepare($sql);
			$menu_nivel2->execute();

			while($row_nivel2 = $menu_nivel2->fetch(PDO::FETCH_OBJ)){
				$id_menu_nivel2 = $row_nivel2->id_menu.PHP_EOL;
				echo "        2 - ".utf8_encode($row_nivel2->menu).PHP_EOL;

				$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['nivel1']['nivel2']['id'] = $id_menu_nivel2;
				$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['nivel1']['nivel2']['nome'] = $row_nivel2->menu;



			}
			
			
		}
		
		
		//$_SESSION['coopex']['menu'][] = $row;
	}

	print_r($_SESSION['coopex']['menu']);

	foreach($_SESSION['coopex']['menu']['grupo'] as $grupo){
		print_r($grupo['nome']);
	}

	exit;
?>


<?php
	/*
$_SESSION['dados']['id_tipo_usuario'] = 1;	
	
mysql_connect("10.0.0.33", "fernando", "jklp13SA");
mysql_select_db("jurisis");
	
$sql1 = "select * from acesso_tela where nivel = 1 order by ordem";
$res1 = mysql_query($sql1);
while($row1 = mysql_fetch_assoc($res1)){
	$permissao = 0;
	$submenu = 0;
	if($row1['url']){
		echo $sql_aux = "select permissao from acesso_tela_permissao
					left join acesso_tela_permissao_usuario using(id_acesso_tela_permissao)
					where id_acesso_tela = ".$row1['id_acesso_tela']."
					and id_acesso_tela_permissao_tipo = 1
					and id_tipo_usuario = ".$_SESSION['dados']['id_tipo_usuario'].PHP_EOL;
					
		$res_aux = mysql_query($sql_aux);
		$row_aux = mysql_fetch_assoc($res_aux);
		$permissao = $row_aux['permissao'];
		
	} else {
		$sql_aux = "select * from acesso_tela 
					left join acesso_tela_permissao using(id_acesso_tela)
					left join acesso_tela_permissao_usuario using(id_acesso_tela_permissao)
					where nivel = 2
					and pai = ".$row1['id_acesso_tela']."
					and id_acesso_tela_permissao_tipo = 1
					and permissao = 1
					and id_tipo_usuario = ".$_SESSION['dados']['id_tipo_usuario'];
		
		$res_aux = mysql_query($sql_aux);
		$submenu = mysql_num_rows($res_aux);
		
	}
	
	if($permissao || $submenu){	

		$page_nav[$row1['menu']] = array(
			"title" => $row1['tela'],
			"url" => $row1['url'],
			"icon" => $row1['icone']
		);
		
		$sql2 = "select * from acesso_tela where nivel = 2 and pai = ".$row1['id_acesso_tela'];
		$res2 = mysql_query($sql2);
		$i=0;
		while($row2 = mysql_fetch_assoc($res2)){
			$permissao = 0;
			$submenu = 0;
			if($row2['url']){
				$sql_aux = "select permissao from acesso_tela_permissao
							left join acesso_tela_permissao_usuario using(id_acesso_tela_permissao)
							where id_acesso_tela = ".$row2['id_acesso_tela']."
							and id_acesso_tela_permissao_tipo = 1
							and id_tipo_usuario = ".$_SESSION['dados']['id_tipo_usuario'];
				$res_aux = mysql_query($sql_aux);
				$row_aux = mysql_fetch_assoc($res_aux);
				$permissao = $row_aux['permissao'];
			} else {
				$sql_aux = "select permissao from acesso_tela 
							left join acesso_tela_permissao using(id_acesso_tela)
							left join acesso_tela_permissao_usuario using(id_acesso_tela_permissao)
							where nivel = 3
							and pai = ".$row2['id_acesso_tela']."
							and id_acesso_tela_permissao_tipo = 1
							and permissao = 1
							and id_tipo_usuario = ".$_SESSION['dados']['id_tipo_usuario'];
				
				$res_aux = mysql_query($sql_aux);
				$submenu = mysql_num_rows($res_aux);
			}
			if($permissao || $submenu){
				$page_nav[$row1['menu']]['sub'][$i] = array(
					"title" => $row2['tela'],
					"url" => $row2['url'],
					"icon" => $row2['icone']
				);
				
				
				
				$sql3 = "select * from acesso_tela where nivel = 3 and pai = ".$row2['id_acesso_tela'];
				$res3 = mysql_query($sql3);
				while($row3 = mysql_fetch_assoc($res3)){
					$permissao = 0;
					
					$sql_aux = "select permissao from acesso_tela_permissao
							left join acesso_tela_permissao_usuario using(id_acesso_tela_permissao)
							where id_acesso_tela = ".$row3['id_acesso_tela']."
							and id_acesso_tela_permissao_tipo = 1
							and id_tipo_usuario = ".$_SESSION['dados']['id_tipo_usuario'];
					$res_aux = mysql_query($sql_aux);
					$row_aux = mysql_fetch_assoc($res_aux);
					$permissao = $row_aux['permissao'];
					
					if($permissao){
						$page_nav[$row1['menu']]['sub'][$i]['sub'][] = array(
							"title" => $row3['tela'],
							"url" => $row3['url'],
							"icon" => $row3['icone']
						);
					}
				}
			}
	
			$i++;
		}
	}
	
}
			  
print_r($page_nav);

exit;
*/
?>	

<!DOCTYPE html>
<html lang="pt_br">
    <head>
        <meta charset="utf-8">
        <title>
            Sistema Coopex
        </title>
		<base href="<?php echo $_base?>">
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
<pre>
	
		

		<div class="page-wrapper">
            <div class="page-inner">
                <?php include_once("modulos/_core/estrutura/menu.php")?>
                <div class="page-content-wrapper">
                    <?php include_once("modulos/_core/estrutura/header.php")?>

                    <?php
						if(isset($_GET['diretorio2'])){
							
							include_once("modulos/".$_GET['modulo']."/".$_GET['diretorio']."/".$_GET['diretorio2']."/".$_GET['arquivo'].".php");
						} else if(isset($_GET['diretorio'])){
							//echo "modulos/".$_GET['modulo']."/".$_GET['arquivo'].".php";
							include_once("modulos/".$_GET['modulo']."/".$_GET['diretorio']."/".$_GET['arquivo'].".php");
						} else if(isset($_GET['arquivo'])){
							//echo "modulos/".$_GET['modulo']."/".$_GET['arquivo'].".php";
							include_once("modulos/".$_GET['modulo']."/".$_GET['arquivo']."/".$_GET['arquivo'].".php");
						} else {
							include_once("modulos/_core/estrutura/main.php");
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
