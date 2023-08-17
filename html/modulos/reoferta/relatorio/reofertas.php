<?php
	$id_menu = 22;
?>
<link rel="stylesheet" media="screen, print" href="css/formplugins/select2/select2.bundle.css">
<main id="js-page-content" role="main" class="page-content naoimprimir">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Reofertas</a></li>
		<li class="breadcrumb-item">Relatórios</li>
		<li class="breadcrumb-item active">Período</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-credit-card-front'></i> Reofertas
			<small>
				Relatório de Reofertas
			</small>
		</h1>
	</div>
	
	<div class="row">
		<div class="col-xl-12">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Relatório
					</h2>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<div class="form-row">
							<div class="col-md-4 mb-3">
								<label class="form-label" for="validationCustom03">Curso <span class="text-danger">*</span></label>
								<?php

									if(isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][5]) || isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][6])){
										$where = " WHERE graduacao = 1 ";
									} else {
										$where = " WHERE graduacao = 1 AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
									}

									$sql = "SELECT
												id_departamento,
												departamento 
											FROM
												coopex.departamento
												INNER JOIN coopex.departamento_pessoa USING ( id_departamento ) 
												$where 
											GROUP BY
												id_departamento 
											ORDER BY
												departamento";

									$curso = $coopex->query($sql);
								?>
								<select id="id_curso" class="select2 form-control" required="">
									<option value="">Selecione o Curso</option>
								<?php
									while($row = $curso->fetch(PDO::FETCH_OBJ)){
										$selecionado = '';
										if($dados->id_departamento == $row->id_departamento){
											$selecionado = 'selected=""';
										}
								?>
									<option <?php echo isset($dados->id_departamento) ? $selecionado : ""?> value="<?php echo $row->id_departamento?>"><?php echo utf8_encode($row->departamento)?></option>
								<?php
									}
								?>	
								</select>
								<div class="invalid-feedback">
									Selecione o curso
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-3 mb-3">
								<label class="form-label" for="validationCustom03">Período da Reoferta<span class="text-danger">*</span></label>
								<?php
									$sql = "SELECT
												id_periodo,
												periodo,
												ativo,
												DATE_FORMAT( pre_inscricao_data_inicial, '%d/%m/%Y' ) AS pre_inscricao_data_inicial,
												DATE_FORMAT( pre_inscricao_data_final, '%d/%m/%Y' ) AS pre_inscricao_data_final,
												DATE_FORMAT( inscricao_data_inicial, '%d/%m/%Y' ) AS inscricao_data_inicial,
												DATE_FORMAT( inscricao_data_final, '%d/%m/%Y' ) AS inscricao_data_final 
											FROM
												coopex_reoferta.periodo 
											ORDER BY
												id_periodo DESC";

									$periodo = $coopex->query($sql);
								?>
								<select id="id_periodo" class="select2 form-control" required="">
									<option value="">Selecione o Período</option>
								<?php
									while($row = $periodo->fetch(PDO::FETCH_OBJ)){
										$selecionado = '';
										if($dados->id_periodo == $row->id_periodo){
											$selecionado = 'selected=""';
										}
								?>	
									<option <?php echo isset($dados->id_periodo) ? $selecionado : ""?> value="<?php echo $row->id_periodo?>"><?php echo $row->periodo?> <?php echo !$row->ativo ? "" : "(Atual)"?></option>
								<?php
									}
								?>	
								</select>
								<div class="invalid-feedback">
									Selecione o período da reoferta
								</div>
							</div>
						</div>
						<button onclick="carregar_relatorio()" class="btn btn-primary ml-auto" type="submit">Gerar Relatório</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<div id="resultado_relatorio_c">
	<div id="resultado_relatorio" class="mt-3"></div>
</div>

<script src="js/formplugins/select2/select2.bundle.js"></script>

<script>

	function carregar_relatorio(){
		var id_curso = $("#id_curso").val();
		var id_periodo = $("#id_periodo").val();
		$("#resultado_relatorio").load("modulos/reoferta/relatorio/reofertas_ajax.php?id_curso="+id_curso+"&id_periodo="+id_periodo, function() {
			$.scrollTo('#resultado_relatorio_c', 1000, {easing:'easeOutQuart'});
		});
	}

	$(document).ready(function(){
		$('.select2').select2();
	});

</script>