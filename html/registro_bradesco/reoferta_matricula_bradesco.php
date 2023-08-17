<?	
session_start();
header('Content-Type: text/html; charset=utf-8');
header("Expires: Mon, 26 Jul 1997 00:01:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");


//error_reporting(E_ALL);
//ini_set('display_errors', 1);

/*
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


*/


/*BANCO DE DADOS*/

$BD['base']   = "coopex_usuario";
$BD['user']   = "fernando";
$BD['pass']   = "jklp13SA";
$BD['server'] = "10.0.0.33";
$BD['tipo']   = "mysql";
$id = mysql_connect($BD['server'], $BD['user'], $BD['pass']);
$con = mysql_select_db($BD['base'], $id);

if(!$id){
	echo "Não foi possível estabelecer uma conexão com o gerenciador MySQL. Favor contactar o administrador.";
	exit;
}
if(!$con){
	echo "Não foi possível estabelecer uma conexão com o gerenciador MySQL. Favor contactar o administrador.";
	exit;
}
mysql_query("SET CHARACTER SET utf8");


$BD_MS['base']   = "portal3";
$BD_MS['user']   = "academicoadm";
$BD_MS['pass']   = "academicoadm";
$BD_MS['server'] = "10.0.0.150:49320";

$id3 =  mssql_connect($BD_MS['server'], $BD_MS['user'], $BD_MS['pass']);

if(!$id3){
	echo "Não foi possível estabelecer uma conexão com o gerenciador mssql. Favor contactar o administrador.";
	exit;
}

$con3 = mssql_select_db($BD_MS['base'], $id3);
	if(!$con3){
	echo "Não foi possível estabelecer uma conexão com o gerenciador mssql. Favor contactar o administrador.";
	exit;
}	

$tabela = $_SESSION['login']['campus'] == 2 ? "coopex_toledo" : "coopex_cascavel";

$sql = "SELECT
			ins.id_matricula,
			ins.id_reoferta,
			usu.nome,
			usu.cpf,
			reo.disciplina_descricao AS titulo,
			ins.pago_data_vencimento AS data_vencimento,
			reo.inscricao_data_final AS data_limite,
			ins.pago_nosso_numero AS nosso_numero,
			ins.valor,
			ins.pago AS pago,
			usu.id_usuario 
		FROM
			coopex_usuario.usuario usu
			INNER JOIN $tabela.reoferta_matricula ins ON ins.id_usuario = usu.id_usuario
			INNER JOIN $tabela.reoferta reo ON reo.id_reoferta = ins.id_reoferta 
		WHERE
			ins.id_matricula = ".$_GET['id']." ";
$res = mysql_query($sql);
$row = mysql_fetch_assoc($res);
$id_reoferta = $row['id_reoferta'];

$sql2 = "select USR_NU_IDENTIDADE as rg, USR_DS_ENDERECO as endereco, USR_DS_CIDADE as endereco_cidade, USR_DS_BAIRRO as endereco_bairro, 
				USR_SG_ESTADO as endereco_estado, (SUBSTRING(USR_NU_CEP,0,6)+'-'+SUBSTRING(USR_NU_CEP,6,3)) as endereco_cep 
			from USR_USUARIO 
			where USR_ID_PESSOA = ".$row['id_usuario']."";
$res2 = mssql_query($sql2);
$row2 = mssql_fetch_assoc($res2);



$row['rg'] = $row2['rg'];

$data_atual = date('Y-m-d');
$data_limite = $row['data_limite'];

$id_usuario = $row['id_usuario'];

$sql_pre = "SELECT
				* 
			FROM
				$tabela.reoferta_pre_inscricao 
			WHERE
				id_reoferta = $id_reoferta 
				AND id_usuario = $id_usuario
				and boleto_pago = 2";
$res_pre = mysql_query($sql_pre);
if(mysql_num_rows($res_pre)){
	$valor_pre = 0;
} else {
	$valor_pre = 55;
}



	//$nosso_numero = $row['nosso_numero'];
	//$data_vencimento = $row['data_vencimento'];
	$data_vencimento = $row['data_limite'];



//echo "<script>console.log('".$nosso_numero."')</script>";

