<?php
require_once("../../../../php/config.php");
require_once("../../../../php/mysql.php");
require_once("../../../../php/utils.php");

$id_modalidade = implode(",", $_REQUEST['id_modalidade']);

$sql = "SELECT
			SUM( valor_externo ) AS total,
			SUM( desconto_externo ) AS desconto 
		FROM
			colegio.modalidade 
		WHERE
			id_modalidade IN ($id_modalidade)";
$res = $coopex->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);

if(count($_REQUEST['id_modalidade']) > 1){
	$subtotal = $row->total;
	$desconto = $row->total - $row->desconto;
	$total = $row->total - $desconto;
	$anual = $total * 10;
} else {
	$subtotal = $row->total;
	$desconto = 0;
	$total = $row->total;
	$anual = $total * 10;
}

$total_anual = $anual - ($anual * 0.1);
$total_semestral = $anual - ($anual * 0.05);
$total_mensal = $anual;

$parcela_anual = $total_anual;
$parcela_semestral = $total_semestral / 2;
$parcela_mensal = $anual / 10;

$resultArray = [
	'subtotal' => number_format($subtotal, 2, ',', '.'),
	'desconto' => number_format($desconto, 2, ',', '.'),
	'total' => number_format($total, 2, ',', '.'),
	'anual' => number_format($anual, 2, ',', '.'),
	'total_anual' => number_format($total_anual, 2, ',', '.'),
	'total_semestral' => number_format($total_semestral, 2, ',', '.'),
	'total_mensal' => number_format($total_mensal, 2, ',', '.'),
	'parcela_anual' => number_format($parcela_anual, 2, ',', '.'),
	'parcela_semestral' => number_format($parcela_semestral, 2, ',', '.'),
	'parcela_mensal' => number_format($parcela_mensal, 2, ',', '.')
];

echo json_encode([$resultArray]);
