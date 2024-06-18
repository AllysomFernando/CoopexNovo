<?php session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../../../../php/mysql.php");
require_once("../../../../php/utils.php");

$id_pessoa = $_GET['id_usuario'];

?>

<?php
#Dados pessoais

$sql = "SELECT p.id_pessoa,
u.id_usuario,
u.ra,
u.nome,
u.cpf,
u.email,
c.curso,
t.tipo,
u.usuario
FROM coopex_usuario.usuario u
LEFT JOIN coopex_usuario.evento_pessoa p ON p.id_usuario = u.id_usuario
INNER JOIN coopex_usuario.usuario_tipo t ON u.tipo = t.id_tipo
LEFT JOIN coopex_usuario.curso c ON u.curso = c.id_curso
WHERE u.id_usuario = $id_pessoa";
$res = $coopex_antigo->query($sql);
$pessoa = $res->fetch(PDO::FETCH_OBJ);

?>

<h1>Dados Pessoais</h1>
<ul class="list-group">
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="badge badge-primary badge-pill">ID Pessoa</span>
    <?php echo $pessoa->id_pessoa ? $pessoa->id_pessoa : "..."; ?>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="badge badge-primary badge-pill">ID Usuario</span>
    <?php echo $pessoa->id_usuario ? $pessoa->id_usuario : "..." ?>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="badge badge-primary badge-pill">RA</span>
    <?php echo $pessoa->ra ? $pessoa->ra : "..."; ?>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="badge badge-primary badge-pill">CPF</span>
    <?php echo $pessoa->cpf ? $pessoa->cpf : "..."; ?>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="badge badge-primary badge-pill">Email</span>
    <?php echo $pessoa->email ? $pessoa->email : "..." ; ?>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="badge badge-primary badge-pill">Curso</span>
   <?php echo $pessoa->curso ? $pessoa->curso : "..."; ?>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="badge badge-primary badge-pill">Tipo</span>
    <?php echo $pessoa->tipo ? $pessoa->tipo : "..."; ?>
  </li>
  <li class="list-group-item d-flex justify-content-between align-items-center">
    <span class="badge badge-primary badge-pill">Usuario de login</span>
    <?php echo $pessoa->usuario ? $pessoa->usuario : "..."; ?>
  </li>
</ul>

<hr>

<?
#inscritos em eventos
$sql = "SELECT
				id_inscricao,
				id_projeto,
				id_evento,
				e.titulo,
				pago,
				camiseta_tamanho,
				valor,
				p.carga_horaria,
				liberacao_projeto,
				liberacao_coopex,
				previsao_orcamentaria,
				YEAR(cadastro_data) as ano
			FROM
				coopex_usuario.evento_inscricao
				INNER JOIN coopex_usuario.evento_projeto e USING ( id_evento )
				INNER JOIN coopex_cascavel.projeto p USING ( id_projeto )
				INNER JOIN coopex_usuario.evento_pessoa USING ( id_pessoa )
				LEFT JOIN coopex_usuario.usuario USING ( id_usuario ) 
			WHERE
				id_usuario = $id_pessoa
			ORDER BY
				data_inscricao";
