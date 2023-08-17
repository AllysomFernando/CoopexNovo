<?php
	$id_menu = 22;

	#VERIFICA SE O TIPO DE USUÁRIO POSSUI PERMISSÃO PARA ACESSAR TODOS OS REGISTROS
	if(in_array($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'],array(1,2,3,8,9,11))){
		$where  = " AND 1=1 ";
	} else {
		$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];
		$where  = "AND (coopex_reoferta.reoferta.id_pessoa = $id_pessoa
					OR id_departamento IN (SELECT id_departamento FROM coopex.departamento_pessoa WHERE id_pessoa = $id_pessoa)) ";
	}

	$sql = "SELECT
				*,
				DATE_FORMAT( data_cadastro, '%d/%m/%Y' ) AS data_cadastro,
				carga_horaria_disciplina > carga_horaria AS reducao 
			FROM
				coopex_reoferta.reoferta
				INNER JOIN coopex_reoferta.periodo USING ( id_periodo )
				INNER JOIN coopex.departamento USING ( id_departamento )
				INNER JOIN coopex_reoferta.carga_horaria USING ( id_carga_horaria ) 
			WHERE
				coopex_reoferta.reoferta.excluido = 0 
				$where
			ORDER BY
				disciplina, data_cadastro DESC";
	$reoferta = $coopex->query($sql);
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
		<li class="breadcrumb-item"><a href="javascript:void(0);">Reoferta</a></li>
		<li class="breadcrumb-item active">Consulta</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title col-6">
			<i class='subheader-icon fal fa-repeat'></i> Reofertas
			<small>
				Gerenciamento de Reofertas
			</small>
		</h1>
		<?php
			if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][2])){
		?>
		<div class="subheader-title col-6 text-right">
			<a href="reoferta/cadastro">
				<button type="button" class="btn btn-lg btn-primary waves-effect waves-themed">
					<span class="ni ni-plus mr-3"></span>
					Cadastrar Reoferta
				</button>
			</a>
		</div>
		<?php
			}
		?>
	</div>
	<div class="row">
		<div class="col-xl-12">
			<div id="panel-1" class="panel">

				<div class="panel-container show">
					<div class="panel-content">
						<!-- datatable start -->
						<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th>Disciplina</th>
									<th>Curso</th>
									<th>Período</th>
									<th>Cadastro</th>
									<th>Situação</th>
									<th class="text-center">Ações</th>
								</tr>
							</thead>
							<tbody>
								<?php
									while($row = $reoferta->fetch(PDO::FETCH_OBJ)){

										if(!$row->enviado_aprovacao){
											$situacao = "Não enviado para aprovação";
											$cor = "warning";
										} else if($row->enviado_aprovacao_reducao == 1 && $row->id_parecer_reducao == 1){
											$situacao = "Aguardando aprovação de redução de CH";
											$cor = "warning";
										} else if($row->id_parecer_reducao == 2){
											$situacao = "Redução de CH aprovada";
											$cor = "success";
										} else if($row->id_parecer_reducao == 3){
											$situacao = "Redução de CH reprovada";
											$cor = "danger";
										} else if($row->enviado_aprovacao == 1 && $row->id_parecer == 1){
											$situacao = "Aguardando aprovação final";
											$cor = "warning";
										} else if($row->id_parecer == 2){
											$situacao = "Aprovada";
											$cor = "success";
										} else if($row->id_parecer == 3){
											$situacao = "Reprovada";
											$cor = "danger";
										} 
								?>
								<tr>
									<td class="pointer"><?php echo texto($row->disciplina)?></td>
									<td><?php echo texto($row->departamento)?></td>
									<td><?php echo texto($row->periodo)?></td>
									<td><?php echo ($row->data_cadastro)?></td>
									<td><span class="badge badge-<?php echo $cor;?> badge-pill"><?php echo $situacao;?></span></td>
									<td style="width: 70px">
										<?php
											if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][3]) ||
											   isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][5]) ||
											   isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][6])){
										?>
										<a href="reoferta/cadastro/cadastro/<?php echo $row->id_reoferta?>" class="btn btn-sm btn-icon btn-outline-success rounded-circle mr-2" title="Editar Registro">
											<i class="fal fa-pencil-alt"></i>
										</a>
										<?php
											}
										?>
										
										<?php
											if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][4])){
										?>
										<a href="javascript:excluir_registro('coopex_reoferta.reoferta', 'id_reoferta', <?php echo $row->id_reoferta?>)" class="btn btn-sm btn-icon btn-outline-danger rounded-circle" title="Excluir Registro">
											<i class="fal fa-times"></i>
										</a>
										<?php
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

        <script src="js/datagrid/datatables/datatables.bundle.js"></script>
        <script>


            $(document).ready(function()
            {
                $('#dt-basic-example').dataTable(
                {
                    responsive: true,
                    pageLength: 15,
                    order: [
                        [2, 'desc']
                    ],
                    rowGroup:
                    {
                        dataSrc: 1
                    },
                    columnDefs: [
			            {
			                "targets": [ 1 ],
			                "visible": false
						}
        			]
                });
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
    </body>
</html>
