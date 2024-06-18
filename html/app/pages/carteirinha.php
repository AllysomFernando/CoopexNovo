    <div class="page-content header-clear">

    	<img src="images/pictures/ceu_r2.jpg" class="img-fluid mb-n4">
    	<div class="container">
    		<div class="avatar3 text-center">
    			<img data-menu="menu-story" src="images/pictures/faces/<?= $_SESSION['app']['aluno'][$_SESSION['app']['pessoa_ativa']]->id_pessoa ?>.jpg" width="200">
    		</div>
    	</div>

    	<h1 class="text-center font-30 pt-2 mb-0 mt-2"><?= $_SESSION['app']['aluno'][$_SESSION['app']['pessoa_ativa']]->nome ?></h1>

    	<p class="text-center font-18 mb-2"><b><?= $_SESSION['app']['aluno'][$_SESSION['app']['pessoa_ativa']]->curso ?><br><?= utf8_e($_SESSION['app']['aluno'][$_SESSION['app']['pessoa_ativa']]->serie) ?></u></span></b></p>


    	<div class="card card-style mt-2">
    		<div class="content mt-2 pt-1">
    			<div class="list-group list-custom-small ms-n1 mt-n1">

    				<a href="#" class="border-0 mb-n3">
    					<i class="fa font-16 fa-heart color-theme opacity-30"></i>
    					<span>CPF: <u class="font-800 text-decoration-none"><?= cpf($_SESSION['app']['aluno'][$_SESSION['app']['pessoa_ativa']]->cpf) ?></u></span><i class="fa"></i>
    				</a>
    				<a href="#" class="border-0 mb-n3">
    					<i class="fa font-16 fa-heart color-theme opacity-30"></i>
    					<span>RG: <u class="font-800 text-decoration-none"><?= $_SESSION['app']['aluno'][$_SESSION['app']['pessoa_ativa']]->rg ?></u></span><i class="fa"></i>
    				</a>
    				<a href="#" class="border-0 mb-n3">
    					<i class="fa font-16 fa-calendar color-theme opacity-30"></i>
    					<span>Nascimento: <u class="font-800 text-decoration-none"><?= converterData($_SESSION['app']['aluno'][$_SESSION['app']['pessoa_ativa']]->nascimento) ?></u></span>
    				</a>

    				<div class="generate-qr-result"></div>
    			</div>
    		</div>
    	</div>
    </div>

	<script type="text/javascript" src="scripts/carteirinha.js?asfsdasdasdf"></script>