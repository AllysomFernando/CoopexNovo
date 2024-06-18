<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Sao_Paulo');

$uri = $_SERVER['REQUEST_URI'];
$url = "https://coopex.fag.edu.br/app/";

$titulo = "Colégio FAG - APP";

function utf8_e($str){
    return utf8_encode($str);
}

function primeiro_nome($nome){
    $str = explode(" ", $nome);
    return utf8_e($str[0]);
}

function cpf($cpf){
    // Limpa caracteres indesejados
    $cpf = preg_replace("/[^0-9]/", "", $cpf);

    // Adiciona pontos e traço
    $cpf_formatado = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);

    return $cpf_formatado;
}

function converterData($data)
{
	if (strstr($data, "/")) {
		$A = explode("/", $data);
		$V_data = $A[2] . "-" . $A[1] . "-" . $A[0];
	} else {
		$A = explode("-", $data);
		$V_data = $A[2] . "/" . $A[1] . "/" . $A[0];
	}
	return $V_data;
}