$res = $coopex_antigo->query($sql);
?>
<h1>Eventos</h1>
<table class="dt-basic-example table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
	<thead class="bg-primary-600">
		<tr>
			<th>#</th>
			<th>Inscrito em Eventos</th>
			<th>Pagamento</th>
			<th>Valor</th>
			<th>CH</th>
			<th>Ano</th>
			<th>Coord</th>
			<th>Coop</th>
		</tr>
	</thead>
	<tbody>
		<?php
		while ($row = $res->fetch(PDO::FETCH_OBJ)) {
			?>
			<tr>
				<td class="text-center">
					<a href="https://www2.fag.edu.br/coopex/projeto/<?= $row->previsao_orcamentaria ? "com_previsao" : "sem_previsao" ?>/<?= $row->id_projeto ?>"
						target="_blank">
						<i class="fal fa-<?= $row->previsao_orcamentaria ? "dollar-sign" : "tag" ?>"></i>
					</a>
				</td>
				<td>
					<a href="https://www2.fag.edu.br/coopex/evento/<?= $row->id_evento ?>" target="_blank">
						<strong>
							<?= utf8_encode($row->titulo) ?>
						</strong>
					</a>
				</td>
				<td class="text-center">
					<a href='<?= $row->pago == 1 ? "https://www4.fag.edu.br/inscricao/pdf/comprovante/$row->id_inscricao" : "https://www2.fag.edu.br/coopex/pagamento/boleto/$row->id_inscricao" ?>'
						target="_blank"><span class="badge badge-<?php echo $row->pago == 1 ? 'success' : 'danger' ?> badge-pill">
							<?php echo $row->pago ? 'Pago' : 'Em aberto' ?>
						</span></a>
				</td>
				<td class="text-center">
					<?= number_format($row->valor, 2, ',', '.'); ?>
				</td>
				<td class="text-center"><strong>
						<?= $row->carga_horaria ?>
					</strong></td>
				<td class="text-center"><strong>
						<?= $row->ano ?>
					</strong></td>
				<td class="text-center"><strong><i
							class="color-<?= $row->liberacao_projeto ? "success" : "danger" ?>-800 fa-2x fal fa-<?= $row->liberacao_projeto ? "check" : "times" ?>-circle"></i></strong>
				</td>
				<td class="text-center"><strong><i
							class="color-<?= $row->liberacao_coopex ? "success" : "danger" ?>-800 fa-2x fal fa-<?= $row->liberacao_coopex ? "check" : "times" ?>-circle"></i></strong>
				</td>
			</tr>

		<?
		}
		?>
	</tbody>
</table>

<hr>

<?
#participantes não inscritos
$sql = "SELECT
			id_projeto,
			titulo,
			a.carga_horaria,
			emissao_certificado,
			previsao_orcamentaria,
			YEAR(b.cadastro_data) AS ano
		FROM
		coopex_usuario.evento_nao_inscrito a
		INNER JOIN coopex_cascavel.projeto b USING ( id_projeto ) 
		WHERE
			id_usuario = $id_pessoa
		GROUP BY
			id_projeto";
$res = $coopex_antigo->query($sql);
?>
<h1>Projetos</h1>
<table class="dt-basic-example table table-bordered table-hover table-striped w-100 dataTable dtr-inline mt-5">
	<thead class="bg-primary-600">
		<tr>
			<th>#</th>
			<th>Evento</th>
			<th>CH</th>
			<th>Ano</th>
			<th>Certi</th>
		</tr>
	</thead>
	<tbody>
		<?php
		while ($row = $res->fetch(PDO::FETCH_OBJ)) {
			?>
			<tr>
				<td class="text-center">
					<a href="https://www2.fag.edu.br/coopex/projeto/<?= $row->previsao_orcamentaria ? "com_previsao" : "sem_previsao" ?>/<?= $row->id_projeto ?>"
						target="_blank">
						<i class="fal fa-<?= $row->previsao_orcamentaria ? "dollar-sign" : "tag" ?>"></i>
					</a>
				</td>
				<td>
					<a href="https://www2.fag.edu.br/coopex/projeto/<?= $row->previsao_orcamentaria ? "com_previsao" : "sem_previsao" ?>/<?= $row->id_projeto ?>"
						target="_blank">
						<strong>
							<?php echo utf8_encode($row->titulo) ?>
						</strong>
					</a>
				</td>
				<td class="text-center"><strong>
						<?php echo $row->carga_horaria ?>
					</strong></td>
				<td class="text-center"><strong>
						<?php echo $row->ano ?>
					</strong></td>
				<td class="text-center"><strong><i
							class="color-<?= $row->emissao_certificado ? "success" : "danger" ?>-800 fa-2x fal fa-<?= $row->emissao_certificado ? "check" : "times" ?>-circle"></i></strong>
				</td>
			</tr>

		<?
		}
		?>
	</tbody>
