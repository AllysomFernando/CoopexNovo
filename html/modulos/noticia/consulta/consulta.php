<?php
	$id_menu = 6;

	$sql = "SELECT
				id_noticia,
				n.titulo,
				editoria,
				b.titulo as blog,
				n.data_cadastro,
				date( n.data_cadastro ) AS data_cadastro,
				time( n.data_cadastro ) AS hora_cadastro,
				publicado,
				nome
			FROM
				noticia_2022 n
				LEFT JOIN editoria USING ( id_editoria )
				LEFT JOIN blog b USING ( id_blog ) 
				LEFT JOIN pessoa USING (id_pessoa)
			WHERE
				n.excluido = 0 
			AND galeria  = 0	
			ORDER BY
				id_noticia DESC 
				LIMIT 300";
	$reoferta = $coopex->query($sql);

	//include "modulos/noticia/cadastro/ajax/cambio.php";

?>

<iframe class="d-none" name="dados" src=""></iframe>
<form id="form_excluir_registro" method="post" target="dados" action="modulos/noticia/cadastro/cadastro_dados.php">
	<input type="hidden" id="excluir_registro" name="excluir_registro">
</form>	
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>

<script type="text/javascript">
	$.ajax({
      type: "POST",
      url: "modulos/noticia/cadastro/ajax/cambio.php",
      cache: false,
      contentType: false,
      processData: false,
      success: function(url) {
        console.log("atualizou");
      }
    });
</script>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Notícias</a></li>
		<li class="breadcrumb-item active">Consulta</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title col-6">
			<i class='subheader-icon fal fa-repeat'></i> Notícias
			<small>
				Gerenciamento de Notícias
			</small>
		</h1>
		<?php
			//if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][2])){
		?>
		<div class="subheader-title col-6 text-right">
			<a href="noticia/cadastro">
				<button type="button" class="btn btn-lg btn-primary waves-effect waves-themed">
					<span class="ni ni-plus mr-3"></span>
					Cadastrar Notícia
				</button>
			</a>
		</div>
		<?php
			//}
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
									<th>Titulo</th>
									<th>Editoria/Blog</th>
									<th>Data</th>
									<th>Hora</th>
									<th>Pessoa</th>
									<th>Publicado</th>
									<th class="text-center">Ações</th>
								</tr>
							</thead>
							<tbody>
								<?php
									while($row = $reoferta->fetch(PDO::FETCH_OBJ)){
								?>
								
								<tr>
									
									<td class="pointer">
										<a style="color: #000" href="noticia/cadastro/<?php echo $row->id_noticia?>">
											<?php echo texto(utf8_decode($row->titulo))?>
										</a>
									</td>

									<td class=""><?= $row->editoria ? $row->editoria : $row->blog?></td>
									<td><span style="display: none;"><?php echo ($row->data_cadastro)?></span><?php echo converterData($row->data_cadastro)?></td>
									<td><?php echo $row->hora_cadastro?></td>
									<td><?php echo $row->nome?></td>
									<td><span ><?php echo $row->publicado == 1 ? "Sim" : "Não";?></span></td>
									<td style="width: 150px" class="text-center">
										<?php
											if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][3]) ||
											   isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][5]) ||
											   isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][6])){
										?>
											<a href="noticia/cadastro/<?php echo $row->id_noticia?>" class="btn btn-success btn-icon rounded-circle waves-effect waves-themed mr-2"><i class="fal fa-pencil"></i></a>
										<?php
											}
										
											if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][4])){
										?>
											<a href="javascript:excluir_registro('noticia_2022', 'id_noticia', <?php echo $row->id_noticia?>)" class="btn btn-danger btn-icon rounded-circle waves-effect waves-themed"><i class="fal fa-times"></i></a>
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
                    stateSave: false,
                    order: [
                        [2, 'desc']
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
