<?
require_once("inc/config.php");
require_once("class/login.php");

//print_r($_SESSION['app']);
//print_r($_POST);

if (isset($_POST['login'])) {
	$login = login('mreginatto', '161719');
	if ($login['erro'] == 1) {
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
	<link rel="stylesheet" type="text/css" href="styles/style.css?<?= rand() ?>">
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900,900i|Source+Sans+Pro:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="fonts/css/fontawesome-all.min.css">
	<link rel="manifest" href="_manifest.json" data-pwa-version="set_in_manifest_and_pwa_js">
	<link rel="apple-touch-icon" sizes="180x180" href="app/icons/icon-192x192.png">
</head>

<body class="theme-light card-overlay-infinite" data-highlight="highlight-red" style="background-image: url(images/ceu4.jpg?dasdff); background-size: cover">
	<canvas id="canvas" style="opacity: .9; z-index: -9; position:absolute; width: 100%; height:100%"></canvas>

	<script>
		"use strict";

var canvas = document.getElementById('canvas'),
  ctx = canvas.getContext('2d'),
  w = canvas.width = window.innerWidth,
  h = canvas.height = window.innerHeight,
    
  hue = 217,
  stars = [],
  count = 0,
  maxStars = 1400;

var canvas2 = document.createElement('canvas'),
    ctx2 = canvas2.getContext('2d');
    canvas2.width = 100;
    canvas2.height = 100;
var half = canvas2.width/2,
    gradient2 = ctx2.createRadialGradient(half, half, 0, half, half, half);
    gradient2.addColorStop(0.025, '#fff');
    gradient2.addColorStop(0.1, 'hsl(' + hue + ', 61%, 33%)');
    gradient2.addColorStop(0.25, 'hsl(' + hue + ', 64%, 6%)');
    gradient2.addColorStop(1, 'transparent');

    ctx2.fillStyle = gradient2;
    ctx2.beginPath();
    ctx2.arc(half, half, half, 0, Math.PI * 2);
    ctx2.fill();

// End cache

function random(min, max) {
  if (arguments.length < 2) {
    max = min;
    min = 0;
  }
  
  if (min > max) {
    var hold = max;
    max = min;
    min = hold;
  }

  return Math.floor(Math.random() * (max - min + 1)) + min;
}

function maxOrbit(x,y) {
  var max = Math.max(x,y),
      diameter = Math.round(Math.sqrt(max*max + max*max));
  return diameter/2;
}

var Star = function() {

  this.orbitRadius = random(maxOrbit(w,h));
  this.radius = random(60, this.orbitRadius) / 12;
  this.orbitX = w / 2;
  this.orbitY = h / 2;
  this.timePassed = random(0, maxStars);
  this.speed = random(this.orbitRadius) / 500000;
  this.alpha = random(2, 10) / 10;

  count++;
  stars[count] = this;
}

Star.prototype.draw = function() {
  var x = Math.sin(this.timePassed) * this.orbitRadius + this.orbitX,
      y = Math.cos(this.timePassed) * this.orbitRadius + this.orbitY,
      twinkle = random(10);

  if (twinkle === 1 && this.alpha > 0) {
    this.alpha -= 0.05;
  } else if (twinkle === 2 && this.alpha < 1) {
    this.alpha += 0.05;
  }

  ctx.globalAlpha = this.alpha;
    ctx.drawImage(canvas2, x - this.radius / 2, y - this.radius / 2, this.radius, this.radius);
  this.timePassed += this.speed;
}

for (var i = 0; i < maxStars; i++) {
  new Star();
}

function animation() {
    ctx.globalCompositeOperation = 'source-over';
    ctx.globalAlpha = 0.8;
    ctx.fillStyle = 'hsla(' + hue + ', 64%, 6%, 1)';
    ctx.fillRect(0, 0, w, h)
  
  ctx.globalCompositeOperation = 'lighter';
  for (var i = 1, l = stars.length; i < l; i++) {
    stars[i].draw();
  };  
  
  window.requestAnimationFrame(animation);
}

animation();
	</script>

	<style>
		
	</style>

	<img class="bandeira1" src="images/bandeiras.png">
	<img class="bandeira2" src="images/bandeiras.png">

	<style>
		.bandeira1 {
			position: absolute;
			left: -45px;
			top: 40px;
			width: 150px;
			z-index: 9999;
		}

		.bandeira2 {
			position: absolute;
			right: -75px;
			top: -20px;
			width: 200px;
			z-index: 9999;
		}
	</style>

	<!-- <div class="card-overlay-infinite" style="background-image:url(images/ceu_vertical.jpg)"></div> -->
	
	<div id="preloader">
		<div class="spinner-border color-highlight" role="status"></div>
	</div>

	<div id="page">
		<div class="page-content ">
			<div class="text-center mt-5">
				<img width="180" src="images/logo_estrelas.png?dasdff" class="img-fluid mb-4">
			</div>
			<div class="container">
				<div class="avatar2 text-center mt-n4">
					<img data-menu="menu-story" src="images/astro_<?=rand(1,2)?>.png" width="350">
				</div>
			</div>
			<!-- <img width="100%" src="images/astro.png?Asdf"> -->

			<div class="mt-0"></div>

			<div class="card card-style">
				<form action="" method="post" name="form_login" class="content mb-0">
					<input type="hidden" name="login" value="1">
					<h2>Acesso</h2>
					<p class="mb-4">
						Entre com Usuário e Senha do Sagres do aluno, ou do responsável para gerenciar várias contas.
					</p>
					<div class="input-style input-style-always-active has-borders has-icon validate-field">
						<i class="fa fa-user font-12"></i>
						<input required name="usuario" type="text" class="form-control validate-name" id="f1" placeholder="">
						<label for="f1" class="color-blue-dark font-13">Usuário</label>
						<i class="fa fa-times disabled invalid color-red-dark"></i>
						<i class="fa fa-check disabled valid color-green-dark"></i>
						<em>(required)</em>
					</div>

					<div class="input-style input-style-always-active has-borders has-icon validate-field mt-4">
						<i class="fa fa-key font-12"></i>
						<input required name="senha" type="password" class="form-control validate-name" id="f1a">
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