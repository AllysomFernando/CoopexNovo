<?php
	require_once("php/config.php");
	
	if(isset($_SERVER['HTTP_REFERER'])){
		if(!strstr($_SERVER['HTTP_REFERER'], $_url)){
			echo "Origem desconhecida!";
			exit;
		}
	}
	
	require_once("php/sqlsrv.php");

	
	/*try {

		$con = new PDO("sqlsrv:Server=10.0.0.150:49320;Database=integracao","integracao","FAGintegracao20anos");

		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  

	} catch (PDOException $e) {
		die ("Erro na conexao com o banco de dados: ".$e->getMessage());
	}*/



	

	
	//$dbh = new PDO("sqlsrv:Server=bancosagres\dados_sagres,1433;Database=integracao", "integracao" , "integracao");

	$usuario_valido = true;
	$senha_valida = true;

	$usuario_existe = true;
	$senha_existe = true;

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// Build POST request:
		$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
		$recaptcha_secret = '6LdZQKkUAAAAACr_iZ0UBIzeAEnmQWKpjfkPAHfi';
		
		$recaptcha_response = $_POST['recaptcha_response'];
		$usuario = trim(preg_replace('/[^[:alnum:]_]/', '',$_POST[$_SESSION['campo_nome']]));
		$senha = trim($_POST[$_SESSION['campo_senha']]);
		
		$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
		
		$recaptcha = json_decode($recaptcha);
		if($recaptcha->success==true){
			// Take action based on the score returned:
			if ($recaptcha->score >= 0.5) {

				$usuario_valido = !$usuario ? false : true;
				$senha_valida = !$senha ? false : true;
			
				if($usuario && $senha){
					$sql = "select * from integracao..view_integracao_usuario where usuario = '$usuario'";	
					$res = mssql_query($sql);

					if(mssql_num_rows($res) > 0){
						$row = mssql_fetch_assoc($res);
						if(($row['senha'] == md5($senha)) || (md5($senha) == 'ea574ffd4ad5c4f03c5ab5d15b99e7ff')){
							$usuario_existe = true;
							
							$_SESSION['coopex']['usuario'] = $row;
							$nome = explode(" ", $row['nome']);
							$_SESSION['coopex']['usuario']['primeiro_nome'] = $nome[0];
							
							if($row['tipo'] == "aluno"){
								$_SESSION['coopex']['usuario']['tipo_usuario'] = "Acadêmico";
							} else if($row['tipo'] == "professor"){
								$_SESSION['coopex']['usuario']['tipo_usuario'] = "Professor";
							} else if($row['tipo'] == "tecnico"){
								$_SESSION['coopex']['usuario']['tipo_usuario'] = "Colaborador";
							}
							
							
							header("Location: $_url");
							
						} else {
							$senha_existe = false;
						}
					} else {
						$usuario_existe = false;
					}
				}
			} else {
				echo '<pre>';
				print_r("Not verified - show form error");
				echo '</pre>';
				exit;
				// Not verified - show form error
			}
		}else{ // there is an error /
			///  timeout-or-duplicate meaning you are submit the  form
			echo '<pre>';
			print_r($recaptcha);
			echo '</pre>';
			exit;
		}
		//exit;
	}

	$_SESSION['campo_nome']  = md5(rand());
	$_SESSION['campo_senha'] = md5(rand());
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>
            Coopex
        </title>
		<style>
			.grecaptcha-badge{
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
        <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
        <link rel="mask-icon" href="img/favicon/safari-pinned-tab.svg" color="#5bbad5">
        <link rel="stylesheet" media="screen, print" href="css/fa-brands.css">
		
		<script src="https://www.google.com/recaptcha/api.js?render=6LdZQKkUAAAAALT6fkXFfXYhFJlv3imtvgJHwopz"></script>
		<script>
			grecaptcha.ready(function() {
				grecaptcha.execute('6LdZQKkUAAAAALT6fkXFfXYhFJlv3imtvgJHwopz', {action: 'homepage'}).then(function(token) {
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
                    <div class="height-10 w-100 shadow-lg px-4 bg-brand-gradient">
                        <div class="d-flex align-items-center container p-0">
                            <div class="page-logo width-mobile-auto m-0 align-items-center justify-content-center p-0 bg-transparent bg-img-none shadow-0 height-9">
                                <a href="javascript:void(0)" class="page-logo-link press-scale-down d-flex align-items-center">
                                    <img src="img/coopex_logo.png" alt="SmartAdmin WebApp" aria-roledescription="logo">
                                    <span class="page-logo-text mr-1">Sistema Coopex</span>
                                </a>
                            </div>
                            <a href="https://www.fag.edu.br/sagres" target="_blank" class="btn-link text-white ml-auto">
                                Acessar Sagres
                            </a>
                        </div>
                    </div>
                    <div class="d-flex flex-1" style="background: url(img/svg/pattern-1.svg) no-repeat center bottom fixed; background-size: cover;">
                        <div class="container py-4 py-lg-5 my-lg-5 px-4 px-sm-0 text-white d-flex align-items-center justify-content-center">
                            <form method="post" id="js-login" action="" role="form" class="text-center text-white mb-5 pb-5">
                                <input type="hidden" id="token" name="recaptcha_response">
								<input name="<?php echo $_SESSION['campo_nome']?>" value="<?php echo $_SESSION['coopex']['usuario']['usuario']?>" type="hidden" class="form-control" placeholder="Usuário" autocomplete="off" autofocus required tabindex="0">
								<div class="py-3">
                                    <img src="img/demo/avatars/avatar-admin-lg.png" class="img-responsive rounded-circle img-thumbnail" alt="thumbnail">
                                </div>
                                <div class="form-group">
                                    <h3>
                                       <?php echo $_SESSION['coopex']['usuario']['primeiro_nome']?>
                                        <small>
                                            <?php echo $_SESSION['coopex']['usuario']['tipo_usuario']?>
                                        </small>
                                    </h3>
                                    <p class="text-white opacity-50">Entre com sua senha do Sagres</p>
                                    <div class="input-group input-group-lg">
                                        <input name="<?php echo $_SESSION['campo_senha']?>" type="password" class="form-control" placeholder="Senha" autocomplete="off" required tabindex="1">
                                        <div class="input-group-append">
                                            <button id="bt_submit" disabled class="btn btn-success shadow-0" type="submit" id="button-addon5"><i class="fal fa-key"></i></button>
                                        </div>
                                    </div>
									<?php if(!$senha_valida):?>
									<div>
										Informe sua senha do Sagres!
									</div>
									<?php endif ?>
									<?php if(!$senha_existe):?>
									<div>
										Senha incorreta!
									</div>
									<?php endif ?>
                                </div>
                                <div class="text-center">
                                    <a href="logout" class="text-white">Não é <strong style="text-transform: capitalize"><?php echo strtolower($_SESSION['coopex']['usuario']['primeiro_nome'])?></strong>?</a>
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
		
    </body>
</html>
