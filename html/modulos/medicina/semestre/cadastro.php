<?php
	$id_menu = 57;
	$chave	 = "id_semestre";

	$sql = "SELECT
			id_semestre,
			semestre,
			DATE_FORMAT( data_inicio, '%d/%m/%Y' ) AS data_inicio,
			DATE_FORMAT( data_fim, '%d/%m/%Y' )  AS data_fim
		FROM
			medicina.semestre";

	$res = $coopex->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);


?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Semestre</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-calendar'></i> Período de Semestre
			<small>
				Cadastro de Períodos do Semestre
			</small>
		</h1>
	</div>
	<iframe class="d-none" name="dados" src=""></iframe>
	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/medicina/semestre/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave?>" value="<?php echo $$chave ? $$chave : 0?>">

		<div class="row">
			<div class="col-xl-12">
			  <div id="panel-2" class="panel">
				  <div class="panel-hdr">
						<h2>
							Período do Semestre
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
										<label class="form-label" for="validationCustom03">Semestre <span class="text-danger">*</span></label>
										<input name="semestre" value="<?php echo isset($row->semestre) ? $row->semestre : ""?>" type="text" class="form-control" id="semestre" placeholder="" value="" required>
										<div class="invalid-feedback">
											Campo obrigatório, preencha o período, Ex.: <?php echo date("Y")?>/2
										</div>
									</div>
								</div>	
								<div class="form-row">	
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Inicio do Semestre <span class="text-danger">*</span></label>
										<input name="data_inicio" value="<?php echo isset($row->data_inicio) ? $row->data_inicio : ""?>" data-inputmask="'mask': '99/99/9999'" type="text" class="form-control periodo_diferente" id="data_inicio" placeholder="" required>
										<div class="invalid-feedback">
											Campo obrigatório
										</div>
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Fim do Semestre <span class="text-danger">*</span></label>
										<input name="data_fim" value="<?php echo isset($row->data_fim) ? $row->data_fim : ""?>" data-inputmask="'mask': '99/99/9999'" type="text" class="form-control periodo_diferente" id="data_fim" placeholder="" required>
										<div class="invalid-feedback">
											Campo obrigatório
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


<script>
	function cadastroOK(operacao){
		var msg = operacao == 1 ? "Registro cadastrado com sucesso" : "Registro alterado com sucesso";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				//document.location.reload(true)
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

		$(":input").inputmask();
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
