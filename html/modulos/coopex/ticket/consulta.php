<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("api/repository/TicketRepository.php");
require_once("api/controllers/TicketController.php");
require_once("partials/ticket-badge.php");
require_once __DIR__ . "/../../../php/services/Mailer.php";

$id_menu = 119;

$isAdmin = $_SESSION['coopex']['usuario']['sistema']['tipo_usuario'] == "ADMINISTRADOR";
$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];
$mailer = new Mailer();
$repository = new TicketRepository($coopex);
$controller = new TicketController($repository, $mailer);

if ($isAdmin) {
	$tickets = $controller->getAllTickets();
} else {
	$tickets = $controller->getAllTicketsByUserId($id_pessoa);
}


?>

<iframe class="d-none" name="dados" src=""></iframe>
<form id="form_excluir_registro" method="post" target="dados" action="modulos/pos/projeto/cadastro_dados.php">
	<input type="hidden" id="excluir_registro" name="excluir_registro">
</form>
<link rel="stylesheet" media="screen, print" href="css/notifications/sweetalert2/sweetalert2.bundle.css">
<script src="js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<link rel="stylesheet" media="screen, print" href="css/datagrid/datatables/datatables.bundle.css">
<main id="js-page-content" role="main" class="page-content">
	<ol class="breadcrumb page-breadcrumb">
		<li class="breadcrumb-item"><a href="javascript:void(0);">Tickets</a></li>
		<li class="breadcrumb-item active">Consulta</li>
		<li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
	</ol>
	<div class="subheader">
		<h1 class="subheader-title col-6">
			<i class='subheader-icon fal fa-ticket-alt'></i> Meus Tickets
			<small>
				Controle de atendimento e suporte ao usuário
			</small>
		</h1>
		<div class="subheader-title col-6 text-right">
			<a href="coopex/ticket/cadastro">
				<button type="button" class="btn btn-lg btn-primary waves-effect waves-themed">
					<span class="ni ni-plus mr-3"></span>
					Enviar novo ticket
				</button>
			</a>
		</div>
	</div>
	<div class="row">
		<div class="col-xl-12">
			<div id="panel-1" class="panel">
				<div class="panel-container show">
					<div class="panel-content">

						<div class="col-xl-12">
							<div class="border-faded bg-faded p-3 mb-g d-flex">
								<input type="text" id="js-filter-tickets" name="filter-contacts" class="form-control shadow-inset-2 form-control-lg" placeholder="Filtrar tickets">
							</div>
						</div>
						<?php if (count($tickets) > 0) { ?>
							<!--  -->
							<div id="ticket-contacts" class="row">
								<?php
								foreach ($tickets as $ticket) {
									$badge = getTicketPanelBadge($ticket->status)
								?>
									<div class="col-sm-6 mb-g" style="height: 300px;">
										<div id="c_1" class="card border shadow-0 shadow-sm-hover h-100" data-filter-tags="<?php echo strtolower($ticket->titulo) . ' ' .  strtolower($badge->message) ?>">
											<div class="card-body border-faded border-top-0 border-left-0 border-right-0 rounded-top">
												<div class="d-flex flex-row align-items-center flex-nowrap">
													<div class="info-card-text flex-1 w-100 d-flex flex-row align-items-center flex-nowrap">
														<h3 class="color-primary-400 w-100">
															<strong><?php echo $ticket->titulo ?></strong>
														</h3>
														<span class="w-auto mr-3"><?php echo date_format(date_create($ticket->data_envio), 'd/m/Y') ?></span>
														<h3>
															<span class="badge <?php echo $badge->class_name ?> fw-400 l-h-n ml-2">
																<?php echo $badge->message ?>
															</span>
														</h3>
													</div>
												</div>
											</div>
											<div class="card-body p-0 collapse show">
												<div class="p-3">
													<p class="flex-grow h-100 flex-1"><?php echo $ticket->descricao ?></p>
												</div>
											</div>
											<div class="card-footer d-flex flex-row">
												<a href="coopex/ticket/atendimento/<?php echo $ticket->id ?>" class="btn btn-icon btn-primary w-100" title="Atendimento">
													<i class="fal fa-comment-alt"></i> Atendimento
												</a>
											</div>
										</div>
									</div>
								<?php } ?>

							</div>
							<!--  -->
						<?php } else { ?>
							<h2>Nenhum ticket encontrado</h2>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>

</main>

<script src="js/datagrid/datatables/datatables.bundle.js"></script>
<script>
	$(document).ready(function() {

		$('.dt-basic-example').dataTable({
			responsive: true,
			"aaSorting": []
		});

		initApp.listFilter($('#ticket-contacts'), $('#js-filter-tickets'));
	});

	function exclusaoOK() {
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

	function exclusaoFalha() {
		Swal.fire({
			type: "error",
			title: "Falha ao excluir registro",
			showConfirmButton: true
		});
	}
</script>
</body>

</html>