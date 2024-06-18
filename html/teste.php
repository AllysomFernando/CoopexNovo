<?
$url = "https://simplechat.com.br/api/send/065eb096f036b233b928b4ae9b1a6ffb";
$handle = curl_init($url);

$teste =
curl_setopt($handle, CURLOPT_POST, true);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($handle, CURLOPT_POSTFIELDS, [
  'nome' => 'Tesouraria',
  'message' => 'teste envio',
  'celular' => '554599161429',
  'anexo' => 'https://coopex.fag.edu.br/arquivos/colegio/sports/camiseta.pdf',
  'extensao' => '.pdf'
]);
$dados = curl_exec($handle);
print_r($dados);
curl_close($handle);
  