<pre>
<?php session_start();
	
	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	$sucesso = true;

	print_r(($_REQUEST['valor']));
	
	$campos = " id_registro = :id_registro,
			 	tabela = :tabela,
				id_reoferta_tipo_pagamento = :id_reoferta_tipo_pagamento,
				valor = :valor,
				id_pessoa = :id_pessoa,
				data_recebimento  = :data_recebimento,
				recebido  = :recebido,
				data_registro = :data_registro,
				data_vencimento = :data_vencimento";

	for($i=0; $i<count($_POST['valor']); $i++){
		
		if($_POST['valor'][$i]){
			if($_POST['editar'][$i]){
				$operacao = "UPDATE `coopex_cascavel`.`reoferta_recebimento` SET ";
				$condicao = " WHERE id_reoferta_recebimento = ".$_POST['editar'][$i];
			} else {
				$operacao = "INSERT INTO `coopex_cascavel`.`reoferta_recebimento` SET ";
				$condicao = "";
			}
			
			//tratamento das variáveis
			$valor = tratarValor($_POST['valor'][$i])."<br>";
			$data_registro = $_POST['data_registro'][$i] ? $_POST['data_registro'][$i] : date("Y-m-d H:i:s");
			$data_vencimento = converterData($_POST['data_vencimento'][$i]);
			
			if($_POST['recebido'][$i]){
				$recebido = 1;
				if($_POST['data_recebimento'][$i] == "0000-00-00 00:00:00"){
					$data_recebimento = date("Y-m-d H:i:s");
				} else {
					$data_recebimento = $_POST['data_recebimento'][$i];
				}
			} else {
				$recebido = 0;
				$data_recebimento = "null";
			}
			
			//echo $recebido."<br>";
			$coopex->beginTransaction();
			try {
				$sql = $operacao.$campos.$condicao;
				
				$reoferta = $coopex->prepare($sql);
				$reoferta->bindParam(':id_registro', $_POST['id_registro'][$i]);
				$reoferta->bindParam(':tabela', $_POST['tabela'][$i]);
				$reoferta->bindParam(':id_reoferta_tipo_pagamento', $_POST['id_reoferta_tipo_pagamento'][$i]);
				$reoferta->bindParam(':valor', $valor);
				$reoferta->bindParam(':id_pessoa', $_POST['id_pessoa'][$i]);
				$reoferta->bindParam(':data_registro', $data_registro);
				$reoferta->bindParam(':data_vencimento', $data_vencimento);
				$reoferta->bindParam(':data_recebimento', $data_recebimento);
				$reoferta->bindParam(':recebido', $recebido);

				echo $reoferta->execute();
				$coopex->lastInsertId();
				//echo $reoferta->debugDumpParams();
				if(!$coopex->commit()){
					$sucesso = false;
				}
				
			} catch (Exception $e) {
			  $coopex->rollBack();
			  echo "Failed: " . $e->getMessage();
			}
			echo "<br>";
		}
		
	}
	if($sucesso){
		echo "<script>console.log(window.parent.sucesso())</script>";
	}
/*
	for($i=0; $i<count($_POST['valor']); $i++){
		echo $i;
		if($_POST['valor'][$i]){
			
			if($_POST['editar'][$i]){
				$operacao = "UPDATE `coopex_cascavel`.`reoferta_recebimento` SET ";
				$condicao = " WHERE id_reoferta_recebimento = ".$_POST['editar'][$i];
			} else {
				$operacao = "INSERT INTO `coopex_cascavel`.`reoferta_recebimento` SET ";
				$condicao = "";
			}
			
			$valor = str_replace("R$ ","",$_POST['valor'][$i]);
			$valor = str_replace(".","",$valor);
			$valor = str_replace(",",".",$valor);
			
			
				//echo $_POST['valor'][$i];
			//print_r($_POST);

			$data_registro = $_POST['data_registro'][$i] ? $_POST['data_registro'][$i] : date("Y-m-d H:i:s");
			$data_vencimento = converterData($_POST['data_vencimento'][$i]);

			//$data_recebimento = $_POST['data_recebimento'][$i] ? $_POST['data_recebimento'][$i] : date("Y-m-d H:i:s");
			//$recebido = isset($_POST['recebido'][$i]) ? 1 : 0;

			if($_POST['recebido'][$i]){
				$recebido = 1;
				if($_POST['data_recebimento'][$i]){
					$data_recebimento = $_POST['data_recebimento'][$i];
				} else {
					$data_recebimento = date("Y-m-d H:i:s");
				}
			} else {
				$recebido = 0;
				$data_recebimento = "null";
			}
			//echo "asd".$recebido;
			
			
			$campos = "
				id_registro = :id_registro,
			 	tabela = :tabela,
				tipo_pagamento = :tipo_pagamento,
				valor = :valor,
				id_pessoa = :id_pessoa,
				data_recebimento  = :data_recebimento,
				recebido  = :recebido,
				data_registro = :data_registro,
				data_vencimento = :data_vencimento";
			
			try {
				$sql = $operacao.$campos.$condicao;
				$reoferta = $coopex->prepare($sql);
				$reoferta->bindParam(':id_registro', $_POST['id_registro'][$i]);
				$reoferta->bindParam(':tabela', $_POST['tabela'][$i]);
				$reoferta->bindParam(':tipo_pagamento', $_POST['tipo_pagamento'][$i]);
				$reoferta->bindParam(':valor', $valor);
				$reoferta->bindParam(':id_pessoa', $_POST['id_pessoa'][$i]);
				$reoferta->bindParam(':data_registro', $data_registro);
				$reoferta->bindParam(':data_vencimento', $data_vencimento);
				$reoferta->bindParam(':data_recebimento', $data_recebimento);
				$reoferta->bindParam(':recebido', $recebido);

				$reoferta->execute();
				//echo $reoferta->debugDumpParams();
				$coopex->commit();
				
			} catch (Exception $e) {
			  $coopex->rollBack();
			  echo "Failed: " . $e->getMessage();
			}
		}
	}
*/
	

	/*$campo = $_GET['tabela'] == "reoferta_matricula" ? "id_matricula" : "id_inscricao";
	$sql = "SELECT
				* 
			FROM
				reoferta
				INNER JOIN ".$_GET['tabela']." USING ( id_reoferta ) 
			WHERE
				$campo = ?";
	$reoferta = $coopex->prepare($sql);
	$reoferta->bindParam(1, $_GET['id_registro']);
	$reoferta->execute();
	$reoferta = $reoferta->fetch(PDO::FETCH_OBJ);

	
*/
?>