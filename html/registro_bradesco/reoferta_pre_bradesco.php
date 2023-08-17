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

$sql = "
		select ins.id_inscricao, ins.id_reoferta, usu.nome, usu.cpf, reo.disciplina_descricao as titulo, ins.boleto_data_vencimento as data_vencimento,
			   reo.pre_inscricao_data_final as data_limite, ins.boleto_nosso_numero as nosso_numero, 55 as valor, ins.boleto_pago as pago, usu.id_usuario
		  from coopex_usuario.usuario usu 
	inner join $tabela.reoferta_pre_inscricao ins on ins.id_usuario = usu.id_usuario
	inner join $tabela.reoferta reo on reo.id_reoferta = ins.id_reoferta
		 where ins.id_inscricao = ".$_GET['id']." ";

$res = mysql_query($sql);
$row = mysql_fetch_assoc($res);

$sql2 = "select USR_NU_IDENTIDADE as rg, USR_DS_ENDERECO as endereco, USR_DS_CIDADE as endereco_cidade, 
				USR_SG_ESTADO as endereco_estado, (SUBSTRING(USR_NU_CEP,0,6)+'-'+SUBSTRING(USR_NU_CEP,6,3)) as endereco_cep 
			from USR_USUARIO 
			where USR_ID_PESSOA = ".$row['id_usuario']."";
$res2 = mssql_query($sql2);
$row2 = mssql_fetch_assoc($res2);

$row['rg'] = $row2['rg'];

//
$data_atual = date('Y-m-d');

$data_limite = $row['data_limite'];
$data_limite = $data_atual;

//echo $data_limite;


//$nosso_numero = $nosso_numero.modulo_11($nosso_numero);

//echo "<script>console.log('".$nosso_numero."')</script>";

if(mysql_num_rows($res) == 0){
	die;
}
if($row['pago'] == 2){
	echo "Este boleto já foi pago.";
	die;
}




//include "../vestibular/inscricao/openboleto/autoloader.php";
include "openboleto2/autoloader.php";

#inclui o arquivo de registro via XML
include("bradesco_xml.php");


use OpenBoleto\Banco\Bradesco;
use OpenBoleto\Agente;

$sacado = new Agente($row['nome'], $row['cpf'], utf8_encode($row2['endereco']), $row2['endereco_cep'], $row2['endereco_cidade'], $row2['endereco_estado']);
$cedente = new Agente('FUNDACAO ASSIS GURGACZ', '2203539000173', 'Avenida das Torres, 500 - Loteamento FAG', '85806-095', 'Cascavel', 'PR');


############################################<br>
# REGISTRO ONLINE

$tirar = array("/", ".", ",", "-", "_", "º");

$data_atual = date('Y-m-d');

/*
if(date($row['data_vencimento']) < $data_atual){
	$data_vencimento = date('Y-m-d');
} else {
	$data_vencimento = date($row['data_vencimento']);
}*/

$data_vencimento = $data_limite;
$data_vencimento = $data_atual;

$dt_vencimento = date('d.m.Y', strtotime($data_vencimento) );
//$dt_vencimento = str_replace($tirar, '', $dt_vencimento);
$cpf_xml = str_replace($tirar, '', $row['cpf']);
$cep_xml = $row2['endereco_cep'];

$valor_nominal = str_replace($tirar, '', $row['valor']);
$valor_nominal = number_format($valor_nominal, 2, '','');

$dt_emissao = date('d.m.Y');

//$endereco = explode("-", $end[0]);
//$endereco = $endereco[0];
$endereco = "Av. das Torres, 500";
$endereco = substr($endereco, 0,-4);
$endereco = removerAcento($endereco);

$endereco_numero = trim(substr($row2['endereco'], -4));

//echo $numero;

$endereco_cidade = removerAcento($row2['endereco_cidade']);
$endereco_cidade = substr($endereco_cidade, 0,20);

//print_r($row2);

@$bairro = substr($row2['endereco_bairro'], 0,30);
@$bairro = removerAcento($bairro);

$bairro = $bairro ? $bairro : "Centro";

$nome_xml = substr($row['nome'], 0,40);


//$nsu = rand(0,99999999999999999);

//echo "Emissao -> ".$end[1];
#log do NSU gerado com CPF (sem digitos verificadores) + time();

$cpf_nsu = substr($cpf_xml, 0, -4).time();
echo "<script> console.log('Nsu: ".$cpf_nsu."'); </script>";


#SEU-NUMERO
#Reoferta = REO
// $seu_numero = "REPb-".$row['id_inscricao'];
$prefixo = $_SESSION['login']['campus'] == 2 ? "TREP" : "REPb";

