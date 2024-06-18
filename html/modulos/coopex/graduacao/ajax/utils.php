<?php

function preparaSQLComParametrosVazios($array, $tabela, $campo = '', $registro = '')
{
	$aux = $campo ? "UPDATE" : "INSERT INTO";
	$aux .= " $tabela SET ";
	$arr = [];
	foreach ($array as $key => $value) {
		$arr[] = "\n$key = :$key";
	}
	$aux .= implode(", ", $arr);
	$aux .= $campo ? " WHERE $campo = $registro" : "";
	return $aux;
}
