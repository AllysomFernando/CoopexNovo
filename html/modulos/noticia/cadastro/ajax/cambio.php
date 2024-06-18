<?php

	session_start();
	
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
	$str = file_get_contents('https://p1.trrsf.com/api/top-indices', false, stream_context_create($arrContextOptions)); 
	$str = json_decode($str);

	for($i = 0; $i<count($str->ativos->ativo); $i++){
		$str->ativos->ativo[$i]->ult;
		if($str->ativos->ativo[$i]->cod == "BRL-AE"){
			$valor_atual = $str->ativos->ativo[$i]->ult;
		}
	}
		
	//echo "DÓLAR:<br>$valor_atual<br><br>";

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
		$sql = "INSERT INTO `cotacao`(`id_ativo`, `valor`, `variacao`, `data_cotacao`) VALUES (1, $valor_atual, $variacao, now())";
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

	//echo "EURO:<br>$valor_atual<br><br>";
	
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
		$sql = "INSERT INTO `cotacao`(`id_ativo`, `valor`, `variacao`, `data_cotacao`) VALUES (2, $valor_atual, $variacao, now())";
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
	//echo "IBOVESPA:<br>$valor_atual<br><br>";

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
		$sql = "INSERT INTO `cotacao`(`id_ativo`, `valor`, `variacao`, `data_cotacao`) VALUES (3, $valor_atual, $variacao, now())";
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


	/* SOJA */
	/*unset($json);
	$str = file_get_contents('https://www.noticiasagricolas.com.br/widgets/cotacoes?id=121&imagem=false', false, stream_context_create($arrContextOptions));
	$str = explode('<td>', $str);
	
	$valor = explode('</td>', $str[1]);
	$valor_atual = str_replace(",", ".", $valor[0]);

	//echo "SOJA: $valor_atual<br>";

	$variacao = explode('</td>', $str[2]);
	$variacao = str_replace(",", ".", $variacao[0]);
	$variacao = str_replace("%", "", $variacao);

	$sql = "SELECT
				valor 
			FROM
				cotacao 
			WHERE
				id_ativo = 4 
			ORDER BY
				data_cotacao DESC 
			LIMIT 1";
	$res = $coopex->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);

	if(floatval($row->valor) != floatval($valor_atual)){
		$sql = "INSERT INTO `catve_db`.`cotacao`(`id_ativo`, `valor`, `variacao`, `data_cotacao`) VALUES (4, $valor_atual, $variacao, now())";
		$coopex->query($sql);
	}*/
	/* SOJA */


	/* MILHO */
	/*unset($json);
	$str = file_get_contents('https://www.noticiasagricolas.com.br/widgets/cotacoes?id=91&imagem=false', false, stream_context_create($arrContextOptions));
	$str = explode('<td>', $str);
	
	$valor = explode('</td>', $str[1]);
	$valor_atual = str_replace(",", ".", $valor[0]);

	//echo "MILHO: $valor_atual<br>";

	$variacao = explode('</td>', $str[2]);
	$variacao = str_replace(",", ".", $variacao[0]);
	$variacao = str_replace("%", "", $variacao);

	$sql = "SELECT
				valor 
			FROM
				cotacao 
			WHERE
				id_ativo = 5 
			ORDER BY
				data_cotacao DESC 
			LIMIT 1";
	$res = $coopex->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);

	if(floatval($row->valor) != floatval($valor_atual)){
		$sql = "INSERT INTO `catve_db`.`cotacao`(`id_ativo`, `valor`, `variacao`, `data_cotacao`) VALUES (5, $valor_atual, $variacao, now())";
		$coopex->query($sql);
	}*/
	/* MILHO */


	/* TRIGO */
	/*unset($json);
	$str = file_get_contents('https://www.noticiasagricolas.com.br/widgets/cotacoes?id=211&imagem=false', false, stream_context_create($arrContextOptions));
	$str = explode('<td>', $str);
	
	$valor_atual = explode('</td>', trim($str[2]));

	$valor_atual = str_replace(".", "", $valor_atual[0]);
	$valor_atual = str_replace(",", ".", $valor_atual);

	//echo "TRIGO:<br>$valor_atual<br>";

	$variacao = explode('</td>', $str[3]);
	$variacao = str_replace(",", ".", $variacao[0]);
	$variacao = str_replace("%", "", $variacao);

	$sql = "SELECT
				valor 
			FROM
				cotacao 
			WHERE
				id_ativo = 6 
			ORDER BY
				data_cotacao DESC 
			LIMIT 1";
	$res = $coopex->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);

	if(floatval($row->valor) != floatval($valor_atual)){
		$sql = "INSERT INTO `catve_db`.`cotacao`(`id_ativo`, `valor`, `variacao`, `data_cotacao`) VALUES (6, $valor_atual, $variacao, now())";
		$coopex->query($sql);
	}*/
	/* TRIGO */

	/* BOI */
	/*unset($json);
	$str = file_get_contents('https://www.noticiasagricolas.com.br/widgets/cotacoes?id=12&imagem=false', false, stream_context_create($arrContextOptions));
	$str = explode('<td>', $str);
	
	$valor = explode('</td>', $str[1]);
	$valor_atual = floatval(str_replace(",", ".", $valor[0]));

	//echo "BOI:<br>$valor_atual<br>";

	$variacao = explode('</td>', $str[2]);
	$variacao = str_replace(",", ".", $variacao[0]);
	$variacao = str_replace("%", "", $variacao);

	$sql = "SELECT
				valor 
			FROM
				cotacao 
			WHERE
				id_ativo = 7 
			ORDER BY
				data_cotacao DESC 
			LIMIT 1";
	$res = $coopex->query($sql);
	$row = $res->fetch(PDO::FETCH_OBJ);

	if(floatval($row->valor) != floatval($valor_atual)){
		$sql = "INSERT INTO `catve_db`.`cotacao`(`id_ativo`, `valor`, `variacao`, `data_cotacao`) VALUES (7, $valor_atual, $variacao, now())";
		$coopex->query($sql);
	}*/
	/* BOI */

	$sql = "SELECT
				id_ativo 
			FROM
				ativo
			WHERE
				manual = 0";


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


	$sql = "INSERT INTO `cotacao_atualizacao`(`data_atualizacao`) VALUES (now())";
	$coopex->query($sql);


?>