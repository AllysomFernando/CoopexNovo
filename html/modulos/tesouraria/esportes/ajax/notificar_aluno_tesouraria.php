<?php
require_once("../../../../php/config.php");
require_once("../../../../php/mysql.php");
require_once("../../../../php/sqlsrv.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_pessoa = $_GET['id_pessoa'];

//$id_pessoa = 5000246883;

$mes[1] = "Fevereiro";
$mes[2] = "Março";
$mes[3] = "Abril";
$mes[4] = "Maio";
$mes[5] = "Junho";
$mes[6] = "Julho";
$mes[7] = "Agosto";
$mes[8] = "Setembro";
$mes[9] = "Outubro";
$mes[10] = "Novembro";

$sql = "SELECT
			tel_cd_ddd AS ddd,
			tel_nu_telefone AS telefone,
			pes_nm_pessoa AS nome  
		FROM
			academico..AUE_aluno_unidade_ensino a
			INNER JOIN academico..TEL_telefone b ON a.aue_id_responsavel = tel_id_pessoa
			INNER JOIN academico..PES_pessoa c ON a.aue_id_aluno = c.pes_id_pessoa 
		WHERE
			aue_id_aluno = $id_pessoa
		ORDER BY
			tel_ch_preferencial DESC";
$res = mssql_query($sql);
$row = mssql_fetch_assoc($res);
$nome = $row['nome'];
$whats = $row['ddd'].$row['telefone'];

//$whats = "45999113888";

$sql = "SELECT
			id_matricula_boleto,
			id_matricula,
			p.id_pessoa,
			nome,
			DATE ( data_matricula ) as data_matricula,
			b.valor,
			data_vencimento,
			parcela,
			DATEDIFF(now(), data_vencimento) AS dias_atraso,
			IF
				( MONTH ( data_vencimento ) = MONTH ( now()), 'Atual', 'Anterior' ) as situacao
		FROM
			colegio.matricula m
			INNER JOIN colegio.matricula_boleto b USING ( id_matricula )
			INNER JOIN coopex.pessoa p ON m.id_pessoa = p.id_pessoa 
		WHERE
			data_vencimento < now() 
			AND pago = 0 
			AND ativo = 1
			AND b.id_pessoa = $id_pessoa 
		ORDER BY
			nome,
			parcela";
$res = $coopex->query($sql);
$tabela = "";
while($row = $res->fetch(PDO::FETCH_OBJ)){
	$tabela .= $mes[$row->parcela]." - $row->dias_atraso dias de atraso\n";
}

$texto	= "Prezado(a) Responsável por *$nome*,\nVerificamos que, até a presente data, não se encontram quitadas as mensalidades relacionadas abaixo da Escola de Esportes. Dessa forma, serve a presente para solicitá-lo(a) que providencie referida regularização financeira o mais breve possível, a fim de evitar o acréscimo de encargos e as demais cominações contratuais.\n\nCaso já tenha efetuado o pagamento, favor desconsiderar a presente mensagem.\n\n$tabela";

$url = "https://simplechat.com.br/api/send/065eb096f036b233b928b4ae9b1a6ffb";
$handle = curl_init($url);
curl_setopt($handle, CURLOPT_POST, true);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($handle, CURLOPT_POSTFIELDS, [
	'nome' => 'Teste',
	'message' => $texto,
	'celular' => "55" . $whats
	//'celular' => "554599911388"
]);
$dados = curl_exec($handle);
curl_close($handle);

$texto = utf8_decode($texto);

$dados = json_decode($dados);
if (isset($dados->success)) {

	$usuario = $_SESSION['coopex']['usuario']['id_pessoa'];
	$sql = "INSERT INTO `colegio`.`notificacao` (`data_notificacao`, `texto`, `id_pessoa`, `telefone`, `id_notificante`) VALUES (now(), '$texto', $id_pessoa, '$whats', $usuario)";

	$coopex->query($sql);

	echo 1;
} else {
	echo 0;
}
