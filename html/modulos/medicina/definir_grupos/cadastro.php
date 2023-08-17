<?php
	require_once("php/sqlsrv.php");
	
	$id_menu = 50;
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
			<i class='subheader-icon fal fa-repeat'></i> Grupos
			<small>
				Cadastro de Grupos
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
							1. Grupo
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
									<div class="col-md-4 mb-3">
										<label class="form-label" for="validationCustom03">Período<span class="text-danger">*</span></label>
										<?php
											$sql = "SELECT
														*
													FROM
														medicina.periodo
													ORDER BY
														periodo";

											$periodo = $coopex->query($sql);
										?>
										<select id="id_periodo" onchange="carregar_grupo()" name="id_periodo" class="select2 form-control" required="">
											<option value="">Selecione o Período</option>
										<?php
											while($row = $periodo->fetch(PDO::FETCH_OBJ)){
					
										?>	
											<option value="<?php echo $row->id_periodo?>"><?php echo texto($row->periodo)?></option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											O período é obrigatório
										</div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="panel-2" class="panel">
            <div class="panel-hdr">
                <h2>
                    Grupos
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
                    <button class="btn btn-panel waves-effect waves-themed" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
                </div>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="border bg-light rounded-top">

                        <div id="grupos">
                        	
                        </div>
                </div>
            </div>
        </div>

        <div id="aux"></div>


		
		<textarea class="d-none" name="cronograma" id="cronograma" rows="10" cols="100"></textarea>
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

	function carregar_grupo(){
		var id_periodo = $("#id_periodo").val();
		$("#grupos").load( "modulos/medicina/definir_grupos/ajax/carrega_grupo.php?id_periodo=" + id_periodo );
	}

	function adicionar_pessoa_grupo(id_grupo, id_pessoa){
		$("#aux").load( `modulos/medicina/definir_grupos/ajax/adicionar_pessoa_grupo.php?id_grupo=${id_grupo}&id_pessoa=${id_pessoa}` );
	}

	function excluir_pessoa_grupo(id_grupo, id_pessoa){
		$("#aux").load( `modulos/medicina/definir_grupos/ajax/excluir_pessoa_grupo.php?id_grupo=${id_grupo}&id_pessoa=${id_pessoa}` );
		$(`#${id_grupo}${id_pessoa}`).remove();
	}

	function adicionar_pessoa(grupo){
		var id_pessoa = $("#id_pessoa_"+grupo).find(':selected');
		console.dir(id_periodo);
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