if(mysql_num_rows($res) == 0){
	die;
}
if($row['pago'] == 2){
	echo "Este boleto já foi pago.";
	die;
}
/*
if(($data_atual > $row['data_limite']) && $_SESSION['login']['id_usuario'] != 1000167401){	
	echo "Período de inscrições encerrado.";
	die;
}
*/		

				
				$sql99 = "SELECT
							count( id_inscricao ) AS inscricao, reoferta_minimo, carga_horaria
						FROM
							$tabela.reoferta_pre_inscricao 
							INNER JOIN $tabela.reoferta using (id_reoferta)
							inner join coopex_usuario.curso on curso.id_curso = reoferta.curso
						WHERE
							id_reoferta = $id_reoferta 
							AND permissao = 1";
				$res99 = mysql_query($sql99);
				$row99 = mysql_fetch_assoc($res99);
				//print_r($row99);

				$carga_horaria = $row99['carga_horaria'];


				if($row99['inscricao'] >= $row99['reoferta_minimo']){
					$tabela_valor = $row99['reoferta_minimo'];
				} else {
					$tabela_valor = $row99['inscricao'];
				}




				if(intval($row99['inscricao']) == 1){
					$quantidade = "um";
				} else if(intval($row99['inscricao']) == 2){
					$quantidade = "dois";
				} else if(intval($row99['inscricao']) == 3){
					$quantidade = "tres";
				} else if(intval($row99['inscricao']) == 4){
					$quantidade = "quatro";
				} else if(intval($row99['inscricao']) == 5){
					$quantidade = "cinco";
				} else {
					$quantidade = "mais";
				}

				$sql99 = "select carga_horaria from $tabela.reoferta where id_reoferta = ".$id_reoferta."";
				$res99 = mysql_query($sql99);
				$row99 = mysql_fetch_assoc($res99);

				$coluna = "valor_$tabela_valor";

				$sql99 = "SELECT
							$coluna
						FROM
							$tabela.reoferta_valores 
						WHERE
							carga_horaria = $carga_horaria";
				$res99 = mysql_query($sql99);
				$row99 = mysql_fetch_assoc($res99);

				$valor_reoferta = $row99[$coluna] + $valor_pre;


				$sql_exc = "SELECT
					* 
				FROM
					$tabela.reoferta_excecao_valor_matricula 
				WHERE
					id_reoferta = $id_reoferta 
					AND id_usuario = $id_usuario";
				$res_exc = mysql_query($sql_exc);
				if(mysql_num_rows($res_exc)){
					$row_exc = mysql_fetch_assoc($res_exc);
					$valor_reoferta = $row_exc['valor'] + $valor_pre;
				}
				


include("openboleto2/autoloader.php");

#inclui o arquivo de registro via XML
include("bradesco_xml.php");

use OpenBoleto\Banco\Bradesco;
use OpenBoleto\Agente;

$sacado = new Agente($row['nome'], $row['cpf'], utf8_encode($row2['endereco']), $row2['endereco_cep'], $row2['endereco_cidade'], $row2['endereco_estado']);
$cedente = new Agente('FUNDACAO ASSIS GURGACZ', '2203539000173', 'Avenida das Torres, 500 - Loteamento FAG', '85806-095', 'Cascavel', 'PR');

//print_r($row2);

############################################<br>
# REGISTRO ONLINE

$tirar = array("/", ".", ",", "-", "_", "º");

$data_atual = date('Y-m-d');

/*if(date($row['data_vencimento']) < $data_atual){
	$data_vencimento = date('Y-m-d');
} else {
	$data_vencimento = date($row['data_vencimento']);
}
*/

//$data_vencimento = '2019-11-25';

//$row['valor'] = "932.80";

$dt_vencimento = date('d.m.Y', strtotime($data_vencimento) );
//$dt_vencimento = str_replace($tirar, '', $dt_vencimento);
$cpf_xml = str_replace($tirar, '', $row['cpf']);
$cep_xml = str_replace($tirar, '', $row2['endereco_cep']);
$valor_nominal = str_replace($tirar, '', $row['valor']);
$dt_emissao = date('d.m.Y');

//$endereco = explode("-", $end[0]);
//$endereco = $endereco[0];
$endereco = $row2['endereco'];
$endereco = substr($endereco, 0,40);
$endereco = removerAcento($endereco);

