<?php
	$id_menu = 23;
	$chave	 = "id_periodo";

	if(isset($_GET['id'])){
		$$chave = $_GET['id'];
		$sql = "SELECT
				id_periodo,
				periodo,
				DATE_FORMAT( pre_inscricao_data_inicial, '%d/%m/%Y' ) AS pre_inscricao_data_inicial,
				DATE_FORMAT( pre_inscricao_data_final, '%d/%m/%Y' )  AS pre_inscricao_data_final,
				DATE_FORMAT( inscricao_data_inicial, '%d/%m/%Y' ) 	 AS inscricao_data_inicial,
				DATE_FORMAT( inscricao_data_final, '%d/%m/%Y' ) 	 AS inscricao_data_final,
				ativo 
			FROM
				coopex_reoferta.periodo
			WHERE id_periodo = ".$_GET['id'];

		$res = $coopex->query($sql);
		$row = $res->fetch(PDO::FETCH_OBJ);
	} else {
		$$chave = 0;
	}

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Período de Reofertas</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-calendar'></i> Período de Reofertas
			<small>
				Cadastro de Períodos de Reofertas
			</small>
		</h1>
	</div>
	<iframe class="d-none" name="dados" src=""></iframe>
	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/reoferta/periodo/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave?>" value="<?php echo $$chave ? $$chave : 0?>">
		<input type="hidden" name="periodo_letivo" value="1">
		<div class="row">
			<div class="col-xl-12">
			  <div id="panel-2" class="panel">
				  <div class="panel-hdr">
						<h2>
							Período de Reofertas
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
										<label class="form-label" for="validationCustom03">Período <span class="text-danger">*</span></label>
										<input name="periodo" value="<?php echo isset($row->periodo) ? $row->periodo : ""?>" type="text" class="form-control" id="pre_inscricao_data_inicio_fixo" placeholder="" value="" required>
										<div class="invalid-feedback">
											Campo obrigatório, preencha o período, Ex.: <?php echo date("Y")?>/2
										</div>
									</div>
								</div>	
								<div class="form-row">	
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Inicio da Pré-Matrícula <span class="text-danger">*</span></label>
										<input name="pre_inscricao_data_inicial" value="<?php echo isset($row->pre_inscricao_data_inicial) ? $row->pre_inscricao_data_inicial : ""?>" data-inputmask="'mask': '99/99/9999'" type="text" class="form-control periodo_diferente" id="pre_inscricao_data_inicio" placeholder="" required>
										<div class="invalid-feedback">
											Campo obrigatório
										</div>
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Fim da Pré-Matrícula <span class="text-danger">*</span></label>
										<input name="pre_inscricao_data_final" value="<?php echo isset($row->pre_inscricao_data_final) ? $row->pre_inscricao_data_final : ""?>" data-inputmask="'mask': '99/99/9999'" type="text" class="form-control periodo_diferente" id="pre_inscricao_data_final" placeholder="" required>
										<div class="invalid-feedback">
											Campo obrigatório
										</div>
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Inicio da Matrícula <span class="text-danger">*</span></label>
										<input name="inscricao_data_inicial" value="<?php echo isset($row->inscricao_data_inicial) ? $row->inscricao_data_inicial : ""?>" data-inputmask="'mask': '99/99/9999'" type="text" class="form-control periodo_diferente" id="inscricao_data_inicial" placeholder=""  required>
										<div class="invalid-feedback">
											Campo obrigatório
										</div>
									</div>
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom02">Fim da Matrícula <span class="text-danger">*</span></label>
										<input name="inscricao_data_final" value="<?php echo isset($row->inscricao_data_final) ? $row->inscricao_data_final : ""?>" data-inputmask="'mask': '99/99/9999'" type="text" class="form-control periodo_diferente" id="inscricao_data_final" placeholder="" required>
										<div class="invalid-feedback">
											Campo obrigatório
										</div>
									</div>
								</div>
								<div class="form-row form-group">
									<div class="col-md-4 mb-3">
										<label class="form-label">Período Ativo</label>
										<div class="custom-control custom-switch">
											<input type="hidden" id="ativo_hidden" name="ativo" value="<?php echo isset($row->ativo) && $row->ativo ? "true" : "false"?>">
											<input onchange="$('#ativo_hidden').val(this.checked)" <?php echo isset($row->ativo) && $row->ativo ? "checked" : ""?>  contenteditable="" type="checkbox" class="custom-control-input is-invalid" id="select_ativo" >
											<label class="custom-control-label" for="select_ativo">Permitir cadastro de novas reofertas neste período</label>
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
