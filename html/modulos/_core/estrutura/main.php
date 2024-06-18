<?php
$isAdmin = $_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] == "1";
?>

<main id="js-page-content" role="main" class="page-content">
    <div class="subheader">
        <h1 class="subheader-title">
            <i class='fal fa-info-circle'></i> Coopex
            <small>
                Novo Sistema Coopex
            </small>
        </h1>
    </div>
    <div class="fs-lg fw-300 p-5 bg-white border-faded rounded mb-g">
        <h2>Aviso</h2>
        <p class="mb-g">
            Esta é a nova versão do Sistema Coopex, todos os módulos do sistema antigo serão migrados gradativamente para esta nova versão. O sistema está em fase de desenvolvimento e testes, portanto, é muito importante a sua contribuição e seu feedback para construirmos um sistema ainda melhor.<br><br>
            Caso esteja enfrentando problemas, envie um ticket relatando o seu problema. Você pode enviar um ticket <a href="https://coopex.fag.edu.br/coopex/ticket/consulta">clicando aqui</a> ou clicando no ícone <button type="button" class="btn btn-sm btn-default"><i class="fal fa-bug"></i></button> no canto superior direito.
        </p>
    </div>

    <?php if ($isAdmin) { ?>
        <div class="fs-lg fw-300 p-5 bg-white border-faded rounded mb-g">
            <h2>Acesso Rápido</h2>
            <p>
                Clique em um dos botões para navegar para a página.<br><br>
                <a href="https://coopex.fag.edu.br/coopex/ticket/consulta"><button type="button" class="btn btn-lg btn-default"><i class="fal fa-ticket-alt"></i> Tickets</button></a>
                <a href="https://coopex.fag.edu.br/coopex/raiox"><button type="button" class="btn btn-lg btn-default"><i class="fal fa-wrench"></i> Raio-X</button></a>
                <a href="https://coopex.fag.edu.br/coopex/acesso"><button type="button" class="btn btn-lg btn-default"><i class="fal fa-unlock-alt"></i> Liberação de Acesso</button></a>
            </p>
        </div>

    <? } ?>
</main>