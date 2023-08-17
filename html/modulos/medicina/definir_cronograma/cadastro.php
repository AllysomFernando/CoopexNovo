<?php
	require_once("php/sqlsrv.php");
	
	$id_menu = 54;
	$chave	 = "id_grupo_periodo";

	if(isset($_GET['id'])){
		$$chave = $_GET['id'];

		$sql = "SELECT
					*
				FROM
					medicina.horario
				WHERE
					id_horario = ".$_GET['id'];
		$res = $coopex->query($sql);
		$dados = $res->fetch(PDO::FETCH_OBJ);
	} else {
		$$chave = 0;
	}
?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/core.js"></script>

<main id="js-page-content" role="main" class="page-content">

	<?php
		if(!isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][1])){
	?>
	<div class="alert border-danger bg-transparent text-secondary fade show" role="alert">
		<div class="d-flex align-items-center">
			<div class="alert-icon">
				<span class="icon-stack icon-stack-md">
					<i class="base-7 icon-stack-3x color-danger-900"></i>
					<i class="fal fa-times icon-stack-1x text-white"></i>
				</span>
			</div>
			<div class="flex-1">
				<span class="h5 color-danger-900">Seu usuário não possui permissão para acessar esta tela</span>
			</div>
			<a href="javascript:solicitarPermissao()" class="btn btn-outline-danger btn-sm btn-w-m">Solicitar acesso</a>
		</div>
	</div>
	<?php		
			exit;
		}
	?>

	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Grupos</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block">
			<span class="">ID. <?php echo $id_menu?>c</span>
		</li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-repeat'></i> Definir Cronograma
			<small>
				Definir Cronograma
			</small>
		</h1>
	</div>

	<iframe class="d-none" name="dados" src="" style="position: fixed; z-index: 999999999999; width: 100%; background-color: #fff; top: 0; left: 0; height: 200px"></iframe>

	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/medicina/grupos/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave?>" value="<?php echo $$chave ? $$chave : 0?>">
		<div class="row">
			<div class="col-xl-12">
			  	<div id="panel-2" class="panel">
				  	<div class="panel-hdr">
						<h2>
							1. Definir Cronograma
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="form-row">
							
								</div>
								
							</div>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
							<button onclick="gerar_subgrupos()" class="btn btn-primary btn-lg" type="button">Gerar Cronograma</button>
						</div>
					</div>
				</div>
			</div>
		</div>

	</form>
</main>

<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/datagrid/datatables/dataTables.editor.min.js"></script>
<script src="js/moment-with-locales.js"></script>
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>

<script>
	//MENSAGEM DE CADASTRO OK
	function cadastroOK(operacao){ 
		var msg = operacao == 1 ? "Registro cadastrado com sucesso" : "Registro alterado com sucesso";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				<?php
					if(!isset($_GET['id'])){
						echo "window.history.back();";
					} else {
						echo "document.location.reload(true);";
					}
				?>
				//document.location.reload(true)
			}
		});
	}

	//MENSAGEM DE FALHA NO CADASTRO
	function cadastroFalha(operacao){ 
		var msg = operacao == 1 ? "Falha ao cadastrar dados" : "Falha ao alterar dados";
		Swal.fire({
			type: "error",
			title: msg,
			showConfirmButton: false,
			timer: 1500
		});
	}

	function gerar_subgrupos(){
		var id_ordem = $("#id_ordem").val();

		$.ajax({
			method: "POST",
			url: "modulos/medicina/definir_cronograma/subgrupo.php",
			data: { id_ordem: id_ordem }
		})
		.done(function( msg ) {
			console.log(msg);
			gerar_cronograma(id_ordem);
		});
	}

	function gerar_cronograma(){
		var id_ordem = $("#id_ordem").val();

		$.ajax({
			method: "POST",
			url: "modulos/medicina/definir_cronograma/cronograma.php",
			data: { id_ordem: id_ordem }
		})
		.done(function( msg ) {
			alert( "Cronograma Gerado: " + msg );
		});
	}



	$(document).ready(function(){

		//initApp.listFilter($('#js_list_accordion'), $('#js_list_accordion_filter'));
		//initApp.buildNavigation($('#js_list_accordion'));

		$(":input").inputmask();
		$('.select2').select2();

		


	
	});

	(function() {
		'use strict';
		window.addEventListener('load', function(){
			// Fetch all the forms we want to apply custom Bootstrap validation styles to
			var forms = document.getElementsByClassName('needs-validation');
			// Loop over them and prevent submission
			var validation = Array.prototype.filter.call(forms, function(form){
				form.addEventListener('submit', function(event){
					if (form.checkValidity() === false){
						event.preventDefault();
						event.stopPropagation();
					}
					form.classList.add('was-validated');
				}, false);
			});
		}, false);
	})();

</script>