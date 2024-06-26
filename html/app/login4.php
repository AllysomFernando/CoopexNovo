<?
require_once("inc/config.php");
require_once("class/login.php");

//print_r($_SESSION['app']);
//print_r($_POST);

//unset($_SESSION['app']);

if (isset($_POST['login'])) {
	$login = login('mreginatto', '161719');
	if($login['erro'] == 1){
		header("Location: /app/home");
	}
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
	<link rel="stylesheet" type="text/css" href="styles/style.css?asdsas">
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900,900i|Source+Sans+Pro:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="fonts/css/fontawesome-all.min.css">
	<link rel="manifest" href="_manifest.json" data-pwa-version="set_in_manifest_and_pwa_js">
	<link rel="apple-touch-icon" sizes="180x180" href="app/icons/icon-192x192.png">
</head>

<body class="theme-light" data-highlight="highlight-red" data-gradient="body-default">

	<div id="preloader">
		<div class="spinner-border color-highlight" role="status"></div>
	</div>

	<!-- <div id="footer-bar" class="footer-bar-1 d-flex justify-content-center">
    </div> -->

	<div id="page">

		<div class="page-content ">
			<img src="images/bg_bilingue2.jpg?dasdff" class="img-fluid mb-n4 shadow-xl">
			<div class="container">
				<div class="avatar text-center mt-n4">
					<img data-menu="menu-story"  src="images/logo_g.jpg" width="150" >
				</div>
			</div>
			<h1 class="text-center font-30 pt-4 mb-0">Login</h1>
			<p class="text-center mb-2">App Colégio FAG</p>

			<!-- <img width="100%" src="images/astro.png?Asdf"> -->


			<div class="mt-4"></div>

			<div class="card card-style">
				<form action="" method="post" name="form_login" class="content mb-0">
					<input type="hidden" name="login" value="1">
					<h2>Acesso</h2>
					<p class="mb-4">
						Entre com Usuário e Senha do Sagres do aluno, ou do responsável para gerenciar várias contas.
					</p>
					<div class="input-style input-style-always-active has-borders has-icon validate-field">
						<i class="fa fa-user font-12"></i>
						<input name="usuario" type="text" class="form-control validate-name" id="f1" placeholder="">
						<label for="f1" class="color-blue-dark font-13">Usuário</label>
						<i class="fa fa-times disabled invalid color-red-dark"></i>
						<i class="fa fa-check disabled valid color-green-dark"></i>
						<em>(required)</em>
					</div>

					<div class="input-style input-style-always-active has-borders has-icon validate-field mt-4">
						<i class="fa fa-key font-12"></i>
						<input name="senha" type="password" class="form-control validate-name" id="f1a">
						<label for="f1a" class="color-blue-dark font-13">Senha</label>
						<i class="fa fa-times disabled invalid color-red-dark"></i>
						<i class="fa fa-check disabled valid color-green-dark"></i>
						<em>(required)</em>
					</div>

					<button href="#" class="btn btn-full bg-blue-dark btn-m text-uppercase rounded-sm shadow-l mb-3 mt-4 w-100 font-900">ENTRAR</button>
				</form>
			</div>

		</div>
	</div>

	<script type="text/javascript" src="scripts/bootstrap.min.js"></script>
	<script type="text/javascript" src="scripts/custom.js"></script>
</body>