<?php

	$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];

	$sql = "SELECT
				p.id_reoferta,
				disciplina,
				pago AS pre_pago,
				permissao_matricula 
			FROM
				coopex_reoferta.pre_matricula p
				INNER JOIN coopex_reoferta.reoferta r ON p.id_reoferta = r.id_reoferta 
			WHERE
				p.id_pessoa = $id_pessoa 
			ORDER BY
				disciplina";
	$pre_inscritos = $coopex->query($sql);
?>

<iframe class="d-none" name="dados" src=""></iframe>
<form id="form_excluir_registro" method="post" target="dados" action="modulos/reoferta/cadastro/cadastro_dados.php">
	<input type="hidden" id="excluir_registro" name="excluir_registro">
</form>	
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Cooopex</a></li>
		<li class="breadcrumb-item active">Dashboard</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
            <i class="subheader-icon fal fa-chart-area"></i> Dashboard <span class="fw-300">Acadêmico</span>
            <small>
            	Resumo das suas atividades no Sistema Coopex
            </small>
        </h1>
	</div>
	<div class="row">
		<div class="col-xl-12">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
                    <h2>
                        Minhas reofertas
                    </h2>

                </div>
				<div class="panel-container show">
					<div class="panel-content">
						<!-- datatable start -->
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th>Disciplina</th>
									<th class="text-center" width="120">Pré-matrícula</th>
									<th class="text-center" width="120">Matrícula</th>
									<th class="text-center" width="120">Permissão para Matricula</th>
								</tr>
							</thead>
							<tbody>
								<?php
									while($row = $pre_inscritos->fetch(PDO::FETCH_OBJ)){

										if($row->pre_pago){
											$situacao_pre = "Pago";
											$cor_pre = "success";
										} else {
											$situacao_pre = "Não pago";
											$cor_pre = "warning";
										}

										$id_reoferta = $row->id_reoferta;
										$sql = "SELECT
													*
												FROM
													coopex_reoferta.matricula
												WHERE
													id_pessoa = 5000202475 
													
													and id_reoferta = $id_reoferta";
										$inscritos = $coopex->query($sql);
											


										if(!$inscritos->rowCount()){
											$situacao_matricula = "Não Matriculado";
											$cor_matricula = "danger";
										} else{
											$row2 = $inscritos->fetch(PDO::FETCH_OBJ);
											if($row2->pago){
												$situacao_matricula = "Pago";
												$cor_matricula = "success";
											} else {
												$situacao_matricula = "Não pago";
												$cor_matricula = "warning";
											}

										} 

								?>
								<tr>
									<td class="pointer"><a href="reoferta/inscricao/inscricao/<?php echo $row->id_reoferta?>" class="btn">
										<?php echo texto($row->disciplina)?></a></td>
									<td class="text-center">
										<span class="btn btn-<?php echo $cor_pre;?> btn-sm btn-block waves-effect waves-themed"><?php echo $situacao_pre;?></span>
									</td>
									<td class="text-center">
										<span class="btn btn-<?php echo $cor_matricula;?> btn-sm btn-block waves-effect waves-themed"><?php echo $situacao_matricula;?></span>
									</td>

									<td class="text-center">
										<?php
											if($row->permissao_matricula){
												echo '<a href="reoferta/inscricao/inscricao/'.texto($row->id_reoferta).'" class="btn btn-success btn-icon waves-effect waves-themed"><i class="fal fa-check"></i></a>';
											} else {
												echo '<a href="reoferta/inscricao/inscricao/'.texto($row->id_reoferta).'" class="btn btn-danger btn-icon waves-effect waves-themed"><i class="fal fa-times"></i></a>';	
											}
										?>
									</td>
								</tr>
								<?php
									}
								?>
							</tbody>
						</table>
						<!-- datatable end -->
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
<iframe class="d-none" name="dados"></iframe>
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/reoferta/inscritos/autorizacao_dados.php">
    	<input type="hidden" name="id_autorizacao" id="id_autorizacao">
	    <input type="hidden" name="id_pre_matricula" id="id_pre_matricula">
	    <input type="text" name="id_reoferta" value="<?php echo $id_reoferta?>">
	    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title">Autorizar</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
	                </button>
	            </div>
	            <div class="modal-body">
	                <div class="form-row">	
						<div class="col-md-12 mb-3">
							<label class="form-label" for="validationCustom02">Observações <span class="text-danger"></span></label>
							<textarea id="obs" type="text" name="obs" class="form-control"></textarea>
						</div>
						
					</div>
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
	                <button type="submit" class="btn btn-primary">Autorizar</button>
	            </div>
	        </div>
	    </div>
	</form>
