<?php	

	#header('Content-Type: text/html; charset=utf-8');

	function nome_sagres($id_pessoa){
		require_once("/var/www/html/php/sqlsrv.php");
		
		$sql = "SELECT
					* 
				FROM
					integracao..view_integracao_usuario 
				WHERE
					id_pessoa = $id_pessoa";
		$res = mssql_query($sql);
		$row = mssql_fetch_object($res);
		return $row->nome;
	}

	function converterData($data){
		if (strstr($data, "/")){
			$A = explode ("/", $data);
			$V_data = $A[2] . "-". $A[1] . "-" . $A[0];
		} else {
			$A = explode ("-", $data);
			$V_data = $A[2] . "/". $A[1] . "/" . $A[0];
		}
		return $V_data;
	}
	function converterDataHora($data){
		$aux = explode(" ", $data);
		$data = $aux[0];
		if (strstr($data, "/")){
			$A = explode ("/", $data);
			$V_data = $A[2] . "-". $A[1] . "-" . $A[0];
		} else {
			$A = explode ("-", $data);
			$V_data = $A[2] . "/". $A[1] . "/" . $A[0];
		}
		return $V_data." ".$aux[1];
	}

	function removerPontuacao($string){
		$remover = array("/", ".", ",", "-", "_", "º");
		return str_replace($remover, '', $string);
	}

	function tratarValor($valor){
		$aux = str_replace("R$ ","",$valor);
		$aux = str_replace(".","",$aux);
		$aux = str_replace(",",".",$aux);
		return $aux;
	}

	function preparaSQL($array, $tabela, $campo = '', $registro = ''){
		$aux = $campo ? "UPDATE" : "INSERT INTO";
		$aux .= " $tabela SET ";
		$arr = [];
		foreach ($array as $key => $value) {
			if($value){
				$arr[] = "\n$key = :$key";
			}
		}
		$aux .= implode(", ", $arr);
		$aux .= $campo ? " WHERE $campo = $registro" : "";
		return $aux;
	}

	function verificarPermissao($id_menu, $tabela, $chave, $id_registro){
		if(isset($_SESSION['coopex'])){
			if($_SESSION['coopex']['usuario']['sistema']['id_tipo_usuario'] != 1){
				//VERIFICA SE O USUÁRIO POSSUI PERMISSÃO PARA ACESSO AO MENU
				!isset($_SESSION['coopex']['usuario']['permissao'][$id_menu]) ? die("Usuário sem permissão para acessar este Módulo") : null;

				//VERIFICA SE ESTÁ CADASTRANDO OU ALTERANDO
				if(!$id_registro){
					//VERIFICA SE O USUÁRIO POSSUI PERMISSÃO PARA CADASTRAR
					if(!isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][2])){
						die("Usuário sem permissão para cadastrar neste Módulo");
					}
				} else {
					//VERIFICA SE O USUÁRIO POSSUI PERMISSÃO PARA ALTERAR
					if(!isset($_SESSION['coopex']['usuario']['permissao'][$id_menu][3])){
						die("Usuário sem permissão para alterar neste Módulo");
					} else {
						include("/var/www/html/php/mysql.php");
						$sql = "SELECT
									id_registro 
								FROM
									coopex.log 
								WHERE
									id_registro = $id_registro 
									AND tabela = '$tabela' 
									AND id_pessoa = ".$_SESSION['coopex']['usuario']['id_pessoa'];
						$sql = $coopex->prepare($sql);
						$sql->execute();
						//!$sql->rowCount() ? die("Usuário não é proprietário deste registro") : null;
					}
				}
			}
		} else {
			die("Sessão não iniciada!");
		}
	}

	function gravarLog($tabela, $id_registro, $operacao, $query, $dados, $erro = ''){
		include("/var/www/html/php/mysql.php");
		$id_pessoa = $_SESSION['coopex']['usuario']['id_pessoa'];
		$sql = "INSERT INTO `coopex`.`log` ( `id_pessoa`, `tabela`, `id_registro`, `operacao`, `data_log`, `comando`, `dados`, `erro` )
				VALUES
					($id_pessoa, '$tabela', $id_registro, $operacao, now(), :sql, :dados, :erro)";
		$sql = $coopex->prepare($sql);
		$sql->bindValue(":sql", $query);
		$sql->bindValue(":dados", $dados);
		$sql->bindValue(":erro", $erro);

		$sql->execute();
		!$sql->rowCount() ? die("Usuário não é proprietário deste registro") : null;
	}


	function excluirRegistro($tabela, $chave, $id_registro){
		include("/var/www/html/php/mysql.php");

		$sql = "UPDATE $tabela SET excluido = 1 WHERE $chave = :excluir_registro";
		$stm = $coopex->prepare($sql);
		$stm->bindValue(":excluir_registro", $id_registro);

		try { //NÃO ALTERAR------------------------------------------------------------------------------------------------------------------------------------
	        $coopex->beginTransaction();
	        $stm->execute();
	        $coopex->commit();
			gravarLog($tabela, $id_registro, 3, $sql, "$chave => $id_registro");
			echo "<script>parent.exclusaoOK()</script>";
	    } catch(PDOException $e) {
	    	gravarLog($tabela, $id_registro, 3, $sql, $id_registro, $e->getMessage());
	        echo "<script>parent.exclusaoFalha()</script>";
	    }
	}

	function email($remetente, $destino, $assunto, $corpo){
		include_once 'class.phpmailer.php';
		

		$horario = substr((date("H:i:s")), 0, 2);
		if ($horario > 5 && $horario < 12){
			$msg = 'Bom dia';
		} elseif ($horario >= 12 && $horario < 19){
			$msg = 'Boa tarde';
		} else {
			$msg = 'Boa noite';	
		}

		
		$body = '
			<style>
				body{ font-family:Tahoma, Geneva, sans-serif; font-size:12px; margin:0; margin-top:10px; }
				a:link, a:visited, a:active { color: #000000; text-decoration: none; }
				a:hover { text-decoration:underline; color: #000000; }
				p{ margin-left:20px; line-height:17px; }
			</style>
		
			<a href="http://www2.fag.edu.br/coopex/">
				<div style="max-width:470px">
					<img style="width:100%" src="https://www2.fag.edu.br/coopex/img/logo.png">
				</div>
			</a>
			<br><br>
			<span>			
				'.$corpo.'			
			</span>
			
			<br><br><br>
					
			<div style="font-weigh:300;font-size:11px;color:#555555">
				Este email foi gerado automaticamente.
			</div>';
			
		$nome = 'COOPEX';
		$email = $remetente;

		$mail = new phpmailer();
		$mail->IsSMTP();
		$mail->From     = $email;
		$mail->FromName = utf8_decode($nome);
		$mail->Mailer   = "smtp";
		$mail->Host   	= "localhost";
		$mail->Subject  = utf8_decode($assunto);
		$mail->Body     = $body;
		$mail->AltBody  = $assunto;
		$mail->AddAddress($destino, $destino);
		
		$mail->Send() ? 1 : 2;
	}

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

	function registrar_bradesco($nome, $cpf, $endereco, $bairro, $cidade, $uf, $cep, $data_vencimento, $data_emissao, $valor_nominal, $seu_numero, $endereco_numero ){
		
		global $coopex;
		
	 //echo $nome.' --- '.$cpf.' --- '.$endereco.' --- '.$bairro.' --- '.$cidade.' --- '.$uf.' --- '.$cep.' --- '.$data_vencimento.' --- '.$data_emissao.' --- '.$valor_nominal.' --- '.$seu_numero.' --- '.$endereco_numero;
	
		$nome 				= ($nome);
		$endereco 			= $endereco == "" ? "Avenidas das Torres" : removerAcento($endereco);
		$cepPagador 		= substr($endereco, 0, 19);
		
		$cidade				= $cidade 	== "" ? "Cascavel" : removerAcento($cidade,"");
		$bairro 			= $bairro 	== "" ? "Loteamento FAG" : removerAcento($bairro,"");
		
		$cepPagador 		= substr($cep, 0, 5);
		$cepComplemento 	= substr($cep, -3, 3);
		$cpf_0 				= str_pad($cpf, 14, "0", STR_PAD_LEFT);

		$endereco_numero	= intval($endereco_numero);
		$endereco_numero	= "500";
		
		
		
		#Define os parâmetros a serem enviados
		$parametros_json = array(
			"nuCPFCNPJ"				=> "002203539",
			"filialCPFCNPJ"			=> "0001",
			"ctrlCPFCNPJ"			=> "73",
			"cdTipoAcesso"			=> "2",
			"clubBanco"				=> "2372269651",
			"cdTipoContrato"		=> "48",
			"nuSequenciaContrato"	=> "7960364",
			"idProduto"				=> "09",
			"nuNegociacao"			=> "353600000000001040",
			"cdBanco"				=> "237",
			"eNuSequenciaContrato" 	=> "0",
			"tpRegistro"			=> "1",
			"cdProduto"				=> "0",
			"nuTitulo"				=> "0",
			"nuCliente"				=> $seu_numero,
			"dtEmissaoTitulo"		=> $data_emissao,
			"dtVencimentoTitulo"	=> $data_vencimento,
			"tpVencimento"			=> "0",
			"vlNominalTitulo"		=> $valor_nominal,
			"cdEspecieTitulo"		=> "02",
			"nomePagador"			=> $nome,
			"logradouroPagador"		=> $endereco,
			
			"nuLogradouroPagador"   => $endereco_numero,
			
			"cepPagador"			=> $cepPagador,
			"complementoCepPagador"	=> $cepComplemento,
			"bairroPagador"			=> $bairro,
			"municipioPagador"		=> $cidade,
			"ufPagador"				=> $uf,
			"cdIndCpfcnpjPagador"	=> "1",
			"nuCpfcnpjPagador"		=> $cpf_0
			
			
		);
		#########################################################################################
		
//		echo "<pre>";
//		print_r($parametros_json);
		
		# converte o array p/ JSON
		$parametros_json = json_encode($parametros_json);
		
		#arquivo de emissao
		$assinatura = '/var/www/html/php/registro_bradesco/retorno_bradesco/assinatura/'.$data_emissao.'_'.date('H-i-s').'-'.$cpf.'.txt';
		$arquivo_emissao = '/var/www/html/php/registro_bradesco/retorno_bradesco/'.$data_emissao.'_'.date('H-i-s').'-'.$cpf.'.txt';
	    file_put_contents($arquivo_emissao, $parametros_json);

	    $certificado_pfx = file_get_contents('/var/www/html/php/boleto_bradesco/certificados/fag1.pfx');
	    if (!openssl_pkcs12_read($certificado_pfx, $result, '150773')) { throw new Exception('Nao foi possivel ler o certificado digital.'); }

		$certificado_key = openssl_x509_read($result['cert']);
		$private_key = openssl_pkey_get_private($result['pkey'], '150773');

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
	        throw new Exception('<pre>Nao foi possível registrar o boleto. ' . 'Erro: ---> ' . (curl_errno($ch)) . '.<br>' . ($info));
	    }

	    $doc = new DOMDocument();
	    $doc->loadXML($retorno);
	    $retorno = $doc->getElementsByTagName('return')->item(0)->nodeValue;
		//echo $retorno;
	    $retorno = preg_replace('/, }/i', '}', $retorno); 
	    $retorno = json_decode($retorno);

	    if (!empty($retorno->cdErro)) { throw new Exception( die('<pre>Nao foi possível registrar o boleto. --> ' . $retorno->msgErro) ); }
		
		return array(
			'mensagemErro' => $retorno->msgErro,
			'cdErro' => $retorno->cdErro,
			'linhaDigitavel' => $retorno->linhaDigitavel,
			'nosso_numero' => $retorno->nuTituloGerado,
			'seu_numero' => $seu_numero,
			'retorno_titulo_completo' => $retorno
		);
	}
	

	function avaliacao_reoferta($id_pessoa, $id_reoferta){

		require_once("/var/www/html/php/sqlsrv.php");
		include("/var/www/html/php/mysql.php");

		$dependente_disciplina = false;
		$matriculado_no_periodo = false;
		//$maximo_reofertas_periodo = false;
		$reofertas_cursadas_no_periodo = 0;
		$choque_de_horario = false;
		$choque_de_horario_tempo = 0;
		$pendencia_financeira = false;

		$permissao_para_matricula = true;

		if(isset($id_reoferta)){

			#CARREGA DADOS DA REOFERTA
			$sql = "SELECT
						DATE_FORMAT( a.pre_inscricao_data_inicial, '%d/%m/%Y' ) AS pre_inscricao_data_inicial,
						DATE_FORMAT( a.pre_inscricao_data_final, '%d/%m/%Y' ) AS pre_inscricao_data_final,
						DATE_FORMAT( a.inscricao_data_inicial, '%d/%m/%Y' ) AS inscricao_data_inicial,
						DATE_FORMAT( a.inscricao_data_final, '%d/%m/%Y' ) AS inscricao_data_final,
						departamento,
						disciplina,
						nome,
						carga_horaria,
						local,
						SPLIT_STRING ( disciplina, ' ', 1 ) AS codigo_disciplina,
						periodo_letivo,
						reoferta_minimo,
						date(now( )) BETWEEN a.pre_inscricao_data_inicial 
						AND a.pre_inscricao_data_final AS intervalo_prematricula,
						date(now( )) BETWEEN a.inscricao_data_inicial 
						AND a.inscricao_data_final AS intervalo_matricula 
					FROM
						coopex_reoferta.reoferta a
						INNER JOIN coopex_reoferta.carga_horaria USING ( id_carga_horaria )
						INNER JOIN coopex_reoferta.periodo USING ( id_periodo )
						INNER JOIN coopex.departamento USING ( id_departamento )
						LEFT JOIN coopex.pessoa ON coopex.pessoa.id_pessoa = a.id_docente 
					WHERE
						id_reoferta = ".$id_reoferta;
			$res = $coopex->query($sql);
			$reoferta = $res->fetch(PDO::FETCH_OBJ);

			$periodo_letivo = $reoferta->periodo_letivo;

			#VERIFICA SE O ACADÊMICO ESTÁ PRÉ-MATRICULADO
			$sql = "SELECT
						*,
						DATE_FORMAT( data_pre_matricula, '%d/%m/%Y' ) AS data_pre_matricula,
						DATE_FORMAT( data_pagamento, '%d/%m/%Y' )	  AS data_pagamento
					FROM
						coopex_reoferta.pre_matricula 
					WHERE
						id_reoferta = ".$id_reoferta." 
						AND id_pessoa = ".$id_pessoa;

			$pre = $coopex->query($sql);
			$pre_matriculado = $pre->rowCount() ? true : false;


			#VERIFICA SE O ACADÊMICO ESTÁ MATRICULADO
			$sql = "SELECT
						*,
						DATE_FORMAT( data_matricula, '%d/%m/%Y' ) AS data_matricula,
						DATE_FORMAT( data_pagamento, '%d/%m/%Y' )	  AS data_pagamento
					FROM
						coopex_reoferta.matricula 
					WHERE
						id_reoferta = ".$id_reoferta." 
						AND id_pessoa = ".$id_pessoa;

			$matricula = $coopex->query($sql);
			$matriculado = $matricula->rowCount() ? true : false;

			if($matriculado){
				$matricula = $matricula->fetch(PDO::FETCH_OBJ);

				#VERIFICA SE JÁ EXISTE BOLETO GERADO PARA A MATRÍCULA
				$sql = "SELECT
					data_vencimento,
					data_emissao,
					DATE_FORMAT( data_vencimento, '%d/%m/%Y' ) 		AS data_vencimento_f,
					DATE_FORMAT( data_emissao, '%d/%m/%Y' ) 		AS data_emissao_f,
					valor
				FROM
					coopex_reoferta.matricula_boleto 
				WHERE
					id_matricula = ".$matricula->id_matricula." ORDER BY data_vencimento DESC";
				$boleto_matricula = $coopex->query($sql);
				if($boleto_matricula->rowCount()){
					$row_boleto_matricula = $boleto_matricula->fetch(PDO::FETCH_OBJ);
				}
			}


			#SE ESTIVER PRÉ-MATRICULADO
			if($pre_matriculado){
				$pre = $pre->fetch(PDO::FETCH_OBJ);

				if($pre->pago){
					$permissao_para_matricula = true;
				}



				#VERFIFICA SE O ALUNO TEM REPROVAÇÃO NA DISCIPLINA OU NAS EQUIVALENTES
				$sql = "SELECT
							id_disciplina
						FROM
							coopex_reoferta.disciplina_equivalente 
						WHERE
							id_reoferta = ".$id_reoferta;
				$res = $coopex->query($sql);
				$aux = [];
				while($equivalente = $res->fetch(PDO::FETCH_ASSOC)){
					$aux[] = trim($equivalente['id_disciplina']);
				}
				$equivalente = "'".implode("','", $aux)."'";
				$codigo_disciplina = "'".$reoferta->codigo_disciplina."'";
				
				$sql = "SELECT
							* 
						FROM
							academico..view_reofertas_reprovacoes rep 
						WHERE
							id_aluno = $id_pessoa 
							AND cd_disciplina IN ($codigo_disciplina,$equivalente)";
				$res = mssql_query($sql);
				if(mssql_num_rows($res)){
					$dependente_disciplina = true;
					$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `dependente_disciplina` = 1 WHERE
									id_reoferta = ".$id_reoferta." 
									AND id_pessoa = ".$id_pessoa;
					$coopex->query($sql);
				} else {

					$sql = "SELECT
								id_pre_matricula 
							FROM
								coopex_reoferta.matricula_autorizacao 
							WHERE
								id_autorizacao = 1 
								AND id_pre_matricula = ".$pre->id_pre_matricula;
					$matricula_autorizacao = $coopex->query($sql);
					if($matricula_autorizacao->rowCount() == 0){
						$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `dependente_disciplina` = 0 WHERE
									id_reoferta = ".$id_reoferta." 
										AND id_pessoa = ".$id_pessoa;
						$coopex->query($sql);
						$permissao_para_matricula = false;
					}

					
				}
				#--VERFIFICA SE O ALUNO TEM REPROVAÇÃO NA DISCIPLINA OU NAS EQUIVALENTES

				#VERIFICA SE TEM MATRÍCULA NO PERÍODO
				$sql = "SELECT DISTINCT
							pell.pel_ds_compacta PERIODO_LETIVO,
							rca_id_aluno ID_ALUNO 
						FROM
							academico..RCA_registro_curso_aluno WITH ( NOLOCK ),
							academico..CRS_curso,
							academico..COL_colegiado,
							academico..PEL_periodo_letivo pell 
						WHERE
							col_id_colegiado = crs_id_unidade 
							AND crs_id_tp_curso IN ( '1000000002', '1000000003', '1000000004', '1100000001', '1100000002', '2100000006' ) 
							AND col_id_faculdade IN ( '1000000002', '1000000004', '1100000002' ) 
							AND crs_id_curso = rca_id_curso 
							AND rca_id_forma_saida IS NULL 
							AND rca_id_aluno = $id_pessoa 
							AND EXISTS (
							SELECT
								1 
							FROM
								academico..mtr_matricula,
								academico..STM_situacao_matricula 
							WHERE
								mtr_id_registro_curso = rca_id_registro_curso 
								AND mtr_id_situacao_matricula = stm_id_situacao 
								AND stm_st_situacao = 'C' 
							AND mtr_id_periodo_letivo = pell.pel_id_periodo_letivo 
							AND pell.pel_ds_compacta IN ( '$periodo_letivo' ))";
				$res = mssql_query($sql);
				if(mssql_num_rows($res)){
					$matriculado_no_periodo = true;
					$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `matriculado_no_periodo` = 1 WHERE
									id_reoferta = ".$id_reoferta." 
									AND id_pessoa = ".$id_pessoa;
					$coopex->query($sql);
				} else {
					$sql = "SELECT
								id_pre_matricula 
							FROM
								coopex_reoferta.matricula_autorizacao 
							WHERE
								id_autorizacao = 2 
								AND id_pre_matricula = ".$pre->id_pre_matricula;
					$matricula_autorizacao = $coopex->query($sql);
					if($matricula_autorizacao->rowCount() == 0){
						$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `matriculado_no_periodo` = 0 WHERE
									id_reoferta = ".$id_reoferta." 
									AND id_pessoa = ".$id_pessoa;
						$coopex->query($sql);
						$permissao_para_matricula = false;
					}
				}
				#--VERIFICA SE TEM MATRÍCULA NO PERÍODO


				#VERIFICA SE TEM PENDENCIA FINANCEIRA
				$sql = "SELECT
							*
						FROM
							financeiro..ttf_titulo_financeiro WITH ( NOLOCK ) 
						WHERE
							ttf_id_tipo_titulo NOT IN (1000000078,1000000075,5000000621,5000000625,1000000067,1000000066,1000000065,1000000064,1000000089,1000000088,1000000090,1000000091,1000000084,1000000352,1000000370,1000000063,1000000063,1000000073 ) 
							AND ttf_st_situacao = 'A' 
							AND ttf_id_cliente_fornecedor = $id_pessoa
 							AND ttf_dt_vencimento < getdate() - 1";
				$res = mssql_query($sql);
				if(mssql_num_rows($res)){
					$pendencia_financeira = true;
					$sql = "SELECT
								id_pre_matricula 
							FROM
								coopex_reoferta.matricula_autorizacao 
							WHERE
								id_autorizacao = 5 
								AND id_pre_matricula = ".$pre->id_pre_matricula;
					$matricula_autorizacao = $coopex->query($sql);
					if($matricula_autorizacao->rowCount() == 0){
						$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `pendencia_financeira` = 0 WHERE
									id_reoferta = ".$id_reoferta." 
									AND id_pessoa = ".$id_pessoa;
						$coopex->query($sql);
						$permissao_para_matricula = false;
					}

				} else {
					$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `pendencia_financeira` = 1 WHERE
									id_reoferta = ".$id_reoferta." 
									AND id_pessoa = ".$id_pessoa;
					$coopex->query($sql);
				}
				#--VERIFICA SE TEM MATRÍCULA NO PERÍODO


				#VERIFICA SE JÁ CURSOU MAIS DE 3 REOFERTAS NO PERÍODO LETIVO
				$sql = "SELECT
							id_matricula 
						FROM
							coopex_reoferta.matricula a
							INNER JOIN coopex_reoferta.reoferta USING ( id_reoferta )
							INNER JOIN coopex_reoferta.periodo USING ( id_periodo ) 
						WHERE
							pago = 1
							AND periodo_letivo = $periodo_letivo 
							AND a.id_pessoa = $id_pessoa";
				$res = $coopex->query($sql);
				$reofertas_cursadas_no_periodo = $res->rowCount();

				
				// echo $reofertas_cursadas_no_periodo;		

				/*if($res->rowCount()){
					$maximo_reofertas_periodo = true;
				}*/
				if($reofertas_cursadas_no_periodo<4){
					$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `reofertas_cursadas_no_periodo` = 1 WHERE
									id_reoferta = ".$id_reoferta." 
									AND id_pessoa = ".$id_pessoa;
					$coopex->query($sql);
				} else {

					$sql = "SELECT
								id_pre_matricula 
							FROM
								coopex_reoferta.matricula_autorizacao 
							WHERE
								id_autorizacao = 3 
								AND id_pre_matricula = ".$pre->id_pre_matricula;
					$matricula_autorizacao = $coopex->query($sql);
					if($matricula_autorizacao->rowCount() == 0){
						$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `reofertas_cursadas_no_periodo` = 0 WHERE
									id_reoferta = ".$id_reoferta." 
									AND id_pessoa = ".$id_pessoa;
						$permissao_para_matricula = false;
					}
				}
				#--VERIFICA SE JÁ CURSOU MAIS DE 3 REOFERTAS NO PERÍODO LETIVO





				#VERIFICA AS DATAS DE POSSÍVEIS CHOQUES DE HORÁRIO ENTRE REOFERTAS
				$tempo = [];
				$sql = "SELECT
							* 
						FROM
							coopex_reoferta.cronograma 
						WHERE
							id_reoferta = $id_reoferta 
						ORDER BY
							data_reoferta";
				$res_cronograma = $coopex->query($sql);

				if($res_cronograma->rowCount()){
					$sql = "SELECT
								cronograma.* 
							FROM
								coopex_reoferta.matricula
								INNER JOIN coopex_reoferta.cronograma USING ( id_reoferta ) 
							WHERE
								id_pessoa = $id_pessoa 
								AND id_reoferta <> $id_reoferta 
							ORDER BY
								data_reoferta";
					$res_cronograma_matricula = $coopex->query($sql);
					if($res_cronograma_matricula->rowCount()){
						
						while($cronograma = $res_cronograma->fetch(PDO::FETCH_OBJ)){
							$sql = "SELECT
										cronograma.* 
									FROM
										coopex_reoferta.matricula
										INNER JOIN coopex_reoferta.cronograma USING ( id_reoferta ) 
									WHERE
										id_pessoa = $id_pessoa 
										AND id_reoferta <> $id_reoferta
										AND data_reoferta = '$cronograma->data_reoferta'
									ORDER BY
										data_reoferta";
							$res_cronograma_matricula = $coopex->query($sql);
							
							$res2 = $coopex->query($sql);
							$data_reoferta = $res2->fetch(PDO::FETCH_OBJ);


							//print_r($data_reoferta);

							if((strtotime(@$data_reoferta->horario_inicio) >= strtotime(@$cronograma->horario_termino))){
												
							} else if((strtotime(@$data_reoferta->horario_termino) <= strtotime(@$cronograma->horario_inicio))){
								
							} else if((strtotime($data_reoferta->horario_inicio) >= strtotime($cronograma->horario_inicio)) && 
							  (strtotime($data_reoferta->horario_termino) <= strtotime($cronograma->horario_termino))){
								
								$total = gmdate('H:i:s', strtotime( $data_reoferta->horario_termino ) - strtotime($data_reoferta->horario_inicio ));
								$tempo[] = $total;
							} else if((strtotime($data_reoferta->horario_inicio) <= strtotime($cronograma->horario_inicio)) && 
							  		 (strtotime($data_reoferta->horario_termino) <= strtotime($cronograma->horario_termino))){
								
								$total = gmdate('H:i:s', strtotime( $data_reoferta->horario_termino ) - strtotime($cronograma->horario_inicio ));
								$tempo[] = $total;
							} else if((strtotime($data_reoferta->horario_inicio) <= strtotime($cronograma->horario_inicio)) && 
							  		 (strtotime($data_reoferta->horario_termino) >= strtotime($cronograma->horario_termino))){
								
								$total = gmdate('H:i:s', strtotime( $cronograma->horario_termino ) - strtotime($cronograma->horario_inicio ));
								$tempo[] = $total;
							} else if((strtotime($data_reoferta->horario_inicio) >= strtotime($cronograma->horario_inicio)) && 
							  		 (strtotime($data_reoferta->horario_termino) >= strtotime($cronograma->horario_termino))){
								
								$total = gmdate('H:i:s', strtotime( $cronograma->horario_termino ) - strtotime($data_reoferta->horario_inicio ));
								$tempo[] = $total;
							} 

						}

						print_r($tempo);

						$segundos = 0;

						foreach ( $tempo as $temp ){
							list( $h, $m, $s ) = explode( ':', $temp );

							$segundos += $h * 3600;
							$segundos += $m * 60;
							$segundos += $s;
						}
						echo "<br>Choque -> ".$choque_de_horario_tempo = $segundos * 100 / ($reoferta->carga_horaria * 60 * 60);

						if($choque_de_horario_tempo<=26){
							$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `choque_de_horario` = 1 WHERE
										id_reoferta = ".$id_reoferta." 
										AND id_pessoa = ".$id_pessoa;
							$coopex->query($sql);
						} else {
							$sql = "SELECT
										id_pre_matricula 
									FROM
										coopex_reoferta.matricula_autorizacao 
									WHERE
										id_autorizacao = 4 
										AND id_pre_matricula = ".$pre->id_pre_matricula;
							$matricula_autorizacao = $coopex->query($sql);
							if($matricula_autorizacao->rowCount() == 0){
								$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `choque_de_horario` = 0 WHERE
										id_reoferta = ".$id_reoferta." 
										AND id_pessoa = ".$id_pessoa;
								$coopex->query($sql);
								$permissao_para_matricula = false;
							}
						}

					} else {
						$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `choque_de_horario` = 1 WHERE
									id_reoferta = ".$id_reoferta." 
									AND id_pessoa = ".$id_pessoa;
						$coopex->query($sql);
					}
				}



				//echo ">".$permissao_para_matricula;
				if($permissao_para_matricula && ($pre->pago || $matricula->pago)){ 
					$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `permissao_matricula` = 1 WHERE
							id_reoferta = ".$id_reoferta." 
							AND id_pessoa = ".$id_pessoa;
					$coopex->query($sql);
				} else {
					$sql = "UPDATE `coopex_reoferta`.`pre_matricula` SET `permissao_matricula` = 0 WHERE
							id_reoferta = ".$id_reoferta." 
							AND id_pessoa = ".$id_pessoa;
					$coopex->query($sql);
					
				}
			}
		}
	}
