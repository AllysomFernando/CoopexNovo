<?php
	$id_menu = 26;
	$chave	 = "id_periodo";

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Financeiro</a></li>
		<li class="breadcrumb-item active">Retorno Bradesco</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-calendar'></i> Retorno Bradesco
			<small>
				Carregamento de arquivo de retorno
			</small>
		</h1>
	</div>
	<iframe class="d-none" name="dados" src=""></iframe>
	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/financeiro/retorno/bradesco_dados.php" enctype="multipart/form-data">
		<input type="hidden" name="<?php echo $chave?>" value="<?php echo $$chave ? $$chave : 0?>">
		<div class="row">
			<div class="col-xl-12">
			  <div id="panel-2" class="panel">
				  <div class="panel-hdr">
						<h2>
							Retorno Bradesco
						</h2>
					</div>
					<div class="panel-container show">
						<div class="panel-content p-0">
							<div class="panel-content">
								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Arquivo <span class="text-danger">*</span></label>
										<input name="arquivo_retorno" value="<?php echo isset($row->periodo) ? $row->periodo : ""?>" type="file" class="form-control" id="pre_inscricao_data_inicio_fixo" placeholder="" value="" required>
										<div class="invalid-feedback">
											Selecione o arquivo de retorno .RET
										</div>
									</div>
								</div>	

							</div>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
							<button class="btn btn-primary ml-auto" type="submit">Carregar</button>
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
	function retornoOK(id_retorno){
		//alert(id_retorno);

		Swal.fire({
			type: "success",
			title: "Arquivo de retorno carregado com sucesso!",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				document.location = "https://coopex.fag.edu.br/financeiro/retorno/resumo/"+id_retorno
			}
		});
	}
	function cadastroFalha(operacao){
		Swal.fire({
			type: "error",
			title: "Falha ao carregar arquivo de retorno!",
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
