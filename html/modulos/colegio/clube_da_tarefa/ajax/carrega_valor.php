<?php
require_once("../../../../php/config.php");
require_once("../../../../php/mysql.php");
require_once("../../../../php/utils.php");

$id_modalidade = implode(",", $_REQUEST['id_pessoa_matricula']);

$sql = "SELECT
			SUM( valor ) AS total,
			SUM( desconto ) AS desconto 
		FROM
			colegio.modalidade 
		WHERE
			id_modalidade IN ($id_modalidade)";
$res = $coopex->query($sql);
$row = $res->fetch(PDO::FETCH_OBJ);

if(count($_REQUEST['id_pessoa_matricula']) > 1){
	$subtotal = 70 * count($_REQUEST['id_pessoa_matricula']);
	$desconto = $subtotal - (50 * count($_REQUEST['id_pessoa_matricula']));
	$total = $subtotal - $desconto;
} else {
	$subtotal = 70;
	$desconto = 0;
	$total = 70;
}

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
