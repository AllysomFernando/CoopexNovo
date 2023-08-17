<?php 
	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	$aux = explode(" - ", $_POST['intervalo']);
	if(count($aux)){
		$data_inicio = converterData($aux[0]);
		$data_fim = converterData($aux[1]);
	} else {
		$data_inicio = converterData($aux[0]);
		$data_fim = converterData($aux[0]);
	}
	
?>
<link rel="stylesheet" media="screen, print" href="css/page-invoice.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Tesouraria</a></li>
		<li class="breadcrumb-item">Recebimentos</li>
		<li class="breadcrumb-item active">Recibo</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title col-sm-6">
			<i class='subheader-icon fal fa-print'></i> NAP
			<small>
				Relatório Curso NAP
			</small>
			<button onClick="window.print();" id="bt_imprimir" type="button" class="btn btn-info mt-3"><span class="fal fa-print mr-1"></span>Imprimir Relatório</button>
		</h1>
	</div>
	<div class="container">
		<div data-size="A4">
			<div class="row">
				<div class="col-sm-12">
					<div class="d-flex align-items-center mb-5">
						<h2 class="keep-print-font fw-500 mb-0 text-primary flex-1 position-relative">
							Sistema Coopex
							<small class="text-muted mb-0 fs-xs">
								Relatório de Receitas NAP
							</small>
							<!-- barcode demo only -->
						</h2>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6 ">
					<div class="table-responsive">
						<table class="table table-clean table-sm align-self-end">
							<tbody>
								
								<tr>
									<td colspan="2"><strong>GERADO EM:</strong><?php echo date("d/m/Y H:i:s")?></td>
								</tr>
								<tr>
									<td>
										<strong>POR:</strong> <?php echo $_SESSION['coopex']['usuario']['nome']?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<?php
				$sql = "SELECT
							titulo, id_evento, sum(evento_inscricao.valor) as total
						FROM
							coopex_usuario.evento_projeto
							INNER JOIN coopex_usuario.evento_inscricao USING ( id_evento )
						WHERE
							id_projeto = 1 
							AND pago = 1 
						GROUP BY
							titulo";
				$aux_nap = $coopex->prepare($sql);
				$aux_nap->execute();

				while($nap = $aux_nap->fetch(PDO::FETCH_OBJ)){
					$sql = "SELECT
									evento,
									nosso_numero,
									data_pagamento,
									valor_pago,
									nome,
									id_inscricao  
								FROM
									coopex_usuario.evento_projeto
									INNER JOIN coopex_usuario.evento_inscricao USING ( id_evento )
									INNER JOIN coopex_usuario.evento_pessoa USING ( id_pessoa )
									INNER JOIN coopex_usuario.evento_inscricao_boleto_pagamento USING ( id_inscricao ) 
								WHERE
									id_projeto = 1 
									AND id_evento = ? 
									AND evento_inscricao_boleto_pagamento.data_pagamento BETWEEN '$data_inicio' AND '$data_fim'";
					$aux_boleto = $coopex->prepare($sql);
					$aux_boleto->bindParam(1, $nap->id_evento);
					$aux_boleto->execute();
					$boleto_total = 0;
			?>
			<div class="mt-3">
				<h1><?php echo utf8_encode(mb_strtoupper($nap->titulo))?></h1>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="table-responsive">
						<table  class="table">
							<tbody>
								<thead>
							  <tr>
								<th>ID</td>
								<th>Nome</td>
								<th>Boleto</td>
								<th>Pagamento</td>
								<th class="text-right">Valor</td>
							  </tr>
								</thead>
						<?
							while($nap_boleto = $aux_boleto->fetch(PDO::FETCH_OBJ)){
								$boleto_total +=$nap_boleto->valor_pago; 
						?>
							  <tr>
								<td><?php echo $nap_boleto->evento."-".$nap_boleto->id_inscricao?></td>
								<td><?php echo utf8_encode($nap_boleto->nome)?></td>
								<td><?php echo $nap_boleto->nosso_numero?></td>
								<td><?php echo converterData($nap_boleto->data_pagamento)?></td>
								<td align="right"><?php echo number_format($nap_boleto->valor_pago, 2, ',', '.')?></td>
							  </tr>
						<?
							}
						?>
							</tbody>
						  </table>
						<table class="table">
							<thead>
								<tr>
									<th class="border-top-0 table-scale-border-bottom fw-700">Forma de Pagamento</th>
									<th class="text-right border-top-0 table-scale-border-bottom fw-700">Valor</th>
								</tr>
							</thead>
							<tbody>
								
								
								<tr>
								  <td colspan="2" class="text-left strong">
									  
			</td>
							  </tr>
								<tr>
									<td class="text-left strong">Boleto</td>
									<td class="text-right"><?php echo isset($boleto_total) ? number_format($boleto_total, 2, ',', '.') : "0,00"?></td>
								</tr>
								
								<?php
									$sql = "SELECT
												titulo,
												id_evento,
												sum( evento_inscricao.valor ) as total
											FROM
												coopex_usuario.evento_projeto
												INNER JOIN coopex_usuario.evento_inscricao USING ( id_evento )
												INNER JOIN coopex_usuario.evento_inscricao_cartao USING ( id_inscricao ) 
											WHERE
												id_projeto = 1 
												AND erro = 0 
												and id_evento = ?
											GROUP BY
												id_evento";
									$aux = $coopex->prepare($sql);
									$aux->bindParam(1, $nap->id_evento);
									$aux->execute();
									$cartao = $aux->fetch(PDO::FETCH_OBJ);
									$cartao_total = isset($cartao->total) ? $cartao->total : 0;
								?>
								<tr>
									<td class="text-left strong">Cartão de Crédito</td>
									<td class="text-right"><?php echo isset($cartao->total) ? number_format($cartao->total, 2, ',', '.') : "0,00"?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4 ml-sm-auto">
					<table class="table table-clean">
						<tbody>
							<tr class="table-scale-border-top border-left-0 border-right-0 border-bottom-0">
								<td class="text-left">
									<strong>Valor Total</strong>
								</td>
								<td class="text-right"><strong><?php echo number_format($boleto_total + $cartao_total, 2, ',', '.')?></strong></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<?
				}
			?>

		</div>
	</div>
</main>