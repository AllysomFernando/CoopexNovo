
<?php
	$id_menu = 68;
	$chave	 = "id_entrada";


	if(isset($_GET['id'])){
		$$chave = $_GET['id'];
		$sql = "SELECT
				* 
			FROM
				tesouraria_meia.entrada
			WHERE id_material = ".$_GET['id'];

		$res = $coopex->query($sql);
		$dados = $res->fetch(PDO::FETCH_OBJ);
	} else {
		$$chave = 0;
	}

?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Entrada</a></li>
		<li class="breadcrumb-item active">Cadastro</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-calendar'></i> Entrada
			<small>
				Cadastro de Entrada
			</small>
		</h1>
	</div>
	<iframe class="d-none" name="dados" src=""></iframe>
	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/tesouraria/meia/entrada/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave?>" value="<?php echo $$chave ? $$chave : 0?>">
		<div class="row">
			<div class="col-xl-12">
			  <div id="panel-2" class="panel">
				  <div class="panel-hdr">
						<h2>
						Entrada
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
										<label class="form-label" for="validationCustom03">Meia<span class="text-danger">*</span></label>
										<?php
											$sql = "SELECT
														* 
													FROM
													tesouraria_meia.material
													ORDER BY
														material";

											$serie = $coopex->query($sql);
										?>
										<select onchange="mostra_quantidade()" id="id_material" name="id_material" class="select2 form-control" required>
											<option value="">Selecione o Tamanho</option>
										<?php
											while($row = $serie->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if($dados->id_material == $row->id_material){
													$selecionado = 'selected=""';
												}
										?>	
											<option title="<?php echo $row->quantidade?>" <?php echo isset($dados->id_material) ? $selecionado : ""?> value="<?php echo $row->id_material?>"><?php echo utf8_encode($row->material)?></option>
										<?php
											}
										?>	
										</select>
										<div class="invalid-feedback">
											Selecione o Material
										</div>
									</div>
								</div>

								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Quantidade em estoque</label>
										<input disabled value="" type="number" class="form-control" id="qtd_estoque">
										<div class="invalid-feedback">
											Campo obrigatório, preencha o Material, Ex.: São Lucas - Ambulatório
										</div>
									</div>
								</div>	

								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" for="validationCustom03">Quantidade <span class="text-danger">*</span></label>
										<input name="quantidade" value="<?php echo isset($dados->quantidade) ? texto($dados->quantidade) : ""?>" type="number" class="form-control" id="quantidade" placeholder="" value="" required>
										<div class="invalid-feedback">
											Campo obrigatório, preencha o Material, Ex.: São Lucas - Ambulatório
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
<script src="js/formplugins/select2/select2.bundle.js"></script>

<script>

	$(document).ready(function(){
		$('.select2').select2();
	});

	function mostra_quantidade(){
		var qtd = $("#id_material").select2('data');
		$("#qtd_estoque").val(qtd[0].title)
	}

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
