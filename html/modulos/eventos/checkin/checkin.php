<?php session_start();
	require_once("../../../php/mysql.php");
	$_SESSION['coopex']['checkin']['id_evento'] = 2569;

	if(isset($_POST)){
		/*ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);*/
		
		$id_registro = $_POST['ra'];
		
		require_once("../../../php/config.php");
		require_once("../../../php/mysql.php");
		require_once("../../../php/utils.php");
		require_once("../../../php/sqlsrv.php");

		
		$sql = "SELECT
					id_pessoa 
				FROM
					integracao..view_integracao_usuario 
				WHERE
					ra = '$id_registro'";
		$res = mssql_query($sql);
		if(mssql_num_rows($res)){
			$row = mssql_fetch_object($res);
			$chave = $row->id_pessoa;	
		} else {
			$chave = $id_registro;
		}

		$sql = "SELECT
					image 
				FROM
					fagid360.user_images
				WHERE
					id_user = $chave";
		$imagem = $fagid360->query($sql);
		$imagem->rowCount();
		$imagem = $imagem->fetch(PDO::FETCH_OBJ);

		$str = substr($imagem->image, 0,4) == "data" ? $imagem->image : "data:image/jpeg;base64,$imagem->image";

		$sql = "SELECT
					pago, p.nome 
				FROM
					coopex_usuario.evento_inscricao
					INNER JOIN coopex_usuario.evento_pessoa p USING ( id_pessoa )
					LEFT JOIN coopex_usuario.usuario USING ( id_usuario ) 
				WHERE
					( p.cpf = '$chave' OR id_inscricao = '$chave' OR id_usuario = '$chave' ) 
					AND id_evento = ".$_SESSION['coopex']['checkin']['id_evento'];
		$pessoa = $coopex_antigo->query($sql);
		if($pessoa->rowCount()){

			$dados = $pessoa->fetch(PDO::FETCH_OBJ);

			if($dados->pago == 1){
				echo "<div class='foto'></div><h1>$dados->nome</h1>";
				echo "<h1>CHECKIN REALIZADO COM SUCESSO</h1>";	
			} else {
				echo "<h1>FALHA NO CHECKIN</h1>";
			}
			
		} else {
			echo "<h1>FALHA NO CHECKIN</h1>";
		}
	}

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;900&display=swap" rel="stylesheet">
<script type="text/javascript">
	
	function cadastroOk(){
		$("#ra").val("");
		console.log("entrou");
		$("#ra").focus();
	}
	function cadastroFalha(){
		$("#ra").val("");
		console.log("entrou");
		$("#ra").focus();
	}

	$( document ).ready(function() {
	    cadastroOk();
	});

	$("*").click(function() {
	    cadastroOk();
	});

</script>
<h1>I Seminário de Pesquisa em Psicologia e I Jornada da Clínica Escola de Psicologia da FAG: Produção em Psicologia como ciência critica.</h1>
<iframe src="" name="dados"></iframe>

<style type="text/css">
	html, body{
		font-family: 'Roboto', sans-serif;
		width: 100%;
		height: 100%;
		margin: 0;
		overflow: hidden;
		/*background-color: #330065;*/
		color: #000;
	}
	iframe{
		width: 100%;
		height: 100%;
	}
	form{
		position: fixed;
		top: 1000px;
	}
	h1{
		text-align: center;
		padding: 50px;
		font-size: 40px;
	}
</style>

<form method="post" action="cadastro_dados.php">
	<input id="ra" type="text" name="ra" value="1000095486">
</form>