</table>

<hr>

<?
#producao cientifica
$sql = "SELECT
			id_producao,
			titulo,
			funcao,
			year(periodo_inicial) as ano
		FROM
			coopex_cascavel.producao
		INNER JOIN coopex_cascavel.producao_equipe USING ( id_producao )
		INNER JOIN coopex_cascavel.funcao USING ( id_funcao ) 
		WHERE
			id_usuario IN ($id_pessoa)";
$res = $coopex_antigo->query($sql);
?>
<h1>Produção Científica</h1>
<table class="dt-basic-example table table-bordered table-hover table-striped w-100 dataTable dtr-inline mt-5">
	<thead class="bg-primary-600">
		<tr>
			<th>Titulo</th>
			<th>Função</th>
			<th>Ano</th>
		</tr>
	</thead>
	<tbody>
		<?php
		while ($row = $res->fetch(PDO::FETCH_OBJ)) {
			?>
			<tr>
				<td>
					<a href="https://www2.fag.edu.br/coopex/producao_cientifica/<?= $row->id_producao ?>" target="_blank">
						<strong>
							<?php echo utf8_encode($row->titulo) ?>
						</strong>
					</a>
				</td>
				<td class="text-center"><strong>
						<?php echo $row->funcao ?>
					</strong></td>
				<td class="text-center"><strong>
						<?php echo $row->ano ?>
					</strong></td>
			</tr>
		<?
		}
		?>
	</tbody>
</table>
<hr>

<?
#certificados avulsos
$sql = "SELECT
			id_certificado,
			titulo,
			YEAR(cadastro_data) as ano
		FROM
			certificados 
		WHERE
			REPLACE ( REPLACE ( cpf, '.', '' ), '-', '' ) IN (
			SELECT
				cpf 
			FROM
				usuario 
			WHERE
				id_usuario = $id_pessoa 
		)";
$res = $coopex_antigo->query($sql);
?>
<h1>Certificados Avulsos</h1>
<table class="dt-basic-example table table-bordered table-hover table-striped w-100 dataTable dtr-inline mt-5">
	<thead class="bg-primary-600">
		<tr>
			<th>Titulo</th>
			<th>Ano</th>
		</tr>
	</thead>
	<tbody>
		<?php
		while ($row = $res->fetch(PDO::FETCH_OBJ)) {
			?>
			<tr>
				<td>
					<a href="https://www2.fag.edu.br/coopex/certificado/gerado/<?= $row->id_certificado ?>" target="_blank">
						<strong>
							<?php echo utf8_encode($row->titulo) ?>
						</strong>
					</a>
				</td>
				<td class="text-center"><strong>
						<?php echo $row->ano ?>
					</strong></td>
			</tr>

		<?
		}
		?>
	</tbody>
</table>
<hr>

<?
#monitoria
$sql = "SELECT
			disciplina,
			carga_horaria,
			YEAR(cadastro_data) AS ano,
			emissao_certificado
		FROM
			coopex_cascavel.monitoria
		INNER JOIN coopex_cascavel.monitoria_monitor USING ( id_monitoria ) 
		WHERE
			academico = $id_pessoa";
$res = $coopex_antigo->query($sql);
?>
<h1>Monitorias</h1>
<table class="dt-basic-example table table-bordered table-hover table-striped w-100 dataTable dtr-inline mt-5">
	<thead class="bg-primary-600">
		<tr>
			<th>Titulo</th>
			<th>CH</th>
			<th>Situação</th>
			<th>Ano</th>
			<th>Certi</th>
		</tr>
	</thead>
	<tbody>
		<?php
		while ($row = $res->fetch(PDO::FETCH_OBJ)) {
			?>
			<tr>
				<td>
					<a href="https://www2.fag.edu.br/coopex/certificado/gerado/<?= $row->id_certificado ?>" target="_blank">
						<strong>
							<?php echo utf8_encode($row->disciplina) ?>
						</strong>
					</a>
				</td>
				<td class="text-center"><strong>
						<?php echo $row->carga_horaria ?>
					</strong></td>
				<td class="text-center"><strong>
						<?php echo $row->situacao ?>
					</strong></td>
				<td class="text-center"><strong>
						<?php echo $row->ano ?>
					</strong></td>
				<td class="text-center"><strong>
						<?php echo $row->emissao_certificado ?>
					</strong></td>
			</tr>

		<?
		}
		?>
	</tbody>
