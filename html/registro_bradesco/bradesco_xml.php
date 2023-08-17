<?php

header('Content-Type: text/html; charset=UTF-8');
header("Expires: Mon, 26 Jul 1997 00:01:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");

	error_reporting(E_ALL);
	ini_set('display_errors', 1);


	function removerAcento($str, $enc = 'iso-8859-1'){
		
		$str = utf8_decode($str);
 
		$acentos = array(
				'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
				'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
				'C' => '/&Ccedil;/',
				'c' => '/&ccedil;/',
				'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
				'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
				'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
				'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
				'N' => '/&Ntilde;/',
				'n' => '/&ntilde;/',
				'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
				'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
				'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
				'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
				'Y' => '/&Yacute;/',
				'y' => '/&yacute;|&yuml;/',
				':' => '/&ordf;/',
				'' => '/&ordm;|&amp;/'
			);
		
		$acentos = preg_replace($acentos, array_keys($acentos), htmlentities($str,ENT_NOQUOTES, $enc));
       
	   	$acentos = str_replace(",","",$acentos);
		$acentos = str_replace("!","",$acentos);
		$acentos = str_replace("$","",$acentos);
		$acentos = str_replace(".","",$acentos);
		$acentos = str_replace(":","",$acentos);
		$acentos = str_replace(";","",$acentos);
		$acentos = str_replace("/","",$acentos);
		$acentos = str_replace("(","",$acentos);
		$acentos = str_replace(")","",$acentos);
		$acentos = str_replace("$","s",$acentos);
		$acentos = str_replace("%","",$acentos);
		$acentos = rtrim($acentos);
		//$acentos = str_replace(" ","-",$acentos);
		$acentos = str_replace("'","",$acentos);
		$acentos = str_replace('"',"",$acentos);
	
		return strtolower($acentos);
	}



$emissao = date("d.m.Y");
$vencimento = date('d.m.Y', strtotime(date("d.m.Y"). ' + 10 days'));