$endereco_cidade = removerAcento($row2['endereco_cidade']);
$endereco_cidade = substr($endereco_cidade, 0,20);

$endereco_numero = trim(substr($row2['endereco'], -4));

$bairro = substr($row2['endereco_bairro'], 0,30);
$bairro = removerAcento($bairro);

$bairro = $bairro ? $bairro : "Centro";

$nome_xml = substr($row['nome'], 0,40);


//$nsu = rand(0,99999999999999999);

//echo "Emissao -> ".$end[1];
#log do NSU gerado com CPF (sem digitos verificadores) + time();

$cpf_nsu = substr($cpf_xml, 0, -4).time();
echo "<script> console.log('Nsu: ".$cpf_nsu."'); </script>";


/*$nome_xml = "Vinícius Marinho";
$cpf_xml = "07625837971";
$endereco = "Rua dos Antúrios"; 
$bairro = "Guarujá";
$endereco_cidade = "Cascavel"; 
$row2['endereco_estado'] = "PR"; 
$cep_xml = "85804330";
$dt_vencimento = "28.08.2019";*/

//$valor_nominal = "122100";

//$endereco = "Rua dos Antúrios"; 



#SEU-NUMERO
#Reoferta = REO
// $prefixo = $_SESSION['login']['campus'] == 2 ? "T" : "";
// $seu_numero = $prefixo."REMb-".$row['id_matricula'];

$prefixo = $_SESSION['login']['campus'] == 2 ? "TREM" : "REMb";

$seu_numero = $prefixo."-".$row['id_matricula'];




$xml = registrar_bradesco($nome_xml, $cpf_xml, $endereco, $bairro, $endereco_cidade, $row2['endereco_estado'], $cep_xml, $dt_vencimento,
					$dt_emissao, $valor_nominal, '0', '0', '0',
					'0', '0', '0', '0', '0',
					'0', '0', $cpf_nsu, $seu_numero, $endereco_numero);


//echo("<pre>");
//print_r($xml);

#Verifica a mensagem de retorno.. se não for sucesso.. para tudo e mostra o erro na tela
if( !strstr($xml['mensagemErro'], "00000") ){
	
	//die("<h4>".$xml['mensagemErro']."</h4>");
	echo "<script> console.log('IF - Msg Erro: ".$xml['mensagemErro']."'); </script>";
	
}


$nosso_numero = $xml['nosso_numero'];
######################################################### 
#GRAVA O NOSSO-NUMERO


	//$sql3 = "insert into coopex_cascavel.reoferta_matricula_boleto (select * from coopex_cascavel.reoferta_matricula where id_matricula = ".$_GET['id'].")";
	//mysql_query($sql3);

	$sql3 = "insert into $tabela.reoferta_matricula_boleto_historico (select * from $tabela.reoferta_matricula where id_matricula = ".$_GET['id'].")";
	mysql_query($sql3);

	$sql3 = "update $tabela.reoferta_matricula set pago = 0, valor = '$valor_reoferta', pago_nosso_numero = '$nosso_numero', pago_data_geracao = now(), cadastro_data = now(), pago_data_vencimento = '$data_vencimento'  where id_matricula = ".$_GET['id'];
	mysql_query($sql3);

	$sql = "SELECT
			ins.id_matricula,
			ins.id_reoferta,
			usu.nome,
			usu.cpf,
			reo.disciplina_descricao AS titulo,
			ins.pago_data_vencimento AS data_vencimento,
			reo.inscricao_data_final AS data_limite,
			ins.pago_nosso_numero AS nosso_numero,
			ins.valor,
			ins.pago AS pago,
			usu.id_usuario 
		FROM
			coopex_usuario.usuario usu
			INNER JOIN $tabela.reoferta_matricula ins ON ins.id_usuario = usu.id_usuario
			INNER JOIN $tabela.reoferta reo ON reo.id_reoferta = ins.id_reoferta 
		WHERE
			ins.id_matricula = ".$_GET['id']." ";
$res = mysql_query($sql);
$row = mysql_fetch_assoc($res);

########################### FIM REGISTRO ################################################

echo "<script> console.log('Nosso Numero: ".$xml['nosso_numero']."'); </script>";

echo "<script> console.log('Seu Numero: ".$xml['seu_numero']."'); </script>";

