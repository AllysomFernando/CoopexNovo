<?php
	$id_menu = 26;
	$id_retorno	 = $_GET['id'];

	$sql = "SELECT
				*,
				SUBSTRING_INDEX( seu_numero, '-', 1 ) AS tipo,
				sum( valor_pago ) AS total,
				count(valor_pago) AS qtd
			FROM
				coopex_financeiro.retorno_bradesco_pagamento 
			WHERE
				id_retorno = $id_retorno 
			GROUP BY
				tipo";
	$resumo = $coopex->query($sql);
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
				Resumo de pagamentos
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
                        <div class="panel-content">
                            <div class="frame-wrap">
                                <table class="table m-0 table-hover">
                                    <thead class="thead-themed">
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Pagamentos</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                	<?php
                                		$total = 0;
										while($row = $resumo->fetch(PDO::FETCH_OBJ)){

											if($row->tipo == "REMB"){
												$tipo = "Reoferta - Matrícula";
											} else if($row->tipo == "REPB"){
												$tipo = "Reoferta - Pré-matrícula";
											} else if($row->tipo == "EVTB"){
												$tipo = "Evento";
											} else if($row->tipo == "BIGJ"){
												$tipo = "Big Jump - Meias";
											} else if($row->tipo == "CDTM"){
												$tipo = "Clube da Tarefa";
											}
											
											$total += $row->total;
									?>
                                        <tr>
                                            <td class="fw-500"><?php echo $tipo?></td>
                                            <td><?php echo $row->qtd?></td>
                                            <td>R$ <?php echo number_format($row->total, 2, ',', '.')?></td>
                                        </tr>
									<?php
										}
									?>
									<tr class="thead-themed" >
                                        <td class="fw-900">TOTAL</td>
                                        <td class="text-right"></td>
                                        <td class="fw-900">R$ <?php echo number_format($total, 2, ',', '.')?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
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
		alert(id_retorno);

		/*var msg = operacao == 1 ? "Registro cadastrado com sucesso" : "Registro alterado com sucesso";
		Swal.fire({
			type: "success",
			title: msg,
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				//document.location.reload(true)
			}
		});*/
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
