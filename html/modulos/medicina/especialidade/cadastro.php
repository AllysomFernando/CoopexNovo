<?php
	$id_menu = 45;
	$chave	 = "id_especialidade";

	if(isset($_GET['id'])){
		$$chave = $_GET['id'];
		$sql = "SELECT
				* 
			FROM
				medicina.especialidade
			WHERE id_especialidade = ".$_GET['id'];

		$res = $coopex->query($sql);
		$row = $res->fetch(PDO::FETCH_OBJ);
	} else {
		$$chave = 0;
	}

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/bootstrap-colorpicker/bootstrap-colorpicker.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Especialidade</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-calendar'></i> Especialidade
			<small>
				Cadastro de Especialidades
			</small>
		</h1>
	</div>
	<iframe class="d-none" name="dados" src=""></iframe>
	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/medicina/especialidade/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave?>" value="<?php echo $$chave ? $$chave : 0?>">
		<input type="hidden" name="especialidade" value="1">
		<div class="row">
			<div class="col-xl-12">
			  <div id="panel-2" class="panel">
				  <div class="panel-hdr">
						<h2>
							Especialidade
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
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Especialidade <span class="text-danger">*</span></label>
										<input name="especialidade" value="<?php echo isset($row->especialidade) ? texto($row->especialidade) : ""?>" type="text" class="form-control" id="pre_inscricao_data_inicio_fixo" placeholder="" value="" required>
										<div class="invalid-feedback">
											Campo obrigatório, preencha a Especialidade, Ex.: Ginecologia
										</div>
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Cor <span class="text-danger">*</span></label>
										<div id="cp1" class="input-group colorpicker-element" title="Using input value" data-colorpicker-id="2">
	                                        <input style="display: none;" type="text" class="form-control input-lg" name="cor" value="<?php echo isset($row->cor) ? texto($row->cor) : "#FFFFFF"?>">
	                                        <span class="input-group-append">
	                                            <span class="input-group-text colorpicker-input-addon" data-original-title="" title="" tabindex="0"><i style="background: <?php echo isset($row->cor) ? texto($row->cor) : "#FFFFFF"?>;"></i></span>
	                                        </span>
	                                    </div>
									</div>

								</div>	
							</div>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
							<button class="btn btn-primary ml-auto" type="submit"><?php echo isset($_GET['id']) ? "Alterar" : "Cadastrar"?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</main>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<script src="js/formplugins/bootstrap-colorpicker/bootstrap-colorpicker.js"></script>

<script>
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
			}
		});
	}
	function cadastroFalha(operacao){
		var msg = operacao == 1 ? "Falha ao cadastrar dados" : "Falha ao alterar dados";
		Swal.fire({
			type: "error",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				window.history.back();
			}
		});
	}

	$(document).ready(function(){
		$('#cp1').colorpicker();

	});

    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function() {
        'use strict';
        window.addEventListener('load', function()
        {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form)
            {
                form.addEventListener('submit', function(event)
                {
                    if (form.checkValidity() === false)
                    {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();

</script>
