<pre>
<?php

	//session_start();
	
	require_once("../../../../php/mysql.php");
	require_once("../../../../php/utils.php");

	error_reporting(E_ALL);	
	ini_set('display_errors', 1);

	$json = null;

	$arrContextOptions=array(
	    "ssl"=>array(
	        "verify_peer"=>false,
	        "verify_peer_name"=>false,
	    ),
	); 

	/* DÓLAR */
	unset($json);
	$str = file_get_contents('https://api.cotacoes.uol.com/mixed/summary?&currencies=1,11,5&itens=1,23243,1168&fields=name,openbidvalue,askvalue,variationpercentbid,price,exchangeasset,open,pctChange,date,abbreviation&jsonp=jsonp', false, stream_context_create($arrContextOptions)); 
	$str = json_decode($str);

	print_r($str);

	$valor_atual = $str->ativos->ativo[1]->ult;

	for($i = 0; $i<count($str->ativos->ativo); $i++){
		$str->ativos->ativo[$i]->ult;
		if($str->ativos->ativo[$i]->cod == "BRL-AE"){
			$valor_atual = $str->ativos->ativo[$i]->ult;
		}
	}
		
	echo "DÓLAR:<br>$valor_atual<br><br>";

	exit;

	$sql = "SELECT
				valor 
			FROM
				cotacao 
			WHERE
				id_ativo = 1 
			ORDER BY
				data_cotacao DESC 
				LIMIT 1";
	$res = $coopex->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);

	$variacao = (($valor_atual * 100) / $row->valor);
	$variacao = $variacao - 100;

	if($row->valor != $valor_atual){
		$sql = "INSERT INTO `catve_db`.`cotacao`(`id_ativo`, `valor`, `variacao`, `data_cotacao`) VALUES (1, $valor_atual, $variacao, now())";
		$coopex->query($sql);
	}
	/* DÓLAR */


	/* EURO */
	unset($json);


	for($i = 0; $i<count($str->ativos->ativo); $i++){
		$str->ativos->ativo[$i]->ult;
		if($str->ativos->ativo[$i]->cod == "AEEUCO"){
			$valor_atual = $str->ativos->ativo[$i]->ult;
		}
	}

	

	echo "EURO:<br>$valor_atual<br><br>";
	
	$sql = "SELECT
				valor 
			FROM
				cotacao 
			WHERE
				id_ativo = 2 
			ORDER BY
				data_cotacao DESC 
			LIMIT 1";
	$res = $coopex->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);

	$variacao = (($valor_atual * 100) / $row->valor);
	$variacao = $variacao - 100;

	if($row->valor != $valor_atual){
		$sql = "INSERT INTO `catve_db`.`cotacao`(`id_ativo`, `valor`, `variacao`, `data_cotacao`) VALUES (2, $valor_atual, $variacao, now())";
		$coopex->query($sql);
	}
	/* EURO */
	

	/* IBOVESPA */
	unset($json);
	
	for($i = 0; $i<count($str->ativos->ativo); $i++){
		$str->ativos->ativo[$i]->ult;
		if($str->ativos->ativo[$i]->cod == "IBOV"){
			$valor_atual = $str->ativos->ativo[$i]->ult;
		}
	}


	echo "IBOVESPA:<br>$valor_atual<br><br>";

	$sql = "SELECT
				valor 
			FROM
				cotacao 
			WHERE
				id_ativo = 3 
			ORDER BY
				data_cotacao DESC 
			LIMIT 1";
	$res = $coopex->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);

	if(floatval($row->valor) != floatval($valor_atual)){
		$sql = "INSERT INTO `catve_db`.`cotacao`(`id_ativo`, `valor`, `variacao`, `data_cotacao`) VALUES (3, $valor_atual, $variacao, now())";
		$coopex->query($sql);
	}
	/* IBOVESPA */


	$sql = "SELECT
				id_ativo 
			FROM
				ativo";


	$res = $coopex->query($sql);
	unset($json);
	while($row = $res->fetch(PDO::FETCH_OBJ)){

		$sql2 = "SELECT
					id_ativo,
					ativo,
					valor,
					variacao,
					ordem 
				FROM
					cotacao
					INNER JOIN ativo USING ( id_ativo ) 
				WHERE
					id_ativo = $row->id_ativo 
				ORDER BY
					data_cotacao DESC 
					LIMIT 1";
		$res2 = $coopex->query($sql2);
		while($row2 = $res2->fetch(PDO::FETCH_OBJ)){				
			$json[] = $row2;
		}	
	}
	$json_data = json_encode($json);
	file_put_contents("../../../../../../json/home/cotacao.json", $json_data);


	$sql = "INSERT INTO `catve_db`.`cotacao_atualizacao`(`data_atualizacao`) VALUES (now())";
	$coopex->query($sql);


?>