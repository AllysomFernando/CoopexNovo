<?php
	$id_menu = 42;

	#VERIFICA SE O TIPO DE USUÁRIO POSSUI PERMISSÃO PARA ACESSAR TODOS OS REGISTROS
	if(in_array($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'],array(14))){
		$where  = " WHERE 1=1 ";
	} else {
		$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];
		$where  = " WHERE id_pessoa = $id_pessoa ";
	}

	$sql = "SELECT
				id_prospect,
				b.curso AS curso_graduacao,
				c.curso AS curso_pos,
				nome,
				telefone, DATE_FORMAT( data_cadastro, '%d/%m/%Y' ) AS data_cadastro 
			FROM
				marketing.prospect a
			LEFT JOIN coopex.curso b USING (id_curso)
			LEFT JOIN coopex.pos_curso c ON a.id_curso = c.id_pos_curso $where AND a.excluido = 0";
	$reoferta = $coopex->query($sql);
?>

<iframe class="d-none" name="dados" src=""></iframe>
<form id="form_excluir_registro" method="post" target="dados" action="modulos/marketing/prospeccao/cadastro_dados.php">
	<input type="hidden" id="excluir_registro" name="excluir_registro">
</form>	
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Prospects</a></li>
		<li class="breadcrumb-item active">Consulta</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title col-6">
			<i class='subheader-icon fal fa-repeat'></i> Prospects
			<small>
				Prospects
			</small>
		</h1>
		<?php
			if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][2])){
		?>
		<div class="subheader-title col-6 text-right">
			<a href="marketing/prospeccao/cadastro">
				<button type="button" class="btn btn-lg btn-primary waves-effect waves-themed">
					<span class="ni ni-plus mr-3"></span>
					Cadastrar Prospect
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
									<th>Nome</th>
									<th>Curso</th>
									<th>Telefone</th>
									<th>Data Cadastro</th>
									<th class="text-center">Ações</th>
								</tr>
							</thead>
							<tbody>
								<?php
									while($row = $reoferta->fetch(PDO::FETCH_OBJ)){
								?>
								<tr>
									<td><?= texto($row->nome)?></td>
									<td><?= $row->curso_graduacao ? texto($row->curso_graduacao) : texto($row->curso_pos)?></td>
									<td><a target="_blank" href="https://api.whatsapp.com/send?phone=55<?=$row->telefone?>"><?php echo texto($row->telefone)?></a></td>
									<td><?= $row->curso_graduacao ? texto($row->curso_graduacao) : texto($row->curso_pos)?></td>
									<td style="width: 100px" class="text-center">
										<?php
											if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][3]) ||
											   isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][5]) ||
											   isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][6])){
										?>
										<a href="marketing/prospeccao/cadastro/<?php echo $row->id_prospect?>" class="btn btn-success btn-icon rounded-circle waves-effect waves-themed mr-2"><i class="fal fa-pencil"></i></a>
										<?php
											}
										?>
										<?php
											if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][4])){
										?>
										<a href="javascript:excluir_registro('marketing.prospect', 'id_prospect', <?php echo $row->id_prospect?>)" class="btn btn-danger btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-times"></i></a>
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


        	function base(x){
        		return function produto(y){
        			return x * y;
        			console.log("asdf" + x);
        			console.log(y);
        		}
        	}

        	var f = base(2);
        	var g = base(-1);
        	

            $(document).ready(function(){
                $('#dt-basic-example').dataTable(
                {
                    responsive: true,
                    pageLength: 15,
                    stateSave: true
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