</table>
<hr>


<?
$sql = "SELECT
				* 
			FROM
				coopex_reoferta.matricula_boleto
				INNER JOIN coopex_reoferta.matricula m USING ( id_matricula )
				INNER JOIN coopex_reoferta.reoferta USING ( id_reoferta ) 
			WHERE
				m.id_pessoa = $id_pessoa";
$matricula = $coopex->query($sql);

$sql = "SELECT
				* 
			FROM
				coopex_reoferta.pre_matricula_boleto
				INNER JOIN coopex_reoferta.pre_matricula m USING ( id_pre_matricula )
				INNER JOIN coopex_reoferta.reoferta USING ( id_reoferta ) 
			WHERE
				m.id_pessoa = $id_pessoa";
$prematricula = $coopex->query($sql);




?>
<h1>Reofertas</h1>
<table id="dt-basic-example" class="table table-bordered table-hover table-striped w-100 dataTable dtr-inline">
	<thead class="bg-primary-600">
		<tr>
			<th>Name</th>
			<th>Disciplina</th>
			<th>Vencimento</th>
			<th>Valor</th>
			<th>Situa&ccedil;&atilde;o</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
		while ($row = $matricula->fetch(PDO::FETCH_OBJ)) {
			?>
			<tr>
				<td><strong>Matr&iacute;cula</strong></td>
				<td><strong>
						<?php echo utf8_encode($row->disciplina) ?>
					</strong></td>
				<td>
					<?php echo converterData($row->data_vencimento) ?>
				</td>
				<td>
					<?php echo number_format($row->valor, 2, ',', '.'); ?>
				</td>
				<td class="text-center"><span class="badge badge-<?php echo $row->pago ? 'success' : 'danger' ?> badge-pill">
						<?php echo $row->pago ? 'Pago' : 'Em aberto' ?>
					</span></td>
			</tr>
			<?php
		}

		while ($row = $prematricula->fetch(PDO::FETCH_OBJ)) {
			?>
			<tr>
				<td><strong>Pr&eacute;-Matr&iacute;cula</strong></td>
				<td><strong>
						<?php echo utf8_encode($row->disciplina) ?>
					</strong></td>
				<td>
					<?php echo converterData($row->data_vencimento) ?>
				</td>
				<td>
					<?php echo number_format($row->valor, 2, ',', '.'); ?>
				</td>
				<td class="text-center"><span class="badge badge-<?php echo $row->pago ? 'success' : 'danger' ?> badge-pill">
						<?= $row->pago ? 'Pago' : 'Em aberto' ?>
					</span></td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>

<style>
	table tr td {
		vertical-align: middle !important;
	}
</style>
<script>
	$(document).ready(function () {
		$('.dt-basic-example').dataTable({
			responsive: true,
			pageLength: 99
		});

		$(".botao_receber_matricula").click(function () {
			pagamentos($(this).attr('value'), 'reoferta_matricula');
		});
		$(".botao_receber_prematricula").click(function () {
			pagamentos($(this).attr('value'), 'reoferta_pre_inscricao');
		});

		function pagamentos(id, tabela) {
			$("#pagamentos_modal_conteudo").load("modulos/tesouraria/receber/ajax/titulos_em_aberto_valores.php?id_registro=" + id + "&tabela=" + tabela);
		}

		$(":input").inputmask();

		$('.data').datepicker({
			todayHighlight: true,
			orientation: "bottom left",
			locale: "pt-BR"
		});
	});
</script>