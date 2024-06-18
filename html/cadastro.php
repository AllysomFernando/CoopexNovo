<?php

require_once("php/config.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//verifica se existe a sessão coopex, caso exista direciona para a página inicial
if (isset($_SESSION['coopex'])) {
	header("Location: " . $_url . "/home");
}

function validarCPF($cpf)
{
	// Remove caracteres não numéricos
	$cpf = preg_replace('/[^0-9]/', '', $cpf);

	// Verifica se o CPF possui 11 dígitos
	if (strlen($cpf) != 11) {
		return false;
	}

	// Verifica se todos os dígitos são iguais, o que não é permitido
	if (preg_match('/(\d)\1{10}/', $cpf)) {
		return false;
	}

	// Calcula o primeiro dígito verificador
	$sum = 0;
	for ($i = 0; $i < 9; $i++) {
		$sum += $cpf[$i] * (10 - $i);
	}
	$remainder = $sum % 11;
	$digit1 = ($remainder < 2) ? 0 : (11 - $remainder);

	// Verifica o primeiro dígito verificador
	if ($cpf[9] != $digit1) {
		return false;
	}

	// Calcula o segundo dígito verificador
	$sum = 0;
	for ($i = 0; $i < 10; $i++) {
		$sum += $cpf[$i] * (11 - $i);
	}
	$remainder = $sum % 11;
	$digit2 = ($remainder < 2) ? 0 : (11 - $remainder);

	// Verifica o segundo dígito verificador
	if ($cpf[10] != $digit2) {
		return false;
	}

	return true;
}




#inicializa as variáveis de validação de preenchimento do usuário e senha
$usuario_valido = true;
$cpf_invalido = false;



#inicializa as variáveis de validação do usuário e senha
$usuario_existe = false;


#verifica se o formulário foi enviado po POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


	#verifica se o captcha foi bem sucedido
	// if($recaptcha->success==true){
	//se o score do captcha foi satisfatório permite o login
	// if ($recaptcha->score >= 0.5) {

	#conecta com o sqlserver
	require_once("php/mysqli.php");

	#verifica o usuário informou usuário e senha<strong></strong>
	$usuario = preg_replace('/[^0-9]/', '', $_POST['usuario']);
	$senha = $_POST['senha'];
	$whatsapp = preg_replace('/[^0-9]/', '', $_POST['whatsapp']);

	#se o usuário informou o usuário e senha consulta o usuário no banco
	if ($usuario && $senha) {
		if (validarCPF($usuario)) {
			$sql = "SELECT
						* 
					FROM
						pessoa
						inner join tipo_usuario USING (id_tipo_usuario) 
					WHERE
						usuario = '$usuario'";
			$res = mysqli_query($link, $sql);

			#se o usuário existir no banco compara se a senha é igual a informada
			if (mysqli_num_rows($res) == 0) {
				$row = mysqli_fetch_assoc($res);

				#valida se a senha é válida ou se é igual a senha mestra
				$usuario_existe = false;

				#se o usuário não existir no Coopex, insere
				include("php/mysql.php");

				$sql = "INSERT INTO `pessoa`(`nome`, `usuario`, `cpf`, `email`, `id_tipo_usuario`, `senha`, `id_campus`, `telefone`)
										VALUES ('" . $_POST['nome'] . "', '$usuario', '$usuario', '" . $_POST['email'] . "', 25, '" . md5($_POST['senha']) . "', '0', '$whatsapp')";
				$coopex->query($sql);

				/*$last_id = $coopex->lastInsertId();
				$sql = "UPDATE pessoa SET id_pessoa = $last_id WHERE id_usuario = $last_id";
				$coopex->query($sql);*/


				#direciona o usuário para página inicial
				header("Location: https://coopex.fag.edu.br/pos");
			} else {
				$usuario_existe = true;
			}
		} else {
			$cpf_invalido = true;
		}
	}
}

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
		Coopex
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
							<img src="img/fag.png" class="img-responsive  " alt="thumbnail">
							</div>
							<div class="form-group">
								<div class="input-group input-group-lg">
									<input name="nome" type="text" class="form-control" placeholder="Nome" autocomplete="off" autofocus required tabindex="1" value="<?= isset($_POST['nome']) ? $_POST['nome'] : "" ?>">
									<div class="input-group-append">
										<button class="btn btn-primary shadow-0" type="button" id="button-addon5" disabled><i class="fal fa-user"></i></button>
									</div>
								</div>
								<div class="input-group input-group-lg mt-3">
									<input data-inputmask="'mask': '999.999.999-99'" name="usuario" type="text" class="form-control" placeholder="CPF" autocomplete="off" autofocus required tabindex="1">
									<div class="input-group-append">
										<button class="btn btn-primary shadow-0" type="button" id="button-addon5" disabled><i class="fal fa-address-card"></i></button>
									</div>
								</div>
								<?php if ($usuario_existe) : ?>
									<div>
										O CPF informado já está cadastrado!
									</div>
								<?php endif ?>
								<?php if ($cpf_invalido) : ?>
									<div>
										O CPF informado é inválido!
									</div>
								<?php endif ?>
								<div class="input-group input-group-lg mt-3">
									<input name="email" type="email" class="form-control" placeholder="E-mail" autocomplete="off" autofocus required tabindex="1" value="<?= isset($_POST['email']) ? $_POST['email'] : "" ?>">
									<div class="input-group-append">
										<button class="btn btn-primary shadow-0" type="button" id="button-addon5" disabled><i class="fal fa-envelope"></i></button>
									</div>
								</div>
								<div class="input-group input-group-lg mt-3">
									<input data-inputmask="'mask': '(99) 99999-9999'" name="whatsapp" type="text" class="form-control" placeholder="Celular/WhatsApp" autocomplete="off" autofocus required tabindex="1" value="<?= isset($_POST['whatsapp']) ? $_POST['whatsapp'] : "" ?>">
									<div class="input-group-append">
										<button class="btn btn-primary shadow-0" type="button" id="button-addon5" disabled><i class="fal fa-phone"></i></button>
									</div>
								</div>
								<?php if (!$usuario_valido) : ?>
									<div>
										Informe seu usuário!
									</div>
								<?php endif ?>

								<div class="input-group input-group-lg mt-3">
									<input name="senha" type="password" class="form-control" placeholder="Senha" value="<?= isset($_POST['senha']) ? $_POST['senha'] : "" ?>" autocomplete="off" required tabindex="2">
									<div class="input-group-append">
										<button class="btn btn-primary shadow-0" type="button" id="button-addon5" disabled><i class="fal fa-key"></i></button>
									</div>
								</div>

								<div class="text-center mt-3">
									<button id="bt_submit" type="submit" class="btn btn-lg btn-default">
										<span class="fal fa-arrow-alt-right mr-1"></span>Cadastrar
									</button>
								</div>
							</div>
						</form>
						<div class="position-absolute pos-bottom pos-left pos-right p-3 text-center text-white">
							2019 © Sistema Coopex by&nbsp;<span class='text-white opacity-40 fw-500'>House FAG</span>
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
		})
	</script>
	

</body>

</html>