</div>


<div class="modal fade" id="modal_autorizacao" tabindex="-1" role="dialog" aria-hidden="true">
    <form class="needs-validation" novalidate="" method="post" target="dados" action="modulos/reoferta/inscritos/autorizacao_dados.php">
    	<input type="hidden" name="id_autorizacao" id="id_autorizacao">
	    <input type="hidden" name="id_pre_matricula" id="id_pre_matricula">
	    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title">Autorização</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
	                </button>
	            </div>
	            <div class="modal-body">
	                <div class="form-row">	
						<div class="col-md-12 mb-3">
							<label class="form-label" for="validationCustom02">Autorizado por <span class="text-danger"></span></label>
							<input id="nome" type="text" readonly="" class="form-control">
						</div>
					</div>
					<div class="form-row">	
						<div class="col-md-12 mb-3">
							<label class="form-label" for="validationCustom02">Em <span class="text-danger"></span></label>
							<input id="data_autorizacao" type="text" readonly="" class="form-control">
						</div>
					</div>
					<div class="form-row">	
						<div class="col-md-12 mb-3">
							<label class="form-label" for="validationCustom02">Observações <span class="text-danger"></span></label>
							<input id="motivo" type="text" readonly="" class="form-control">
						</div>
					</div>
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
	            </div>
	        </div>
	    </div>
	</form>
</div>

<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script>

	function autorizar(id_pre_matricula, id_autorizacao){
		$("#id_pre_matricula").val(id_pre_matricula);
		$("#id_autorizacao").val(id_autorizacao);
	}

	function mostrar_autorizacao(nome, data_autorizacao, obs){
		$("#nome").val(nome);
		$("#data_autorizacao").val(data_autorizacao);
		$("#motivo").val(obs);
	}


	function autorizacaoOK(){ 
		
		$("#obs").val('');
		$("#id_pre_matricula").val('');
		$("#id_autorizacao").val('');
		$("#modal .close").click();

		Swal.fire({
			type: "success",
			title: "Autorização efetuada com sucesso!",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {

				document.location.reload(true)
			}
		});
	}

	//MENSAGEM DE FALHA NO CADASTRO
	function autorizacaoFalha(){ 
		$("#obs").val('');
		$("#id_pre_matricula").val('');
		$("#id_autorizacao").val('');
		$("#modal .close").click();

		Swal.fire({
			type: "error",
			title: "Falha ao autorizar",
			showConfirmButton: false,
			timer: 1500
		});
	}

    $(document).ready(function()
    {
        $('#dt-basic-example').dataTable(
        {
            responsive: true,
            pageLength: 15,
            order: [
                [2, 'desc']
            ]
            // ,
            // rowGroup:
            // {
            //     dataSrc: 1
            // }
        });


		$(".js-sweetalert2-example-13").on("click", function()
        {
            Swal.fire(
            {
                title: "Observação",
                input: "text",
                inputAttributes:
                {
                    autocapitalize: "off"
                },
                showCancelButton: true,
                confirmButtonText: "Autorizar",
                showLoaderOnConfirm: true,
                preConfirm: function preConfirm(login)
                {
                    return fetch("//api.github.com/users/".concat(login))
                        .then(function(response)
                        {
                            if (!response.ok)
                            {
                                throw new Error(response.statusText);
                            }

                            return response.json();
                        })
                        .catch(function(error)
                        {
                            Swal.showValidationMessage("Request failed: ".concat(error));
                        });
                },
                allowOutsideClick: function allowOutsideClick()
                {
                    return !Swal.isLoading();
                }
            }).then(function(result)
            {
                if (result.value)
                {
                    Swal.fire(
                    {
                        title: "".concat(result.value.login, "'s avatar"),
                        imageUrl: result.value.avatar_url
                    });
                }
            });
        }); //Dynamic queue example

    });

	function exclusaoOK(){
		Swal.fire({
			type: "success",
			title: "Registro excluido com sucesso!",
			showConfirmButton: false,
			timer: 1500,
			onClose: () => {
				document.location.reload(true)
			}
		});
	}
	function exclusaoFalha(){
		Swal.fire({
			type: "error",
			title: "Falha ao excluir registro",
			showConfirmButton: true
		});
	}
</script>