echo "<script> console.log('Msg Erro: ".$xml['mensagemErro']."'); </script>";
$xml_retorno = json_encode($xml);
echo "<script> console.dir('Msg Erro: ".$xml_retorno."'); </script>";


//$row['valor'] = "932.80";

$boleto = new Bradesco(array(
    // Parâmetros obrigatórios     
	//'dataVencimento' => new DateTime($row['data_vencimento']),
	'dataVencimento' => new DateTime($data_vencimento),
    'valor' => $row['valor'], 
    //'valor' => $row['valor'], 
    'sequencial' => $nosso_numero, // Até 13 dígitos
    'sacado' => $sacado, 
    'cedente' => $cedente, 
    'agencia' => 3536, // Até 4 dígitos
    'carteira' => 9, // 101, 102 ou 201
    'conta' => 1040, // Código do cedente: Até 7 dígitos
     // IOS – Seguradoras (Se 7% informar 7. Limitado a 9%)
     // Demais clientes usar 0 (zero)
    'ios' => '0', // Apenas para o Santander

    // Parâmetros recomendáveis
    //'logoPath' => 'http://empresa.com.br/logo.jpg', // Logo da sua empresa
    'contaDv' => 5, 
    'agenciaDv' => 0,
	'descricaoDemonstrativo' => array( // Até 5
        'Boleto emitido pelo Sistema Coopex', 
        'Inscrição para '.$row['titulo'], 
		'Código de inscrição: '.@$row['id_matricula'], 
    ),
    'instrucoes' => array( // Até 8
       
        'Não receber após o vencimento.', 
		
    ),
	
	// Parâmetros opcionais
    //'resourcePath' => '../resources',
    //'moeda' => Santander::MOEDA_REAL,
    //'dataDocumento' => new DateTime(),
    //'dataProcessamento' => new DateTime(),
    //'contraApresentacao' => true,
    //'pagamentoMinimo' => 23.00,
    'aceite' => 'N', 
    'especieDoc' => 'DM', 
    'numeroDocumento' => $nosso_numero, 
	'linhaDigitavelRetorno' => $xml['linhaDigitavel'], 
	//'codigoBarrasRetorno' => $codigo_barra,
    //'usoBanco' => 'Uso banco',
    //'layout' => 'layout.phtml',
    'logoPath' => 'https://www2.fag.edu.br/coopex/img/logo_boleto.jpg', 
    //'sacadorAvalista' => new Agente('Antônio da Silva', '02.123.123/0001-11'),
    //'descontosAbatimentos' => 123.12,
    //'moraMulta' => 123.12,
    //'outrasDeducoes' => 123.12,
    //'outrosAcrescimos' => 123.12,
    //'valorCobrado' => $row['valor'],
    'valorUnitario' => $row['valor'], 
    'quantidade' => 1,));

echo $boleto->getOutput();

###########################################################
# ESCREVE O RETORNO DO XML EM UM TXT

/*$arquivo = "/portal/httpd/htdocs/registro_bradesco/registro_txt/reoferta/arquivo_dia_".date('d-m-Y').".txt";
$escritor = fopen($arquivo, 'a');

$texto = "REGISTRO: ".$xml['mensagemErro']." Nosso Número -> ".$xml['nosso_numero']." --- "." Seu Número -> ".$xml['seu_numero']." --- "."Matricula: ".$row['id_matricula']." --- "."Nome -> ".$row['nome']." --- "."CPF -> ".$cpf_xml." --- "." Evento -> ".$row['titulo']." --- XML -> ".$xml."\r\n\n";
fwrite($escritor, $texto);
fclose($escritor);*/

$arquivo = "/portal/httpd/htdocs/registro_bradesco/registro_txt/reoferta/matricula_arquivo_dia_".date('d-m-Y').".txt";
$escritor = fopen($arquivo, 'a');

$texto = "REGISTRO: ".$xml['mensagemErro']." Nosso Número -> ".$xml['nosso_numero']." --- "." Seu Número -> ".$xml['seu_numero']." --- "."Matricula: ".$row['id_matricula']." --- "."Nome -> ".$nome_xml." --- "."CPF -> ".$cpf_xml." --- "." Evento -> ".$row['titulo']." --- XML -> ".print_r($xml, TRUE)."\r\n\n";
fwrite($escritor, $texto);
fclose($escritor);





?>
