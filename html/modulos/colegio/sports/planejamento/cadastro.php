
<?php
	$id_menu = 108;
	$chave	 = "id_planejamento";

	if(isset($_GET['id'])){
		$$chave = $_GET['id'];
		$sql = "SELECT
				* 
			FROM
				colegio.planejamento
			WHERE id_planejamento = ".$_GET['id'];

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
		<li class="breadcrumb-item"><a href="javascript:void(0);">Sports</a></li>
		<li class="breadcrumb-item active">Planejamento</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="">ID. <?php echo $id_menu?>c</span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-calendar'></i> Sports
			<small>
				Cadastro de Planejamento
			</small>
		</h1>
	</div>
	<iframe class="d-none" name="dados" src=""></iframe>
	<form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/colegio/sports/planejamento/cadastro_dados.php">
		<input type="hidden" name="<?php echo $chave?>" value="<?php echo $$chave ? $$chave : 0?>">
		<div class="row">
			<div class="col-xl-12">
			  <div id="panel-2" class="panel">
				  <div class="panel-hdr">
						<h2>
							Modalidade
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
										<label class="form-label" for="id_modalidade">Modalidade<span class="text-danger">*</span></label>
										<?php
											$sql = "SELECT
														* 
													FROM
														colegio.modalidade
													ORDER BY
													modalidade";

											$serie = $coopex->query($sql);
										?>
										<select id="id_modalidade" name="id_modalidade" class="select2 form-control" required>
											<option value="">Selecione a Modalidade</option>
										<?php
											while($row = $serie->fetch(PDO::FETCH_OBJ)){
												$selecionado = '';
												if($dados->id_modalidade == $row->id_modalidade){
													$selecionado = 'selected=""';
												}
										?>	
											<option <?php echo isset($dados->id_modalidade) ? $selecionado : ""?> value="<?php echo $row->id_modalidade?>"><?php echo utf8_encode($row->modalidade)?></option>
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
									<div class="col-md-12 mb-3">
										<label class="form-label" for="resumo">Resumo<span class="text-danger">*</span></label>
										<textarea name="resumo" class="form-control" id="resumo" required><?php echo isset($dados->resumo) ? texto($dados->resumo) : ""?></textarea>
										<div class="invalid-feedback">
											Campo obrigatório
										</div>
									</div>
								</div>

								<div class="form-row">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="objetivo">Objetivo<span class="text-danger">*</span></label>
										<textarea name="objetivo" class="form-control" id="objetivo" required><?php echo isset($dados->objetivo) ? texto($dados->objetivo) : ""?></textarea>
										<div class="invalid-feedback">
											Campo obrigatório
										</div>
									</div>
								</div>

								<div class="form-row">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="conteudo">Conteúdo<span class="text-danger">*</span></label>
										<textarea name="conteudo" class="form-control" id="conteudo" required><?php echo isset($dados->conteudo) ? texto($dados->conteudo) : ""?></textarea>
										<div class="invalid-feedback">
											Campo obrigatório
										</div>
									</div>
								</div>

								<div class="form-row">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="avaliacao">Avaliação<span class="text-danger">*</span></label>
										<textarea name="avaliacao" class="form-control" id="avaliacao" required><?php echo isset($dados->avaliacao) ? texto($dados->avaliacao) : ""?></textarea>
										<div class="invalid-feedback">
											Campo obrigatório
										</div>
									</div>
								</div>

								<div class="form-row">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="projeto_evento">Projetos/Eventos que pretende participar interno e externo<span class="text-danger">*</span></label>
										<textarea name="projeto_evento" class="form-control" id="projeto_evento" required><?php echo isset($dados->projeto_evento) ? texto($dados->projeto_evento) : ""?></textarea>
										<div class="invalid-feedback">
											Campo obrigatório
										</div>
									</div>
								</div>

								

							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xl-12">
			  <div id="panel-2" class="panel">
				  <div class="panel-hdr">
						<h2>
							Planejamento
						</h2>
						<div class="panel-toolbar">
							<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
							<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content p-0">
							<div class="panel-content">

							<?
								for($i=2; $i<=12; $i++){
									$semana = "mes_$i";
							?>
								<div class="form-row">
									<div class="col-md-12 mb-3">
										<label class="form-label" for="mes_<?=$i?>">Mês <?=$i?><span class="text-danger">*</span></label>
										<input name="mes_<?=$i?>" type="text" value="<?php echo isset($dados->$semana) ? texto($dados->$semana) : ""?>" class="form-control" id="mes_<?=$i?>" required/>
										<div class="invalid-feedback">
											Campo obrigatório
										</div>
									</div>
								</div>
							<?
								}
							?>	
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

	/*function mostra_quantidade(){
		var qtd = $("#id_material").select2('data');
		$("#qtd_estoque").val(qtd[0].title)
	}*/

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
						echo "document.location.reload(true);";
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