function registrar_bradesco( $nome, $cpf, $endereco, $bairro, $cidade, $uf, $cep, $dt_vencimento,
					 	$dt_emissao, $valor_nominal, $porcentagem_multa, $quantidade_dias_multa, $porcentagem_juro,
					 	$tipo_desconto, $valor_desconto, $data_limite_desconto, $valor_abatimento, $tipo_protesto,
					 	$quantidade_dias_protesto, $quantidade_dias_baixa, $nsu, $seu_numero, $endereco_numero ){
	
	
	$nome = removerAcento($nome);
	$endereco = removerAcento($endereco);
	$cidade = removerAcento($cidade);
	
	//echo "<script> console.log('No XML: Esta na funçao'); </script>";  
	
	//echo "convertendo -> ". $city = remover_caracter($cidade);
	
	//$city = iconv('UTF-8', 'ASCII//TRANSLIT', $city); 
	
	//echo "<script> console.log('Endereco: ".$cidade."'); </script>"; 

	
	$cpf_0 = str_pad($cpf, 14, "0", STR_PAD_LEFT);
	
	
	$cepPagador = substr($cep, 0, 5);
	$cepComplemento = substr($cep, -3, 3);
	
	//echo "END NUMERO". $endereco_numero;

	
		if( $bairro == "" ){
			$bairro = "centro";
		}

#Define os parâmetros a serem enviados
$parametros_json = array(
			"nuCPFCNPJ" 							=> "002203539",
			"filialCPFCNPJ" 						=> "0001",
			"ctrlCPFCNPJ" 							=> "73",
			"cdTipoAcesso" 							=> "2",
			"clubBanco" 							=> "2372269651",
			"cdTipoContrato" 						=> "48",
			"nuSequenciaContrato" 					=> "7960364",             // Campo utilizado para diferenciar os convenios
			"idProduto" 							=> "09",
			"nuNegociacao" 							=> "353600000000001040",
			"cdBanco" 								=> "237",
			"eNuSequenciaContrato" 					=> "0",
			"tpRegistro" 							=> "1",
			"cdProduto" 							=> "0",
			"nuTitulo" 								=> "0",
			"nuCliente" 							=> $seu_numero,
			"dtEmissaoTitulo" 						=> $dt_emissao,
			"dtVencimentoTitulo" 					=> $dt_vencimento,
			"tpVencimento" 							=> "0",
			"vlNominalTitulo" 						=> $valor_nominal,
			"cdEspecieTitulo" 						=> "02",
			"tpProtestoAutomaticoNegativacao" 		=> "00",
			"prazoProtestoAutomaticoNegativacao" 	=> "00",
			"controleParticipante" 					=> "",
			"cdPagamentoParcial" 					=> "",
			"qtdePagamentoParcial" 					=> "0",
			"percentualJuros" 						=> "0",
			"vlJuros" 								=> "0",
			"qtdeDiasJuros" 						=> "0",
			"percentualMulta" 						=> "0",
			"vlMulta" 								=> "0",
			"qtdeDiasMulta" 						=> "0",
			"percentualDesconto1" 					=> "0",
			"vlDesconto1" 							=> "0",
			"dataLimiteDesconto1" 					=> "",
			"percentualDesconto2" 					=> "0",
			"vlDesconto2" 							=> "0",
			"dataLimiteDesconto2" 					=> "",
			"percentualDesconto3" 					=> "0",
			"vlDesconto3" 							=> "0",
			"dataLimiteDesconto3" 					=> "",
			"prazoBonificacao" 						=> "0",
			"percentualBonificacao" 				=> "0",
			"vlBonificacao" 						=> "0",
			"dtLimiteBonificacao" 					=> "",
			"vlAbatimento" 							=> "0",
			"vlIOF" 								=> "0",
			"nomePagador" 							=> $nome,
			"logradouroPagador" 					=> $endereco,
			"nuLogradouroPagador" 					=> "300",  //$endereco_numero,
			"complementoLogradouroPagador" 			=> "",
			"cepPagador" 							=> $cepPagador,
			"complementoCepPagador" 				=> $cepComplemento,
			"bairroPagador" 						=> $bairro,
			"municipioPagador" 						=> $cidade,
			"ufPagador" 							=> $uf,
			"cdIndCpfcnpjPagador" 					=> "1",
			"nuCpfcnpjPagador" 						=> $cpf_0,
			"endEletronicoPagador" 					=> "", #email nao obrigat�rio
			"nomeSacadorAvalista" 					=> "",
			"logradouroSacadorAvalista" 			=> "",
			"nuLogradouroSacadorAvalista" 			=> "0",
			"complementoLogradouroSacadorAvalista" 	=> "",
			"cepSacadorAvalista" 					=> "0",
			"complementoCepSacadorAvalista" 		=> "0",
			"bairroSacadorAvalista" 				=> "",
			"municipioSacadorAvalista" 				=> "",
			"ufSacadorAvalista" 					=> "",
			"cdIndCpfcnpjSacadorAvalista" 			=> "0",
			"nuCpfcnpjSacadorAvalista" 				=> "0",
			"endEletronicoSacadorAvalista" 			=> ""
		);
#########################################################################################
	//echo "<pre>";
	//print_r($parametros_json);
	
	# converte o array p/ JSON
	$parametros_json = json_encode($parametros_json);
	
	
	#arquivo de emissao
	$assinatura = '/var/www/html/registro_bradesco/retorno_bradesco/assinatura/'.$dt_emissao.'_'.date('H-i-s').'-'.$cpf.'.txt';
	$arquivo_emissao = '/var/www/html/registro_bradesco/retorno_bradesco/'.$dt_emissao.'_'.date('H-i-s').'-'.$cpf.'.txt';
    file_put_contents($arquivo_emissao, $parametros_json);

    //$certificado_pfx = file_get_contents('/portal/httpd/htdocs/vestibular/inscricao/FUNDACAO_ASSIS_GURGACZ02203539000173_gambiarra.pfx');
    $certificado_pfx = file_get_contents('/var/www/html/php/boleto_bradesco/certificados/fag1.pfx');
    //$certificado_pfx = file_get_contents('/portal/httpd/htdocs/registro_bradesco/novo.pfx');
    if (!openssl_pkcs12_read($certificado_pfx, $result, '150773')) {
    	throw new Exception('Nao foi possivel ler o certificado digital.');
    }

   	$certificado_key = openssl_x509_read($result['cert']);

    $private_key = openssl_pkey_get_private($result['pkey'], '150773'); //

    openssl_pkcs7_sign(
    	$arquivo_emissao, $assinatura, $certificado_key, $private_key, [], PKCS7_BINARY | PKCS7_TEXT
    );
	
    $signature = file_get_contents($assinatura);
    $parts = preg_split("#\n\s*\n#Uis", $signature);
    $mensagem_assinada_base64 = $parts[1];
 
    $ch = curl_init('https://cobranca.bradesconetempresa.b.br/ibpjregistrotitulows/registrotitulo');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $mensagem_assinada_base64);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $retorno = curl_exec($ch);
    if (curl_errno($ch)) {
        $info = curl_getinfo($ch);
        throw new Exception('Nao foi possível registrar o boleto. ' . 'Erro: ---> ' . curl_errno($ch) . '.<br>' . $info);
    }

    $doc = new DOMDocument();
    $doc->loadXML($retorno);
    $retorno = $doc->getElementsByTagName('return')->item(0)->nodeValue;
    $retorno = preg_replace('/, }/i', '}', $retorno); 
    $retorno = json_decode($retorno);

    if (!empty($retorno->cdErro)) {
        throw new Exception('Nao foi possível registrar o boleto. ---> ' . $retorno->msgErro);
    }
	$tirar = array('<', '>');
	
	$barras = str_replace($tirar, '', $retorno->cdBarras);
	
	//echo "<pre>";
	return array(
					'mensagemErro' => $retorno->msgErro,

					#retorna 00 se for registrado
					'cdErro' => $retorno->cdErro,

					#titulo aceito S ou N
					//'aceito' => $rResponse->return->titulo->aceito,

					#Dados do boleto(retorno do registro)
					
					'codigoBarra' => $barras,
					'linhaDigitavel' => $retorno->linhaDigitavel,
					'nosso_numero' => $retorno->nuTituloGerado,
					'seu_numero' => $seu_numero,
					//'retorno_create' => $xmlCreate,
					//'retorno_xml_registro' => $xmlRegistro,
					//'retorno_sonda_registro' => $cResponse,
					'retorno_titulo_completo' => $retorno
				)
	;
	//echo "</pre>";
	
 }






?>