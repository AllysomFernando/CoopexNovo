<?php



//		echo $nome.'  -- '. $cpf.' --  '.$endereco.'  -- '.$bairro.'   '.
//			$endereco_cidade.' --  '.
//			$row_sqlserver['endereco_estado'].'  -- '.
//			$cep_xml.'  -- '.
//			$data_vencimento_xml.' --  '.
//			$data_emissao.' --  '.
//			$valor_nominal.'  -- '.
//			$seu_numero.' --  '.
//			$endereco_numero;

		$nome = "THAYSSA RODRIGUES";
		$cpf = "087.154.899-22";
		
		$endereco = "Av. das Torres";
		$endereco_numero = "500";
		$cep_xml = "85806-095";
		$bairro = "Bairro FAG";
		$endereco_cidade = "Cascavel";
		$estado = "PR";
		$valor_nominal = "249600";
		$seu_numero = "REMB-4093";
		$data_vencimento_xml = date("d.m.Y");
		$data_emissao = date("d.m.Y");

		
		$xml = registrar_bradesco(
			$nome, $cpf, $endereco, $bairro, $endereco_cidade, $estado, $cep_xml, $data_vencimento_xml, $data_emissao, $valor_nominal, $seu_numero, $endereco_numero
		);


	function registrar_bradesco($nome, $cpf, $endereco, $bairro, $cidade, $uf, $cep, $data_vencimento, $data_emissao, $valor_nominal, $seu_numero, $endereco_numero ){
		
		
	 //echo $nome.' --- '.$cpf.' --- '.$endereco.' --- '.$bairro.' --- '.$cidade.' --- '.$uf.' --- '.$cep.' --- '.$data_vencimento.' --- '.$data_emissao.' --- '.$valor_nominal.' --- '.$seu_numero.' --- '.$endereco_numero;
	
		$nome 				= ($nome);
		$endereco 			= $endereco == "" ? "Avenidas das Torres" : ($endereco);
		$cidade				= $cidade 	== "" ? "Cascavel" : ($cidade);
		$bairro 			= $bairro 	== "" ? "Loteamento FAG" : ($bairro);
		
		$cepPagador 		= substr($cep, 0, 5);
		$cepComplemento 	= substr($cep, -3, 3);
		$cpf_0 				= str_pad($cpf, 14, "0", STR_PAD_LEFT);

		$endereco_numero	= intval($endereco_numero);
		#$endereco_numero	= 500;
		
	 //echo $nome.' --- '.$cpf_0.' --- '.$endereco.' --- '.$bairro.' --- '.$cidade.' --- '.$uf.' --- '.$cep.' --- '.$data_vencimento.' --- '.$data_emissao.' --- '.$valor_nominal.' --- '.$seu_numero.' --- '.$endereco_numero;

		
		#Define os parâmetros a serem enviados
		$parametros_json = array(
			
			"nuCPFCNPJ" => "002203539",
			"filialCPFCNPJ" => "0001",
			"ctrlCPFCNPJ" => "73",
			"cdTipoAcesso" => "2",
			"clubBanco" => "2372269651",
			"cdTipoContrato" => "48",
			"nuSequenciaContrato" => "7960364",
			"idProduto" => "09",
			"nuNegociacao" => "353600000000001040",
			"cdBanco" => "237",
			"eNuSequenciaContrato" => "0",
			"tpRegistro" => "1",
			"cdProduto" => "0",
			"nuTitulo" => "0",
			"nuCliente" => "REMB-4093",
			"dtEmissaoTitulo" => "27.03.2020",
			"dtVencimentoTitulo" => "27.03.2020",
			"tpVencimento" => "0",
			"vlNominalTitulo" => "249600",
			"cdEspecieTitulo" => "02",
			"nomePagador" => "edina andr?a schmitt freisleben",
			"logradouroPagador" => "rua major hipolito",
			"nuLogradouroPagador" => "300",
			"cepPagador" => "85825",
			"complementoCepPagador" => "000",
			"bairroPagador" => "centro",
			"municipioPagador" => "santa tereza do oest",
			"ufPagador" => "PR",
			"cdIndCpfcnpjPagador" => "1",
			"nuCpfcnpjPagador" => "00010867912901" 
			
//			"nuCPFCNPJ"				=> "002203539",
//			"filialCPFCNPJ"			=> "0001",
//			"ctrlCPFCNPJ"			=> "73",
//			"cdTipoAcesso"			=> "2",
//			"clubBanco"				=> "2372269651",
//			"cdTipoContrato"		=> "48",
//			"nuSequenciaContrato"	=> "7960364",
//			"idProduto"				=> "09",
//			"nuNegociacao"			=> "353600000000001040",
//			"cdBanco"				=> "237",
//			"eNuSequenciaContrato" 	=> "0",
//			"tpRegistro"			=> "1",
//			"cdProduto"				=> "0",
//			"nuTitulo"				=> "0",
//			"nuCliente"				=> $seu_numero,
//			"dtEmissaoTitulo"		=> $data_emissao,
//			"dtVencimentoTitulo"	=> $data_vencimento,
//			"tpVencimento"			=> "0",
//			"vlNominalTitulo"		=> $valor_nominal,
//			"cdEspecieTitulo"		=> "02",
//			"nomePagador"			=> $nome,
//			"logradouroPagador"		=> $endereco,
//			
//			"nuLogradouroPagador"   => $endereco_numero,
//			
//			"cepPagador"			=> $cepPagador,
//			"complementoCepPagador"	=> $cepComplemento,
//			"bairroPagador"			=> $bairro,
//			"municipioPagador"		=> $cidade,
//			"ufPagador"				=> $uf,
//			"cdIndCpfcnpjPagador"	=> "1",
//			"nuCpfcnpjPagador"		=> $cpf_0
			
			
		);
		#########################################################################################
		
		echo "<pre>";
		print_r($parametros_json);
		
		# converte o array p/ JSON
		$parametros_json = json_encode($parametros_json);
		
		#arquivo de emissao
		$assinatura = '/var/www/html/php/registro_teste/assinatura/'.$data_emissao.'_'.date('H-i-s').'-'.$cpf.'.txt';
		$arquivo_emissao = '/var/www/html/php/registro_teste/'.$data_emissao.'_'.date('H-i-s').'-'.$cpf.'.txt';
	    file_put_contents($arquivo_emissao, $parametros_json);

	    $certificado_pfx = file_get_contents('/var/www/html/certificados/fag1.pfx');
	    if (!openssl_pkcs12_read($certificado_pfx, $result, '150773')) { throw new Exception('Nao foi possivel ler o certificado digital.'); }

		$certificado_key = openssl_x509_read($result['cert']);
		$private_key = openssl_pkey_get_private($result['pkey'], '150773');

		openssl_pkcs7_sign(
			$arquivo_emissao, $assinatura, $certificado_key, $private_key, [], PKCS7_BINARY | PKCS7_TEXT
		);

		$signature = file_get_contents($assinatura);
		$parts = preg_split("#\n\s*\n#Uis", $signature);
		$mensagem_assinada_base64 = $parts[1];

	 
		$ch = curl_init('https://cobranca.bradesconetempresa.b.br/ibpjregistrotitulows/registrotitulohomologacao');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $mensagem_assinada_base64);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		
		
	    $retorno = curl_exec($ch);
	    if (curl_errno($ch)) {
	        $info = curl_getinfo($ch);
	        throw new Exception('<pre>Nao foi possível registrar o boleto. ' . 'Erro: ---> ' . print_r(curl_errno($ch)) . '.<br>' . print_r($info));
	    }

	    $doc = new DOMDocument();
	    $doc->loadXML($retorno);
	    $retorno = $doc->getElementsByTagName('return')->item(0)->nodeValue;
	    $retorno = preg_replace('/, }/i', '}', $retorno); 
	    $retorno = json_decode($retorno);
		
		
		print_r($retorno);

		//echo "<br>";

		$doc = new DOMDocument();
		$doc->loadXML($retorno);
		$retorno = $doc->getElementsByTagName('return')->item(0)->nodeValue;
		$retorno = preg_replace('/, }/i', '}', $retorno); 
		$retorno = json_decode($retorno);



    if (!empty($retorno->cdErro)) {
        throw new Exception('Não foi possível registrar o boleto. ' . $retorno->msgErro);
    }
		

		echo "<pre>";
		print_r($retorno);
		echo "</pre>";

	    if (!empty($retorno->cdErro)) { throw new Exception('<pre>Nao foi possível registrar o boleto. -00--> ' . $retorno->msgErro); }
		
		return array(
			'mensagemErro' => $retorno->msgErro,
			'cdErro' => $retorno->cdErro,
			'linhaDigitavel' => $retorno->linhaDigitavel,
			'nosso_numero' => $retorno->nuTituloGerado,
			'seu_numero' => $seu_numero,
			'retorno_titulo_completo' => $retorno
		);
	}




?>