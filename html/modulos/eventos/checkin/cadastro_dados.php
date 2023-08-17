<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;900&display=swap" rel="stylesheet">
<img width="100%" style="bottom: 0; position: absolute; left: 0;" src="https://www4.fag.edu.br/ecci/assets/images/palestrantes.jpg?1234">
<?
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
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
			echo "<h2>CONSIDERE-SE CHECADO</h2>";	
		} else {
			echo "<h2>FALHA NO CHECKIN</h2>";
		}
		
	} else {
		echo "<h2>FALHA NO CHECKIN</h2>";
	}
?>
<pre>
	<style type="text/css">
	body{
		background: rgb(9,9,9);
background: linear-gradient(180deg, rgba(9,9,9,1) 0%, rgba(1,7,75,1) 100%);
		overflow: hidden;
		margin: 0;
	}
	*{
		/*color: #fff;*/
		font-family: 'Roboto', sans-serif;
		text-align: center;
		color: #fff;
	}
	.foto{
		width: 500px;
		height: 500px;
		background-image: url(<?=$str?>);
		background-size: cover;
		background-position: center;
		border-radius: 500px;
		position: absolute;
		margin-left: -250px;
		left:  50%;
		top: 100px;
	}
	h1{
		position: absolute;
		top: 700px;
		width: 100%;
		text-align: center;
		font-size: 100px;
	}
	h2{
		position: absolute;
		top: 1100px;
		width: 100%;
		text-align: center;
		font-size: 50px;
	}
</style>
<script>parent.cadastroOk()</script>