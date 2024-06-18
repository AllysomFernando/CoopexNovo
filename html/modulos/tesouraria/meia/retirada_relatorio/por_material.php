<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<link rel="stylesheet" media="screen, print" href="css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
<script src="js/core.js"></script>

<main id="js-page-content" role="main" class="page-content">
<ol class="breadcrumb page-breadcrumb">
	<li class="breadcrumb-item"><a href="javascript:void(0);">Tesouraria</a></li>
	<li class="breadcrumb-item">Colégio</li>
	<li class="breadcrumb-item active">Entrega de Materiais</li>
	<li class="position-absolute pos-top pos-right d-none d-sm-block"><code>ID: 01</code></li>
</ol>
<div class="subheader">
	<h1 class="subheader-title">
		<i class='subheader-icon fal fa-barcode-read'></i> Entrega de Materiais

	</h1>
</div>
<div class="row">
	<div class="col-xl-12">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>
					Série
				</h2>
				<div class="panel-toolbar">
					<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
					<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
					<button class="btn btn-panel" data-action="panel-close" data-toggle="tooltip" data-offset="0,10" data-original-title="Close"></button>
				</div>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<div class="form-row">
							<div class="col-md-3 mb-3">
								<label class="form-label" for="validationCustom03">Série<span class="text-danger">*</span></label>
								<?php
									$sql = "SELECT
												* 
											FROM
												tesouraria.serie
											WHERE
												ativo = 1	
											ORDER BY
												serie";

									$serie = $coopex->query($sql);
								?>
								<select  id="id_serie2" name="id_serie" class="select2 form-control" required>
									<option value="">Selecione a Série</option>
								<?php
									while($row = $serie->fetch(PDO::FETCH_OBJ)){
								?>	
									<option  value="<?php echo $row->id_serie?>"><?php echo utf8_encode($row->serie)?></option>
								<?php
									}
								?>	
								</select>
								<div class="invalid-feedback">
									Selecione a Série
								</div>
							</div>
						</div>

				</div>
			</div>
			<div class="panel-container show">
				<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
					<button onclick="exibir_relatorio()" class="btn btn-primary ml-auto" type="submit">Gerar Relatório</button>
				</div>
			</div>
			
		</div>
		
	</div>
	
</div>

</main>

<script src="js/formplugins/select2/select2.bundle.js"></script>
<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script src="js/formplugins/inputmask/inputmask.bundle.js"></script>
<script src="js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>

<script>
var id_serie;

$(document).ready(function(){
	$('.select2').select2();

	$('#id_serie2').on('select2:select', function (e) {
		data = e.params.data;
		id_serie = data.id;
		console.log(data);
	});
});

function exibir_relatorio(){
	//alert(id_serie);
	window.location.href = "tesouraria/colegio/retirada_relatorio/por_material_pdf/"+id_serie;
}

</script>