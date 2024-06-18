<footer class="page-footer " role="contentinfo">
	<div class="d-flex align-items-center flex-1 text-muted">
		<span class="hidden-md-down fw-700">2019 © Sistema Coopex by&nbsp;<span class='text-primary fw-500'>House FAG</span></span>
	</div>
	<div>
		<ul class="list-table m-0 d-none">
			<li><a href="#" class="text-secondary fw-700">Sobre</a></li>
			<li class="pl-3"><a href="#" class="text-secondary fw-700">License</a></li>
			<li class="pl-3"><a href="#" class="text-secondary fw-700">Documentation</a></li>
			<li class="pl-3 fs-xl"><a href="#" class="text-secondary"><i class="fal fa-question-circle" aria-hidden="true"></i></a></li>
		</ul>
	</div>
</footer>
<script>
	$("#form-ticket").submit(function(e) {

		e.preventDefault();

		var form = $(this);

		if ($.trim($("#tab-ticket-titulo").val()) === "" || $.trim($("#tab-ticket-description").val()) === "") {
			alert('Alguns campos obrigatórios estão vazios');
			return false;
		}

		$.ajax({
			type: "POST",
			url: "modulos/coopex/ticket/api/routes/ticket.php",
			data: form.serialize(),
			success: function(data) {
				alert("Ticket cadastrado com sucesso")
			},
			error: function(xhr, textStatus, error) {
				alert("Não foi possível registrar seu ticket")
				console.log(error)
				console.log(xhr)
			}
		});
	});
</script>