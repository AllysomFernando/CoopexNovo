<link rel="stylesheet" media="screen, print" href="css/formplugins/bootstrap-daterangepicker/bootstrap-daterangepicker.css">
<main id="js-page-content" role="main" class="page-content naoimprimir">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Financeiro</a></li>
		<li class="breadcrumb-item">Relatórios</li>
		<li class="breadcrumb-item active">Boletos Pagos Bradesco</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title">
			<i class='subheader-icon fal fa-credit-card-front'></i> BOLETOS PAGOS BRADESCO<sup class='badge badge-primary fw-500'>NOVO</sup>
			<small>
				Relatório de baixas do arquivo de retorno do Bradesco
			</small>
		</h1>
	</div>
	
	<div class="row">
		<div class="col-xl-12">
			<div id="panel-1" class="panel">
				<div class="panel-hdr">
					<h2>
						Período
					</h2>
					<div class="panel-toolbar">
						<button class="btn btn-panel" data-action="panel-collapse" data-toggle="tooltip" data-offset="0,10" data-original-title="Collapse"></button>
						<button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip" data-offset="0,10" data-original-title="Fullscreen"></button>
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<div class="panel-tag">
							Clique na data inicial e na data final para selecionar o período, clique sobre a data duas vezes para selecionar uma única data
						</div>

						<form class="form-group row" method="post" action="" >
							<div class="col-4">
								<label class="col-form-label form-label">Selecione o período</label>
								<div class="input-group">
									<input name="intervalo" type="text" class="form-control" placeholder="Selecionar data" id="datepicker-2">
									<div class="input-group-append">
										<span class="input-group-text fs-xl">
											<i class="fal fa-calendar"></i>
										</span>
									</div>
								</div>
								<button type="button" onClick="carregar_relatorio()" class="btn btn-primary waves-effect waves-themed mt-5">Gerar Relatório</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
	function carregar_relatorio(){
		var periodo = $("#datepicker-2").val();
		$("#resultado_relatorio").load("modulos/financeiro/relatorio/reofertas_por_curso_ajax.php?periodo="+encodeURI(periodo), function() {
			$.scrollTo('#resultado_relatorio_c', 1000, {easing:'easeOutQuart'});
		});
		
		/*$('html, body').animate({
			scrollTop: $("#resultado_relatorio").offset().top
		}, 1000,{easing:'elasout'});*/
		
		

	}
	</script>
	<div id="resultado_relatorio_c">
		<div id="resultado_relatorio" class="mt-3">
		</div>
	</div>
	

<script src="js/dependency/moment/moment.js"></script>
<script src="js/formplugins/bootstrap-daterangepicker/bootstrap-daterangepicker.js"></script>
<script>
	$(document).ready(function(){
		
		$('#datepicker-2, #datepicker-modal-3').daterangepicker({
			startDate: moment().startOf('hour'),
			endDate: moment().startOf('hour').add(32, 'hour'),
			locale:
			{
				format: 'DD/MM/YYYY'
			}
		});
		
		

		$(function(){

			var start = moment().subtract(29, 'days');
			var end = moment();

			function cb(start, end)
			{
				$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
			}

			$('#datepicker-4').daterangepicker(
			{
				startDate: start,
				endDate: end,
				ranges:
				{
					'Today': [moment(), moment()],
					'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
					'Last 7 Days': [moment().subtract(6, 'days'), moment()],
					'Last 30 Days': [moment().subtract(29, 'days'), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
				}
			}, cb);

			cb(start, end);

		});
		
		

	});

</script>

</main>