$seu_numero = $prefixo."-".$row['id_inscricao'];

//$valor_nominal = '120000'; # só teste

$xml = registrar_bradesco($nome_xml, $cpf_xml, $endereco, $bairro, $endereco_cidade, $row2['endereco_estado'], $cep_xml, $dt_vencimento,
					$dt_emissao, $valor_nominal, '0', '0', '0',
					'0', '0', '0', '0', '0',
					'0', '0', $cpf_nsu, $seu_numero, $endereco_numero);



//echo("<pre>");
//print_r($xml);

$nosso_numero = $xml['nosso_numero']; //
#########################################################
#GRAVA O NOSSO-NUMERO


	//$sql3 = "insert into coopex_cascavel.reoferta_pre_inscricao_boleto (select * from coopex_cascavel.reoferta_pre_inscricao where id_inscricao = ".$_GET['id'].")";
	//mysql_query($sql3);

	$sql3 = "insert into $tabela.reoferta_pre_inscricao_boleto_historico (select * from coopex_cascavel.reoferta_pre_inscricao where id_inscricao = ".$_GET['id'].")";
	mysql_query($sql3);

	$sql3 = "update $tabela.reoferta_pre_inscricao set boleto_pago = 0, boleto_nosso_numero = ".$xml['nosso_numero'].", boleto_data_geracao = now(), data_registro = now(), boleto_data_vencimento = '$data_limite'  where id_inscricao = ".$_GET['id'];
	mysql_query($sql3);


########################### FIM REGISTRO ################################################

	echo "<script> console.log('Valor Nominal: ".$valor_nominal."'); </script>";

	echo "<script> console.log('Nosso Numero: ".$xml['nosso_numero']."'); </script>";

	echo "<script> console.log('Seu Numero: ".$xml['seu_numero']."'); </script>";

	echo "<script> console.log('Codigo Barra: ".$xml['codigoBarra']."'); </script>";

	echo "<script> console.log('Msg Erro: ".$xml['mensagemErro']."'); </script>";

	$xml_retorno = json_encode($xml);

	echo "<script> console.log('Msg Erro: ".$xml_retorno."'); </script>";


#Verifica a mensagem de retorno.. se não for sucesso.. para tudo e mostra o erro na tela
if( $xml['cdErro'] != "0" ){
	
	die("<h4>".$xml['mensagemErro']."</h4>");
	echo "<script> console.log('IF - Msg Erro: ".$xml['mensagemErro']."'); </script>";
	
}


function converterCodigoBarras($codigoBarras){  #teste
	
	# converte o codigo de barras do retorno do Bradesco que vem em formato abaixo
	# NWnnwnNnWwwnnNWnWwNnnwNWnnnWWnnnWWnNnWwnwnWNnNNwwnWNwnnnwWNnNwnWnwwNNnnwNWnnnWWnnnWWnNnWnwnnWWnNnwwNnnWwNnnWWn
	
	/*
	----linhas
	E = estreita
	L = larga
	----------
	N = EL = 01
	W = LL = 11
	n = EE = 00
	w = LE = 10
	
	*/
	
	$codigos = array('N', 'W', 'n', 'w');
	
	$valores = array('01', '11', '00', '10');
	
	$codigo_pronto = str_replace($codigos, $valores, $codigoBarras);
	
	
	echo $codigo_pronto;
	return $codigo_pronto;
	
}



//$codigo_barra = converterCodigoBarras($xml['codigoBarra']);




$boleto = new Bradesco(array(
    // Parâmetros obrigatórios     
	//'dataVencimento' => new DateTime($row['data_vencimento']),
	'dataVencimento' => new DateTime($data_vencimento),
    'valor' => $row['valor'], 
    //'valor' => 1200, 
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
		'Código de inscrição: '.$row['id_inscricao'], 
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


$arquivo = "/portal/httpd/htdocs/registro_bradesco/registro_txt/reoferta/pre-matricula_arquivo_dia_".date('d-m-Y').".txt";
$escritor = fopen($arquivo, 'a');

$texto = "REGISTRO: ".$xml['mensagemErro']." Nosso Número -> ".$xml['nosso_numero']." --- "." Seu Número -> ".$xml['seu_numero']." --- "."Matricula: ".$row['id_inscricao']." --- "."Nome -> ".$row['nome']." --- "."CPF -> ".$cpf_xml." --- "." Evento -> ".$row['titulo']." --- XML -> ".print_r($xml, TRUE)."\r\n\n";
fwrite($escritor, $texto);
fclose($escritor);







?>
